<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

class AdminController extends Controller
{
    private function requireAuth(): ?array
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }

        $stmt = $this->db->query(
            "SELECT * FROM admin_users WHERE id = ? AND is_active = 1",
            [$_SESSION['admin_id']]
        );
        $admin = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$admin) {
            unset($_SESSION['admin_id']);
            header('Location: /admin/login');
            exit;
        }

        return $admin;
    }

    public function showLogin(): string
    {
        if (isset($_SESSION['admin_id'])) {
            return $this->redirect('/admin/dashboard');
        }

        return $this->view('pages/admin/login', [
            'pageTitle' => 'Admin Login - GlamourSchedule'
        ]);
    }

    public function login(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/login?error=csrf');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->redirect('/admin/login?error=empty');
        }

        $stmt = $this->db->query(
            "SELECT * FROM admin_users WHERE email = ? AND is_active = 1",
            [$email]
        );
        $admin = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return $this->redirect('/admin/login?error=invalid');
        }

        // Update last login
        $this->db->query(
            "UPDATE admin_users SET last_login = NOW() WHERE id = ?",
            [$admin['id']]
        );

        $_SESSION['admin_id'] = $admin['id'];
        return $this->redirect('/admin/dashboard');
    }

    public function logout(): string
    {
        unset($_SESSION['admin_id']);
        return $this->redirect('/admin/login');
    }

    public function dashboard(): string
    {
        $admin = $this->requireAuth();

        // Get stats
        $stats = $this->getDashboardStats();

        return $this->view('pages/admin/dashboard', [
            'pageTitle' => 'Dashboard - Admin',
            'admin' => $admin,
            'stats' => $stats
        ]);
    }

    private function getDashboardStats(): array
    {
        // Total users
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $totalUsers = $result['count'] ?? 0;

        // Total businesses
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM businesses");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $totalBusinesses = $result['count'] ?? 0;

        // Active businesses
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM businesses WHERE status = 'active'");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $activeBusinesses = $result['count'] ?? 0;

        // Pending businesses
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM businesses WHERE status = 'pending'");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $pendingBusinesses = $result['count'] ?? 0;

        // Total bookings
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $totalBookings = $result['count'] ?? 0;

        // Bookings today
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = CURDATE()");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $bookingsToday = $result['count'] ?? 0;

        // Bookings this month
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $bookingsThisMonth = $result['count'] ?? 0;

        // Revenue this month (admin fees)
        $stmt = $this->db->query("SELECT COALESCE(SUM(admin_fee), 0) as total FROM bookings WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $revenueThisMonth = $result['total'] ?? 0;

        // Total revenue
        $stmt = $this->db->query("SELECT COALESCE(SUM(admin_fee), 0) as total FROM bookings WHERE payment_status = 'paid'");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $totalRevenue = $result['total'] ?? 0;

        // Sales partners
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM sales_users WHERE status = 'active'");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $activeSalesPartners = $result['count'] ?? 0;

        // Recent registrations (last 7 days)
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM businesses WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $recentRegistrations = $result['count'] ?? 0;

        // Bookings per day (last 14 days)
        $stmt = $this->db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM bookings
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $bookingsPerDay = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'totalUsers' => $totalUsers,
            'totalBusinesses' => $totalBusinesses,
            'activeBusinesses' => $activeBusinesses,
            'pendingBusinesses' => $pendingBusinesses,
            'totalBookings' => $totalBookings,
            'bookingsToday' => $bookingsToday,
            'bookingsThisMonth' => $bookingsThisMonth,
            'revenueThisMonth' => $revenueThisMonth,
            'totalRevenue' => $totalRevenue,
            'activeSalesPartners' => $activeSalesPartners,
            'recentRegistrations' => $recentRegistrations,
            'bookingsPerDay' => $bookingsPerDay
        ];
    }

    public function users(): string
    {
        $admin = $this->requireAuth();

        $search = $_GET['search'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?";
            $searchParam = "%{$search}%";
            $params = [$searchParam, $searchParam, $searchParam];
        }

        // Get total count
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users {$whereClause}", $params);
        $totalUsers = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
        $totalPages = ceil($totalUsers / $perPage);

        // Get users
        $stmt = $this->db->query(
            "SELECT * FROM users {$whereClause} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('pages/admin/users', [
            'pageTitle' => 'Gebruikers - Admin',
            'admin' => $admin,
            'users' => $users,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers
        ]);
    }

    public function businesses(): string
    {
        $admin = $this->requireAuth();

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $whereClause = '1=1';
        $params = [];

        if (!empty($search)) {
            $whereClause .= " AND (company_name LIKE ? OR email LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($status)) {
            if ($status === 'kvk_pending') {
                // Filter: has KVK number but not verified yet
                $whereClause .= " AND kvk_number IS NOT NULL AND kvk_number != '' AND kvk_verified = 0 AND is_verified = 0";
            } else {
                $whereClause .= " AND status = ?";
                $params[] = $status;
            }
        }

        // Get total count
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM businesses WHERE {$whereClause}", $params);
        $totalBusinesses = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
        $totalPages = ceil($totalBusinesses / $perPage);

        // Get businesses
        $stmt = $this->db->query(
            "SELECT b.*, u.first_name, u.last_name, u.email as user_email
             FROM businesses b
             LEFT JOIN users u ON b.user_id = u.id
             WHERE {$whereClause}
             ORDER BY b.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $businesses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('pages/admin/businesses', [
            'pageTitle' => 'Bedrijven - Admin',
            'admin' => $admin,
            'businesses' => $businesses,
            'search' => $search,
            'status' => $status,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalBusinesses' => $totalBusinesses
        ]);
    }

    public function salesPartners(): string
    {
        $admin = $this->requireAuth();

        $search = $_GET['search'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE name LIKE ? OR email LIKE ? OR referral_code LIKE ?";
            $searchParam = "%{$search}%";
            $params = [$searchParam, $searchParam, $searchParam];
        }

        // Get total count
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM sales_users {$whereClause}", $params);
        $totalPartners = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
        $totalPages = ceil($totalPartners / $perPage);

        // Get partners with stats
        $stmt = $this->db->query(
            "SELECT s.*,
                    (SELECT COUNT(*) FROM sales_referrals WHERE sales_user_id = s.id) as total_referrals,
                    (SELECT COUNT(*) FROM sales_referrals WHERE sales_user_id = s.id AND status = 'converted') as converted_referrals,
                    (SELECT COALESCE(SUM(commission), 0) FROM sales_referrals WHERE sales_user_id = s.id AND status = 'paid') as total_earned
             FROM sales_users s
             {$whereClause}
             ORDER BY s.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $partners = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view('pages/admin/sales-partners', [
            'pageTitle' => 'Sales Partners - Admin',
            'admin' => $admin,
            'partners' => $partners,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPartners' => $totalPartners
        ]);
    }

    public function revenue(): string
    {
        $admin = $this->requireAuth();

        $period = $_GET['period'] ?? 'month';

        // Revenue by period
        if ($period === 'day') {
            $stmt = $this->db->query("
                SELECT DATE(created_at) as period,
                       COALESCE(SUM(admin_fee), 0) as revenue,
                       COUNT(*) as bookings
                FROM bookings
                WHERE payment_status = 'paid' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY period DESC
            ");
        } elseif ($period === 'week') {
            $stmt = $this->db->query("
                SELECT YEARWEEK(created_at) as period,
                       COALESCE(SUM(admin_fee), 0) as revenue,
                       COUNT(*) as bookings
                FROM bookings
                WHERE payment_status = 'paid' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
                GROUP BY YEARWEEK(created_at)
                ORDER BY period DESC
            ");
        } else {
            $stmt = $this->db->query("
                SELECT DATE_FORMAT(created_at, '%Y-%m') as period,
                       COALESCE(SUM(admin_fee), 0) as revenue,
                       COUNT(*) as bookings
                FROM bookings
                WHERE payment_status = 'paid' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY period DESC
            ");
        }
        $revenueData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Total revenue
        $stmt = $this->db->query("SELECT COALESCE(SUM(admin_fee), 0) as total FROM bookings WHERE payment_status = 'paid'");
        $totalRevenue = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Revenue this month
        $stmt = $this->db->query("SELECT COALESCE(SUM(admin_fee), 0) as total FROM bookings WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $revenueThisMonth = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Total registration fees
        $stmt = $this->db->query("SELECT COALESCE(SUM(registration_fee_paid), 0) as total FROM businesses WHERE registration_fee_paid > 0");
        $totalRegistrationFees = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Sales commissions paid
        $stmt = $this->db->query("SELECT COALESCE(SUM(commission), 0) as total FROM sales_referrals WHERE status = 'paid'");
        $totalCommissionsPaid = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        return $this->view('pages/admin/revenue', [
            'pageTitle' => 'Omzet - Admin',
            'admin' => $admin,
            'revenueData' => $revenueData,
            'totalRevenue' => $totalRevenue,
            'revenueThisMonth' => $revenueThisMonth,
            'totalRegistrationFees' => $totalRegistrationFees,
            'totalCommissionsPaid' => $totalCommissionsPaid,
            'period' => $period
        ]);
    }

    /**
     * Manual payouts overview - shows what needs to be transferred manually
     */
    public function payouts(): string
    {
        $admin = $this->requireAuth();

        // Get pending business payouts (bookings that need manual transfer)
        $stmt = $this->db->query(
            "SELECT
                b.id as booking_id,
                b.booking_number,
                b.service_price,
                b.platform_fee,
                b.business_payout,
                b.payout_status,
                b.checked_in_at,
                b.created_at,
                bus.id as business_id,
                bus.company_name,
                bus.iban,
                bus.email as business_email,
                s.name as service_name,
                u.name as customer_name
             FROM bookings b
             JOIN businesses bus ON b.business_id = bus.id
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.payment_status = 'paid'
               AND b.payout_status IN ('pending', 'processing')
               AND (bus.mollie_account_id IS NULL OR bus.mollie_account_id = '')
             ORDER BY b.checked_in_at ASC, b.created_at ASC"
        );
        $pendingBookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Group by business for easier processing
        $businessPayouts = [];
        foreach ($pendingBookings as $booking) {
            $bizId = $booking['business_id'];
            if (!isset($businessPayouts[$bizId])) {
                $businessPayouts[$bizId] = [
                    'business_id' => $bizId,
                    'company_name' => $booking['company_name'],
                    'iban' => $booking['iban'],
                    'email' => $booking['business_email'],
                    'bookings' => [],
                    'total_amount' => 0
                ];
            }
            $payoutAmount = $booking['business_payout'] ?? ($booking['service_price'] - ($booking['platform_fee'] ?? 1.75));
            $businessPayouts[$bizId]['bookings'][] = $booking;
            $businessPayouts[$bizId]['total_amount'] += $payoutAmount;
        }

        // Get pending sales partner payouts
        $stmt = $this->db->query(
            "SELECT
                su.id as sales_user_id,
                su.name as sales_name,
                su.email as sales_email,
                su.iban as sales_iban,
                COUNT(sr.id) as referral_count,
                SUM(sr.commission) as total_commission
             FROM sales_users su
             JOIN sales_referrals sr ON sr.sales_user_id = su.id
             WHERE su.status = 'active'
               AND sr.status = 'converted'
               AND su.iban IS NOT NULL AND su.iban != ''
             GROUP BY su.id
             HAVING total_commission >= 49.99"
        );
        $salesPayouts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get failed/pending business_payouts records
        $stmt = $this->db->query(
            "SELECT bp.*, bus.company_name, bus.iban
             FROM business_payouts bp
             JOIN businesses bus ON bp.business_id = bus.id
             WHERE bp.status IN ('pending', 'failed', 'processing')
             ORDER BY bp.created_at DESC
             LIMIT 50"
        );
        $payoutRecords = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Calculate totals
        $totalBusinessAmount = array_sum(array_column($businessPayouts, 'total_amount'));
        $totalSalesAmount = array_sum(array_column($salesPayouts, 'total_commission'));

        // Get Wise pending transfers (waiting for funding/approval)
        $wiseTransfers = [];
        $wiseBalance = null;
        try {
            $wise = new \GlamourSchedule\Services\WiseService();
            if ($wise->isConfigured()) {
                $wiseTransfers = $wise->getPendingTransfers();
                $wiseBalance = $wise->getEurBalance();
            }
        } catch (\Exception $e) {
            // Wise not available, continue without
        }

        $totalWiseAmount = array_sum(array_column($wiseTransfers, 'source_amount'));

        return $this->view('pages/admin/payouts', [
            'pageTitle' => 'Handmatige Uitbetalingen - Admin',
            'admin' => $admin,
            'businessPayouts' => $businessPayouts,
            'salesPayouts' => $salesPayouts,
            'payoutRecords' => $payoutRecords,
            'wiseTransfers' => $wiseTransfers,
            'wiseBalance' => $wiseBalance,
            'totalBusinessAmount' => $totalBusinessAmount,
            'totalSalesAmount' => $totalSalesAmount,
            'totalWiseAmount' => $totalWiseAmount,
            'totalAmount' => $totalBusinessAmount + $totalSalesAmount + $totalWiseAmount
        ]);
    }

    /**
     * Mark a payout as manually completed
     */
    public function markPayoutComplete(string $type, string $id): string
    {
        $admin = $this->requireAuth();

        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/payouts?error=csrf');
        }

        if ($type === 'business') {
            // Mark all pending bookings for this business as paid out
            $this->db->query(
                "UPDATE bookings SET payout_status = 'completed', payout_date = CURDATE()
                 WHERE business_id = ? AND payout_status IN ('pending', 'processing') AND payment_status = 'paid'",
                [$id]
            );

            // Also update any business_payouts records
            $this->db->query(
                "UPDATE business_payouts SET status = 'completed', payout_date = CURDATE(), notes = 'Handmatig gemarkeerd door admin'
                 WHERE business_id = ? AND status IN ('pending', 'processing', 'failed')",
                [$id]
            );
        } elseif ($type === 'sales') {
            // Mark sales referrals as paid
            $this->db->query(
                "UPDATE sales_referrals SET status = 'paid', paid_at = NOW()
                 WHERE sales_user_id = ? AND status = 'converted'",
                [$id]
            );
        } elseif ($type === 'record') {
            // Mark specific payout record as completed
            $this->db->query(
                "UPDATE business_payouts SET status = 'completed', payout_date = CURDATE(), notes = 'Handmatig gemarkeerd door admin'
                 WHERE id = ?",
                [$id]
            );
        }

        return $this->redirect('/admin/payouts?success=marked');
    }

    public function updateUser(string $id): string
    {
        $admin = $this->requireAuth();

        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/users?error=csrf');
        }

        $status = $_POST['status'] ?? 'active';

        $this->db->query(
            "UPDATE users SET status = ? WHERE id = ?",
            [$status, $id]
        );

        return $this->redirect('/admin/users?success=updated');
    }

    public function deleteUser(string $id): string
    {
        $admin = $this->requireAuth();

        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/users?error=csrf');
        }

        // Don't allow deleting users with businesses
        $stmt = $this->db->query("SELECT id FROM businesses WHERE user_id = ?", [$id]);
        if ($stmt->fetch()) {
            return $this->redirect('/admin/users?error=has_business');
        }

        $this->db->query("DELETE FROM users WHERE id = ?", [$id]);

        return $this->redirect('/admin/users?success=deleted');
    }

    public function updateBusiness(string $id): string
    {
        $admin = $this->requireAuth();

        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/businesses?error=csrf');
        }

        $status = $_POST['status'] ?? 'active';
        $subscriptionStatus = $_POST['subscription_status'] ?? 'active';

        $this->db->query(
            "UPDATE businesses SET status = ?, subscription_status = ? WHERE id = ?",
            [$status, $subscriptionStatus, $id]
        );

        return $this->redirect('/admin/businesses?success=updated');
    }

    public function deleteBusiness(string $id): string
    {
        $admin = $this->requireAuth();

        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/businesses?error=csrf');
        }

        // Delete related data first
        $this->db->query("DELETE FROM business_hours WHERE business_id = ?", [$id]);
        $this->db->query("DELETE FROM business_images WHERE business_id = ?", [$id]);
        $this->db->query("DELETE FROM business_categories WHERE business_id = ?", [$id]);
        $this->db->query("DELETE FROM services WHERE business_id = ?", [$id]);
        $this->db->query("DELETE FROM sales_referrals WHERE business_id = ?", [$id]);
        $this->db->query("DELETE FROM businesses WHERE id = ?", [$id]);

        return $this->redirect('/admin/businesses?success=deleted');
    }

    public function activateBusiness(string $id): string
    {
        $admin = $this->requireAuth();

        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/businesses?error=csrf');
        }

        $this->db->query(
            "UPDATE businesses SET status = 'active', subscription_status = 'active' WHERE id = ?",
            [$id]
        );

        return $this->redirect('/admin/businesses?success=activated');
    }

    /**
     * Verify a business's KVK number
     */
    public function verifyKvk(string $id): string
    {
        $admin = $this->requireAuth();

        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/businesses?error=csrf');
        }

        // Get business info for email
        $stmt = $this->db->query("SELECT email, company_name, kvk_number FROM businesses WHERE id = ?", [$id]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business || empty($business['kvk_number'])) {
            return $this->redirect('/admin/businesses?error=no_kvk');
        }

        $this->db->query(
            "UPDATE businesses SET kvk_verified = 1, status = 'active' WHERE id = ?",
            [$id]
        );

        // Send notification email to business
        $this->sendKvkVerifiedEmail($business);

        return $this->redirect('/admin/businesses?success=kvk_verified');
    }

    /**
     * Send email notification when KVK is verified
     */
    private function sendKvkVerifiedEmail(array $business): void
    {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#22c55e,#16a34a);color:#ffffff;padding:40px;text-align:center;">
                            <div style="font-size:3rem;margin-bottom:1rem;">✓</div>
                            <h1 style="margin:0;font-size:24px;">KVK Geverifieerd!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;margin:0 0 20px;">Beste {$business['company_name']},</p>
                            <p style="font-size:16px;color:#cccccc;line-height:1.6;margin:0 0 25px;">
                                Goed nieuws! Je KVK-nummer ({$business['kvk_number']}) is succesvol geverifieerd.
                            </p>
                            <div style="background:#f0fdf4;border:2px solid #22c55e;border-radius:12px;padding:20px;margin:25px 0;text-align:center;">
                                <p style="margin:0;color:#166534;font-weight:600;font-size:16px;">
                                    Je kunt nu boekingen ontvangen!
                                </p>
                            </div>
                            <p style="text-align:center;margin:30px 0;">
                                <a href="https://glamourschedule.nl/login" style="display:inline-block;background:#22c55e;color:#fff;padding:15px 40px;border-radius:10px;text-decoration:none;font-weight:600;">
                                    Ga naar Dashboard
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#999;font-size:12px;">GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($business['email'], 'Je KVK is geverifieerd - GlamourSchedule', $html);
        } catch (\Exception $e) {
            error_log('KVK verification email failed: ' . $e->getMessage());
        }
    }

    public function updateSalesPartner(string $id): string
    {
        $admin = $this->requireAuth();

        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/sales-partners?error=csrf');
        }

        $status = $_POST['status'] ?? 'active';

        $this->db->query(
            "UPDATE sales_users SET status = ? WHERE id = ?",
            [$status, $id]
        );

        return $this->redirect('/admin/sales-partners?success=updated');
    }

    public function deleteSalesPartner(string $id): string
    {
        $admin = $this->requireAuth();

        if (!$this->verifyCsrf()) {
            return $this->redirect('/admin/sales-partners?error=csrf');
        }

        $this->db->query("DELETE FROM sales_referrals WHERE sales_user_id = ?", [$id]);
        $this->db->query("DELETE FROM sales_users WHERE id = ?", [$id]);

        return $this->redirect('/admin/sales-partners?success=deleted');
    }

    // ============================================================
    // BUSINESS VERIFICATION (from email link)
    // ============================================================

    public function showVerifyBusiness(string $token): string
    {
        // Find business by token
        $stmt = $this->db->query(
            "SELECT b.*, u.email as user_email FROM businesses b
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.admin_verification_token = ?",
            [$token]
        );
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            return $this->view('pages/admin/verify-business-invalid', [
                'pageTitle' => 'Ongeldige Link'
            ]);
        }

        // Check if already verified or rejected
        if (!empty($business['is_verified'])) {
            return $this->view('pages/admin/verify-business-done', [
                'pageTitle' => 'Al Geverifieerd',
                'business' => $business,
                'status' => 'approved'
            ]);
        }

        if (!empty($business['rejected_at'])) {
            return $this->view('pages/admin/verify-business-done', [
                'pageTitle' => 'Al Afgewezen',
                'business' => $business,
                'status' => 'rejected'
            ]);
        }

        $action = $_GET['action'] ?? 'view';

        return $this->view('pages/admin/verify-business', [
            'pageTitle' => 'Bedrijf Verifiëren',
            'business' => $business,
            'token' => $token,
            'action' => $action,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function processVerifyBusiness(string $token): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect("/admin/verify-business/{$token}?error=csrf");
        }

        // Find business by token
        $stmt = $this->db->query(
            "SELECT b.*, u.email as user_email FROM businesses b
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.admin_verification_token = ?",
            [$token]
        );
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            return $this->view('pages/admin/verify-business-invalid', [
                'pageTitle' => 'Ongeldige Link'
            ]);
        }

        $action = $_POST['action'] ?? '';
        $reason = trim($_POST['reason'] ?? '');

        if ($action === 'approve') {
            // Approve the business
            $this->db->query(
                "UPDATE businesses SET is_verified = 1, admin_verified_at = NOW(), admin_verification_token = NULL WHERE id = ?",
                [$business['id']]
            );

            // Send approval email
            $this->sendBusinessApprovalEmail($business);

            return $this->view('pages/admin/verify-business-done', [
                'pageTitle' => 'Bedrijf Geaccepteerd',
                'business' => $business,
                'status' => 'approved'
            ]);

        } elseif ($action === 'reject') {
            if (empty($reason)) {
                return $this->redirect("/admin/verify-business/{$token}?action=reject&error=reason_required");
            }

            // Reject the business
            $this->db->query(
                "UPDATE businesses SET rejected_at = NOW(), rejection_reason = ?, admin_verification_token = NULL WHERE id = ?",
                [$reason, $business['id']]
            );

            // Send rejection email
            $this->sendBusinessRejectionEmail($business, $reason);

            return $this->view('pages/admin/verify-business-done', [
                'pageTitle' => 'Bedrijf Afgewezen',
                'business' => $business,
                'status' => 'rejected',
                'reason' => $reason
            ]);
        }

        return $this->redirect("/admin/verify-business/{$token}");
    }

    private function sendBusinessApprovalEmail(array $business): void
    {
        $dashboardUrl = "https://glamourschedule.nl/business/dashboard";
        $businessUrl = "https://glamourschedule.nl/business/{$business['slug']}";

        $subject = "Goed nieuws! Je bedrijf is geverifieerd - {$business['name']}";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#000000;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#000000;padding:40px 20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:20px;overflow:hidden;box-shadow:0 25px 50px rgba(0,0,0,0.5);">
                <tr><td style="background:#000000;color:#ffffff;padding:40px;text-align:center;">
                    <div style="width:80px;height:80px;background:#22c55e;border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
                        <span style="font-size:40px;line-height:80px;">✓</span>
                    </div>
                    <h1 style="margin:0;font-size:28px;font-weight:700;">Account Geactiveerd!</h1>
                    <p style="margin:15px 0 0;opacity:0.9;font-size:16px;">Je bedrijf is succesvol geverifieerd</p>
                </td></tr>
                <tr><td style="padding:40px;">
                    <p style="font-size:16px;color:#ffffff;margin:0 0 20px;">Beste {$business['name']},</p>

                    <p style="font-size:16px;color:#ffffff;line-height:1.7;margin:0 0 25px;">
                        Geweldig nieuws! Ons team heeft je bedrijfsregistratie beoordeeld en goedgekeurd.
                        Je account is nu <strong>volledig actief</strong> en je kunt direct boekingen ontvangen van klanten.
                    </p>

                    <div style="background:#f0fdf4;border:2px solid #22c55e;border-radius:16px;padding:25px;margin-bottom:30px;">
                        <h3 style="margin:0 0 15px;color:#166534;font-size:18px;">Wat kun je nu doen?</h3>
                        <ul style="margin:0;padding-left:20px;color:#166534;line-height:1.8;">
                            <li>Klanten kunnen nu bij je boeken</li>
                            <li>Je bedrijfspagina is zichtbaar op GlamourSchedule</li>
                            <li>Je ontvangt meldingen bij nieuwe boekingen</li>
                        </ul>
                    </div>

                    <div style="text-align:center;margin:30px 0;">
                        <a href="{$dashboardUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:18px 50px;border-radius:12px;text-decoration:none;font-weight:700;font-size:16px;margin:5px;">
                            Naar Dashboard
                        </a>
                    </div>

                    <p style="font-size:14px;color:#cccccc;text-align:center;margin:20px 0 0;">
                        Je bedrijfspagina: <a href="{$businessUrl}" style="color:#ffffff;font-weight:600;">{$businessUrl}</a>
                    </p>
                </td></tr>
                <tr><td style="background:#000000;padding:25px;text-align:center;">
                    <p style="margin:0;color:#ffffff;font-size:12px;opacity:0.7;">&copy; 2025 GlamourSchedule - Beauty & Wellness Bookings</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($business['email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send business approval email: " . $e->getMessage());
        }
    }

    private function sendBusinessRejectionEmail(array $business, string $reason): void
    {
        $contactUrl = "https://glamourschedule.nl/contact";
        $registerUrl = "https://glamourschedule.nl/business/register";

        $subject = "Je bedrijfsregistratie is helaas afgewezen - {$business['name']}";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#000000;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#000000;padding:40px 20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:20px;overflow:hidden;box-shadow:0 25px 50px rgba(0,0,0,0.5);">
                <tr><td style="background:#000000;color:#ffffff;padding:40px;text-align:center;">
                    <div style="width:80px;height:80px;background:#ef4444;border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
                        <span style="font-size:40px;line-height:80px;">✗</span>
                    </div>
                    <h1 style="margin:0;font-size:28px;font-weight:700;">Registratie Afgewezen</h1>
                    <p style="margin:15px 0 0;opacity:0.9;font-size:16px;">Je aanvraag kon helaas niet worden goedgekeurd</p>
                </td></tr>
                <tr><td style="padding:40px;">
                    <p style="font-size:16px;color:#ffffff;margin:0 0 20px;">Beste {$business['name']},</p>

                    <p style="font-size:16px;color:#ffffff;line-height:1.7;margin:0 0 25px;">
                        Helaas moeten we je meedelen dat je bedrijfsregistratie is afgewezen na beoordeling door ons team.
                    </p>

                    <div style="background:#fef2f2;border:2px solid #ef4444;border-radius:16px;padding:25px;margin-bottom:30px;">
                        <h3 style="margin:0 0 15px;color:#991b1b;font-size:16px;">Reden van afwijzing:</h3>
                        <p style="margin:0;color:#991b1b;line-height:1.7;font-size:15px;">{$reason}</p>
                    </div>

                    <div style="background:#f9fafb;border-radius:16px;padding:25px;margin-bottom:30px;">
                        <h3 style="margin:0 0 15px;color:#ffffff;font-size:16px;">Wat kun je doen?</h3>
                        <ul style="margin:0;padding-left:20px;color:#555;line-height:1.8;">
                            <li>Controleer of je gegevens correct zijn</li>
                            <li>Voeg een geldig KVK-nummer toe</li>
                            <li>Registreer opnieuw met de juiste informatie</li>
                            <li>Neem contact met ons op als je vragen hebt</li>
                        </ul>
                    </div>

                    <div style="text-align:center;margin:30px 0;">
                        <a href="{$registerUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:18px 40px;border-radius:12px;text-decoration:none;font-weight:700;font-size:16px;margin:5px;">
                            Opnieuw Registreren
                        </a>
                        <a href="{$contactUrl}" style="display:inline-block;background:#ffffff;color:#000000;padding:18px 40px;border-radius:12px;text-decoration:none;font-weight:700;font-size:16px;border:2px solid #333;margin:5px;">
                            Contact Opnemen
                        </a>
                    </div>
                </td></tr>
                <tr><td style="background:#000000;padding:25px;text-align:center;">
                    <p style="margin:0;color:#ffffff;font-size:12px;opacity:0.7;">&copy; 2025 GlamourSchedule - Beauty & Wellness Bookings</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($business['email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send business rejection email: " . $e->getMessage());
        }
    }
}

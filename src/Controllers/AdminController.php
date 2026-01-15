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
        $totalUsers = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Total businesses
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM businesses");
        $totalBusinesses = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Active businesses
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM businesses WHERE status = 'active'");
        $activeBusinesses = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Pending businesses
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM businesses WHERE status = 'pending'");
        $pendingBusinesses = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Total bookings
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings");
        $totalBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Bookings today
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = CURDATE()");
        $bookingsToday = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Bookings this month
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $bookingsThisMonth = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Revenue this month (admin fees)
        $stmt = $this->db->query("SELECT COALESCE(SUM(admin_fee), 0) as total FROM bookings WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $revenueThisMonth = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Total revenue
        $stmt = $this->db->query("SELECT COALESCE(SUM(admin_fee), 0) as total FROM bookings WHERE payment_status = 'paid'");
        $totalRevenue = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Sales partners
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM sales_users WHERE status = 'active'");
        $activeSalesPartners = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Recent registrations (last 7 days)
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM businesses WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $recentRegistrations = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

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
            $whereClause .= " AND status = ?";
            $params[] = $status;
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
}

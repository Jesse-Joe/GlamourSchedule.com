<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;

class SalesController extends Controller
{
    // Pricing constants (must match BusinessRegisterController)
    private const REGISTRATION_FEE = 99.99;
    private const SALES_PARTNER_DISCOUNT = 25.00;
    private const EARLY_BIRD_PRICE = 0.99;

    private ?array $salesUser = null;

    /**
     * Check if early bird is still available (first 100 via sales partners)
     */
    private function isEarlyBirdAvailable(): bool
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) as cnt FROM businesses WHERE referred_by_sales_partner IS NOT NULL AND is_early_adopter = 1"
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)$result['cnt'] < 100;
    }

    /**
     * Get early bird spots remaining
     */
    private function getEarlyBirdSpotsLeft(): int
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) as cnt FROM businesses WHERE referred_by_sales_partner IS NOT NULL AND is_early_adopter = 1"
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return max(0, 100 - (int)$result['cnt']);
    }

    private function requireAuth(): bool
    {
        if (!isset($_SESSION['sales_user_id'])) {
            return false;
        }
        $stmt = $this->db->query(
            "SELECT * FROM sales_users WHERE id = ? AND status = 'active'",
            [$_SESSION['sales_user_id']]
        );
        $this->salesUser = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $this->salesUser !== false;
    }

    public function index(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }
        return $this->dashboard();
    }

    public function dashboard(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        $stats = $this->getStats();
        $recentReferrals = $this->getRecentReferrals();
        $trialReferrals = $this->getTrialReferrals();
        $pendingPayouts = $this->getPendingPayouts();

        return $this->view('pages/sales/dashboard', [
            'pageTitle' => 'Sales Dashboard',
            'salesUser' => $this->salesUser,
            'stats' => $stats,
            'recentReferrals' => $recentReferrals,
            'trialReferrals' => $trialReferrals,
            'pendingPayouts' => $pendingPayouts,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function referrals(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        $referrals = $this->getAllReferrals();

        return $this->view('pages/sales/referrals', [
            'pageTitle' => 'Mijn Referrals',
            'salesUser' => $this->salesUser,
            'referrals' => $referrals,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Show salons registered via this sales partner
     */
    public function mijnSalons(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        $salons = $this->getRegisteredSalons();
        $stats = $this->getSalonStats();

        return $this->view('pages/sales/mijn-salons', [
            'pageTitle' => 'Mijn Salons',
            'salesUser' => $this->salesUser,
            'salons' => $salons,
            'stats' => $stats,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Get all salons registered via this sales partner
     */
    private function getRegisteredSalons(): array
    {
        $stmt = $this->db->query(
            "SELECT b.id, b.company_name, b.email, b.phone, b.city, b.status,
                    b.subscription_status, b.created_at, b.trial_ends_at,
                    sr.commission, sr.status as referral_status
             FROM businesses b
             JOIN sales_referrals sr ON sr.business_id = b.id
             WHERE sr.sales_user_id = ?
             ORDER BY b.created_at DESC",
            [$this->salesUser['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get salon statistics for this sales partner
     */
    private function getSalonStats(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) as total,
                SUM(CASE WHEN b.status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN b.subscription_status = 'trial' THEN 1 ELSE 0 END) as in_trial,
                SUM(CASE WHEN b.status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN b.status = 'inactive' OR b.status = 'suspended' THEN 1 ELSE 0 END) as inactive
             FROM businesses b
             JOIN sales_referrals sr ON sr.business_id = b.id
             WHERE sr.sales_user_id = ?",
            [$this->salesUser['id']]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['total' => 0, 'active' => 0, 'in_trial' => 0, 'pending' => 0, 'inactive' => 0];
    }

    /**
     * Show early birds page
     */
    public function earlyBirds(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        $earlyBirds = $this->getEarlyBirds();
        $stats = $this->getEarlyBirdStats();

        return $this->view('pages/sales/early-birds', [
            'pageTitle' => 'Early Birds',
            'salesUser' => $this->salesUser,
            'earlyBirds' => $earlyBirds,
            'stats' => $stats,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Register a new early bird
     */
    public function registerEarlyBird(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return $this->redirect('/sales/early-birds');
        }

        $businessName = trim($_POST['business_name'] ?? '');
        $contactName = trim($_POST['contact_name'] ?? '');
        $contactEmail = trim($_POST['contact_email'] ?? '');
        $contactPhone = trim($_POST['contact_phone'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        // Validation
        if (empty($businessName) || empty($contactName) || empty($contactEmail)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Vul alle verplichte velden in'];
            return $this->redirect('/sales/early-birds');
        }

        if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldig e-mailadres'];
            return $this->redirect('/sales/early-birds');
        }

        // Check if email already registered
        $stmt = $this->db->query(
            "SELECT id FROM sales_early_birds WHERE contact_email = ?",
            [$contactEmail]
        );
        if ($stmt->fetch()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Dit e-mailadres is al geregistreerd als Early Bird'];
            return $this->redirect('/sales/early-birds');
        }

        // Generate unique invite code
        $inviteCode = 'EB-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Set expiry date (30 days)
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        try {
            $this->db->query(
                "INSERT INTO sales_early_birds (sales_user_id, business_name, contact_name, contact_email, contact_phone, invite_code, notes, expires_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$this->salesUser['id'], $businessName, $contactName, $contactEmail, $contactPhone, $inviteCode, $notes, $expiresAt]
            );

            // Send invitation email
            $this->sendEarlyBirdInviteEmail($contactEmail, $contactName, $businessName, $inviteCode);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Early Bird geregistreerd! Uitnodiging verstuurd naar ' . $contactEmail];
        } catch (\Exception $e) {
            error_log("Early bird registration error: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Er ging iets mis bij het registreren'];
        }

        return $this->redirect('/sales/early-birds');
    }

    /**
     * Resend early bird invite
     */
    public function resendEarlyBirdInvite(int $id): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        $stmt = $this->db->query(
            "SELECT * FROM sales_early_birds WHERE id = ? AND sales_user_id = ? AND status = 'pending'",
            [$id, $this->salesUser['id']]
        );
        $earlyBird = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$earlyBird) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Early Bird niet gevonden'];
            return $this->redirect('/sales/early-birds');
        }

        // Update expiry date
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        $this->db->query(
            "UPDATE sales_early_birds SET expires_at = ?, updated_at = NOW() WHERE id = ?",
            [$expiresAt, $id]
        );

        // Resend email
        $this->sendEarlyBirdInviteEmail(
            $earlyBird['contact_email'],
            $earlyBird['contact_name'],
            $earlyBird['business_name'],
            $earlyBird['invite_code']
        );

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Uitnodiging opnieuw verstuurd!'];
        return $this->redirect('/sales/early-birds');
    }

    /**
     * Get all early birds for current sales user
     */
    private function getEarlyBirds(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM sales_early_birds WHERE sales_user_id = ? ORDER BY created_at DESC",
            [$this->salesUser['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get early bird statistics
     */
    private function getEarlyBirdStats(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'trial' THEN 1 ELSE 0 END) as in_trial,
                SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
             FROM sales_early_birds
             WHERE sales_user_id = ?",
            [$this->salesUser['id']]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['total' => 0, 'in_trial' => 0, 'converted' => 0, 'pending' => 0];
    }

    /**
     * Send early bird invitation email
     */
    private function sendEarlyBirdInviteEmail(string $email, string $name, string $businessName, string $inviteCode): void
    {
        $inviteLink = "https://glamourschedule.nl/early-bird/{$inviteCode}";
        $salesName = $this->salesUser['name'];

        $subject = "Exclusieve Early Bird aanbieding - Start voor slechts €0,99!";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;border:1px solid #333;">
                <tr><td style="background:#000000;padding:40px;text-align:center;">
                    <h1 style="margin:0;color:#ffffff;font-size:28px;">Early Bird Aanbieding</h1>
                    <p style="margin:10px 0 0;color:#ffffff;font-size:16px;opacity:0.9;">Exclusief voor {$businessName}</p>
                </td></tr>
                <tr><td style="padding:40px;">
                    <p style="font-size:18px;color:#ffffff;margin:0 0 20px;">Beste {$name},</p>

                    <p style="font-size:16px;color:#cccccc;line-height:1.6;margin:0 0 25px;">
                        Via {$salesName} heb je een exclusieve Early Bird uitnodiging ontvangen voor GlamourSchedule -
                        het slimste online boekingssysteem voor salons.
                    </p>

                    <div style="background:#0a0a0a;border:2px solid #000000;border-radius:12px;padding:25px;text-align:center;margin:0 0 25px;">
                        <p style="margin:0 0 10px;color:#ffffff;font-size:14px;text-transform:uppercase;letter-spacing:1px;">Early Bird Prijs</p>
                        <p style="margin:0;font-size:48px;font-weight:bold;color:#ffffff;">€0,99</p>
                        <p style="margin:10px 0 0;color:#cccccc;font-size:14px;"><span style="text-decoration:line-through;">Normaal €99,99</span> - <strong style="color:#ffffff;">Je bespaart €99!</strong></p>
                    </div>

                    <div style="background:#0a0a0a;border:2px solid #333333;border-radius:12px;padding:20px;text-align:center;margin:0 0 25px;">
                        <p style="margin:0;color:#ffffff;font-size:18px;font-weight:bold;">Eerste 14 dagen GRATIS proberen!</p>
                        <p style="margin:8px 0 0;color:#cccccc;font-size:14px;">Daarna betaal je eenmalig de aanmeldkosten van €0,99</p>
                    </div>

                    <div style="margin:0 0 25px;">
                        <p style="color:#ffffff;font-weight:600;margin:0 0 15px;">Wat krijg je?</p>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr><td style="padding:8px 0;color:#cccccc;font-size:14px;">- Online boekingssysteem</td></tr>
                            <tr><td style="padding:8px 0;color:#cccccc;font-size:14px;">- Automatische herinneringen</td></tr>
                            <tr><td style="padding:8px 0;color:#cccccc;font-size:14px;">- Online betalingen</td></tr>
                            <tr><td style="padding:8px 0;color:#cccccc;font-size:14px;">- Eigen salon pagina</td></tr>
                            <tr><td style="padding:8px 0;color:#cccccc;font-size:14px;">- Gratis trial periode</td></tr>
                        </table>
                    </div>

                    <a href="{$inviteLink}" style="display:block;background:#000000;color:#ffffff;text-decoration:none;padding:18px 30px;border-radius:10px;font-weight:bold;font-size:16px;text-align:center;border:2px solid #000000;">
                        Registreer nu
                    </a>

                    <p style="margin:25px 0 0;font-size:13px;color:#cccccc;text-align:center;">
                        Deze aanbieding is 30 dagen geldig.
                    </p>
                </td></tr>
                <tr><td style="background:#0a0a0a;padding:25px;text-align:center;border-top:1px solid #e5e5e5;">
                    <p style="margin:0;color:#cccccc;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send early bird invite email: " . $e->getMessage());
        }
    }

    public function payouts(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        $payouts = $this->getPayoutHistory();
        $pendingAmount = $this->getPendingAmount();

        return $this->view('pages/sales/payouts', [
            'pageTitle' => 'Uitbetalingen',
            'salesUser' => $this->salesUser,
            'payouts' => $payouts,
            'pendingAmount' => $pendingAmount,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function materials(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        return $this->view('pages/sales/materials', [
            'pageTitle' => 'Promotiemateriaal',
            'salesUser' => $this->salesUser,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function sendReferralEmail(): string
    {
        header('Content-Type: application/json');

        if (!$this->requireAuth()) {
            return json_encode(['success' => false, 'error' => 'Niet ingelogd']);
        }

        if (!$this->verifyCsrf()) {
            return json_encode(['success' => false, 'error' => 'CSRF token ongeldig']);
        }

        $salonName = trim($_POST['salon_name'] ?? '');
        $salonEmail = trim($_POST['salon_email'] ?? '');
        $personalMessage = trim($_POST['personal_message'] ?? '');

        if (empty($salonName) || empty($salonEmail)) {
            return json_encode(['success' => false, 'error' => 'Vul alle verplichte velden in']);
        }

        if (!filter_var($salonEmail, FILTER_VALIDATE_EMAIL)) {
            return json_encode(['success' => false, 'error' => 'Ongeldig e-mailadres']);
        }

        $referralCode = $this->salesUser['referral_code'];
        $salesName = $this->salesUser['name'];
        $referralLink = "https://glamourschedule.nl/partner/register?ref={$referralCode}";

        // Determine pricing based on early bird availability
        $isEarlyBird = $this->isEarlyBirdAvailable();
        $spotsLeft = $this->getEarlyBirdSpotsLeft();

        if ($isEarlyBird) {
            $price = number_format(self::EARLY_BIRD_PRICE, 2, ',', '.');
            $savings = number_format(self::REGISTRATION_FEE - self::EARLY_BIRD_PRICE, 0, ',', '.');
            $subject = "Early Bird Aanbieding - Start voor slechts €{$price}!";
            $offerType = 'Early Bird';
            $offerLabel = 'Exclusieve Early Bird Prijs';
            $inviteText = "een exclusieve <strong>Early Bird uitnodiging</strong>";
            $urgencyText = "<p style='margin:1rem 0 0 0;color:#ffffff;font-size:0.85rem;font-weight:600'>Nog maar {$spotsLeft} plekken beschikbaar!</p>";
        } else {
            $partnerPrice = self::REGISTRATION_FEE - self::SALES_PARTNER_DISCOUNT;
            $price = number_format($partnerPrice, 2, ',', '.');
            $savings = number_format(self::SALES_PARTNER_DISCOUNT, 0, ',', '.');
            $subject = "Exclusieve €" . number_format(self::SALES_PARTNER_DISCOUNT, 0) . " korting op GlamourSchedule!";
            $offerType = 'Partner Korting';
            $offerLabel = 'Exclusieve Partner Prijs';
            $inviteText = "een exclusieve <strong>partner korting</strong>";
            $urgencyText = "";
        }

        $personalLine = !empty($personalMessage) ? "<p style='font-style:italic;color:#cccccc;border-left:3px solid #000000;padding-left:1rem;margin-bottom:1.5rem;background:#0a0a0a;padding:1rem;border-radius:0 8px 8px 0'>\"{$personalMessage}\"</p>" : "";

        $htmlBody = "
        <div style='font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;max-width:600px;margin:0 auto;background:#0a0a0a'>
            <div style='background:#000000;padding:2.5rem;text-align:center;border-radius:12px 12px 0 0'>
                <p style='color:#ffffff;margin:0 0 0.5rem 0;font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;font-weight:600;opacity:0.9'>{$offerType}</p>
                <h1 style='color:#ffffff;margin:0;font-size:1.75rem;font-weight:700'>GlamourSchedule</h1>
                <p style='color:#ffffff;margin:0.5rem 0 0 0;opacity:0.8'>Het slimste boekingssysteem voor salons</p>
            </div>

            <div style='background:#1a1a1a;padding:2rem;border:1px solid #333;border-top:none;border-radius:0 0 12px 12px'>
                <p style='color:#ffffff;font-size:1.1rem;margin-top:0'>Beste {$salonName},</p>

                {$personalLine}

                <p style='color:#cccccc;line-height:1.7'>
                    Via <strong style='color:#ffffff'>{$salesName}</strong> heb je {$inviteText} ontvangen!
                </p>

                <div style='background:#0a0a0a;border:2px solid #333;border-radius:12px;padding:2rem;margin:1.5rem 0;text-align:center'>
                    <p style='margin:0 0 0.5rem 0;color:#ffffff;font-size:0.85rem;text-transform:uppercase;letter-spacing:1px;font-weight:600'>{$offerLabel}</p>
                    <p style='margin:0;font-size:3rem;font-weight:700;color:#ffffff'>€{$price}</p>
                    <p style='margin:0.5rem 0 0 0;color:#888888;font-size:0.95rem'><span style='text-decoration:line-through'>Normaal €99,99</span> - <strong style='color:#22c55e'>Je bespaart €{$savings}!</strong></p>
                    {$urgencyText}
                </div>

                <div style='background:#0a0a0a;border:2px solid #333333;border-radius:12px;padding:1.5rem;margin:1.5rem 0;text-align:center'>
                    <p style='margin:0;color:#ffffff;font-size:1.1rem;font-weight:700'>Eerste 14 dagen GRATIS proberen!</p>
                    <p style='margin:0.5rem 0 0 0;color:#cccccc;font-size:0.9rem'>Daarna betaal je eenmalig de aanmeldkosten van €{$price}</p>
                </div>

                <div style='background:#0a0a0a;border-left:4px solid #000000;border-radius:0 8px 8px 0;padding:1.5rem;margin:1.5rem 0'>
                    <p style='margin:0 0 0.75rem 0;color:#ffffff;font-weight:700;font-size:1.1rem'>Waarom GlamourSchedule anders is</p>
                    <p style='color:#cccccc;line-height:1.7;margin:0 0 0.75rem 0'>
                        Bij GlamourSchedule betaal je <strong style='color:#ffffff'>geen maandelijks abonnement</strong> en <strong style='color:#ffffff'>geen vaste kosten</strong>.
                    </p>
                    <p style='color:#cccccc;line-height:1.7;margin:0 0 0.75rem 0'>
                        Heb je een rustige periode, een dip in boekingen of ga je op vakantie?<br>
                        <strong style='color:#ffffff'>Dan betaal je helemaal niets.</strong>
                    </p>
                    <p style='color:#cccccc;line-height:1.7;margin:0'>
                        Je betaalt alleen <strong style='color:#ffffff'>€1,75 per boeking</strong>, wanneer je echt klanten ontvangt.<br>
                        Dat maakt GlamourSchedule eerlijk, flexibel en risicoloos.
                    </p>
                </div>

                <p style='color:#ffffff;font-weight:600;margin-bottom:0.75rem'>Wat krijg je?</p>
                <table style='width:100%;color:#cccccc;font-size:0.95rem'>
                    <tr><td style='padding:8px 0'>- Online boekingen 24/7</td></tr>
                    <tr><td style='padding:8px 0'>- Automatische herinneringen aan klanten</td></tr>
                    <tr><td style='padding:8px 0'>- Betalingen via iDEAL</td></tr>
                    <tr><td style='padding:8px 0'>- Eigen professionele salonpagina</td></tr>
                    <tr><td style='padding:8px 0'>- Klantenbeheer dashboard</td></tr>
                    <tr><td style='padding:8px 0'>- Gratis proefperiode</td></tr>
                </table>

                <div style='text-align:center;margin:2rem 0'>
                    <a href='{$referralLink}' style='display:inline-block;background:#000000;color:#ffffff;text-decoration:none;padding:1.25rem 2.5rem;border-radius:10px;font-weight:700;font-size:1.1rem;border:2px solid #000000'>
                        Registreer nu
                    </a>
                </div>

                <p style='color:#cccccc;font-size:0.85rem;text-align:center;margin-bottom:0'>
                    Of kopieer deze link: <span style='color:#ffffff'>{$referralLink}</span>
                </p>
            </div>

            <p style='text-align:center;color:#cccccc;font-size:0.8rem;margin-top:1rem;padding:0 1rem'>
                Deze email is verstuurd namens {$salesName} via GlamourSchedule Sales
            </p>
        </div>
        ";

        try {
            $mailer = new Mailer();
            $result = $mailer->send($salonEmail, $subject, $htmlBody);

            if ($result) {
                // Log the email sent
                $this->db->query(
                    "INSERT INTO sales_email_logs (sales_user_id, recipient_email, recipient_name, sent_at) VALUES (?, ?, ?, NOW())",
                    [$this->salesUser['id'], $salonEmail, $salonName]
                );
                return json_encode(['success' => true]);
            } else {
                return json_encode(['success' => false, 'error' => 'Email versturen mislukt. Probeer opnieuw.']);
            }
        } catch (\Exception $e) {
            error_log('Sales referral email failed: ' . $e->getMessage());
            return json_encode(['success' => false, 'error' => 'Email versturen mislukt. Probeer opnieuw.']);
        }
    }

    public function guide(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        return $this->view('pages/sales/guide', [
            'pageTitle' => 'Sales Stappenplan',
            'salesUser' => $this->salesUser,
            'csrfToken' => $this->csrf()
        ]);
    }

    // ============================================================
    // AUTHENTICATION
    // ============================================================

    public function showLogin(): string
    {
        if (isset($_SESSION['sales_user_id'])) {
            return $this->redirect('/sales/dashboard');
        }

        return $this->view('pages/sales/login', [
            'pageTitle' => 'Sales Login',
            'csrfToken' => $this->csrf()
        ]);
    }

    public function login(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/sales/login?error=csrf');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $this->db->query(
            "SELECT * FROM sales_users WHERE email = ?",
            [$email]
        );
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->view('pages/sales/login', [
                'pageTitle' => 'Sales Login',
                'error' => 'Ongeldige inloggegevens',
                'csrfToken' => $this->csrf()
            ]);
        }

        if ($user['status'] !== 'active') {
            return $this->view('pages/sales/login', [
                'pageTitle' => 'Sales Login',
                'error' => 'Je account is nog niet geactiveerd',
                'csrfToken' => $this->csrf()
            ]);
        }

        // 2FA: Generate and send verification code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $this->db->query(
            "UPDATE sales_users SET two_factor_code = ?, two_factor_code_expires = ? WHERE id = ?",
            [$code, $expires, $user['id']]
        );

        // Send 2FA code via email
        $this->send2FAEmail($user['email'], $user['name'], $code);

        // Store user ID in session for 2FA verification (not logged in yet)
        $_SESSION['sales_2fa_user_id'] = $user['id'];
        $_SESSION['sales_2fa_email'] = $user['email'];

        return $this->redirect('/sales/2fa');
    }

    public function show2FA(): string
    {
        if (!isset($_SESSION['sales_2fa_user_id'])) {
            return $this->redirect('/sales/login');
        }

        $email = $_SESSION['sales_2fa_email'] ?? '';
        $parts = explode('@', $email);
        $maskedEmail = substr($parts[0], 0, 2) . '***@' . ($parts[1] ?? '');

        return $this->view('pages/sales/2fa', [
            'pageTitle' => 'Verificatie',
            'maskedEmail' => $maskedEmail,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function verify2FA(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/sales/login?error=csrf');
        }

        if (!isset($_SESSION['sales_2fa_user_id'])) {
            return $this->redirect('/sales/login');
        }

        $userId = $_SESSION['sales_2fa_user_id'];
        $code = trim($_POST['code'] ?? '');

        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return $this->view('pages/sales/2fa', [
                'pageTitle' => 'Verificatie',
                'maskedEmail' => substr($_SESSION['sales_2fa_email'], 0, 2) . '***@' . explode('@', $_SESSION['sales_2fa_email'])[1],
                'error' => 'Voer een geldige 6-cijferige code in',
                'csrfToken' => $this->csrf()
            ]);
        }

        $stmt = $this->db->query(
            "SELECT two_factor_code, two_factor_code_expires FROM sales_users WHERE id = ?",
            [$userId]
        );
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || $user['two_factor_code'] !== $code) {
            return $this->view('pages/sales/2fa', [
                'pageTitle' => 'Verificatie',
                'maskedEmail' => substr($_SESSION['sales_2fa_email'], 0, 2) . '***@' . explode('@', $_SESSION['sales_2fa_email'])[1],
                'error' => 'Ongeldige verificatiecode',
                'csrfToken' => $this->csrf()
            ]);
        }

        if (strtotime($user['two_factor_code_expires']) < time()) {
            return $this->view('pages/sales/2fa', [
                'pageTitle' => 'Verificatie',
                'maskedEmail' => substr($_SESSION['sales_2fa_email'], 0, 2) . '***@' . explode('@', $_SESSION['sales_2fa_email'])[1],
                'error' => 'Code is verlopen. Vraag een nieuwe aan.',
                'expired' => true,
                'csrfToken' => $this->csrf()
            ]);
        }

        // Clear 2FA code
        $this->db->query(
            "UPDATE sales_users SET two_factor_code = NULL, two_factor_code_expires = NULL WHERE id = ?",
            [$userId]
        );

        // Log user in
        $_SESSION['sales_user_id'] = $userId;
        unset($_SESSION['sales_2fa_user_id']);
        unset($_SESSION['sales_2fa_email']);

        return $this->redirect('/sales/dashboard');
    }

    public function resend2FA(): string
    {
        if (!isset($_SESSION['sales_2fa_user_id'])) {
            return $this->redirect('/sales/login');
        }

        $userId = $_SESSION['sales_2fa_user_id'];

        $stmt = $this->db->query("SELECT email, name FROM sales_users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return $this->redirect('/sales/login');
        }

        // Generate new code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $this->db->query(
            "UPDATE sales_users SET two_factor_code = ?, two_factor_code_expires = ? WHERE id = ?",
            [$code, $expires, $userId]
        );

        $this->send2FAEmail($user['email'], $user['name'], $code);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Nieuwe code verzonden!'];
        return $this->redirect('/sales/2fa');
    }

    private function send2FAEmail(string $email, string $name, string $code): void
    {
        $subject = "Je verificatiecode: $code - GlamourSchedule Sales";
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr><td align="center">
            <table width="500" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;border:1px solid #333;">
                <tr><td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                    <h1 style="margin:0;font-size:22px;">Login Verificatie</h1>
                </td></tr>
                <tr><td style="padding:35px;text-align:center;">
                    <p style="font-size:16px;color:#fff;margin:0 0 20px 0;">Hallo {$name},</p>
                    <p style="font-size:14px;color:#a1a1a1;margin:0 0 25px 0;">Gebruik deze code om in te loggen op je Sales Portal:</p>
                    <div style="background:#000;border:2px solid #333;border-radius:12px;padding:20px;margin:0 0 25px 0;">
                        <span style="font-size:36px;font-weight:bold;color:#ffffff;letter-spacing:8px;font-family:monospace;">{$code}</span>
                    </div>
                    <p style="font-size:13px;color:#cccccc;margin:0 0 10px 0;">Deze code is 10 minuten geldig.</p>
                    <p style="font-size:12px;color:#555;margin:0;">Heb jij niet geprobeerd in te loggen? Negeer deze e-mail en wijzig je wachtwoord.</p>
                </td></tr>
                <tr><td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                    <p style="margin:0;color:#cccccc;font-size:12px;">&copy; 2026 GlamourSchedule Sales</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send 2FA email: " . $e->getMessage());
        }
    }

    public function logout(): string
    {
        unset($_SESSION['sales_user_id']);
        return $this->redirect('/sales/login');
    }

    // ============================================================
    // PASSWORD RESET
    // ============================================================

    public function showForgotPassword(): string
    {
        return $this->view('pages/sales/forgot-password', [
            'pageTitle' => 'Wachtwoord Vergeten',
            'csrfToken' => $this->csrf()
        ]);
    }

    public function sendResetCode(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/sales/forgot-password?error=csrf');
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->view('pages/sales/forgot-password', [
                'pageTitle' => 'Wachtwoord Vergeten',
                'error' => 'Voer een geldig e-mailadres in',
                'email' => $email,
                'csrfToken' => $this->csrf()
            ]);
        }

        // Check if email exists
        $stmt = $this->db->query("SELECT id, name, email FROM sales_users WHERE email = ?", [$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            // Generate unique reset token (64 chars)
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store reset token
            $this->db->query(
                "UPDATE sales_users SET reset_code = ?, reset_code_expires = ? WHERE id = ?",
                [$token, $expires, $user['id']]
            );

            // Send reset email with direct link
            $this->sendResetEmail($user['email'], $user['name'], $token);
        }

        // Always show success message (security: don't reveal if email exists)
        return $this->view('pages/sales/forgot-password', [
            'pageTitle' => 'Wachtwoord Vergeten',
            'success' => 'Als dit e-mailadres bekend is, ontvang je binnen enkele minuten een link om je wachtwoord te resetten.',
            'email' => $email,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function showResetPassword(): string
    {
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';

        if (empty($token) || empty($email)) {
            return $this->view('pages/sales/reset-password', [
                'pageTitle' => 'Ongeldige Link',
                'error' => 'Deze reset link is ongeldig. Vraag een nieuwe aan.',
                'linkExpired' => true,
                'csrfToken' => $this->csrf()
            ]);
        }

        // Verify token and email match
        $stmt = $this->db->query(
            "SELECT id, reset_code, reset_code_expires FROM sales_users WHERE email = ?",
            [$email]
        );
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || $user['reset_code'] !== $token) {
            return $this->view('pages/sales/reset-password', [
                'pageTitle' => 'Ongeldige Link',
                'error' => 'Deze reset link is ongeldig of al gebruikt. Vraag een nieuwe aan.',
                'linkExpired' => true,
                'csrfToken' => $this->csrf()
            ]);
        }

        if (strtotime($user['reset_code_expires']) < time()) {
            return $this->view('pages/sales/reset-password', [
                'pageTitle' => 'Link Verlopen',
                'error' => 'Deze reset link is verlopen. Vraag een nieuwe aan.',
                'linkExpired' => true,
                'csrfToken' => $this->csrf()
            ]);
        }

        return $this->view('pages/sales/reset-password', [
            'pageTitle' => 'Nieuw Wachtwoord',
            'email' => $email,
            'token' => $token,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function resetPassword(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/sales/forgot-password?error=csrf');
        }

        $email = trim($_POST['email'] ?? '');
        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validate inputs
        if (empty($email) || empty($token)) {
            return $this->view('pages/sales/reset-password', [
                'pageTitle' => 'Ongeldige Link',
                'error' => 'Ongeldige reset link.',
                'linkExpired' => true,
                'csrfToken' => $this->csrf()
            ]);
        }

        if (strlen($password) < 8) {
            return $this->view('pages/sales/reset-password', [
                'pageTitle' => 'Nieuw Wachtwoord',
                'error' => 'Wachtwoord moet minimaal 8 karakters zijn',
                'email' => $email,
                'token' => $token,
                'csrfToken' => $this->csrf()
            ]);
        }

        if ($password !== $passwordConfirm) {
            return $this->view('pages/sales/reset-password', [
                'pageTitle' => 'Nieuw Wachtwoord',
                'error' => 'Wachtwoorden komen niet overeen',
                'email' => $email,
                'token' => $token,
                'csrfToken' => $this->csrf()
            ]);
        }

        // Verify token
        $stmt = $this->db->query(
            "SELECT id, reset_code, reset_code_expires FROM sales_users WHERE email = ?",
            [$email]
        );
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || $user['reset_code'] !== $token) {
            return $this->view('pages/sales/reset-password', [
                'pageTitle' => 'Ongeldige Link',
                'error' => 'Deze reset link is ongeldig of al gebruikt.',
                'linkExpired' => true,
                'csrfToken' => $this->csrf()
            ]);
        }

        if (strtotime($user['reset_code_expires']) < time()) {
            return $this->view('pages/sales/reset-password', [
                'pageTitle' => 'Link Verlopen',
                'error' => 'Deze reset link is verlopen. Vraag een nieuwe aan.',
                'linkExpired' => true,
                'csrfToken' => $this->csrf()
            ]);
        }

        // Update password and clear reset token
        $this->db->query(
            "UPDATE sales_users SET password = ?, reset_code = NULL, reset_code_expires = NULL WHERE id = ?",
            [password_hash($password, PASSWORD_BCRYPT), $user['id']]
        );

        // Set flash message and redirect to login
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Wachtwoord succesvol gewijzigd! Je kunt nu inloggen.'];
        return $this->redirect('/sales/login');
    }

    private function sendResetEmail(string $email, string $name, string $token): void
    {
        $resetUrl = "https://glamourschedule.com/sales/reset-password?email=" . urlencode($email) . "&token=" . $token;
        $subject = "Wachtwoord resetten - GlamourSchedule";
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr><td align="center">
            <table width="500" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;">
                <tr><td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                    <h1 style="margin:0;font-size:22px;">Wachtwoord Resetten</h1>
                </td></tr>
                <tr><td style="padding:35px;text-align:center;">
                    <p style="font-size:16px;color:#ffffff;margin:0 0 20px 0;">Hallo {$name},</p>
                    <p style="font-size:14px;color:#cccccc;margin:0 0 25px 0;">Je hebt een wachtwoord reset aangevraagd. Klik op de knop hieronder om een nieuw wachtwoord in te stellen:</p>
                    <p style="margin:25px 0;">
                        <a href="{$resetUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:16px;">
                            Wachtwoord Resetten
                        </a>
                    </p>
                    <p style="font-size:13px;color:#999;margin:25px 0 15px 0;">Deze link is 1 uur geldig.</p>
                    <p style="font-size:12px;color:#ccc;margin:0;word-break:break-all;">Of kopieer deze link:<br>{$resetUrl}</p>
                    <p style="font-size:13px;color:#999;margin:20px 0 0 0;">Heb je geen reset aangevraagd? Negeer deze e-mail.</p>
                </td></tr>
                <tr><td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                    <p style="margin:0;color:#cccccc;font-size:12px;">&copy; 2025 GlamourSchedule</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send password reset email: " . $e->getMessage());
        }
    }

    public function showRegister(): string
    {
        return $this->view('pages/sales/register', [
            'pageTitle' => 'Word Sales Partner',
            'csrfToken' => $this->csrf()
        ]);
    }

    public function register(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/sales/register?error=csrf');
        }

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'terms' => isset($_POST['terms'])
        ];

        $errors = [];

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'Voornaam is verplicht';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Achternaam is verplicht';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Geldig e-mailadres is verplicht';
        }

        if (!$data['terms']) {
            $errors['terms'] = 'Je moet akkoord gaan met de algemene voorwaarden';
        }

        // Check if email exists
        $stmt = $this->db->query("SELECT id, registration_paid, email_verified FROM sales_users WHERE email = ?", [$data['email']]);
        $existing = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($existing) {
            if ($existing['registration_paid']) {
                $errors['email'] = 'Dit e-mailadres is al in gebruik';
            } else {
                // Existing unpaid registration
                $_SESSION['sales_register_id'] = $existing['id'];
                $_SESSION['sales_register_email'] = $data['email'];

                if (!$existing['email_verified']) {
                    // Email not verified yet - send new code and redirect to verification
                    $this->sendVerificationCode($data['email']);
                    return $this->redirect('/sales/verify-email');
                } else {
                    // Email verified, redirect to payment
                    $_SESSION['sales_verify_user_id'] = $existing['id'];
                    return $this->redirect('/sales/payment');
                }
            }
        }

        if (!empty($errors)) {
            return $this->view('pages/sales/register', [
                'pageTitle' => 'Word Sales Partner',
                'errors' => $errors,
                'data' => $data,
                'csrfToken' => $this->csrf()
            ]);
        }

        // Generate unique referral code
        $fullName = $data['first_name'] . ' ' . $data['last_name'];
        $referralCode = $this->generateReferralCode($fullName);

        // Generate temporary password (user will set their own after first login)
        $tempPassword = bin2hex(random_bytes(8));

        // Create user with email_verified = 0 (needs verification first)
        $this->db->query(
            "INSERT INTO sales_users (email, password, name, first_name, last_name, referral_code, status, email_verified, registration_paid)
             VALUES (?, ?, ?, ?, ?, ?, 'pending', 0, 0)",
            [
                $data['email'],
                password_hash($tempPassword, PASSWORD_BCRYPT),
                $fullName,
                $data['first_name'],
                $data['last_name'],
                $referralCode
            ]
        );

        $salesUserId = $this->db->lastInsertId();
        $_SESSION['sales_register_id'] = $salesUserId;
        $_SESSION['sales_register_email'] = $data['email'];
        $_SESSION['sales_temp_password'] = $tempPassword;

        // Send verification code first, then payment after verification
        $this->sendVerificationCode($data['email']);

        return $this->redirect('/sales/verify-email');
    }

    private function createSalesPayment(int $salesUserId): string
    {
        // Get sales user
        $stmt = $this->db->query("SELECT * FROM sales_users WHERE id = ?", [$salesUserId]);
        $salesUser = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$salesUser) {
            return $this->redirect('/sales/register?error=notfound');
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($_ENV['MOLLIE_API_KEY']);

            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '0.99'
                ],
                'description' => 'GlamourSchedule Sales Partner Registratie',
                'redirectUrl' => 'https://glamourschedule.nl/sales/payment-complete',
                'webhookUrl' => 'https://glamourschedule.nl/webhook/sales-payment',
                'metadata' => [
                    'sales_user_id' => $salesUserId,
                    'type' => 'sales_registration'
                ]
            ]);

            // Store payment ID
            $this->db->query(
                "UPDATE sales_users SET payment_id = ? WHERE id = ?",
                [$payment->id, $salesUserId]
            );

            return $this->redirect($payment->getCheckoutUrl());
        } catch (\Exception $e) {
            error_log('Mollie payment error: ' . $e->getMessage());
            return $this->redirect('/sales/register?error=payment');
        }
    }

    public function paymentComplete(): string
    {
        $salesUserId = $_SESSION['sales_register_id'] ?? null;
        $tempPassword = $_SESSION['sales_temp_password'] ?? null;

        if (!$salesUserId) {
            return $this->redirect('/sales/login');
        }

        // Check payment status
        $stmt = $this->db->query("SELECT * FROM sales_users WHERE id = ?", [$salesUserId]);
        $salesUser = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$salesUser || !$salesUser['payment_id']) {
            return $this->redirect('/sales/register?error=notfound');
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($_ENV['MOLLIE_API_KEY']);

            $payment = $mollie->payments->get($salesUser['payment_id']);

            if ($payment->isPaid()) {
                // Mark as paid and active
                $this->db->query(
                    "UPDATE sales_users SET registration_paid = 1, status = 'active' WHERE id = ?",
                    [$salesUserId]
                );

                // Store IBAN if available
                if (!empty($payment->details->consumerAccount)) {
                    $this->db->query(
                        "UPDATE sales_users SET iban = ? WHERE id = ?",
                        [$payment->details->consumerAccount, $salesUserId]
                    );
                }

                // Send welcome email with login info
                $this->sendWelcomeEmail($salesUser, $tempPassword);

                // Clear session
                unset($_SESSION['sales_register_id']);
                unset($_SESSION['sales_temp_password']);

                return $this->view('pages/sales/payment-success', [
                    'pageTitle' => 'Registratie Voltooid',
                    'salesUser' => $salesUser
                ]);
            } else {
                return $this->view('pages/sales/payment-failed', [
                    'pageTitle' => 'Betaling Mislukt'
                ]);
            }
        } catch (\Exception $e) {
            error_log('Payment check error: ' . $e->getMessage());
            return $this->redirect('/sales/register?error=payment');
        }
    }

    private function sendWelcomeEmail(array $salesUser, ?string $tempPassword): void
    {
        $loginUrl = 'https://glamourschedule.nl/sales/login';
        $name = $salesUser['first_name'] ?? $salesUser['name'];

        $html = "
        <div style='font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;max-width:600px;margin:0 auto'>
            <div style='background:linear-gradient(135deg,#000000,#000000);padding:2rem;text-align:center;border-radius:12px 12px 0 0'>
                <h1 style='color:#ffffff;margin:0;font-size:1.5rem'>Welkom bij GlamourSchedule!</h1>
            </div>
            <div style='background:#0a0a0a;padding:2rem;border:1px solid #333;border-top:none;border-radius:0 0 12px 12px'>
                <p style='color:#374151;font-size:1.1rem;margin-top:0'>Beste {$name},</p>
                <p style='color:#374151;line-height:1.6'>
                    Bedankt voor je betaling! Je registratie als Sales Partner is nu compleet.
                </p>
                <div style='background:#ecfdf5;border:2px solid #333333;border-radius:12px;padding:1.5rem;margin:1.5rem 0'>
                    <p style='margin:0 0 1rem 0;color:#ffffff;font-weight:600'>Je kunt nu inloggen en je account verder afronden:</p>
                    <p style='margin:0;color:#374151'>
                        <strong>E-mail:</strong> {$salesUser['email']}<br>
                        <strong>Tijdelijk wachtwoord:</strong> {$tempPassword}
                    </p>
                </div>
                <p style='color:#374151;line-height:1.6'>
                    Na je eerste login kun je een nieuw wachtwoord instellen en je profiel aanvullen.
                </p>
                <div style='text-align:center;margin:2rem 0'>
                    <a href='{$loginUrl}' style='display:inline-block;background:linear-gradient(135deg,#333333,#000000);color:white;text-decoration:none;padding:1rem 2rem;border-radius:10px;font-weight:600;font-size:1.1rem'>
                        Nu Inloggen
                    </a>
                </div>
                <p style='color:#6b7280;font-size:0.9rem;margin-bottom:0'>
                    Je referral code: <strong style='color:#ffffff'>{$salesUser['referral_code']}</strong>
                </p>
            </div>
        </div>
        ";

        try {
            $mailer = new Mailer();
            $mailer->send($salesUser['email'], 'Welkom! Je Sales Partner account is actief', $html);
        } catch (\Exception $e) {
            error_log('Welcome email failed: ' . $e->getMessage());
        }
    }

    // ============================================================
    // EMAIL VERIFICATION
    // ============================================================

    public function showVerifyEmail(): string
    {
        $email = $_SESSION['sales_register_email'] ?? null;
        if (!$email) {
            return $this->redirect('/sales/register');
        }

        // Mask email
        $parts = explode('@', $email);
        $maskedEmail = substr($parts[0], 0, 2) . '***@' . $parts[1];

        return $this->view('pages/sales/verify-email', [
            'pageTitle' => 'Verifieer E-mail',
            'maskedEmail' => $maskedEmail,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function verifyEmail(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/sales/verify-email?error=csrf');
        }

        $email = $_SESSION['sales_register_email'] ?? null;
        if (!$email) {
            return $this->redirect('/sales/register');
        }

        $code = trim($_POST['code'] ?? '');
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return $this->showVerifyEmailWithError('invalid_code');
        }

        $stmt = $this->db->query(
            "SELECT id, name, verification_code, verification_code_expires FROM sales_users WHERE email = ?",
            [$email]
        );
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return $this->redirect('/sales/register');
        }

        if ($user['verification_code'] !== $code) {
            return $this->showVerifyEmailWithError('wrong_code');
        }

        if (strtotime($user['verification_code_expires']) < time()) {
            return $this->showVerifyEmailWithError('expired');
        }

        // Mark email as verified
        $this->db->query(
            "UPDATE sales_users SET email_verified = 1, verification_code = NULL WHERE id = ?",
            [$user['id']]
        );

        // Store user id for payment
        $_SESSION['sales_verify_user_id'] = $user['id'];

        return $this->redirect('/sales/payment');
    }

    public function resendVerificationCode(): string
    {
        $email = $_SESSION['sales_register_email'] ?? null;
        if (!$email) {
            return $this->redirect('/sales/register');
        }

        $this->sendVerificationCode($email);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Nieuwe code verstuurd!'];
        return $this->redirect('/sales/verify-email');
    }

    private function showVerifyEmailWithError(string $error): string
    {
        $email = $_SESSION['sales_register_email'] ?? '';
        $parts = explode('@', $email);
        $maskedEmail = substr($parts[0], 0, 2) . '***@' . ($parts[1] ?? '');

        return $this->view('pages/sales/verify-email', [
            'pageTitle' => 'Verifieer E-mail',
            'maskedEmail' => $maskedEmail,
            'error' => $error,
            'csrfToken' => $this->csrf()
        ]);
    }

    // ============================================================
    // PAYMENT
    // ============================================================

    public function showPayment(): string
    {
        $userId = $_SESSION['sales_verify_user_id'] ?? null;
        if (!$userId) {
            return $this->redirect('/sales/register');
        }

        $stmt = $this->db->query("SELECT * FROM sales_users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !$user['email_verified']) {
            return $this->redirect('/sales/register');
        }

        if ($user['registration_paid']) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Registratie voltooid! Je kunt nu inloggen.'];
            unset($_SESSION['sales_verify_user_id']);
            unset($_SESSION['sales_register_email']);
            return $this->redirect('/sales/login');
        }

        return $this->view('pages/sales/payment', [
            'pageTitle' => 'Registratie Voltooien',
            'user' => $user,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function processPayment(): string
    {
        error_log("SalesController::processPayment() called");

        if (!$this->verifyCsrf()) {
            error_log("CSRF verification failed");
            return $this->redirect('/sales/payment?error=csrf');
        }

        $userId = $_SESSION['sales_verify_user_id'] ?? null;
        error_log("User ID from session: " . ($userId ?? 'null'));

        if (!$userId) {
            error_log("No user ID in session, redirecting to register");
            return $this->redirect('/sales/register');
        }

        $stmt = $this->db->query("SELECT * FROM sales_users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !$user['email_verified']) {
            error_log("User not found or email not verified");
            return $this->redirect('/sales/register');
        }

        // Create Mollie payment
        try {
            $apiKey = $_ENV['MOLLIE_API_KEY'] ?? getenv('MOLLIE_API_KEY');
            error_log("Mollie API key: " . substr($apiKey, 0, 10) . "...");

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($apiKey);

            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '0.99'
                ],
                'description' => 'GlamourSchedule Sales Partner Registratie',
                'redirectUrl' => 'https://glamourschedule.nl/sales/payment/complete',
                'webhookUrl' => 'https://glamourschedule.nl/sales/payment/webhook',
                'metadata' => [
                    'sales_user_id' => $userId,
                    'type' => 'sales_registration'
                ]
            ]);

            // Store payment ID
            $this->db->query(
                "UPDATE sales_users SET payment_id = ? WHERE id = ?",
                [$payment->id, $userId]
            );

            return $this->redirect($payment->getCheckoutUrl());

        } catch (\Exception $e) {
            error_log("Mollie payment error: " . $e->getMessage());
            return $this->view('pages/sales/payment', [
                'pageTitle' => 'Registratie Voltooien',
                'user' => $user,
                'error' => 'Er ging iets mis met de betaling. Probeer het opnieuw.',
                'csrfToken' => $this->csrf()
            ]);
        }
    }

    public function paymentWebhook(): string
    {
        $paymentId = $_POST['id'] ?? null;
        if (!$paymentId) {
            http_response_code(400);
            return '';
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($_ENV['MOLLIE_API_KEY'] ?? getenv('MOLLIE_API_KEY'));

            $payment = $mollie->payments->get($paymentId);

            if ($payment->isPaid()) {
                $userId = $payment->metadata->sales_user_id ?? null;
                if ($userId) {
                    // Update user status
                    $this->db->query(
                        "UPDATE sales_users SET registration_paid = 1, status = 'active' WHERE id = ?",
                        [$userId]
                    );

                    // Get user details and send admin notification
                    $stmt = $this->db->query("SELECT * FROM sales_users WHERE id = ?", [$userId]);
                    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($user) {
                        $this->sendAdminNotification($user, $payment->amount->value);
                    }
                }
            }

            http_response_code(200);
            return '';

        } catch (\Exception $e) {
            error_log("Mollie webhook error: " . $e->getMessage());
            http_response_code(500);
            return '';
        }
    }

    private function sendAdminNotification(array $user, string $amount): void
    {
        $adminEmail = 'jjt-services@outlook.com';
        $subject = "Nieuwe Sales Partner Registratie: {$user['name']}";

        $html = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr><td align="center">
            <table width="500" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;">
                <tr><td style="background:#000000;color:#ffffff;padding:25px;text-align:center;">
                    <h1 style="margin:0;font-size:20px;">Nieuwe Sales Partner!</h1>
                </td></tr>
                <tr><td style="padding:25px;">
                    <p style="margin:0 0 15px;color:#ffffff;">Er is een nieuwe sales partner geregistreerd en heeft betaald.</p>

                    <table style="width:100%;border-collapse:collapse;">
                        <tr>
                            <td style="padding:8px 0;color:#cccccc;border-bottom:1px solid #eee;">Naam:</td>
                            <td style="padding:8px 0;color:#000;font-weight:600;border-bottom:1px solid #eee;">{$user['name']}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;color:#cccccc;border-bottom:1px solid #eee;">Email:</td>
                            <td style="padding:8px 0;color:#000;border-bottom:1px solid #eee;">{$user['email']}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;color:#cccccc;border-bottom:1px solid #eee;">Telefoon:</td>
                            <td style="padding:8px 0;color:#000;border-bottom:1px solid #eee;">{$user['phone']}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;color:#cccccc;border-bottom:1px solid #eee;">Referral Code:</td>
                            <td style="padding:8px 0;color:#000;font-weight:600;font-family:monospace;letter-spacing:1px;border-bottom:1px solid #eee;">{$user['referral_code']}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 0;color:#cccccc;">Betaald:</td>
                            <td style="padding:8px 0;color:#22c55e;font-weight:600;">&euro;{$amount}</td>
                        </tr>
                    </table>

                    <p style="margin:20px 0 0;text-align:center;">
                        <a href="https://glamourschedule.com/admin" style="display:inline-block;background:#000;color:#fff;padding:12px 25px;text-decoration:none;border-radius:8px;font-weight:600;">
                            Bekijk in Admin
                        </a>
                    </p>
                </td></tr>
                <tr><td style="background:#0a0a0a;padding:15px;text-align:center;border-top:1px solid #333;">
                    <p style="margin:0;color:#999;font-size:12px;">GlamourSchedule Sales Notificatie</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send($adminEmail, $subject, $html);
        } catch (\Exception $e) {
            error_log("Failed to send admin notification: " . $e->getMessage());
        }
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    private function sendVerificationCode(string $email): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $this->db->query(
            "UPDATE sales_users SET verification_code = ?, verification_code_expires = ? WHERE email = ?",
            [$code, $expires, $email]
        );

        $stmt = $this->db->query("SELECT name FROM sales_users WHERE email = ?", [$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        $name = $user['name'] ?? 'Partner';

        $this->sendVerificationEmail($email, $name, $code);
    }

    private function sendVerificationEmail(string $email, string $name, string $code): void
    {
        $subject = "Je verificatiecode: $code";
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr><td align="center">
            <table width="500" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#333333,#000000);color:#ffffff;padding:30px;text-align:center;">
                    <h1 style="margin:0;font-size:22px;">Verificatiecode</h1>
                </td></tr>
                <tr><td style="padding:35px;text-align:center;">
                    <p style="font-size:16px;color:#ffffff;margin:0 0 20px 0;">Hallo {$name},</p>
                    <p style="font-size:14px;color:#cccccc;margin:0 0 25px 0;">Gebruik deze code om je e-mailadres te verifiëren:</p>
                    <div style="background:#f0fdf4;border:2px solid #333333;border-radius:12px;padding:20px;margin:0 0 25px 0;">
                        <span style="font-size:36px;font-weight:bold;color:#ffffff333;letter-spacing:8px;font-family:monospace;">{$code}</span>
                    </div>
                    <p style="font-size:13px;color:#999;margin:0;">Deze code is 10 minuten geldig.</p>
                </td></tr>
                <tr><td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                    <p style="margin:0;color:#cccccc;font-size:12px;">&copy; 2025 GlamourSchedule</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send verification email: " . $e->getMessage());
        }
    }

    private function getStats(): array
    {
        $userId = $this->salesUser['id'];

        // Total referrals
        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM sales_referrals WHERE sales_user_id = ?",
            [$userId]
        );
        $totalReferrals = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Converted (paid after trial, waiting for payout)
        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'converted'",
            [$userId]
        );
        $convertedReferrals = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Paid out (completed)
        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'paid'",
            [$userId]
        );
        $paidReferrals = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // In trial (pending)
        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'pending'",
            [$userId]
        );
        $pendingReferrals = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Cancelled/failed
        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM sales_referrals WHERE sales_user_id = ? AND status IN ('cancelled', 'failed', 'expired')",
            [$userId]
        );
        $cancelledReferrals = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Total earnings paid out
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(commission), 0) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'paid'",
            [$userId]
        );
        $totalEarnings = (float)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Pending earnings (converted, waiting for payout)
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(commission), 0) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'converted'",
            [$userId]
        );
        $pendingEarnings = (float)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Potential earnings (in trial)
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(commission), 0) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'pending'",
            [$userId]
        );
        $potentialEarnings = (float)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        return [
            'totalReferrals' => $totalReferrals,
            'convertedReferrals' => $convertedReferrals,
            'paidReferrals' => $paidReferrals,
            'pendingReferrals' => $pendingReferrals,
            'cancelledReferrals' => $cancelledReferrals,
            'totalEarnings' => $totalEarnings,
            'pendingEarnings' => $pendingEarnings,
            'potentialEarnings' => $potentialEarnings,
            'conversionRate' => $totalReferrals > 0 ? round((($convertedReferrals + $paidReferrals) / $totalReferrals) * 100, 1) : 0
        ];
    }

    /**
     * Get referrals currently in trial period with days remaining
     */
    private function getTrialReferrals(): array
    {
        $stmt = $this->db->query(
            "SELECT sr.*, b.company_name, b.email, b.trial_ends_at, b.created_at as business_created,
                    DATEDIFF(b.trial_ends_at, NOW()) as days_remaining
             FROM sales_referrals sr
             JOIN businesses b ON sr.business_id = b.id
             WHERE sr.sales_user_id = ?
               AND sr.status = 'pending'
               AND b.subscription_status = 'trial'
               AND b.trial_ends_at > NOW()
             ORDER BY b.trial_ends_at ASC",
            [$this->salesUser['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getRecentReferrals(): array
    {
        $stmt = $this->db->query(
            "SELECT sr.*, b.company_name, b.created_at as business_created
             FROM sales_referrals sr
             JOIN businesses b ON sr.business_id = b.id
             WHERE sr.sales_user_id = ?
             ORDER BY sr.created_at DESC
             LIMIT 10",
            [$this->salesUser['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getAllReferrals(): array
    {
        $stmt = $this->db->query(
            "SELECT sr.*, b.company_name, b.email, b.created_at as business_created, b.subscription_status
             FROM sales_referrals sr
             JOIN businesses b ON sr.business_id = b.id
             WHERE sr.sales_user_id = ?
             ORDER BY sr.created_at DESC",
            [$this->salesUser['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getPendingPayouts(): float
    {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(commission), 0) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'converted'",
            [$this->salesUser['id']]
        );
        return (float)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }

    private function getPendingAmount(): float
    {
        return $this->getPendingPayouts();
    }

    private function getPayoutHistory(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM sales_payouts WHERE sales_partner_id = ? ORDER BY created_at DESC",
            [$this->salesUser['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function generateReferralCode(string $name): string
    {
        $base = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 4));
        $code = $base . rand(1000, 9999);

        // Ensure unique
        $stmt = $this->db->query("SELECT id FROM sales_users WHERE referral_code = ?", [$code]);
        while ($stmt->fetch()) {
            $code = $base . rand(1000, 9999);
            $stmt = $this->db->query("SELECT id FROM sales_users WHERE referral_code = ?", [$code]);
        }

        return $code;
    }

    // ============================================================
    // ACCOUNT SETTINGS
    // ============================================================

    public function showAccountSettings(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        return $this->view('pages/sales/account', [
            'pageTitle' => 'Account Instellingen',
            'salesUser' => $this->salesUser,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function updateAccount(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        if (!$this->verifyCsrf()) {
            return $this->redirect('/sales/account?error=csrf');
        }

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'iban' => trim($_POST['iban'] ?? '')
        ];

        $errors = [];

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'Voornaam is verplicht';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Achternaam is verplicht';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Geldig e-mailadres is verplicht';
        }

        // Check if email is taken by another user
        if ($data['email'] !== $this->salesUser['email']) {
            $stmt = $this->db->query("SELECT id FROM sales_users WHERE email = ? AND id != ?", [$data['email'], $this->salesUser['id']]);
            if ($stmt->fetch()) {
                $errors['email'] = 'Dit e-mailadres is al in gebruik';
            }
        }

        // Validate IBAN format if provided
        if (!empty($data['iban'])) {
            $iban = strtoupper(preg_replace('/\s+/', '', $data['iban']));
            if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/', $iban)) {
                $errors['iban'] = 'Ongeldig IBAN formaat';
            } else {
                $data['iban'] = $iban;
            }
        }

        if (!empty($errors)) {
            // Refresh user data
            $stmt = $this->db->query("SELECT * FROM sales_users WHERE id = ?", [$this->salesUser['id']]);
            $this->salesUser = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $this->view('pages/sales/account', [
                'pageTitle' => 'Account Instellingen',
                'salesUser' => $this->salesUser,
                'errors' => $errors,
                'data' => $data,
                'csrfToken' => $this->csrf()
            ]);
        }

        // Check if IBAN is being changed/added
        $currentIban = $this->salesUser['iban'] ?? '';
        $newIban = $data['iban'] ?? '';

        // Update account (without IBAN - IBAN requires verification)
        $fullName = $data['first_name'] . ' ' . $data['last_name'];
        $this->db->query(
            "UPDATE sales_users SET first_name = ?, last_name = ?, name = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?",
            [$data['first_name'], $data['last_name'], $fullName, $data['email'], $data['phone'], $this->salesUser['id']]
        );

        // If IBAN is being changed/added, redirect to verification
        if (!empty($newIban) && $newIban !== $currentIban) {
            $_SESSION['pending_sales_iban'] = $newIban;
            $_SESSION['flash'] = ['type' => 'info', 'message' => 'Overige gegevens bijgewerkt. Verifieer je bankrekening via een betaling van €0,01.'];
            return $this->redirect('/sales/verify-iban');
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account gegevens bijgewerkt!'];
        return $this->redirect('/sales/account');
    }

    public function updatePassword(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        if (!$this->verifyCsrf()) {
            return $this->redirect('/sales/account?error=csrf');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Verify current password
        if (!password_verify($currentPassword, $this->salesUser['password'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Huidig wachtwoord is onjuist'];
            return $this->redirect('/sales/account');
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nieuw wachtwoord moet minimaal 8 karakters zijn'];
            return $this->redirect('/sales/account');
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Wachtwoorden komen niet overeen'];
            return $this->redirect('/sales/account');
        }

        // Update password
        $this->db->query(
            "UPDATE sales_users SET password = ?, updated_at = NOW() WHERE id = ?",
            [password_hash($newPassword, PASSWORD_BCRYPT), $this->salesUser['id']]
        );

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Wachtwoord succesvol gewijzigd!'];
        return $this->redirect('/sales/account');
    }

    /**
     * Delete sales partner account
     */
    public function deleteAccount(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return $this->redirect('/sales/account');
        }

        $confirmText = trim($_POST['confirm_text'] ?? '');

        if ($confirmText !== 'VERWIJDER') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Bevestigingstekst onjuist'];
            return $this->redirect('/sales/account');
        }

        $userId = $this->salesUser['id'];

        try {
            // Delete related records first
            $this->db->query("DELETE FROM sales_commissions WHERE sales_user_id = ?", [$userId]);
            $this->db->query("DELETE FROM sales_payouts WHERE sales_user_id = ?", [$userId]);
            $this->db->query("DELETE FROM sales_iban_verifications WHERE sales_user_id = ?", [$userId]);

            // Clear referral links from businesses
            $this->db->query("UPDATE businesses SET referred_by = NULL WHERE referred_by = ?", [$userId]);

            // Delete the sales user
            $this->db->query("DELETE FROM sales_users WHERE id = ?", [$userId]);

            // Clear session
            unset($_SESSION['sales_user_id']);
            unset($_SESSION['sales_2fa_user_id']);
            unset($_SESSION['sales_2fa_email']);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Je account is succesvol verwijderd'];
            return $this->redirect('/sales/login');

        } catch (\Exception $e) {
            error_log("Sales account deletion error: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Fout bij verwijderen account'];
            return $this->redirect('/sales/account');
        }
    }

    /**
     * Show IBAN verification page
     */
    public function showVerifyIban(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        $pendingIban = $_SESSION['pending_sales_iban'] ?? null;

        return $this->view('pages/sales/verify-iban', [
            'pageTitle' => 'Bankrekening Verificatie',
            'salesUser' => $this->salesUser,
            'pendingIban' => $pendingIban,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Initiate IBAN verification via Mollie payment
     */
    public function initiateIbanVerification(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return $this->redirect('/sales/account');
        }

        $mollieApiKey = $this->config['mollie']['api_key'] ?? '';

        if (empty($mollieApiKey)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betalingssysteem niet geconfigureerd'];
            return $this->redirect('/sales/account');
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($mollieApiKey);

            // Create unique payment reference
            $reference = 'SALES-IBAN-' . $this->salesUser['id'] . '-' . time();

            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '0.01'
                ],
                'description' => 'GlamourSchedule Sales IBAN Verificatie',
                'redirectUrl' => 'https://glamourschedule.nl/sales/iban/complete?ref=' . $reference,
                'webhookUrl' => 'https://glamourschedule.nl/api/webhooks/mollie',
                'method' => 'ideal',
                'metadata' => [
                    'type' => 'sales_iban_verification',
                    'sales_user_id' => $this->salesUser['id'],
                    'reference' => $reference
                ]
            ]);

            // Store payment reference
            $this->db->query(
                "INSERT INTO sales_iban_verifications (sales_user_id, verification_code, mollie_payment_id, status, expires_at)
                 VALUES (?, ?, ?, 'payment_pending', DATE_ADD(NOW(), INTERVAL 1 HOUR))",
                [$this->salesUser['id'], $reference, $payment->id]
            );

            // Redirect to Mollie payment
            return $this->redirect($payment->getCheckoutUrl());

        } catch (\Exception $e) {
            error_log("Sales IBAN verification error: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Fout bij starten verificatie: ' . $e->getMessage()];
            return $this->redirect('/sales/account');
        }
    }

    /**
     * Handle IBAN verification completion
     */
    public function ibanVerificationComplete(): string
    {
        if (!$this->requireAuth()) {
            return $this->redirect('/sales/login');
        }

        $reference = $_GET['ref'] ?? '';

        if (empty($reference)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige verificatie referentie'];
            return $this->redirect('/sales/account');
        }

        try {
            // Get verification record
            $stmt = $this->db->query(
                "SELECT * FROM sales_iban_verifications WHERE sales_user_id = ? AND verification_code = ? ORDER BY id DESC LIMIT 1",
                [$this->salesUser['id'], $reference]
            );
            $verification = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$verification) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Verificatie niet gevonden'];
                return $this->redirect('/sales/account');
            }

            // Check payment status with Mollie
            $mollieApiKey = $this->config['mollie']['api_key'] ?? '';
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($mollieApiKey);

            $payment = $mollie->payments->get($verification['mollie_payment_id']);

            if ($payment->isPaid()) {
                // Get IBAN from payment details
                $details = $payment->details;
                if ($details) {
                    $iban = $details->consumerAccount ?? null;
                    $accountHolder = $details->consumerName ?? null;

                    if ($iban && $accountHolder) {
                        // Update sales user with verified IBAN
                        $this->db->query(
                            "UPDATE sales_users SET iban = ?, account_holder = ?, iban_verified = 1, iban_changed_at = NOW() WHERE id = ?",
                            [$iban, $accountHolder, $this->salesUser['id']]
                        );

                        // Update verification record
                        $this->db->query(
                            "UPDATE sales_iban_verifications SET iban = ?, account_holder = ?, status = 'verified', verified_at = NOW()
                             WHERE sales_user_id = ? AND mollie_payment_id = ?",
                            [$iban, $accountHolder, $this->salesUser['id'], $payment->id]
                        );

                        // Clear pending IBAN
                        unset($_SESSION['pending_sales_iban']);

                        // Send confirmation email
                        $this->sendIbanVerifiedEmail($iban);

                        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Bankrekening succesvol geverifieerd!'];
                        return $this->redirect('/sales/account');
                    }
                }

                // Payment successful but couldn't get IBAN details
                $this->db->query("UPDATE sales_iban_verifications SET status = 'failed' WHERE id = ?", [$verification['id']]);
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Kon IBAN niet ophalen uit betaling'];
                return $this->redirect('/sales/account');
            }

            // Payment not completed
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Betaling niet voltooid. Probeer opnieuw.'];
            return $this->redirect('/sales/verify-iban');

        } catch (\Exception $e) {
            error_log("Sales IBAN verification complete error: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Fout bij verificatie: ' . $e->getMessage()];
            return $this->redirect('/sales/account');
        }
    }

    /**
     * Send IBAN verified confirmation email
     */
    private function sendIbanVerifiedEmail(string $iban): void
    {
        $maskedIban = substr($iban, 0, 4) . ' **** **** ' . substr($iban, -4);

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:#000000;padding:40px;text-align:center;color:#fff;">
                            <h1 style="margin:0;font-size:24px;">Bankrekening Geverifieerd</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;">Hallo <strong>{$this->salesUser['name']}</strong>,</p>
                            <p style="font-size:16px;color:#555;line-height:1.6;">
                                Je bankrekening is succesvol geverifieerd en gekoppeld aan je Sales Partner account.
                            </p>

                            <div style="background:#0a0a0a;border-radius:12px;padding:25px;margin:25px 0;text-align:center;">
                                <p style="margin:0;color:#cccccc;font-size:14px;">Gekoppelde bankrekening</p>
                                <p style="margin:10px 0 0;color:#ffffff;font-size:20px;font-weight:700;letter-spacing:2px;">
                                    {$maskedIban}
                                </p>
                            </div>

                            <p style="font-size:14px;color:#888;text-align:center;">
                                Vanaf nu ontvang je uitbetalingen op dit rekeningnummer.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#cccccc;font-size:13px;">GlamourSchedule Sales Partner</p>
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
            $mailer = new Mailer();
            $mailer->send($this->salesUser['email'], "Bankrekening Geverifieerd - GlamourSchedule", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send sales IBAN verified email: " . $e->getMessage());
        }
    }

    // ============================================================
    // STATIC NOTIFICATION METHODS (called from other controllers)
    // ============================================================

    /**
     * Notify sales partner when a business registers via their referral
     */
    public static function notifySalesPartnerNewRegistration(array $salesUser, array $business, float $commission): void
    {
        $isEarlyBird = !empty($business['is_early_adopter']);
        $registrationType = $isEarlyBird ? 'Early Bird' : 'Standaard';

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                <tr><td style="background:linear-gradient(135deg,#22c55e,#16a34a);padding:40px;text-align:center;">
                    <h1 style="margin:0;color:#ffffff;font-size:28px;">Nieuwe Aanmelding!</h1>
                    <p style="margin:10px 0 0;color:#ffffff;opacity:0.9;">Via jouw referral code</p>
                </td></tr>
                <tr><td style="padding:40px;">
                    <p style="font-size:18px;color:#ffffff;margin:0 0 20px;">Hoi {$salesUser['name']},</p>

                    <p style="font-size:16px;color:#555;line-height:1.6;margin:0 0 25px;">
                        Geweldig nieuws! Er is een nieuwe salon geregistreerd via jouw referral code.
                    </p>

                    <div style="background:#f9fafb;border-radius:12px;padding:25px;margin:0 0 25px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:8px 0;color:#cccccc;">Salon:</td>
                                <td style="padding:8px 0;color:#000;font-weight:600;text-align:right;">{$business['company_name']}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px 0;color:#cccccc;">Type:</td>
                                <td style="padding:8px 0;color:#000;text-align:right;">
                                    <span style="background:#f59e0b;color:#000;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">{$registrationType}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:8px 0;color:#cccccc;">Status:</td>
                                <td style="padding:8px 0;color:#f59e0b;font-weight:600;text-align:right;">In proeftijd (14 dagen)</td>
                            </tr>
                            <tr style="border-top:1px solid #e5e7eb;">
                                <td style="padding:15px 0 8px;color:#cccccc;font-weight:600;">Potentiële commissie:</td>
                                <td style="padding:15px 0 8px;color:#22c55e;font-weight:700;font-size:24px;text-align:right;">€{$commission}</td>
                            </tr>
                        </table>
                    </div>

                    <p style="font-size:14px;color:#cccccc;line-height:1.6;margin:0 0 25px;">
                        De salon heeft nu 14 dagen om het platform te testen. Als ze na de proeftijd hun abonnement activeren,
                        ontvang je <strong>€{$commission}</strong> commissie!
                    </p>

                    <a href="https://glamourschedule.nl/sales/mijn-salons" style="display:block;background:#000;color:#fff;text-decoration:none;padding:16px 30px;border-radius:10px;font-weight:600;text-align:center;">
                        Bekijk in Dashboard
                    </a>
                </td></tr>
                <tr><td style="background:#f9fafb;padding:25px;text-align:center;border-top:1px solid #e5e5e5;">
                    <p style="margin:0;color:#999;font-size:12px;">© 2026 GlamourSchedule Sales</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send($salesUser['email'], "Nieuwe aanmelding via jouw code - {$business['company_name']}", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send sales new registration notification: " . $e->getMessage());
        }
    }

    /**
     * Notify sales partner when a business activates their subscription (commission earned!)
     */
    public static function notifySalesPartnerActivation(array $salesUser, array $business, float $commission): void
    {
        $isEarlyBird = !empty($business['is_early_adopter']);

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                <tr><td style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:40px;text-align:center;">
                    <div style="font-size:60px;margin-bottom:10px;">🎉</div>
                    <h1 style="margin:0;color:#000;font-size:28px;">Commissie Verdiend!</h1>
                    <p style="margin:10px 0 0;color:#000;opacity:0.8;">Een salon heeft geactiveerd</p>
                </td></tr>
                <tr><td style="padding:40px;">
                    <p style="font-size:18px;color:#ffffff;margin:0 0 20px;">Hoi {$salesUser['name']},</p>

                    <p style="font-size:16px;color:#555;line-height:1.6;margin:0 0 25px;">
                        Fantastisch nieuws! <strong>{$business['company_name']}</strong> heeft zojuist hun abonnement geactiveerd.
                        Je hebt commissie verdiend!
                    </p>

                    <div style="background:linear-gradient(135deg,#22c55e,#16a34a);border-radius:16px;padding:30px;text-align:center;margin:0 0 25px;">
                        <p style="margin:0 0 5px;color:#fff;opacity:0.9;font-size:14px;">Jouw commissie</p>
                        <p style="margin:0;color:#fff;font-size:48px;font-weight:800;">€{$commission}</p>
                    </div>

                    <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:0 0 25px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:8px 0;color:#cccccc;">Salon:</td>
                                <td style="padding:8px 0;color:#000;font-weight:600;text-align:right;">{$business['company_name']}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px 0;color:#cccccc;">Plaats:</td>
                                <td style="padding:8px 0;color:#000;text-align:right;">{$business['city']}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px 0;color:#cccccc;">Status:</td>
                                <td style="padding:8px 0;text-align:right;">
                                    <span style="background:#22c55e;color:#fff;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">Geactiveerd</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <p style="font-size:14px;color:#cccccc;line-height:1.6;margin:0 0 25px;">
                        Dit bedrag wordt toegevoegd aan je uitstaande saldo en wordt uitbetaald zodra je de minimale uitbetalingsdrempel bereikt.
                    </p>

                    <a href="https://glamourschedule.nl/sales/payouts" style="display:block;background:#000;color:#fff;text-decoration:none;padding:16px 30px;border-radius:10px;font-weight:600;text-align:center;">
                        Bekijk Uitbetalingen
                    </a>
                </td></tr>
                <tr><td style="background:#f9fafb;padding:25px;text-align:center;border-top:1px solid #e5e5e5;">
                    <p style="margin:0;color:#999;font-size:12px;">© 2026 GlamourSchedule Sales</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send($salesUser['email'], "Commissie verdiend! {$business['company_name']} heeft geactiveerd", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send sales activation notification: " . $e->getMessage());
        }
    }
}

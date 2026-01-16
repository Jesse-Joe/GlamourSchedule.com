<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;

class SalesController extends Controller
{
    private ?array $salesUser = null;

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
        $pendingPayouts = $this->getPendingPayouts();

        return $this->view('pages/sales/dashboard', [
            'pageTitle' => 'Sales Dashboard',
            'salesUser' => $this->salesUser,
            'stats' => $stats,
            'recentReferrals' => $recentReferrals,
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

        // Build email content
        $subject = "25 euro korting op GlamourSchedule - Exclusieve aanbieding";

        $personalLine = !empty($personalMessage) ? "<p style='font-style:italic;color:#6b7280;border-left:3px solid #333333;padding-left:1rem;margin-bottom:1.5rem'>{$personalMessage}</p>" : "";

        $htmlBody = "
        <div style='font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;max-width:600px;margin:0 auto'>
            <div style='background:linear-gradient(135deg,#000000,#000000);padding:2rem;text-align:center;border-radius:12px 12px 0 0'>
                <h1 style='color:#ffffff;margin:0;font-size:1.5rem'>GlamourSchedule</h1>
                <p style='color:#cccccc;margin:0.5rem 0 0 0'>Het slimste boekingssysteem voor salons</p>
            </div>

            <div style='background:#fafafa;padding:2rem;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 12px 12px'>
                <p style='color:#374151;font-size:1.1rem;margin-top:0'>Beste {$salonName},</p>

                {$personalLine}

                <p style='color:#374151;line-height:1.6'>
                    Ik had graag even langs willen komen, maar helaas is daar geen tijd voor.
                    Daarom neem ik via deze weg contact met je op over GlamourSchedule.
                </p>

                <p style='color:#374151;line-height:1.6'>
                    Via {$salesName} ontvang je <strong style='color:#000000'>€25 korting</strong> op je registratie!
                </p>

                <div style='background:#f0fdf4;border-left:4px solid #000000;border-radius:8px;padding:1.5rem;margin:1.5rem 0'>
                    <p style='margin:0 0 0.75rem 0;color:#000000;font-weight:700;font-size:1.1rem'>Waarom GlamourSchedule anders is</p>
                    <p style='color:#374151;line-height:1.6;margin:0 0 0.75rem 0'>
                        Bij GlamourSchedule betaal je <strong>geen maandelijks abonnement</strong> en <strong>geen vaste kosten</strong>.
                    </p>
                    <p style='color:#374151;line-height:1.6;margin:0 0 0.75rem 0'>
                        Heb je een rustige periode, een dip in boekingen of ga je op vakantie?<br>
                        👉 <strong>Dan betaal je helemaal niets.</strong>
                    </p>
                    <p style='color:#374151;line-height:1.6;margin:0'>
                        Je betaalt pas <strong>€1,75 per boeking</strong>, alleen wanneer je écht klanten ontvangt.<br>
                        Dat maakt GlamourSchedule eerlijk, flexibel en risicoloos.
                    </p>
                </div>

                <div style='background:#ecfdf5;border:2px solid #333333;border-radius:12px;padding:1.5rem;margin:1.5rem 0;text-align:center'>
                    <p style='margin:0 0 0.5rem 0;color:#000000;font-weight:600'>Jouw exclusieve korting:</p>
                    <p style='margin:0;font-size:1.5rem;font-weight:700;color:#000000'>€25,- korting</p>
                    <p style='margin:0.5rem 0 0 0;color:#6b7280;font-size:0.9rem'>Normale prijs: €99,99 - Jouw prijs: €74,99</p>
                </div>

                <p style='color:#374151;font-weight:600;margin-bottom:0.5rem'>Wat krijg je?</p>
                <ul style='color:#374151;line-height:1.8;padding-left:1.25rem'>
                    <li>Online boekingen 24/7</li>
                    <li>Automatische herinneringen aan klanten</li>
                    <li>Betalingen via iDEAL</li>
                    <li>Eigen professionele salonpagina</li>
                    <li>Klantenbeheer dashboard</li>
                    <li>14 dagen gratis proberen</li>
                </ul>

                <div style='text-align:center;margin:2rem 0'>
                    <a href='{$referralLink}' style='display:inline-block;background:linear-gradient(135deg,#333333,#000000);color:white;text-decoration:none;padding:1rem 2rem;border-radius:10px;font-weight:600;font-size:1.1rem'>
                        Registreer nu met korting
                    </a>
                </div>

                <p style='color:#6b7280;font-size:0.9rem;text-align:center;margin-bottom:0'>
                    Of kopieer deze link: <span style='color:#000000'>{$referralLink}</span>
                </p>
            </div>

            <p style='text-align:center;color:#9ca3af;font-size:0.8rem;margin-top:1rem'>
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

        $_SESSION['sales_user_id'] = $user['id'];
        return $this->redirect('/sales/dashboard');
    }

    public function logout(): string
    {
        unset($_SESSION['sales_user_id']);
        return $this->redirect('/sales/login');
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
        $stmt = $this->db->query("SELECT id, registration_paid FROM sales_users WHERE email = ?", [$data['email']]);
        $existing = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($existing) {
            if ($existing['registration_paid']) {
                $errors['email'] = 'Dit e-mailadres is al in gebruik';
            } else {
                // Existing unpaid registration - redirect to payment
                $_SESSION['sales_register_id'] = $existing['id'];
                return $this->createSalesPayment($existing['id']);
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

        $this->db->query(
            "INSERT INTO sales_users (email, password, name, first_name, last_name, referral_code, status, email_verified, registration_paid)
             VALUES (?, ?, ?, ?, ?, ?, 'pending', 1, 0)",
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
        $_SESSION['sales_temp_password'] = $tempPassword;

        // Create payment and redirect
        return $this->createSalesPayment($salesUserId);
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
                <h1 style='color:#000000;margin:0;font-size:1.5rem'>Welkom bij GlamourSchedule!</h1>
            </div>
            <div style='background:#fafafa;padding:2rem;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 12px 12px'>
                <p style='color:#374151;font-size:1.1rem;margin-top:0'>Beste {$name},</p>
                <p style='color:#374151;line-height:1.6'>
                    Bedankt voor je betaling! Je registratie als Sales Partner is nu compleet.
                </p>
                <div style='background:#ecfdf5;border:2px solid #333333;border-radius:12px;padding:1.5rem;margin:1.5rem 0'>
                    <p style='margin:0 0 1rem 0;color:#000000;font-weight:600'>Je kunt nu inloggen en je account verder afronden:</p>
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
                    Je referral code: <strong style='color:#000000'>{$salesUser['referral_code']}</strong>
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
                    $this->db->query(
                        "UPDATE sales_users SET registration_paid = 1, status = 'active' WHERE id = ?",
                        [$userId]
                    );
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

    // ============================================================
    // HELPER METHODS
    // ============================================================

    private function sendVerificationCode(string $email): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

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
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr><td align="center">
            <table width="500" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#333333,#000000);color:#ffffff;padding:30px;text-align:center;">
                    <h1 style="margin:0;font-size:22px;">Verificatiecode</h1>
                </td></tr>
                <tr><td style="padding:35px;text-align:center;">
                    <p style="font-size:16px;color:#333;margin:0 0 20px 0;">Hallo {$name},</p>
                    <p style="font-size:14px;color:#666;margin:0 0 25px 0;">Gebruik deze code om je e-mailadres te verifiëren:</p>
                    <div style="background:#f0fdf4;border:2px solid #333333;border-radius:12px;padding:20px;margin:0 0 25px 0;">
                        <span style="font-size:36px;font-weight:bold;color:#333333;letter-spacing:8px;font-family:monospace;">{$code}</span>
                    </div>
                    <p style="font-size:13px;color:#999;margin:0;">Deze code is 30 minuten geldig.</p>
                </td></tr>
                <tr><td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                    <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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

        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM sales_referrals WHERE sales_user_id = ?",
            [$userId]
        );
        $totalReferrals = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'converted'",
            [$userId]
        );
        $convertedReferrals = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(commission), 0) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'paid'",
            [$userId]
        );
        $totalEarnings = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(commission), 0) as total FROM sales_referrals WHERE sales_user_id = ? AND status = 'converted'",
            [$userId]
        );
        $pendingEarnings = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        return [
            'totalReferrals' => $totalReferrals,
            'convertedReferrals' => $convertedReferrals,
            'totalEarnings' => $totalEarnings,
            'pendingEarnings' => $pendingEarnings,
            'conversionRate' => $totalReferrals > 0 ? round(($convertedReferrals / $totalReferrals) * 100, 1) : 0
        ];
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
}

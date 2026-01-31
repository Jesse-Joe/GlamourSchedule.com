<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;
use GlamourSchedule\Core\GeoIP;

class AuthController extends Controller
{
    private const TERMS_VERSION = '1.0';
    private const CODE_EXPIRY_MINUTES = 10;
    private const MAX_CODE_ATTEMPTS = 3;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function showLogin(): string
    {
        // Redirect to appropriate dashboard if already logged in
        if (isset($_SESSION['business_id'])) {
            return $this->redirect('/business/dashboard');
        }
        if (isset($_SESSION['user_id'])) {
            return $this->redirect('/dashboard');
        }
        return $this->view('pages/auth/login', ['pageTitle' => $this->t('page_login')]);
    }

    public function login(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->view('pages/auth/login', [
                'pageTitle' => $this->t('page_login'),
                'error' => $this->t('validation_invalid_request')
            ]);
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $accountType = $_POST['account_type'] ?? 'personal';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Check rate limiting
        $rateLimitCheck = $this->checkRateLimit($ipAddress, $email);
        if ($rateLimitCheck['blocked']) {
            return $this->view('pages/auth/login', [
                'pageTitle' => $this->t('page_login'),
                'error' => $this->t('auth_too_many_attempts', ['minutes' => $rateLimitCheck['minutes_remaining']]),
                'email' => $email
            ]);
        }

        $errors = $this->validate(['email' => $email, 'password' => $password], [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            return $this->view('pages/auth/login', [
                'pageTitle' => $this->t('page_login'),
                'errors' => $errors,
                'email' => $email
            ]);
        }

        // Check based on selected account type
        if ($accountType === 'business') {
            // Check businesses table with user password from users table
            $stmt = $this->db->query(
                "SELECT b.*, u.password_hash FROM businesses b
                 JOIN users u ON b.user_id = u.id
                 WHERE b.email = ? AND b.status = 'active'",
                [$email]
            );
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($business && password_verify($password, $business['password_hash'])) {
                // Record successful attempt
                $this->recordLoginAttempt($ipAddress, $email, true);

                // Store pending login and send 2FA code
                $_SESSION['pending_login'] = [
                    'business_id' => $business['id'],
                    'user_type' => 'business',
                    'email' => $email
                ];

                $this->sendVerificationCode($email, null, 'login');
                return $this->redirect('/verify-login');
            }

            // Record failed attempt
            $this->recordLoginAttempt($ipAddress, $email, false);

            return $this->view('pages/auth/login', [
                'pageTitle' => $this->t('page_login'),
                'error' => $this->t('auth_invalid_business_credentials'),
                'email' => $email
            ]);
        }

        // Personal account - only check users table
        $stmt = $this->db->query("SELECT * FROM users WHERE email = ? AND status = 'active'", [$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Record successful attempt
            $this->recordLoginAttempt($ipAddress, $email, true);

            // Always require 2FA for all logins
            $_SESSION['pending_login'] = [
                'user_id' => $user['id'],
                'user_type' => 'customer',
                'email' => $email
            ];

            // Generate and send 2FA code
            $this->sendVerificationCode($email, $user['id'], 'login');

            return $this->redirect('/verify-login');
        }

        // Record failed attempt
        $this->recordLoginAttempt($ipAddress, $email, false);

        return $this->view('pages/auth/login', [
            'pageTitle' => $this->t('page_login'),
            'error' => $this->t('auth_invalid_credentials'),
            'email' => $email
        ]);
    }

    public function showVerifyLogin(): string
    {
        if (!isset($_SESSION['pending_login'])) {
            return $this->redirect('/login');
        }

        return $this->view('pages/auth/verify-code', [
            'pageTitle' => $this->t('page_verification'),
            'email' => $_SESSION['pending_login']['email'],
            'type' => 'login'
        ]);
    }

    public function verifyLogin(): string
    {
        if (!isset($_SESSION['pending_login'])) {
            return $this->redirect('/login');
        }

        if (!$this->verifyCsrf()) {
            return $this->view('pages/auth/verify-code', [
                'pageTitle' => $this->t('page_verification'),
                'email' => $_SESSION['pending_login']['email'],
                'type' => 'login',
                'error' => $this->t('validation_invalid_request')
            ]);
        }

        $code = trim($_POST['code'] ?? '');
        $email = $_SESSION['pending_login']['email'];

        if (!$this->verifyCode($email, $code, 'login')) {
            return $this->view('pages/auth/verify-code', [
                'pageTitle' => $this->t('page_verification'),
                'email' => $email,
                'type' => 'login',
                'error' => $this->t('auth_invalid_or_expired_code')
            ]);
        }

        // Complete login
        if (isset($_SESSION['pending_login']['user_id'])) {
            $_SESSION['user_id'] = $_SESSION['pending_login']['user_id'];
            $_SESSION['user_type'] = 'customer';
            // Load theme preference
            $themeStmt = $this->db->query("SELECT theme_preference FROM users WHERE id = ?", [$_SESSION['user_id']]);
            $themeResult = $themeStmt->fetch(\PDO::FETCH_ASSOC);
            $_SESSION['theme_preference'] = $themeResult['theme_preference'] ?? 'light';
            $this->db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$_SESSION['user_id']]);
            unset($_SESSION['pending_login']);
            return $this->redirect('/dashboard');
        }

        if (isset($_SESSION['pending_login']['business_id'])) {
            $_SESSION['business_id'] = $_SESSION['pending_login']['business_id'];
            $_SESSION['user_type'] = 'business';
            unset($_SESSION['pending_login']);
            return $this->redirect('/business/dashboard');
        }

        return $this->redirect('/login');
    }

    public function resendLoginCode(): string
    {
        if (!isset($_SESSION['pending_login'])) {
            return $this->json(['success' => false, 'message' => $this->t('auth_no_active_session')]);
        }

        $email = $_SESSION['pending_login']['email'];
        $userId = $_SESSION['pending_login']['user_id'] ?? null;

        $this->sendVerificationCode($email, $userId, 'login');

        return $this->json(['success' => true, 'message' => $this->t('auth_new_code_sent')]);
    }

    public function showRegister(): string
    {
        // Redirect to appropriate dashboard if already logged in
        if (isset($_SESSION['business_id'])) {
            return $this->redirect('/business/dashboard');
        }
        if (isset($_SESSION['user_id'])) {
            return $this->redirect('/dashboard');
        }

        // Get categories for business registration tab
        $categories = $this->getCategories();

        return $this->view('pages/auth/register', [
            'pageTitle' => $this->t('page_register'),
            'categories' => $categories
        ]);
    }

    private function getCategories(): array
    {
        $stmt = $this->db->query(
            "SELECT c.*, ct.name as translated_name
             FROM categories c
             LEFT JOIN category_translations ct ON c.id = ct.category_id AND ct.language = ?
             WHERE c.is_active = 1
             ORDER BY c.sort_order",
            [$this->lang]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function register(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->view('pages/auth/register', [
                'pageTitle' => $this->t('page_register'),
                'error' => $this->t('validation_invalid_request')
            ]);
        }

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'accept_terms' => isset($_POST['accept_terms'])
        ];

        $errors = $this->validate($data, [
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = $this->t('error_password_match');
        }

        if (!$data['accept_terms']) {
            $errors['accept_terms'] = $this->t('auth_accept_terms_required');
        }

        // Check if email exists in users table
        $stmt = $this->db->query("SELECT id, status FROM users WHERE email = ?", [$data['email']]);
        $existingUser = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($existingUser) {
            if ($existingUser['status'] === 'active') {
                $errors['email'] = $this->t('auth_email_in_use');
            } else {
                $errors['email'] = $this->t('auth_account_deactivated');
            }
        }

        // Also check businesses table
        $stmt = $this->db->query("SELECT id FROM businesses WHERE email = ?", [$data['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = $this->t('auth_email_registered_business');
        }

        if (!empty($errors)) {
            return $this->view('pages/auth/register', [
                'pageTitle' => $this->t('page_register'),
                'errors' => $errors,
                'data' => $data
            ]);
        }

        // Store registration data in session and send verification code
        $_SESSION['pending_registration'] = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'terms_version' => self::TERMS_VERSION
        ];

        $this->sendVerificationCode($data['email'], null, 'registration');

        return $this->redirect('/verify-registration');
    }

    public function showVerifyRegistration(): string
    {
        if (!isset($_SESSION['pending_registration'])) {
            return $this->redirect('/register');
        }

        return $this->view('pages/auth/verify-code', [
            'pageTitle' => $this->t('page_email_verification'),
            'email' => $_SESSION['pending_registration']['email'],
            'type' => 'registration'
        ]);
    }

    public function verifyRegistration(): string
    {
        if (!isset($_SESSION['pending_registration'])) {
            return $this->redirect('/register');
        }

        if (!$this->verifyCsrf()) {
            return $this->view('pages/auth/verify-code', [
                'pageTitle' => $this->t('page_email_verification'),
                'email' => $_SESSION['pending_registration']['email'],
                'type' => 'registration',
                'error' => $this->t('validation_invalid_request')
            ]);
        }

        $code = trim($_POST['code'] ?? '');
        $email = $_SESSION['pending_registration']['email'];

        if (!$this->verifyCode($email, $code, 'registration')) {
            return $this->view('pages/auth/verify-code', [
                'pageTitle' => $this->t('page_email_verification'),
                'email' => $email,
                'type' => 'registration',
                'error' => $this->t('auth_invalid_or_expired_code')
            ]);
        }

        // Complete registration
        $data = $_SESSION['pending_registration'];
        $uuid = $this->generateUuid();

        // Detect user's language from IP location
        $geoIP = new GeoIP($this->db);
        $location = $geoIP->lookup();
        $userLanguage = $location['language'] ?? 'nl';

        // Validate language
        $validLangs = ['nl', 'en', 'de', 'fr', 'es', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar', 'tr', 'pl', 'sv', 'no', 'da', 'fi', 'el', 'cs', 'hu', 'ro', 'bg', 'hr', 'sk', 'sl', 'et', 'lv', 'lt', 'uk', 'hi', 'th', 'vi', 'id', 'ms', 'tl', 'he', 'fa', 'sw', 'af'];
        if (!in_array($userLanguage, $validLangs)) {
            $userLanguage = 'nl';
        }

        $this->db->query(
            "INSERT INTO users (uuid, email, password_hash, first_name, last_name, phone,
                               email_verified, email_verified_at, terms_accepted_at, terms_version, status, language)
             VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW(), ?, 'active', ?)",
            [$uuid, $data['email'], $data['password_hash'], $data['first_name'],
             $data['last_name'], $data['phone'], $data['terms_version'], $userLanguage]
        );

        $userId = $this->db->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_type'] = 'customer';
        $_SESSION['lang'] = $userLanguage;
        unset($_SESSION['pending_registration']);

        // Set language cookie
        setcookie('lang', $userLanguage, [
            'expires' => time() + (365 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Lax'
        ]);

        return $this->redirect('/dashboard');
    }

    public function resendRegistrationCode(): string
    {
        if (!isset($_SESSION['pending_registration'])) {
            return $this->json(['success' => false, 'message' => $this->t('auth_no_active_registration')]);
        }

        $email = $_SESSION['pending_registration']['email'];
        $this->sendVerificationCode($email, null, 'registration');

        return $this->json(['success' => true, 'message' => $this->t('auth_new_code_sent')]);
    }

    public function logout(): string
    {
        session_destroy();
        return $this->redirect('/');
    }

    private function sendVerificationCode(string $email, ?int $userId, string $type): void
    {
        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . self::CODE_EXPIRY_MINUTES . ' minutes'));

        // Delete any existing codes for this email and type
        $this->db->query(
            "DELETE FROM email_verification_codes WHERE email = ? AND type = ?",
            [$email, $type]
        );

        // Insert new code
        $this->db->query(
            "INSERT INTO email_verification_codes (user_id, email, code, type, expires_at)
             VALUES (?, ?, ?, ?, ?)",
            [$userId, $email, $code, $type, $expiresAt]
        );

        // Send email
        $this->sendCodeEmail($email, $code, $type);
    }

    private function sendCodeEmail(string $email, string $code, string $type): void
    {
        switch ($type) {
            case 'registration':
                $subject = $this->t('email_subject_verify_email');
                $typeText = $this->t('email_purpose_registration');
                break;
            case 'login':
                $subject = $this->t('email_subject_login_code');
                $typeText = $this->t('email_purpose_login');
                break;
            case 'password_reset':
                $subject = $this->t('email_subject_password_reset');
                $typeText = $this->t('email_purpose_password_reset');
                break;
            default:
                $subject = $this->t('email_subject_verification_code');
                $typeText = $this->t('email_purpose_continue');
        }

        $codeExpiry = $this->t('email_code_validity', ['minutes' => self::CODE_EXPIRY_MINUTES]);
        $ignoreText = $this->t('email_ignore_if_not_requested');
        $yourCode = $this->t('email_your_verification_code');
        $useCode = $this->t('email_use_code_to', ['purpose' => $typeText]);
        $allRights = $this->t('email_all_rights_reserved');

        $year = date('Y');
        $body = <<<HTML
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <style>
        :root { color-scheme: light dark; }
        @media (prefers-color-scheme: dark) {
            .email-body { background-color: #1f1f1f !important; }
            .email-title { color: #ffffff !important; }
            .email-text { color: #e0e0e0 !important; }
            .email-subtext { color: #b0b0b0 !important; }
            .code-box { background: #2d2d2d !important; border: 2px solid #4a4a4a !important; }
            .code-text { color: #ffffff !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 20px; background-color: #f5f5f5;">
    <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 600px; margin: 0 auto;">
        <div style="background: #000000; padding: 30px; text-align: center; border-radius: 20px 20px 0 0;">
            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">GlamourSchedule</h1>
        </div>
        <div class="email-body" style="background-color: #ffffff; padding: 40px; border-radius: 0 0 20px 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <h2 class="email-title" style="color: #1f2937; margin-top: 0;">{$yourCode}</h2>
            <p class="email-text" style="color: #4b5563; font-size: 16px;">
                {$useCode}
            </p>
            <div class="code-box" style="background: linear-gradient(135deg, #fffbeb, #f5f3ff); border: 2px solid #e5e7eb; border-radius: 12px; padding: 25px; text-align: center; margin: 25px 0;">
                <span class="code-text" style="font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #000000;">{$code}</span>
            </div>
            <p class="email-subtext" style="color: #6b7280; font-size: 14px;">
                {$codeExpiry}<br>
                {$ignoreText}
            </p>
        </div>
        <p style="text-align: center; color: #9ca3af; font-size: 12px; margin-top: 20px;">
            &copy; {$year} GlamourSchedule. {$allRights}
        </p>
    </div>
</body>
</html>
HTML;

        try {
            $mailer = new Mailer($this->lang);
            $mailer->send($email, $subject, $body);
        } catch (\Exception $e) {
            error_log("Failed to send verification email: " . $e->getMessage());
        }
    }

    private function verifyCode(string $email, string $code, string $type): bool
    {
        // Get the verification code
        $stmt = $this->db->query(
            "SELECT * FROM email_verification_codes
             WHERE email = ? AND code = ? AND type = ? AND used_at IS NULL
             ORDER BY created_at DESC LIMIT 1",
            [$email, $code, $type]
        );

        $verification = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$verification) {
            return false;
        }

        // Check if expired
        if (strtotime($verification['expires_at']) < time()) {
            return false;
        }

        // Check attempts
        if ($verification['attempts'] >= self::MAX_CODE_ATTEMPTS) {
            return false;
        }

        // Increment attempts
        $this->db->query(
            "UPDATE email_verification_codes SET attempts = attempts + 1 WHERE id = ?",
            [$verification['id']]
        );

        // Mark as used
        $this->db->query(
            "UPDATE email_verification_codes SET used_at = NOW() WHERE id = ?",
            [$verification['id']]
        );

        return true;
    }

    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Check if IP/email is rate limited
     */
    private function checkRateLimit(string $ipAddress, string $email): array
    {
        $lockoutMinutes = self::LOCKOUT_MINUTES;
        $maxAttempts = self::MAX_LOGIN_ATTEMPTS;

        // Count failed attempts in the lockout window
        $stmt = $this->db->query(
            "SELECT COUNT(*) as cnt, MIN(attempted_at) as first_attempt
             FROM login_attempts
             WHERE (ip_address = ? OR email = ?)
               AND successful = 0
               AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$ipAddress, $email, $lockoutMinutes]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result['cnt'] >= $maxAttempts) {
            // Calculate remaining lockout time
            $firstAttempt = strtotime($result['first_attempt']);
            $lockoutEnd = $firstAttempt + ($lockoutMinutes * 60);
            $remaining = ceil(($lockoutEnd - time()) / 60);

            return [
                'blocked' => true,
                'minutes_remaining' => max(1, $remaining)
            ];
        }

        return ['blocked' => false];
    }

    /**
     * Record a login attempt
     */
    private function recordLoginAttempt(string $ipAddress, string $email, bool $successful): void
    {
        $this->db->query(
            "INSERT INTO login_attempts (ip_address, email, successful) VALUES (?, ?, ?)",
            [$ipAddress, $email, $successful ? 1 : 0]
        );

        // Clean up old attempts (older than 24 hours)
        $this->db->query(
            "DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );
    }
}

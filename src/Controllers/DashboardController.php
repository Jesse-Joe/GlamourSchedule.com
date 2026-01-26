<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Services\LoyaltyService;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
    }

    public function index(): string
    {
        $user = $this->getCurrentUser();
        $upcomingBookings = $this->getUpcomingBookings($user['id']);
        $pastBookings = $this->getPastBookings($user['id']);

        return $this->view('pages/dashboard/index', [
            'pageTitle' => 'Dashboard',
            'user' => $user,
            'upcomingBookings' => $upcomingBookings,
            'pastBookings' => $pastBookings
        ]);
    }

    public function bookings(): string
    {
        $user = $this->getCurrentUser();
        $bookings = $this->getAllBookings($user['id']);

        return $this->view('pages/dashboard/bookings', [
            'pageTitle' => 'Mijn Boekingen',
            'bookings' => $bookings
        ]);
    }

    public function settings(): string
    {
        $user = $this->getCurrentUser();
        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->verifyCsrf()) {
                $message = 'Ongeldige sessie. Probeer opnieuw.';
                $messageType = 'danger';
            } else {
                $action = $_POST['action'] ?? 'update_profile';

                if ($action === 'update_profile') {
                    $result = $this->handleUpdateProfile($user['id']);
                    $message = $result['message'];
                    $messageType = $result['type'];
                } elseif ($action === 'change_password') {
                    $result = $this->handleChangePassword($user['id']);
                    $message = $result['message'];
                    $messageType = $result['type'];
                } elseif ($action === 'delete_account') {
                    $result = $this->handleDeleteAccount($user['id']);
                    if ($result['redirect']) {
                        return $this->redirect($result['redirect']);
                    }
                    $message = $result['message'];
                    $messageType = $result['type'];
                }

                // Refresh user data
                $user = $this->getCurrentUser();
            }
        }

        return $this->view('pages/dashboard/settings', [
            'pageTitle' => 'Instellingen',
            'user' => $user,
            'message' => $message,
            'messageType' => $messageType,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function profile(): string
    {
        // Redirect to settings page
        return $this->redirect('/dashboard/settings');
    }

    /**
     * Loyalty points overview page
     */
    public function loyalty(): string
    {
        $user = $this->getCurrentUser();

        $loyaltyService = new LoyaltyService();
        $totalPoints = $loyaltyService->getTotalPoints($user['id']);
        $balances = $loyaltyService->getAllBalances($user['id']);
        $transactions = $loyaltyService->getAllTransactionHistory($user['id'], 20);

        return $this->view('pages/dashboard/loyalty', [
            'pageTitle' => 'Loyaliteitspunten',
            'user' => $user,
            'totalPoints' => $totalPoints,
            'balances' => $balances,
            'transactions' => $transactions,
            'pointsPerBooking' => LoyaltyService::getPointsPerBooking(),
            'pointsPerReview' => LoyaltyService::getPointsPerReview(),
            'pointsPerPercent' => LoyaltyService::getPointsPerPercent()
        ]);
    }

    private function getUpcomingBookings(int $userId): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name, biz.slug as business_slug, s.name as service_name
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             JOIN services s ON b.service_id = s.id
             WHERE b.user_id = ? AND b.appointment_date >= CURDATE() AND b.status != 'cancelled'
             ORDER BY b.appointment_date, b.appointment_time
             LIMIT 5",
            [$userId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getPastBookings(int $userId): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name, s.name as service_name
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             JOIN services s ON b.service_id = s.id
             WHERE b.user_id = ? AND (b.appointment_date < CURDATE() OR b.status = 'completed')
             ORDER BY b.appointment_date DESC
             LIMIT 5",
            [$userId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getAllBookings(int $userId): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name, biz.slug as business_slug, s.name as service_name
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             JOIN services s ON b.service_id = s.id
             WHERE b.user_id = ?
             ORDER BY b.appointment_date DESC, b.appointment_time DESC",
            [$userId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function handleUpdateProfile(int $userId): array
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $language = $_POST['language'] ?? 'nl';

        if (empty($firstName)) {
            return ['message' => 'Voornaam is verplicht.', 'type' => 'danger'];
        }

        // Validate language
        $validLangs = ['nl', 'en', 'de', 'fr', 'es', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar', 'tr', 'pl', 'sv', 'no', 'da', 'fi', 'el', 'cs', 'hu', 'ro', 'bg', 'hr', 'sk', 'sl', 'et', 'lv', 'lt', 'uk', 'hi', 'th', 'vi', 'id', 'ms', 'tl', 'he', 'fa', 'sw', 'af'];
        if (!in_array($language, $validLangs)) {
            $language = 'nl';
        }

        $this->db->query(
            "UPDATE users SET first_name = ?, last_name = ?, phone = ?, language = ? WHERE id = ?",
            [$firstName, $lastName, $phone, $language, $userId]
        );

        // Update session language
        $_SESSION['lang'] = $language;
        setcookie('lang', $language, [
            'expires' => time() + (365 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Lax'
        ]);

        return ['message' => 'Je profiel is bijgewerkt!', 'type' => 'success'];
    }

    private function handleChangePassword(int $userId): array
    {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return ['message' => 'Vul alle wachtwoordvelden in.', 'type' => 'danger'];
        }

        if ($newPassword !== $confirmPassword) {
            return ['message' => 'Nieuwe wachtwoorden komen niet overeen.', 'type' => 'danger'];
        }

        if (strlen($newPassword) < 8) {
            return ['message' => 'Wachtwoord moet minimaal 8 tekens zijn.', 'type' => 'danger'];
        }

        // Verify current password
        $stmt = $this->db->query("SELECT password_hash FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['message' => 'Huidig wachtwoord is onjuist.', 'type' => 'danger'];
        }

        // Update password
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->query(
            "UPDATE users SET password_hash = ? WHERE id = ?",
            [$newHash, $userId]
        );

        return ['message' => 'Je wachtwoord is gewijzigd!', 'type' => 'success'];
    }

    private function handleDeleteAccount(int $userId): array
    {
        $password = $_POST['delete_password'] ?? '';
        $confirmation = $_POST['delete_confirmation'] ?? '';

        if ($confirmation !== 'VERWIJDER') {
            return ['message' => 'Typ "VERWIJDER" om te bevestigen.', 'type' => 'danger', 'redirect' => null];
        }

        // Verify password
        $stmt = $this->db->query("SELECT password_hash, email FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['message' => 'Wachtwoord is onjuist.', 'type' => 'danger', 'redirect' => null];
        }

        // Cancel upcoming bookings
        $this->db->query(
            "UPDATE bookings SET status = 'cancelled', notes = CONCAT(IFNULL(notes, ''), '\nAccount verwijderd door gebruiker.')
             WHERE user_id = ? AND status IN ('pending', 'confirmed') AND appointment_date >= CURDATE()",
            [$userId]
        );

        // Anonymize user data instead of hard delete (for data integrity)
        $anonymizedEmail = 'deleted_' . $userId . '_' . time() . '@deleted.local';
        $this->db->query(
            "UPDATE users SET
                email = ?,
                first_name = 'Verwijderd',
                last_name = 'Account',
                phone = NULL,
                password_hash = NULL,
                pin_hash = NULL,
                pin_enabled = 0,
                status = 'inactive'
             WHERE id = ?",
            [$anonymizedEmail, $userId]
        );

        // Clear session
        session_destroy();

        return ['message' => 'Account verwijderd.', 'type' => 'success', 'redirect' => '/?account_deleted=1'];
    }

    /**
     * Security settings page (PIN code)
     */
    public function security(): string
    {
        $user = $this->getCurrentUser();
        $message = null;
        $messageType = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->verifyCsrf()) {
                $message = 'Ongeldige sessie. Probeer opnieuw.';
                $messageType = 'danger';
            } else {
                $action = $_POST['action'] ?? '';

                if ($action === 'set_pin') {
                    $result = $this->handleSetPin($user['id']);
                    $message = $result['message'];
                    $messageType = $result['type'];
                } elseif ($action === 'remove_pin') {
                    $result = $this->handleRemovePin($user['id']);
                    $message = $result['message'];
                    $messageType = $result['type'];
                }

                // Refresh user data
                $user = $this->getCurrentUser();
            }
        }

        return $this->view('pages/dashboard/security', [
            'pageTitle' => 'Beveiliging',
            'user' => $user,
            'pinEnabled' => (bool)($user['pin_enabled'] ?? false),
            'message' => $message,
            'messageType' => $messageType,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Handle setting a new PIN
     */
    private function handleSetPin(int $userId): array
    {
        $pin = $_POST['pin'] ?? '';
        $pinConfirm = $_POST['pin_confirm'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';

        // Validate PIN format
        if (!preg_match('/^\d{6}$/', $pin)) {
            return ['message' => 'PIN moet exact 6 cijfers zijn.', 'type' => 'danger'];
        }

        if ($pin !== $pinConfirm) {
            return ['message' => 'PIN codes komen niet overeen.', 'type' => 'danger'];
        }

        // Verify current password
        $stmt = $this->db->query("SELECT password_hash FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['message' => 'Huidig wachtwoord is onjuist.', 'type' => 'danger'];
        }

        // Hash and save PIN
        $pinHash = password_hash($pin, PASSWORD_DEFAULT);
        $this->db->query(
            "UPDATE users SET pin_hash = ?, pin_enabled = 1 WHERE id = ?",
            [$pinHash, $userId]
        );

        return ['message' => 'PIN code is ingesteld! Bij het openen van de app wordt nu om je PIN gevraagd.', 'type' => 'success'];
    }

    /**
     * Handle removing PIN
     */
    private function handleRemovePin(int $userId): array
    {
        $currentPassword = $_POST['current_password'] ?? '';

        // Verify current password
        $stmt = $this->db->query("SELECT password_hash FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['message' => 'Wachtwoord is onjuist.', 'type' => 'danger'];
        }

        $this->db->query(
            "UPDATE users SET pin_hash = NULL, pin_enabled = 0 WHERE id = ?",
            [$userId]
        );

        return ['message' => 'PIN code is verwijderd.', 'type' => 'success'];
    }

    /**
     * Verify PIN (AJAX endpoint)
     */
    public function verifyPin(): string
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            return json_encode(['success' => false, 'error' => 'Niet ingelogd']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Check if this is a session check request (app reopened)
        if (!empty($input['check_session'])) {
            // Check if PIN verification has expired (5 minutes)
            $pinTimeout = 5 * 60; // 5 minutes
            if (isset($_SESSION['pin_verified_at']) && (time() - $_SESSION['pin_verified_at']) > $pinTimeout) {
                // PIN verification expired - require re-entry
                unset($_SESSION['pin_verified']);
                unset($_SESSION['pin_verified_at']);
                return json_encode(['success' => false, 'expired' => true]);
            }
            return json_encode(['success' => true]);
        }

        $pin = $input['pin'] ?? '';

        $stmt = $this->db->query(
            "SELECT pin_hash FROM users WHERE id = ? AND pin_enabled = 1",
            [$_SESSION['user_id']]
        );
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !$user['pin_hash']) {
            return json_encode(['success' => true]); // No PIN set
        }

        if (password_verify($pin, $user['pin_hash'])) {
            $_SESSION['pin_verified'] = true;
            $_SESSION['pin_verified_at'] = time();
            return json_encode(['success' => true]);
        }

        return json_encode(['success' => false, 'error' => 'Onjuiste PIN']);
    }
}

<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;
use GlamourSchedule\Core\WebPush;

class VerifyEmailController extends Controller
{
    public function show(): string
    {
        // Check if there's a pending verification
        if (!isset($_SESSION['pending_verification_email'])) {
            return $this->redirect('/business/register');
        }

        $email = $_SESSION['pending_verification_email'];
        $maskedEmail = $this->maskEmail($email);

        return $this->view('pages/auth/verify-email', [
            'pageTitle' => 'Verifieer je e-mail',
            'maskedEmail' => $maskedEmail,
            'error' => $_GET['error'] ?? null,
            'success' => $_GET['success'] ?? null
        ]);
    }

    public function verify(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/verify-email?error=csrf');
        }

        if (!isset($_SESSION['pending_verification_email'])) {
            return $this->redirect('/business/register');
        }

        $email = $_SESSION['pending_verification_email'];
        $code = trim($_POST['code'] ?? '');

        // Remove any spaces from code
        $code = str_replace(' ', '', $code);

        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return $this->redirect('/verify-email?error=invalid_code');
        }

        // Find verification record
        $stmt = $this->db->query(
            "SELECT * FROM email_verifications
             WHERE email = ? AND verification_code = ? AND verified_at IS NULL
             ORDER BY created_at DESC LIMIT 1",
            [$email, $code]
        );
        $verification = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$verification) {
            // Increment attempts
            $this->db->query(
                "UPDATE email_verifications SET attempts = attempts + 1
                 WHERE email = ? AND verified_at IS NULL",
                [$email]
            );
            return $this->redirect('/verify-email?error=wrong_code');
        }

        // Check if expired
        if (strtotime($verification['expires_at']) < time()) {
            return $this->redirect('/verify-email?error=expired');
        }

        // Check max attempts
        if ($verification['attempts'] >= 5) {
            return $this->redirect('/verify-email?error=max_attempts');
        }

        // Mark as verified
        $this->db->query(
            "UPDATE email_verifications SET verified_at = NOW() WHERE id = ?",
            [$verification['id']]
        );

        // Update user email_verified
        $this->db->query(
            "UPDATE users SET email_verified = 1, email_verified_at = NOW() WHERE id = ?",
            [$verification['user_id']]
        );

        // Get business details for verification check
        $stmt = $this->db->query("SELECT kvk_number, name, email FROM businesses WHERE id = ?", [$verification['business_id']]);
        $businessData = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Auto-verify if business has KVK number
        $isAutoVerified = !empty($businessData['kvk_number']);

        // Update business status to active and set verification status
        $this->db->query(
            "UPDATE businesses SET status = 'active', is_verified = ?, admin_verified_at = ? WHERE id = ?",
            [
                $isAutoVerified ? 1 : 0,
                $isAutoVerified ? date('Y-m-d H:i:s') : null,
                $verification['business_id']
            ]
        );

        // Notify admins of new business registration
        $this->notifyAdminsOfNewBusiness($verification['business_id'], $businessData, $isAutoVerified);

        // Log the user in
        $_SESSION['user_id'] = $verification['user_id'];
        $_SESSION['business_id'] = $verification['business_id'];
        $_SESSION['user_type'] = 'business';
        $_SESSION['new_business_registration'] = true;

        // Get business slug
        $stmt = $this->db->query("SELECT slug FROM businesses WHERE id = ?", [$verification['business_id']]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);
        $_SESSION['business_slug'] = $business['slug'] ?? '';

        // Clear pending verification
        unset($_SESSION['pending_verification_email']);
        unset($_SESSION['pending_business_id']);

        // Send welcome email
        $this->sendWelcomeEmail($verification['user_id'], $verification['business_id']);

        // Send KVK warning email if no KVK number
        if (!$isAutoVerified) {
            $this->sendKvkWarningEmail($verification['business_id'], $businessData);
        }

        // Notify all personal accounts about new business
        $this->notifyUsersOfNewBusiness($verification['business_id']);

        // Redirect to dashboard with welcome message
        return $this->redirect('/business/dashboard?welcome=1&verified=1');
    }

    public function resend(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/verify-email?error=csrf');
        }

        if (!isset($_SESSION['pending_verification_email'])) {
            return $this->redirect('/business/register');
        }

        $email = $_SESSION['pending_verification_email'];

        // Check rate limit (max 1 per minute)
        $stmt = $this->db->query(
            "SELECT created_at FROM email_verifications
             WHERE email = ? AND verified_at IS NULL
             ORDER BY created_at DESC LIMIT 1",
            [$email]
        );
        $lastVerification = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($lastVerification && strtotime($lastVerification['created_at']) > strtotime('-1 minute')) {
            return $this->redirect('/verify-email?error=rate_limit');
        }

        // Get business info
        $stmt = $this->db->query(
            "SELECT b.id, b.company_name, u.id as user_id
             FROM businesses b
             JOIN users u ON b.user_id = u.id
             WHERE b.email = ?",
            [$email]
        );
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            return $this->redirect('/business/register');
        }

        // Generate new code
        $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        // Invalidate old codes
        $this->db->query(
            "UPDATE email_verifications SET verified_at = NOW() WHERE email = ? AND verified_at IS NULL",
            [$email]
        );

        // Insert new code
        $this->db->query(
            "INSERT INTO email_verifications (user_id, business_id, email, verification_code, expires_at)
             VALUES (?, ?, ?, ?, ?)",
            [$business['user_id'], $business['id'], $email, $newCode, $expiresAt]
        );

        // Send email
        $this->sendVerificationEmail($email, $business['company_name'], $newCode);

        return $this->redirect('/verify-email?success=resent');
    }

    private function sendVerificationEmail(string $email, string $companyName, string $code): void
    {
        $subject = "Bevestig je GlamourSchedule account - Code: {$code}";

        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:#f5f5f5;padding:20px;'>
                <tr>
                    <td align='center'>
                        <table width='500' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:10px;overflow:hidden;'>
                            <tr>
                                <td style='background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:30px;text-align:center;'>
                                    <h1 style='margin:0;font-size:24px;'>Bevestig je account</h1>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:40px;text-align:center;'>
                                    <p style='font-size:16px;color:#333;'>Beste <strong>{$companyName}</strong>,</p>
                                    <p style='font-size:16px;color:#333;'>Je verificatiecode is:</p>
                                    <div style='background:#f8f9fa;border:2px dashed #000000;border-radius:10px;padding:30px;margin:20px 0;'>
                                        <span style='font-size:42px;font-weight:bold;letter-spacing:8px;color:#000000;font-family:monospace;'>{$code}</span>
                                    </div>
                                    <p style='font-size:14px;color:#666;'>Deze code is 30 minuten geldig.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";

        try {
            $mailer = new Mailer();
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send verification email: " . $e->getMessage());
        }
    }

    private function sendWelcomeEmail(int $userId, int $businessId): void
    {
        $stmt = $this->db->query(
            "SELECT b.*, u.email FROM businesses b JOIN users u ON b.user_id = u.id WHERE b.id = ?",
            [$businessId]
        );
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) return;

        $dashboardUrl = "https://glamourschedule.nl/business/dashboard";
        $businessUrl = "https://glamourschedule.nl/business/{$business['slug']}";

        $subject = "Welkom bij GlamourSchedule - Je account is geactiveerd!";

        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:#f5f5f5;padding:20px;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:10px;overflow:hidden;'>
                            <tr>
                                <td style='background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:40px;text-align:center;'>
                                    <h1 style='margin:0;font-size:28px;'>Welkom bij GlamourSchedule!</h1>
                                    <p style='margin:10px 0 0;opacity:0.9;'>Je account is succesvol geactiveerd</p>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:40px;'>
                                    <p style='font-size:16px;color:#333;'>Beste <strong>{$business['company_name']}</strong>,</p>
                                    <p style='font-size:16px;color:#333;line-height:1.6;'>
                                        Gefeliciteerd! Je account is geverifieerd en je kunt nu beginnen met het opzetten van je bedrijfspagina.
                                    </p>

                                    <div style='background:#fffbeb;border-left:4px solid #000000;padding:20px;margin:20px 0;border-radius:0 8px 8px 0;'>
                                        <h3 style='margin:0 0 15px;color:#000000;'>Volgende stappen:</h3>
                                        <ol style='margin:0;padding-left:20px;color:#333;'>
                                            <li style='margin:10px 0;'>Upload je logo en cover foto</li>
                                            <li style='margin:10px 0;'>Voeg je diensten toe met prijzen en tijdsduur</li>
                                            <li style='margin:10px 0;'>Stel je openingstijden in</li>
                                            <li style='margin:10px 0;'>Betaal de registratievergoeding</li>
                                        </ol>
                                    </div>

                                    <p style='text-align:center;margin:30px 0;'>
                                        <a href='{$dashboardUrl}' style='display:inline-block;background:#000000;color:#ffffff;padding:15px 40px;text-decoration:none;border-radius:8px;font-weight:bold;'>Ga naar je Dashboard</a>
                                    </p>

                                    <p style='font-size:14px;color:#666;'>
                                        Je bedrijfspagina: <a href='{$businessUrl}' style='color:#000000;'>{$businessUrl}</a>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style='background:#fafafa;padding:20px;text-align:center;color:#666;font-size:12px;'>
                                    <p style='margin:0;'>&copy; " . date('Y') . " GlamourSchedule</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";

        try {
            $mailer = new Mailer();
            $mailer->send($business['email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send welcome email: " . $e->getMessage());
        }
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 4, 2)) . substr($name, -2);

        return $maskedName . '@' . $domain;
    }

    /**
     * Notify all personal accounts about a new business registration
     */
    private function notifyUsersOfNewBusiness(int $businessId): void
    {
        try {
            // Get business info
            $stmt = $this->db->query(
                "SELECT b.*, c.slug as category_slug,
                        (SELECT ct.name FROM category_translations ct
                         WHERE ct.category_id = bc.category_id AND ct.language = 'nl' LIMIT 1) as category_name
                 FROM businesses b
                 LEFT JOIN business_categories bc ON b.id = bc.business_id
                 LEFT JOIN categories c ON bc.category_id = c.id
                 WHERE b.id = ?",
                [$businessId]
            );
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$business) {
                return;
            }

            $businessUrl = "https://glamourschedule.nl/business/{$business['slug']}";
            $categoryName = $business['category_name'] ?? 'Beauty';

            // Get all personal accounts (users without a business)
            $usersStmt = $this->db->query(
                "SELECT u.id, u.email, u.first_name
                 FROM users u
                 WHERE u.id NOT IN (SELECT user_id FROM businesses WHERE user_id IS NOT NULL)
                   AND u.email_verified = 1
                   AND u.status = 'active'"
            );
            $users = $usersStmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($users)) {
                return;
            }

            // Send push notifications
            $this->sendNewBusinessPushNotifications($users, $business, $businessUrl, $categoryName);

            // Send emails (in batches to avoid overwhelming mail server)
            $this->sendNewBusinessEmails($users, $business, $businessUrl, $categoryName);

            error_log("Notified " . count($users) . " users about new business: {$business['company_name']}");

        } catch (\Exception $e) {
            error_log("Error notifying users of new business: " . $e->getMessage());
        }
    }

    /**
     * Send push notifications to users with subscriptions
     */
    private function sendNewBusinessPushNotifications(array $users, array $business, string $businessUrl, string $categoryName): void
    {
        $userIds = array_column($users, 'id');
        if (empty($userIds)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($userIds), '?'));

        // Get all push subscriptions for these users
        $stmt = $this->db->query(
            "SELECT * FROM push_subscriptions WHERE user_id IN ($placeholders)",
            $userIds
        );
        $subscriptions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($subscriptions)) {
            return;
        }

        $webPush = new WebPush();

        $payload = [
            'title' => 'Nieuw bij GlamourSchedule!',
            'body' => "{$business['company_name']} in {$business['city']} is nu beschikbaar voor boekingen!",
            'icon' => '/images/icon-192.png',
            'badge' => '/images/badge-72.png',
            'tag' => 'new-business-' . $business['id'],
            'data' => [
                'url' => $businessUrl,
                'businessId' => $business['id'],
                'type' => 'new_business'
            ],
            'actions' => [
                ['action' => 'view', 'title' => 'Bekijk']
            ]
        ];

        $result = $webPush->sendToMultiple($subscriptions, $payload);
        error_log("Push notifications for new business: {$result['success']} success, {$result['failed']} failed");
    }

    /**
     * Send email notifications to all personal accounts
     */
    private function sendNewBusinessEmails(array $users, array $business, string $businessUrl, string $categoryName): void
    {
        $mailer = new Mailer();

        $subject = "Nieuw op GlamourSchedule: {$business['company_name']} in {$business['city']}!";

        foreach ($users as $user) {
            $firstName = $user['first_name'] ?: 'daar';

            $htmlBody = $this->getNewBusinessEmailTemplate($firstName, $business, $businessUrl, $categoryName);

            try {
                $mailer->send($user['email'], $subject, $htmlBody);
                usleep(50000); // 50ms delay between emails
            } catch (\Exception $e) {
                error_log("Failed to send new business email to {$user['email']}: " . $e->getMessage());
            }
        }
    }

    /**
     * Get email template for new business notification
     */
    private function getNewBusinessEmailTemplate(string $firstName, array $business, string $businessUrl, string $categoryName): string
    {
        $location = trim("{$business['city']}");
        $description = !empty($business['description'])
            ? substr(strip_tags($business['description']), 0, 150) . '...'
            : "Ontdek de diensten van {$business['company_name']}";

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
</head>
<body style='margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background:#f5f5f5;padding:20px;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);'>
                    <!-- Header -->
                    <tr>
                        <td style='background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:30px;text-align:center;'>
                            <div style='font-size:2.5rem;margin-bottom:10px;'>✨</div>
                            <h1 style='margin:0;font-size:24px;font-weight:700;'>Nieuw bij GlamourSchedule!</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style='padding:40px 30px;'>
                            <p style='font-size:16px;color:#333;margin:0 0 20px;'>
                                Hoi <strong>{$firstName}</strong>,
                            </p>

                            <p style='font-size:16px;color:#333;margin:0 0 25px;line-height:1.6;'>
                                Er is een nieuwe salon toegevoegd die perfect bij jou zou kunnen passen!
                            </p>

                            <!-- Business Card -->
                            <div style='background:linear-gradient(135deg,#fffbeb,#f5f3ff);border-radius:12px;padding:25px;margin:25px 0;border:2px solid #f5f5f5;'>
                                <h2 style='margin:0 0 10px;color:#333;font-size:20px;'>{$business['company_name']}</h2>

                                <p style='margin:0 0 8px;color:#6b7280;font-size:14px;'>
                                    <span style='color:#000000;'>📍</span> {$location}
                                </p>

                                <p style='margin:0 0 8px;color:#6b7280;font-size:14px;'>
                                    <span style='color:#000000;'>💅</span> {$categoryName}
                                </p>

                                <p style='margin:15px 0 0;color:#4b5563;font-size:14px;line-height:1.5;'>
                                    {$description}
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <p style='text-align:center;margin:30px 0;'>
                                <a href='{$businessUrl}' style='display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;box-shadow:0 4px 15px rgba(0,0,0,0.3);'>
                                    Bekijk & Boek Nu
                                </a>
                            </p>

                            <p style='font-size:14px;color:#9ca3af;text-align:center;margin:20px 0 0;'>
                                Wees er snel bij en boek jouw afspraak!
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style='background:#fafafa;padding:25px;text-align:center;border-top:1px solid #e5e7eb;'>
                            <p style='margin:0 0 10px;color:#6b7280;font-size:12px;'>
                                Je ontvangt deze email omdat je een account hebt bij GlamourSchedule.
                            </p>
                            <p style='margin:0;color:#9ca3af;font-size:12px;'>
                                &copy; 2026 GlamourSchedule - <a href='https://glamourschedule.nl' style='color:#000000;text-decoration:none;'>glamourschedule.nl</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function sendKvkWarningEmail(int $businessId, array $businessData): void
    {
        $profileUrl = "https://glamourschedule.nl/business/profile";

        $subject = "Belangrijk: Voeg je KVK-nummer toe om boekingen te ontvangen";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#ffffff;padding:30px;text-align:center;">
                    <div style="font-size:50px;margin-bottom:15px;">&#9888;</div>
                    <h1 style="margin:0;font-size:24px;">Actie Vereist</h1>
                    <p style="margin:10px 0 0;opacity:0.95;">Je account is nog niet volledig actief</p>
                </td></tr>
                <tr><td style="padding:35px;">
                    <p style="font-size:16px;color:#333;margin:0 0 20px;">Beste {$businessData['name']},</p>

                    <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:12px;padding:20px;margin-bottom:25px;">
                        <h3 style="margin:0 0 10px;color:#92400e;display:flex;align-items:center;gap:10px;">
                            <span style="font-size:24px;">&#128274;</span> Je account wacht op verificatie
                        </h3>
                        <p style="margin:0;color:#92400e;line-height:1.6;">
                            Zonder KVK-nummer wordt je account handmatig geverifieerd door ons team.
                            Dit kan tot <strong>24 uur</strong> duren. Tot die tijd kun je <strong>geen boekingen ontvangen</strong>.
                        </p>
                    </div>

                    <h3 style="color:#333;margin:0 0 15px;">Waarom is een KVK-nummer belangrijk?</h3>
                    <ul style="color:#555;line-height:1.8;padding-left:20px;margin:0 0 25px;">
                        <li><strong>Directe activatie</strong> - Met KVK wordt je account direct actief</li>
                        <li><strong>Vertrouwen</strong> - Klanten zien dat je een geregistreerd bedrijf bent</li>
                        <li><strong>Uitbetalingen</strong> - Nodig voor correcte facturatie en uitbetalingen</li>
                        <li><strong>Geen wachttijd</strong> - Begin direct met het ontvangen van boekingen</li>
                    </ul>

                    <div style="background:#f0fdf4;border:2px solid #22c55e;border-radius:12px;padding:20px;margin-bottom:25px;">
                        <h3 style="margin:0 0 10px;color:#166534;">
                            <span style="font-size:20px;">&#10003;</span> Oplossing
                        </h3>
                        <p style="margin:0;color:#166534;line-height:1.6;">
                            Voeg je KVK-nummer toe in je profiel en je account wordt <strong>direct geverifieerd</strong>.
                        </p>
                    </div>

                    <div style="text-align:center;margin:30px 0;">
                        <a href="{$profileUrl}" style="display:inline-block;background:linear-gradient(135deg,#000,#333);color:#fff;padding:16px 40px;border-radius:10px;text-decoration:none;font-weight:600;font-size:16px;">
                            KVK-nummer Toevoegen
                        </a>
                    </div>

                    <p style="font-size:14px;color:#666;text-align:center;margin:0;">
                        Geen KVK-nummer? Geen zorgen - ons team verifieert je account binnen 24 uur.
                    </p>
                </td></tr>
                <tr><td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                    <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule - Beauty & Wellness Bookings</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send($businessData['email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send KVK warning email: " . $e->getMessage());
        }
    }

    private function notifyAdminsOfNewBusiness(int $businessId, array $businessData, bool $isAutoVerified): void
    {
        // Get admin email(s)
        $adminEmails = ['admin@glamourschedule.nl', 'chenayra6@gmail.com'];

        // For businesses without KVK, generate verification token
        $verificationToken = '';
        if (!$isAutoVerified) {
            $verificationToken = bin2hex(random_bytes(32));
            $this->db->query(
                "UPDATE businesses SET admin_verification_token = ? WHERE id = ?",
                [$verificationToken, $businessId]
            );
        }

        $verificationStatus = $isAutoVerified ? 'Automatisch geverifieerd (KVK aanwezig)' : 'Wacht op handmatige verificatie';
        $statusColor = $isAutoVerified ? '#22c55e' : '#f59e0b';

        $subject = $isAutoVerified
            ? "Nieuw bedrijf geregistreerd: {$businessData['name']}"
            : "ACTIE VEREIST: Nieuw bedrijf wacht op verificatie - {$businessData['name']}";

        $kvkDisplay = !empty($businessData['kvk_number']) ? $businessData['kvk_number'] : '<span style="color:#dc2626;">Niet ingevuld</span>';

        // Build action buttons for non-verified businesses
        $actionButtons = '';
        if (!$isAutoVerified) {
            $verifyUrl = "https://glamourschedule.nl/admin/verify-business/{$verificationToken}";
            $actionButtons = <<<HTML
                    <div style="margin-top:30px;text-align:center;">
                        <p style="margin:0 0 20px;color:#666;font-size:14px;">Klik op een knop om het bedrijf te verifiëren of af te wijzen:</p>
                        <a href="{$verifyUrl}?action=approve" style="display:inline-block;background:#000000;color:#ffffff;padding:16px 40px;border-radius:10px;text-decoration:none;font-weight:600;margin:5px;">
                            ✓ Accepteren
                        </a>
                        <a href="{$verifyUrl}?action=reject" style="display:inline-block;background:#ffffff;color:#000000;padding:16px 40px;border-radius:10px;text-decoration:none;font-weight:600;border:2px solid #000000;margin:5px;">
                            ✗ Weigeren
                        </a>
                    </div>
HTML;
        } else {
            $actionButtons = <<<HTML
                    <div style="margin-top:30px;text-align:center;">
                        <a href="https://glamourschedule.nl/admin/businesses" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 30px;border-radius:8px;text-decoration:none;font-weight:600;">
                            Bekijk in Admin Panel
                        </a>
                    </div>
HTML;
        }

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#000000;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#000000;padding:40px 20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 25px 50px rgba(0,0,0,0.5);">
                <tr><td style="background:#000000;color:#ffffff;padding:40px;text-align:center;">
                    <div style="font-size:40px;margin-bottom:15px;">🏢</div>
                    <h1 style="margin:0;font-size:24px;font-weight:700;">Nieuw Bedrijf Geregistreerd</h1>
                    <p style="margin:10px 0 0;opacity:0.8;font-size:14px;">GlamourSchedule Admin Notificatie</p>
                </td></tr>
                <tr><td style="padding:40px;">
                    <div style="background:{$statusColor};color:#fff;padding:12px 24px;border-radius:50px;margin-bottom:30px;display:inline-block;font-weight:600;">
                        {$verificationStatus}
                    </div>

                    <h2 style="margin:0 0 25px 0;color:#000000;font-size:28px;">{$businessData['name']}</h2>

                    <table style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:12px;overflow:hidden;">
                        <tr>
                            <td style="padding:15px 20px;border-bottom:1px solid #e5e7eb;color:#6b7280;width:140px;font-weight:500;">E-mail</td>
                            <td style="padding:15px 20px;border-bottom:1px solid #e5e7eb;color:#111827;font-weight:600;">{$businessData['email']}</td>
                        </tr>
                        <tr>
                            <td style="padding:15px 20px;border-bottom:1px solid #e5e7eb;color:#6b7280;font-weight:500;">KVK-nummer</td>
                            <td style="padding:15px 20px;border-bottom:1px solid #e5e7eb;color:#111827;font-weight:600;">{$kvkDisplay}</td>
                        </tr>
                        <tr>
                            <td style="padding:15px 20px;color:#6b7280;font-weight:500;">Status</td>
                            <td style="padding:15px 20px;color:#111827;font-weight:600;">{$verificationStatus}</td>
                        </tr>
                    </table>

                    {$actionButtons}
                </td></tr>
                <tr><td style="background:#000000;padding:25px;text-align:center;">
                    <p style="margin:0;color:#ffffff;font-size:12px;opacity:0.7;">&copy; 2025 GlamourSchedule - Admin Portal</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer();
            foreach ($adminEmails as $adminEmail) {
                $mailer->send($adminEmail, $subject, $htmlBody);
            }
        } catch (\Exception $e) {
            error_log("Failed to send admin notification email: " . $e->getMessage());
        }
    }
}

<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotForm(): string
    {
        return $this->view('pages/auth/forgot-password', [
            'pageTitle' => 'Wachtwoord vergeten'
        ]);
    }

    /**
     * Handle forgot password request
     */
    public function sendResetLink(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/forgot-password?error=csrf');
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->redirect('/forgot-password?error=invalid_email');
        }

        // Find user by email (check both users and businesses tables)
        $stmt = $this->db->query("SELECT id, first_name, last_name, 'user' as account_type FROM users WHERE email = ?", [$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // If not found in users, check businesses
        if (!$user) {
            $stmt = $this->db->query("SELECT id, company_name as first_name, '' as last_name, 'business' as account_type FROM businesses WHERE email = ?", [$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        // Always show success message (security: don't reveal if email exists)
        if (!$user) {
            return $this->redirect('/forgot-password?success=1');
        }

        // Rate limit: max 3 requests per hour
        $stmt = $this->db->query(
            "SELECT COUNT(*) as cnt FROM password_resets
             WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [$email]
        );
        $count = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        if ($count >= 3) {
            return $this->redirect('/forgot-password?error=rate_limit');
        }

        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Invalidate old tokens
        $this->db->query(
            "UPDATE password_resets SET used_at = NOW() WHERE email = ? AND used_at IS NULL",
            [$email]
        );

        // Store new token (include account_type for later use)
        $this->db->query(
            "INSERT INTO password_resets (user_id, email, token, expires_at, account_type) VALUES (?, ?, ?, ?, ?)",
            [$user['id'], $email, $token, $expiresAt, $user['account_type']]
        );

        // Send reset email
        $this->sendResetEmail($email, $user['first_name'], $token);

        return $this->redirect('/forgot-password?success=1');
    }

    /**
     * Show reset password form
     */
    public function showResetForm(string $token): string
    {
        // Validate token
        $stmt = $this->db->query(
            "SELECT * FROM password_resets
             WHERE token = ? AND expires_at > NOW() AND used_at IS NULL",
            [$token]
        );
        $reset = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$reset) {
            return $this->view('pages/auth/reset-password', [
                'pageTitle' => 'Link verlopen',
                'error' => 'expired',
                'token' => null
            ]);
        }

        return $this->view('pages/auth/reset-password', [
            'pageTitle' => 'Nieuw wachtwoord instellen',
            'token' => $token,
            'email' => $reset['email']
        ]);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/forgot-password?error=csrf');
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validate token
        $stmt = $this->db->query(
            "SELECT * FROM password_resets
             WHERE token = ? AND expires_at > NOW() AND used_at IS NULL",
            [$token]
        );
        $reset = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$reset) {
            return $this->redirect('/forgot-password?error=invalid_token');
        }

        // Validate passwords
        if (strlen($password) < 8) {
            return $this->redirect("/reset-password/{$token}?error=password_short");
        }

        if ($password !== $passwordConfirm) {
            return $this->redirect("/reset-password/{$token}?error=password_mismatch");
        }

        // Update password in the correct table
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $accountType = $reset['account_type'] ?? 'user';

        if ($accountType === 'business') {
            $this->db->query(
                "UPDATE businesses SET password_hash = ? WHERE id = ?",
                [$hashedPassword, $reset['user_id']]
            );
        } else {
            $this->db->query(
                "UPDATE users SET password_hash = ? WHERE id = ?",
                [$hashedPassword, $reset['user_id']]
            );
        }

        // Mark token as used
        $this->db->query(
            "UPDATE password_resets SET used_at = NOW() WHERE id = ?",
            [$reset['id']]
        );

        // Send confirmation email
        $this->sendPasswordChangedEmail($reset['email']);

        return $this->redirect('/login?reset=success');
    }

    /**
     * Send password reset email
     */
    private function sendResetEmail(string $email, string $firstName, string $token): void
    {
        $resetUrl = "https://glamourschedule.nl/reset-password/{$token}";

        $subject = "Wachtwoord herstellen - GlamourSchedule";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:40px;text-align:center;">
                            <div style="font-size:48px;margin-bottom:10px;">🔐</div>
                            <h1 style="margin:0;font-size:26px;font-weight:700;">Wachtwoord herstellen</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;margin:0 0 20px;">Hoi <strong>{$firstName}</strong>,</p>

                            <p style="font-size:16px;color:#555;line-height:1.6;margin:0 0 25px;">
                                Je hebt een verzoek ingediend om je wachtwoord te herstellen.
                                Klik op onderstaande knop om een nieuw wachtwoord in te stellen.
                            </p>

                            <p style="text-align:center;margin:35px 0;">
                                <a href="{$resetUrl}" style="display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:18px 50px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:17px;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                                    Nieuw wachtwoord instellen
                                </a>
                            </p>

                            <div style="background:#0a0a0a;border-left:4px solid #000000;padding:15px 20px;margin:25px 0;border-radius:0 8px 8px 0;">
                                <p style="margin:0;color:#000000;font-size:14px;">
                                    <strong>Let op:</strong> Deze link is 1 uur geldig. Heb je dit verzoek niet gedaan? Negeer dan deze email.
                                </p>
                            </div>

                            <p style="font-size:13px;color:#999;margin:25px 0 0;">
                                Werkt de knop niet? Kopieer dan deze link:<br>
                                <a href="{$resetUrl}" style="color:#000000;word-break:break-all;">{$resetUrl}</a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#0a0a0a;padding:25px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#cccccc;font-size:13px;">&copy; 2025 GlamourSchedule</p>
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
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send password reset email: " . $e->getMessage());
        }
    }

    /**
     * Send password changed confirmation email
     */
    private function sendPasswordChangedEmail(string $email): void
    {
        $subject = "Je wachtwoord is gewijzigd - GlamourSchedule";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#333333,#000000);color:#ffffff;padding:35px;text-align:center;">
                            <div style="font-size:42px;margin-bottom:8px;">✓</div>
                            <h1 style="margin:0;font-size:24px;">Wachtwoord gewijzigd</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:35px;">
                            <p style="font-size:16px;color:#ffffff;line-height:1.6;">
                                Je wachtwoord voor GlamourSchedule is succesvol gewijzigd.
                            </p>
                            <p style="font-size:14px;color:#cccccc;margin-top:20px;">
                                Heb je dit niet gedaan? Neem dan direct contact met ons op via
                                <a href="mailto:support@glamourschedule.nl" style="color:#000000;">support@glamourschedule.nl</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#cccccc;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send password changed email: " . $e->getMessage());
        }
    }
}

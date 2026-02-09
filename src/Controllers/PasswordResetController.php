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
            'pageTitle' => $this->t('page_forgot_password')
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
            // Always show same success message to prevent information leak
            return $this->redirect('/forgot-password?success=1');
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
                'pageTitle' => $this->t('page_link_expired'),
                'error' => 'expired',
                'token' => null
            ]);
        }

        return $this->view('pages/auth/reset-password', [
            'pageTitle' => $this->t('page_new_password'),
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
        $currentYear = date('Y');

        // Get translations
        $subject = $this->t('email_password_reset_subject');
        $heading = $this->t('email_password_reset_heading');
        $greeting = $this->t('email_greeting', ['name' => $firstName]);
        $body = $this->t('email_password_reset_body');
        $buttonText = $this->t('email_password_reset_button');
        $notice = $this->t('email_password_reset_notice');
        $linkNotWorking = $this->t('email_link_not_working');

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>@media (prefers-color-scheme: dark) { .email-body, .email-outer { background:#000000 !important; } .email-content { background:#1a1a1a !important; border-color:#333333 !important; } .email-text { color:#ffffff !important; } .email-muted { color:#cccccc !important; } .email-footer { background:#111111 !important; border-color:#333333 !important; } .email-footer-text { color:#cccccc !important; } }</style>
</head>
<body class="email-body" style="margin:0;padding:0;font-family:Arial,sans-serif;background:#ffffff;">
    <table width="100%" cellpadding="0" cellspacing="0" class="email-outer" style="background:#ffffff;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" class="email-content" style="background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e0e0e0;">
                    <!-- Header -->
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:40px;text-align:center;">
                            <div style="font-size:48px;margin-bottom:10px;">🔐</div>
                            <h1 style="margin:0;font-size:26px;font-weight:700;">{$heading}</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p class="email-text" style="font-size:18px;color:#000000;margin:0 0 20px;">{$greeting}</p>

                            <p class="email-muted" style="font-size:16px;color:#666666;line-height:1.6;margin:0 0 25px;">
                                {$body}
                            </p>

                            <p style="text-align:center;margin:35px 0;">
                                <a href="{$resetUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:18px 50px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:17px;">
                                    {$buttonText}
                                </a>
                            </p>

                            <div style="background:#f5f5f5;border-left:4px solid #000000;padding:15px 20px;margin:25px 0;border-radius:0 8px 8px 0;">
                                <p class="email-text" style="margin:0;color:#000000;font-size:14px;">
                                    {$notice}
                                </p>
                            </div>

                            <p class="email-muted" style="font-size:13px;color:#666666;margin:25px 0 0;">
                                {$linkNotWorking}<br>
                                <a href="{$resetUrl}" style="color:#000000;word-break:break-all;">{$resetUrl}</a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td class="email-footer" style="background:#f5f5f5;padding:25px;text-align:center;border-top:1px solid #e0e0e0;">
                            <p class="email-footer-text" style="margin:0;color:#666666;font-size:13px;">&copy; {$currentYear} GlamourSchedule</p>
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
            $mailer = new Mailer($this->lang);
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
        $subject = $this->t('email_pw_changed_subject');
        $heading = $this->t('email_pw_changed_heading');
        $body = $this->t('email_pw_changed_body');
        $warning = $this->t('email_pw_changed_warning');
        $currentYear = date('Y');

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>@media (prefers-color-scheme: dark) { .email-body, .email-outer { background:#000000 !important; } .email-content { background:#1a1a1a !important; border-color:#333333 !important; } .email-text { color:#ffffff !important; } .email-muted { color:#cccccc !important; } .email-footer { background:#111111 !important; border-color:#333333 !important; } .email-footer-text { color:#cccccc !important; } }</style>
</head>
<body class="email-body" style="margin:0;padding:0;font-family:Arial,sans-serif;background:#ffffff;">
    <table width="100%" cellpadding="0" cellspacing="0" class="email-outer" style="background:#ffffff;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" class="email-content" style="background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e0e0e0;">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:35px;text-align:center;">
                            <div style="font-size:42px;margin-bottom:8px;">✓</div>
                            <h1 style="margin:0;font-size:24px;">{$heading}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:35px;">
                            <p class="email-text" style="font-size:16px;color:#000000;line-height:1.6;">
                                {$body}
                            </p>
                            <p class="email-muted" style="font-size:14px;color:#666666;margin-top:20px;">
                                {$warning}
                                <a href="mailto:support@glamourschedule.nl" style="color:#000000;">support@glamourschedule.nl</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-footer" style="background:#f5f5f5;padding:20px;text-align:center;border-top:1px solid #e0e0e0;">
                            <p class="email-footer-text" style="margin:0;color:#666666;font-size:12px;">&copy; {$currentYear} GlamourSchedule</p>
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
            $mailer = new Mailer($this->lang);
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send password changed email: " . $e->getMessage());
        }
    }
}

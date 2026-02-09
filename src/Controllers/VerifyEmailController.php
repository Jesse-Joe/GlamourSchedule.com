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

        // Get business details for verification check
        $stmt = $this->db->query("SELECT kvk_number, company_name as name, email FROM businesses WHERE id = ?", [$verification['business_id']]);
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

        // Log the business in (businesses are separate from customers)
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
        $this->sendWelcomeEmail($verification['business_id']);

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

        // Get business info (businesses are separate from customers)
        $stmt = $this->db->query(
            "SELECT id, company_name FROM businesses WHERE email = ?",
            [$email]
        );
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            return $this->redirect('/business/register');
        }

        // Generate new code
        $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Invalidate old codes
        $this->db->query(
            "UPDATE email_verifications SET verified_at = NOW() WHERE email = ? AND verified_at IS NULL",
            [$email]
        );

        // Insert new code (user_id is NULL for business registrations)
        $this->db->query(
            "INSERT INTO email_verifications (user_id, business_id, email, verification_code, expires_at)
             VALUES (NULL, ?, ?, ?, ?)",
            [$business['id'], $email, $newCode, $expiresAt]
        );

        // Send email
        $this->sendVerificationEmail($email, $business['company_name'], $newCode);

        return $this->redirect('/verify-email?success=resent');
    }

    private function sendVerificationEmail(string $email, string $companyName, string $code): void
    {
        $subject = str_replace('{code}', $code, $this->t('email_verify_subject'));
        $heading = $this->t('email_verify_title');
        $dear = $this->t('email_verify_dear') . ' ' . $companyName . ',';
        $codeText = $this->t('email_verify_use_code');
        $validText = $this->t('email_verify_valid_10min');

        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'>
        <style>@media (prefers-color-scheme: dark) { .email-body, .email-outer { background:#000000 !important; } .email-content { background:#1a1a1a !important; } .email-text { color:#ffffff !important; } .email-muted { color:#cccccc !important; } .code-box { background:#1a1a1a !important; border-color:#333333 !important; } .code-text { color:#ffffff !important; } .email-footer { background:#111111 !important; } .email-footer-text { color:#cccccc !important; } }</style>
        </head>
        <body class='email-body' style='margin:0;padding:0;font-family:Arial,sans-serif;background:#ffffff;'>
            <table width='100%' cellpadding='0' cellspacing='0' class='email-outer' style='background:#ffffff;padding:20px;'>
                <tr>
                    <td align='center'>
                        <table width='500' cellpadding='0' cellspacing='0' class='email-content' style='background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e0e0e0;'>
                            <tr>
                                <td style='background:#000000;color:#ffffff;padding:30px;text-align:center;'>
                                    <h1 style='margin:0;font-size:24px;'>{$heading}</h1>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:40px;text-align:center;'>
                                    <p class='email-text' style='font-size:16px;color:#000000;'>{$dear}</p>
                                    <p class='email-text' style='font-size:16px;color:#000000;'>{$codeText}</p>
                                    <div class='code-box' style='background:#f5f5f5;border:2px solid #e0e0e0;border-radius:10px;padding:30px;margin:20px 0;'>
                                        <span class='code-text' style='font-size:42px;font-weight:bold;letter-spacing:8px;color:#000000;font-family:monospace;'>{$code}</span>
                                    </div>
                                    <p class='email-muted' style='font-size:14px;color:#666666;'>{$validText}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";

        try {
            $mailer = new Mailer($this->lang);
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send verification email: " . $e->getMessage());
        }
    }

    private function sendWelcomeEmail(int $businessId): void
    {
        // Businesses are separate from customers - query directly
        $stmt = $this->db->query("SELECT * FROM businesses WHERE id = ?", [$businessId]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) return;

        $dashboardUrl = "https://glamourschedule.com/business/dashboard";
        $businessUrl = "https://glamourschedule.com/business/{$business['slug']}";

        $subject = $this->t('email_welcome_activated_subject');
        $heading = $this->t('email_welcome_heading');
        $subtitle = $this->t('email_welcome_activated_msg');
        $dear = $this->t('email_verify_dear') . ' ' . $business['company_name'] . ',';
        $congrats = $this->t('email_welcome_congrats_text');
        $nextSteps = $this->t('email_welcome_next_steps_title');
        $step1 = $this->t('email_welcome_step1');
        $step2 = $this->t('email_welcome_step2');
        $step3 = $this->t('email_welcome_step3');
        $step4 = $this->t('email_welcome_step4');
        $goBtn = $this->t('email_welcome_dashboard_button');
        $yourPage = $this->t('email_welcome_your_page_text');
        $year = date('Y');

        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'>
        <style>@media (prefers-color-scheme: dark) { .email-body, .email-outer { background:#000000 !important; } .email-content { background:#1a1a1a !important; } .email-text { color:#ffffff !important; } .email-muted { color:#cccccc !important; } .email-link { color:#ffffff !important; } .steps-box { background:#1a1a1a !important; border-color:#333333 !important; } .email-footer { background:#111111 !important; } .email-footer-text { color:#cccccc !important; } }</style>
        </head>
        <body class='email-body' style='margin:0;padding:0;font-family:Arial,sans-serif;background:#ffffff;'>
            <table width='100%' cellpadding='0' cellspacing='0' class='email-outer' style='background:#ffffff;padding:20px;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' class='email-content' style='background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e0e0e0;'>
                            <tr>
                                <td style='background:#000000;color:#ffffff;padding:40px;text-align:center;'>
                                    <h1 style='margin:0;font-size:28px;'>{$heading}</h1>
                                    <p style='margin:10px 0 0;opacity:0.9;'>{$subtitle}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:40px;'>
                                    <p class='email-text' style='font-size:16px;color:#000000;'>{$dear}</p>
                                    <p class='email-text' style='font-size:16px;color:#000000;line-height:1.6;'>
                                        {$congrats}
                                    </p>

                                    <div class='steps-box' style='background:#f5f5f5;border-left:4px solid #000000;padding:20px;margin:20px 0;border-radius:0 8px 8px 0;'>
                                        <h3 class='email-text' style='margin:0 0 15px;color:#000000;'>{$nextSteps}</h3>
                                        <ol class='email-text' style='margin:0;padding-left:20px;color:#000000;'>
                                            <li style='margin:10px 0;'>{$step1}</li>
                                            <li style='margin:10px 0;'>{$step2}</li>
                                            <li style='margin:10px 0;'>{$step3}</li>
                                            <li style='margin:10px 0;'>{$step4}</li>
                                        </ol>
                                    </div>

                                    <p style='text-align:center;margin:30px 0;'>
                                        <a href='{$dashboardUrl}' style='display:inline-block;background:#000000;color:#ffffff;padding:15px 40px;text-decoration:none;border-radius:8px;font-weight:bold;'>{$goBtn}</a>
                                    </p>

                                    <p class='email-muted' style='font-size:14px;color:#666666;'>
                                        {$yourPage} <a class='email-link' href='{$businessUrl}' style='color:#000000;'>{$businessUrl}</a>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class='email-footer' style='background:#f5f5f5;padding:20px;text-align:center;border-top:1px solid #e0e0e0;'>
                                    <p class='email-footer-text' style='margin:0;color:#666666;font-size:12px;'>&copy; {$year} GlamourSchedule</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";

        try {
            $mailer = new Mailer($this->lang);
            $mailer->send($business['email'], $subject, $htmlBody);
            // Dashboard reminder will be sent 24h later via cron job
        } catch (\Exception $e) {
            error_log("Failed to send welcome email: " . $e->getMessage());
        }
    }

    private function sendDashboardReminderEmail(array $business): void
    {
        $dashboardUrl = "https://glamourschedule.com/business/dashboard";
        $servicesUrl = "https://glamourschedule.com/business/services";
        $photosUrl = "https://glamourschedule.com/business/photos";
        $profileUrl = "https://glamourschedule.com/business/profile";

        $subject = $this->t('email_dash_reminder_subject');
        $heading = $this->t('email_dash_reminder_heading');
        $dear = $this->t('email_verify_dear') . ' ' . $business['company_name'] . ',';
        $bodyText = $this->t('email_dash_reminder_body');
        $completeTitle = $this->t('email_dash_reminder_complete_title');
        $photosTitle = $this->t('email_dash_photos_title');
        $photosDesc = $this->t('email_dash_photos_desc');
        $servicesTitle = $this->t('email_dash_services_title');
        $servicesDesc = $this->t('email_dash_services_desc');
        $profileTitle = $this->t('email_dash_profile_title');
        $profileDesc = $this->t('email_dash_profile_desc');
        $tip = $this->t('email_dash_tip');
        $goBtn = $this->t('email_dash_button');

        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'>
        <style>@media (prefers-color-scheme: dark) { .email-body, .email-outer { background:#000000 !important; } .email-content { background:#1a1a1a !important; border-color:#333333 !important; } .email-text { color:#ffffff !important; } .email-muted { color:#cccccc !important; } .card-item { background:#1a1a1a !important; border-color:#333333 !important; } .card-icon { background:#333333 !important; } .card-subtitle { color:#cccccc !important; } .email-footer { background:#111111 !important; border-color:#333333 !important; } .email-footer-text { color:#cccccc !important; } }</style>
        </head>
        <body class='email-body' style='margin:0;padding:0;font-family:Arial,sans-serif;background:#ffffff;'>
            <table width='100%' cellpadding='0' cellspacing='0' class='email-outer' style='background:#ffffff;padding:20px;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' class='email-content' style='background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e0e0e0;'>
                            <tr>
                                <td style='background:#000000;color:#ffffff;padding:40px;text-align:center;'>
                                    <div style='width:60px;height:60px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:50%;margin:0 auto 15px;display:flex;align-items:center;justify-content:center;'>
                                        <span style='font-size:28px;color:#000;font-weight:bold;'>&#10003;</span>
                                    </div>
                                    <h1 style='margin:0;font-size:24px;'>{$heading}</h1>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:40px;'>
                                    <p class='email-text' style='font-size:16px;color:#000000;'>{$dear}</p>
                                    <p class='email-muted' style='font-size:16px;color:#666666;line-height:1.6;'>
                                        {$bodyText}
                                    </p>

                                    <div style='background:#f5f5f5;border-radius:12px;padding:25px;margin:25px 0;'>
                                        <h3 class='email-text' style='margin:0 0 20px;color:#000000;font-size:18px;'>{$completeTitle}</h3>

                                        <table width='100%' cellpadding='0' cellspacing='0'>
                                            <tr>
                                                <td style='padding:8px 0;'>
                                                    <a href='{$photosUrl}' class='card-item' style='color:#000000;text-decoration:none;display:block;padding:15px;background:#ffffff;border-radius:8px;border:1px solid #e0e0e0;'>
                                                        <table cellpadding='0' cellspacing='0'>
                                                            <tr>
                                                                <td style='width:45px;vertical-align:top;'>
                                                                    <div class='card-icon' style='width:36px;height:36px;background:#f5f5f5;border-radius:8px;text-align:center;line-height:36px;'>
                                                                        <span style='color:#f59e0b;font-size:18px;'>&#9634;</span>
                                                                    </div>
                                                                </td>
                                                                <td style='vertical-align:top;'>
                                                                    <strong class='email-text' style='color:#000;'>{$photosTitle}</strong><br>
                                                                    <span class='card-subtitle' style='color:#666666;font-size:13px;'>{$photosDesc}</span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style='padding:8px 0;'>
                                                    <a href='{$servicesUrl}' class='card-item' style='color:#000000;text-decoration:none;display:block;padding:15px;background:#ffffff;border-radius:8px;border:1px solid #e0e0e0;'>
                                                        <table cellpadding='0' cellspacing='0'>
                                                            <tr>
                                                                <td style='width:45px;vertical-align:top;'>
                                                                    <div class='card-icon' style='width:36px;height:36px;background:#f5f5f5;border-radius:8px;text-align:center;line-height:36px;'>
                                                                        <span style='color:#f59e0b;font-size:18px;'>&#9986;</span>
                                                                    </div>
                                                                </td>
                                                                <td style='vertical-align:top;'>
                                                                    <strong class='email-text' style='color:#000;'>{$servicesTitle}</strong><br>
                                                                    <span class='card-subtitle' style='color:#666666;font-size:13px;'>{$servicesDesc}</span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style='padding:8px 0;'>
                                                    <a href='{$profileUrl}' class='card-item' style='color:#000000;text-decoration:none;display:block;padding:15px;background:#ffffff;border-radius:8px;border:1px solid #e0e0e0;'>
                                                        <table cellpadding='0' cellspacing='0'>
                                                            <tr>
                                                                <td style='width:45px;vertical-align:top;'>
                                                                    <div class='card-icon' style='width:36px;height:36px;background:#f5f5f5;border-radius:8px;text-align:center;line-height:36px;'>
                                                                        <span style='color:#f59e0b;font-size:18px;'>&#9673;</span>
                                                                    </div>
                                                                </td>
                                                                <td style='vertical-align:top;'>
                                                                    <strong class='email-text' style='color:#000;'>{$profileTitle}</strong><br>
                                                                    <span class='card-subtitle' style='color:#666666;font-size:13px;'>{$profileDesc}</span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div style='background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:12px;padding:20px;margin:25px 0;'>
                                        <table cellpadding='0' cellspacing='0'>
                                            <tr>
                                                <td style='width:30px;vertical-align:top;'>
                                                    <span style='font-size:20px;color:#000;'>&#9733;</span>
                                                </td>
                                                <td>
                                                    <p style='margin:0;color:#000;font-weight:600;font-size:15px;'>
                                                        {$tip}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <p style='text-align:center;margin:30px 0;'>
                                        <a href='{$dashboardUrl}' style='display:inline-block;background:#ffffff;color:#000000;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;'>
                                            {$goBtn} &#8594;
                                        </a>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class='email-footer' style='background:#f5f5f5;padding:20px;text-align:center;font-size:12px;border-top:1px solid #e0e0e0;'>
                                    <p class='email-footer-text' style='margin:0;color:#666666;'>&copy; " . date('Y') . " GlamourSchedule</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";

        try {
            $mailer = new Mailer($this->lang);
            $mailer->send($business['email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send dashboard reminder email: " . $e->getMessage());
        }
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0] ?? '';
        $domain = $parts[1] ?? 'example.com';

        if (strlen($name) <= 4) {
            $maskedName = substr($name, 0, 1) . str_repeat('*', max(strlen($name) - 1, 1));
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 4, 2)) . substr($name, -2);
        }

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
            'title' => $this->t('push_new_biz_title'),
            'body' => str_replace([':name', ':city'], [$business['company_name'], $business['city']], $this->t('push_new_biz_body')),
            'icon' => '/images/icon-192.png',
            'badge' => '/images/badge-72.png',
            'tag' => 'new-business-' . $business['id'],
            'data' => [
                'url' => $businessUrl,
                'businessId' => $business['id'],
                'type' => 'new_business'
            ],
            'actions' => [
                ['action' => 'view', 'title' => $this->t('push_new_biz_view')]
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
        $mailer = new Mailer($this->lang);

        $subject = str_replace([':name', ':city'], [$business['company_name'], $business['city']], $this->t('email_new_biz_subject'));

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
            : str_replace(':name', $business['company_name'], $this->t('email_new_biz_fallback'));
        $nbHeading = $this->t('email_new_biz_heading');
        $nbHi = str_replace(':name', $firstName, $this->t('email_new_biz_hi'));
        $nbIntro = $this->t('email_new_biz_intro');
        $nbCta = $this->t('email_new_biz_cta');
        $nbHurry = $this->t('email_new_biz_hurry');
        $nbFooter = $this->t('email_new_biz_email_footer');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>@media (prefers-color-scheme: dark) { .email-body, .email-outer { background:#000000 !important; } .email-content { background:#1a1a1a !important; border-color:#333333 !important; } .email-text { color:#ffffff !important; } .email-muted { color:#cccccc !important; } .email-footer { background:#111111 !important; border-color:#333333 !important; } .email-footer-text { color:#cccccc !important; } }</style>
</head>
<body class='email-body' style='margin:0;padding:0;font-family:Arial,sans-serif;background:#ffffff;'>
    <table width='100%' cellpadding='0' cellspacing='0' class='email-outer' style='background:#ffffff;padding:20px;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' class='email-content' style='background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e0e0e0;'>
                    <!-- Header -->
                    <tr>
                        <td style='background:#000000;color:#ffffff;padding:30px;text-align:center;'>
                            <div style='font-size:2.5rem;margin-bottom:10px;'>✨</div>
                            <h1 style='margin:0;font-size:24px;font-weight:700;'>{$nbHeading}</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style='padding:40px 30px;'>
                            <p class='email-text' style='font-size:16px;color:#000000;margin:0 0 20px;'>
                                {$nbHi}
                            </p>

                            <p class='email-text' style='font-size:16px;color:#000000;margin:0 0 25px;line-height:1.6;'>
                                {$nbIntro}
                            </p>

                            <!-- Business Card -->
                            <div style='background:#f5f5f5;border-radius:12px;padding:25px;margin:25px 0;border:2px solid #e0e0e0;'>
                                <h2 class='email-text' style='margin:0 0 10px;color:#000000;font-size:20px;'>{$business['company_name']}</h2>

                                <p style='margin:0 0 8px;color:#666666;font-size:14px;'>
                                    📍 {$location}
                                </p>

                                <p style='margin:0 0 8px;color:#666666;font-size:14px;'>
                                    💅 {$categoryName}
                                </p>

                                <p style='margin:15px 0 0;color:#666666;font-size:14px;line-height:1.5;'>
                                    {$description}
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <p style='text-align:center;margin:30px 0;'>
                                <a href='{$businessUrl}' style='display:inline-block;background:#000000;color:#ffffff;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;'>
                                    {$nbCta}
                                </a>
                            </p>

                            <p class='email-muted' style='font-size:14px;color:#666666;text-align:center;margin:20px 0 0;'>
                                {$nbHurry}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class='email-footer' style='background:#f5f5f5;padding:25px;text-align:center;border-top:1px solid #e0e0e0;'>
                            <p class='email-footer-text' style='margin:0 0 10px;color:#666666;font-size:12px;'>
                                {$nbFooter}
                            </p>
                            <p class='email-footer-text' style='margin:0;color:#666666;font-size:12px;'>
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

        $subject = $this->t('email_kvk_warning_subject');

        $kvkHeading = $this->t('email_kvk_action_required');
        $kvkSub = $this->t('email_kvk_not_active');
        $kvkDear = $this->t('email_verify_dear') . ' ' . $businessData['name'] . ',';
        $kvkWaiting = $this->t('email_kvk_waiting_verification');
        $kvkWithout = $this->t('email_kvk_without_text');
        $kvkWhy = $this->t('email_kvk_why_important');
        $kvkR1 = $this->t('email_kvk_reason1');
        $kvkR2 = $this->t('email_kvk_reason2');
        $kvkR3 = $this->t('email_kvk_reason3');
        $kvkR4 = $this->t('email_kvk_reason4');
        $kvkSolution = $this->t('email_kvk_solution_title');
        $kvkAddText = $this->t('email_kvk_add_text');
        $kvkAddBtn = $this->t('email_kvk_add_button');
        $kvkNoWorries = $this->t('email_kvk_no_worries');

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>@media (prefers-color-scheme: dark) { .email-body, .email-outer { background:#000000 !important; } .email-content { background:#1a1a1a !important; border-color:#333333 !important; } .email-text { color:#ffffff !important; } .email-muted { color:#cccccc !important; } .email-list { color:#cccccc !important; } .email-footer { background:#111111 !important; border-color:#333333 !important; } .email-footer-text { color:#cccccc !important; } }</style>
</head>
<body class="email-body" style="margin:0;padding:0;font-family:Arial,sans-serif;background:#ffffff;">
    <table width="100%" cellpadding="0" cellspacing="0" class="email-outer" style="background:#ffffff;padding:20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" class="email-content" style="background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e0e0e0;">
                <tr><td style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#ffffff;padding:30px;text-align:center;">
                    <div style="font-size:50px;margin-bottom:15px;">&#9888;</div>
                    <h1 style="margin:0;font-size:24px;">{$kvkHeading}</h1>
                    <p style="margin:10px 0 0;opacity:0.95;">{$kvkSub}</p>
                </td></tr>
                <tr><td style="padding:35px;">
                    <p class="email-text" style="font-size:16px;color:#000000;margin:0 0 20px;">{$kvkDear}</p>

                    <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:12px;padding:20px;margin-bottom:25px;">
                        <h3 style="margin:0 0 10px;color:#92400e;display:flex;align-items:center;gap:10px;">
                            <span style="font-size:24px;">&#128274;</span> {$kvkWaiting}
                        </h3>
                        <p style="margin:0;color:#92400e;line-height:1.6;">
                            {$kvkWithout}
                        </p>
                    </div>

                    <h3 class="email-text" style="color:#000000;margin:0 0 15px;">{$kvkWhy}</h3>
                    <ul class="email-list" style="color:#666666;line-height:1.8;padding-left:20px;margin:0 0 25px;">
                        <li>{$kvkR1}</li>
                        <li>{$kvkR2}</li>
                        <li>{$kvkR3}</li>
                        <li>{$kvkR4}</li>
                    </ul>

                    <div style="background:#f0fdf4;border:2px solid #22c55e;border-radius:12px;padding:20px;margin-bottom:25px;">
                        <h3 style="margin:0 0 10px;color:#166534;">
                            <span style="font-size:20px;">&#10003;</span> {$kvkSolution}
                        </h3>
                        <p style="margin:0;color:#166534;line-height:1.6;">
                            {$kvkAddText}
                        </p>
                    </div>

                    <div style="text-align:center;margin:30px 0;">
                        <a href="{$profileUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:16px 40px;border-radius:10px;text-decoration:none;font-weight:600;font-size:16px;">
                            {$kvkAddBtn}
                        </a>
                    </div>

                    <p class="email-muted" style="font-size:14px;color:#666666;text-align:center;margin:0;">
                        {$kvkNoWorries}
                    </p>
                </td></tr>
                <tr><td class="email-footer" style="background:#f5f5f5;padding:20px;text-align:center;border-top:1px solid #e0e0e0;">
                    <p class="email-footer-text" style="margin:0;color:#666666;font-size:12px;">&copy; 2026 GlamourSchedule - Beauty & Wellness Bookings</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer($this->lang);
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

        $verificationStatus = $isAutoVerified ? $this->t('email_admin_auto_verified') : $this->t('email_admin_waiting');
        $statusColor = $isAutoVerified ? '#22c55e' : '#f59e0b';

        $subject = $isAutoVerified
            ? str_replace(':name', $businessData['name'], $this->t('email_admin_new_biz_subject'))
            : str_replace(':name', $businessData['name'], $this->t('email_admin_action_subject'));

        $kvkNotFilled = $this->t('email_admin_not_filled');
        $kvkDisplay = !empty($businessData['kvk_number']) ? $businessData['kvk_number'] : '<span style="color:#dc2626;">' . $kvkNotFilled . '</span>';
        $adminHeading = $this->t('email_admin_heading');
        $adminNotif = $this->t('email_admin_notification');
        $adminEmailLabel = $this->t('email_admin_email_label');
        $adminKvkLabel = $this->t('email_admin_kvk_label');
        $adminStatusLabel = $this->t('email_admin_status_label');
        $adminAccept = $this->t('email_admin_accept');
        $adminReject = $this->t('email_admin_reject');
        $adminVerifyPrompt = $this->t('email_admin_verify_prompt');
        $adminViewPanel = $this->t('email_admin_view_panel');

        // Build action buttons for non-verified businesses
        $actionButtons = '';
        if (!$isAutoVerified) {
            $verifyUrl = "https://glamourschedule.nl/admin/verify-business/{$verificationToken}";
            $actionButtons = <<<HTML
                    <div style="margin-top:30px;text-align:center;">
                        <p class="email-muted" style="margin:0 0 20px;color:#666666;font-size:14px;">{$adminVerifyPrompt}</p>
                        <a href="{$verifyUrl}?action=approve" style="display:inline-block;background:#000000;color:#ffffff;padding:16px 40px;border-radius:10px;text-decoration:none;font-weight:600;margin:5px;">
                            ✓ {$adminAccept}
                        </a>
                        <a href="{$verifyUrl}?action=reject" style="display:inline-block;background:#ffffff;color:#000000;padding:16px 40px;border-radius:10px;text-decoration:none;font-weight:600;border:2px solid #e0e0e0;margin:5px;">
                            ✗ {$adminReject}
                        </a>
                    </div>
HTML;
        } else {
            $actionButtons = <<<HTML
                    <div style="margin-top:30px;text-align:center;">
                        <a href="https://glamourschedule.nl/admin/businesses" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 30px;border-radius:8px;text-decoration:none;font-weight:600;">
                            {$adminViewPanel}
                        </a>
                    </div>
HTML;
        }

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>@media (prefers-color-scheme: dark) { .email-body, .email-outer { background:#000000 !important; } .email-content { background:#1a1a1a !important; border-color:#333333 !important; } .email-text { color:#ffffff !important; } .email-muted { color:#cccccc !important; } .email-footer { background:#111111 !important; border-color:#333333 !important; } .email-footer-text { color:#cccccc !important; } }</style>
</head>
<body class="email-body" style="margin:0;padding:0;font-family:Arial,sans-serif;background:#ffffff;">
    <table width="100%" cellpadding="0" cellspacing="0" class="email-outer" style="background:#ffffff;padding:40px 20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" class="email-content" style="background:#ffffff;border-radius:20px;overflow:hidden;border:1px solid #e0e0e0;">
                <tr><td style="background:#000000;color:#ffffff;padding:40px;text-align:center;">
                    <div style="font-size:40px;margin-bottom:15px;">🏢</div>
                    <h1 style="margin:0;font-size:24px;font-weight:700;">{$adminHeading}</h1>
                    <p style="margin:10px 0 0;opacity:0.8;font-size:14px;">{$adminNotif}</p>
                </td></tr>
                <tr><td style="padding:40px;">
                    <div style="background:{$statusColor};color:#fff;padding:12px 24px;border-radius:50px;margin-bottom:30px;display:inline-block;font-weight:600;">
                        {$verificationStatus}
                    </div>

                    <h2 class="email-text" style="margin:0 0 25px 0;color:#000000;font-size:28px;">{$businessData['name']}</h2>

                    <table style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:12px;overflow:hidden;">
                        <tr>
                            <td style="padding:15px 20px;border-bottom:1px solid #e5e7eb;color:#6b7280;width:140px;font-weight:500;">{$adminEmailLabel}</td>
                            <td style="padding:15px 20px;border-bottom:1px solid #e5e7eb;color:#111827;font-weight:600;">{$businessData['email']}</td>
                        </tr>
                        <tr>
                            <td style="padding:15px 20px;border-bottom:1px solid #e5e7eb;color:#6b7280;font-weight:500;">{$adminKvkLabel}</td>
                            <td style="padding:15px 20px;border-bottom:1px solid #e5e7eb;color:#111827;font-weight:600;">{$kvkDisplay}</td>
                        </tr>
                        <tr>
                            <td style="padding:15px 20px;color:#6b7280;font-weight:500;">{$adminStatusLabel}</td>
                            <td style="padding:15px 20px;color:#111827;font-weight:600;">{$verificationStatus}</td>
                        </tr>
                    </table>

                    {$actionButtons}
                </td></tr>
                <tr><td class="email-footer" style="background:#f5f5f5;padding:25px;text-align:center;border-top:1px solid #e0e0e0;">
                    <p class="email-footer-text" style="margin:0;color:#666666;font-size:12px;">&copy; 2026 GlamourSchedule - Admin Portal</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;

        try {
            $mailer = new Mailer($this->lang);
            foreach ($adminEmails as $adminEmail) {
                $mailer->send($adminEmail, $subject, $htmlBody);
            }
        } catch (\Exception $e) {
            error_log("Failed to send admin notification email: " . $e->getMessage());
        }
    }
}

<?php
/**
 * Cron Job: Send Dashboard Reminder Emails
 *
 * Sends reminder emails to businesses that:
 * - Have status = 'active'
 * - Were created more than 24 hours ago
 * - Haven't received a reminder yet (reminder_sent_at IS NULL)
 *
 * Run this script hourly via cron:
 * 0 * * * * /usr/bin/php /var/www/glamourschedule/cron/send_dashboard_reminders.php
 */

// Only allow CLI execution
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

define('GLAMOUR_LOADED', true);
define('BASE_PATH', dirname(__DIR__));

$config = require BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/vendor/autoload.php';

use GlamourSchedule\Core\Database;
use GlamourSchedule\Core\Mailer;

try {
    $db = new Database($config['database']);

    // Find businesses that need reminders:
    // - Active status
    // - Created more than 24 hours ago
    // - No reminder sent yet
    $stmt = $db->query(
        "SELECT id, company_name, email, slug
         FROM businesses
         WHERE status = 'active'
           AND reminder_sent_at IS NULL
           AND created_at <= DATE_SUB(NOW(), INTERVAL 24 HOUR)
         LIMIT 50"
    );

    $businesses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    if (empty($businesses)) {
        echo date('Y-m-d H:i:s') . " - No reminders to send.\n";
        exit(0);
    }

    echo date('Y-m-d H:i:s') . " - Found " . count($businesses) . " businesses to remind.\n";

    $mailer = new Mailer();
    $sent = 0;
    $failed = 0;

    foreach ($businesses as $business) {
        try {
            $htmlBody = getDashboardReminderHtml($business);
            $subject = "Vergeet niet je dashboard te verkennen! - GlamourSchedule";

            $mailer->send($business['email'], $subject, $htmlBody);

            // Mark reminder as sent
            $db->query(
                "UPDATE businesses SET reminder_sent_at = NOW() WHERE id = ?",
                [$business['id']]
            );

            $sent++;
            echo "  Sent to: {$business['email']} ({$business['company_name']})\n";

            // Small delay between emails
            usleep(100000); // 100ms

        } catch (Exception $e) {
            $failed++;
            echo "  Failed: {$business['email']} - " . $e->getMessage() . "\n";
        }
    }

    echo date('Y-m-d H:i:s') . " - Done. Sent: {$sent}, Failed: {$failed}\n";

} catch (Exception $e) {
    echo date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Generate the dashboard reminder email HTML
 */
function getDashboardReminderHtml(array $business): string
{
    $dashboardUrl = "https://glamourschedule.com/business/dashboard";
    $servicesUrl = "https://glamourschedule.com/business/services";
    $photosUrl = "https://glamourschedule.com/business/photos";
    $profileUrl = "https://glamourschedule.com/business/profile";
    $companyName = htmlspecialchars($business['company_name']);

    return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'></head>
<body style='margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background:#0a0a0a;padding:20px;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background:#1a1a1a;border-radius:16px;overflow:hidden;'>
                    <tr>
                        <td style='background:#000000;color:#ffffff;padding:40px;text-align:center;'>
                            <div style='width:60px;height:60px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:50%;margin:0 auto 15px;display:flex;align-items:center;justify-content:center;'>
                                <span style='font-size:28px;color:#000;font-weight:bold;'>&#10003;</span>
                            </div>
                            <h1 style='margin:0;font-size:24px;'>Vergeet niet je pagina compleet te maken!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:40px;'>
                            <p style='font-size:16px;color:#ffffff;'>Beste <strong>{$companyName}</strong>,</p>
                            <p style='font-size:16px;color:#cccccc;line-height:1.6;'>
                                Je account is geactiveerd, maar je pagina is nog niet compleet.
                                Een complete pagina trekt meer klanten aan en zorgt voor meer boekingen!
                            </p>

                            <div style='background:#0a0a0a;border-radius:12px;padding:25px;margin:25px 0;'>
                                <h3 style='margin:0 0 20px;color:#ffffff;font-size:18px;'>Maak je pagina compleet:</h3>

                                <table width='100%' cellpadding='0' cellspacing='0'>
                                    <tr>
                                        <td style='padding:8px 0;'>
                                            <a href='{$photosUrl}' style='color:#ffffff;text-decoration:none;display:block;padding:15px;background:#1a1a1a;border-radius:8px;border:1px solid #333;'>
                                                <table cellpadding='0' cellspacing='0'>
                                                    <tr>
                                                        <td style='width:45px;vertical-align:top;'>
                                                            <div style='width:36px;height:36px;background:#333;border-radius:8px;text-align:center;line-height:36px;'>
                                                                <span style='color:#f59e0b;font-size:18px;'>&#9634;</span>
                                                            </div>
                                                        </td>
                                                        <td style='vertical-align:top;'>
                                                            <strong style='color:#fff;'>Upload foto's</strong><br>
                                                            <span style='color:#888;font-size:13px;'>Logo, cover foto en portfolio</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='padding:8px 0;'>
                                            <a href='{$servicesUrl}' style='color:#ffffff;text-decoration:none;display:block;padding:15px;background:#1a1a1a;border-radius:8px;border:1px solid #333;'>
                                                <table cellpadding='0' cellspacing='0'>
                                                    <tr>
                                                        <td style='width:45px;vertical-align:top;'>
                                                            <div style='width:36px;height:36px;background:#333;border-radius:8px;text-align:center;line-height:36px;'>
                                                                <span style='color:#f59e0b;font-size:18px;'>&#9986;</span>
                                                            </div>
                                                        </td>
                                                        <td style='vertical-align:top;'>
                                                            <strong style='color:#fff;'>Voeg diensten toe</strong><br>
                                                            <span style='color:#888;font-size:13px;'>Prijzen, tijdsduur en beschrijvingen</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='padding:8px 0;'>
                                            <a href='{$profileUrl}' style='color:#ffffff;text-decoration:none;display:block;padding:15px;background:#1a1a1a;border-radius:8px;border:1px solid #333;'>
                                                <table cellpadding='0' cellspacing='0'>
                                                    <tr>
                                                        <td style='width:45px;vertical-align:top;'>
                                                            <div style='width:36px;height:36px;background:#333;border-radius:8px;text-align:center;line-height:36px;'>
                                                                <span style='color:#f59e0b;font-size:18px;'>&#9673;</span>
                                                            </div>
                                                        </td>
                                                        <td style='vertical-align:top;'>
                                                            <strong style='color:#fff;'>Vul je profiel aan</strong><br>
                                                            <span style='color:#888;font-size:13px;'>Adres, openingstijden en KVK</span>
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
                                                <strong>Tip:</strong> Salons met complete profielen krijgen tot 3x meer boekingen!
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <p style='text-align:center;margin:30px 0;'>
                                <a href='{$dashboardUrl}' style='display:inline-block;background:#ffffff;color:#000000;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;'>
                                    Naar mijn Dashboard &#8594;
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style='background:#0a0a0a;padding:20px;text-align:center;color:#666;font-size:12px;border-top:1px solid #333;'>
                            <p style='margin:0;'>&copy; 2026 GlamourSchedule - Beauty & Wellness Bookings</p>
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

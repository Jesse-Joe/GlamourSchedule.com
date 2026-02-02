<?php
/**
 * Cron Job: Trial Expiry Check
 *
 * Checks for businesses with expiring trials and sends warning emails.
 * Also deactivates businesses 3 days after trial expiry.
 *
 * Run this script daily via cron:
 * 0 9 * * * /usr/bin/php /var/www/glamourschedule/cron/trial-expiry.php
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

// Log file
$logFile = BASE_PATH . '/storage/logs/cron-trial-expiry.log';

function logMessage(string $message): void
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    echo "[$timestamp] $message\n";
}

try {
    $db = new Database($config['database']);

    logMessage("Starting trial expiry check");

    // =============================================
    // PART 1: Send warning emails for trials ending today
    // =============================================
    $stmt = $db->query(
        "SELECT b.id, b.company_name, b.email, b.trial_ends_at, b.subscription_price,
                b.is_early_adopter, b.language
         FROM businesses b
         WHERE b.subscription_status = 'trial'
           AND b.trial_ends_at = CURDATE()
           AND b.trial_warning_sent_at IS NULL"
    );
    $expiringBusinesses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $warningsSent = 0;
    foreach ($expiringBusinesses as $business) {
        try {
            $lang = $business['language'] ?? 'nl';
            $mailer = new Mailer($lang);

            $htmlBody = getTrialExpiryEmailHtml($business);
            $subject = $lang === 'nl'
                ? "Je proefperiode eindigt vandaag - GlamourSchedule"
                : "Your trial ends today - GlamourSchedule";

            $mailer->send($business['email'], $subject, $htmlBody);

            // Mark warning as sent
            $db->query(
                "UPDATE businesses SET trial_warning_sent_at = NOW() WHERE id = ?",
                [$business['id']]
            );

            $warningsSent++;
            logMessage("Trial expiry warning sent to: {$business['email']} ({$business['company_name']})");

            usleep(100000); // 100ms delay between emails

        } catch (Exception $e) {
            logMessage("Failed to send warning to {$business['email']}: " . $e->getMessage());
        }
    }

    logMessage("Trial expiry warnings sent: $warningsSent");

    // =============================================
    // PART 2: Deactivate businesses 3 days after trial expiry
    // =============================================
    $stmt = $db->query(
        "SELECT b.id, b.company_name, b.email, b.trial_ends_at, b.language
         FROM businesses b
         WHERE b.subscription_status = 'trial'
           AND b.trial_warning_sent_at IS NOT NULL
           AND b.trial_ends_at <= DATE_SUB(CURDATE(), INTERVAL 3 DAY)"
    );
    $expiredBusinesses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $deactivated = 0;
    foreach ($expiredBusinesses as $business) {
        try {
            // Deactivate business
            $db->query(
                "UPDATE businesses SET subscription_status = 'expired', status = 'inactive' WHERE id = ?",
                [$business['id']]
            );

            // Send deactivation email
            $lang = $business['language'] ?? 'nl';
            $mailer = new Mailer($lang);

            $htmlBody = getDeactivationEmailHtml($business);
            $subject = $lang === 'nl'
                ? "Je account is gedeactiveerd - GlamourSchedule"
                : "Your account has been deactivated - GlamourSchedule";

            $mailer->send($business['email'], $subject, $htmlBody);

            $deactivated++;
            logMessage("Business deactivated: {$business['company_name']} ({$business['email']})");

            usleep(100000); // 100ms delay

        } catch (Exception $e) {
            logMessage("Failed to deactivate {$business['email']}: " . $e->getMessage());
        }
    }

    logMessage("Businesses deactivated: $deactivated");
    logMessage("Trial expiry check complete. Warnings: $warningsSent, Deactivated: $deactivated");

} catch (Exception $e) {
    logMessage("Error: " . $e->getMessage());
    exit(1);
}

/**
 * Generate trial expiry warning email HTML
 */
function getTrialExpiryEmailHtml(array $business): string
{
    $companyName = htmlspecialchars($business['company_name']);
    $price = number_format($business['subscription_price'] ?? 29.00, 2, ',', '.');
    $isEarlyAdopter = !empty($business['is_early_adopter']);
    $lang = $business['language'] ?? 'nl';

    $subscriptionUrl = "https://glamourschedule.com/business/subscription";

    if ($lang === 'nl') {
        $title = "Je proefperiode eindigt vandaag!";
        $greeting = "Beste {$companyName},";
        $message = "Je gratis proefperiode van 14 dagen eindigt vandaag. Om je salon online te houden en boekingen te blijven ontvangen, activeer je nu je abonnement.";
        $priceLabel = $isEarlyAdopter ? "Early Adopter Prijs" : "Maandelijks";
        $buttonText = "Activeer nu";
        $warning = "Zonder actief abonnement wordt je pagina over 3 dagen offline gehaald.";
    } else {
        $title = "Your trial ends today!";
        $greeting = "Dear {$companyName},";
        $message = "Your free 14-day trial ends today. To keep your salon online and continue receiving bookings, activate your subscription now.";
        $priceLabel = $isEarlyAdopter ? "Early Adopter Price" : "Monthly";
        $buttonText = "Activate now";
        $warning = "Without an active subscription, your page will be taken offline in 3 days.";
    }

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
                        <td style='background:linear-gradient(135deg,#dc2626,#b91c1c);color:#ffffff;padding:40px;text-align:center;'>
                            <div style='width:60px;height:60px;background:#ffffff;border-radius:50%;margin:0 auto 15px;display:flex;align-items:center;justify-content:center;'>
                                <span style='font-size:28px;color:#dc2626;'>&#9888;</span>
                            </div>
                            <h1 style='margin:0;font-size:24px;'>{$title}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:40px;'>
                            <p style='font-size:16px;color:#ffffff;'>{$greeting}</p>
                            <p style='font-size:16px;color:#cccccc;line-height:1.6;'>{$message}</p>

                            <div style='background:#0a0a0a;border-radius:12px;padding:25px;margin:25px 0;text-align:center;'>
                                <p style='color:#888;margin:0 0 5px;font-size:14px;'>{$priceLabel}</p>
                                <p style='color:#ffffff;margin:0;font-size:36px;font-weight:bold;'>€{$price}<span style='font-size:16px;color:#888;'>/maand</span></p>
                            </div>

                            <div style='background:#7f1d1d;border-radius:8px;padding:15px;margin:20px 0;'>
                                <p style='color:#fca5a5;margin:0;font-size:14px;'>⚠️ {$warning}</p>
                            </div>

                            <p style='text-align:center;margin:30px 0;'>
                                <a href='{$subscriptionUrl}' style='display:inline-block;background:#f59e0b;color:#000000;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;'>
                                    {$buttonText} →
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style='background:#0a0a0a;padding:20px;text-align:center;color:#666;font-size:12px;border-top:1px solid #333;'>
                            <p style='margin:0;'>© 2026 GlamourSchedule - Beauty & Wellness Bookings</p>
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

/**
 * Generate deactivation email HTML
 */
function getDeactivationEmailHtml(array $business): string
{
    $companyName = htmlspecialchars($business['company_name']);
    $lang = $business['language'] ?? 'nl';

    $subscriptionUrl = "https://glamourschedule.com/business/subscription";

    if ($lang === 'nl') {
        $title = "Je account is gedeactiveerd";
        $greeting = "Beste {$companyName},";
        $message = "Je proefperiode is verlopen en je account is nu gedeactiveerd. Je salonpagina is niet meer zichtbaar voor klanten.";
        $reactivate = "Je kunt je account op elk moment opnieuw activeren door een abonnement te nemen:";
        $buttonText = "Heractiveer mijn salon";
    } else {
        $title = "Your account has been deactivated";
        $greeting = "Dear {$companyName},";
        $message = "Your trial period has expired and your account has been deactivated. Your salon page is no longer visible to customers.";
        $reactivate = "You can reactivate your account at any time by subscribing:";
        $buttonText = "Reactivate my salon";
    }

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
                        <td style='background:#374151;color:#ffffff;padding:40px;text-align:center;'>
                            <h1 style='margin:0;font-size:24px;'>{$title}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding:40px;'>
                            <p style='font-size:16px;color:#ffffff;'>{$greeting}</p>
                            <p style='font-size:16px;color:#cccccc;line-height:1.6;'>{$message}</p>
                            <p style='font-size:16px;color:#cccccc;line-height:1.6;'>{$reactivate}</p>

                            <p style='text-align:center;margin:30px 0;'>
                                <a href='{$subscriptionUrl}' style='display:inline-block;background:#f59e0b;color:#000000;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;'>
                                    {$buttonText} →
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style='background:#0a0a0a;padding:20px;text-align:center;color:#666;font-size:12px;border-top:1px solid #333;'>
                            <p style='margin:0;'>© 2026 GlamourSchedule - Beauty & Wellness Bookings</p>
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

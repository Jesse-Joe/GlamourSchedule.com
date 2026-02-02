<?php
/**
 * Cron Job: QR Payouts
 *
 * Processes automatic payouts 24 hours after QR check-in.
 * For businesses with Mollie Connect, split payments route funds directly.
 *
 * Run this script hourly via cron:
 * 0 * * * * /usr/bin/php /var/www/glamourschedule/cron/qr-payouts.php
 *
 * Or call the endpoint:
 * 0 * * * * curl -s "https://glamourschedule.com/cron/qr-payouts?key=glamour-cron-2024-secret"
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
$logFile = BASE_PATH . '/storage/logs/cron-qr-payouts.log';

function logMessage(string $message): void
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    echo "[$timestamp] $message\n";
}

// Platform fee per booking
const PLATFORM_FEE = 1.75;

// Hours after QR check-in before payout
const PAYOUT_DELAY_HOURS = 24;

try {
    $db = new Database($config['database']);

    logMessage("=== QR PAYOUT PROCESSING START ===");

    // Find bookings that:
    // 1. Are checked in (QR scanned)
    // 2. Were checked in more than 24 hours ago
    // 3. Have payout_status = 'pending'
    // 4. Business has Mollie Connect (split payment)
    $stmt = $db->query(
        "SELECT b.*,
                biz.id as biz_id,
                biz.company_name,
                biz.email as business_email,
                biz.mollie_account_id,
                biz.mollie_onboarding_status,
                s.name as service_name
         FROM bookings b
         JOIN businesses biz ON b.business_id = biz.id
         JOIN services s ON b.service_id = s.id
         WHERE b.status = 'checked_in'
           AND b.checked_in_at <= DATE_SUB(NOW(), INTERVAL ? HOUR)
           AND b.payout_status = 'pending'
           AND b.payment_status = 'paid'
           AND biz.mollie_account_id IS NOT NULL
           AND biz.mollie_onboarding_status = 'completed'
         ORDER BY b.checked_in_at ASC",
        [PAYOUT_DELAY_HOURS]
    );
    $bookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    if (empty($bookings)) {
        logMessage("No QR payouts to process");
        exit(0);
    }

    logMessage("Found " . count($bookings) . " bookings to process");

    $processed = 0;
    $failed = 0;

    foreach ($bookings as $booking) {
        try {
            // Calculate payout amount
            $totalAmount = (float)$booking['total_price'];
            $platformFee = PLATFORM_FEE;
            $businessAmount = $totalAmount - $platformFee;

            // For Mollie split payments, funds are already routed to business
            // We just need to mark as completed and notify
            $db->query(
                "UPDATE bookings
                 SET payout_status = 'completed',
                     payout_amount = ?,
                     payout_date = CURDATE(),
                     platform_fee = ?
                 WHERE id = ?",
                [$businessAmount, $platformFee, $booking['id']]
            );

            // Send notification to business
            $mailer = new Mailer();

            $subject = "Uitbetaling voltooid - €" . number_format($businessAmount, 2, ',', '.');
            $totalFormatted = number_format($totalAmount, 2, ',', '.');
            $feeFormatted = number_format($platformFee, 2, ',', '.');
            $businessFormatted = number_format($businessAmount, 2, ',', '.');
            $checkinDate = date('d-m-Y H:i', strtotime($booking['checked_in_at']));

            $body = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#22c55e,#16a34a);padding:40px;text-align:center;color:#fff;">
                            <div style="font-size:48px;margin-bottom:10px;">💰</div>
                            <h1 style="margin:0;font-size:24px;">Automatische Uitbetaling</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;">Beste {$booking['company_name']},</p>
                            <p style="color:#cccccc;line-height:1.6;">
                                24 uur na de QR check-in is de uitbetaling voor boeking <strong>{$booking['booking_number']}</strong> automatisch verwerkt.
                            </p>

                            <div style="background:#0a0a0a;border-radius:12px;padding:25px;margin:25px 0;">
                                <p style="color:#888;margin:0 0 15px;font-size:14px;">📱 Check-in: {$checkinDate}</p>
                                <table width="100%" style="color:#ffffff;">
                                    <tr>
                                        <td style="padding:8px 0;border-bottom:1px solid #333;">Dienst: {$booking['service_name']}</td>
                                        <td style="padding:8px 0;border-bottom:1px solid #333;text-align:right;">€{$totalFormatted}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;border-bottom:1px solid #333;color:#dc2626;">Platformkosten</td>
                                        <td style="padding:8px 0;border-bottom:1px solid #333;text-align:right;color:#dc2626;">-€{$feeFormatted}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;font-weight:bold;font-size:18px;">Jouw uitbetaling</td>
                                        <td style="padding:12px 0;text-align:right;font-weight:bold;font-size:18px;color:#22c55e;">€{$businessFormatted}</td>
                                    </tr>
                                </table>
                            </div>

                            <div style="background:#14532d;border-radius:8px;padding:15px;margin:20px 0;">
                                <p style="color:#bbf7d0;margin:0;font-size:14px;">
                                    ✓ Het bedrag is direct naar je gekoppelde Mollie account overgemaakt.
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#666;font-size:12px;">© 2026 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

            $mailer->send($booking['business_email'], $subject, $body);

            $processed++;
            logMessage("SUCCESS: {$booking['booking_number']} - €{$businessFormatted} to {$booking['company_name']}");

        } catch (Exception $e) {
            $failed++;
            logMessage("FAILED: {$booking['booking_number']} - " . $e->getMessage());
        }
    }

    logMessage("=== QR PAYOUT COMPLETE: Processed: $processed, Failed: $failed ===");

} catch (Exception $e) {
    logMessage("Error: " . $e->getMessage());
    exit(1);
}

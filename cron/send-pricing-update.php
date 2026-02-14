#!/usr/bin/env php
<?php
/**
 * Eenmalig script: Stuur pricing update email naar salons
 * Over de nieuwe eerste 25 boekingen fee, daarna gratis structuur.
 *
 * Gebruik: php send-pricing-update.php [--test=email@example.com] [--send-all]
 */

define('GLAMOUR_LOADED', true);
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

$config = require BASE_PATH . '/config/config.php';

// Database connectie
$db = new PDO(
    "mysql:host={$config['database']['host']};dbname={$config['database']['name']};charset=utf8mb4",
    $config['database']['user'],
    $config['database']['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Parse argumenten
$testEmail = null;
$sendAll = false;
foreach ($argv as $arg) {
    if (strpos($arg, '--test=') === 0) {
        $testEmail = substr($arg, 7);
    }
    if ($arg === '--send-all') {
        $sendAll = true;
    }
}

if (!$testEmail && !$sendAll) {
    echo "Gebruik:\n";
    echo "  php send-pricing-update.php --test=email@example.com   (test naar 1 adres)\n";
    echo "  php send-pricing-update.php --send-all                  (stuur naar alle actieve salons)\n";
    exit(1);
}

// Mailer initialiseren
$mailer = new GlamourSchedule\Core\Mailer('nl');

/**
 * Genereer de HTML email
 */
function getPricingUpdateEmail(string $companyName): string
{
    $dashboardUrl = 'https://glamourschedule.com/business/dashboard';

    return <<<HTML
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
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Goed nieuws voor je salon!</h1>
                            <p style="margin:10px 0 0;opacity:0.9;">GlamourSchedule - Prijswijziging</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:30px;color:#333333;font-size:15px;line-height:1.7;">
                            <p><strong>Beste {$companyName},</strong></p>

                            <p>We hebben goed nieuws! We hebben onze transactiekosten aangepast om het nog voordeliger te maken voor jouw salon.</p>

                            <!-- Highlight box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;">
                                <tr>
                                    <td style="background:#f0fdf4;border:2px solid #22c55e;border-radius:12px;padding:20px;">
                                        <h3 style="margin:0 0 10px;color:#16a34a;font-size:18px;">Wat verandert er?</h3>
                                        <p style="margin:0 0 8px;color:#333333;font-size:15px;">
                                            <strong>Eerste 25 boekingen per maand:</strong> transactiefee van &euro;1,75 per boeking
                                        </p>
                                        <p style="margin:0 0 8px;color:#333333;font-size:15px;">
                                            <strong>Vanaf boeking 26:</strong> elke boeking die maand is <strong style="color:#16a34a;">GRATIS</strong>
                                        </p>
                                        <p style="margin:0;color:#333333;font-size:15px;">
                                            <strong>Geen boekingen?</strong> Dan betaal je niets!
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p><strong>Hoe werkt het?</strong></p>
                            <ul style="padding-left:20px;color:#333333;">
                                <li style="margin-bottom:8px;">De teller begint elke maand opnieuw bij 0</li>
                                <li style="margin-bottom:8px;">Alleen online boekingen via het platform tellen mee</li>
                                <li style="margin-bottom:8px;">Geannuleerde boekingen worden niet meegeteld</li>
                                <li style="margin-bottom:8px;">Je ziet altijd je huidige stand in je dashboard</li>
                            </ul>

                            <p>Deze wijziging gaat <strong>per direct</strong> in. Je hoeft zelf niets te doen.</p>

                            <p>Heb je vragen? Neem gerust contact met ons op via <a href="mailto:support@glamourschedule.com" style="color:#000000;">support@glamourschedule.com</a></p>

                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$dashboardUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Ga naar je Dashboard
                                </a>
                            </p>

                            <p style="color:#666666;font-size:13px;">Met vriendelijke groet,<br><strong>Team GlamourSchedule</strong></p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="email-footer" style="background:#f5f5f5;padding:20px;text-align:center;border-top:1px solid #e0e0e0;">
                            <p class="email-footer-text" style="margin:0;color:#666666;font-size:12px;">&copy; 2026 GlamourSchedule - Transparante prijzen, geen verrassingen.</p>
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

$subject = 'Goed nieuws: na 25 boekingen per maand geen transactiekosten meer!';

// === TEST MODE ===
if ($testEmail) {
    echo "Test-email versturen naar: {$testEmail}\n";
    $html = getPricingUpdateEmail('Test Salon');
    $result = $mailer->send($testEmail, $subject, $html);
    echo $result ? "Verstuurd!\n" : "FOUT: email kon niet verstuurd worden.\n";
    exit($result ? 0 : 1);
}

// === SEND ALL ===
if ($sendAll) {
    $stmt = $db->query(
        "SELECT id, company_name, email FROM businesses WHERE status = 'active'"
    );
    $businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Aantal salons: " . count($businesses) . "\n";

    $success = 0;
    $failed = 0;

    foreach ($businesses as $biz) {
        $html = getPricingUpdateEmail(htmlspecialchars($biz['company_name'], ENT_QUOTES, 'UTF-8'));
        echo "Verzenden naar {$biz['company_name']} ({$biz['email']})... ";

        if ($mailer->send($biz['email'], $subject, $html)) {
            echo "OK\n";
            $success++;
        } else {
            echo "FOUT\n";
            $failed++;
        }

        // 200ms pauze tussen emails
        usleep(200000);
    }

    echo "\nKlaar! Verstuurd: {$success}, Mislukt: {$failed}\n";
}

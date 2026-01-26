<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;
use GlamourSchedule\Services\BunqService;
use GlamourSchedule\Services\InvoiceService;

class CronController extends Controller
{
    private const CRON_SECRET = 'glamour-cron-2024-secret';

    /**
     * Check for expired trials and send warning emails
     * Run daily: /cron/trial-expiry?key=glamour-cron-2024-secret
     */
    public function trialExpiry(): string
    {
        // Verify cron secret
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $this->logCron('Starting trial expiry check');

        try {
            // Find businesses where trial ends today (send warning email)
            $stmt = $this->db->query(
                "SELECT b.id, b.company_name, b.email, b.trial_ends_at, b.subscription_price,
                        b.is_early_adopter, b.language, u.first_name
                 FROM businesses b
                 JOIN users u ON b.user_id = u.id
                 WHERE b.subscription_status = 'trial'
                   AND b.trial_ends_at = CURDATE()
                   AND b.trial_warning_sent_at IS NULL"
            );
            $businesses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $emailsSent = 0;
            foreach ($businesses as $business) {
                $this->sendTrialExpiryEmail($business);

                // Mark warning as sent
                $this->db->query(
                    "UPDATE businesses SET trial_warning_sent_at = NOW() WHERE id = ?",
                    [$business['id']]
                );

                $emailsSent++;
                $this->logCron("Trial expiry email sent to: {$business['email']}");
            }

            $this->logCron("Trial expiry check complete. Emails sent: $emailsSent");

            return json_encode([
                'success' => true,
                'emails_sent' => $emailsSent,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            $this->logCron("Error: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Deactivate businesses 3 days after trial expiry warning
     * Run daily: /cron/deactivate-expired?key=glamour-cron-2024-secret
     */
    public function deactivateExpired(): string
    {
        // Verify cron secret
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $this->logCron('Starting deactivation check');

        try {
            // Find businesses where trial ended 3 days ago and still on trial status
            $stmt = $this->db->query(
                "SELECT b.id, b.company_name, b.email, b.trial_ends_at, b.language,
                        u.first_name
                 FROM businesses b
                 JOIN users u ON b.user_id = u.id
                 WHERE b.subscription_status = 'trial'
                   AND b.trial_warning_sent_at IS NOT NULL
                   AND b.trial_ends_at <= DATE_SUB(CURDATE(), INTERVAL 3 DAY)"
            );
            $businesses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $deactivated = 0;
            foreach ($businesses as $business) {
                // Deactivate business
                $this->db->query(
                    "UPDATE businesses SET subscription_status = 'expired', status = 'inactive' WHERE id = ?",
                    [$business['id']]
                );

                // Send deactivation email
                $this->sendDeactivationEmail($business);

                $deactivated++;
                $this->logCron("Business deactivated: {$business['company_name']} ({$business['email']})");
            }

            $this->logCron("Deactivation check complete. Businesses deactivated: $deactivated");

            return json_encode([
                'success' => true,
                'deactivated' => $deactivated,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            $this->logCron("Error: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    private function sendTrialExpiryEmail(array $business): void
    {
        $lang = $business['language'] ?? 'nl';
        $mailer = new Mailer($lang);
        $isEarlyAdopter = !empty($business['is_early_adopter']);
        $price = number_format($business['subscription_price'], 2, ',', '.');

        // Translations
        $translations = $this->getTrialExpiryTranslations($lang, $isEarlyAdopter);

        $subject = $translations['subject'];
        $priceLabel = $translations['price_label'];
        $priceSubtext = $translations['price_subtext'];
        $activateText = $translations['activate_text'];
        $greeting = $translations['greeting'];
        $trialEndsText = str_replace(
            ['{company}'],
            [$business['company_name']],
            $translations['trial_ends']
        );
        $warningText = $translations['warning'];
        $buttonText = $translations['button'];
        $questionsText = $translations['questions'];
        $copyrightText = $translations['copyright'];

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #000000; padding: 30px; text-align: center;'>
                <h1 style='color: #ffffff; margin: 0;'>GlamourSchedule</h1>
            </div>

            <div style='padding: 30px; background: #ffffff;'>
                <h2 style='color: #333333;'>{$greeting} {$business['first_name']},</h2>

                <p style='color: #666666; line-height: 1.6;'>
                    {$trialEndsText}
                </p>

                <p style='color: #666666; line-height: 1.6;'>
                    {$activateText}
                </p>

                <div style='background: #f5f5f5; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>
                    <p style='margin: 0 0 10px 0; color: #333333;'>{$priceLabel}</p>
                    <p style='font-size: 2rem; font-weight: bold; color: #000000; margin: 0;'>
                        &euro;{$price}
                    </p>
                    <p style='margin: 10px 0 0 0; color: #666666; font-size: 0.9rem;'>{$priceSubtext}</p>
                </div>

                <p style='color: #e74c3c; font-weight: bold;'>
                    {$warningText}
                </p>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://glamourschedule.nl/business/dashboard'
                       style='display: inline-block; background: #000000; color: #ffffff;
                              padding: 15px 30px; text-decoration: none; border-radius: 25px;
                              font-weight: bold;'>
                        {$buttonText}
                    </a>
                </div>

                <p style='color: #999999; font-size: 0.9rem;'>
                    {$questionsText}
                </p>
            </div>

            <div style='background: #f5f5f5; padding: 20px; text-align: center;'>
                <p style='color: #999999; font-size: 0.8rem; margin: 0;'>
                    {$copyrightText}
                </p>
            </div>
        </div>
        ";

        $mailer->send($business['email'], $subject, $body);
    }

    private function getTrialExpiryTranslations(string $lang, bool $isEarlyAdopter): array
    {
        $translations = [
            'nl' => [
                'subject' => 'Je proefperiode bij GlamourSchedule eindigt vandaag',
                'greeting' => 'Hallo',
                'trial_ends' => 'Je 14-daagse proefperiode voor <strong>{company}</strong> eindigt vandaag.',
                'price_label_early' => 'Early Bird aanmeldkosten',
                'price_label_normal' => 'Maandelijks abonnement',
                'price_subtext_early' => 'eenmalig',
                'price_subtext_normal' => 'per maand',
                'activate_text_early' => 'Om verder gebruik te maken van GlamourSchedule verzoeken wij je om je Early Bird aanmelding af te ronden.',
                'activate_text_normal' => 'Om verder gebruik te maken van GlamourSchedule verzoeken wij je om het maandelijkse abonnement te activeren.',
                'warning' => 'Let op: Als je niet binnen 3 dagen activeert, wordt je account gedeactiveerd.',
                'button' => 'Abonnement Activeren',
                'questions' => 'Heb je vragen? Neem contact op via info@glamourschedule.nl',
                'copyright' => '&copy; ' . date('Y') . ' GlamourSchedule. Alle rechten voorbehouden.',
            ],
            'en' => [
                'subject' => 'Your GlamourSchedule trial ends today',
                'greeting' => 'Hello',
                'trial_ends' => 'Your 14-day trial for <strong>{company}</strong> ends today.',
                'price_label_early' => 'Early Bird registration fee',
                'price_label_normal' => 'Monthly subscription',
                'price_subtext_early' => 'one-time',
                'price_subtext_normal' => 'per month',
                'activate_text_early' => 'To continue using GlamourSchedule, please complete your Early Bird registration.',
                'activate_text_normal' => 'To continue using GlamourSchedule, please activate your monthly subscription.',
                'warning' => 'Note: If you don\'t activate within 3 days, your account will be deactivated.',
                'button' => 'Activate Subscription',
                'questions' => 'Questions? Contact us at info@glamourschedule.nl',
                'copyright' => '&copy; ' . date('Y') . ' GlamourSchedule. All rights reserved.',
            ],
            'de' => [
                'subject' => 'Ihre GlamourSchedule Testphase endet heute',
                'greeting' => 'Hallo',
                'trial_ends' => 'Ihre 14-tägige Testphase für <strong>{company}</strong> endet heute.',
                'price_label_early' => 'Early Bird Anmeldegebühr',
                'price_label_normal' => 'Monatliches Abonnement',
                'price_subtext_early' => 'einmalig',
                'price_subtext_normal' => 'pro Monat',
                'activate_text_early' => 'Um GlamourSchedule weiterhin zu nutzen, schließen Sie bitte Ihre Early Bird Anmeldung ab.',
                'activate_text_normal' => 'Um GlamourSchedule weiterhin zu nutzen, aktivieren Sie bitte Ihr monatliches Abonnement.',
                'warning' => 'Hinweis: Wenn Sie nicht innerhalb von 3 Tagen aktivieren, wird Ihr Konto deaktiviert.',
                'button' => 'Abonnement Aktivieren',
                'questions' => 'Fragen? Kontaktieren Sie uns unter info@glamourschedule.nl',
                'copyright' => '&copy; ' . date('Y') . ' GlamourSchedule. Alle Rechte vorbehalten.',
            ],
            'fr' => [
                'subject' => 'Votre période d\'essai GlamourSchedule se termine aujourd\'hui',
                'greeting' => 'Bonjour',
                'trial_ends' => 'Votre période d\'essai de 14 jours pour <strong>{company}</strong> se termine aujourd\'hui.',
                'price_label_early' => 'Frais d\'inscription Early Bird',
                'price_label_normal' => 'Abonnement mensuel',
                'price_subtext_early' => 'unique',
                'price_subtext_normal' => 'par mois',
                'activate_text_early' => 'Pour continuer à utiliser GlamourSchedule, veuillez finaliser votre inscription Early Bird.',
                'activate_text_normal' => 'Pour continuer à utiliser GlamourSchedule, veuillez activer votre abonnement mensuel.',
                'warning' => 'Attention: Si vous n\'activez pas dans les 3 jours, votre compte sera désactivé.',
                'button' => 'Activer l\'Abonnement',
                'questions' => 'Des questions? Contactez-nous à info@glamourschedule.nl',
                'copyright' => '&copy; ' . date('Y') . ' GlamourSchedule. Tous droits réservés.',
            ],
        ];

        $t = $translations[$lang] ?? $translations['nl'];

        return [
            'subject' => $t['subject'],
            'greeting' => $t['greeting'],
            'trial_ends' => $t['trial_ends'],
            'price_label' => $isEarlyAdopter ? $t['price_label_early'] : $t['price_label_normal'],
            'price_subtext' => $isEarlyAdopter ? $t['price_subtext_early'] : $t['price_subtext_normal'],
            'activate_text' => $isEarlyAdopter ? $t['activate_text_early'] : $t['activate_text_normal'],
            'warning' => $t['warning'],
            'button' => $t['button'],
            'questions' => $t['questions'],
            'copyright' => $t['copyright'],
        ];
    }

    private function sendDeactivationEmail(array $business): void
    {
        $lang = $business['language'] ?? 'nl';
        $mailer = new Mailer($lang);

        $t = $this->getDeactivationTranslations($lang);
        $subject = $t['subject'];

        $deactivatedText = str_replace('{company}', $business['company_name'], $t['deactivated']);

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #000000; padding: 30px; text-align: center;'>
                <h1 style='color: #ffffff; margin: 0;'>GlamourSchedule</h1>
            </div>

            <div style='padding: 30px; background: #ffffff;'>
                <h2 style='color: #333333;'>{$t['greeting']} {$business['first_name']},</h2>

                <p style='color: #666666; line-height: 1.6;'>
                    {$deactivatedText}
                </p>

                <p style='color: #666666; line-height: 1.6;'>
                    {$t['reactivate_info']}
                </p>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://glamourschedule.nl/business/login'
                       style='display: inline-block; background: #000000; color: #ffffff;
                              padding: 15px 30px; text-decoration: none; border-radius: 25px;
                              font-weight: bold;'>
                        {$t['button']}
                    </a>
                </div>

                <p style='color: #999999; font-size: 0.9rem;'>
                    {$t['questions']}
                </p>
            </div>

            <div style='background: #f5f5f5; padding: 20px; text-align: center;'>
                <p style='color: #999999; font-size: 0.8rem; margin: 0;'>
                    {$t['copyright']}
                </p>
            </div>
        </div>
        ";

        $mailer->send($business['email'], $subject, $body);
    }

    private function getDeactivationTranslations(string $lang): array
    {
        $translations = [
            'nl' => [
                'subject' => 'Je GlamourSchedule account is gedeactiveerd',
                'greeting' => 'Hallo',
                'deactivated' => 'Je account voor <strong>{company}</strong> is gedeactiveerd omdat de proefperiode is verlopen zonder activatie van het abonnement.',
                'reactivate_info' => 'Je kunt je account op elk moment opnieuw activeren door in te loggen en het abonnement te activeren.',
                'button' => 'Opnieuw Activeren',
                'questions' => 'Heb je vragen? Neem contact op via info@glamourschedule.nl',
                'copyright' => '&copy; ' . date('Y') . ' GlamourSchedule. Alle rechten voorbehouden.',
            ],
            'en' => [
                'subject' => 'Your GlamourSchedule account has been deactivated',
                'greeting' => 'Hello',
                'deactivated' => 'Your account for <strong>{company}</strong> has been deactivated because the trial period expired without subscription activation.',
                'reactivate_info' => 'You can reactivate your account at any time by logging in and activating your subscription.',
                'button' => 'Reactivate Account',
                'questions' => 'Questions? Contact us at info@glamourschedule.nl',
                'copyright' => '&copy; ' . date('Y') . ' GlamourSchedule. All rights reserved.',
            ],
            'de' => [
                'subject' => 'Ihr GlamourSchedule Konto wurde deaktiviert',
                'greeting' => 'Hallo',
                'deactivated' => 'Ihr Konto für <strong>{company}</strong> wurde deaktiviert, da die Testphase ohne Abonnement-Aktivierung abgelaufen ist.',
                'reactivate_info' => 'Sie können Ihr Konto jederzeit reaktivieren, indem Sie sich anmelden und Ihr Abonnement aktivieren.',
                'button' => 'Konto Reaktivieren',
                'questions' => 'Fragen? Kontaktieren Sie uns unter info@glamourschedule.nl',
                'copyright' => '&copy; ' . date('Y') . ' GlamourSchedule. Alle Rechte vorbehalten.',
            ],
            'fr' => [
                'subject' => 'Votre compte GlamourSchedule a été désactivé',
                'greeting' => 'Bonjour',
                'deactivated' => 'Votre compte pour <strong>{company}</strong> a été désactivé car la période d\'essai a expiré sans activation de l\'abonnement.',
                'reactivate_info' => 'Vous pouvez réactiver votre compte à tout moment en vous connectant et en activant votre abonnement.',
                'button' => 'Réactiver le Compte',
                'questions' => 'Des questions? Contactez-nous à info@glamourschedule.nl',
                'copyright' => '&copy; ' . date('Y') . ' GlamourSchedule. Tous droits réservés.',
            ],
        ];

        return $translations[$lang] ?? $translations['nl'];
    }

    private function logCron(string $message): void
    {
        $logFile = BASE_PATH . '/storage/logs/cron-trial-expiry.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    // Platform fee per booking
    private const PLATFORM_FEE = 1.75;

    /**
     * Process automatic payouts to businesses for completed bookings
     * Runs hourly: /cron/process-payouts?key=glamour-cron-2024-secret
     *
     * Requirements:
     * - Booking status = 'completed' (QR scanned)
     * - 24 hours have passed since completion
     * - Business has Mollie Connect linked
     */
    public function processPayouts(): string
    {
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $this->logPayout('Starting automatic payout processing');

        try {
            // Find checked-in bookings older than 24 hours that haven't been paid out
            // Business must have Mollie Connect linked
            // Status = 'checked_in' means QR code was scanned
            $stmt = $this->db->query(
                "SELECT b.*,
                        bus.id as business_id,
                        bus.company_name,
                        bus.email as business_email,
                        bus.iban as business_iban,
                        bus.mollie_account_id,
                        bus.mollie_access_token,
                        s.name as service_name
                 FROM bookings b
                 JOIN businesses bus ON b.business_id = bus.id
                 JOIN services s ON b.service_id = s.id
                 WHERE b.status = 'checked_in'
                   AND b.payout_status = 'pending'
                   AND b.payment_status = 'paid'
                   AND b.checked_in_at <= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 ORDER BY b.business_id, b.created_at"
            );
            $bookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($bookings)) {
                $this->logPayout('No bookings to process');
                return json_encode([
                    'success' => true,
                    'message' => 'No payouts to process',
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            }

            // Group bookings by business
            $businessPayouts = [];
            foreach ($bookings as $booking) {
                $businessId = $booking['business_id'];
                if (!isset($businessPayouts[$businessId])) {
                    $businessPayouts[$businessId] = [
                        'business_id' => $businessId,
                        'company_name' => $booking['company_name'],
                        'email' => $booking['business_email'],
                        'iban' => $booking['business_iban'],
                        'mollie_account_id' => $booking['mollie_account_id'],
                        'mollie_access_token' => $booking['mollie_access_token'],
                        'bookings' => [],
                        'total_service_amount' => 0,
                        'total_platform_fee' => 0,
                        'total_payout' => 0
                    ];
                }

                // Calculate payout: service price - €1.75 platform fee
                $servicePrice = (float) $booking['service_price'];
                $platformFee = self::PLATFORM_FEE;
                $payoutAmount = $servicePrice - $platformFee;

                $businessPayouts[$businessId]['bookings'][] = [
                    'id' => $booking['id'],
                    'booking_number' => $booking['booking_number'],
                    'service_name' => $booking['service_name'],
                    'service_price' => $servicePrice,
                    'platform_fee' => $platformFee,
                    'payout_amount' => $payoutAmount
                ];
                $businessPayouts[$businessId]['total_service_amount'] += $servicePrice;
                $businessPayouts[$businessId]['total_platform_fee'] += $platformFee;
                $businessPayouts[$businessId]['total_payout'] += $payoutAmount;
            }

            $payoutsProcessed = 0;
            $payoutsFailed = 0;
            $totalAmount = 0;
            $results = [];

            foreach ($businessPayouts as $payout) {
                // Check if business has Mollie Connect linked
                if (empty($payout['mollie_account_id'])) {
                    $this->logPayout("Skipping {$payout['company_name']} - no Mollie Connect linked");
                    $results[] = [
                        'business' => $payout['company_name'],
                        'status' => 'skipped',
                        'reason' => 'No Mollie Connect'
                    ];
                    continue;
                }

                // Create payout record in database first
                $this->db->query(
                    "INSERT INTO business_payouts (business_id, amount, platform_fee, bookings_count, period_start, period_end, status, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, 'processing', NOW())",
                    [
                        $payout['business_id'],
                        $payout['total_payout'],
                        $payout['total_platform_fee'],
                        count($payout['bookings']),
                        date('Y-m-d', strtotime('-24 hours')),
                        date('Y-m-d')
                    ]
                );
                $payoutId = $this->db->lastInsertId();

                // Execute Mollie Connect transfer
                $transferResult = $this->executeMollieTransfer($payout, $payoutId);

                if ($transferResult['success']) {
                    // Update payout record with Mollie transfer ID
                    $this->db->query(
                        "UPDATE business_payouts SET mollie_transfer_id = ?, status = 'completed', payout_date = CURDATE() WHERE id = ?",
                        [$transferResult['transfer_id'], $payoutId]
                    );

                    // Update booking payout status
                    $bookingIds = array_column($payout['bookings'], 'id');
                    $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));
                    $this->db->query(
                        "UPDATE bookings SET payout_status = 'completed', payout_amount = ?, payout_date = CURDATE() WHERE id IN ($placeholders)",
                        array_merge([$payout['total_payout'] / count($bookingIds)], $bookingIds)
                    );

                    // Send confirmation email to business
                    $this->sendBusinessPayoutEmail($payout, $payoutId);

                    $payoutsProcessed++;
                    $totalAmount += $payout['total_payout'];

                    $this->logPayout("SUCCESS: Payout to {$payout['company_name']}: €" . number_format($payout['total_payout'], 2) . " (Transfer: {$transferResult['transfer_id']})");

                    $results[] = [
                        'business' => $payout['company_name'],
                        'status' => 'success',
                        'amount' => $payout['total_payout'],
                        'transfer_id' => $transferResult['transfer_id']
                    ];
                } else {
                    // Update payout as failed
                    $this->db->query(
                        "UPDATE business_payouts SET status = 'failed', notes = ? WHERE id = ?",
                        [$transferResult['error'], $payoutId]
                    );

                    $payoutsFailed++;
                    $this->logPayout("FAILED: Payout to {$payout['company_name']}: " . $transferResult['error']);

                    $results[] = [
                        'business' => $payout['company_name'],
                        'status' => 'failed',
                        'error' => $transferResult['error']
                    ];
                }
            }

            // Send admin notification
            if ($payoutsProcessed > 0 || $payoutsFailed > 0) {
                $this->sendAdminPayoutNotification($businessPayouts, $totalAmount, $payoutsFailed);
            }

            $this->logPayout("Payout processing complete. Success: $payoutsProcessed, Failed: $payoutsFailed, Total: €" . number_format($totalAmount, 2));

            return json_encode([
                'success' => true,
                'payouts_processed' => $payoutsProcessed,
                'payouts_failed' => $payoutsFailed,
                'total_amount' => $totalAmount,
                'results' => $results,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            $this->logPayout("Error: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Execute Mollie Connect transfer to business
     */
    private function executeMollieTransfer(array $payout, int $payoutId): array
    {
        $apiKey = getenv('MOLLIE_API_KEY');

        // Build description with booking numbers
        $bookingNumbers = array_column($payout['bookings'], 'booking_number');
        $description = "GlamourSchedule uitbetaling #$payoutId - " . implode(', ', array_slice($bookingNumbers, 0, 3));
        if (count($bookingNumbers) > 3) {
            $description .= ' (+' . (count($bookingNumbers) - 3) . ' meer)';
        }

        // Create transfer to connected account
        $transferData = [
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format($payout['total_payout'], 2, '.', '')
            ],
            'description' => $description,
            'destination' => [
                'type' => 'organization',
                'organizationId' => $payout['mollie_account_id']
            ],
            'metadata' => [
                'payout_id' => $payoutId,
                'business_id' => $payout['business_id'],
                'bookings_count' => count($payout['bookings'])
            ]
        ];

        $ch = curl_init('https://api.mollie.com/v2/transfers');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($transferData),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300 && isset($result['id'])) {
            return [
                'success' => true,
                'transfer_id' => $result['id']
            ];
        }

        // Log the full error for debugging
        $this->logPayout("Mollie Transfer Error: HTTP $httpCode - $response");

        return [
            'success' => false,
            'error' => $result['detail'] ?? $result['title'] ?? 'Unknown Mollie error (HTTP ' . $httpCode . ')'
        ];
    }

    /**
     * Mark payouts as completed (called after manual Mollie transfer)
     * /cron/complete-payouts?key=glamour-cron-2024-secret&payout_id=123
     */
    public function completePayouts(): string
    {
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $payoutId = $_GET['payout_id'] ?? null;

        if ($payoutId) {
            // Complete specific payout
            $this->db->query(
                "UPDATE business_payouts SET status = 'completed', payout_date = CURDATE() WHERE id = ?",
                [$payoutId]
            );

            // Get business_id for this payout
            $stmt = $this->db->query("SELECT business_id FROM business_payouts WHERE id = ?", [$payoutId]);
            $payout = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($payout) {
                // Update all processing bookings for this business to completed
                $this->db->query(
                    "UPDATE bookings SET payout_status = 'completed' WHERE business_id = ? AND payout_status = 'processing'",
                    [$payout['business_id']]
                );
            }

            return json_encode(['success' => true, 'payout_id' => $payoutId]);
        }

        // Complete all processing payouts
        $this->db->query("UPDATE business_payouts SET status = 'completed', payout_date = CURDATE() WHERE status = 'processing'");
        $this->db->query("UPDATE bookings SET payout_status = 'completed' WHERE payout_status = 'processing'");

        return json_encode(['success' => true, 'message' => 'All processing payouts marked as completed']);
    }

    /**
     * Process weekly payouts for businesses WITHOUT Mollie Connect
     * Runs every Wednesday 08:00: /cron/weekly-payouts?key=glamour-cron-2024-secret
     *
     * Schedule rationale:
     * - Mon-Sun: Bookings completed (QR scanned)
     * - Monday: Mollie processes previous week
     * - Tuesday: Mollie pays out to Bunq
     * - Wednesday: We pay salons from Bunq (balance confirmed)
     */
    public function weeklyPayouts(): string
    {
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $this->logPayout('=== WEEKLY PAYOUT START ===');
        $this->logPayout('Processing payouts for week: ' . date('Y-W', strtotime('-1 week')));

        try {
            // Find checked-in bookings that haven't been paid out
            // Only for businesses WITHOUT Mollie Connect
            $stmt = $this->db->query(
                "SELECT b.*,
                        bus.id as business_id,
                        bus.company_name,
                        bus.email as business_email,
                        bus.iban as business_iban,
                        s.name as service_name
                 FROM bookings b
                 JOIN businesses bus ON b.business_id = bus.id
                 JOIN services s ON b.service_id = s.id
                 WHERE b.status = 'checked_in'
                   AND b.payout_status = 'pending'
                   AND b.payment_status = 'paid'
                   AND (bus.mollie_account_id IS NULL OR bus.mollie_account_id = '')
                   AND bus.iban IS NOT NULL AND bus.iban != ''
                 ORDER BY b.business_id, b.created_at"
            );
            $bookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($bookings)) {
                $this->logPayout('No weekly payouts to process');
                return json_encode([
                    'success' => true,
                    'message' => 'No weekly payouts to process',
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            }

            // Group bookings by business
            $businessPayouts = [];
            foreach ($bookings as $booking) {
                $businessId = $booking['business_id'];
                if (!isset($businessPayouts[$businessId])) {
                    $businessPayouts[$businessId] = [
                        'business_id' => $businessId,
                        'company_name' => $booking['company_name'],
                        'email' => $booking['business_email'],
                        'iban' => $booking['business_iban'],
                        'bookings' => [],
                        'total_service_amount' => 0,
                        'total_platform_fee' => 0,
                        'total_payout' => 0
                    ];
                }

                $servicePrice = (float) $booking['service_price'];
                $platformFee = self::PLATFORM_FEE;
                $payoutAmount = $servicePrice - $platformFee;

                $businessPayouts[$businessId]['bookings'][] = [
                    'id' => $booking['id'],
                    'booking_number' => $booking['booking_number'],
                    'service_name' => $booking['service_name'],
                    'service_price' => $servicePrice,
                    'platform_fee' => $platformFee,
                    'payout_amount' => $payoutAmount
                ];
                $businessPayouts[$businessId]['total_service_amount'] += $servicePrice;
                $businessPayouts[$businessId]['total_platform_fee'] += $platformFee;
                $businessPayouts[$businessId]['total_payout'] += $payoutAmount;
            }

            // Calculate total amount needed
            $totalNeeded = array_sum(array_column($businessPayouts, 'total_payout'));
            $this->logPayout("Total payout amount needed: €" . number_format($totalNeeded, 2));
            $this->logPayout("Number of businesses to pay: " . count($businessPayouts));

            // Check if Bunq is configured for automatic transfers
            $bunq = new BunqService();
            $useBunq = $bunq->isConfigured();

            if (!$useBunq) {
                $this->logPayout("WARNING: Bunq not configured - payouts will require manual processing");
            }

            // CRITICAL: Check Bunq balance before proceeding
            $bunqBalance = null;
            $sufficientFunds = false;

            if ($useBunq) {
                $bunqBalance = $bunq->getBalance();
                $this->logPayout("Bunq balance: €" . ($bunqBalance !== null ? number_format($bunqBalance, 2) : 'UNKNOWN'));

                if ($bunqBalance === null) {
                    // Cannot determine balance - abort automatic payments
                    $this->logPayout("ERROR: Could not retrieve Bunq balance - aborting automatic payments");
                    $this->sendBalanceErrorAlert($businessPayouts, $totalNeeded);
                    return json_encode([
                        'success' => false,
                        'error' => 'Could not retrieve Bunq balance',
                        'total_needed' => $totalNeeded,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]);
                }

                // Add 10% safety margin
                $requiredBalance = $totalNeeded * 1.10;
                $sufficientFunds = $bunqBalance >= $requiredBalance;

                if (!$sufficientFunds) {
                    $this->logPayout("ERROR: Insufficient funds - Need €" . number_format($requiredBalance, 2) . ", Have €" . number_format($bunqBalance, 2));
                    $this->sendInsufficientFundsAlert($businessPayouts, $totalNeeded, $bunqBalance);
                    return json_encode([
                        'success' => false,
                        'error' => 'Insufficient Bunq balance',
                        'balance' => $bunqBalance,
                        'required' => $requiredBalance,
                        'total_needed' => $totalNeeded,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]);
                }

                $this->logPayout("Balance check PASSED: €" . number_format($bunqBalance, 2) . " >= €" . number_format($requiredBalance, 2));
            }

            $payoutsProcessed = 0;
            $payoutsFailed = 0;
            $totalAmount = 0;
            $manualPayouts = [];

            foreach ($businessPayouts as &$payout) {
                // Create payout record
                $this->db->query(
                    "INSERT INTO business_payouts (business_id, amount, platform_fee, bookings_count, period_start, period_end, status, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, 'processing', NOW())",
                    [
                        $payout['business_id'],
                        $payout['total_payout'],
                        $payout['total_platform_fee'],
                        count($payout['bookings']),
                        date('Y-m-d', strtotime('-7 days')),
                        date('Y-m-d')
                    ]
                );
                $payoutId = $this->db->lastInsertId();
                $payout['payout_id'] = $payoutId;

                // Try Bunq automatic transfer
                if ($useBunq) {
                    $description = "GlamourSchedule uitbetaling #$payoutId";
                    $bunqResult = $bunq->makePayment(
                        $payout['iban'],
                        $payout['company_name'],
                        $payout['total_payout'],
                        $description
                    );

                    if ($bunqResult['success']) {
                        // Mark as completed
                        $this->db->query(
                            "UPDATE business_payouts SET status = 'completed', payout_date = CURDATE(), transaction_id = ?, notes = 'Automatisch via Bunq' WHERE id = ?",
                            [$bunqResult['payment_id'], $payoutId]
                        );

                        // Update bookings
                        $bookingIds = array_column($payout['bookings'], 'id');
                        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));
                        $this->db->query(
                            "UPDATE bookings SET payout_status = 'completed', payout_date = CURDATE() WHERE id IN ($placeholders)",
                            $bookingIds
                        );

                        // Send success email
                        $this->sendWeeklyPayoutEmail($payout, $payoutId, true);

                        $payoutsProcessed++;
                        $totalAmount += $payout['total_payout'];
                        $payout['bunq_status'] = 'success';

                        $this->logPayout("SUCCESS (Bunq): {$payout['company_name']} - €" . number_format($payout['total_payout'], 2) . " - Payment ID: {$bunqResult['payment_id']}");
                    } else {
                        // Mark as failed, add to manual list
                        $this->db->query(
                            "UPDATE business_payouts SET status = 'failed', notes = ? WHERE id = ?",
                            ['Bunq error: ' . $bunqResult['error'], $payoutId]
                        );

                        $payoutsFailed++;
                        $manualPayouts[] = $payout;
                        $payout['bunq_status'] = 'failed';
                        $payout['bunq_error'] = $bunqResult['error'];

                        $this->logPayout("FAILED (Bunq): {$payout['company_name']} - " . $bunqResult['error']);
                    }
                } else {
                    // No Bunq configured - mark for manual processing
                    $this->db->query(
                        "UPDATE business_payouts SET status = 'pending', notes = 'Handmatige overboeking vereist' WHERE id = ?",
                        [$payoutId]
                    );

                    // Update bookings to processing
                    $bookingIds = array_column($payout['bookings'], 'id');
                    $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));
                    $this->db->query(
                        "UPDATE bookings SET payout_status = 'processing' WHERE id IN ($placeholders)",
                        $bookingIds
                    );

                    $manualPayouts[] = $payout;
                    $totalAmount += $payout['total_payout'];
                    $payout['bunq_status'] = 'manual';

                    // Send notification email
                    $this->sendWeeklyPayoutEmail($payout, $payoutId, false);

                    $this->logPayout("MANUAL: {$payout['company_name']} - €" . number_format($payout['total_payout'], 2));
                }
            }

            // Send admin notification
            $this->sendWeeklyAdminNotification($businessPayouts, $totalAmount, $useBunq, count($manualPayouts));

            $this->logPayout("Weekly payout complete. Bunq: $payoutsProcessed success, $payoutsFailed failed. Manual: " . count($manualPayouts));

            return json_encode([
                'success' => true,
                'bunq_enabled' => $useBunq,
                'payouts_processed' => $payoutsProcessed,
                'payouts_failed' => $payoutsFailed,
                'manual_required' => count($manualPayouts),
                'total_amount' => $totalAmount,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            $this->logPayout("Error: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Send weekly payout notification to business
     */
    private function sendWeeklyPayoutEmail(array $payout, int $payoutId, bool $isAutomatic = false): void
    {
        $mailer = new Mailer();

        $bookingsList = '';
        foreach ($payout['bookings'] as $b) {
            $bookingsList .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #eee;'>{$b['booking_number']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;'>{$b['service_name']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>€" . number_format($b['payout_amount'], 2, ',', '.') . "</td>
            </tr>";
        }

        if ($isAutomatic) {
            $subject = "Uitbetaling voltooid - €" . number_format($payout['total_payout'], 2, ',', '.');
            $headerBg = '#22c55e';
            $headerText = 'Uitbetaling Voltooid';
            $statusBox = "
                <div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:20px;margin:20px 0;text-align:center;'>
                    <p style='margin:0;font-size:28px;font-weight:bold;color:#22c55e;'>€" . number_format($payout['total_payout'], 2, ',', '.') . "</p>
                    <p style='margin:5px 0 0;color:#166534;font-size:14px;'>" . count($payout['bookings']) . " boeking(en) - Automatisch overgemaakt</p>
                </div>";
            $statusMessage = "<p style='color:#166534;'>Het bedrag is automatisch overgemaakt naar je bankrekening. Je ontvangt het binnen 1 werkdag.</p>";
            $tipBox = "";
        } else {
            $subject = "Wekelijkse uitbetaling in verwerking - €" . number_format($payout['total_payout'], 2, ',', '.');
            $headerBg = '#000';
            $headerText = 'Wekelijkse Uitbetaling';
            $statusBox = "
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:20px;margin:20px 0;text-align:center;'>
                    <p style='margin:0;font-size:28px;font-weight:bold;color:#d97706;'>€" . number_format($payout['total_payout'], 2, ',', '.') . "</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:14px;'>" . count($payout['bookings']) . " boeking(en)</p>
                </div>";
            $statusMessage = "<p><strong>Verwachte verwerkingstijd:</strong> 1-3 werkdagen</p>";
            $tipBox = "
                <div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;margin:20px 0;'>
                    <p style='margin:0;font-size:14px;color:#166534;'>
                        <strong>Tip:</strong> Koppel je Mollie account om automatisch binnen 24 uur na elke boeking uitbetaald te worden!
                        <a href='https://glamourschedule.com/business/mollie/connect' style='color:#166534;'>Koppel nu →</a>
                    </p>
                </div>";
        }

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:{$headerBg};padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>{$headerText}</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <p>Beste {$payout['company_name']},</p>

                {$statusBox}

                <table style='width:100%;border-collapse:collapse;margin:20px 0;'>
                    <thead>
                        <tr style='background:#0a0a0a;'>
                            <th style='padding:10px;text-align:left;'>Boeking</th>
                            <th style='padding:10px;text-align:left;'>Service</th>
                            <th style='padding:10px;text-align:right;'>Bedrag</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$bookingsList}
                    </tbody>
                </table>

                <p><strong>Bankrekening:</strong> {$payout['iban']}</p>
                {$statusMessage}
                {$tipBox}
            </div>
            <div style='background:#0a0a0a;padding:15px;text-align:center;border-top:1px solid #333;'>
                <p style='margin:0;color:#999;font-size:12px;'>© " . date('Y') . " GlamourSchedule</p>
            </div>
        </div>";

        // Generate and attach invoice PDF
        try {
            $invoiceService = new InvoiceService();
            $invoicePath = $invoiceService->generateBusinessInvoice($payout);
            $invoiceName = 'GlamourSchedule-Factuur-' . date('Y-m-d') . '.pdf';
            $mailer->sendWithAttachment($payout['email'], $subject, $body, $invoicePath, $invoiceName);
        } catch (\Exception $e) {
            // Fallback to regular email if invoice generation fails
            error_log('Invoice generation failed for business payout: ' . $e->getMessage());
            $mailer->send($payout['email'], $subject, $body);
        }
    }

    /**
     * Send weekly admin notification
     */
    private function sendWeeklyAdminNotification(array $payouts, float $totalAmount, bool $bunqEnabled = false, int $manualCount = 0): void
    {
        $mailer = new Mailer();
        $adminEmail = 'jjt-services@outlook.com';

        $successCount = 0;
        $failedCount = 0;
        $successAmount = 0;
        $manualAmount = 0;

        $payoutsList = '';
        foreach ($payouts as $p) {
            $status = $p['bunq_status'] ?? 'manual';
            if ($status === 'success') {
                $statusBadge = '<span style="background:#22c55e;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;">Automatisch</span>';
                $successCount++;
                $successAmount += $p['total_payout'];
            } elseif ($status === 'failed') {
                $statusBadge = '<span style="background:#dc2626;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;">Mislukt</span>';
                $failedCount++;
            } else {
                $statusBadge = '<span style="background:#f59e0b;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;">Handmatig</span>';
                $manualAmount += $p['total_payout'];
            }

            $payoutsList .= "<tr>
                <td style='padding:10px;border-bottom:1px solid #eee;'>{$p['company_name']}</td>
                <td style='padding:10px;border-bottom:1px solid #eee;font-family:monospace;font-size:11px;'>{$p['iban']}</td>
                <td style='padding:10px;border-bottom:1px solid #eee;text-align:center;'>" . count($p['bookings']) . "</td>
                <td style='padding:10px;border-bottom:1px solid #eee;text-align:right;font-weight:600;'>€" . number_format($p['total_payout'], 2, ',', '.') . "</td>
                <td style='padding:10px;border-bottom:1px solid #eee;text-align:center;'>{$statusBadge}</td>
            </tr>";
        }

        if ($bunqEnabled && $manualCount === 0) {
            $subject = "Wekelijkse uitbetalingen voltooid - €" . number_format($totalAmount, 2, ',', '.');
            $headerBg = '#22c55e';
            $headerText = 'Alle Uitbetalingen Automatisch Verwerkt';
        } elseif ($manualCount > 0) {
            $subject = "ACTIE VEREIST: " . $manualCount . " handmatige uitbetaling(en) - €" . number_format($manualAmount, 2, ',', '.');
            $headerBg = '#dc2626';
            $headerText = 'Handmatige Uitbetalingen Vereist';
        } else {
            $subject = "Wekelijkse uitbetalingen overzicht - €" . number_format($totalAmount, 2, ',', '.');
            $headerBg = '#000';
            $headerText = 'Wekelijkse Uitbetalingen';
        }

        $statsHtml = "
        <div style='display:flex;gap:10px;margin:20px 0;flex-wrap:wrap;'>
            <div style='flex:1;min-width:120px;background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;text-align:center;'>
                <p style='margin:0;font-size:20px;font-weight:bold;color:#22c55e;'>$successCount</p>
                <p style='margin:3px 0 0;color:#166534;font-size:12px;'>Automatisch (Bunq)</p>
            </div>
            <div style='flex:1;min-width:120px;background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:15px;text-align:center;'>
                <p style='margin:0;font-size:20px;font-weight:bold;color:#d97706;'>$manualCount</p>
                <p style='margin:3px 0 0;color:#92400e;font-size:12px;'>Handmatig</p>
            </div>
            <div style='flex:1;min-width:120px;background:#fef2f2;border:1px solid #dc2626;border-radius:8px;padding:15px;text-align:center;'>
                <p style='margin:0;font-size:20px;font-weight:bold;color:#dc2626;'>$failedCount</p>
                <p style='margin:3px 0 0;color:#991b1b;font-size:12px;'>Mislukt</p>
            </div>
        </div>";

        $manualActionHtml = $manualCount > 0 ? "
        <div style='background:#fef2f2;border:1px solid #dc2626;border-radius:8px;padding:15px;margin:20px 0;'>
            <p style='margin:0 0 10px;font-weight:600;color:#991b1b;'>Handmatige actie vereist:</p>
            <p style='margin:0;color:#991b1b;font-size:14px;'>
                €" . number_format($manualAmount, 2, ',', '.') . " moet handmatig worden overgemaakt naar " . $manualCount . " salon(s).
            </p>
        </div>
        <p>
            Na het overmaken, markeer als voltooid:<br>
            <code style='background:#0a0a0a;padding:5px 10px;border-radius:4px;display:inline-block;margin-top:10px;font-size:12px;'>
                /cron/complete-payouts?key=glamour-cron-2024-secret
            </code>
        </p>" : "";

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:750px;margin:0 auto;'>
            <div style='background:{$headerBg};padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>{$headerText}</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <p>Overzicht van de wekelijkse uitbetalingen.</p>

                {$statsHtml}

                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:13px;'>
                    <thead>
                        <tr style='background:#0a0a0a;'>
                            <th style='padding:10px;text-align:left;'>Bedrijf</th>
                            <th style='padding:10px;text-align:left;'>IBAN</th>
                            <th style='padding:10px;text-align:center;'>Boekingen</th>
                            <th style='padding:10px;text-align:right;'>Bedrag</th>
                            <th style='padding:10px;text-align:center;'>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$payoutsList}
                    </tbody>
                </table>

                {$manualActionHtml}

                <div style='background:#f9fafb;border-radius:8px;padding:15px;margin-top:20px;'>
                    <p style='margin:0;font-size:13px;color:#cccccc;'>
                        <strong>Bunq automatisering:</strong> " . ($bunqEnabled ? 'Actief' : 'Niet geconfigureerd') . "<br>
                        <strong>Totaal verwerkt:</strong> €" . number_format($totalAmount, 2, ',', '.') . "
                    </p>
                </div>
            </div>
            <div style='background:#0a0a0a;padding:15px;text-align:center;border-top:1px solid #333;'>
                <p style='margin:0;color:#999;font-size:12px;'>GlamourSchedule Wekelijkse Payout Rapport</p>
            </div>
        </div>";

        $mailer->send($adminEmail, $subject, $body);
    }

    private function sendBusinessPayoutEmail(array $payout, int $payoutId): void
    {
        $mailer = new Mailer();

        $bookingsList = '';
        foreach ($payout['bookings'] as $b) {
            $bookingsList .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #eee;'>{$b['booking_number']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;'>{$b['service_name']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>€" . number_format($b['service_price'], 2, ',', '.') . "</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;color:#dc2626;'>-€" . number_format($b['platform_fee'], 2, ',', '.') . "</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;font-weight:600;color:#22c55e;'>€" . number_format($b['payout_amount'], 2, ',', '.') . "</td>
            </tr>";
        }

        $subject = "Uitbetaling voltooid - €" . number_format($payout['total_payout'], 2, ',', '.');

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:650px;margin:0 auto;'>
            <div style='background:#000;padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>Uitbetaling Voltooid</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <p>Beste {$payout['company_name']},</p>
                <p>Geweldig nieuws! Je uitbetaling is automatisch verwerkt via Mollie Connect.</p>

                <div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:20px;margin:20px 0;text-align:center;'>
                    <p style='margin:0;font-size:28px;font-weight:bold;color:#22c55e;'>€" . number_format($payout['total_payout'], 2, ',', '.') . "</p>
                    <p style='margin:5px 0 0;color:#cccccc;font-size:14px;'>" . count($payout['bookings']) . " boeking(en) - Payout #$payoutId</p>
                </div>

                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:14px;'>
                    <thead>
                        <tr style='background:#0a0a0a;'>
                            <th style='padding:10px;text-align:left;'>Boeking</th>
                            <th style='padding:10px;text-align:left;'>Service</th>
                            <th style='padding:10px;text-align:right;'>Bedrag</th>
                            <th style='padding:10px;text-align:right;'>Fee</th>
                            <th style='padding:10px;text-align:right;'>Uitbetaling</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$bookingsList}
                    </tbody>
                    <tfoot>
                        <tr style='background:#f9fafb;font-weight:bold;'>
                            <td colspan='2' style='padding:10px;'>Totaal</td>
                            <td style='padding:10px;text-align:right;'>€" . number_format($payout['total_service_amount'], 2, ',', '.') . "</td>
                            <td style='padding:10px;text-align:right;color:#dc2626;'>-€" . number_format($payout['total_platform_fee'], 2, ',', '.') . "</td>
                            <td style='padding:10px;text-align:right;color:#22c55e;'>€" . number_format($payout['total_payout'], 2, ',', '.') . "</td>
                        </tr>
                    </tfoot>
                </table>

                <div style='background:#f9fafb;padding:15px;border-radius:8px;margin:20px 0;'>
                    <p style='margin:0 0 5px;font-weight:600;'>Uitbetalingsdetails:</p>
                    <p style='margin:0;color:#cccccc;font-size:14px;'>Het bedrag is overgemaakt naar je gekoppelde Mollie account en wordt automatisch doorgeboekt naar je bankrekening.</p>
                </div>

                <p style='color:#cccccc;font-size:13px;'>
                    Bekijk al je uitbetalingen in je <a href='https://glamourschedule.com/business/payouts' style='color:#000;'>dashboard</a>.
                </p>
            </div>
            <div style='background:#0a0a0a;padding:15px;text-align:center;border-top:1px solid #333;'>
                <p style='margin:0;color:#999;font-size:12px;'>© " . date('Y') . " GlamourSchedule</p>
            </div>
        </div>";

        $mailer->send($payout['email'], $subject, $body);
    }

    private function sendAdminPayoutNotification(array $payouts, float $totalAmount, int $failedCount = 0): void
    {
        $mailer = new Mailer();
        $adminEmail = 'jjt-services@outlook.com';

        $successCount = 0;
        $skippedCount = 0;
        $totalPlatformFee = 0;

        $payoutsList = '';
        foreach ($payouts as $p) {
            $hasConnect = !empty($p['mollie_account_id']);
            $status = $hasConnect ? '<span style="color:#22c55e;">Automatisch</span>' : '<span style="color:#f59e0b;">Geen Mollie Connect</span>';

            if ($hasConnect) {
                $successCount++;
                $totalPlatformFee += $p['total_platform_fee'] ?? 0;
            } else {
                $skippedCount++;
            }

            $payoutsList .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #eee;'>{$p['company_name']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;'>" . count($p['bookings']) . "</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>€" . number_format($p['total_service_amount'] ?? 0, 2, ',', '.') . "</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;color:#dc2626;'>€" . number_format($p['total_platform_fee'] ?? 0, 2, ',', '.') . "</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;font-weight:600;'>€" . number_format($p['total_payout'] ?? 0, 2, ',', '.') . "</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;font-size:12px;'>$status</td>
            </tr>";
        }

        $subject = "Uitbetalingen verwerkt - €" . number_format($totalAmount, 2, ',', '.') . " uitbetaald";

        $statusBanner = $failedCount > 0
            ? "<div style='background:#fef2f2;border:1px solid #ef4444;border-radius:8px;padding:15px;margin:20px 0;'>
                <p style='margin:0;color:#991b1b;font-weight:600;'>$failedCount uitbetaling(en) mislukt - controleer de logs</p>
               </div>"
            : "";

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:800px;margin:0 auto;'>
            <div style='background:#000;padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>Automatische Uitbetalingen</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <p>De automatische uitbetalingen via Mollie Connect zijn verwerkt.</p>

                $statusBanner

                <div style='display:flex;gap:15px;margin:20px 0;'>
                    <div style='flex:1;background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;text-align:center;'>
                        <p style='margin:0;font-size:24px;font-weight:bold;color:#22c55e;'>€" . number_format($totalAmount, 2, ',', '.') . "</p>
                        <p style='margin:5px 0 0;color:#166534;font-size:13px;'>Uitbetaald aan salons</p>
                    </div>
                    <div style='flex:1;background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:15px;text-align:center;'>
                        <p style='margin:0;font-size:24px;font-weight:bold;color:#d97706;'>€" . number_format($totalPlatformFee, 2, ',', '.') . "</p>
                        <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Platform inkomsten</p>
                    </div>
                </div>

                <div style='display:flex;gap:10px;margin:15px 0;'>
                    <div style='background:#f0fdf4;padding:10px 15px;border-radius:6px;'>
                        <span style='color:#22c55e;font-weight:600;'>$successCount</span> <span style='color:#cccccc;font-size:13px;'>gelukt</span>
                    </div>
                    <div style='background:#fef2f2;padding:10px 15px;border-radius:6px;'>
                        <span style='color:#ef4444;font-weight:600;'>$failedCount</span> <span style='color:#cccccc;font-size:13px;'>mislukt</span>
                    </div>
                    <div style='background:#fef3c7;padding:10px 15px;border-radius:6px;'>
                        <span style='color:#d97706;font-weight:600;'>$skippedCount</span> <span style='color:#cccccc;font-size:13px;'>overgeslagen</span>
                    </div>
                </div>

                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:13px;'>
                    <thead>
                        <tr style='background:#0a0a0a;'>
                            <th style='padding:10px;text-align:left;'>Bedrijf</th>
                            <th style='padding:10px;text-align:center;'>Boekingen</th>
                            <th style='padding:10px;text-align:right;'>Omzet</th>
                            <th style='padding:10px;text-align:right;'>Fee</th>
                            <th style='padding:10px;text-align:right;'>Uitbetaling</th>
                            <th style='padding:10px;text-align:center;'>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$payoutsList}
                    </tbody>
                </table>

                <p style='color:#cccccc;font-size:13px;margin-top:20px;'>
                    Salons zonder Mollie Connect kunnen hun account koppelen in het dashboard.
                </p>
            </div>
            <div style='background:#0a0a0a;padding:15px;text-align:center;border-top:1px solid #333;'>
                <p style='margin:0;color:#999;font-size:12px;'>GlamourSchedule Automatische Payout Notificatie</p>
            </div>
        </div>";

        $mailer->send($adminEmail, $subject, $body);
    }

    /**
     * Send alert when Bunq balance cannot be retrieved
     */
    private function sendBalanceErrorAlert(array $payouts, float $totalNeeded): void
    {
        $mailer = new Mailer();
        $adminEmail = 'jjt-services@outlook.com';

        $subject = "KRITIEK: Bunq saldo kon niet worden opgehaald - Uitbetalingen gestopt";

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#dc2626;padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>Kritieke Fout: Bunq API</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <div style='background:#fef2f2;border:1px solid #dc2626;border-radius:8px;padding:20px;margin-bottom:20px;'>
                    <p style='margin:0;color:#991b1b;font-weight:600;'>Het Bunq saldo kon niet worden opgehaald.</p>
                    <p style='margin:10px 0 0;color:#991b1b;'>Alle automatische uitbetalingen zijn GESTOPT ter bescherming.</p>
                </div>

                <h3>Details:</h3>
                <ul style='color:#cccccc;'>
                    <li>Aantal salons: " . count($payouts) . "</li>
                    <li>Totaal te betalen: €" . number_format($totalNeeded, 2, ',', '.') . "</li>
                    <li>Tijdstip: " . date('d-m-Y H:i:s') . "</li>
                </ul>

                <h3>Actie vereist:</h3>
                <ol style='color:#cccccc;'>
                    <li>Controleer de Bunq API key in .env</li>
                    <li>Controleer of je Bunq account actief is</li>
                    <li>Voer de cron handmatig opnieuw uit na het oplossen</li>
                </ol>

                <p style='margin-top:20px;'>
                    <code style='background:#0a0a0a;padding:8px 12px;border-radius:4px;display:block;font-size:12px;'>
                        curl \"https://glamourschedule.com/cron/weekly-payouts?key=glamour-cron-2024-secret\"
                    </code>
                </p>
            </div>
        </div>";

        $mailer->send($adminEmail, $subject, $body);
    }

    /**
     * Send alert when Bunq balance is insufficient
     */
    private function sendInsufficientFundsAlert(array $payouts, float $totalNeeded, float $currentBalance): void
    {
        $mailer = new Mailer();
        $adminEmail = 'jjt-services@outlook.com';

        $shortage = $totalNeeded - $currentBalance;

        $payoutsList = '';
        foreach ($payouts as $p) {
            $payoutsList .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #eee;'>{$p['company_name']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>€" . number_format($p['total_payout'], 2, ',', '.') . "</td>
            </tr>";
        }

        $subject = "ACTIE VEREIST: Onvoldoende Bunq saldo - €" . number_format($shortage, 2, ',', '.') . " tekort";

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#f59e0b;padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>Onvoldoende Saldo</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:20px;margin-bottom:20px;'>
                    <p style='margin:0;color:#92400e;font-weight:600;'>Er is onvoldoende saldo op de Bunq rekening.</p>
                    <p style='margin:10px 0 0;color:#92400e;'>Uitbetalingen zijn uitgesteld ter bescherming.</p>
                </div>

                <div style='display:flex;gap:15px;margin:20px 0;'>
                    <div style='flex:1;background:#fef2f2;border-radius:8px;padding:15px;text-align:center;'>
                        <p style='margin:0;font-size:20px;font-weight:bold;color:#dc2626;'>€" . number_format($totalNeeded, 2, ',', '.') . "</p>
                        <p style='margin:5px 0 0;color:#991b1b;font-size:12px;'>Nodig</p>
                    </div>
                    <div style='flex:1;background:#fef3c7;border-radius:8px;padding:15px;text-align:center;'>
                        <p style='margin:0;font-size:20px;font-weight:bold;color:#d97706;'>€" . number_format($currentBalance, 2, ',', '.') . "</p>
                        <p style='margin:5px 0 0;color:#92400e;font-size:12px;'>Beschikbaar</p>
                    </div>
                    <div style='flex:1;background:#0a0a0a;border-radius:8px;padding:15px;text-align:center;'>
                        <p style='margin:0;font-size:20px;font-weight:bold;color:#cccccc;'>€" . number_format($shortage, 2, ',', '.') . "</p>
                        <p style='margin:5px 0 0;color:#999;font-size:12px;'>Tekort</p>
                    </div>
                </div>

                <h3>Wachtende uitbetalingen:</h3>
                <table style='width:100%;border-collapse:collapse;margin:15px 0;font-size:14px;'>
                    <thead>
                        <tr style='background:#0a0a0a;'>
                            <th style='padding:10px;text-align:left;'>Salon</th>
                            <th style='padding:10px;text-align:right;'>Bedrag</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$payoutsList}
                    </tbody>
                </table>

                <h3>Actie vereist:</h3>
                <ol style='color:#cccccc;'>
                    <li>Stort minimaal €" . number_format($shortage + 50, 2, ',', '.') . " op je Bunq rekening</li>
                    <li>Of wacht tot Mollie de volgende uitbetaling doet</li>
                    <li>Voer de cron opnieuw uit na aanvulling</li>
                </ol>

                <p style='margin-top:20px;'>
                    <code style='background:#0a0a0a;padding:8px 12px;border-radius:4px;display:block;font-size:12px;'>
                        curl \"https://glamourschedule.com/cron/weekly-payouts?key=glamour-cron-2024-secret\"
                    </code>
                </p>
            </div>
        </div>";

        $mailer->send($adminEmail, $subject, $body);
    }

    private function logPayout(string $message): void
    {
        $logFile = BASE_PATH . '/storage/logs/cron-payouts.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    // Minimum payout amount for sales partners
    private const SALES_MINIMUM_PAYOUT = 49.99;

    /**
     * Process weekly payouts for sales partners
     * Runs every Wednesday 08:00: /cron/sales-payouts?key=glamour-cron-2024-secret
     *
     * Sales partners earn commission per converted salon (after trial, when they pay registration fee).
     * Commission amount is stored in sales_referrals.commission column.
     * NO commission for Early Bird registrations.
     * Minimum payout: €49.99 (1 converted salon)
     */
    public function salesPayouts(): string
    {
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $this->logSalesPayout('=== SALES PARTNER PAYOUT START ===');
        $this->logSalesPayout('Processing date: ' . date('Y-m-d'));

        try {
            // Find sales partners with converted referrals (salons that paid after trial)
            // Commission is stored in sales_referrals.commission and is set when business is created
            // Status 'converted' means the business paid their registration fee after trial
            $stmt = $this->db->query(
                "SELECT
                    su.id as sales_user_id,
                    su.name as sales_name,
                    su.email as sales_email,
                    su.iban as sales_iban,
                    su.referral_code,
                    COUNT(sr.id) as referral_count,
                    SUM(sr.commission) as total_commission
                 FROM sales_users su
                 JOIN sales_referrals sr ON sr.sales_user_id = su.id
                 WHERE su.status = 'active'
                   AND su.iban IS NOT NULL AND su.iban != ''
                   AND sr.status = 'converted'
                 GROUP BY su.id
                 HAVING total_commission >= ?",
                [self::SALES_MINIMUM_PAYOUT]
            );
            $salesPartners = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($salesPartners)) {
                $this->logSalesPayout('No sales partner payouts to process');
                return json_encode([
                    'success' => true,
                    'message' => 'No sales partner payouts to process',
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            }

            $this->logSalesPayout("Found " . count($salesPartners) . " sales partners with pending payouts");

            // Calculate total needed
            $totalNeeded = array_sum(array_column($salesPartners, 'total_commission'));
            $this->logSalesPayout("Total payout amount needed: €" . number_format($totalNeeded, 2));

            // Check Bunq configuration
            $bunq = new BunqService();
            $useBunq = $bunq->isConfigured();

            if (!$useBunq) {
                $this->logSalesPayout("WARNING: Bunq not configured - payouts will require manual processing");
            }

            // Check Bunq balance
            if ($useBunq) {
                $bunqBalance = $bunq->getBalance();
                if ($bunqBalance === null) {
                    $this->logSalesPayout("ERROR: Could not retrieve Bunq balance");
                    $this->sendSalesBalanceErrorAlert($salesPartners, $totalNeeded);
                    return json_encode([
                        'success' => false,
                        'error' => 'Could not retrieve Bunq balance',
                        'timestamp' => date('Y-m-d H:i:s')
                    ]);
                }

                $requiredBalance = $totalNeeded * 1.10; // 10% safety margin
                if ($bunqBalance < $requiredBalance) {
                    $this->logSalesPayout("ERROR: Insufficient funds - Need €" . number_format($requiredBalance, 2) . ", Have €" . number_format($bunqBalance, 2));
                    $this->sendSalesInsufficientFundsAlert($salesPartners, $totalNeeded, $bunqBalance);
                    return json_encode([
                        'success' => false,
                        'error' => 'Insufficient Bunq balance for sales payouts',
                        'balance' => $bunqBalance,
                        'required' => $requiredBalance,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]);
                }

                $this->logSalesPayout("Balance check PASSED: €" . number_format($bunqBalance, 2));
            }

            $payoutsProcessed = 0;
            $payoutsFailed = 0;
            $totalAmount = 0;
            $results = [];

            foreach ($salesPartners as &$partner) {
                // Get the converted referrals (businesses that paid) for this sales partner
                $referralStmt = $this->db->query(
                    "SELECT sr.id as referral_id, sr.commission, sr.created_at,
                            b.id as business_id, b.company_name, b.email as business_email
                     FROM sales_referrals sr
                     JOIN businesses b ON sr.business_id = b.id
                     WHERE sr.sales_user_id = ?
                       AND sr.status = 'converted'",
                    [$partner['sales_user_id']]
                );
                $referrals = $referralStmt->fetchAll(\PDO::FETCH_ASSOC);
                $partner['referrals'] = $referrals;

                // Build list of business names for description
                $businessNames = array_map(fn($r) => $r['company_name'], $referrals);
                $businessList = implode(', ', array_slice($businessNames, 0, 3));
                if (count($businessNames) > 3) {
                    $businessList .= ' +' . (count($businessNames) - 3) . ' meer';
                }

                // Create payout record
                $reference = 'SP-' . date('YW') . '-' . $partner['sales_user_id'];
                $this->db->query(
                    "INSERT INTO sales_payouts (sales_partner_id, amount, referral_count, reference, status, created_at)
                     VALUES (?, ?, ?, ?, 'processing', NOW())",
                    [
                        $partner['sales_user_id'],
                        $partner['total_commission'],
                        count($referrals),
                        $reference
                    ]
                );
                $payoutId = $this->db->lastInsertId();
                $partner['payout_id'] = $payoutId;

                $this->logSalesPayout("Processing {$partner['sales_name']}: " . count($referrals) . " salons - " . $businessList);

                // Try Bunq automatic transfer
                if ($useBunq) {
                    $description = "GlamourSchedule commissie - " . count($referrals) . " salon(s)";
                    $bunqResult = $bunq->makePayment(
                        $partner['sales_iban'],
                        $partner['sales_name'],
                        $partner['total_commission'],
                        $description
                    );

                    if ($bunqResult['success']) {
                        // Mark payout as completed
                        $this->db->query(
                            "UPDATE sales_payouts SET status = 'completed', transaction_id = ?, completed_at = NOW() WHERE id = ?",
                            [$bunqResult['payment_id'], $payoutId]
                        );

                        // Update referrals status to 'paid'
                        $referralIds = array_column($referrals, 'referral_id');
                        if (!empty($referralIds)) {
                            $placeholders = implode(',', array_fill(0, count($referralIds), '?'));
                            $this->db->query(
                                "UPDATE sales_referrals SET status = 'paid', paid_at = NOW() WHERE id IN ($placeholders)",
                                $referralIds
                            );
                        }

                        // Send success email to sales partner
                        $this->sendSalesPayoutEmail($partner, true);

                        $payoutsProcessed++;
                        $totalAmount += $partner['total_commission'];

                        $this->logSalesPayout("SUCCESS: {$partner['sales_name']} - €" . number_format($partner['total_commission'], 2) . " - {$bunqResult['payment_id']}");

                        $results[] = [
                            'partner' => $partner['sales_name'],
                            'status' => 'success',
                            'amount' => $partner['total_commission'],
                            'salons' => count($referrals),
                            'businesses' => $businessNames
                        ];
                    } else {
                        // Mark as failed
                        $this->db->query(
                            "UPDATE sales_payouts SET status = 'failed', notes = ? WHERE id = ?",
                            ['Bunq error: ' . $bunqResult['error'], $payoutId]
                        );

                        $payoutsFailed++;

                        $this->logSalesPayout("FAILED: {$partner['sales_name']} - " . $bunqResult['error']);

                        $results[] = [
                            'partner' => $partner['sales_name'],
                            'status' => 'failed',
                            'error' => $bunqResult['error']
                        ];
                    }
                } else {
                    // No Bunq - mark for manual processing
                    $this->db->query(
                        "UPDATE sales_payouts SET status = 'pending', notes = 'Handmatige overboeking vereist' WHERE id = ?",
                        [$payoutId]
                    );

                    // Send notification email
                    $this->sendSalesPayoutEmail($partner, false);

                    $totalAmount += $partner['total_commission'];

                    $this->logSalesPayout("MANUAL: {$partner['sales_name']} - €" . number_format($partner['total_commission'], 2));

                    $results[] = [
                        'partner' => $partner['sales_name'],
                        'status' => 'manual',
                        'amount' => $partner['total_commission'],
                        'salons' => count($referrals),
                        'businesses' => $businessNames
                    ];
                }
            }

            // Send admin notification
            $this->sendSalesAdminNotification($salesPartners, $totalAmount, $useBunq, $payoutsFailed);

            $this->logSalesPayout("Sales payout complete. Success: $payoutsProcessed, Failed: $payoutsFailed, Total: €" . number_format($totalAmount, 2));

            return json_encode([
                'success' => true,
                'bunq_enabled' => $useBunq,
                'payouts_processed' => $payoutsProcessed,
                'payouts_failed' => $payoutsFailed,
                'total_amount' => $totalAmount,
                'results' => $results,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            $this->logSalesPayout("Error: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Send payout notification to sales partner
     */
    private function sendSalesPayoutEmail(array $partner, bool $isAutomatic): void
    {
        $mailer = new Mailer();

        $referrals = $partner['referrals'] ?? [];
        $salonCount = count($referrals);
        $amount = $partner['total_commission'];

        // Build salon list for email
        $salonListHtml = '';
        foreach ($referrals as $ref) {
            $commission = number_format($ref['commission'], 2, ',', '.');
            $salonListHtml .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #eee;'>{$ref['company_name']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>€{$commission}</td>
            </tr>";
        }

        if ($isAutomatic) {
            $subject = "Commissie uitbetaald - €" . number_format($amount, 2, ',', '.');
            $headerBg = '#22c55e';
            $statusBox = "
                <div style='background:#f0fdf4;border:2px solid #22c55e;border-radius:12px;padding:20px;margin:20px 0;text-align:center;'>
                    <p style='margin:0;font-size:32px;font-weight:bold;color:#22c55e;'>€" . number_format($amount, 2, ',', '.') . "</p>
                    <p style='margin:8px 0 0;color:#166534;font-size:14px;'>$salonCount geconverteerde salon(s)</p>
                </div>";
            $statusMessage = "<p style='color:#166534;'>Het bedrag is automatisch overgemaakt naar je bankrekening. Je ontvangt het binnen 1-2 werkdagen.</p>";
        } else {
            $subject = "Commissie in verwerking - €" . number_format($amount, 2, ',', '.');
            $headerBg = '#000';
            $statusBox = "
                <div style='background:#fef3c7;border:2px solid #f59e0b;border-radius:12px;padding:20px;margin:20px 0;text-align:center;'>
                    <p style='margin:0;font-size:32px;font-weight:bold;color:#d97706;'>€" . number_format($amount, 2, ',', '.') . "</p>
                    <p style='margin:8px 0 0;color:#92400e;font-size:14px;'>$salonCount geconverteerde salon(s)</p>
                </div>";
            $statusMessage = "<p><strong>Verwachte verwerkingstijd:</strong> 1-3 werkdagen</p>";
        }

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:{$headerBg};padding:25px;text-align:center;border-radius:12px 12px 0 0;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>Commissie Uitbetaling</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;border:1px solid #333;border-top:none;'>
                <p>Hallo {$partner['sales_name']},</p>
                <p>Goed nieuws! Je commissie is verwerkt.</p>

                {$statusBox}

                <div style='background:#f9fafb;border-radius:8px;padding:15px;margin:20px 0;'>
                    <p style='margin:0 0 10px;font-weight:600;'>Salons waarvoor je commissie ontvangt:</p>
                    <table style='width:100%;font-size:14px;'>
                        <thead>
                            <tr style='background:#e5e7eb;'>
                                <th style='padding:8px;text-align:left;'>Salon</th>
                                <th style='padding:8px;text-align:right;'>Commissie</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$salonListHtml}
                        </tbody>
                        <tfoot>
                            <tr style='background:#f0fdf4;'>
                                <td style='padding:10px 8px;font-weight:600;'>Totaal ({$salonCount} salon(s))</td>
                                <td style='padding:10px 8px;text-align:right;font-weight:600;color:#22c55e;'>€" . number_format($amount, 2, ',', '.') . "</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p><strong>Bankrekening:</strong> {$partner['sales_iban']}</p>
                {$statusMessage}

                <p style='color:#cccccc;font-size:13px;margin-top:20px;'>
                    Bekijk al je uitbetalingen in je <a href='https://glamourschedule.nl/sales/payouts' style='color:#000;'>dashboard</a>.
                </p>
            </div>
            <div style='background:#0a0a0a;padding:15px;text-align:center;border-radius:0 0 12px 12px;border:1px solid #333;border-top:none;'>
                <p style='margin:0;color:#999;font-size:12px;'>© " . date('Y') . " GlamourSchedule Sales Partner</p>
            </div>
        </div>";

        // Generate and attach invoice PDF
        try {
            $invoiceService = new InvoiceService();
            $invoicePath = $invoiceService->generateSalesInvoice($partner);
            $invoiceName = 'GlamourSchedule-Commissie-Factuur-' . date('Y-m-d') . '.pdf';
            $mailer->sendWithAttachment($partner['sales_email'], $subject, $body, $invoicePath, $invoiceName);
        } catch (\Exception $e) {
            // Fallback to regular email if invoice generation fails
            error_log('Invoice generation failed for sales payout: ' . $e->getMessage());
            $mailer->send($partner['sales_email'], $subject, $body);
        }
    }

    /**
     * Send admin notification for sales payouts
     */
    private function sendSalesAdminNotification(array $partners, float $totalAmount, bool $bunqEnabled, int $failedCount): void
    {
        $mailer = new Mailer();
        $adminEmail = 'jjt-services@outlook.com';

        $partnersList = '';
        $successCount = 0;
        $manualCount = 0;

        foreach ($partners as $p) {
            $status = $p['bunq_status'] ?? ($bunqEnabled ? 'success' : 'manual');
            if ($status === 'success') {
                $badge = '<span style="background:#22c55e;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;">Automatisch</span>';
                $successCount++;
            } elseif ($status === 'failed') {
                $badge = '<span style="background:#dc2626;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;">Mislukt</span>';
            } else {
                $badge = '<span style="background:#f59e0b;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;">Handmatig</span>';
                $manualCount++;
            }

            // Build salon list for this partner
            $salonNames = [];
            if (!empty($p['referrals'])) {
                foreach ($p['referrals'] as $ref) {
                    $salonNames[] = $ref['company_name'];
                }
            }
            $salonList = implode(', ', $salonNames);
            $salonCount = $p['referral_count'] ?? count($salonNames);

            $partnersList .= "<tr>
                <td style='padding:10px;border-bottom:1px solid #eee;'>
                    <strong>{$p['sales_name']}</strong><br>
                    <small style='color:#cccccc;font-size:11px;'>{$p['sales_iban']}</small>
                </td>
                <td style='padding:10px;border-bottom:1px solid #eee;text-align:center;'>{$salonCount}</td>
                <td style='padding:10px;border-bottom:1px solid #eee;font-size:11px;color:#cccccc;'>{$salonList}</td>
                <td style='padding:10px;border-bottom:1px solid #eee;text-align:right;font-weight:600;'>€" . number_format($p['total_commission'], 2, ',', '.') . "</td>
                <td style='padding:10px;border-bottom:1px solid #eee;text-align:center;'>{$badge}</td>
            </tr>";
        }

        $subject = "Sales Partner Uitbetalingen - €" . number_format($totalAmount, 2, ',', '.');
        if ($failedCount > 0) {
            $subject = "ACTIE: $failedCount mislukte sales uitbetaling(en)";
        }

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:850px;margin:0 auto;'>
            <div style='background:#000;padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>Sales Partner Uitbetalingen</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <div style='display:flex;gap:15px;margin-bottom:20px;'>
                    <div style='flex:1;background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;text-align:center;'>
                        <p style='margin:0;font-size:24px;font-weight:bold;color:#22c55e;'>€" . number_format($totalAmount, 2, ',', '.') . "</p>
                        <p style='margin:5px 0 0;color:#166534;font-size:12px;'>Totaal uitbetaald</p>
                    </div>
                    <div style='flex:1;background:#0a0a0a;border-radius:8px;padding:15px;text-align:center;'>
                        <p style='margin:0;font-size:24px;font-weight:bold;color:#ffffff;'>" . count($partners) . "</p>
                        <p style='margin:5px 0 0;color:#cccccc;font-size:12px;'>Sales partners</p>
                    </div>
                </div>

                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:13px;'>
                    <thead>
                        <tr style='background:#0a0a0a;'>
                            <th style='padding:10px;text-align:left;'>Partner</th>
                            <th style='padding:10px;text-align:center;'>Salons</th>
                            <th style='padding:10px;text-align:left;'>Bedrijven</th>
                            <th style='padding:10px;text-align:right;'>Bedrag</th>
                            <th style='padding:10px;text-align:center;'>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$partnersList}
                    </tbody>
                </table>

                <div style='background:#f9fafb;border-radius:8px;padding:15px;margin-top:20px;'>
                    <p style='margin:0;font-size:13px;color:#cccccc;'>
                        <strong>Bunq automatisering:</strong> " . ($bunqEnabled ? 'Actief' : 'Niet geconfigureerd') . "<br>
                        <strong>Commissie per salon:</strong> €49,99 (na betaling registratiefee)<br>
                        <strong>Minimum uitbetaling:</strong> €" . number_format(self::SALES_MINIMUM_PAYOUT, 2, ',', '.') . "
                    </p>
                </div>
            </div>
            <div style='background:#0a0a0a;padding:15px;text-align:center;border-top:1px solid #333;'>
                <p style='margin:0;color:#999;font-size:12px;'>GlamourSchedule Sales Partner Payout Rapport</p>
            </div>
        </div>";

        $mailer->send($adminEmail, $subject, $body);
    }

    /**
     * Send alert when Bunq balance cannot be retrieved (sales)
     */
    private function sendSalesBalanceErrorAlert(array $partners, float $totalNeeded): void
    {
        $mailer = new Mailer();
        $adminEmail = 'jjt-services@outlook.com';

        $subject = "KRITIEK: Bunq saldo niet beschikbaar - Sales uitbetalingen gestopt";

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#dc2626;padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>Sales Payout Fout</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <div style='background:#fef2f2;border:1px solid #dc2626;border-radius:8px;padding:20px;margin-bottom:20px;'>
                    <p style='margin:0;color:#991b1b;font-weight:600;'>Bunq saldo kon niet worden opgehaald.</p>
                    <p style='margin:10px 0 0;color:#991b1b;'>Sales partner uitbetalingen zijn GESTOPT.</p>
                </div>
                <p><strong>Details:</strong></p>
                <ul style='color:#cccccc;'>
                    <li>Aantal partners: " . count($partners) . "</li>
                    <li>Totaal te betalen: €" . number_format($totalNeeded, 2, ',', '.') . "</li>
                </ul>
                <p style='margin-top:20px;'>
                    <code style='background:#0a0a0a;padding:8px 12px;border-radius:4px;display:block;font-size:12px;'>
                        curl \"https://glamourschedule.com/cron/sales-payouts?key=glamour-cron-2024-secret\"
                    </code>
                </p>
            </div>
        </div>";

        $mailer->send($adminEmail, $subject, $body);
    }

    /**
     * Send alert when Bunq balance is insufficient (sales)
     */
    private function sendSalesInsufficientFundsAlert(array $partners, float $totalNeeded, float $currentBalance): void
    {
        $mailer = new Mailer();
        $adminEmail = 'jjt-services@outlook.com';

        $shortage = $totalNeeded - $currentBalance;

        $subject = "ACTIE: Onvoldoende saldo voor sales uitbetalingen - €" . number_format($shortage, 2, ',', '.') . " tekort";

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#f59e0b;padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>Onvoldoende Saldo</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:20px;margin-bottom:20px;'>
                    <p style='margin:0;color:#92400e;font-weight:600;'>Sales partner uitbetalingen uitgesteld.</p>
                </div>
                <div style='display:flex;gap:15px;margin:20px 0;'>
                    <div style='flex:1;background:#fef2f2;border-radius:8px;padding:15px;text-align:center;'>
                        <p style='margin:0;font-size:20px;font-weight:bold;color:#dc2626;'>€" . number_format($totalNeeded, 2, ',', '.') . "</p>
                        <p style='margin:5px 0 0;color:#991b1b;font-size:12px;'>Nodig</p>
                    </div>
                    <div style='flex:1;background:#fef3c7;border-radius:8px;padding:15px;text-align:center;'>
                        <p style='margin:0;font-size:20px;font-weight:bold;color:#d97706;'>€" . number_format($currentBalance, 2, ',', '.') . "</p>
                        <p style='margin:5px 0 0;color:#92400e;font-size:12px;'>Beschikbaar</p>
                    </div>
                </div>
                <p><strong>" . count($partners) . " sales partner(s)</strong> wachten op uitbetaling.</p>
                <p style='margin-top:20px;'>
                    <code style='background:#0a0a0a;padding:8px 12px;border-radius:4px;display:block;font-size:12px;'>
                        curl \"https://glamourschedule.com/cron/sales-payouts?key=glamour-cron-2024-secret\"
                    </code>
                </p>
            </div>
        </div>";

        $mailer->send($adminEmail, $subject, $body);
    }

    private function logSalesPayout(string $message): void
    {
        $logFile = BASE_PATH . '/storage/logs/cron-sales-payouts.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    /**
     * Expire waitlist offers after 60 minutes
     * Run every 5 minutes: /cron/waitlist-expire?key=glamour-cron-2024-secret
     */
    public function waitlistExpire(): string
    {
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $this->logWaitlist('=== WAITLIST EXPIRY CHECK START ===');

        try {
            // Find expired waitlist entries that were notified but didn't book within 60 minutes
            $stmt = $this->db->query(
                "SELECT w.*, b.company_name as business_name, b.slug as business_slug,
                        s.name as service_name
                 FROM booking_waitlist w
                 JOIN businesses b ON w.business_id = b.id
                 LEFT JOIN services s ON w.service_id = s.id
                 WHERE w.status = 'notified'
                   AND w.expires_at < NOW()"
            );
            $expiredEntries = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($expiredEntries)) {
                $this->logWaitlist('No expired waitlist entries found');
                return json_encode([
                    'success' => true,
                    'message' => 'No expired waitlist entries',
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            }

            $this->logWaitlist("Found " . count($expiredEntries) . " expired waitlist entries");

            $processedCount = 0;
            $nextNotifiedCount = 0;

            foreach ($expiredEntries as $entry) {
                // Mark the current entry as expired
                $this->db->query(
                    "UPDATE booking_waitlist SET status = 'expired' WHERE id = ?",
                    [$entry['id']]
                );

                $this->logWaitlist("Expired: {$entry['name']} for {$entry['business_name']} on {$entry['requested_date']}");
                $processedCount++;

                // Find the next person on the waitlist for this business/date
                $nextStmt = $this->db->query(
                    "SELECT w.*, b.company_name as business_name, b.slug as business_slug,
                            s.name as service_name
                     FROM booking_waitlist w
                     JOIN businesses b ON w.business_id = b.id
                     LEFT JOIN services s ON w.service_id = s.id
                     WHERE w.business_id = ? AND w.requested_date = ? AND w.status = 'waiting'
                     ORDER BY w.created_at ASC
                     LIMIT 1",
                    [$entry['business_id'], $entry['requested_date']]
                );
                $nextEntry = $nextStmt->fetch(\PDO::FETCH_ASSOC);

                if ($nextEntry) {
                    // Notify the next person
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+60 minutes'));
                    $this->db->query(
                        "UPDATE booking_waitlist SET status = 'notified', notified_at = NOW(), expires_at = ? WHERE id = ?",
                        [$expiresAt, $nextEntry['id']]
                    );

                    // Send notification email
                    $this->sendWaitlistNotificationFromCron([
                        'name' => $nextEntry['name'],
                        'email' => $nextEntry['email'],
                        'business_name' => $nextEntry['business_name'],
                        'business_slug' => $nextEntry['business_slug'],
                        'service_name' => $nextEntry['service_name'] ?? 'Dienst',
                        'date' => $entry['requested_date'],
                        'time' => $entry['requested_time'] ?? '10:00'
                    ]);

                    $nextNotifiedCount++;
                    $this->logWaitlist("Notified next: {$nextEntry['name']} for {$nextEntry['business_name']}");
                }
            }

            $this->logWaitlist("Processed: $processedCount expired, $nextNotifiedCount next notifications sent");
            $this->logWaitlist('=== WAITLIST EXPIRY CHECK END ===');

            return json_encode([
                'success' => true,
                'expired_count' => $processedCount,
                'next_notified_count' => $nextNotifiedCount,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            $this->logWaitlist("ERROR: " . $e->getMessage());
            return json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Send waitlist notification from cron (when previous person's time expired)
     */
    private function sendWaitlistNotificationFromCron(array $data): void
    {
        $dateFormatted = date('d-m-Y', strtotime($data['date']));
        $timeFormatted = date('H:i', strtotime($data['time']));
        $bookingUrl = "https://new.glamourschedule.nl/business/{$data['business_slug']}?date={$data['date']}&time={$data['time']}";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#000000,#000000);padding:40px;text-align:center;color:#fff;">
                            <h1 style="margin:0;font-size:24px;">Je bent aan de beurt!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;">Hallo <strong>{$data['name']}</strong>,</p>
                            <p style="font-size:16px;color:#555;line-height:1.6;">
                                De vorige persoon op de wachtlijst heeft niet gereageerd en jij bent nu aan de beurt voor een afspraak bij <strong>{$data['business_name']}</strong>!
                            </p>

                            <div style="background:linear-gradient(135deg,#fafafa,#f5f5f5);border:2px solid #000000;border-radius:12px;padding:25px;margin:25px 0;text-align:center;">
                                <p style="margin:0;color:#cccccc;font-size:14px;">Beschikbare plek</p>
                                <p style="margin:10px 0 0;color:#ffffff;font-size:24px;font-weight:700;">
                                    {$dateFormatted} om {$timeFormatted}
                                </p>
                                <p style="margin:10px 0 0;color:#cccccc;">{$data['service_name']}</p>
                            </div>

                            <div style="background:#fef2f2;border-left:4px solid #dc2626;padding:15px 20px;border-radius:0 8px 8px 0;margin:25px 0;">
                                <p style="margin:0;color:#991b1b;font-size:14px;">
                                    <strong>Let op - Beperkte tijd!</strong><br>
                                    Je hebt <strong>60 minuten</strong> om te boeken, daarna gaat de plek automatisch naar de volgende persoon op de wachtlijst.
                                </p>
                            </div>

                            <p style="text-align:center;margin:30px 0;">
                                <a href="{$bookingUrl}" style="display:inline-block;background:#000000;color:#fff;padding:18px 50px;border-radius:50px;text-decoration:none;font-weight:700;font-size:17px;">
                                    Nu Boeken
                                </a>
                            </p>

                            <p style="font-size:14px;color:#888;text-align:center;">
                                Kun je toch niet? Geen probleem, de plek gaat automatisch naar de volgende persoon.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#cccccc;font-size:13px;">GlamourSchedule</p>
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
            $mailer->send($data['email'], "Je bent aan de beurt! Plek bij {$data['business_name']}", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send waitlist notification: " . $e->getMessage());
        }
    }

    private function logWaitlist(string $message): void
    {
        $logFile = BASE_PATH . '/storage/logs/cron-waitlist.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    /**
     * Process booking reminders (24h and 1h before appointments)
     * Run every 5 minutes: /cron/process-reminders?key=glamour-cron-2024-secret
     */
    public function processReminders(): string
    {
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $this->logReminder('Starting reminder processing');

        try {
            $pushService = new \GlamourSchedule\Core\PushNotification();
            $sent24h = 0;
            $sent1h = 0;
            $pushSent = 0;

            // Get pending reminders that are due (scheduled_for <= NOW())
            $stmt = $this->db->query(
                "SELECT r.*,
                        b.uuid, b.booking_number, b.appointment_date, b.appointment_time,
                        b.guest_name, b.guest_email, b.user_id, b.status as booking_status,
                        b.payment_status, b.language,
                        s.name as service_name,
                        biz.company_name as business_name,
                        biz.street as address, biz.city,
                        u.email as user_email, u.first_name, u.last_name
                 FROM booking_reminders r
                 JOIN bookings b ON r.booking_id = b.id
                 JOIN services s ON b.service_id = s.id
                 JOIN businesses biz ON b.business_id = biz.id
                 LEFT JOIN users u ON b.user_id = u.id
                 WHERE r.status = 'pending'
                   AND r.scheduled_for <= NOW()
                   AND b.status NOT IN ('cancelled', 'checked_in')
                   AND b.payment_status = 'paid'
                 ORDER BY r.scheduled_for ASC
                 LIMIT 50"
            );
            $reminders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($reminders as $reminder) {
                // Get customer email
                $customerEmail = $reminder['guest_email'] ?: $reminder['user_email'];
                if (!$customerEmail) {
                    $this->db->query("UPDATE booking_reminders SET status = 'failed', sent_at = NOW() WHERE id = ?", [$reminder['id']]);
                    continue;
                }

                // Prepare booking data for mailer
                $bookingData = [
                    'uuid' => $reminder['uuid'],
                    'customer_email' => $customerEmail,
                    'customer_name' => $reminder['guest_name'] ?: trim($reminder['first_name'] . ' ' . $reminder['last_name']) ?: 'Klant',
                    'business_name' => $reminder['business_name'],
                    'service_name' => $reminder['service_name'],
                    'date' => $reminder['appointment_date'],
                    'time' => $reminder['appointment_time'],
                    'address' => $reminder['address'],
                    'city' => $reminder['city'],
                    'user_id' => $reminder['user_id']
                ];

                $emailSuccess = false;
                $pushSuccess = false;

                // Create mailer with booking's language for personalized email
                $bookingLang = $reminder['language'] ?? 'nl';
                $mailer = new Mailer($bookingLang);

                // Send email reminder
                try {
                    if ($reminder['reminder_type'] === '24h') {
                        $emailSuccess = $mailer->sendBookingReminder($bookingData);
                        if ($emailSuccess) $sent24h++;
                    } elseif ($reminder['reminder_type'] === '1h') {
                        $emailSuccess = $mailer->sendBookingReminder1Hour($bookingData);
                        if ($emailSuccess) $sent1h++;
                    }
                } catch (\Exception $e) {
                    $this->logReminder("Failed to send {$reminder['reminder_type']} email for booking {$reminder['booking_number']}: " . $e->getMessage());
                }

                // Send push notification (only for logged-in users)
                if ($reminder['user_id']) {
                    try {
                        $timeFormatted = date('H:i', strtotime($reminder['appointment_time']));

                        // Get push notification translations based on booking language
                        $pushTitles = [
                            'nl' => ['24h' => 'Herinnering: Morgen afspraak', '1h' => 'Over 1 uur: Afspraak'],
                            'en' => ['24h' => 'Reminder: Appointment tomorrow', '1h' => 'In 1 hour: Appointment'],
                            'de' => ['24h' => 'Erinnerung: Termin morgen', '1h' => 'In 1 Stunde: Termin'],
                            'fr' => ['24h' => 'Rappel: Rendez-vous demain', '1h' => 'Dans 1 heure: Rendez-vous']
                        ];
                        $pushMessages = [
                            'nl' => ['24h' => "Je hebt morgen om {$timeFormatted} een afspraak bij {$reminder['business_name']}.", '1h' => "Je afspraak bij {$reminder['business_name']} begint om {$timeFormatted}."],
                            'en' => ['24h' => "You have an appointment at {$reminder['business_name']} tomorrow at {$timeFormatted}.", '1h' => "Your appointment at {$reminder['business_name']} starts at {$timeFormatted}."],
                            'de' => ['24h' => "Sie haben morgen um {$timeFormatted} einen Termin bei {$reminder['business_name']}.", '1h' => "Ihr Termin bei {$reminder['business_name']} beginnt um {$timeFormatted}."],
                            'fr' => ['24h' => "Vous avez un rendez-vous chez {$reminder['business_name']} demain à {$timeFormatted}.", '1h' => "Votre rendez-vous chez {$reminder['business_name']} commence à {$timeFormatted}."]
                        ];

                        $lang = $bookingLang;
                        if ($reminder['reminder_type'] === '24h') {
                            $pushSuccess = $pushService->sendToUser(
                                $reminder['user_id'],
                                $pushTitles[$lang]['24h'] ?? $pushTitles['nl']['24h'],
                                $pushMessages[$lang]['24h'] ?? $pushMessages['nl']['24h'],
                                ['url' => '/booking/' . $reminder['uuid']]
                            );
                        } elseif ($reminder['reminder_type'] === '1h') {
                            $pushSuccess = $pushService->sendToUser(
                                $reminder['user_id'],
                                $pushTitles[$lang]['1h'] ?? $pushTitles['nl']['1h'],
                                $pushMessages[$lang]['1h'] ?? $pushMessages['nl']['1h'],
                                ['url' => '/booking/' . $reminder['uuid']]
                            );
                        }
                        if ($pushSuccess) $pushSent++;
                    } catch (\Exception $e) {
                        $this->logReminder("Failed to send push for booking {$reminder['booking_number']}: " . $e->getMessage());
                    }
                }

                // Update reminder status (success if at least email was sent)
                $newStatus = $emailSuccess ? 'sent' : 'failed';
                $this->db->query(
                    "UPDATE booking_reminders SET status = ?, sent_at = NOW() WHERE id = ?",
                    [$newStatus, $reminder['id']]
                );

                if ($emailSuccess || $pushSuccess) {
                    $methods = [];
                    if ($emailSuccess) $methods[] = 'email';
                    if ($pushSuccess) $methods[] = 'push';
                    $this->logReminder("Sent {$reminder['reminder_type']} reminder (" . implode('+', $methods) . ") for booking #{$reminder['booking_number']} to {$customerEmail}");
                }
            }

            $this->logReminder("Reminder processing complete. Email: 24h={$sent24h}, 1h={$sent1h}. Push: {$pushSent}");

            return json_encode([
                'success' => true,
                'reminders_processed' => count($reminders),
                'email_sent_24h' => $sent24h,
                'email_sent_1h' => $sent1h,
                'push_sent' => $pushSent,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            $this->logReminder("Error: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    private function logReminder(string $message): void
    {
        $logFile = BASE_PATH . '/storage/logs/cron-reminders.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    // =========================================================================
    // AI MANAGER - GLAMORI MANAGER
    // =========================================================================

    /**
     * Process daily AI Manager tasks for all businesses
     * Run daily at 8:00 AM: /cron/ai-manager?key=glamour-cron-2024-secret
     *
     * This processes:
     * - Daily summary emails to business owners
     * - Tomorrow's appointment reminders (internal notifications)
     * - New review notifications
     * - Milestone achievements
     */
    public function aiManager(): string
    {
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $this->logAiManager('Starting AI Manager daily processing');

        try {
            $mailer = new Mailer();
            $manager = new \GlamourSchedule\Core\GlamoriManager($this->db, $mailer);

            $results = $manager->processDailyTasks();

            $this->logAiManager(sprintf(
                "AI Manager complete. Summaries: %d, Reminders: %d, Notifications: %d, Errors: %d",
                $results['summaries_sent'],
                $results['reminders_sent'],
                $results['notifications_created'],
                count($results['errors'])
            ));

            // Log any errors
            foreach ($results['errors'] as $error) {
                $this->logAiManager("Error for business {$error['business_id']}: {$error['error']}");
            }

            return json_encode([
                'success' => true,
                'summaries_sent' => $results['summaries_sent'],
                'reminders_sent' => $results['reminders_sent'],
                'notifications_created' => $results['notifications_created'],
                'errors' => count($results['errors']),
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            $this->logAiManager("Error: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Process hourly AI Manager alerts (new bookings, cancellations)
     * Run hourly: /cron/ai-manager-alerts?key=glamour-cron-2024-secret
     */
    public function aiManagerAlerts(): string
    {
        if (($_GET['key'] ?? '') !== self::CRON_SECRET) {
            http_response_code(403);
            return json_encode(['error' => 'Unauthorized']);
        }

        $this->logAiManager('Starting AI Manager hourly alerts');

        try {
            $manager = new \GlamourSchedule\Core\GlamoriManager($this->db);

            $alertsCreated = 0;
            $errors = [];

            // Get all active businesses
            $businesses = $this->db->query(
                "SELECT id, language FROM businesses
                 WHERE status = 'active'
                 AND subscription_status IN ('active', 'trial')"
            )->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($businesses as $business) {
                try {
                    // Check for new bookings in the last hour
                    $newBookings = $manager->checkNewBookings($business['id'], 60);
                    foreach ($newBookings as $alert) {
                        $manager->createNotification(
                            $business['id'],
                            'new_booking',
                            $alert['message'],
                            $alert['data']
                        );
                        $alertsCreated++;
                    }

                    // Check for cancellations in the last hour
                    $cancellations = $manager->checkCancellations($business['id'], 60);
                    foreach ($cancellations as $alert) {
                        $manager->createNotification(
                            $business['id'],
                            'cancellation',
                            $alert['message'],
                            $alert['data']
                        );
                        $alertsCreated++;
                    }

                } catch (\Exception $e) {
                    $errors[] = [
                        'business_id' => $business['id'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            $this->logAiManager("AI Manager alerts complete. Alerts created: $alertsCreated");

            return json_encode([
                'success' => true,
                'alerts_created' => $alertsCreated,
                'businesses_processed' => count($businesses),
                'errors' => count($errors),
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            $this->logAiManager("Error: " . $e->getMessage());
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    private function logAiManager(string $message): void
    {
        $logFile = BASE_PATH . '/storage/logs/cron-ai-manager.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
}

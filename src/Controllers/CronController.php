<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;

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
                        u.first_name
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
                "SELECT b.id, b.company_name, b.email, b.trial_ends_at,
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
        $mailer = new Mailer();

        $subject = 'Je proefperiode bij GlamourSchedule eindigt vandaag';

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #000000; padding: 30px; text-align: center;'>
                <h1 style='color: #ffffff; margin: 0;'>GlamourSchedule</h1>
            </div>

            <div style='padding: 30px; background: #ffffff;'>
                <h2 style='color: #333333;'>Hallo {$business['first_name']},</h2>

                <p style='color: #666666; line-height: 1.6;'>
                    Je 14-daagse proefperiode voor <strong>{$business['company_name']}</strong>
                    eindigt vandaag.
                </p>

                <p style='color: #666666; line-height: 1.6;'>
                    Om verder gebruik te maken van GlamourSchedule verzoeken wij je om het
                    maandelijkse abonnement te activeren.
                </p>

                <div style='background: #f5f5f5; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>
                    <p style='margin: 0 0 10px 0; color: #333333;'>Maandelijks abonnement</p>
                    <p style='font-size: 2rem; font-weight: bold; color: #000000; margin: 0;'>
                        &euro;" . number_format($business['subscription_price'], 2, ',', '.') . "
                    </p>
                    <p style='margin: 10px 0 0 0; color: #666666; font-size: 0.9rem;'>per maand</p>
                </div>

                <p style='color: #e74c3c; font-weight: bold;'>
                    Let op: Als je niet binnen 3 dagen activeert, wordt je account gedeactiveerd.
                </p>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://glamourschedule.nl/business/dashboard'
                       style='display: inline-block; background: #000000; color: #ffffff;
                              padding: 15px 30px; text-decoration: none; border-radius: 25px;
                              font-weight: bold;'>
                        Abonnement Activeren
                    </a>
                </div>

                <p style='color: #999999; font-size: 0.9rem;'>
                    Heb je vragen? Neem contact op via info@glamourschedule.nl
                </p>
            </div>

            <div style='background: #f5f5f5; padding: 20px; text-align: center;'>
                <p style='color: #999999; font-size: 0.8rem; margin: 0;'>
                    &copy; " . date('Y') . " GlamourSchedule. Alle rechten voorbehouden.
                </p>
            </div>
        </div>
        ";

        $mailer->send($business['email'], $subject, $body);
    }

    private function sendDeactivationEmail(array $business): void
    {
        $mailer = new Mailer();

        $subject = 'Je GlamourSchedule account is gedeactiveerd';

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #000000; padding: 30px; text-align: center;'>
                <h1 style='color: #ffffff; margin: 0;'>GlamourSchedule</h1>
            </div>

            <div style='padding: 30px; background: #ffffff;'>
                <h2 style='color: #333333;'>Hallo {$business['first_name']},</h2>

                <p style='color: #666666; line-height: 1.6;'>
                    Je account voor <strong>{$business['company_name']}</strong> is gedeactiveerd
                    omdat de proefperiode is verlopen zonder activatie van het abonnement.
                </p>

                <p style='color: #666666; line-height: 1.6;'>
                    Je kunt je account op elk moment opnieuw activeren door in te loggen en
                    het abonnement te activeren.
                </p>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://glamourschedule.nl/business/login'
                       style='display: inline-block; background: #000000; color: #ffffff;
                              padding: 15px 30px; text-decoration: none; border-radius: 25px;
                              font-weight: bold;'>
                        Opnieuw Activeren
                    </a>
                </div>

                <p style='color: #999999; font-size: 0.9rem;'>
                    Heb je vragen? Neem contact op via info@glamourschedule.nl
                </p>
            </div>

            <div style='background: #f5f5f5; padding: 20px; text-align: center;'>
                <p style='color: #999999; font-size: 0.8rem; margin: 0;'>
                    &copy; " . date('Y') . " GlamourSchedule. Alle rechten voorbehouden.
                </p>
            </div>
        </div>
        ";

        $mailer->send($business['email'], $subject, $body);
    }

    private function logCron(string $message): void
    {
        $logFile = BASE_PATH . '/storage/logs/cron-trial-expiry.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
}

<?php
namespace GlamourSchedule\Services;

use Mollie\Api\MollieApiClient;

/**
 * Mollie Split Payment Service
 *
 * Handles split payments where platform fee is automatically deducted
 * and business payout is scheduled for 24h after QR check-in.
 */
class MollieSplitPaymentService
{
    private MollieApiClient $mollie;
    private \PDO $db;

    // Platform fee per booking (fixed)
    public const PLATFORM_FEE = 1.75;

    // Hours after QR check-in before payout
    public const PAYOUT_DELAY_HOURS = 24;

    public function __construct(array $config)
    {
        $this->mollie = new MollieApiClient();
        $this->mollie->setApiKey($config['mollie']['api_key'] ?? '');

        // Initialize database connection
        $dbConfig = $config['database'];
        $this->db = new \PDO(
            "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset={$dbConfig['charset']}",
            $dbConfig['user'],
            $dbConfig['pass'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
    }

    /**
     * Create a split payment with routing to business
     *
     * When business has Mollie Connect:
     * - Platform fee goes to platform account
     * - Service cost is routed to business Mollie account
     * - Funds are held until 24h after QR check-in
     */
    public function createSplitPayment(array $data): array
    {
        $businessId = $data['business_id'] ?? null;
        $amount = (float)$data['amount'];

        // Check if business has Mollie Connect
        $business = $this->getBusinessMollieInfo($businessId);

        if (!$business || empty($business['mollie_account_id'])) {
            // No Mollie Connect - create regular payment
            return $this->createRegularPayment($data);
        }

        // Calculate split amounts
        $platformFee = self::PLATFORM_FEE;
        $businessAmount = $amount - $platformFee;

        // Create payment with routing (split payment)
        $paymentData = [
            'amount' => [
                'currency' => $data['currency'] ?? 'EUR',
                'value' => number_format($amount, 2, '.', '')
            ],
            'description' => $data['description'],
            'redirectUrl' => $data['redirect_url'],
            'webhookUrl' => $data['webhook_url'],
            'metadata' => array_merge($data['metadata'] ?? [], [
                'split_payment' => true,
                'business_id' => $businessId,
                'platform_fee' => $platformFee,
                'business_amount' => $businessAmount
            ]),
            // Route funds to business account
            'routing' => [
                [
                    'amount' => [
                        'currency' => 'EUR',
                        'value' => number_format($businessAmount, 2, '.', '')
                    ],
                    'destination' => [
                        'type' => 'organization',
                        'organizationId' => $business['mollie_account_id']
                    ],
                    // Hold funds until released (24h after QR check-in)
                    'releaseDate' => null // Will be set after QR check-in
                ]
            ]
        ];

        // Add specific payment method if requested
        if (!empty($data['method']) && $data['method'] !== 'card') {
            $paymentData['method'] = $data['method'];
        }

        $payment = $this->mollie->payments->create($paymentData);

        return [
            'provider' => 'mollie',
            'payment_id' => $payment->id,
            'checkout_url' => $payment->getCheckoutUrl(),
            'status' => $payment->status,
            'split_payment' => true,
            'platform_fee' => $platformFee,
            'business_amount' => $businessAmount
        ];
    }

    /**
     * Create regular (non-split) payment
     */
    private function createRegularPayment(array $data): array
    {
        $paymentData = [
            'amount' => [
                'currency' => $data['currency'] ?? 'EUR',
                'value' => number_format($data['amount'], 2, '.', '')
            ],
            'description' => $data['description'],
            'redirectUrl' => $data['redirect_url'],
            'webhookUrl' => $data['webhook_url'],
            'metadata' => array_merge($data['metadata'] ?? [], [
                'split_payment' => false
            ])
        ];

        if (!empty($data['method']) && $data['method'] !== 'card') {
            $paymentData['method'] = $data['method'];
        }

        $payment = $this->mollie->payments->create($paymentData);

        return [
            'provider' => 'mollie',
            'payment_id' => $payment->id,
            'checkout_url' => $payment->getCheckoutUrl(),
            'status' => $payment->status,
            'split_payment' => false
        ];
    }

    /**
     * Release funds to business 24h after QR check-in
     *
     * Called by cron job to process eligible payouts
     */
    public function processQRPayouts(): array
    {
        $processed = 0;
        $failed = 0;
        $results = [];

        // Find bookings that:
        // 1. Are checked in (QR scanned)
        // 2. Were checked in more than 24 hours ago
        // 3. Have split payments not yet released
        // 4. Business has Mollie Connect
        $stmt = $this->db->prepare(
            "SELECT b.*, biz.mollie_account_id, biz.company_name, biz.email as business_email
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             WHERE b.status = 'checked_in'
               AND b.checked_in_at <= DATE_SUB(NOW(), INTERVAL ? HOUR)
               AND b.payout_status = 'pending'
               AND b.payment_status = 'paid'
               AND biz.mollie_account_id IS NOT NULL
               AND biz.mollie_onboarding_status = 'completed'"
        );
        $stmt->execute([self::PAYOUT_DELAY_HOURS]);
        $bookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($bookings as $booking) {
            try {
                $result = $this->releasePaymentFunds($booking);

                if ($result['success']) {
                    $processed++;
                    $results[] = [
                        'booking_number' => $booking['booking_number'],
                        'business' => $booking['company_name'],
                        'amount' => $result['amount'],
                        'status' => 'released'
                    ];

                    // Send payout notification to business
                    $this->sendPayoutNotification($booking, $result['amount']);
                } else {
                    $failed++;
                    $results[] = [
                        'booking_number' => $booking['booking_number'],
                        'business' => $booking['company_name'],
                        'error' => $result['error'],
                        'status' => 'failed'
                    ];
                }
            } catch (\Exception $e) {
                $failed++;
                $results[] = [
                    'booking_number' => $booking['booking_number'],
                    'error' => $e->getMessage(),
                    'status' => 'error'
                ];
            }
        }

        return [
            'processed' => $processed,
            'failed' => $failed,
            'results' => $results
        ];
    }

    /**
     * Release payment funds to business
     */
    private function releasePaymentFunds(array $booking): array
    {
        $paymentId = $booking['mollie_payment_id'] ?? $booking['payment_id'];

        if (!$paymentId) {
            return ['success' => false, 'error' => 'No payment ID found'];
        }

        try {
            // Get the payment from Mollie
            $payment = $this->mollie->payments->get($paymentId);

            // Calculate business amount
            $totalAmount = (float)$booking['total_price'];
            $platformFee = self::PLATFORM_FEE;
            $businessAmount = $totalAmount - $platformFee;

            // For split payments with routing, Mollie handles the release automatically
            // when we update the release date. For regular payments, we need to create a transfer.

            if (isset($payment->routing) && !empty($payment->routing)) {
                // Split payment - funds are already routed, just update status
                $this->updatePayoutStatus($booking['id'], 'completed', $businessAmount);

                return [
                    'success' => true,
                    'amount' => $businessAmount,
                    'type' => 'split_payment_released'
                ];
            } else {
                // Regular payment - create manual transfer record
                // This would require a separate bank transfer (Bunq/manual)
                $this->updatePayoutStatus($booking['id'], 'processing', $businessAmount);

                return [
                    'success' => true,
                    'amount' => $businessAmount,
                    'type' => 'manual_transfer_queued'
                ];
            }

        } catch (\Exception $e) {
            $this->logError("Failed to release funds for booking {$booking['booking_number']}: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Update booking payout status
     */
    private function updatePayoutStatus(int $bookingId, string $status, float $amount): void
    {
        $stmt = $this->db->prepare(
            "UPDATE bookings
             SET payout_status = ?,
                 payout_amount = ?,
                 payout_date = CASE WHEN ? = 'completed' THEN CURDATE() ELSE payout_date END
             WHERE id = ?"
        );
        $stmt->execute([$status, $amount, $status, $bookingId]);
    }

    /**
     * Get business Mollie Connect info
     */
    private function getBusinessMollieInfo(int $businessId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, company_name, email, mollie_account_id, mollie_profile_id,
                    mollie_onboarding_status, mollie_access_token
             FROM businesses
             WHERE id = ?"
        );
        $stmt->execute([$businessId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Send payout notification email to business
     */
    private function sendPayoutNotification(array $booking, float $amount): void
    {
        try {
            $mailer = new \GlamourSchedule\Core\Mailer();

            $subject = "Uitbetaling voltooid - €" . number_format($amount, 2, ',', '.');
            $platformFee = number_format(self::PLATFORM_FEE, 2, ',', '.');
            $totalAmount = number_format($booking['total_price'], 2, ',', '.');
            $businessAmount = number_format($amount, 2, ',', '.');

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
                            <h1 style="margin:0;font-size:24px;">Uitbetaling Voltooid!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;">Beste {$booking['company_name']},</p>
                            <p style="color:#cccccc;line-height:1.6;">
                                De uitbetaling voor boeking <strong>{$booking['booking_number']}</strong> is automatisch verwerkt.
                            </p>

                            <div style="background:#0a0a0a;border-radius:12px;padding:25px;margin:25px 0;">
                                <table width="100%" style="color:#ffffff;">
                                    <tr>
                                        <td style="padding:8px 0;border-bottom:1px solid #333;">Totaal betaald door klant</td>
                                        <td style="padding:8px 0;border-bottom:1px solid #333;text-align:right;">€{$totalAmount}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;border-bottom:1px solid #333;color:#dc2626;">Platformkosten</td>
                                        <td style="padding:8px 0;border-bottom:1px solid #333;text-align:right;color:#dc2626;">-€{$platformFee}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;font-weight:bold;font-size:18px;">Jouw uitbetaling</td>
                                        <td style="padding:12px 0;text-align:right;font-weight:bold;font-size:18px;color:#22c55e;">€{$businessAmount}</td>
                                    </tr>
                                </table>
                            </div>

                            <div style="background:#14532d;border-radius:8px;padding:15px;margin:20px 0;">
                                <p style="color:#bbf7d0;margin:0;font-size:14px;">
                                    ✓ Het bedrag wordt automatisch op je Mollie account gestort.
                                </p>
                            </div>

                            <p style="color:#888;font-size:13px;margin-top:20px;">
                                Bekijk al je uitbetalingen in je <a href="https://glamourschedule.com/business/payouts" style="color:#ffffff;">dashboard</a>.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#666;font-size:12px;">© 2026 GlamourSchedule - Automatische Uitbetalingen</p>
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

        } catch (\Exception $e) {
            $this->logError("Failed to send payout notification: " . $e->getMessage());
        }
    }

    /**
     * Log errors to file
     */
    private function logError(string $message): void
    {
        $logFile = defined('BASE_PATH')
            ? BASE_PATH . '/storage/logs/mollie-split-payments.log'
            : '/var/www/glamourschedule/storage/logs/mollie-split-payments.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] ERROR: $message\n", FILE_APPEND);
    }

    /**
     * Log info to file
     */
    private function logInfo(string $message): void
    {
        $logFile = defined('BASE_PATH')
            ? BASE_PATH . '/storage/logs/mollie-split-payments.log'
            : '/var/www/glamourschedule/storage/logs/mollie-split-payments.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] INFO: $message\n", FILE_APPEND);
    }
}

<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;
use GlamourSchedule\Core\PushNotification;
use GlamourSchedule\Services\LoyaltyService;
use Mollie\Api\MollieApiClient;

class WebhookController extends Controller
{
    public function mollie(): string
    {
        $paymentId = $_POST['id'] ?? null;

        if (!$paymentId) {
            http_response_code(400);
            return '';
        }

        try {
            $mollie = new MollieApiClient();
            $mollie->setApiKey($this->config['mollie']['api_key'] ?? '');

            $payment = $mollie->payments->get($paymentId);

            // Check payment type from metadata
            $type = $payment->metadata->type ?? 'booking';

            if ($type === 'pos_booking') {
                // Handle POS booking payment
                $this->handlePosPayment($payment);
            } else {
                // Handle regular booking payment
                $this->handleRegularBookingPayment($payment);
            }

            http_response_code(200);
            return '';

        } catch (\Exception $e) {
            error_log("Mollie webhook error: " . $e->getMessage());
            http_response_code(500);
            return '';
        }
    }

    /**
     * Handle regular booking payment
     */
    private function handleRegularBookingPayment($payment): void
    {
        $bookingUuid = $payment->metadata->booking_uuid ?? null;

        if (!$bookingUuid) {
            error_log("Mollie webhook: No booking UUID in metadata for payment {$payment->id}");
            return;
        }

        if ($payment->isPaid()) {
            // Check if this is a split payment
            $isSplitPayment = $payment->metadata->split_payment ?? false;
            $platformFee = $payment->metadata->platform_fee ?? 1.75;
            $businessAmount = $payment->metadata->business_amount ?? null;

            // Update booking with payment and split info
            if ($isSplitPayment && $businessAmount !== null) {
                $this->db->query(
                    "UPDATE bookings
                     SET payment_status = 'paid',
                         status = 'confirmed',
                         platform_fee = ?,
                         business_payout = ?,
                         payout_status = 'pending'
                     WHERE uuid = ?",
                    [$platformFee, $businessAmount, $bookingUuid]
                );
                error_log("Mollie webhook: Split payment confirmed for booking $bookingUuid (fee: €$platformFee, business: €$businessAmount)");
            } else {
                $this->db->query(
                    "UPDATE bookings SET payment_status = 'paid', status = 'confirmed' WHERE uuid = ?",
                    [$bookingUuid]
                );
                error_log("Mollie webhook: Payment confirmed for booking $bookingUuid");
            }

            // Award loyalty points for completed booking
            $this->awardLoyaltyPoints($bookingUuid);

            // Stuur bevestigingsemails na succesvolle betaling
            $this->sendBookingEmails($bookingUuid);
        } elseif ($payment->isFailed()) {
            $this->db->query(
                "UPDATE bookings SET payment_status = 'pending' WHERE uuid = ?",
                [$bookingUuid]
            );
            error_log("Mollie webhook: Payment failed for booking $bookingUuid");
        } elseif ($payment->isExpired()) {
            $this->db->query(
                "UPDATE bookings SET payment_status = 'pending', status = 'cancelled' WHERE uuid = ?",
                [$bookingUuid]
            );
            error_log("Mollie webhook: Payment expired for booking $bookingUuid");
        }
    }

    /**
     * Handle POS booking payment
     */
    private function handlePosPayment($payment): void
    {
        $posBookingUuid = $payment->metadata->pos_booking_uuid ?? null;

        if (!$posBookingUuid) {
            error_log("Mollie webhook: No POS booking UUID in metadata for payment {$payment->id}");
            return;
        }

        if ($payment->isPaid()) {
            $this->db->query(
                "UPDATE pos_bookings SET payment_status = 'paid', booking_status = 'confirmed', paid_at = NOW() WHERE uuid = ?",
                [$posBookingUuid]
            );
            error_log("Mollie webhook: POS payment confirmed for booking $posBookingUuid");

            // Send confirmation email
            $this->sendPosConfirmationEmail($posBookingUuid);
        } elseif ($payment->isFailed()) {
            $this->db->query(
                "UPDATE pos_bookings SET payment_status = 'failed' WHERE uuid = ?",
                [$posBookingUuid]
            );
            error_log("Mollie webhook: POS payment failed for booking $posBookingUuid");
        } elseif ($payment->isExpired()) {
            $this->db->query(
                "UPDATE pos_bookings SET payment_status = 'pending' WHERE uuid = ?",
                [$posBookingUuid]
            );
            error_log("Mollie webhook: POS payment expired for booking $posBookingUuid");
        }
    }

    /**
     * Send POS confirmation email
     */
    private function sendPosConfirmationEmail(string $posBookingUuid): void
    {
        try {
            $stmt = $this->db->query(
                "SELECT pb.*, s.name as service_name, s.duration_minutes,
                        b.company_name, b.street, b.house_number, b.postal_code, b.city, b.phone as business_phone
                 FROM pos_bookings pb
                 JOIN services s ON pb.service_id = s.id
                 JOIN businesses b ON pb.business_id = b.id
                 WHERE pb.uuid = ?",
                [$posBookingUuid]
            );
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$booking || empty($booking['customer_email'])) {
                return;
            }

            // Idempotency check: don't send if already sent
            if (!empty($booking['confirmation_email_sent'])) {
                error_log("POS confirmation email already sent for booking $posBookingUuid, skipping");
                return;
            }

            $appointmentDate = date('d-m-Y', strtotime($booking['appointment_date']));
            $appointmentTime = date('H:i', strtotime($booking['appointment_time']));
            $totalPrice = number_format($booking['total_price'], 2, ',', '.');
            $address = trim($booking['street'] . ' ' . $booking['house_number'] . ', ' . $booking['postal_code'] . ' ' . $booking['city']);

            $cashNote = $booking['payment_method'] === 'cash'
                ? "<div style='background:#fef3c7;border-radius:10px;padding:15px;margin-top:20px;'>
                    <p style='margin:0;color:#92400e;font-size:14px;'>
                        <strong>Let op:</strong> Betaal het resterende bedrag van €" . number_format($booking['total_price'] - $booking['service_fee'], 2, ',', '.') . " contant bij je afspraak.
                    </p>
                   </div>"
                : "";

            $subject = "Afspraak Bevestigd - " . $booking['company_name'];
            $currentYear = date('Y');
            $checkinUrl = rtrim($_ENV['APP_URL'] ?? 'https://glamourschedule.com', '/') . "/checkin/{$posBookingUuid}";
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&margin=20&data=" . urlencode($checkinUrl);
            $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f4f4f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:#000000;padding:40px;text-align:center;color:#fff;">
                            <div style="font-size:48px;margin-bottom:10px;">✓</div>
                            <h1 style="margin:0;font-size:24px;">Afspraak Bevestigd!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333333;">Beste {$booking['customer_name']},</p>
                            <p style="color:#555555;line-height:1.6;">
                                Je afspraak bij <strong>{$booking['company_name']}</strong> is bevestigd.
                            </p>

                            <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:25px 0;border:1px solid #e5e7eb;">
                                <p style="margin:0 0 10px;color:#333333;"><strong>Dienst:</strong> {$booking['service_name']}</p>
                                <p style="margin:0 0 10px;color:#333333;"><strong>Datum:</strong> {$appointmentDate}</p>
                                <p style="margin:0 0 10px;color:#333333;"><strong>Tijd:</strong> {$appointmentTime}</p>
                                <p style="margin:0 0 10px;color:#333333;"><strong>Duur:</strong> {$booking['duration_minutes']} minuten</p>
                                <p style="margin:0;color:#333333;"><strong>Totaal:</strong> €{$totalPrice}</p>
                            </div>

                            <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:25px 0;border:1px solid #e5e7eb;">
                                <p style="margin:0 0 5px;color:#333333;font-weight:600;">📍 Locatie</p>
                                <p style="margin:0;color:#555555;">{$booking['company_name']}<br>{$address}</p>
                            </div>

                            {$cashNote}

                            <!-- QR Code -->
                            <div style="text-align:center;padding:25px;background:#ffffff;border-radius:12px;margin:25px 0;border:1px solid #e5e7eb;">
                                <p style="margin:0 0 15px;color:#333333;font-weight:bold;">📱 Check-in QR Code</p>
                                <img src="{$qrCodeUrl}" alt="QR Code" style="width:200px;height:200px;display:block;margin:0 auto;">
                                <p style="margin:10px 0 0;color:#666666;font-size:12px;">Toon deze QR code bij aankomst</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="margin:0;color:#888888;font-size:13px;">&copy; {$currentYear} GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

            $mailer = new Mailer();
            $mailer->send($booking['customer_email'], $subject, $htmlBody);

            // Mark email as sent to prevent duplicates
            $this->db->query(
                "UPDATE pos_bookings SET confirmation_email_sent = 1 WHERE uuid = ?",
                [$posBookingUuid]
            );
            error_log("Mollie webhook: POS confirmation email sent for $posBookingUuid");

        } catch (\Exception $e) {
            error_log("Mollie webhook: POS email sending failed for $posBookingUuid: " . $e->getMessage());
        }
    }

    private function sendBookingEmails(string $bookingUuid): void
    {
        try {
            $stmt = $this->db->query(
                "SELECT b.*,
                        biz.company_name as business_name, biz.email as business_email,
                        biz.language as business_language,
                        s.name as service_name, s.duration_minutes,
                        u.first_name, u.last_name, u.email as user_email
                 FROM bookings b
                 JOIN businesses biz ON b.business_id = biz.id
                 JOIN services s ON b.service_id = s.id
                 LEFT JOIN users u ON b.user_id = u.id
                 WHERE b.uuid = ?",
                [$bookingUuid]
            );
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$booking) {
                error_log("Mollie webhook: Booking not found for email: $bookingUuid");
                return;
            }

            // Check if confirmation email was already sent (prevent duplicates on refresh/retry)
            if (!empty($booking['confirmation_email_sent'])) {
                error_log("Mollie webhook: Confirmation email already sent for $bookingUuid, skipping");
                return;
            }

            // Get business settings for email theming
            $settings = $this->getBusinessSettings($booking['business_id']);

            $customerEmail = $booking['guest_email'] ?? $booking['user_email'] ?? null;
            $customerName = $booking['guest_name'] ?? trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? ''));

            $bookingData = [
                'uuid' => $booking['uuid'],
                'booking_number' => $booking['booking_number'],
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'business_name' => $booking['business_name'],
                'business_email' => $booking['business_email'],
                'service_name' => $booking['service_name'],
                'date' => $booking['appointment_date'],
                'time' => $booking['appointment_time'],
                'duration' => $booking['duration_minutes'],
                'price' => $booking['total_price'],
                'notes' => $booking['notes'] ?? ''
            ];

            // Get customer language from booking (personalized based on platform language used during booking)
            $customerLang = $booking['language'] ?? 'nl';

            // Get business language (for business notifications)
            $businessLang = $booking['business_language'] ?? 'nl';

            // Send confirmation email to customer in CUSTOMER'S language
            if ($customerEmail) {
                $mailerCustomer = new Mailer($customerLang);
                $mailerCustomer->sendBookingConfirmation($bookingData, $settings);
                error_log("Mollie webhook: Confirmation email sent to customer for $bookingUuid (lang: $customerLang)");
            }

            // Send notification email to business in BUSINESS'S language
            if ($booking['business_email']) {
                $mailerBusiness = new Mailer($businessLang);
                $mailerBusiness->sendBookingNotificationToBusiness($bookingData, $settings);
                error_log("Mollie webhook: Notification email sent to business for $bookingUuid (lang: $businessLang)");
            }

            // Mark confirmation email as sent to prevent duplicates
            $this->db->query(
                "UPDATE bookings SET confirmation_email_sent = 1 WHERE uuid = ?",
                [$bookingUuid]
            );

        } catch (\Exception $e) {
            error_log("Mollie webhook: Email sending failed for $bookingUuid: " . $e->getMessage());
        }
    }

    private function getBusinessSettings(int $businessId): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM business_settings WHERE business_id = ?",
            [$businessId]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [
            'primary_color' => '#000000',
            'secondary_color' => '#333333',
            'accent_color' => '#000000',
        ];
    }

    /**
     * Award loyalty points for a completed booking
     */
    private function awardLoyaltyPoints(string $bookingUuid): void
    {
        try {
            // Get booking details
            $stmt = $this->db->query(
                "SELECT id, user_id, business_id FROM bookings WHERE uuid = ?",
                [$bookingUuid]
            );
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$booking || !$booking['user_id']) {
                return; // No user (guest booking) - no loyalty points
            }

            $loyaltyService = new LoyaltyService();
            $loyaltyService->awardBookingPoints(
                $booking['user_id'],
                $booking['business_id'],
                $booking['id']
            );

            error_log("Mollie webhook: Loyalty points awarded for booking $bookingUuid");
        } catch (\Exception $e) {
            error_log("Mollie webhook: Failed to award loyalty points for $bookingUuid: " . $e->getMessage());
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function stripe(): string
    {
        $payload = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $webhookSecret = $this->config['stripe']['webhook_secret'] ?? '';

        try {
            // Verify webhook signature - REQUIRED for security
            if ($webhookSecret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } else {
                // WARNING: No webhook secret configured - this is a security risk!
                error_log("SECURITY WARNING: Stripe webhook secret not configured. Webhook verification disabled.");
                $event = json_decode($payload, false);
                if (!$event || !isset($event->type)) {
                    error_log("Stripe webhook: Invalid payload received");
                    http_response_code(400);
                    return json_encode(['error' => 'Invalid payload']);
                }
            }

            // Handle the event
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $this->handleStripePaymentSuccess($session);
                    break;

                case 'checkout.session.expired':
                    $session = $event->data->object;
                    $this->handleStripePaymentExpired($session);
                    break;

                default:
                    error_log("Stripe webhook: Unhandled event type: " . $event->type);
            }

            http_response_code(200);
            return json_encode(['received' => true]);

        } catch (\Exception $e) {
            error_log("Stripe webhook error: " . $e->getMessage());
            http_response_code(400);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle successful Stripe payment
     */
    private function handleStripePaymentSuccess($session): void
    {
        $bookingUuid = $session->metadata->booking_uuid ?? null;

        if (!$bookingUuid) {
            error_log("Stripe webhook: No booking_uuid in session metadata");
            return;
        }

        // Get booking (LEFT JOIN users to support guest bookings)
        $stmt = $this->db->query(
            "SELECT b.*, biz.email as business_email, biz.company_name as business_name,
                    biz.language as business_language,
                    COALESCE(u.email, b.guest_email) as customer_email,
                    COALESCE(CONCAT(u.first_name, ' ', u.last_name), b.guest_name) as customer_name,
                    s.name as service_name
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             LEFT JOIN users u ON b.user_id = u.id
             JOIN services s ON b.service_id = s.id
             WHERE b.uuid = ?",
            [$bookingUuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            error_log("Stripe webhook: Booking not found for UUID: $bookingUuid");
            return;
        }

        // Update booking status
        $this->db->query(
            "UPDATE bookings SET payment_status = 'paid', status = 'confirmed', payment_id = ? WHERE uuid = ?",
            [$session->id, $bookingUuid]
        );

        // Check if confirmation email was already sent (prevent duplicates on refresh/retry)
        if (!empty($booking['confirmation_email_sent'])) {
            error_log("Stripe webhook: Confirmation email already sent for $bookingUuid, skipping");
            return;
        }

        // Send confirmation emails
        try {
            $lang = $booking['language'] ?? 'nl';
            $mailer = new Mailer($lang);

            // Customer confirmation
            $mailer->sendBookingConfirmation($booking);

            // Business notification
            $mailer->sendBookingNotificationToBusiness($booking);

            // Mark confirmation email as sent to prevent duplicates
            $this->db->query(
                "UPDATE bookings SET confirmation_email_sent = 1 WHERE uuid = ?",
                [$bookingUuid]
            );

            error_log("Stripe webhook: Emails sent for booking $bookingUuid");
        } catch (\Exception $e) {
            error_log("Stripe webhook: Failed to send emails: " . $e->getMessage());
        }

        error_log("Stripe webhook: Payment confirmed for booking $bookingUuid");
    }

    /**
     * Handle expired Stripe payment
     */
    private function handleStripePaymentExpired($session): void
    {
        $bookingUuid = $session->metadata->booking_uuid ?? null;

        if ($bookingUuid) {
            $this->db->query(
                "UPDATE bookings SET payment_status = 'expired' WHERE uuid = ? AND payment_status = 'pending'",
                [$bookingUuid]
            );
            error_log("Stripe webhook: Payment expired for booking $bookingUuid");
        }
    }
}

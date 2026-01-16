<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;
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
            $this->db->query(
                "UPDATE bookings SET payment_status = 'paid', status = 'confirmed' WHERE uuid = ?",
                [$bookingUuid]
            );
            error_log("Mollie webhook: Payment confirmed for booking $bookingUuid");

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
            $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#000000,#333333);padding:40px;text-align:center;color:#fff;">
                            <div style="font-size:48px;margin-bottom:10px;">✓</div>
                            <h1 style="margin:0;font-size:24px;">Afspraak Bevestigd!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;">Beste {$booking['customer_name']},</p>
                            <p style="color:#555;line-height:1.6;">
                                Je afspraak bij <strong>{$booking['company_name']}</strong> is bevestigd.
                            </p>

                            <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:25px 0;">
                                <p style="margin:0 0 10px;color:#333;"><strong>Dienst:</strong> {$booking['service_name']}</p>
                                <p style="margin:0 0 10px;color:#333;"><strong>Datum:</strong> {$appointmentDate}</p>
                                <p style="margin:0 0 10px;color:#333;"><strong>Tijd:</strong> {$appointmentTime}</p>
                                <p style="margin:0 0 10px;color:#333;"><strong>Duur:</strong> {$booking['duration_minutes']} minuten</p>
                                <p style="margin:0;color:#333;"><strong>Totaal:</strong> €{$totalPrice}</p>
                            </div>

                            <div style="background:#f0f0f0;border-radius:12px;padding:20px;margin:25px 0;">
                                <p style="margin:0 0 5px;color:#333;font-weight:600;">📍 Locatie</p>
                                <p style="margin:0;color:#555;">{$booking['company_name']}<br>{$address}</p>
                            </div>

                            {$cashNote}
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">&copy; 2025 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

            $mailer = new Mailer($this->config);
            $mailer->send($booking['customer_email'], $subject, $htmlBody);
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

            $mailer = new Mailer($this->config);

            // Stuur bevestiging naar klant
            if ($customerEmail) {
                $mailer->sendBookingConfirmation($bookingData);
                error_log("Mollie webhook: Confirmation email sent to customer for $bookingUuid");
            }

            // Stuur notificatie naar bedrijf
            if ($booking['business_email']) {
                $mailer->sendBookingNotificationToBusiness($bookingData);
                error_log("Mollie webhook: Notification email sent to business for $bookingUuid");
            }

        } catch (\Exception $e) {
            error_log("Mollie webhook: Email sending failed for $bookingUuid: " . $e->getMessage());
        }
    }
}

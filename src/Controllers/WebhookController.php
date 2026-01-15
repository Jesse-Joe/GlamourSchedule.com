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
            $bookingUuid = $payment->metadata->booking_uuid ?? null;

            if (!$bookingUuid) {
                error_log("Mollie webhook: No booking UUID in metadata for payment $paymentId");
                return '';
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

            http_response_code(200);
            return '';

        } catch (\Exception $e) {
            error_log("Mollie webhook error: " . $e->getMessage());
            http_response_code(500);
            return '';
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

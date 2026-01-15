<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use Mollie\Api\MollieApiClient;

class PaymentController extends Controller
{
    private MollieApiClient $mollie;

    public function __construct()
    {
        parent::__construct();
        $this->mollie = new MollieApiClient();
        $this->mollie->setApiKey($this->config['mollie']['api_key'] ?? '');
    }

    public function create(string $bookingUuid): string
    {
        $booking = $this->getBooking($bookingUuid);

        if (!$booking) {
            http_response_code(404);
            return $this->json(['error' => 'Boeking niet gevonden'], 404);
        }

        if ($booking['payment_status'] === 'paid') {
            return $this->redirect("/booking/$bookingUuid");
        }

        try {
            $payment = $this->mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($booking['total_price'], 2, '.', '')
                ],
                'description' => "Boeking {$booking['booking_number']} - {$booking['service_name']}",
                'redirectUrl' => "https://{$_SERVER['HTTP_HOST']}/payment/return/{$bookingUuid}",
                'webhookUrl' => "https://{$_SERVER['HTTP_HOST']}/api/webhooks/mollie",
                'metadata' => [
                    'booking_uuid' => $bookingUuid,
                    'booking_number' => $booking['booking_number']
                ]
            ]);

            // Store payment ID
            $this->db->query(
                "UPDATE bookings SET mollie_payment_id = ? WHERE uuid = ?",
                [$payment->id, $bookingUuid]
            );

            return $this->redirect($payment->getCheckoutUrl());

        } catch (\Exception $e) {
            error_log("Mollie payment error: " . $e->getMessage());
            return $this->view('pages/payment/error', [
                'pageTitle' => 'Betaling mislukt',
                'error' => 'Er is een fout opgetreden bij het starten van de betaling.'
            ]);
        }
    }

    public function returnUrl(string $bookingUuid): string
    {
        $booking = $this->getBooking($bookingUuid);

        if (!$booking) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => 'Niet gevonden']);
        }

        // Check payment status with Mollie
        if ($booking['mollie_payment_id']) {
            try {
                $payment = $this->mollie->payments->get($booking['mollie_payment_id']);

                if ($payment->isPaid()) {
                    $this->db->query(
                        "UPDATE bookings SET payment_status = 'paid', status = 'confirmed' WHERE uuid = ?",
                        [$bookingUuid]
                    );

                    return $this->view('pages/payment/success', [
                        'pageTitle' => 'Betaling geslaagd',
                        'booking' => $booking
                    ]);
                } elseif ($payment->isFailed() || $payment->isCanceled() || $payment->isExpired()) {
                    return $this->view('pages/payment/failed', [
                        'pageTitle' => 'Betaling mislukt',
                        'booking' => $booking,
                        'status' => $payment->status
                    ]);
                }
            } catch (\Exception $e) {
                error_log("Mollie status check error: " . $e->getMessage());
            }
        }

        // Payment still pending
        return $this->view('pages/payment/pending', [
            'pageTitle' => 'Betaling in behandeling',
            'booking' => $booking
        ]);
    }

    private function getBooking(string $uuid): ?array
    {
        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name, s.name as service_name
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             JOIN services s ON b.service_id = s.id
             WHERE b.uuid = ?",
            [$uuid]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}

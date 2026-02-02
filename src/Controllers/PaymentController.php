<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Services\HybridPaymentService;

class PaymentController extends Controller
{
    private HybridPaymentService $paymentService;

    public function __construct()
    {
        parent::__construct();
        $this->paymentService = new HybridPaymentService($this->config);
    }

    /**
     * Show payment method selection page
     */
    public function selectMethod(string $bookingUuid): string
    {
        $booking = $this->getBooking($bookingUuid);

        if (!$booking) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => $this->t('page_not_found')]);
        }

        if ($booking['payment_status'] === 'paid') {
            return $this->redirect("/booking/$bookingUuid");
        }

        // Get country from session or default to NL
        $country = $_SESSION['customer_country'] ?? 'NL';

        // Get available payment methods for this country
        $methods = $this->paymentService->getPaymentMethods($country, $booking['total_price']);

        return $this->view('pages/payment/select-method', [
            'pageTitle' => $this->t('select_payment_method'),
            'booking' => $booking,
            'methods' => $methods,
            'country' => $country,
            'stripeEnabled' => $this->paymentService->isStripeEnabled()
        ]);
    }

    /**
     * Create payment with selected method
     */
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

        // Get selected payment method
        $method = $_POST['method'] ?? $_GET['method'] ?? null;
        $country = $_SESSION['customer_country'] ?? 'NL';

        try {
            // Determine provider first
            $provider = $this->paymentService->getProvider($country, $method);

            $result = $this->paymentService->createPayment([
                'amount' => (float)$booking['total_price'],
                'currency' => 'EUR',
                'description' => "Boeking {$booking['booking_number']} - {$booking['service_name']}",
                'method' => $method,
                'country' => $country,
                'provider' => $provider,
                'redirect_url' => "https://{$_SERVER['HTTP_HOST']}/payment/return/{$bookingUuid}",
                'cancel_url' => "https://{$_SERVER['HTTP_HOST']}/booking/{$bookingUuid}",
                'webhook_url' => "https://{$_SERVER['HTTP_HOST']}/api/webhooks/{$provider}",
                'metadata' => [
                    'booking_uuid' => $bookingUuid,
                    'booking_number' => $booking['booking_number']
                ]
            ]);

            // Store payment info
            $this->db->query(
                "UPDATE bookings SET
                    payment_provider = ?,
                    payment_id = ?,
                    mollie_payment_id = CASE WHEN ? = 'mollie' THEN ? ELSE mollie_payment_id END
                 WHERE uuid = ?",
                [
                    $result['provider'],
                    $result['payment_id'],
                    $result['provider'],
                    $result['payment_id'],
                    $bookingUuid
                ]
            );

            return $this->redirect($result['checkout_url']);

        } catch (\Exception $e) {
            error_log("Payment error: " . $e->getMessage());
            return $this->view('pages/payment/error', [
                'pageTitle' => $this->t('page_payment_failed'),
                'error' => $this->t('payment_error_starting')
            ]);
        }
    }

    /**
     * Handle return from payment provider
     */
    public function returnUrl(string $bookingUuid): string
    {
        $booking = $this->getBooking($bookingUuid);

        if (!$booking) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => $this->t('page_not_found')]);
        }

        // Check for cancellation
        if (isset($_GET['cancelled'])) {
            return $this->view('pages/payment/cancelled', [
                'pageTitle' => $this->t('payment_cancelled'),
                'booking' => $booking
            ]);
        }

        // Determine provider and payment ID
        $provider = $booking['payment_provider'] ?? 'mollie';
        $paymentId = $booking['payment_id'] ?? $booking['mollie_payment_id'];

        // Handle Stripe session ID from URL
        if (isset($_GET['session_id'])) {
            $provider = 'stripe';
            $paymentId = $_GET['session_id'];
        }

        if ($paymentId) {
            try {
                $status = $this->paymentService->getPaymentStatus($paymentId, $provider);

                if ($status['paid']) {
                    $this->db->query(
                        "UPDATE bookings SET payment_status = 'paid', status = 'confirmed' WHERE uuid = ?",
                        [$bookingUuid]
                    );

                    // Send confirmation email
                    $this->sendBookingConfirmation($booking);

                    return $this->view('pages/payment/success', [
                        'pageTitle' => $this->t('payment_successful'),
                        'booking' => $booking
                    ]);
                } elseif ($status['failed'] || $status['cancelled'] || $status['expired']) {
                    return $this->view('pages/payment/failed', [
                        'pageTitle' => $this->t('payment_failed'),
                        'booking' => $booking,
                        'status' => $status['status']
                    ]);
                }
            } catch (\Exception $e) {
                error_log("Payment status check error: " . $e->getMessage());
            }
        }

        // Payment still pending
        return $this->view('pages/payment/pending', [
            'pageTitle' => $this->t('payment_pending'),
            'booking' => $booking
        ]);
    }

    /**
     * Get available payment methods API endpoint
     */
    public function getMethods(): string
    {
        $country = $_GET['country'] ?? 'NL';
        $amount = (float)($_GET['amount'] ?? 50);

        $methods = $this->paymentService->getPaymentMethods($country, $amount);

        return $this->json([
            'success' => true,
            'country' => $country,
            'methods' => $methods,
            'stripe_enabled' => $this->paymentService->isStripeEnabled(),
            'mollie_enabled' => $this->paymentService->isMollieEnabled()
        ]);
    }

    private function getBooking(string $uuid): ?array
    {
        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name, biz.email as business_email,
                    s.name as service_name, s.duration_minutes as duration,
                    u.email as user_email, u.first_name as user_first_name, u.last_name as user_last_name
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.uuid = ?",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($booking) {
            // Set customer_name and customer_email for display
            if (!empty($booking['user_id']) && !empty($booking['user_email'])) {
                $booking['customer_name'] = trim($booking['user_first_name'] . ' ' . $booking['user_last_name']);
                $booking['customer_email'] = $booking['user_email'];
            } else {
                $booking['customer_name'] = $booking['guest_name'] ?? '';
                $booking['customer_email'] = $booking['guest_email'] ?? '';
            }
        }

        return $booking ?: null;
    }

    private function sendBookingConfirmation(array $booking): void
    {
        try {
            // Get customer info - check user_id first, then guest info
            $customerEmail = $booking['guest_email'];
            $customerName = $booking['guest_name'] ?? 'Klant';

            if (!empty($booking['user_id'])) {
                $stmt = $this->db->query(
                    "SELECT email, first_name, last_name FROM users WHERE id = ?",
                    [$booking['user_id']]
                );
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($user) {
                    $customerEmail = $user['email'];
                    $customerName = trim($user['first_name'] . ' ' . $user['last_name']);
                }
            }

            // Prepare booking data with expected field names
            $bookingData = array_merge($booking, [
                'customer_email' => $customerEmail,
                'customer_name' => $customerName,
                'date' => $booking['appointment_date'],
                'time' => date('H:i', strtotime($booking['appointment_time'])),
                'price' => $booking['total_price']
            ]);

            $mailer = new \GlamourSchedule\Core\Mailer($_SESSION['lang'] ?? 'nl');
            $mailer->sendBookingConfirmation($bookingData);
            $mailer->sendBookingNotificationToBusiness($bookingData);
        } catch (\Exception $e) {
            error_log("Failed to send booking confirmation: " . $e->getMessage());
        }
    }

}

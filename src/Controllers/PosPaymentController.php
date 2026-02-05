<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

class PosPaymentController extends Controller
{
    /**
     * Toon de betaalpagina voor POS boeking
     */
    public function show(string $uuid): string
    {
        // Get booking
        $stmt = $this->db->query(
            "SELECT pb.*, s.name as service_name, s.description as service_description,
                    b.company_name, b.slug as business_slug, b.logo, b.email as business_email,
                    b.phone as business_phone, b.street, b.house_number, b.postal_code, b.city
             FROM pos_bookings pb
             JOIN services s ON pb.service_id = s.id
             JOIN businesses b ON pb.business_id = b.id
             WHERE pb.uuid = ?",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            return $this->view('pages/pos/not-found', [
                'pageTitle' => 'Boeking niet gevonden'
            ]);
        }

        // Check if already paid
        if ($booking['payment_status'] === 'paid') {
            return $this->view('pages/pos/already-paid', [
                'pageTitle' => 'Al Betaald',
                'booking' => $booking
            ]);
        }

        // Check if cancelled
        if ($booking['booking_status'] === 'cancelled') {
            return $this->view('pages/pos/cancelled', [
                'pageTitle' => 'Afspraak Geannuleerd',
                'booking' => $booking
            ]);
        }

        // Calculate payment amount
        $paymentAmount = $booking['payment_method'] === 'cash'
            ? (float)$booking['service_fee']
            : (float)$booking['total_price'];

        return $this->view('pages/pos/payment', [
            'pageTitle' => 'Afspraak Bevestigen',
            'booking' => $booking,
            'paymentAmount' => $paymentAmount,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Process payment - redirect to Mollie
     */
    public function process(string $uuid): string
    {
        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige sessie'];
            return $this->redirect('/pay/' . $uuid);
        }

        // Get booking
        $stmt = $this->db->query(
            "SELECT pb.*, b.company_name
             FROM pos_bookings pb
             JOIN businesses b ON pb.business_id = b.id
             WHERE pb.uuid = ? AND pb.payment_status != 'paid'",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Boeking niet gevonden of al betaald'];
            return $this->redirect('/pay/' . $uuid);
        }

        // Calculate payment amount
        $paymentAmount = $booking['payment_method'] === 'cash'
            ? (float)$booking['service_fee']
            : (float)$booking['total_price'];

        // Create Mollie payment
        $mollieApiKey = $this->config['mollie']['api_key'] ?? '';
        if (empty($mollieApiKey)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betalingssysteem niet beschikbaar'];
            return $this->redirect('/pay/' . $uuid);
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($mollieApiKey);

            $description = $booking['payment_method'] === 'cash'
                ? 'Reserveringskosten - ' . $booking['company_name']
                : 'Afspraak - ' . $booking['company_name'];

            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($paymentAmount, 2, '.', '')
                ],
                'description' => $description,
                'redirectUrl' => 'https://glamourschedule.nl/pay/' . $uuid . '/return',
                'webhookUrl' => 'https://glamourschedule.nl/api/webhooks/mollie',
                'method' => ['ideal', 'creditcard', 'bancontact', 'paypal'],
                'metadata' => [
                    'type' => 'pos_booking',
                    'pos_booking_uuid' => $uuid,
                    'business_id' => $booking['business_id'],
                    'payment_method' => $booking['payment_method']
                ]
            ]);

            // Store Mollie payment ID
            $this->db->query(
                "UPDATE pos_bookings SET mollie_payment_id = ? WHERE uuid = ?",
                [$payment->id, $uuid]
            );

            return $this->redirect($payment->getCheckoutUrl());

        } catch (\Exception $e) {
            error_log("POS Payment error: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => $this->t('error_payment_start_failed')];
            return $this->redirect('/pay/' . $uuid);
        }
    }

    /**
     * Handle payment return from Mollie
     */
    public function returnUrl(string $uuid): string
    {
        // Get booking
        $stmt = $this->db->query(
            "SELECT pb.*, b.company_name
             FROM pos_bookings pb
             JOIN businesses b ON pb.business_id = b.id
             WHERE pb.uuid = ?",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            return $this->redirect('/pay/' . $uuid);
        }

        // Check Mollie payment status
        if (!empty($booking['mollie_payment_id'])) {
            $mollieApiKey = $this->config['mollie']['api_key'] ?? '';
            if (!empty($mollieApiKey)) {
                try {
                    $mollie = new \Mollie\Api\MollieApiClient();
                    $mollie->setApiKey($mollieApiKey);
                    $payment = $mollie->payments->get($booking['mollie_payment_id']);

                    if ($payment->isPaid()) {
                        // Mark as paid
                        $this->db->query(
                            "UPDATE pos_bookings SET payment_status = 'paid', booking_status = 'confirmed', paid_at = NOW() WHERE uuid = ?",
                            [$uuid]
                        );

                        // Email is sent via webhook to prevent duplicates

                        return $this->redirect('/pay/' . $uuid . '/success');

                    } elseif ($payment->isFailed() || $payment->isCanceled() || $payment->isExpired()) {
                        $_SESSION['flash'] = ['type' => 'error', 'message' => $this->t('error_payment_failed')];
                        return $this->redirect('/pay/' . $uuid);
                    }
                } catch (\Exception $e) {
                    error_log("POS payment check error: " . $e->getMessage());
                }
            }
        }

        // Payment pending, show waiting message
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Betaling wordt verwerkt. Je ontvangt een bevestiging zodra de betaling is ontvangen.'];
        return $this->redirect('/pay/' . $uuid);
    }

    /**
     * Show success page
     */
    public function success(string $uuid): string
    {
        // Get booking
        $stmt = $this->db->query(
            "SELECT pb.*, s.name as service_name, s.duration_minutes,
                    b.company_name, b.slug as business_slug, b.logo,
                    b.street, b.house_number, b.postal_code, b.city, b.phone as business_phone
             FROM pos_bookings pb
             JOIN services s ON pb.service_id = s.id
             JOIN businesses b ON pb.business_id = b.id
             WHERE pb.uuid = ?",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            return $this->redirect('/');
        }

        return $this->view('pages/pos/success', [
            'pageTitle' => 'Afspraak Bevestigd',
            'booking' => $booking
        ]);
    }

    /**
     * Send confirmation email to customer
     */
    private function sendConfirmationEmail(array $booking): void
    {
        if (empty($booking['customer_email'])) return;

        // Get full booking details
        $stmt = $this->db->query(
            "SELECT pb.*, s.name as service_name, s.duration_minutes,
                    b.company_name, b.street, b.house_number, b.postal_code, b.city, b.phone as business_phone
             FROM pos_bookings pb
             JOIN services s ON pb.service_id = s.id
             JOIN businesses b ON pb.business_id = b.id
             WHERE pb.uuid = ?",
            [$booking['uuid']]
        );
        $fullBooking = $stmt->fetch(\PDO::FETCH_ASSOC);

        $appointmentDate = date('d-m-Y', strtotime($fullBooking['appointment_date']));
        $appointmentTime = date('H:i', strtotime($fullBooking['appointment_time']));
        $totalPrice = number_format($fullBooking['total_price'], 2, ',', '.');
        $address = trim($fullBooking['street'] . ' ' . $fullBooking['house_number'] . ', ' . $fullBooking['postal_code'] . ' ' . $fullBooking['city']);

        $cashNote = $fullBooking['payment_method'] === 'cash'
            ? "<div style='background:#fef3c7;border-radius:10px;padding:15px;margin-top:20px;'>
                <p style='margin:0;color:#92400e;font-size:14px;'>
                    <strong>Let op:</strong> Betaal het resterende bedrag van €" . number_format($fullBooking['total_price'] - $fullBooking['service_fee'], 2, ',', '.') . " contant bij je afspraak.
                </p>
               </div>"
            : "";

        $subject = "Afspraak Bevestigd - " . $fullBooking['company_name'];
        $currentYear = date('Y');
        $checkinUrl = rtrim($_ENV['APP_URL'] ?? 'https://glamourschedule.com', '/') . "/checkin/{$booking['uuid']}";
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
                            <p style="font-size:18px;color:#333333;">Beste {$fullBooking['customer_name']},</p>
                            <p style="color:#555555;line-height:1.6;">
                                Je afspraak bij <strong>{$fullBooking['company_name']}</strong> is bevestigd. Hieronder vind je de details:
                            </p>

                            <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:25px 0;border:1px solid #e5e7eb;">
                                <p style="margin:0 0 10px;color:#333333;"><strong>Dienst:</strong> {$fullBooking['service_name']}</p>
                                <p style="margin:0 0 10px;color:#333333;"><strong>Datum:</strong> {$appointmentDate}</p>
                                <p style="margin:0 0 10px;color:#333333;"><strong>Tijd:</strong> {$appointmentTime}</p>
                                <p style="margin:0 0 10px;color:#333333;"><strong>Duur:</strong> {$fullBooking['duration_minutes']} minuten</p>
                                <p style="margin:0;color:#333333;"><strong>Totaal:</strong> €{$totalPrice}</p>
                            </div>

                            <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:25px 0;border:1px solid #e5e7eb;">
                                <p style="margin:0 0 5px;color:#333333;font-weight:600;">📍 Locatie</p>
                                <p style="margin:0;color:#555555;">{$fullBooking['company_name']}<br>{$address}</p>
                            </div>

                            {$cashNote}

                            <!-- QR Code -->
                            <div style="text-align:center;padding:25px;background:#ffffff;border-radius:12px;margin:25px 0;border:1px solid #e5e7eb;">
                                <p style="margin:0 0 15px;color:#333333;font-weight:bold;">📱 Check-in QR Code</p>
                                <img src="{$qrCodeUrl}" alt="QR Code" style="width:200px;height:200px;display:block;margin:0 auto;">
                                <p style="margin:10px 0 0;color:#666666;font-size:12px;">Toon deze QR code bij aankomst</p>
                            </div>

                            <p style="color:#999999;font-size:13px;margin-top:30px;text-align:center;">
                                Tot ziens bij {$fullBooking['company_name']}!
                            </p>
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

        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($fullBooking['customer_email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send POS confirmation email: " . $e->getMessage());
        }
    }
}

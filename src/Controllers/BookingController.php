<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;
use GlamourSchedule\Core\PushNotification;

class BookingController extends Controller
{
    public function create(string $businessSlug): string
    {
        $business = $this->getBusinessBySlug($businessSlug);
        if (!$business) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => 'Niet gevonden']);
        }

        $services = $this->getServices($business['id']);
        $selectedService = $_GET['service'] ?? null;

        // Get employees for BV businesses
        $employees = [];
        if (($business['business_type'] ?? 'eenmanszaak') === 'bv') {
            $employees = $this->getEmployees($business['id']);
        }

        return $this->view('pages/booking/create', [
            'pageTitle' => 'Boeken bij ' . $business['name'],
            'business' => $business,
            'services' => $services,
            'selectedService' => $selectedService,
            'employees' => $employees
        ]);
    }

    public function store(string $businessSlug): string
    {
        $business = $this->getBusinessBySlug($businessSlug);
        if (!$business) {
            return $this->json(['error' => 'Bedrijf niet gevonden'], 404);
        }

        if (!$this->verifyCsrf()) {
            return $this->json(['error' => 'Ongeldige aanvraag'], 400);
        }

        $serviceId = (int)($_POST['service_id'] ?? 0);
        $employeeId = (int)($_POST['employee_id'] ?? 0) ?: null;
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $notes = trim($_POST['notes'] ?? '');
        $acceptTerms = isset($_POST['accept_terms']);

        // Guest info
        $guestName = trim($_POST['guest_name'] ?? '');
        $guestEmail = trim($_POST['guest_email'] ?? '');
        $guestPhone = trim($_POST['guest_phone'] ?? '');

        // Validate terms acceptance
        if (!$acceptTerms) {
            return $this->view('pages/booking/create', [
                'pageTitle' => 'Boeken bij ' . $business['name'],
                'business' => $business,
                'services' => $this->getServices($business['id']),
                'error' => 'U moet akkoord gaan met de algemene voorwaarden om te boeken.'
            ]);
        }

        $service = $this->getServiceById($serviceId);
        if (!$service || $service['business_id'] != $business['id']) {
            return $this->view('pages/booking/create', [
                'pageTitle' => 'Boeken bij ' . $business['name'],
                'business' => $business,
                'services' => $this->getServices($business['id']),
                'error' => 'Ongeldige dienst geselecteerd'
            ]);
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId && !$guestEmail) {
            return $this->view('pages/booking/create', [
                'pageTitle' => 'Boeken bij ' . $business['name'],
                'business' => $business,
                'services' => $this->getServices($business['id']),
                'error' => 'Log in of vul je gegevens in'
            ]);
        }

        // Check if time slot is available (consider employee if BV business)
        if (!$this->isTimeSlotAvailable($business['id'], $date, $time, $service['duration_minutes'], $employeeId)) {
            return $this->view('pages/booking/create', [
                'pageTitle' => 'Boeken bij ' . $business['name'],
                'business' => $business,
                'services' => $this->getServices($business['id']),
                'employees' => ($business['business_type'] ?? 'eenmanszaak') === 'bv' ? $this->getEmployees($business['id']) : [],
                'error' => 'Dit tijdslot is helaas niet meer beschikbaar. Kies een andere tijd.'
            ]);
        }

        $uuid = $this->generateUuid();
        $bookingNumber = 'GS' . strtoupper(substr(md5($uuid), 0, 8));
        $servicePrice = $service['sale_price'] ?? $service['price'];
        // Admin fee wordt van bedrijf ingehouden, niet bij klant opgeteld
        $adminFee = 1.75;
        $totalPrice = $servicePrice; // Klant betaalt alleen de dienst prijs
        $qrCodeHash = hash('sha256', $uuid . $bookingNumber);

        $this->db->query(
            "INSERT INTO bookings (uuid, booking_number, business_id, employee_id, user_id, service_id,
             guest_name, guest_email, guest_phone, appointment_date, appointment_time,
             duration_minutes, service_price, admin_fee, total_price, qr_code_hash, customer_notes,
             terms_accepted_at, terms_version, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), '1.0', 'pending')",
            [
                $uuid, $bookingNumber, $business['id'], $employeeId, $userId, $serviceId,
                $guestName ?: null, $guestEmail ?: null, $guestPhone ?: null,
                $date, $time, $service['duration_minutes'],
                $servicePrice, $adminFee, $totalPrice, $qrCodeHash, $notes ?: null
            ]
        );

        // Send confirmation emails
        $customerEmail = $guestEmail ?: $this->getUserEmail($userId);
        $customerName = $guestName ?: $this->getUserName($userId);
        $customerPhone = $guestPhone ?: $this->getUserPhone($userId);

        $mailer = new Mailer();
        $bookingData = [
            'uuid' => $uuid,
            'booking_number' => $bookingNumber,
            'customer_name' => $customerName ?: 'Klant',
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone ?: '-',
            'business_name' => $business['name'],
            'business_email' => $business['email'],
            'service_name' => $service['name'],
            'date' => $date,
            'time' => $time,
            'duration' => $service['duration_minutes'],
            'price' => $totalPrice,
            'notes' => $notes
        ];

        // Email wordt pas verstuurd na betaling (via Mollie webhook)

        // Schedule 24-hour reminder
        $this->scheduleReminder($date, $time, $uuid);

        // Send push notification to business owner
        try {
            $push = new PushNotification();
            $push->notifyNewBooking([
                'business_id' => $business['id'],
                'guest_name' => $guestName,
                'customer_name' => $customerName,
                'service_name' => $service['name'],
                'appointment_date' => $date,
                'appointment_time' => $time
            ]);
        } catch (\Exception $e) {
            error_log('Push notification failed: ' . $e->getMessage());
        }

        return $this->redirect("/booking/$uuid");
    }

    public function show(string $uuid): string
    {
        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name, biz.street as address, biz.city,
                    biz.phone as business_phone, biz.slug as business_slug,
                    s.name as service_name,
                    u.first_name, u.last_name, u.email as user_email
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.uuid = ?",
            [$uuid]
        );

        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => 'Boeking niet gevonden']);
        }

        return $this->view('pages/booking/show', [
            'pageTitle' => 'Boeking #' . $booking['booking_number'],
            'booking' => $booking
        ]);
    }

    public function cancel(string $uuid): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect("/booking/$uuid?error=csrf");
        }

        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name, biz.email as business_email
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             WHERE b.uuid = ?",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            return $this->redirect("/?error=not_found");
        }

        $userId = $_SESSION['user_id'] ?? null;
        // Allow cancel if: logged in user owns booking, OR guest email matches
        $canCancel = ($booking['user_id'] && $booking['user_id'] == $userId) ||
                     (!$booking['user_id'] && $booking['guest_email']);

        if (!$canCancel && $booking['user_id']) {
            return $this->redirect("/booking/$uuid?error=unauthorized");
        }

        // Check if already cancelled
        if ($booking['status'] === 'cancelled') {
            return $this->redirect("/booking/$uuid");
        }

        // Calculate if within 24 hours
        $appointmentDateTime = new \DateTime($booking['appointment_date'] . ' ' . $booking['appointment_time']);
        $now = new \DateTime();
        $hoursUntilAppointment = ($appointmentDateTime->getTimestamp() - $now->getTimestamp()) / 3600;
        $isLateCancel = $hoursUntilAppointment <= 24 && $hoursUntilAppointment > 0;

        // Calculate refund amounts
        $refundAmount = $booking['total_price'];
        $businessFee = 0;

        if ($isLateCancel && $booking['payment_status'] === 'paid') {
            // 50% goes to business on late cancellation
            $businessFee = round($booking['total_price'] / 2, 2);
            $refundAmount = $booking['total_price'] - $businessFee;
        }

        // Update booking status
        $this->db->query(
            "UPDATE bookings SET
                status = 'cancelled',
                cancelled_at = NOW(),
                cancellation_fee = ?,
                refund_amount = ?
             WHERE uuid = ?",
            [$businessFee, $refundAmount, $uuid]
        );

        // Send cancellation email to customer
        $customerEmail = $booking['guest_email'] ?? $this->getUserEmail($booking['user_id']);
        if ($customerEmail) {
            $this->sendCancellationEmail($booking, $customerEmail, $refundAmount, $businessFee, $isLateCancel);
        }

        // Notify business of cancellation
        if ($booking['business_email']) {
            $this->sendBusinessCancellationNotice($booking, $businessFee, $isLateCancel);
        }

        // Check waitlist and notify first person
        $this->notifyWaitlistForCancellation($booking);

        return $this->redirect("/booking/$uuid");
    }

    /**
     * Send cancellation confirmation email to customer
     */
    private function sendCancellationEmail(array $booking, string $email, float $refundAmount, float $businessFee, bool $isLateCancel): void
    {
        $refundFormatted = number_format($refundAmount, 2, ',', '.');
        $feeFormatted = number_format($businessFee, 2, ',', '.');

        $feeNotice = '';
        if ($isLateCancel && $businessFee > 0) {
            $feeNotice = <<<HTML
<div style="background:#f5f5f5;border-radius:12px;padding:20px;margin:25px 0;text-align:center;">
    <p style="margin:0;color:#000000;font-weight:600;">
        <i>⚠️</i> Annulering binnen 24 uur
    </p>
    <p style="margin:10px 0 0;color:#dc2626;">
        €{$feeFormatted} gaat naar {$booking['business_name']}
    </p>
</div>
HTML;
        }

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
                        <td style="background:linear-gradient(135deg,#333333,#dc2626);padding:40px;text-align:center;color:#fff;">
                            <div style="font-size:48px;margin-bottom:10px;">✕</div>
                            <h1 style="margin:0;font-size:24px;">Boeking geannuleerd</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;">Je boeking is geannuleerd</p>
                            <div style="background:#fafafa;border-radius:12px;padding:20px;margin:25px 0;">
                                <p style="margin:0;color:#666;"><strong>Boeking:</strong> #{$booking['booking_number']}</p>
                                <p style="margin:10px 0 0;color:#666;"><strong>Salon:</strong> {$booking['business_name']}</p>
                                <p style="margin:10px 0 0;color:#666;"><strong>Datum:</strong> {$booking['appointment_date']}</p>
                            </div>

                            {$feeNotice}

                            <div style="background:#f0fdf4;border-radius:12px;padding:20px;margin:25px 0;text-align:center;">
                                <p style="margin:0;color:#000000;font-weight:600;">Terugbetaling</p>
                                <p style="margin:10px 0 0;color:#000000;font-size:1.5rem;font-weight:700;">€{$refundFormatted}</p>
                                <p style="margin:10px 0 0;color:#000000;font-size:0.9rem;">
                                    Wordt binnen 5-10 werkdagen teruggestort
                                </p>
                            </div>

                            <p style="text-align:center;margin-top:30px;">
                                <a href="https://glamourschedule.nl/search" style="display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:#fff;padding:14px 35px;border-radius:30px;text-decoration:none;font-weight:600;">
                                    Nieuwe afspraak maken
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">© 2025 GlamourSchedule</p>
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
            $mailer->send($email, "Boeking geannuleerd - #{$booking['booking_number']}", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send cancellation email: " . $e->getMessage());
        }
    }

    /**
     * Notify business of cancellation
     */
    private function sendBusinessCancellationNotice(array $booking, float $businessFee, bool $isLateCancel): void
    {
        $feeFormatted = number_format($businessFee, 2, ',', '.');
        $priceFormatted = number_format($booking['total_price'], 2, ',', '.');
        $customerName = $booking['guest_name'] ?? 'Klant';
        $serviceName = $booking['service_name'] ?? 'Dienst';
        $appointmentDate = date('d-m-Y', strtotime($booking['appointment_date']));
        $appointmentTime = date('H:i', strtotime($booking['appointment_time']));

        $feeNotice = '';
        if ($isLateCancel && $businessFee > 0) {
            $feeNotice = <<<HTML
<div style="background:#f0fdf4;border:2px solid #333333;border-radius:12px;padding:20px;margin:25px 0;text-align:center;">
    <p style="margin:0;color:#000000;font-weight:600;font-size:16px;">
        💰 Late annulering vergoeding
    </p>
    <p style="margin:10px 0 0;color:#000000;font-size:1.75rem;font-weight:700;">€{$feeFormatted}</p>
    <p style="margin:10px 0 0;color:#000000;font-size:0.9rem;">
        Dit bedrag wordt aan uw uitbetaling toegevoegd
    </p>
</div>
HTML;
        }

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#333333,#dc2626);padding:40px;text-align:center;color:#fff;">
                            <div style="width:80px;height:80px;background:rgba(255,255,255,0.2);border-radius:50%;margin:0 auto 15px;display:flex;align-items:center;justify-content:center;">
                                <span style="font-size:40px;">❌</span>
                            </div>
                            <h1 style="margin:0;font-size:26px;font-weight:700;">Boeking Geannuleerd</h1>
                            <p style="margin:10px 0 0;opacity:0.9;font-size:15px;">#{$booking['booking_number']}</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:17px;color:#333;margin:0 0 25px;text-align:center;">
                                Een klant heeft de volgende boeking geannuleerd
                            </p>

                            <!-- Booking Details -->
                            <div style="background:#fafafa;border-radius:16px;padding:25px;margin:0 0 25px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;">Klant</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:600;color:#1f2937;">{$customerName}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;border-top:1px solid #e5e7eb;">Dienst</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:600;color:#1f2937;border-top:1px solid #e5e7eb;">{$serviceName}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;border-top:1px solid #e5e7eb;">Datum</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:600;color:#1f2937;border-top:1px solid #e5e7eb;">{$appointmentDate}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;border-top:1px solid #e5e7eb;">Tijd</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:600;color:#1f2937;border-top:1px solid #e5e7eb;">{$appointmentTime}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;border-top:1px solid #e5e7eb;">Bedrag</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:700;color:#000000;font-size:16px;border-top:1px solid #e5e7eb;">€{$priceFormatted}</td>
                                    </tr>
                                </table>
                            </div>

                            {$feeNotice}

                            <!-- Time Slot Free Notice -->
                            <div style="background:#f5f5f5;border:2px solid #404040;border-radius:12px;padding:18px;margin:25px 0;text-align:center;">
                                <p style="margin:0;color:#000000;font-weight:600;font-size:15px;">
                                    🕐 Tijdslot is weer vrij
                                </p>
                                <p style="margin:8px 0 0;color:#000000;font-size:14px;">
                                    {$appointmentDate} om {$appointmentTime} is nu weer beschikbaar voor nieuwe boekingen
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <p style="text-align:center;margin-top:30px;">
                                <a href="https://glamourschedule.nl/business/calendar" style="display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:#fff;padding:16px 40px;border-radius:50px;text-decoration:none;font-weight:600;font-size:15px;box-shadow:0 4px 15px #rgba(0,0,0,0.3);">
                                    📅 Bekijk agenda
                                </a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#fafafa;padding:25px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="margin:0;color:#9ca3af;font-size:13px;">© 2025 GlamourSchedule - Beauty Booking Platform</p>
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
            $mailer->send($booking['business_email'], "Boeking geannuleerd - #{$booking['booking_number']}", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send business cancellation notice: " . $e->getMessage());
        }
    }

    private function getBusinessBySlug(string $slug): ?array
    {
        $stmt = $this->db->query(
            "SELECT *, company_name as name FROM businesses WHERE slug = ? AND status = 'active'",
            [$slug]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function getServices(int $businessId): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM services WHERE business_id = ? AND is_active = 1 ORDER BY sort_order, name",
            [$businessId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getServiceById(int $id): ?array
    {
        $stmt = $this->db->query("SELECT * FROM services WHERE id = ?", [$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function createGuestSession(string $name, string $email, string $phone): int
    {
        $sessionToken = bin2hex(random_bytes(32));
        $bookingData = json_encode([
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ]);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->db->query(
            "INSERT INTO guest_sessions (session_token, email, booking_data, ip_address, expires_at) VALUES (?, ?, ?, ?, ?)",
            [$sessionToken, $email, $bookingData, $_SERVER['REMOTE_ADDR'] ?? '', $expiresAt]
        );
        return $this->db->lastInsertId();
    }

    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * API endpoint to get available time slots
     */
    public function getAvailableTimes(string $businessSlug): string
    {
        header('Content-Type: application/json');

        $business = $this->getBusinessBySlug($businessSlug);
        if (!$business) {
            return json_encode(['error' => 'Business not found']);
        }

        $date = $_GET['date'] ?? '';
        $serviceId = (int)($_GET['service_id'] ?? 0);
        $employeeId = (int)($_GET['employee_id'] ?? 0) ?: null;

        if (!$date || !$serviceId) {
            return json_encode(['error' => 'Missing date or service_id']);
        }

        $service = $this->getServiceById($serviceId);
        if (!$service) {
            return json_encode(['error' => 'Service not found']);
        }

        $duration = $service['duration_minutes'];
        $bookedSlots = $this->getBookedSlots($business['id'], $date, $employeeId);

        // Generate all possible time slots (9:00 - 18:00, every 30 min)
        $availableSlots = [];
        for ($h = 9; $h <= 18; $h++) {
            for ($m = 0; $m < 60; $m += 30) {
                $time = sprintf('%02d:%02d', $h, $m);
                $endTime = date('H:i', strtotime($time) + ($duration * 60));

                // Skip if slot ends after closing time
                if ($endTime > '19:00') continue;

                // Check if slot overlaps with any booked slot
                $isAvailable = true;
                foreach ($bookedSlots as $booked) {
                    if ($this->timeSlotsOverlap($time, $duration, $booked['time'], $booked['duration'])) {
                        $isAvailable = false;
                        break;
                    }
                }

                $availableSlots[] = [
                    'time' => $time,
                    'available' => $isAvailable
                ];
            }
        }

        return json_encode(['slots' => $availableSlots]);
    }

    private function getBookedSlots(int $businessId, string $date, ?int $employeeId = null): array
    {
        if ($employeeId) {
            // For BV businesses with employee selection, only check that employee's bookings
            $stmt = $this->db->query(
                "SELECT appointment_time as time, duration_minutes as duration
                 FROM bookings
                 WHERE business_id = ? AND appointment_date = ? AND employee_id = ? AND status NOT IN ('cancelled', 'rejected')
                 ORDER BY appointment_time",
                [$businessId, $date, $employeeId]
            );
        } else {
            // For eenmanszaak or no employee selected, check all business bookings
            $stmt = $this->db->query(
                "SELECT appointment_time as time, duration_minutes as duration
                 FROM bookings
                 WHERE business_id = ? AND appointment_date = ? AND status NOT IN ('cancelled', 'rejected')
                 ORDER BY appointment_time",
                [$businessId, $date]
            );
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function isTimeSlotAvailable(int $businessId, string $date, string $time, int $duration, ?int $employeeId = null): bool
    {
        $bookedSlots = $this->getBookedSlots($businessId, $date, $employeeId);

        foreach ($bookedSlots as $booked) {
            if ($this->timeSlotsOverlap($time, $duration, $booked['time'], $booked['duration'])) {
                return false;
            }
        }
        return true;
    }

    private function getEmployees(int $businessId): array
    {
        $stmt = $this->db->query(
            "SELECT id, name, photo, color, bio FROM employees
             WHERE business_id = ? AND is_active = 1
             ORDER BY sort_order, name",
            [$businessId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function timeSlotsOverlap(string $time1, int $duration1, string $time2, int $duration2): bool
    {
        $start1 = strtotime($time1);
        $end1 = $start1 + ($duration1 * 60);
        $start2 = strtotime($time2);
        $end2 = $start2 + ($duration2 * 60);

        return ($start1 < $end2) && ($start2 < $end1);
    }

    private function getUserEmail(?int $userId): ?string
    {
        if (!$userId) return null;
        $stmt = $this->db->query("SELECT email FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user['email'] ?? null;
    }

    private function getUserName(?int $userId): ?string
    {
        if (!$userId) return null;
        $stmt = $this->db->query("SELECT first_name, last_name FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ? trim($user['first_name'] . ' ' . $user['last_name']) : null;
    }

    private function getUserPhone(?int $userId): ?string
    {
        if (!$userId) return null;
        $stmt = $this->db->query("SELECT phone FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user['phone'] ?? null;
    }

    private function scheduleReminder(string $date, string $time, string $uuid): void
    {
        try {
            // Get booking ID
            $stmt = $this->db->query("SELECT id FROM bookings WHERE uuid = ?", [$uuid]);
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$booking) return;

            // Calculate 24 hours before appointment
            $appointmentDateTime = new \DateTime("{$date} {$time}");
            $reminderDateTime = clone $appointmentDateTime;
            $reminderDateTime->modify('-24 hours');

            // Only schedule if reminder time is in the future
            $now = new \DateTime();
            if ($reminderDateTime > $now) {
                $this->db->query(
                    "INSERT INTO booking_reminders (booking_id, reminder_type, scheduled_for, status)
                     VALUES (?, '24h', ?, 'pending')",
                    [$booking['id'], $reminderDateTime->format('Y-m-d H:i:s')]
                );
            }
        } catch (\Exception $e) {
            error_log("Failed to schedule reminder: " . $e->getMessage());
        }
    }

    private function sendBookingConfirmation(array $data): bool
    {
        $to = $data['email'];
        $subject = "Boekingsbevestiging #{$data['booking_number']} - GlamourSchedule";

        $dateFormatted = date('d-m-Y', strtotime($data['date']));
        $priceFormatted = number_format($data['price'], 2, ',', '.');

        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #000000, #000000); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #fafafa; padding: 30px; border-radius: 0 0 10px 10px; }
                .booking-details { background: #ffffff; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .detail-row { display: flex; padding: 10px 0; border-bottom: 1px solid #eee; }
                .detail-label { font-weight: bold; width: 150px; color: #666; }
                .btn { display: inline-block; background: #000000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin:0'>Boekingsbevestiging</h1>
                    <p style='margin:10px 0 0'>#{$data['booking_number']}</p>
                </div>
                <div class='content'>
                    <p>Beste {$data['name']},</p>
                    <p>Bedankt voor je boeking! Hieronder vind je de details van je afspraak.</p>

                    <div class='booking-details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Salon:</span>
                            <span>{$data['business_name']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Behandeling:</span>
                            <span>{$data['service_name']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Datum:</span>
                            <span>{$dateFormatted}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Tijd:</span>
                            <span>{$data['time']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Prijs:</span>
                            <span>&euro;{$priceFormatted}</span>
                        </div>
                    </div>

                    <p style='text-align:center'>
                        <a href='https://glamourschedule.nl/booking/{$data['uuid']}' class='btn'>Bekijk je boeking</a>
                    </p>

                    <p style='margin-top:30px;font-size:14px;color:#666'>
                        Je kunt je afspraak bekijken, wijzigen of annuleren via bovenstaande link.
                    </p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " GlamourSchedule - Beauty & Wellness Booking Platform</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: GlamourSchedule <noreply@glamourschedule.nl>',
            'Reply-To: info@glamourschedule.nl',
            'X-Mailer: PHP/' . phpversion()
        ];

        return mail($to, $subject, $message, implode("\r\n", $headers));
    }

    /**
     * Show check-in page for business to confirm customer arrival
     */
    public function showCheckin(string $uuid): string
    {
        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name, biz.id as biz_id,
                    s.name as service_name, s.duration_minutes,
                    u.first_name, u.last_name, u.email as user_email
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.uuid = ?",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => 'Boeking niet gevonden']);
        }

        // Check if business is logged in
        $isBusinessOwner = isset($_SESSION['business_id']) && $_SESSION['business_id'] == $booking['biz_id'];

        return $this->view('pages/booking/checkin', [
            'pageTitle' => 'Check-in #' . $booking['booking_number'],
            'booking' => $booking,
            'isBusinessOwner' => $isBusinessOwner,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Process check-in confirmation
     */
    public function processCheckin(string $uuid): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect("/checkin/$uuid?error=csrf");
        }

        // Get booking
        $stmt = $this->db->query(
            "SELECT b.*, biz.id as biz_id FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             WHERE b.uuid = ?",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            return $this->redirect("/?error=not_found");
        }

        // Verify business owner
        if (!isset($_SESSION['business_id']) || $_SESSION['business_id'] != $booking['biz_id']) {
            return $this->redirect("/checkin/$uuid?error=unauthorized");
        }

        // Check if already checked in
        if ($booking['status'] === 'checked_in') {
            return $this->redirect("/checkin/$uuid?already=1");
        }

        // Check if booking is valid for check-in (confirmed and paid)
        if ($booking['payment_status'] !== 'paid') {
            return $this->redirect("/checkin/$uuid?error=not_paid");
        }

        // Update status to checked_in
        $this->db->query(
            "UPDATE bookings SET status = 'checked_in', checked_in_at = NOW() WHERE uuid = ?",
            [$uuid]
        );

        return $this->redirect("/checkin/$uuid?success=1");
    }

    /**
     * Show review form for a completed booking
     */
    public function showReview(string $uuid): string
    {
        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name, biz.id as business_id, biz.slug as business_slug,
                    s.name as service_name,
                    u.first_name, u.last_name
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.uuid = ?",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => 'Boeking niet gevonden']);
        }

        // Check if already reviewed
        $stmt = $this->db->query(
            "SELECT id FROM reviews WHERE booking_id = ?",
            [$booking['id']]
        );
        $existingReview = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->view('pages/booking/review', [
            'pageTitle' => 'Review - ' . $booking['business_name'],
            'booking' => $booking,
            'alreadyReviewed' => !empty($existingReview),
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Submit a review for a booking
     */
    public function submitReview(string $uuid): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect("/review/$uuid?error=csrf");
        }

        // Get booking
        $stmt = $this->db->query(
            "SELECT b.*, biz.company_name as business_name FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             WHERE b.uuid = ?",
            [$uuid]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            return $this->redirect("/?error=not_found");
        }

        // Check if already reviewed
        $stmt = $this->db->query(
            "SELECT id FROM reviews WHERE booking_id = ?",
            [$booking['id']]
        );
        if ($stmt->fetch()) {
            return $this->redirect("/review/$uuid?error=already_reviewed");
        }

        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            return $this->redirect("/review/$uuid?error=invalid_rating");
        }

        // Insert review
        $this->db->query(
            "INSERT INTO reviews (business_id, booking_id, user_id, rating, comment, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $booking['business_id'],
                $booking['id'],
                $booking['user_id'],
                $rating,
                $comment ?: null
            ]
        );

        // Update business average rating
        $this->updateBusinessRating($booking['business_id']);

        return $this->redirect("/review/$uuid?success=1");
    }

    /**
     * Update business average rating
     */
    private function updateBusinessRating(int $businessId): void
    {
        $stmt = $this->db->query(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
             FROM reviews WHERE business_id = ?",
            [$businessId]
        );
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($stats && $stats['total_reviews'] > 0) {
            $this->db->query(
                "UPDATE businesses SET rating = ?, total_reviews = ? WHERE id = ?",
                [round($stats['avg_rating'], 1), $stats['total_reviews'], $businessId]
            );
        }
    }

    // ==================== WAITLIST FUNCTIONS ====================

    /**
     * Add to waitlist when slot is full
     */
    public function addToWaitlist(string $businessSlug): string
    {
        $business = $this->getBusinessBySlug($businessSlug);
        if (!$business) {
            return $this->json(['error' => 'Bedrijf niet gevonden'], 404);
        }

        if (!$this->verifyCsrf()) {
            return $this->json(['error' => 'Ongeldige aanvraag'], 400);
        }

        $serviceId = (int)($_POST['service_id'] ?? 0);
        $date = $_POST['date'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $preferredTimeStart = $_POST['preferred_time_start'] ?? null;
        $preferredTimeEnd = $_POST['preferred_time_end'] ?? null;
        $notes = trim($_POST['notes'] ?? '');

        // Validate
        if (!$serviceId || !$date || !$name || !$email) {
            return $this->json(['error' => 'Vul alle verplichte velden in'], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Ongeldig e-mailadres'], 400);
        }

        $service = $this->getServiceById($serviceId);
        if (!$service || $service['business_id'] != $business['id']) {
            return $this->json(['error' => 'Ongeldige dienst'], 400);
        }

        // Check if already on waitlist for this date/service
        $stmt = $this->db->query(
            "SELECT id FROM booking_waitlist
             WHERE business_id = ? AND service_id = ? AND requested_date = ? AND email = ? AND status = 'waiting'",
            [$business['id'], $serviceId, $date, $email]
        );
        if ($stmt->fetch()) {
            return $this->json(['error' => 'Je staat al op de wachtlijst voor deze datum'], 400);
        }

        $uuid = $this->generateUuid();
        $userId = $_SESSION['user_id'] ?? null;

        $this->db->query(
            "INSERT INTO booking_waitlist (uuid, business_id, service_id, user_id, email, name, phone,
             requested_date, preferred_time_start, preferred_time_end, notes, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'waiting')",
            [$uuid, $business['id'], $serviceId, $userId, $email, $name, $phone,
             $date, $preferredTimeStart, $preferredTimeEnd, $notes ?: null]
        );

        // Send confirmation email
        $this->sendWaitlistConfirmationEmail([
            'name' => $name,
            'email' => $email,
            'business_name' => $business['name'],
            'service_name' => $service['name'],
            'date' => $date
        ]);

        return $this->json([
            'success' => true,
            'message' => 'Je staat nu op de wachtlijst. We sturen je een e-mail zodra er een plek vrijkomt.'
        ]);
    }

    /**
     * Notify waitlist when a booking is cancelled
     */
    private function notifyWaitlistForCancellation(array $booking): void
    {
        try {
            // Get service name
            $stmt = $this->db->query("SELECT name FROM services WHERE id = ?", [$booking['service_id']]);
            $service = $stmt->fetch(\PDO::FETCH_ASSOC);
            $serviceName = $service['name'] ?? 'Dienst';

            // Find first person on waitlist for this business/date
            $stmt = $this->db->query(
                "SELECT w.*, b.company_name as business_name, b.slug as business_slug
                 FROM booking_waitlist w
                 JOIN businesses b ON w.business_id = b.id
                 WHERE w.business_id = ? AND w.requested_date = ? AND w.status = 'waiting'
                 ORDER BY w.created_at ASC
                 LIMIT 1",
                [$booking['business_id'], $booking['appointment_date']]
            );
            $waitlistEntry = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($waitlistEntry) {
                // Update waitlist status
                $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
                $this->db->query(
                    "UPDATE booking_waitlist SET status = 'notified', notified_at = NOW(), expires_at = ? WHERE id = ?",
                    [$expiresAt, $waitlistEntry['id']]
                );

                // Send notification email
                $this->sendWaitlistNotificationEmail([
                    'name' => $waitlistEntry['name'],
                    'email' => $waitlistEntry['email'],
                    'business_name' => $waitlistEntry['business_name'],
                    'business_slug' => $waitlistEntry['business_slug'],
                    'service_name' => $serviceName,
                    'date' => $booking['appointment_date'],
                    'time' => $booking['appointment_time']
                ]);

                error_log("Waitlist notification sent to {$waitlistEntry['email']} for {$booking['appointment_date']}");
            }
        } catch (\Exception $e) {
            error_log("Failed to notify waitlist: " . $e->getMessage());
        }
    }

    /**
     * Send waitlist confirmation email
     */
    private function sendWaitlistConfirmationEmail(array $data): void
    {
        $dateFormatted = date('d-m-Y', strtotime($data['date']));

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:40px;text-align:center;">
                            <h1 style="margin:0;font-size:28px;font-weight:700;">Wachtlijst Bevestiging</h1>
                            <p style="margin:10px 0 0;opacity:0.9;font-size:16px;">{$data['business_name']}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;margin:0 0 25px;">Beste <strong>{$data['name']}</strong>,</p>
                            <p style="font-size:16px;color:#555;line-height:1.6;margin:0 0 30px;">
                                Je staat nu op de wachtlijst voor een afspraak bij <strong>{$data['business_name']}</strong>.
                            </p>

                            <div style="background:linear-gradient(135deg,#fffbeb,#faf5ff);border-radius:12px;padding:25px;margin-bottom:30px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid rgba(0,0,0,0.1);">
                                            <span style="color:#666;font-size:14px;">Dienst</span><br>
                                            <strong style="color:#333;font-size:16px;">{$data['service_name']}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;">
                                            <span style="color:#666;font-size:14px;">Gewenste datum</span><br>
                                            <strong style="color:#000000;font-size:18px;">{$dateFormatted}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style="background:#f5f5f5;border-left:4px solid #000000;padding:20px;border-radius:0 8px 8px 0;">
                                <p style="margin:0;color:#333;font-size:14px;">
                                    <strong>Wat gebeurt er nu?</strong><br><br>
                                    Zodra er een plek vrijkomt op jouw gewenste datum, sturen we je direct een e-mail.
                                    Je hebt dan 24 uur om te boeken voordat de plek naar de volgende persoon gaat.
                                </p>
                            </div>

                            <p style="font-size:14px;color:#888;text-align:center;margin:25px 0 0;">
                                We houden je op de hoogte!
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:25px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">© 2025 GlamourSchedule - Beauty & Wellness Platform</p>
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
            $mailer->send($data['email'], "Wachtlijst bevestiging - {$data['business_name']}", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send waitlist confirmation: " . $e->getMessage());
        }
    }

    /**
     * Send notification when slot becomes available
     */
    private function sendWaitlistNotificationEmail(array $data): void
    {
        $dateFormatted = date('d-m-Y', strtotime($data['date']));
        $timeFormatted = date('H:i', strtotime($data['time']));
        $bookingUrl = "https://new.glamourschedule.nl/business/{$data['business_slug']}?date={$data['date']}&time={$data['time']}";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#000000,#000000);padding:40px;text-align:center;color:#fff;">
                            <h1 style="margin:0;font-size:24px;">Er is een plek vrijgekomen!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;">Goed nieuws <strong>{$data['name']}</strong>!</p>
                            <p style="font-size:16px;color:#555;line-height:1.6;">
                                Er is een afspraak geannuleerd bij <strong>{$data['business_name']}</strong> en jij staat bovenaan de wachtlijst!
                            </p>

                            <div style="background:linear-gradient(135deg,#fafafa,#f5f5f5);border:2px solid #000000;border-radius:12px;padding:25px;margin:25px 0;text-align:center;">
                                <p style="margin:0;color:#666;font-size:14px;">Beschikbare plek</p>
                                <p style="margin:10px 0 0;color:#000000;font-size:24px;font-weight:700;">
                                    {$dateFormatted} om {$timeFormatted}
                                </p>
                                <p style="margin:10px 0 0;color:#666;">{$data['service_name']}</p>
                            </div>

                            <div style="background:#fafafa;border-left:4px solid #000000;padding:15px 20px;border-radius:0 8px 8px 0;margin:25px 0;">
                                <p style="margin:0;color:#333;font-size:14px;">
                                    <strong>Let op!</strong><br>
                                    Je hebt <strong>24 uur</strong> om te boeken, daarna gaat de plek naar de volgende persoon op de wachtlijst.
                                </p>
                            </div>

                            <p style="text-align:center;margin:30px 0;">
                                <a href="{$bookingUrl}" style="display:inline-block;background:#000000;color:#fff;padding:18px 50px;border-radius:50px;text-decoration:none;font-weight:700;font-size:17px;">
                                    Nu Boeken
                                </a>
                            </p>

                            <p style="font-size:14px;color:#888;text-align:center;">
                                Kun je toch niet? Geen probleem, we sturen de plek door naar de volgende.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">GlamourSchedule</p>
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
            $mailer->send($data['email'], "Plek vrijgekomen bij {$data['business_name']}", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send waitlist notification: " . $e->getMessage());
        }
    }

    /**
     * Get waitlist status for a user
     */
    public function getWaitlistStatus(): string
    {
        header('Content-Type: application/json');

        $email = $_GET['email'] ?? '';
        if (!$email) {
            return json_encode(['error' => 'Email required']);
        }

        $stmt = $this->db->query(
            "SELECT w.*, b.company_name as business_name, s.name as service_name
             FROM booking_waitlist w
             JOIN businesses b ON w.business_id = b.id
             JOIN services s ON w.service_id = s.id
             WHERE w.email = ? AND w.status IN ('waiting', 'notified')
             ORDER BY w.requested_date ASC",
            [$email]
        );
        $entries = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return json_encode(['waitlist' => $entries]);
    }

    /**
     * Cancel waitlist entry
     */
    public function cancelWaitlist(string $uuid): string
    {
        if (!$this->verifyCsrf()) {
            return $this->json(['error' => 'Ongeldige aanvraag'], 400);
        }

        $this->db->query(
            "UPDATE booking_waitlist SET status = 'cancelled' WHERE uuid = ?",
            [$uuid]
        );

        return $this->json(['success' => true, 'message' => 'Je bent van de wachtlijst verwijderd']);
    }
}

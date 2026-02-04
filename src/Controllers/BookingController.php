<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;
use GlamourSchedule\Core\PushNotification;
use GlamourSchedule\Services\LoyaltyService;
use GlamourSchedule\Services\BookingSignatureService;

class BookingController extends Controller
{
    /**
     * Create booking form by business UUID
     */
    public function createByUuid(string $uuid): string
    {
        $business = $this->getBusinessByUuid($uuid);
        if (!$business) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => $this->t('page_not_found')]);
        }
        return $this->renderCreateForm($business);
    }

    /**
     * Store booking by business UUID
     */
    public function storeByUuid(string $uuid): string
    {
        $business = $this->getBusinessByUuid($uuid);
        if (!$business) {
            return $this->json(['error' => $this->t('error_business_not_found')], 404);
        }
        return $this->processBooking($business);
    }

    /**
     * Create booking form by slug (legacy)
     */
    public function create(string $businessSlug): string
    {
        $business = $this->getBusinessBySlug($businessSlug);
        if (!$business) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => $this->t('page_not_found')]);
        }
        return $this->renderCreateForm($business);
    }

    /**
     * Render the booking create form
     */
    private function renderCreateForm(array $business): string
    {
        // Check if business needs verification (KVK not verified and not admin verified)
        if (empty($business['kvk_verified']) && empty($business['is_verified'])) {
            return $this->view('pages/booking/pending-verification', [
                'pageTitle' => $this->t('page_not_available'),
                'business' => $business
            ]);
        }

        $services = $this->getServices($business['id']);
        $selectedService = $_GET['service'] ?? null;

        // Get employees for BV businesses
        $employees = [];
        if (($business['business_type'] ?? 'eenmanszaak') === 'bv') {
            $employees = $this->getEmployees($business['id']);
        }

        // Get business settings for theme
        $settings = $this->getBusinessSettings($business['id']);

        // Get GeoIP data for localized pricing
        $geoIP = new \GlamourSchedule\Core\GeoIP($this->db);
        $location = $geoIP->lookup();
        $countryCode = $location['country_code'] ?? 'NL';
        $currencyService = new \GlamourSchedule\Services\CurrencyService();
        $visitorCurrency = $currencyService->getCurrencyForCountry($countryCode);
        $showDualCurrency = ($visitorCurrency !== 'EUR');

        return $this->view('pages/booking/create', [
            'pageTitle' => $this->t('page_book_at') . ' ' . $business['name'],
            'business' => $business,
            'services' => $services,
            'selectedService' => $selectedService,
            'employees' => $employees,
            'settings' => $settings,
            'currencyService' => $currencyService,
            'visitorCurrency' => $visitorCurrency,
            'showDualCurrency' => $showDualCurrency
        ]);
    }

    public function store(string $businessSlug): string
    {
        $business = $this->getBusinessBySlug($businessSlug);
        if (!$business) {
            return $this->json(['error' => $this->t('error_business_not_found')], 404);
        }
        return $this->processBooking($business);
    }

    /**
     * Process a booking for a business - stores in session and redirects to checkout
     */
    private function processBooking(array $business): string
    {
        // Check if business needs verification (KVK not verified and not admin verified)
        if (empty($business['kvk_verified']) && empty($business['is_verified'])) {
            return $this->json(['error' => $this->t('error_business_not_verified')], 403);
        }

        if (!$this->verifyCsrf()) {
            return $this->json(['error' => $this->t('validation_invalid_request')], 400);
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
                'pageTitle' => $this->t('page_book_at') . ' ' . $business['name'],
                'business' => $business,
                'services' => $this->getServices($business['id']),
                'error' => 'U moet akkoord gaan met de algemene voorwaarden om te boeken.'
            ]);
        }

        $service = $this->getServiceById($serviceId);
        if (!$service || $service['business_id'] != $business['id']) {
            return $this->view('pages/booking/create', [
                'pageTitle' => $this->t('page_book_at') . ' ' . $business['name'],
                'business' => $business,
                'services' => $this->getServices($business['id']),
                'error' => 'Ongeldige dienst geselecteerd'
            ]);
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId && !$guestEmail) {
            return $this->view('pages/booking/create', [
                'pageTitle' => $this->t('page_book_at') . ' ' . $business['name'],
                'business' => $business,
                'services' => $this->getServices($business['id']),
                'error' => 'Log in of vul je gegevens in'
            ]);
        }

        // Check if time slot is available (consider employee if BV business)
        if (!$this->isTimeSlotAvailable($business['id'], $date, $time, $service['duration_minutes'], $employeeId)) {
            return $this->view('pages/booking/create', [
                'pageTitle' => $this->t('page_book_at') . ' ' . $business['name'],
                'business' => $business,
                'services' => $this->getServices($business['id']),
                'employees' => ($business['business_type'] ?? 'eenmanszaak') === 'bv' ? $this->getEmployees($business['id']) : [],
                'error' => 'Dit tijdslot is helaas niet meer beschikbaar. Kies een andere tijd.'
            ]);
        }

        // Get customer info
        $customerEmail = $guestEmail ?: $this->getUserEmail($userId);
        $customerName = $guestName ?: $this->getUserName($userId);
        $customerPhone = $guestPhone ?: $this->getUserPhone($userId);

        // Calculate prices
        $servicePrice = $service['sale_price'] ?? $service['price'];
        $totalPrice = $servicePrice;

        // Get employee info if selected
        $employee = null;
        if ($employeeId) {
            $stmt = $this->db->query("SELECT id, name, color FROM employees WHERE id = ?", [$employeeId]);
            $employee = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        // Store booking data in session for checkout page
        $_SESSION['pending_booking'] = [
            'business_id' => $business['id'],
            'business_slug' => $business['slug'],
            'service_id' => $serviceId,
            'employee_id' => $employeeId,
            'user_id' => $userId,
            'date' => $date,
            'time' => $time,
            'notes' => $notes,
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
            'guest_phone' => $guestPhone,
            'customer_name' => $customerName ?: 'Klant',
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone ?: '',
            'service_price' => $servicePrice,
            'total_price' => $totalPrice,
            'duration_minutes' => $service['duration_minutes'],
            'created_at' => time()
        ];

        return $this->redirect('/booking/checkout');
    }

    /**
     * Show checkout/review page
     */
    public function showCheckout(): string
    {
        // Check if there's pending booking data
        if (!isset($_SESSION['pending_booking'])) {
            return $this->redirect('/');
        }

        $bookingData = $_SESSION['pending_booking'];

        // Check if session is not too old (30 minutes max)
        if (time() - $bookingData['created_at'] > 1800) {
            unset($_SESSION['pending_booking']);
            return $this->redirect('/');
        }

        // Get business info
        $stmt = $this->db->query(
            "SELECT * FROM businesses WHERE id = ?",
            [$bookingData['business_id']]
        );
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            unset($_SESSION['pending_booking']);
            return $this->redirect('/');
        }

        // Get service info
        $service = $this->getServiceById($bookingData['service_id']);
        if (!$service) {
            unset($_SESSION['pending_booking']);
            return $this->redirect('/');
        }

        // Get employee info if applicable
        $employee = null;
        if ($bookingData['employee_id']) {
            $stmt = $this->db->query("SELECT id, name, color FROM employees WHERE id = ?", [$bookingData['employee_id']]);
            $employee = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        // Get business settings for theme
        $settings = $this->getBusinessSettings($business['id']);

        // Get loyalty data if user is logged in
        $loyaltyData = null;
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $loyaltyService = new LoyaltyService();
            $loyaltyEnabled = $loyaltyService->isEnabled($business['id']);
            if ($loyaltyEnabled) {
                $userPoints = $loyaltyService->getBalance($userId, $business['id']);
                $servicePrice = $bookingData['service_price'];
                $maxRedeemable = $loyaltyService->getMaxRedeemablePoints($business['id'], $userPoints, $servicePrice);

                $loyaltyData = [
                    'enabled' => true,
                    'user_points' => $userPoints,
                    'max_redeemable' => $maxRedeemable,
                    'points_per_percent' => LoyaltyService::getPointsPerPercent(),
                    'points_per_booking' => LoyaltyService::getPointsPerBooking(),
                ];
            }
        }

        // Get GeoIP data for localized pricing
        $geoIP = new \GlamourSchedule\Core\GeoIP($this->db);
        $location = $geoIP->lookup();
        $countryCode = $location['country_code'] ?? 'NL';
        $currencyService = new \GlamourSchedule\Services\CurrencyService();
        $visitorCurrency = $currencyService->getCurrencyForCountry($countryCode);
        $showDualCurrency = ($visitorCurrency !== 'EUR');

        return $this->view('pages/booking/checkout', [
            'pageTitle' => $this->t('page_confirm_booking'),
            'business' => $business,
            'service' => $service,
            'employee' => $employee,
            'bookingData' => $bookingData,
            'csrfToken' => $this->csrf(),
            'settings' => $settings,
            'loyaltyData' => $loyaltyData,
            'currencyService' => $currencyService,
            'visitorCurrency' => $visitorCurrency,
            'showDualCurrency' => $showDualCurrency
        ]);
    }

    /**
     * Confirm booking and create it in database
     */
    public function confirmBooking(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/booking/checkout?error=csrf');
        }

        // Check if there's pending booking data
        if (!isset($_SESSION['pending_booking'])) {
            return $this->redirect('/');
        }

        $bookingData = $_SESSION['pending_booking'];

        // Check if session is not too old (30 minutes max)
        if (time() - $bookingData['created_at'] > 1800) {
            unset($_SESSION['pending_booking']);
            return $this->redirect('/');
        }

        // Get business info
        $stmt = $this->db->query("SELECT * FROM businesses WHERE id = ?", [$bookingData['business_id']]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            unset($_SESSION['pending_booking']);
            return $this->redirect('/');
        }

        // Get service info
        $service = $this->getServiceById($bookingData['service_id']);
        if (!$service) {
            unset($_SESSION['pending_booking']);
            return $this->redirect('/');
        }

        // Re-check if time slot is still available
        if (!$this->isTimeSlotAvailable($business['id'], $bookingData['date'], $bookingData['time'], $bookingData['duration_minutes'], $bookingData['employee_id'])) {
            unset($_SESSION['pending_booking']);
            return $this->redirect('/book/' . $business['slug'] . '?error=slot_taken');
        }

        // Handle loyalty points redemption
        $loyaltyDiscount = 0.00;
        $loyaltyPointsRedeemed = 0;
        $userId = $bookingData['user_id'];

        // If no user_id but guest_email provided, check if email matches existing user account
        if (!$userId && !empty($bookingData['guest_email'])) {
            $existingUser = $this->db->query(
                "SELECT id FROM users WHERE email = ? AND is_guest = 0 LIMIT 1",
                [$bookingData['guest_email']]
            )->fetch(\PDO::FETCH_ASSOC);

            if ($existingUser) {
                $userId = $existingUser['id'];
                $bookingData['user_id'] = $userId;
            }
        }

        if ($userId) {
            $loyaltyService = new LoyaltyService();
            $pointsToRedeem = (int)($_POST['loyalty_points'] ?? 0);

            if ($pointsToRedeem > 0 && $loyaltyService->isEnabled($business['id'])) {
                $userPoints = $loyaltyService->getBalance($userId, $business['id']);
                $maxRedeemable = $loyaltyService->getMaxRedeemablePoints(
                    $business['id'],
                    $userPoints,
                    $bookingData['service_price']
                );

                // Validate points to redeem (must be in steps of 100 and within limits)
                $pointsToRedeem = min($pointsToRedeem, $maxRedeemable);
                $pointsToRedeem = (int)floor($pointsToRedeem / 100) * 100; // Round to nearest 100

                if ($pointsToRedeem > 0) {
                    $loyaltyDiscount = $loyaltyService->calculateDiscount($pointsToRedeem, $bookingData['service_price']);
                    $loyaltyPointsRedeemed = $pointsToRedeem;
                }
            }
        }

        // Calculate final price (service price - loyalty discount)
        // Platform fee wordt van bedrijf afgetrokken, NIET van klant
        $servicePrice = $bookingData['service_price'];
        $finalServicePrice = max(0, $servicePrice - $loyaltyDiscount);
        $platformFee = 1.75; // Platform fee wordt van bedrijf afgetrokken
        $totalPrice = $finalServicePrice; // Klant betaalt alleen serviceprijs (minus korting)
        $businessPayout = max(0, $finalServicePrice - $platformFee); // Bedrijf ontvangt dit na aftrek platform fee

        // Create the booking
        $uuid = $this->generateUuid();
        $bookingNumber = 'GS' . strtoupper(substr(md5($uuid), 0, 8));
        $qrCodeHash = hash('sha256', $uuid . $bookingNumber);

        // Generate SHA256 verification code (like Bitcoin address)
        // Combines: business_id + customer identifier + uuid + secret
        $customerIdentifier = $bookingData['user_id'] ?? $bookingData['guest_email'] ?? '';
        $verificationCode = $this->generateVerificationCode($business['id'], $customerIdentifier, $uuid);

        // Generate cryptographic signature using per-business HMAC-SHA256
        $signatureService = new BookingSignatureService();
        $signatureData = $signatureService->signBooking(
            $business['id'],
            $uuid,
            $bookingNumber,
            $bookingData['date'],
            $bookingData['time']
        );
        $signature = $signatureData['signature'] ?? null;
        $signatureVersion = $signatureData['version'] ?? 1;

        // Get current platform language for email personalization
        $bookingLanguage = $_SESSION['lang'] ?? 'nl';
        $validLangs = ['nl', 'en', 'de', 'fr', 'es', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar', 'tr', 'pl', 'sv', 'no', 'da', 'fi', 'el', 'cs', 'hu', 'ro', 'bg', 'hr', 'sk', 'sl', 'et', 'lv', 'lt', 'uk', 'hi', 'th', 'vi', 'id', 'ms', 'tl', 'he', 'fa', 'sw', 'af'];
        if (!in_array($bookingLanguage, $validLangs)) {
            $bookingLanguage = 'nl';
        }

        $this->db->query(
            "INSERT INTO bookings (uuid, booking_number, business_id, employee_id, user_id, service_id,
             guest_name, guest_email, guest_phone, appointment_date, appointment_time,
             duration_minutes, service_price, admin_fee, total_price, platform_fee, business_payout,
             qr_code_hash, verification_code, signature, signature_version, customer_notes,
             language, terms_accepted_at, terms_version, status, loyalty_discount, loyalty_points_redeemed)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), '1.0', 'pending', ?, ?)",
            [
                $uuid, $bookingNumber, $business['id'], $bookingData['employee_id'], $bookingData['user_id'], $bookingData['service_id'],
                $bookingData['guest_name'] ?: null, $bookingData['guest_email'] ?: null, $bookingData['guest_phone'] ?: null,
                $bookingData['date'], $bookingData['time'], $bookingData['duration_minutes'],
                $servicePrice, 0.00, $totalPrice, $platformFee, $businessPayout,
                $qrCodeHash, $verificationCode, $signature, $signatureVersion, $bookingData['notes'] ?: null,
                $bookingLanguage, $loyaltyDiscount, $loyaltyPointsRedeemed
            ]
        );

        // Redeem loyalty points if applicable
        if ($loyaltyPointsRedeemed > 0 && $userId) {
            $stmt = $this->db->query("SELECT id FROM bookings WHERE uuid = ?", [$uuid]);
            $newBooking = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($newBooking) {
                $loyaltyService->redeemPoints($userId, $business['id'], $newBooking['id'], $loyaltyPointsRedeemed);
            }
        }

        // Schedule reminders (24 hours and 1 hour before)
        $this->scheduleReminders($bookingData['date'], $bookingData['time'], $uuid);

        // Send push notification to business owner in their preferred language
        try {
            $push = new PushNotification();
            $businessLang = $business['language'] ?? 'nl';
            $push->notifyNewBooking([
                'business_id' => $business['id'],
                'guest_name' => $bookingData['guest_name'],
                'customer_name' => $bookingData['customer_name'],
                'service_name' => $service['name'],
                'appointment_date' => $bookingData['date'],
                'appointment_time' => $bookingData['time']
            ], $businessLang);
        } catch (\Exception $e) {
            error_log('Push notification failed: ' . $e->getMessage());
        }

        // Clear pending booking from session
        unset($_SESSION['pending_booking']);

        // Redirect to payment
        return $this->redirect("/payment/create/$uuid");
    }

    /**
     * Schedule both 24h and 1h reminders in business timezone
     */
    private function scheduleReminders(string $date, string $time, string $uuid, ?int $businessId = null): void
    {
        try {
            // Get booking ID and business timezone
            $stmt = $this->db->query(
                "SELECT b.id, biz.timezone
                 FROM bookings b
                 JOIN businesses biz ON b.business_id = biz.id
                 WHERE b.uuid = ?",
                [$uuid]
            );
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$booking) return;

            // Use business timezone (default to Europe/Amsterdam)
            $businessTimezone = new \DateTimeZone($booking['timezone'] ?? 'Europe/Amsterdam');
            $serverTimezone = new \DateTimeZone(date_default_timezone_get());

            // Create appointment datetime in business timezone
            $appointmentDateTime = new \DateTime("{$date} {$time}", $businessTimezone);

            // Convert to server timezone for storage (so NOW() comparison works)
            $appointmentDateTime->setTimezone($serverTimezone);

            $now = new \DateTime();

            // Schedule 24-hour reminder
            $reminder24h = clone $appointmentDateTime;
            $reminder24h->modify('-24 hours');
            if ($reminder24h > $now) {
                $this->db->query(
                    "INSERT INTO booking_reminders (booking_id, reminder_type, scheduled_for, status)
                     VALUES (?, '24h', ?, 'pending')",
                    [$booking['id'], $reminder24h->format('Y-m-d H:i:s')]
                );
            }

            // Schedule 1-hour reminder
            $reminder1h = clone $appointmentDateTime;
            $reminder1h->modify('-1 hour');
            if ($reminder1h > $now) {
                $this->db->query(
                    "INSERT INTO booking_reminders (booking_id, reminder_type, scheduled_for, status)
                     VALUES (?, '1h', ?, 'pending')",
                    [$booking['id'], $reminder1h->format('Y-m-d H:i:s')]
                );
            }
        } catch (\Exception $e) {
            error_log("Failed to schedule reminders: " . $e->getMessage());
        }
    }

    public function show(string $uuid): string
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.verification_code, biz.company_name as business_name, biz.street as address, biz.city,
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
            return $this->view('pages/errors/404', ['pageTitle' => $this->t('page_not_found')]);
        }

        // Get GeoIP data for localized pricing
        $geoIP = new \GlamourSchedule\Core\GeoIP($this->db);
        $location = $geoIP->lookup();
        $countryCode = $location['country_code'] ?? 'NL';
        $currencyService = new \GlamourSchedule\Services\CurrencyService();
        $visitorCurrency = $currencyService->getCurrencyForCountry($countryCode);
        $showDualCurrency = ($visitorCurrency !== 'EUR');

        return $this->view('pages/booking/show', [
            'pageTitle' => $this->t('page_booking_number') . $booking['booking_number'],
            'booking' => $booking,
            'csrfToken' => $this->csrf(),
            'currencyService' => $currencyService,
            'visitorCurrency' => $visitorCurrency,
            'showDualCurrency' => $showDualCurrency
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

        // Anyone with the booking UUID link can cancel
        // The UUID is secret and only shared via confirmation email
        // No additional authorization check needed

        // Check if already cancelled
        if ($booking['status'] === 'cancelled') {
            return $this->redirect("/booking/$uuid");
        }

        // Calculate if within 24 hours
        $appointmentDateTime = new \DateTime($booking['appointment_date'] . ' ' . $booking['appointment_time']);
        $now = new \DateTime();
        $hoursUntilAppointment = ($appointmentDateTime->getTimestamp() - $now->getTimestamp()) / 3600;
        $isLateCancel = $hoursUntilAppointment <= 24 && $hoursUntilAppointment > 0;

        // Platform administration fee (always charged on cancellation)
        $platformFee = 1.75;

        // Calculate refund amounts
        $totalPrice = $booking['total_price'];
        $businessFee = 0;
        $refundAmount = 0;

        if ($booking['payment_status'] === 'paid') {
            if ($isLateCancel) {
                // Within 24 hours: 50% to business, 50% to customer (minus platform fee)
                $businessFee = round($totalPrice / 2, 2);
                $customerShare = $totalPrice - $businessFee;
                $refundAmount = max(0, $customerShare - $platformFee);
            } else {
                // More than 24 hours: 100% to customer (minus platform fee)
                $businessFee = 0;
                $refundAmount = max(0, $totalPrice - $platformFee);
            }
        }

        // Process Mollie refund if payment was made
        $refundProcessed = false;
        $mollieRefundId = null;

        if ($booking['payment_status'] === 'paid' && !empty($booking['mollie_payment_id']) && $refundAmount > 0) {
            $mollieApiKey = $this->config['mollie']['api_key'] ?? '';

            if (!empty($mollieApiKey)) {
                try {
                    $mollie = new \Mollie\Api\MollieApiClient();
                    $mollie->setApiKey($mollieApiKey);

                    // Get the payment
                    $payment = $mollie->payments->get($booking['mollie_payment_id']);

                    // Check if payment can be refunded
                    if ($payment->canBeRefunded() && floatval($payment->amountRemaining->value) >= $refundAmount) {
                        // Create refund for the calculated amount
                        $refund = $payment->refund([
                            'amount' => [
                                'currency' => 'EUR',
                                'value' => number_format($refundAmount, 2, '.', '')
                            ],
                            'description' => 'Afspraak geannuleerd - GlamourSchedule #' . $booking['booking_number']
                        ]);

                        $refundProcessed = true;
                        $mollieRefundId = $refund->id;

                    } elseif ($payment->canBePartiallyRefunded()) {
                        // Try partial refund with remaining amount
                        $refund = $payment->refund([
                            'description' => 'Afspraak geannuleerd - GlamourSchedule #' . $booking['booking_number']
                        ]);
                        $refundProcessed = true;
                        $mollieRefundId = $refund->id;
                    }

                } catch (\Exception $e) {
                    error_log("Booking cancellation refund error for {$uuid}: " . $e->getMessage());
                }
            }
        }

        // Update booking status and refund info
        $paymentStatus = $refundProcessed ? 'refunded' : $booking['payment_status'];

        $this->db->query(
            "UPDATE bookings SET
                status = 'cancelled',
                cancelled_at = NOW(),
                cancellation_fee = ?,
                refund_amount = ?,
                payment_status = ?,
                mollie_refund_id = ?
             WHERE uuid = ?",
            [$businessFee, $refundAmount, $paymentStatus, $mollieRefundId, $uuid]
        );

        // Send cancellation email to customer
        $customerEmail = $booking['guest_email'] ?? $this->getUserEmail($booking['user_id']);
        if ($customerEmail) {
            $this->sendCancellationEmail($booking, $customerEmail, $refundAmount, $businessFee, $platformFee, $isLateCancel);
        }

        // Notify business of cancellation
        if ($booking['business_email']) {
            $this->sendBusinessCancellationNotice($booking, $businessFee, $isLateCancel);
        }

        // Check waitlist and notify first person
        $this->notifyWaitlistForCancellation($booking);

        // Redirect with success message
        $successParam = $refundProcessed ? 'cancelled_refund' : 'cancelled';
        return $this->redirect("/booking/$uuid?success=$successParam");
    }

    /**
     * Send cancellation confirmation email to customer
     */
    private function sendCancellationEmail(array $booking, string $email, float $refundAmount, float $businessFee, float $platformFee, bool $isLateCancel): void
    {
        $customerLang = $booking['language'] ?? $this->lang ?? 'nl';
        $translations = $this->loadTranslationsForLang($customerLang);
        $t = function($key, $replacements = []) use ($translations) {
            $text = $translations[$key] ?? $key;
            foreach ($replacements as $search => $replace) {
                $text = str_replace('{' . $search . '}', $replace, $text);
            }
            return $text;
        };

        $totalPrice = $booking['total_price'];
        $refundFormatted = number_format($refundAmount, 2, ',', '.');
        $businessFeeFormatted = number_format($businessFee, 2, ',', '.');
        $platformFeeFormatted = number_format($platformFee, 2, ',', '.');
        $totalFormatted = number_format($totalPrice, 2, ',', '.');

        // Build cost breakdown
        $breakdownRows = '';
        $breakdownRows .= "<tr><td style='padding:8px 0;color:#cccccc;'>{$t('email_original_amount')}</td><td style='padding:8px 0;text-align:right;'>EUR {$totalFormatted}</td></tr>";

        if ($isLateCancel && $businessFee > 0) {
            $breakdownRows .= "<tr><td style='padding:8px 0;color:#dc2626;'>{$t('email_to_salon_within_24h')}</td><td style='padding:8px 0;text-align:right;color:#dc2626;'>- EUR {$businessFeeFormatted}</td></tr>";
        }

        $breakdownRows .= "<tr><td style='padding:8px 0;color:#cccccc;'>{$t('email_admin_fee')}</td><td style='padding:8px 0;text-align:right;color:#cccccc;'>- EUR {$platformFeeFormatted}</td></tr>";
        $breakdownRows .= "<tr style='border-top:2px solid #e5e7eb;'><td style='padding:12px 0 0;font-weight:600;color:#22c55e;'>{$t('email_refund')}</td><td style='padding:12px 0 0;text-align:right;font-weight:700;color:#22c55e;font-size:1.1rem;'>EUR {$refundFormatted}</td></tr>";

        $cancelTypeNotice = $isLateCancel
            ? "<div style='background:#fef2f2;border:1px solid #dc2626;border-radius:8px;padding:15px;margin-bottom:20px;'><p style='margin:0;color:#991b1b;font-weight:600;'>{$t('email_cancel_notice_within_24h')}</p><p style='margin:8px 0 0;color:#991b1b;font-size:0.9rem;'>{$t('email_50_percent_to_salon')}</p></div>"
            : "<div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;margin-bottom:20px;'><p style='margin:0;color:#166534;font-weight:600;'>{$t('email_cancel_notice_after_24h')}</p><p style='margin:8px 0 0;color:#166534;font-size:0.9rem;'>{$t('email_full_refund_minus_admin')}</p></div>";

        $adminFeeExplanation = $t('email_admin_fee_explanation', ['amount' => "EUR {$platformFeeFormatted}"]);
        $refundWithinDays = $t('email_refund_within_days', ['amount' => "EUR {$refundFormatted}"]);
        $subject = $t('email_booking_cancelled_subject', ['booking_number' => $booking['booking_number']]);

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:#000;padding:30px;text-align:center;color:#fff;">
                            <h1 style="margin:0;font-size:22px;">{$t('email_booking_cancelled_heading')}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <div style="background:#0a0a0a;border-radius:12px;padding:20px;margin-bottom:20px;">
                                <p style="margin:0;color:#cccccc;font-size:0.9rem;"><strong>{$t('email_booking_label')}</strong> #{$booking['booking_number']}</p>
                                <p style="margin:8px 0 0;color:#cccccc;font-size:0.9rem;"><strong>{$t('email_salon_label')}</strong> {$booking['business_name']}</p>
                                <p style="margin:8px 0 0;color:#cccccc;font-size:0.9rem;"><strong>{$t('email_date_label')}</strong> {$booking['appointment_date']}</p>
                            </div>

                            {$cancelTypeNotice}

                            <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:20px 0;">
                                <p style="margin:0 0 15px;font-weight:600;color:#ffffff;">{$t('email_refund_overview')}</p>
                                <table style="width:100%;font-size:0.9rem;">
                                    {$breakdownRows}
                                </table>
                            </div>

                            <div style="background:#1a1a1a;border:1px solid #f59e0b;border-radius:8px;padding:15px;margin:20px 0;">
                                <p style="margin:0;color:#92400e;font-size:0.85rem;">
                                    <strong>{$t('email_why_admin_fee')}</strong><br>
                                    {$adminFeeExplanation}
                                </p>
                            </div>

                            <p style="color:#cccccc;font-size:0.9rem;margin:20px 0;">
                                {$refundWithinDays}
                            </p>

                            <p style="text-align:center;margin-top:25px;">
                                <a href="https://glamourschedule.com/search" style="display:inline-block;background:#000;color:#fff;padding:12px 30px;border-radius:8px;text-decoration:none;font-weight:600;">
                                    {$t('email_make_new_appointment')}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:15px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#999;font-size:12px;">GlamourSchedule</p>
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
            $mailer = new Mailer($customerLang);
            $mailer->send($email, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send cancellation email: " . $e->getMessage());
        }
    }

    /**
     * Notify business of cancellation
     */
    private function sendBusinessCancellationNotice(array $booking, float $businessFee, bool $isLateCancel): void
    {
        $businessLang = $booking['business_language'] ?? $this->lang ?? 'nl';
        $translations = $this->loadTranslationsForLang($businessLang);
        $t = function($key, $replacements = []) use ($translations) {
            $text = $translations[$key] ?? $key;
            foreach ($replacements as $search => $replace) {
                $text = str_replace('{' . $search . '}', $replace, $text);
            }
            return $text;
        };

        $feeFormatted = number_format($businessFee, 2, ',', '.');
        $priceFormatted = number_format($booking['total_price'], 2, ',', '.');
        $customerName = $booking['guest_name'] ?? $t('customer');
        $serviceName = $booking['service_name'] ?? $t('service');
        $appointmentDate = date('d-m-Y', strtotime($booking['appointment_date']));
        $appointmentTime = date('H:i', strtotime($booking['appointment_time']));

        $currentYear = date('Y');
        $feeNotice = '';
        if ($isLateCancel && $businessFee > 0) {
            $feeNotice = <<<HTML
<div style="background:#f0fdf4;border:2px solid #333333;border-radius:12px;padding:20px;margin:25px 0;text-align:center;">
    <p style="margin:0;color:#ffffff;font-weight:600;font-size:16px;">
        💰 {$t('email_late_cancel_compensation')}
    </p>
    <p style="margin:10px 0 0;color:#ffffff;font-size:1.75rem;font-weight:700;">€{$feeFormatted}</p>
    <p style="margin:10px 0 0;color:#ffffff;font-size:0.9rem;">
        {$t('email_amount_credited_next_payout')}
    </p>
</div>
HTML;
        }

        $subject = $t('email_booking_cancelled_subject', ['booking_number' => $booking['booking_number']]);

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:30px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#333333,#dc2626);padding:40px;text-align:center;color:#fff;">
                            <div style="width:80px;height:80px;background:rgba(255,255,255,0.2);border-radius:50%;margin:0 auto 15px;display:flex;align-items:center;justify-content:center;">
                                <span style="font-size:40px;">❌</span>
                            </div>
                            <h1 style="margin:0;font-size:26px;font-weight:700;">{$t('email_booking_cancelled_heading')}</h1>
                            <p style="margin:10px 0 0;opacity:0.9;font-size:15px;">#{$booking['booking_number']}</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:17px;color:#ffffff;margin:0 0 25px;text-align:center;">
                                {$t('email_customer_cancelled_booking')}
                            </p>

                            <!-- Booking Details -->
                            <div style="background:#0a0a0a;border-radius:16px;padding:25px;margin:0 0 25px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;">{$t('customer')}</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:600;color:#1f2937;">{$customerName}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;border-top:1px solid #e5e7eb;">{$t('service')}</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:600;color:#1f2937;border-top:1px solid #e5e7eb;">{$serviceName}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;border-top:1px solid #e5e7eb;">{$t('date')}</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:600;color:#1f2937;border-top:1px solid #e5e7eb;">{$appointmentDate}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;border-top:1px solid #e5e7eb;">{$t('time')}</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:600;color:#1f2937;border-top:1px solid #e5e7eb;">{$appointmentTime}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:14px;border-top:1px solid #e5e7eb;">{$t('amount')}</td>
                                        <td style="padding:8px 0;text-align:right;font-weight:700;color:#ffffff;font-size:16px;border-top:1px solid #e5e7eb;">€{$priceFormatted}</td>
                                    </tr>
                                </table>
                            </div>

                            {$feeNotice}

                            <!-- Time Slot Free Notice -->
                            <div style="background:#0a0a0a;border:2px solid #404040;border-radius:12px;padding:18px;margin:25px 0;text-align:center;">
                                <p style="margin:0;color:#ffffff;font-weight:600;font-size:15px;">
                                    🕐 {$t('email_timeslot_available')}
                                </p>
                                <p style="margin:8px 0 0;color:#ffffff;font-size:14px;">
                                    {$appointmentDate} - {$appointmentTime}
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <p style="text-align:center;margin-top:30px;">
                                <a href="https://glamourschedule.com/business/calendar" style="display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:#fff;padding:16px 40px;border-radius:50px;text-decoration:none;font-weight:600;font-size:15px;box-shadow:0 4px 15px #rgba(0,0,0,0.3);">
                                    📅 {$t('email_view_calendar')}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#0a0a0a;padding:25px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="margin:0;color:#9ca3af;font-size:13px;">© {$currentYear} GlamourSchedule - Beauty Booking Platform</p>
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
            $mailer = new Mailer($businessLang);
            $mailer->send($booking['business_email'], $subject, $htmlBody);
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

    private function getBusinessByUuid(string $uuid): ?array
    {
        $stmt = $this->db->query(
            "SELECT *, company_name as name FROM businesses WHERE uuid = ? AND status = 'active'",
            [$uuid]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
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
     * Generate SHA256-based verification code (similar to Bitcoin address format)
     * Links business_id with customer for secure check-in verification
     *
     * @param int $businessId The business ID
     * @param mixed $customerIdentifier User ID or guest email
     * @param string $uuid The booking UUID
     * @return string 12-character verification code (format: XXXX-XXXX-XXXX)
     */
    private function generateVerificationCode(int $businessId, $customerIdentifier, string $uuid): string
    {
        // Get secret key from environment
        $secretKey = $_ENV['APP_KEY'] ?? 'glamourschedule-secret-key-2025';

        // Create the data string: business_id + customer + uuid + timestamp seed
        $dataString = sprintf(
            '%d:%s:%s:%s',
            $businessId,
            (string)$customerIdentifier,
            $uuid,
            $secretKey
        );

        // Generate SHA256 hash
        $hash = hash('sha256', $dataString);

        // Take first 12 characters (uppercase) and format as XXXX-XXXX-XXXX
        $code = strtoupper(substr($hash, 0, 12));
        return substr($code, 0, 4) . '-' . substr($code, 4, 4) . '-' . substr($code, 8, 4);
    }

    /**
     * Verify a verification code matches the booking's business
     *
     * @param string $verificationCode The code to verify
     * @param int $businessId The business ID to match
     * @param array $booking The booking data
     * @return bool True if verification passes
     */
    public static function verifyCode(string $verificationCode, int $businessId, array $booking): bool
    {
        // Regenerate the code and compare
        $secretKey = $_ENV['APP_KEY'] ?? 'glamourschedule-secret-key-2025';
        $customerIdentifier = $booking['user_id'] ?? $booking['guest_email'] ?? '';

        $dataString = sprintf(
            '%d:%s:%s:%s',
            $businessId,
            (string)$customerIdentifier,
            $booking['uuid'],
            $secretKey
        );

        $hash = hash('sha256', $dataString);
        $expectedCode = strtoupper(substr($hash, 0, 12));
        $expectedCode = substr($expectedCode, 0, 4) . '-' . substr($expectedCode, 4, 4) . '-' . substr($expectedCode, 8, 4);

        // Normalize input: remove dashes, uppercase, then format like expected
        $normalizedInput = strtoupper(str_replace('-', '', $verificationCode));
        $formattedInput = substr($normalizedInput, 0, 4) . '-' . substr($normalizedInput, 4, 4) . '-' . substr($normalizedInput, 8, 4);

        // Timing-safe comparison
        return hash_equals($expectedCode, $formattedInput);
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

        // Get current time in salon timezone (Europe/Amsterdam)
        $salonTimezone = new \DateTimeZone('Europe/Amsterdam');
        $now = new \DateTime('now', $salonTimezone);
        $currentDate = $now->format('Y-m-d');
        $currentTime = $now->format('H:i');
        $isToday = ($date === $currentDate);

        // Generate all possible time slots (9:00 - 18:00, every 30 min)
        $availableSlots = [];
        for ($h = 9; $h <= 18; $h++) {
            for ($m = 0; $m < 60; $m += 30) {
                $time = sprintf('%02d:%02d', $h, $m);
                $endTime = date('H:i', strtotime($time) + ($duration * 60));

                // Skip if slot ends after closing time
                if ($endTime > '19:00') continue;

                // Check if slot is in the past (for today only)
                $isPast = $isToday && $time <= $currentTime;

                // Check if slot overlaps with any booked slot
                $isAvailable = true;
                if ($isPast) {
                    $isAvailable = false;
                } else {
                    foreach ($bookedSlots as $booked) {
                        if ($this->timeSlotsOverlap($time, $duration, $booked['time'], $booked['duration'])) {
                            $isAvailable = false;
                            break;
                        }
                    }
                }

                $availableSlots[] = [
                    'time' => $time,
                    'available' => $isAvailable
                ];
            }
        }

        // Check if any slots are available
        $hasAvailableSlots = false;
        foreach ($availableSlots as $slot) {
            if ($slot['available']) {
                $hasAvailableSlots = true;
                break;
            }
        }

        $response = ['slots' => $availableSlots];

        // If no available slots, find the next available date/time
        if (!$hasAvailableSlots) {
            $nextAvailable = $this->findNextAvailableDateTime(
                $business['id'],
                $serviceId,
                $duration,
                $employeeId,
                date('Y-m-d', strtotime($date . ' +1 day')) // Start from next day
            );
            if ($nextAvailable) {
                $response['nextAvailable'] = $nextAvailable;
            }
        }

        return json_encode($response);
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

    /**
     * Find the next available date and time slot for a service
     */
    private function findNextAvailableDateTime(int $businessId, int $serviceId, int $duration, ?int $employeeId = null, string $startDate = null): ?array
    {
        // Get current time in salon timezone (Europe/Amsterdam)
        $salonTimezone = new \DateTimeZone('Europe/Amsterdam');
        $now = new \DateTime('now', $salonTimezone);
        $currentDate = $now->format('Y-m-d');
        $currentTime = $now->format('H:i');

        $startDate = $startDate ?? $currentDate;
        $maxDaysToSearch = 60; // Search up to 60 days ahead

        for ($i = 0; $i < $maxDaysToSearch; $i++) {
            $checkDate = date('Y-m-d', strtotime($startDate . ' +' . $i . ' days'));
            $bookedSlots = $this->getBookedSlots($businessId, $checkDate, $employeeId);

            // Generate all possible time slots (9:00 - 18:00, every 30 min)
            for ($h = 9; $h <= 18; $h++) {
                for ($m = 0; $m < 60; $m += 30) {
                    $time = sprintf('%02d:%02d', $h, $m);
                    $endTime = date('H:i', strtotime($time) + ($duration * 60));

                    // Skip if slot ends after closing time
                    if ($endTime > '19:00') continue;

                    // Skip past times for today (using salon timezone)
                    if ($checkDate === $currentDate && $time <= $currentTime) continue;

                    // Check if slot overlaps with any booked slot
                    $isAvailable = true;
                    foreach ($bookedSlots as $booked) {
                        if ($this->timeSlotsOverlap($time, $duration, $booked['time'], $booked['duration'])) {
                            $isAvailable = false;
                            break;
                        }
                    }

                    if ($isAvailable) {
                        return [
                            'date' => $checkDate,
                            'time' => $time
                        ];
                    }
                }
            }
        }

        return null; // No available slot found within search range
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
        $customerLang = $data['language'] ?? $this->lang ?? 'nl';
        $translations = $this->loadTranslationsForLang($customerLang);
        $t = function($key, $replacements = []) use ($translations) {
            $text = $translations[$key] ?? $key;
            foreach ($replacements as $search => $replace) {
                $text = str_replace('{' . $search . '}', $replace, $text);
            }
            return $text;
        };

        $to = $data['email'];
        $subject = $t('email_booking_confirmation_subject', ['booking_number' => $data['booking_number']]);

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
                    <h1 style='margin:0'>{$t('email_booking_confirmation_heading')}</h1>
                    <p style='margin:10px 0 0'>#{$data['booking_number']}</p>
                </div>
                <div class='content'>
                    <p>{$t('email_dear_name', ['name' => $data['name']])}</p>
                    <p>{$t('email_thanks_for_booking')} {$t('email_find_details_below')}</p>

                    <div class='booking-details'>
                        <div class='detail-row'>
                            <span class='detail-label'>{$t('email_salon_label')}</span>
                            <span>{$data['business_name']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>{$t('email_treatment_label')}</span>
                            <span>{$data['service_name']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>{$t('email_date_label')}</span>
                            <span>{$dateFormatted}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>{$t('email_time_label')}</span>
                            <span>{$data['time']}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>{$t('email_price_label')}</span>
                            <span>&euro;{$priceFormatted}</span>
                        </div>
                    </div>

                    <p style='text-align:center'>
                        <a href='https://glamourschedule.com/booking/{$data['uuid']}' class='btn'>{$t('email_view_your_booking')}</a>
                    </p>

                    <p style='margin-top:30px;font-size:14px;color:#cccccc'>
                        {$t('email_can_view_change_cancel')}
                    </p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " GlamourSchedule - Beauty & Wellness Booking Platform</p>
                </div>
            </div>
        </body>
        </html>
        ";

        try {
            $mailer = new Mailer($customerLang);
            return $mailer->send($to, $subject, $message);
        } catch (\Exception $e) {
            error_log("Failed to send booking confirmation email: " . $e->getMessage());
            // Fallback to mail()
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: GlamourSchedule <noreply@glamourschedule.com>',
                'Reply-To: info@glamourschedule.com',
                'X-Mailer: PHP/' . phpversion()
            ];
            return mail($to, $subject, $message, implode("\r\n", $headers));
        }
    }

    /**
     * Show check-in page for business to confirm customer arrival
     */
    public function showCheckin(string $uuid): string
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.verification_code, biz.company_name as business_name, biz.id as biz_id,
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
            return $this->view('pages/errors/404', ['pageTitle' => $this->t('page_not_found')]);
        }

        // Check if business is logged in
        $isBusinessOwner = isset($_SESSION['business_id']) && $_SESSION['business_id'] == $booking['biz_id'];

        return $this->view('pages/booking/checkin', [
            'pageTitle' => $this->t('page_checkin') . $booking['booking_number'],
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

        // Update business total revenue
        $bookingAmount = (float)($booking['total_price'] ?? 0);
        if ($bookingAmount > 0) {
            $this->db->query(
                "UPDATE businesses SET total_revenue = total_revenue + ? WHERE id = ?",
                [$bookingAmount, $booking['business_id']]
            );
        }

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
            return $this->view('pages/errors/404', ['pageTitle' => $this->t('page_not_found')]);
        }

        // Check if already reviewed
        $stmt = $this->db->query(
            "SELECT id FROM reviews WHERE booking_id = ?",
            [$booking['id']]
        );
        $existingReview = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->view('pages/booking/review', [
            'pageTitle' => $this->t('page_review') . ' ' . $booking['business_name'],
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

        // Get the review ID for loyalty points
        $reviewId = $this->db->lastInsertId();

        // Award loyalty points for review (if user is logged in and loyalty is enabled)
        if ($booking['user_id']) {
            try {
                $loyaltyService = new LoyaltyService();
                $loyaltyService->awardReviewPoints(
                    $booking['user_id'],
                    $booking['business_id'],
                    $reviewId,
                    $booking['id']
                );
            } catch (\Exception $e) {
                error_log("Failed to award review loyalty points: " . $e->getMessage());
            }
        }

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
            return $this->json(['error' => $this->t('error_business_not_found')], 404);
        }

        if (!$this->verifyCsrf()) {
            return $this->json(['error' => $this->t('validation_invalid_request')], 400);
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
            return $this->json(['error' => $this->t('error_email')], 400);
        }

        $service = $this->getServiceById($serviceId);
        if (!$service || $service['business_id'] != $business['id']) {
            return $this->json(['error' => $this->t('booking_invalid_service')], 400);
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
                // Update waitlist status - 60 minutes to respond
                $expiresAt = date('Y-m-d H:i:s', strtotime('+60 minutes'));
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
        $customerLang = $data['language'] ?? $this->lang ?? 'nl';
        $translations = $this->loadTranslationsForLang($customerLang);
        $t = function($key, $replacements = []) use ($translations) {
            $text = $translations[$key] ?? $key;
            foreach ($replacements as $search => $replace) {
                $text = str_replace('{' . $search . '}', $replace, $text);
            }
            return $text;
        };

        $dateFormatted = date('d-m-Y', strtotime($data['date']));
        $currentYear = date('Y');
        $subject = $t('email_waitlist_confirmation_subject', ['business_name' => $data['business_name']]);

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:40px;text-align:center;">
                            <h1 style="margin:0;font-size:28px;font-weight:700;">{$t('email_waitlist_confirmation_heading')}</h1>
                            <p style="margin:10px 0 0;opacity:0.9;font-size:16px;">{$data['business_name']}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;margin:0 0 25px;">{$t('email_dear_name', ['name' => $data['name']])}</p>
                            <p style="font-size:16px;color:#555;line-height:1.6;margin:0 0 30px;">
                                {$t('email_on_waitlist_for', ['business_name' => $data['business_name']])}
                            </p>

                            <div style="background:linear-gradient(135deg,#fffbeb,#faf5ff);border-radius:12px;padding:25px;margin-bottom:30px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid rgba(0,0,0,0.1);">
                                            <span style="color:#cccccc;font-size:14px;">{$t('email_service_label')}</span><br>
                                            <strong style="color:#ffffff;font-size:16px;">{$data['service_name']}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;">
                                            <span style="color:#cccccc;font-size:14px;">{$t('email_preferred_date')}</span><br>
                                            <strong style="color:#ffffff;font-size:18px;">{$dateFormatted}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style="background:#0a0a0a;border-left:4px solid #000000;padding:20px;border-radius:0 8px 8px 0;">
                                <p style="margin:0;color:#ffffff;font-size:14px;">
                                    <strong>{$t('email_what_happens_now')}</strong><br><br>
                                    {$t('email_when_spot_available')} {$t('email_book_within_60_minutes')}
                                </p>
                            </div>

                            <p style="font-size:14px;color:#888;text-align:center;margin:25px 0 0;">
                                {$t('email_we_keep_you_posted')}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:25px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#cccccc;font-size:13px;">© {$currentYear} GlamourSchedule - Beauty & Wellness Platform</p>
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
            $mailer = new Mailer($customerLang);
            $mailer->send($data['email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send waitlist confirmation: " . $e->getMessage());
        }
    }

    /**
     * Send notification when slot becomes available
     */
    private function sendWaitlistNotificationEmail(array $data): void
    {
        $customerLang = $data['language'] ?? $this->lang ?? 'nl';
        $translations = $this->loadTranslationsForLang($customerLang);
        $t = function($key, $replacements = []) use ($translations) {
            $text = $translations[$key] ?? $key;
            foreach ($replacements as $search => $replace) {
                $text = str_replace('{' . $search . '}', $replace, $text);
            }
            return $text;
        };

        $dateFormatted = date('d-m-Y', strtotime($data['date']));
        $timeFormatted = date('H:i', strtotime($data['time']));
        $bookingUrl = "https://glamourschedule.com/business/{$data['business_slug']}?date={$data['date']}&time={$data['time']}";
        $subject = $t('email_spot_available_subject', ['business_name' => $data['business_name']]);

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
                            <h1 style="margin:0;font-size:24px;">{$t('email_spot_available_heading')}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;">{$t('email_good_news_name', ['name' => $data['name']])}</p>
                            <p style="font-size:16px;color:#555;line-height:1.6;">
                                {$t('email_booking_cancelled_you_are_first', ['business_name' => $data['business_name']])}
                            </p>

                            <div style="background:linear-gradient(135deg,#fafafa,#f5f5f5);border:2px solid #000000;border-radius:12px;padding:25px;margin:25px 0;text-align:center;">
                                <p style="margin:0;color:#cccccc;font-size:14px;">{$t('email_available_spot')}</p>
                                <p style="margin:10px 0 0;color:#ffffff;font-size:24px;font-weight:700;">
                                    {$dateFormatted} - {$timeFormatted}
                                </p>
                                <p style="margin:10px 0 0;color:#cccccc;">{$data['service_name']}</p>
                            </div>

                            <div style="background:#fef2f2;border-left:4px solid #dc2626;padding:15px 20px;border-radius:0 8px 8px 0;margin:25px 0;">
                                <p style="margin:0;color:#991b1b;font-size:14px;">
                                    <strong>{$t('email_attention_limited_time')}</strong><br>
                                    {$t('email_book_within_60_minutes')}
                                </p>
                            </div>

                            <p style="text-align:center;margin:30px 0;">
                                <a href="{$bookingUrl}" style="display:inline-block;background:#000000;color:#fff;padding:18px 50px;border-radius:50px;text-decoration:none;font-weight:700;font-size:17px;">
                                    {$t('email_book_now')}
                                </a>
                            </p>

                            <p style="font-size:14px;color:#888;text-align:center;">
                                {$t('email_cant_make_it')}
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
            $mailer = new Mailer($customerLang);
            $mailer->send($data['email'], $subject, $htmlBody);
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
            return $this->json(['error' => $this->t('validation_invalid_request')], 400);
        }

        $this->db->query(
            "UPDATE booking_waitlist SET status = 'cancelled' WHERE uuid = ?",
            [$uuid]
        );

        return $this->json(['success' => true, 'message' => 'Je bent van de wachtlijst verwijderd']);
    }
}

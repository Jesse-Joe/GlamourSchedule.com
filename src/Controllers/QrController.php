<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

/**
 * QR Code scanning API controller
 */
class QrController extends Controller
{
    /**
     * Process QR code scan for booking lookup
     * POST /api/qr/scan
     *
     * Supports:
     * - URL with UUID: /checkin/uuid or /booking/uuid
     * - Raw UUID: 36-character UUID
     * - Booking number: GS12345678
     * - Verification code: XXXX-XXXX-XXXX (SHA256-based, links business + customer)
     */
    public function scan(): string
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'error' => 'Method not allowed']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $qrData = trim($input['qr_data'] ?? '');
        $businessId = isset($input['business_id']) ? (int)$input['business_id'] : null;

        if (empty($qrData)) {
            return json_encode(['success' => false, 'error' => 'QR data is required']);
        }

        $booking = null;

        // Check if it's a URL with UUID (e.g., /checkin/uuid or /booking/uuid)
        if (preg_match('/(checkin|booking)\/([a-f0-9\-]+)/i', $qrData, $matches)) {
            $uuid = $matches[2];
            $booking = $this->findBookingByUuid($uuid);
        }
        // Check if it's a raw UUID
        elseif (preg_match('/^[a-f0-9\-]{36}$/i', $qrData)) {
            $booking = $this->findBookingByUuid($qrData);
        }
        // Check if it's a booking number (e.g., GS12345678)
        elseif (preg_match('/^GS[A-F0-9]{8}$/i', $qrData)) {
            $booking = $this->findBookingByNumber(strtoupper($qrData));
        }
        // Check if it's a verification code (format: XXXX-XXXX-XXXX)
        elseif (preg_match('/^[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}$/i', $qrData)) {
            $booking = $this->findBookingByVerificationCode(strtoupper($qrData));
        }

        if (!$booking) {
            return json_encode(['success' => false, 'error' => 'Boeking niet gevonden']);
        }

        // Verify business match if business_id is provided (security check)
        if ($businessId !== null && (int)$booking['business_id'] !== $businessId) {
            return json_encode([
                'success' => false,
                'error' => 'Deze boeking hoort niet bij uw salon',
                'error_code' => 'BUSINESS_MISMATCH'
            ]);
        }

        return json_encode([
            'success' => true,
            'booking' => [
                'uuid' => $booking['uuid'],
                'booking_number' => $booking['booking_number'],
                'verification_code' => $booking['verification_code'] ?? null,
                'status' => $booking['status'],
                'payment_status' => $booking['payment_status'],
                'service_name' => $booking['service_name'],
                'business_id' => (int)$booking['business_id'],
                'business_name' => $booking['business_name'],
                'appointment_date' => $booking['appointment_date'],
                'appointment_time' => substr($booking['appointment_time'], 0, 5),
                'customer_name' => $booking['guest_name'] ?: trim($booking['first_name'] . ' ' . $booking['last_name'])
            ]
        ]);
    }

    /**
     * Find booking by UUID
     */
    private function findBookingByUuid(string $uuid): ?array
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.business_id, s.name as service_name, biz.company_name as business_name,
                    u.first_name, u.last_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             JOIN businesses biz ON b.business_id = biz.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.uuid = ?",
            [$uuid]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Find booking by booking number
     */
    private function findBookingByNumber(string $bookingNumber): ?array
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.business_id, s.name as service_name, biz.company_name as business_name,
                    u.first_name, u.last_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             JOIN businesses biz ON b.business_id = biz.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.booking_number = ?",
            [$bookingNumber]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Find booking by SHA256 verification code
     * Format: XXXX-XXXX-XXXX (12 hex characters with dashes)
     */
    private function findBookingByVerificationCode(string $verificationCode): ?array
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.business_id, s.name as service_name, biz.company_name as business_name,
                    u.first_name, u.last_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             JOIN businesses biz ON b.business_id = biz.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.verification_code = ?",
            [$verificationCode]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}

<?php
namespace GlamourSchedule\Services;

use GlamourSchedule\Core\Database;

/**
 * Cryptographic signature service for bookings
 *
 * Uses HMAC-SHA256 with per-business signing keys to create unforgeable
 * booking signatures. This ensures only valid bookings from our system
 * can pass verification during check-in.
 *
 * Security features:
 * - Per-business isolation: compromise of one key doesn't affect others
 * - Timing-safe comparison: prevents timing attacks
 * - Version field: allows future signature format upgrades
 * - Backwards compatible: legacy bookings without signatures still work
 */
class BookingSignatureService
{
    private \PDO $db;

    // Current signature version
    private const SIGNATURE_VERSION = 1;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Generate a cryptographically secure 256-bit signing key
     *
     * @return string 64-character hex string (256 bits)
     */
    public function generateSigningKey(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Get or create signing key for a business
     *
     * @param int $businessId
     * @return string|null The signing key or null if business not found
     */
    public function getBusinessSigningKey(int $businessId): ?string
    {
        $stmt = $this->db->prepare("SELECT signing_key FROM businesses WHERE id = ?");
        $stmt->execute([$businessId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        // If no key exists, generate one
        if (empty($result['signing_key'])) {
            $key = $this->generateSigningKey();
            $this->setBusinessSigningKey($businessId, $key);
            return $key;
        }

        return $result['signing_key'];
    }

    /**
     * Set signing key for a business
     *
     * @param int $businessId
     * @param string $key
     * @return bool
     */
    public function setBusinessSigningKey(int $businessId, string $key): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE businesses SET signing_key = ?, signing_key_created_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$key, $businessId]);
    }

    /**
     * Create HMAC-SHA256 signature for a booking
     *
     * Message format (version 1):
     * "1|business_id|uuid|booking_number|date|time"
     *
     * @param int $businessId
     * @param string $uuid Booking UUID
     * @param string $bookingNumber Booking number (e.g., GS12345678)
     * @param string $date Appointment date (Y-m-d)
     * @param string $time Appointment time (H:i or H:i:s)
     * @return array ['signature' => string, 'version' => int] or null on failure
     */
    public function signBooking(int $businessId, string $uuid, string $bookingNumber, string $date, string $time): ?array
    {
        $signingKey = $this->getBusinessSigningKey($businessId);
        if (!$signingKey) {
            error_log("BookingSignatureService: No signing key for business {$businessId}");
            return null;
        }

        // Normalize time to H:i format
        $normalizedTime = substr($time, 0, 5);

        // Build message string
        $message = sprintf(
            '%d|%d|%s|%s|%s|%s',
            self::SIGNATURE_VERSION,
            $businessId,
            $uuid,
            $bookingNumber,
            $date,
            $normalizedTime
        );

        // Generate HMAC-SHA256 signature
        $signature = hash_hmac('sha256', $message, $signingKey);

        return [
            'signature' => $signature,
            'version' => self::SIGNATURE_VERSION
        ];
    }

    /**
     * Verify a booking's cryptographic signature
     *
     * @param int $businessId
     * @param array $booking Booking data with uuid, booking_number, appointment_date,
     *                       appointment_time, signature, and optionally signature_version
     * @return bool True if signature is valid
     */
    public function verifyBooking(int $businessId, array $booking): bool
    {
        // Legacy bookings without signatures are verified via legacy method
        if (empty($booking['signature'])) {
            return $this->verifyLegacyBooking($businessId, $booking);
        }

        $signingKey = $this->getBusinessSigningKey($businessId);
        if (!$signingKey) {
            error_log("BookingSignatureService: Cannot verify - no signing key for business {$businessId}");
            return false;
        }

        $version = (int)($booking['signature_version'] ?? 1);

        // Only support version 1 currently
        if ($version !== 1) {
            error_log("BookingSignatureService: Unsupported signature version {$version}");
            return false;
        }

        // Normalize time to H:i format
        $normalizedTime = substr($booking['appointment_time'], 0, 5);

        // Rebuild message string
        $message = sprintf(
            '%d|%d|%s|%s|%s|%s',
            $version,
            $businessId,
            $booking['uuid'],
            $booking['booking_number'],
            $booking['appointment_date'],
            $normalizedTime
        );

        // Generate expected signature
        $expectedSignature = hash_hmac('sha256', $message, $signingKey);

        // Timing-safe comparison to prevent timing attacks
        return hash_equals($expectedSignature, $booking['signature']);
    }

    /**
     * Verify legacy bookings that don't have cryptographic signatures
     *
     * Falls back to verification_code check if present, otherwise
     * allows the booking (for backwards compatibility during migration)
     *
     * @param int $businessId
     * @param array $booking
     * @return bool
     */
    public function verifyLegacyBooking(int $businessId, array $booking): bool
    {
        // If booking has verification_code, verify using legacy method
        if (!empty($booking['verification_code'])) {
            return $this->verifyLegacyVerificationCode($businessId, $booking);
        }

        // Bookings without any verification mechanism are allowed during migration
        // This ensures existing bookings continue to work
        // Once migration completes, this can be made stricter
        return true;
    }

    /**
     * Verify legacy verification code (SHA256-based)
     *
     * @param int $businessId
     * @param array $booking
     * @return bool
     */
    private function verifyLegacyVerificationCode(int $businessId, array $booking): bool
    {
        $secretKey = $_ENV['APP_KEY'] ?? null;
        if (!$secretKey) {
            error_log('SECURITY WARNING: APP_KEY environment variable not set');
            return false;
        }
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

        // Normalize stored verification code
        $storedCode = strtoupper(str_replace('-', '', $booking['verification_code'] ?? ''));
        if (strlen($storedCode) >= 12) {
            $storedCode = substr($storedCode, 0, 4) . '-' . substr($storedCode, 4, 4) . '-' . substr($storedCode, 8, 4);
        }

        return hash_equals($expectedCode, $storedCode);
    }

    /**
     * Sign an existing booking (for migration)
     *
     * @param array $booking Full booking record
     * @return bool True if successfully signed
     */
    public function signExistingBooking(array $booking): bool
    {
        $signatureData = $this->signBooking(
            (int)$booking['business_id'],
            $booking['uuid'],
            $booking['booking_number'],
            $booking['appointment_date'],
            $booking['appointment_time']
        );

        if (!$signatureData) {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE bookings SET signature = ?, signature_version = ? WHERE id = ?"
        );

        return $stmt->execute([
            $signatureData['signature'],
            $signatureData['version'],
            $booking['id']
        ]);
    }

    /**
     * Rotate signing key for a business
     *
     * WARNING: This will invalidate all existing booking signatures!
     * Should only be used in emergency situations or with re-signing all bookings.
     *
     * @param int $businessId
     * @param bool $resignBookings If true, re-signs all existing bookings
     * @return string|null New signing key or null on failure
     */
    public function rotateSigningKey(int $businessId, bool $resignBookings = true): ?string
    {
        $newKey = $this->generateSigningKey();

        if (!$this->setBusinessSigningKey($businessId, $newKey)) {
            return null;
        }

        if ($resignBookings) {
            $this->resignAllBusinessBookings($businessId);
        }

        return $newKey;
    }

    /**
     * Re-sign all bookings for a business
     *
     * @param int $businessId
     * @return int Number of bookings re-signed
     */
    public function resignAllBusinessBookings(int $businessId): int
    {
        $stmt = $this->db->prepare(
            "SELECT id, uuid, booking_number, appointment_date, appointment_time, business_id
             FROM bookings WHERE business_id = ?"
        );
        $stmt->execute([$businessId]);
        $bookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $count = 0;
        foreach ($bookings as $booking) {
            if ($this->signExistingBooking($booking)) {
                $count++;
            }
        }

        return $count;
    }
}

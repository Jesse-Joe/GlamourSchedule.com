<?php
namespace GlamourSchedule\Services;

use GlamourSchedule\Core\Database;

/**
 * Loyalty Points Service
 * Handles all loyalty points operations including earning, redeeming, and balance management
 */
class LoyaltyService
{
    private Database $db;

    // Points configuration
    private const POINTS_PER_BOOKING = 100;
    private const POINTS_PER_REVIEW = 35;
    private const POINTS_PER_PERCENT = 100; // 100 points = 1% discount

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Check if loyalty program is enabled for a business
     */
    public function isEnabled(int $businessId): bool
    {
        $stmt = $this->db->query(
            "SELECT loyalty_enabled FROM business_settings WHERE business_id = ?",
            [$businessId]
        );
        $settings = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (bool)($settings['loyalty_enabled'] ?? false);
    }

    /**
     * Get the max redeemable points setting for a business
     */
    public function getMaxRedeemSetting(int $businessId): int
    {
        $stmt = $this->db->query(
            "SELECT loyalty_max_redeem_points FROM business_settings WHERE business_id = ?",
            [$businessId]
        );
        $settings = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int)($settings['loyalty_max_redeem_points'] ?? 2000);
    }

    /**
     * Get user's points balance for a specific business
     */
    public function getBalance(int $userId, int $businessId): int
    {
        $stmt = $this->db->query(
            "SELECT total_points FROM loyalty_points WHERE user_id = ? AND business_id = ?",
            [$userId, $businessId]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int)($result['total_points'] ?? 0);
    }

    /**
     * Get user's lifetime points for a specific business
     */
    public function getLifetimePoints(int $userId, int $businessId): int
    {
        $stmt = $this->db->query(
            "SELECT lifetime_points FROM loyalty_points WHERE user_id = ? AND business_id = ?",
            [$userId, $businessId]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int)($result['lifetime_points'] ?? 0);
    }

    /**
     * Get all loyalty balances for a user across all businesses
     */
    public function getAllBalances(int $userId): array
    {
        $stmt = $this->db->query(
            "SELECT lp.*, b.company_name, b.slug, b.logo
             FROM loyalty_points lp
             JOIN businesses b ON lp.business_id = b.id
             WHERE lp.user_id = ? AND lp.total_points > 0
             ORDER BY lp.total_points DESC",
            [$userId]
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get total points across all businesses for a user
     */
    public function getTotalPoints(int $userId): int
    {
        $stmt = $this->db->query(
            "SELECT SUM(total_points) as total FROM loyalty_points WHERE user_id = ?",
            [$userId]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int)($result['total'] ?? 0);
    }

    /**
     * Award points for completing a booking
     */
    public function awardBookingPoints(int $userId, int $businessId, int $bookingId): bool
    {
        if (!$this->isEnabled($businessId)) {
            return false;
        }

        // Check if points were already awarded for this booking
        $stmt = $this->db->query(
            "SELECT id FROM loyalty_transactions
             WHERE booking_id = ? AND transaction_type = 'earn_booking'",
            [$bookingId]
        );
        if ($stmt->fetch()) {
            return false; // Already awarded
        }

        return $this->addPoints(
            $userId,
            $businessId,
            self::POINTS_PER_BOOKING,
            'earn_booking',
            $bookingId,
            null,
            'Punten verdiend voor voltooide boeking'
        );
    }

    /**
     * Award points for submitting a review
     */
    public function awardReviewPoints(int $userId, int $businessId, int $reviewId, int $bookingId = null): bool
    {
        if (!$this->isEnabled($businessId)) {
            return false;
        }

        // Check if points were already awarded for this review
        $stmt = $this->db->query(
            "SELECT id FROM loyalty_transactions
             WHERE review_id = ? AND transaction_type = 'earn_review'",
            [$reviewId]
        );
        if ($stmt->fetch()) {
            return false; // Already awarded
        }

        return $this->addPoints(
            $userId,
            $businessId,
            self::POINTS_PER_REVIEW,
            'earn_review',
            $bookingId,
            $reviewId,
            'Punten verdiend voor review'
        );
    }

    /**
     * Calculate discount amount from points
     * 100 points = 1% discount
     */
    public function calculateDiscount(int $points, float $servicePrice): float
    {
        if ($points <= 0 || $servicePrice <= 0) {
            return 0.00;
        }

        $discountPercent = $points / self::POINTS_PER_PERCENT;
        $discount = ($servicePrice * $discountPercent) / 100;

        return round($discount, 2);
    }

    /**
     * Get the maximum redeemable points for a booking
     * Takes into account: user balance, business max setting, service price limit
     */
    public function getMaxRedeemablePoints(int $businessId, int $userPoints, float $servicePrice): int
    {
        if (!$this->isEnabled($businessId)) {
            return 0;
        }

        // Get business max redeem setting
        $maxBusinessPoints = $this->getMaxRedeemSetting($businessId);

        // Calculate max points that would give 100% discount (we cap at service price)
        $maxForPrice = (int)floor(($servicePrice / $servicePrice) * 100 * self::POINTS_PER_PERCENT);

        // The maximum is the minimum of: user balance, business setting, and price limit
        return min($userPoints, $maxBusinessPoints, $maxForPrice);
    }

    /**
     * Redeem points for a discount on a booking
     */
    public function redeemPoints(int $userId, int $businessId, int $bookingId, int $points): bool
    {
        if ($points <= 0) {
            return false;
        }

        if (!$this->isEnabled($businessId)) {
            return false;
        }

        $currentBalance = $this->getBalance($userId, $businessId);
        if ($currentBalance < $points) {
            return false; // Not enough points
        }

        // Check if points were already redeemed for this booking
        $stmt = $this->db->query(
            "SELECT id FROM loyalty_transactions
             WHERE booking_id = ? AND transaction_type = 'redeem'",
            [$bookingId]
        );
        if ($stmt->fetch()) {
            return false; // Already redeemed
        }

        return $this->subtractPoints(
            $userId,
            $businessId,
            $points,
            'redeem',
            $bookingId,
            null,
            'Punten ingewisseld voor korting'
        );
    }

    /**
     * Get transaction history for a user at a specific business
     */
    public function getTransactionHistory(int $userId, int $businessId, int $limit = 20): array
    {
        $stmt = $this->db->query(
            "SELECT lt.*, b.booking_number
             FROM loyalty_transactions lt
             LEFT JOIN bookings b ON lt.booking_id = b.id
             WHERE lt.user_id = ? AND lt.business_id = ?
             ORDER BY lt.created_at DESC
             LIMIT ?",
            [$userId, $businessId, $limit]
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all transaction history for a user
     */
    public function getAllTransactionHistory(int $userId, int $limit = 50): array
    {
        $stmt = $this->db->query(
            "SELECT lt.*, b.booking_number, bus.company_name
             FROM loyalty_transactions lt
             LEFT JOIN bookings b ON lt.booking_id = b.id
             JOIN businesses bus ON lt.business_id = bus.id
             WHERE lt.user_id = ?
             ORDER BY lt.created_at DESC
             LIMIT ?",
            [$userId, $limit]
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Add points to user's balance
     */
    private function addPoints(
        int $userId,
        int $businessId,
        int $points,
        string $type,
        ?int $bookingId,
        ?int $reviewId,
        string $description
    ): bool {
        $currentBalance = $this->getBalance($userId, $businessId);
        $newBalance = $currentBalance + $points;

        // Update or create balance record
        $this->db->query(
            "INSERT INTO loyalty_points (user_id, business_id, total_points, lifetime_points)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
             total_points = total_points + VALUES(total_points),
             lifetime_points = lifetime_points + VALUES(total_points)",
            [$userId, $businessId, $points, $points]
        );

        // Record transaction
        $uuid = $this->generateUuid();
        $this->db->query(
            "INSERT INTO loyalty_transactions
             (uuid, user_id, business_id, booking_id, review_id, transaction_type, points, points_before, points_after, description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$uuid, $userId, $businessId, $bookingId, $reviewId, $type, $points, $currentBalance, $newBalance, $description]
        );

        return true;
    }

    /**
     * Subtract points from user's balance
     */
    private function subtractPoints(
        int $userId,
        int $businessId,
        int $points,
        string $type,
        ?int $bookingId,
        ?int $reviewId,
        string $description
    ): bool {
        $currentBalance = $this->getBalance($userId, $businessId);
        $newBalance = $currentBalance - $points;

        if ($newBalance < 0) {
            return false;
        }

        // Update balance
        $this->db->query(
            "UPDATE loyalty_points SET total_points = ? WHERE user_id = ? AND business_id = ?",
            [$newBalance, $userId, $businessId]
        );

        // Record transaction (negative points)
        $uuid = $this->generateUuid();
        $this->db->query(
            "INSERT INTO loyalty_transactions
             (uuid, user_id, business_id, booking_id, review_id, transaction_type, points, points_before, points_after, description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$uuid, $userId, $businessId, $bookingId, $reviewId, $type, -$points, $currentBalance, $newBalance, $description]
        );

        return true;
    }

    /**
     * Generate UUID
     */
    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Get points earned per booking
     */
    public static function getPointsPerBooking(): int
    {
        return self::POINTS_PER_BOOKING;
    }

    /**
     * Get points earned per review
     */
    public static function getPointsPerReview(): int
    {
        return self::POINTS_PER_REVIEW;
    }

    /**
     * Get points required for 1% discount
     */
    public static function getPointsPerPercent(): int
    {
        return self::POINTS_PER_PERCENT;
    }
}

<?php
namespace GlamourSchedule\Services;

use GlamourSchedule\Core\Database;

/**
 * Marketing Bid Service
 * Handles bidding on marketing sections with currency-aware minimum increments
 */
class MarketingBidService
{
    private Database $db;
    private CurrencyService $currencyService;

    // Base minimum increment in EUR
    private const BASE_INCREMENT_EUR = 25.00;

    // Round-to values per currency for clean bid amounts
    private array $roundingRules = [
        'EUR' => 5,      // Round to nearest €5
        'USD' => 5,      // Round to nearest $5
        'GBP' => 5,      // Round to nearest £5
        'CHF' => 5,      // Round to nearest 5 CHF
        'SEK' => 50,     // Round to nearest 50 kr
        'NOK' => 50,     // Round to nearest 50 kr
        'DKK' => 25,     // Round to nearest 25 kr
        'PLN' => 25,     // Round to nearest 25 zł
        'CZK' => 100,    // Round to nearest 100 Kč
        'HUF' => 1000,   // Round to nearest 1000 Ft
        'RON' => 25,     // Round to nearest 25 lei
        'JPY' => 500,    // Round to nearest ¥500
        'CNY' => 25,     // Round to nearest ¥25
        'INR' => 500,    // Round to nearest ₹500
        'BRL' => 25,     // Round to nearest R$25
        'MXN' => 100,    // Round to nearest MX$100
        'ZAR' => 100,    // Round to nearest R100
        'KRW' => 5000,   // Round to nearest ₩5000
        'TRY' => 100,    // Round to nearest ₺100
        'AED' => 25,     // Round to nearest 25 AED
        'SAR' => 25,     // Round to nearest 25 SAR
        'AUD' => 5,      // Round to nearest A$5
        'CAD' => 5,      // Round to nearest C$5
        'NZD' => 5,      // Round to nearest NZ$5
        'SGD' => 5,      // Round to nearest S$5
        'HKD' => 25,     // Round to nearest HK$25
        'THB' => 100,    // Round to nearest ฿100
        'IDR' => 50000,  // Round to nearest Rp50.000
        'VND' => 100000, // Round to nearest 100.000₫
        'PKR' => 500,    // Round to nearest ₨500
        'EGP' => 100,    // Round to nearest E£100
        'NGN' => 5000,   // Round to nearest ₦5000
        'KES' => 500,    // Round to nearest KSh500
    ];

    public function __construct(Database $db, CurrencyService $currencyService = null)
    {
        $this->db = $db;
        $this->currencyService = $currencyService ?? new CurrencyService();
    }

    /**
     * Get all active marketing sections with reservation counts and current highest bid
     */
    public function getSectionsWithStats(): array
    {
        $sections = $this->db->fetchAll(
            "SELECT ms.*,
                    (SELECT COUNT(*) FROM marketing_bids mb
                     WHERE mb.section_id = ms.id
                     AND mb.status = 'active'
                     AND mb.end_date >= CURDATE()) as active_bids,
                    (SELECT MAX(mb.bid_amount_eur) FROM marketing_bids mb
                     WHERE mb.section_id = ms.id
                     AND mb.status = 'active'
                     AND mb.end_date >= CURDATE()) as highest_bid_eur,
                    (SELECT b2.business_id FROM marketing_bids b2
                     WHERE b2.section_id = ms.id
                     AND b2.status = 'active'
                     AND b2.end_date >= CURDATE()
                     ORDER BY b2.bid_amount_eur DESC LIMIT 1) as highest_bidder_id
             FROM marketing_sections ms
             WHERE ms.is_active = 1
             ORDER BY ms.sort_order ASC"
        );

        // Add reservation counts per category group
        foreach ($sections as &$section) {
            $section['reservation_count'] = $this->getReservationCount($section['category_group']);
            $section['reservation_trend'] = $this->getReservationTrend($section['category_group']);
        }

        return $sections;
    }

    /**
     * Get reservation count for a category group (last 30 days)
     */
    private function getReservationCount(?string $categoryGroup): int
    {
        if (!$categoryGroup) {
            // For non-category sections (homepage, search, sidebar, email), count all bookings
            return (int)$this->db->fetch(
                "SELECT COUNT(*) as cnt FROM bookings
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 AND status NOT IN ('cancelled')",
            )['cnt'];
        }

        return (int)$this->db->fetch(
            "SELECT COUNT(*) as cnt FROM bookings b
             JOIN services s ON b.service_id = s.id
             JOIN categories c ON s.category_id = c.id
             WHERE c.category_group = ?
             AND b.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             AND b.status NOT IN ('cancelled')",
            [$categoryGroup]
        )['cnt'];
    }

    /**
     * Get reservation trend (percentage change vs previous 30 days)
     */
    private function getReservationTrend(?string $categoryGroup): float
    {
        if (!$categoryGroup) {
            $current = (int)$this->db->fetch(
                "SELECT COUNT(*) as cnt FROM bookings
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 AND status NOT IN ('cancelled')"
            )['cnt'];
            $previous = (int)$this->db->fetch(
                "SELECT COUNT(*) as cnt FROM bookings
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)
                 AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
                 AND status NOT IN ('cancelled')"
            )['cnt'];
        } else {
            $current = (int)$this->db->fetch(
                "SELECT COUNT(*) as cnt FROM bookings b
                 JOIN services s ON b.service_id = s.id
                 JOIN categories c ON s.category_id = c.id
                 WHERE c.category_group = ?
                 AND b.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 AND b.status NOT IN ('cancelled')",
                [$categoryGroup]
            )['cnt'];
            $previous = (int)$this->db->fetch(
                "SELECT COUNT(*) as cnt FROM bookings b
                 JOIN services s ON b.service_id = s.id
                 JOIN categories c ON s.category_id = c.id
                 WHERE c.category_group = ?
                 AND b.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)
                 AND b.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
                 AND b.status NOT IN ('cancelled')",
                [$categoryGroup]
            )['cnt'];
        }

        if ($previous === 0) return $current > 0 ? 100.0 : 0.0;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Calculate minimum bid for a section in a given currency
     * Base: €25 minimum increment, converted and rounded to local currency
     */
    public function getMinimumBid(int $sectionId, string $countryCode): array
    {
        $section = $this->db->fetch(
            "SELECT * FROM marketing_sections WHERE id = ?",
            [$sectionId]
        );

        if (!$section) {
            return ['error' => 'Section not found'];
        }

        $currency = $this->currencyService->getCurrencyForCountry($countryCode);
        $currentHighest = $this->getCurrentHighestBid($sectionId);

        // If no bids yet, minimum bid is the section's min_bid_eur
        if (!$currentHighest) {
            $minBidEur = (float)$section['min_bid_eur'];
        } else {
            // Minimum bid = current highest + increment
            $minBidEur = (float)$currentHighest + self::BASE_INCREMENT_EUR;
        }

        $result = $this->formatBidAmount($minBidEur, $currency, $countryCode);
        // Keep original EUR threshold for validation (before rounding inflates it)
        $result['min_eur_threshold'] = $minBidEur;
        return $result;
    }

    /**
     * Calculate the minimum increment in a given currency
     */
    public function getMinimumIncrement(string $countryCode): array
    {
        $currency = $this->currencyService->getCurrencyForCountry($countryCode);
        return $this->formatBidAmount(self::BASE_INCREMENT_EUR, $currency, $countryCode);
    }

    /**
     * Format a EUR bid amount to local currency with proper rounding
     */
    private function formatBidAmount(float $eurAmount, string $currency, string $countryCode): array
    {
        $converted = $this->currencyService->convertFromEur($eurAmount, $currency);
        $localAmount = $converted['local_amount'];

        // Apply rounding rules for clean bid amounts
        $roundTo = $this->roundingRules[$currency] ?? 5;
        $roundedLocal = ceil($localAmount / $roundTo) * $roundTo;

        // Recalculate EUR equivalent of the rounded amount
        if ($currency !== 'EUR') {
            $rate = $converted['exchange_rate'];
            $roundedEur = $rate > 0 ? round($roundedLocal / $rate, 2) : $eurAmount;
        } else {
            $roundedEur = $roundedLocal;
            $roundedLocal = $roundedEur;
        }

        $symbol = $this->currencyService->getSymbol($currency);

        return [
            'eur_amount' => $roundedEur,
            'local_amount' => $roundedLocal,
            'local_currency' => $currency,
            'local_symbol' => $symbol,
            'local_formatted' => $symbol . number_format($roundedLocal, $this->getDecimals($currency), ',', '.'),
            'eur_formatted' => '€' . number_format($roundedEur, 2, ',', '.'),
            'increment_eur' => self::BASE_INCREMENT_EUR,
            'increment_local' => ceil(($this->currencyService->convertFromEur(self::BASE_INCREMENT_EUR, $currency)['local_amount']) / $roundTo) * $roundTo,
            'increment_formatted' => $symbol . number_format(
                ceil(($this->currencyService->convertFromEur(self::BASE_INCREMENT_EUR, $currency)['local_amount']) / $roundTo) * $roundTo,
                $this->getDecimals($currency), ',', '.'
            ),
            'rounding_step' => $roundTo,
            'show_dual' => $currency !== 'EUR'
        ];
    }

    /**
     * Get number of decimal places for a currency
     */
    private function getDecimals(string $currency): int
    {
        $noDecimals = ['JPY', 'KRW', 'VND', 'IDR', 'HUF', 'CLP', 'ISK'];
        return in_array($currency, $noDecimals) ? 0 : 2;
    }

    /**
     * Get current highest bid for a section
     */
    public function getCurrentHighestBid(int $sectionId): ?float
    {
        $result = $this->db->fetch(
            "SELECT MAX(bid_amount_eur) as highest FROM marketing_bids
             WHERE section_id = ? AND status = 'active' AND end_date >= CURDATE()",
            [$sectionId]
        );
        return $result['highest'] ? (float)$result['highest'] : null;
    }

    /**
     * Place a bid on a marketing section
     */
    public function placeBid(int $businessId, int $sectionId, float $bidAmountEur, string $countryCode): array
    {
        // Validate section exists
        $section = $this->db->fetch(
            "SELECT * FROM marketing_sections WHERE id = ? AND is_active = 1",
            [$sectionId]
        );
        if (!$section) {
            return ['success' => false, 'error' => 'Section not found'];
        }

        // Validate minimum bid (use original EUR threshold, not rounded-up amount)
        $minBid = $this->getMinimumBid($sectionId, $countryCode);
        $threshold = $minBid['min_eur_threshold'] ?? $minBid['eur_amount'];
        if ($bidAmountEur < $threshold) {
            return [
                'success' => false,
                'error' => 'Bid too low',
                'minimum' => $minBid
            ];
        }

        // Check if business already has an active bid on this section
        $existingBid = $this->db->fetch(
            "SELECT * FROM marketing_bids
             WHERE business_id = ? AND section_id = ? AND status = 'active' AND end_date >= CURDATE()",
            [$businessId, $sectionId]
        );

        $currency = $this->currencyService->getCurrencyForCountry($countryCode);
        $localAmount = $this->currencyService->convertFromEur($bidAmountEur, $currency);
        $uuid = $this->generateUuid();

        // Start date = today, end date = 30 days from now (campaign period)
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));

        if ($existingBid) {
            // Update existing bid (raise)
            $this->db->query(
                "UPDATE marketing_bids SET
                    bid_amount_eur = ?, bid_currency = ?, bid_amount_local = ?,
                    updated_at = NOW()
                 WHERE id = ?",
                [$bidAmountEur, $currency, $localAmount['local_amount'], $existingBid['id']]
            );
            $bidId = $existingBid['id'];
        } else {
            // Place new bid
            $this->db->query(
                "INSERT INTO marketing_bids (uuid, business_id, section_id, bid_amount_eur, bid_currency, bid_amount_local, status, start_date, end_date)
                 VALUES (?, ?, ?, ?, ?, ?, 'active', ?, ?)",
                [$uuid, $businessId, $sectionId, $bidAmountEur, $currency, $localAmount['local_amount'], $startDate, $endDate]
            );
            $bidId = $this->db->lastInsertId();
        }

        // Mark lower bids as outbid
        $this->db->query(
            "UPDATE marketing_bids SET status = 'outbid'
             WHERE section_id = ? AND bid_amount_eur < ? AND status = 'active' AND id != ?",
            [$sectionId, $bidAmountEur, $bidId]
        );

        return [
            'success' => true,
            'bid_id' => $bidId,
            'amount_eur' => $bidAmountEur,
            'amount_local' => $localAmount
        ];
    }

    /**
     * Get bids for a specific business
     */
    public function getBusinessBids(int $businessId): array
    {
        return $this->db->fetchAll(
            "SELECT mb.*, ms.name_key, ms.icon, ms.slug as section_slug
             FROM marketing_bids mb
             JOIN marketing_sections ms ON mb.section_id = ms.id
             WHERE mb.business_id = ?
             ORDER BY mb.created_at DESC",
            [$businessId]
        );
    }

    /**
     * Get leaderboard for a section (top 5 bids)
     */
    public function getSectionLeaderboard(int $sectionId): array
    {
        return $this->db->fetchAll(
            "SELECT mb.bid_amount_eur, mb.bid_currency, mb.bid_amount_local,
                    b.company_name, b.logo
             FROM marketing_bids mb
             JOIN businesses b ON mb.business_id = b.id
             WHERE mb.section_id = ? AND mb.status = 'active' AND mb.end_date >= CURDATE()
             ORDER BY mb.bid_amount_eur DESC
             LIMIT 5",
            [$sectionId]
        );
    }

    /**
     * Get bid calculation breakdown for display
     * Shows how the minimum increment works in local currency
     */
    public function getBidCalculation(int $sectionId, string $countryCode): array
    {
        $currency = $this->currencyService->getCurrencyForCountry($countryCode);
        $symbol = $this->currencyService->getSymbol($currency);
        $section = $this->db->fetch("SELECT * FROM marketing_sections WHERE id = ?", [$sectionId]);

        if (!$section) return [];

        $currentHighest = $this->getCurrentHighestBid($sectionId);
        $minBid = $this->getMinimumBid($sectionId, $countryCode);
        $increment = $this->getMinimumIncrement($countryCode);

        $steps = [];
        $baseEur = $currentHighest ?? (float)$section['min_bid_eur'];

        // Show 5 possible bid steps
        for ($i = 0; $i < 5; $i++) {
            $stepEur = $baseEur + (self::BASE_INCREMENT_EUR * $i);
            if ($i === 0 && $currentHighest) {
                $stepEur = $baseEur + self::BASE_INCREMENT_EUR; // First step is always +increment
            } elseif ($i === 0) {
                $stepEur = $baseEur; // Start from min bid
            } else {
                $stepEur = $baseEur + (self::BASE_INCREMENT_EUR * ($currentHighest ? $i : $i));
            }

            $formatted = $this->formatBidAmount($stepEur, $currency, $countryCode);
            $steps[] = [
                'step' => $i + 1,
                'eur_amount' => $formatted['eur_amount'],
                'local_amount' => $formatted['local_amount'],
                'local_formatted' => $formatted['local_formatted'],
                'eur_formatted' => $formatted['eur_formatted'],
            ];
        }

        return [
            'section' => $section,
            'currency' => $currency,
            'symbol' => $symbol,
            'current_highest_eur' => $currentHighest,
            'current_highest_local' => $currentHighest
                ? $this->currencyService->convertFromEur($currentHighest, $currency)['local_formatted']
                : null,
            'minimum_bid' => $minBid,
            'increment' => $increment,
            'steps' => $steps,
            'show_dual' => $currency !== 'EUR'
        ];
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        http_response_code(200);

        // Get GeoIP data for country filtering and localized pricing
        $geoIP = new \GlamourSchedule\Core\GeoIP($this->db);
        $location = $geoIP->lookup();
        $countryCode = $location['country_code'] ?? 'NL';

        $boostedBusinesses = $this->getBoostedBusinesses($countryCode);
        $featuredBusinesses = $this->getFeaturedBusinesses($countryCode);
        $categories = $this->getCategories();
        $stats = $this->getPlatformStats($countryCode);

        // If no businesses in visitor's country, fall back to global results
        $showingGlobal = false;
        if (empty($boostedBusinesses) && empty($featuredBusinesses) && $stats['businesses'] == 0) {
            $showingGlobal = true;
            $boostedBusinesses = $this->getBoostedBusinessesGlobal();
            $featuredBusinesses = $this->getFeaturedBusinessesGlobal();
            $stats = $this->getPlatformStatsGlobal();
        }

        $countryStats = $this->getCountryStats();

        // Get localized pricing for visitor's country
        $promo = $geoIP->getPromotionPriceWithCurrency($countryCode);

        return $this->view('pages/home', [
            'pageTitle' => 'Home',
            'boostedBusinesses' => $boostedBusinesses,
            'featuredBusinesses' => $featuredBusinesses,
            'categories' => $categories,
            'stats' => $stats,
            'countryStats' => $countryStats,
            'promo' => $promo,
            'showDualCurrency' => $promo['show_dual'] ?? false,
            'visitorCountry' => $countryCode,
            'showingGlobal' => $showingGlobal
        ]);
    }

    /**
     * Get boosted businesses (paid promotion)
     * Only show businesses with active boost (boost_expires_at > NOW())
     * Filtered by visitor's country
     */
    private function getBoostedBusinesses(string $visitorCountry): array
    {
        $countryNames = $this->getCountryVariants($visitorCountry);
        $placeholders = implode(',', array_fill(0, count($countryNames), '?'));

        $stmt = $this->db->query(
            "SELECT b.*, b.company_name as name,
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(DISTINCT r.id) as review_count,
                    MIN(s.price) as min_price
             FROM businesses b
             LEFT JOIN reviews r ON b.id = r.business_id
             LEFT JOIN services s ON b.id = s.business_id AND s.is_active = 1
             WHERE b.status = 'active'
               AND b.is_boosted = 1
               AND b.boost_expires_at > NOW()
               AND b.country IN ({$placeholders})
             GROUP BY b.id
             ORDER BY b.boost_expires_at DESC
             LIMIT 9",
            $countryNames
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get featured businesses based on monthly bookings
     * Filtered by visitor's country
     */
    private function getFeaturedBusinesses(string $visitorCountry): array
    {
        $countryNames = $this->getCountryVariants($visitorCountry);
        $placeholders = implode(',', array_fill(0, count($countryNames), '?'));

        // Get top 9 salons based on monthly bookings (salon leaderboard)
        // Excludes boosted businesses to avoid duplicates
        // Filtered by visitor's country
        $stmt = $this->db->query(
            "SELECT b.*, b.company_name as name,
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(DISTINCT r.id) as review_count,
                    MIN(s.price) as min_price,
                    (SELECT COUNT(*) FROM bookings bk
                     WHERE bk.business_id = b.id
                     AND bk.status NOT IN ('cancelled')
                     AND bk.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) as monthly_bookings
             FROM businesses b
             LEFT JOIN reviews r ON b.id = r.business_id
             LEFT JOIN services s ON b.id = s.business_id AND s.is_active = 1
             WHERE b.status = 'active'
               AND (b.is_boosted = 0 OR b.is_boosted IS NULL OR b.boost_expires_at <= NOW())
               AND b.country IN ({$placeholders})
             GROUP BY b.id
             ORDER BY monthly_bookings DESC, avg_rating DESC
             LIMIT 9",
            $countryNames
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getCategories(): array
    {
        $stmt = $this->db->query(
            "SELECT c.*, ct.name as translated_name,
                    (SELECT COUNT(DISTINCT bc.business_id)
                     FROM business_categories bc
                     JOIN businesses b ON bc.business_id = b.id
                     WHERE bc.category_id = c.id AND b.status = 'active') as business_count
             FROM categories c
             LEFT JOIN category_translations ct ON c.id = ct.category_id AND ct.language = ?
             WHERE c.is_active = 1
             ORDER BY c.sort_order",
            [$this->lang]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getPlatformStats(string $countryCode): array
    {
        $countryNames = $this->getCountryVariants($countryCode);
        $placeholders = implode(',', array_fill(0, count($countryNames), '?'));

        $stmt = $this->db->query(
            "SELECT COUNT(*) as cnt FROM businesses
             WHERE status = 'active' AND (UPPER(country) IN ({$placeholders}))",
            $countryNames
        );
        $businesses = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        $stmt = $this->db->query(
            "SELECT COUNT(*) as cnt FROM bookings bk
             JOIN businesses b ON bk.business_id = b.id
             WHERE bk.status != 'cancelled'
               AND (b.country IN ({$placeholders}))",
            $countryNames
        );
        $bookings = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        $stmt = $this->db->query(
            "SELECT COUNT(DISTINCT bk.user_id) as cnt FROM bookings bk
             JOIN businesses b ON bk.business_id = b.id
             WHERE bk.status != 'cancelled'
               AND bk.user_id IS NOT NULL
               AND (b.country IN ({$placeholders}))",
            $countryNames
        );
        $users = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        return [
            'businesses' => $businesses,
            'bookings' => $bookings,
            'users' => $users
        ];
    }

    /**
     * Global fallback methods - used when no businesses exist in visitor's country
     */
    private function getBoostedBusinessesGlobal(): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.company_name as name,
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(DISTINCT r.id) as review_count,
                    MIN(s.price) as min_price
             FROM businesses b
             LEFT JOIN reviews r ON b.id = r.business_id
             LEFT JOIN services s ON b.id = s.business_id AND s.is_active = 1
             WHERE b.status = 'active'
               AND b.is_boosted = 1
               AND b.boost_expires_at > NOW()
             GROUP BY b.id
             ORDER BY b.boost_expires_at DESC
             LIMIT 9"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getFeaturedBusinessesGlobal(): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.company_name as name,
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(DISTINCT r.id) as review_count,
                    MIN(s.price) as min_price,
                    (SELECT COUNT(*) FROM bookings bk
                     WHERE bk.business_id = b.id
                     AND bk.status NOT IN ('cancelled')
                     AND bk.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) as monthly_bookings
             FROM businesses b
             LEFT JOIN reviews r ON b.id = r.business_id
             LEFT JOIN services s ON b.id = s.business_id AND s.is_active = 1
             WHERE b.status = 'active'
               AND (b.is_boosted = 0 OR b.is_boosted IS NULL OR b.boost_expires_at <= NOW())
             GROUP BY b.id
             ORDER BY monthly_bookings DESC, avg_rating DESC
             LIMIT 9"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getPlatformStatsGlobal(): array
    {
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM businesses WHERE status = 'active'");
        $businesses = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM bookings WHERE status != 'cancelled'");
        $bookings = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        $stmt = $this->db->query("SELECT COUNT(DISTINCT user_id) as cnt FROM bookings WHERE status != 'cancelled' AND user_id IS NOT NULL");
        $users = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        return [
            'businesses' => $businesses,
            'bookings' => $bookings,
            'users' => $users
        ];
    }

    /**
     * Get all variants for a country code (code + common names)
     * Handles mixed data where country column may store 'NL', 'Nederland', 'Netherlands', etc.
     */
    private function getCountryVariants(string $countryCode): array
    {
        $code = strtoupper($countryCode);
        $variants = [$code];

        $nameMap = [
            'NL' => ['Nederland', 'Netherlands', 'The Netherlands'],
            'BE' => ['België', 'Belgium', 'Belgique'],
            'DE' => ['Duitsland', 'Germany', 'Deutschland'],
            'FR' => ['Frankrijk', 'France'],
            'GB' => ['United Kingdom', 'UK', 'England', 'Groot-Brittannië'],
            'ES' => ['Spanje', 'Spain', 'España'],
            'IT' => ['Italië', 'Italy', 'Italia'],
            'PT' => ['Portugal'],
            'AT' => ['Oostenrijk', 'Austria', 'Österreich'],
            'CH' => ['Zwitserland', 'Switzerland', 'Schweiz', 'Suisse', 'Svizzera'],
            'LU' => ['Luxemburg', 'Luxembourg'],
            'PL' => ['Polen', 'Poland', 'Polska'],
            'CZ' => ['Tsjechië', 'Czech Republic', 'Česko'],
            'DK' => ['Denemarken', 'Denmark', 'Danmark'],
            'SE' => ['Zweden', 'Sweden', 'Sverige'],
            'NO' => ['Noorwegen', 'Norway', 'Norge'],
            'FI' => ['Finland', 'Suomi'],
            'IE' => ['Ierland', 'Ireland'],
            'GR' => ['Griekenland', 'Greece'],
            'TR' => ['Turkije', 'Turkey', 'Türkiye'],
            'US' => ['United States', 'USA', 'Verenigde Staten'],
            'CA' => ['Canada', 'Kanada'],
            'AU' => ['Australië', 'Australia'],
            'ZA' => ['Zuid-Afrika', 'South Africa'],
            'MA' => ['Marokko', 'Morocco'],
            'AE' => ['Verenigde Arabische Emiraten', 'UAE'],
        ];

        if (isset($nameMap[$code])) {
            $variants = array_merge($variants, $nameMap[$code]);
        }

        return $variants;
    }

    /**
     * Get salon count per country for the map legend
     */
    private function getCountryStats(): array
    {
        // Country code to flag and name mapping
        $countryInfo = [
            'NL' => ['flag' => '🇳🇱', 'name' => 'Nederland'],
            'BE' => ['flag' => '🇧🇪', 'name' => 'België'],
            'DE' => ['flag' => '🇩🇪', 'name' => 'Duitsland'],
            'FR' => ['flag' => '🇫🇷', 'name' => 'Frankrijk'],
            'GB' => ['flag' => '🇬🇧', 'name' => 'United Kingdom'],
            'ES' => ['flag' => '🇪🇸', 'name' => 'España'],
            'IT' => ['flag' => '🇮🇹', 'name' => 'Italia'],
            'PT' => ['flag' => '🇵🇹', 'name' => 'Portugal'],
            'AT' => ['flag' => '🇦🇹', 'name' => 'Österreich'],
            'CH' => ['flag' => '🇨🇭', 'name' => 'Schweiz'],
            'LU' => ['flag' => '🇱🇺', 'name' => 'Luxembourg'],
            'PL' => ['flag' => '🇵🇱', 'name' => 'Polska'],
            'CZ' => ['flag' => '🇨🇿', 'name' => 'Česko'],
            'DK' => ['flag' => '🇩🇰', 'name' => 'Danmark'],
            'SE' => ['flag' => '🇸🇪', 'name' => 'Sverige'],
            'NO' => ['flag' => '🇳🇴', 'name' => 'Norge'],
            'FI' => ['flag' => '🇫🇮', 'name' => 'Suomi'],
            'IE' => ['flag' => '🇮🇪', 'name' => 'Ireland'],
            'GR' => ['flag' => '🇬🇷', 'name' => 'Ελλάδα'],
            'TR' => ['flag' => '🇹🇷', 'name' => 'Türkiye'],
            'US' => ['flag' => '🇺🇸', 'name' => 'United States'],
            'CA' => ['flag' => '🇨🇦', 'name' => 'Canada'],
            'AU' => ['flag' => '🇦🇺', 'name' => 'Australia'],
            'ZA' => ['flag' => '🇿🇦', 'name' => 'South Africa'],
            'MA' => ['flag' => '🇲🇦', 'name' => 'المغرب'],
            'AE' => ['flag' => '🇦🇪', 'name' => 'الإمارات'],
        ];

        // Also map common Dutch names to codes
        $nameToCode = [
            'Nederland' => 'NL', 'Netherlands' => 'NL',
            'België' => 'BE', 'Belgium' => 'BE', 'Belgique' => 'BE',
            'Duitsland' => 'DE', 'Germany' => 'DE', 'Deutschland' => 'DE',
            'Frankrijk' => 'FR', 'France' => 'FR',
            'United Kingdom' => 'GB', 'UK' => 'GB', 'England' => 'GB',
            'Spanje' => 'ES', 'Spain' => 'ES', 'España' => 'ES',
            'Italië' => 'IT', 'Italy' => 'IT', 'Italia' => 'IT',
            'Portugal' => 'PT',
            'Oostenrijk' => 'AT', 'Austria' => 'AT', 'Österreich' => 'AT',
            'Zwitserland' => 'CH', 'Switzerland' => 'CH', 'Schweiz' => 'CH', 'Suisse' => 'CH',
            'Luxemburg' => 'LU', 'Luxembourg' => 'LU',
            'Polen' => 'PL', 'Poland' => 'PL', 'Polska' => 'PL',
            'Tsjechië' => 'CZ', 'Czech Republic' => 'CZ', 'Česko' => 'CZ',
            'Denemarken' => 'DK', 'Denmark' => 'DK', 'Danmark' => 'DK',
            'Zweden' => 'SE', 'Sweden' => 'SE', 'Sverige' => 'SE',
            'Noorwegen' => 'NO', 'Norway' => 'NO', 'Norge' => 'NO',
            'Finland' => 'FI', 'Suomi' => 'FI',
            'Ierland' => 'IE', 'Ireland' => 'IE',
            'Griekenland' => 'GR', 'Greece' => 'GR', 'Ελλάδα' => 'GR',
            'Turkije' => 'TR', 'Turkey' => 'TR', 'Türkiye' => 'TR',
            'Verenigde Staten' => 'US', 'United States' => 'US', 'USA' => 'US',
            'Canada' => 'CA', 'Kanada' => 'CA',
            'Australië' => 'AU', 'Australia' => 'AU',
            'Zuid-Afrika' => 'ZA', 'South Africa' => 'ZA',
            'Marokko' => 'MA', 'Morocco' => 'MA', 'المغرب' => 'MA',
            'Verenigde Arabische Emiraten' => 'AE', 'UAE' => 'AE', 'الإمارات' => 'AE',
        ];

        $stmt = $this->db->query(
            "SELECT country, COUNT(*) as salon_count
             FROM businesses
             WHERE status = 'active'
               AND country IS NOT NULL
               AND country != ''
             GROUP BY country
             ORDER BY salon_count DESC"
        );
        $rawStats = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Normalize and aggregate counts
        $aggregated = [];
        foreach ($rawStats as $row) {
            $country = trim($row['country']);
            $count = (int)$row['salon_count'];

            // Try to find the country code
            $code = strtoupper($country);
            if (strlen($code) > 2 || !isset($countryInfo[$code])) {
                // Look up by name
                $code = $nameToCode[$country] ?? null;
            }

            if ($code && isset($countryInfo[$code])) {
                if (!isset($aggregated[$code])) {
                    $aggregated[$code] = [
                        'code' => $code,
                        'flag' => $countryInfo[$code]['flag'],
                        'name' => $countryInfo[$code]['name'],
                        'count' => 0
                    ];
                }
                $aggregated[$code]['count'] += $count;
            } else {
                // Unknown country - add with generic flag
                $key = 'OTHER_' . $country;
                if (!isset($aggregated[$key])) {
                    $aggregated[$key] = [
                        'code' => '',
                        'flag' => '🌍',
                        'name' => $country,
                        'count' => 0
                    ];
                }
                $aggregated[$key]['count'] += $count;
            }
        }

        // Sort by count descending
        usort($aggregated, fn($a, $b) => $b['count'] - $a['count']);

        return $aggregated;
    }
}

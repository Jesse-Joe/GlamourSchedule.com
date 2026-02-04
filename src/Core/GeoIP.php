<?php
namespace GlamourSchedule\Core;

/**
 * IP Geolocation Service
 * Detects user country and language based on IP address
 */
class GeoIP
{
    private Database $db;
    private array $cache = [];

    // Country to language mapping
    private array $countryLanguages = [
        // Dutch
        'NL' => 'nl',
        'BE' => 'nl', // Belgium (could also be French)
        'SR' => 'nl', // Suriname

        // German
        'DE' => 'de',
        'AT' => 'de', // Austria
        'CH' => 'de', // Switzerland
        'LI' => 'de', // Liechtenstein

        // French
        'FR' => 'fr',
        'LU' => 'fr', // Luxembourg
        'MC' => 'fr', // Monaco

        // Spanish
        'ES' => 'es',
        'MX' => 'es',
        'AR' => 'es',
        'CO' => 'es',
        'CL' => 'es',
        'PE' => 'es',
        'VE' => 'es',
        'EC' => 'es',
        'GT' => 'es',
        'CU' => 'es',
        'DO' => 'es',
        'HN' => 'es',
        'SV' => 'es',
        'NI' => 'es',
        'CR' => 'es',
        'PA' => 'es',
        'UY' => 'es',
        'PY' => 'es',
        'BO' => 'es',

        // Portuguese
        'PT' => 'pt',
        'BR' => 'pt',
        'AO' => 'pt', // Angola
        'MZ' => 'pt', // Mozambique

        // Italian
        'IT' => 'it',
        'SM' => 'it', // San Marino
        'VA' => 'it', // Vatican

        // Polish
        'PL' => 'pl',

        // Russian
        'RU' => 'ru',
        'BY' => 'ru', // Belarus
        'KZ' => 'ru', // Kazakhstan

        // Ukrainian
        'UA' => 'uk',

        // Turkish
        'TR' => 'tr',
        'CY' => 'tr', // Cyprus (could be Greek)

        // Greek
        'GR' => 'el',

        // Swedish
        'SE' => 'sv',

        // Norwegian
        'NO' => 'no',

        // Danish
        'DK' => 'da',

        // Finnish
        'FI' => 'fi',

        // Czech
        'CZ' => 'cs',

        // Hungarian
        'HU' => 'hu',

        // Romanian
        'RO' => 'ro',
        'MD' => 'ro', // Moldova

        // Bulgarian
        'BG' => 'bg',

        // Croatian
        'HR' => 'hr',

        // Serbian
        'RS' => 'sr',

        // Slovak
        'SK' => 'sk',

        // Slovenian
        'SI' => 'sl',

        // Arabic
        'SA' => 'ar',
        'AE' => 'ar',
        'EG' => 'ar',
        'MA' => 'ar', // Morocco
        'DZ' => 'ar', // Algeria
        'TN' => 'ar', // Tunisia
        'JO' => 'ar', // Jordan
        'LB' => 'ar', // Lebanon
        'IQ' => 'ar',
        'KW' => 'ar',
        'QA' => 'ar',
        'BH' => 'ar',
        'OM' => 'ar',

        // Hebrew
        'IL' => 'he',

        // Hindi
        'IN' => 'hi',

        // Thai
        'TH' => 'th',

        // Vietnamese
        'VN' => 'vi',

        // Indonesian
        'ID' => 'id',

        // Malay
        'MY' => 'ms',

        // Japanese
        'JP' => 'ja',

        // Korean
        'KR' => 'ko',

        // Chinese (Simplified)
        'CN' => 'zh',
        'SG' => 'zh', // Singapore

        // Chinese (Traditional)
        'TW' => 'zh-TW',
        'HK' => 'zh-TW',

        // English
        'GB' => 'en',
        'US' => 'en',
        'CA' => 'en',
        'AU' => 'en',
        'NZ' => 'en',
        'IE' => 'en',
        'ZA' => 'en', // South Africa
        'PH' => 'en', // Philippines
        'NG' => 'en', // Nigeria
        'KE' => 'en', // Kenya
        'GH' => 'en', // Ghana
    ];

    // Country to timezone mapping (default timezone per country)
    private array $countryTimezones = [
        'NL' => 'Europe/Amsterdam',
        'BE' => 'Europe/Brussels',
        'DE' => 'Europe/Berlin',
        'AT' => 'Europe/Vienna',
        'CH' => 'Europe/Zurich',
        'FR' => 'Europe/Paris',
        'LU' => 'Europe/Luxembourg',
        'MC' => 'Europe/Monaco',
        'GB' => 'Europe/London',
        'IE' => 'Europe/Dublin',
        'US' => 'America/New_York',
        'CA' => 'America/Toronto',
        'AU' => 'Australia/Sydney',
        'NZ' => 'Pacific/Auckland',
        'ES' => 'Europe/Madrid',
        'IT' => 'Europe/Rome',
        'PT' => 'Europe/Lisbon',
        'PL' => 'Europe/Warsaw',
        'CZ' => 'Europe/Prague',
        'SE' => 'Europe/Stockholm',
        'NO' => 'Europe/Oslo',
        'DK' => 'Europe/Copenhagen',
        'FI' => 'Europe/Helsinki',
        'GR' => 'Europe/Athens',
        'TR' => 'Europe/Istanbul',
        'RU' => 'Europe/Moscow',
        'JP' => 'Asia/Tokyo',
        'CN' => 'Asia/Shanghai',
        'KR' => 'Asia/Seoul',
        'IN' => 'Asia/Kolkata',
        'SG' => 'Asia/Singapore',
        'HK' => 'Asia/Hong_Kong',
        'AE' => 'Asia/Dubai',
        'SA' => 'Asia/Riyadh',
        'IL' => 'Asia/Jerusalem',
        'ZA' => 'Africa/Johannesburg',
        'EG' => 'Africa/Cairo',
        'BR' => 'America/Sao_Paulo',
        'MX' => 'America/Mexico_City',
        'AR' => 'America/Argentina/Buenos_Aires',
        'CL' => 'America/Santiago',
        'CO' => 'America/Bogota',
    ];

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get IP address from request
     */
    public function getClientIP(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Proxy
            'HTTP_X_REAL_IP',            // Nginx
            'HTTP_CLIENT_IP',            // Other proxies
            'REMOTE_ADDR'                // Direct connection
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Lookup IP location using free API
     */
    public function lookup(string $ip = null): array
    {
        $ip = $ip ?? $this->getClientIP();

        // Check cache first
        if (isset($this->cache[$ip])) {
            return $this->cache[$ip];
        }

        // Default response
        $default = [
            'ip' => $ip,
            'country_code' => 'NL',
            'country_name' => 'Nederland',
            'city' => '',
            'region' => '',
            'latitude' => 52.3676,
            'longitude' => 4.9041,
            'timezone' => 'Europe/Amsterdam',
            'isp' => '',
            'language' => 'nl',
            'success' => false
        ];

        // Skip lookup for local/private IPs
        if ($this->isPrivateIP($ip)) {
            $default['success'] = true;
            return $default;
        }

        try {
            // Use ip-api.com (free, no API key needed, 45 requests/minute)
            $url = "http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,lat,lon,timezone,isp";

            $context = stream_context_create([
                'http' => [
                    'timeout' => 3,
                    'ignore_errors' => true
                ]
            ]);

            $response = @file_get_contents($url, false, $context);

            if ($response) {
                $data = json_decode($response, true);

                if ($data && $data['status'] === 'success') {
                    $result = [
                        'ip' => $ip,
                        'country_code' => $data['countryCode'] ?? 'NL',
                        'country_name' => $data['country'] ?? 'Nederland',
                        'city' => $data['city'] ?? '',
                        'region' => $data['regionName'] ?? '',
                        'latitude' => $data['lat'] ?? 52.3676,
                        'longitude' => $data['lon'] ?? 4.9041,
                        'timezone' => $data['timezone'] ?? 'Europe/Amsterdam',
                        'isp' => $data['isp'] ?? '',
                        'language' => $this->getLanguageForCountry($data['countryCode'] ?? 'NL'),
                        'success' => true
                    ];

                    $this->cache[$ip] = $result;
                    return $result;
                }
            }
        } catch (\Exception $e) {
            error_log("GeoIP lookup failed for {$ip}: " . $e->getMessage());
        }

        return $default;
    }

    /**
     * Get recommended language for country
     */
    public function getLanguageForCountry(string $countryCode): string
    {
        return $this->countryLanguages[strtoupper($countryCode)] ?? 'en';
    }

    /**
     * Get timezone for country code
     */
    public function getTimezoneForCountry(string $countryCode): string
    {
        return $this->countryTimezones[strtoupper($countryCode)] ?? 'Europe/Amsterdam';
    }

    /**
     * Check if IP is private/local
     */
    private function isPrivateIP(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * Log IP location to database
     */
    public function logLocation(array $location, ?int $userId = null, ?int $businessId = null, string $page = ''): void
    {
        try {
            $this->db->query(
                "INSERT INTO ip_location_logs
                 (ip_address, country_code, country_name, city, region, latitude, longitude, timezone, isp, user_id, business_id, page_visited, user_agent)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $location['ip'],
                    $location['country_code'],
                    $location['country_name'],
                    $location['city'],
                    $location['region'],
                    $location['latitude'],
                    $location['longitude'],
                    $location['timezone'],
                    $location['isp'],
                    $userId,
                    $businessId,
                    $page,
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]
            );
        } catch (\Exception $e) {
            error_log("Failed to log IP location: " . $e->getMessage());
        }
    }

    /**
     * Get promotion price for country
     * Auto-creates country entry if not exists (100 early bird spots)
     */
    public function getPromotionPrice(string $countryCode): array
    {
        $countryCode = strtoupper($countryCode);

        $stmt = $this->db->query(
            "SELECT * FROM country_promotions WHERE country_code = ? AND is_active = 1",
            [$countryCode]
        );

        $promo = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Auto-create country if not exists
        if (!$promo) {
            $this->createCountryPromotion($countryCode);
            $stmt = $this->db->query(
                "SELECT * FROM country_promotions WHERE country_code = ?",
                [$countryCode]
            );
            $promo = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        // Get transaction fee (variable per country)
        $transactionFee = (float)($promo['transaction_fee'] ?? 1.75);
        $currencyCode = $promo['currency_code'] ?? 'EUR';

        if ($promo && $promo['current_registrations'] < $promo['max_promo_registrations']) {
            return [
                'price' => (float)$promo['promo_price'],
                'original_price' => (float)$promo['normal_price'],
                'is_promo' => true,
                'spots_left' => $promo['max_promo_registrations'] - $promo['current_registrations'],
                'early_bird_number' => $promo['current_registrations'] + 1,
                'country' => $promo['country_name'],
                'transaction_fee' => $transactionFee,
                'currency_code' => $currencyCode
            ];
        }

        // Promo exhausted - standard price €99.99
        return [
            'price' => 99.99,
            'original_price' => 99.99,
            'is_promo' => false,
            'spots_left' => 0,
            'early_bird_number' => null,
            'country' => $promo['country_name'] ?? $countryCode,
            'transaction_fee' => $transactionFee,
            'currency_code' => $currencyCode
        ];
    }

    /**
     * Get promotion price with local currency display
     * Shows price in local currency but charges EUR equivalent
     */
    public function getPromotionPriceWithCurrency(string $countryCode): array
    {
        $promo = $this->getPromotionPrice($countryCode);

        try {
            $currencyService = new \GlamourSchedule\Services\CurrencyService();
            $localPrice = $currencyService->getDisplayPrice($promo['price'], $countryCode);
            $localOriginal = $currencyService->getDisplayPrice($promo['original_price'], $countryCode);
            $localFee = $currencyService->getDisplayPrice($promo['transaction_fee'], $countryCode);

            $promo['local_price'] = $localPrice['local_formatted'];
            $promo['local_original'] = $localOriginal['local_formatted'];
            $promo['local_currency'] = $localPrice['local_currency'];
            $promo['local_symbol'] = $localPrice['local_symbol'];
            $promo['eur_price'] = $localPrice['eur_formatted'];
            $promo['eur_original'] = $localOriginal['eur_formatted'];
            $promo['show_dual'] = ($localPrice['local_currency'] !== 'EUR');

            // Transaction fee in local currency
            $promo['local_fee'] = $localFee['local_formatted'];
            $promo['eur_fee'] = $localFee['eur_formatted'];
            $promo['fee_display'] = $promo['show_dual']
                ? $localFee['local_formatted'] . ' (' . $localFee['eur_formatted'] . ')'
                : $localFee['eur_formatted'];

            // Boost price based on country tier (same tiers as transaction fees)
            $boostPriceEur = $this->getBoostPriceForCountry($countryCode);
            $localBoost = $currencyService->getDisplayPrice($boostPriceEur, $countryCode);
            $promo['boost_price'] = $boostPriceEur;
            $promo['boost_price_local'] = $localBoost['local_formatted'];
            $promo['boost_price_eur'] = $localBoost['eur_formatted'];
            $promo['boost_price_display'] = $promo['show_dual']
                ? $localBoost['local_formatted'] . ' (' . $localBoost['eur_formatted'] . ')'
                : $localBoost['eur_formatted'];
        } catch (\Exception $e) {
            // Fallback to EUR only
            $promo['local_price'] = '€' . number_format($promo['price'], 2, ',', '.');
            $promo['local_original'] = '€' . number_format($promo['original_price'], 2, ',', '.');
            $promo['local_currency'] = 'EUR';
            $promo['local_symbol'] = '€';
            $promo['eur_price'] = $promo['local_price'];
            $promo['eur_original'] = $promo['local_original'];
            $promo['show_dual'] = false;
            $promo['local_fee'] = '€' . number_format($promo['transaction_fee'], 2, ',', '.');
            $promo['eur_fee'] = $promo['local_fee'];
            $promo['fee_display'] = $promo['local_fee'];

            // Boost price fallback
            $boostPriceEur = $this->getBoostPriceForCountry($countryCode);
            $promo['boost_price'] = $boostPriceEur;
            $promo['boost_price_local'] = '€' . number_format($boostPriceEur, 2, ',', '.');
            $promo['boost_price_eur'] = $promo['boost_price_local'];
            $promo['boost_price_display'] = $promo['boost_price_local'];
        }

        return $promo;
    }

    /**
     * Get boost price for a country based on economic tier
     * Prices adjusted to be affordable in each region
     */
    private function getBoostPriceForCountry(string $countryCode): float
    {
        // Premium tier - wealthy small countries
        $premium = ['CH', 'NO', 'LI', 'MC', 'LU', 'IS'];
        if (in_array($countryCode, $premium)) return 399.99;

        // High-income tier
        $highIncome = ['US', 'CA', 'AU', 'SG', 'AE', 'QA', 'KW', 'BH', 'SE', 'DK'];
        if (in_array($countryCode, $highIncome)) return 349.99;

        // Standard tier - Western Europe
        $standard = ['NL', 'DE', 'FR', 'GB', 'BE', 'AT', 'IE', 'FI', 'IT', 'ES', 'PT', 'NZ', 'JP'];
        if (in_array($countryCode, $standard)) return 299.99;

        // Mid-tier - Eastern Europe, Asia developed
        $midTier = ['PL', 'CZ', 'HK', 'KR', 'IL', 'SA', 'EE', 'LV', 'LT', 'SK', 'SI', 'HR', 'GR', 'CY', 'MT'];
        if (in_array($countryCode, $midTier)) return 149.99;

        // Budget tier - Emerging markets
        $budget = ['HU', 'RO', 'BG', 'BR', 'MX', 'TR', 'ZA', 'MY', 'TH', 'CN', 'AR', 'CL', 'CO', 'PE'];
        if (in_array($countryCode, $budget)) return 79.99;

        // Low-cost tier - Developing countries
        $lowCost = ['IN', 'ID', 'PH', 'VN', 'PK', 'BD', 'NG', 'KE', 'EG', 'UA'];
        if (in_array($countryCode, $lowCost)) return 29.99;

        // Default - standard pricing
        return 299.99;
    }

    /**
     * Create country promotion entry (100 early bird spots)
     * Early bird: €0.99, Standard: €99.99
     */
    private function createCountryPromotion(string $countryCode): void
    {
        try {
            $this->db->query(
                "INSERT IGNORE INTO country_promotions
                 (country_code, country_name, promo_price, normal_price, max_promo_registrations, current_registrations, is_active)
                 VALUES (?, ?, 0.99, 99.99, 100, 0, 1)",
                [$countryCode, $countryCode]
            );
        } catch (\Exception $e) {
            error_log("Failed to create country promotion: " . $e->getMessage());
        }
    }

    /**
     * Increment registration count for country
     */
    public function incrementRegistrationCount(string $countryCode): bool
    {
        try {
            $this->db->query(
                "UPDATE country_promotions
                 SET current_registrations = current_registrations + 1
                 WHERE country_code = ? AND current_registrations < max_promo_registrations",
                [strtoupper($countryCode)]
            );
            return true;
        } catch (\Exception $e) {
            error_log("Failed to increment registration count: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all country promotions status
     */
    public function getAllPromotions(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM country_promotions WHERE is_active = 1 ORDER BY country_name"
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

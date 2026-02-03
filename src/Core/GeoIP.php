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
        'NL' => 'nl',
        'BE' => 'nl', // Belgium defaults to Dutch, can be French
        'DE' => 'de',
        'AT' => 'de', // Austria
        'CH' => 'de', // Switzerland (could be French/Italian too)
        'FR' => 'fr',
        'LU' => 'fr', // Luxembourg
        'MC' => 'fr', // Monaco
        'GB' => 'en',
        'US' => 'en',
        'CA' => 'en',
        'AU' => 'en',
        'NZ' => 'en',
        'IE' => 'en',
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

        if ($promo && $promo['current_registrations'] < $promo['max_promo_registrations']) {
            return [
                'price' => (float)$promo['promo_price'],
                'original_price' => (float)$promo['normal_price'],
                'is_promo' => true,
                'spots_left' => $promo['max_promo_registrations'] - $promo['current_registrations'],
                'early_bird_number' => $promo['current_registrations'] + 1,
                'country' => $promo['country_name']
            ];
        }

        // Promo exhausted - standard price €99.99
        return [
            'price' => 99.99,
            'original_price' => 99.99,
            'is_promo' => false,
            'spots_left' => 0,
            'early_bird_number' => null,
            'country' => $promo['country_name'] ?? $countryCode
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

            $promo['local_price'] = $localPrice['local_formatted'];
            $promo['local_original'] = $localOriginal['local_formatted'];
            $promo['local_currency'] = $localPrice['local_currency'];
            $promo['local_symbol'] = $localPrice['local_symbol'];
            $promo['eur_price'] = $localPrice['eur_formatted'];
            $promo['eur_original'] = $localOriginal['eur_formatted'];
            $promo['show_dual'] = ($localPrice['local_currency'] !== 'EUR');
        } catch (\Exception $e) {
            // Fallback to EUR only
            $promo['local_price'] = '€' . number_format($promo['price'], 2, ',', '.');
            $promo['local_original'] = '€' . number_format($promo['original_price'], 2, ',', '.');
            $promo['local_currency'] = 'EUR';
            $promo['local_symbol'] = '€';
            $promo['eur_price'] = $promo['local_price'];
            $promo['eur_original'] = $promo['local_original'];
            $promo['show_dual'] = false;
        }

        return $promo;
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

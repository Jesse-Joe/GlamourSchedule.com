<?php
namespace GlamourSchedule\Services;

use GlamourSchedule\Core\Database;

class TrackingService
{
    private Database $db;
    private string $visitorId;
    private array $config;

    public function __construct(Database $db, array $config = [])
    {
        $this->db = $db;
        $this->config = $config;
        $this->visitorId = $this->getOrCreateVisitorId();
    }

    public function getVisitorId(): string
    {
        return $this->visitorId;
    }

    private function getOrCreateVisitorId(): string
    {
        if (isset($_COOKIE['gs_visitor_id'])) {
            return $_COOKIE['gs_visitor_id'];
        }

        $visitorId = bin2hex(random_bytes(32));
        setcookie('gs_visitor_id', $visitorId, [
            'expires' => time() + (365 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        return $visitorId;
    }

    public function trackVisit(): void
    {
        $ip = $this->getClientIp();
        $geoData = $this->getGeoData($ip);

        $stmt = $this->db->query(
            "SELECT id, page_views FROM visitor_tracking WHERE visitor_id = ?",
            [$this->visitorId]
        );
        $visitor = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($visitor) {
            $this->db->query(
                "UPDATE visitor_tracking SET
                    page_views = page_views + 1,
                    last_page = ?,
                    last_visit = NOW(),
                    ip_address = COALESCE(?, ip_address),
                    country_code = COALESCE(?, country_code),
                    city = COALESCE(?, city)
                 WHERE visitor_id = ?",
                [
                    $_SERVER['REQUEST_URI'] ?? '/',
                    $ip,
                    $geoData['country'] ?? null,
                    $geoData['city'] ?? null,
                    $this->visitorId
                ]
            );
        } else {
            $this->db->query(
                "INSERT INTO visitor_tracking
                    (visitor_id, ip_address, country_code, city, user_agent, language, page_views, last_page, referrer)
                 VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?)",
                [
                    $this->visitorId,
                    $ip,
                    $geoData['country'] ?? null,
                    $geoData['city'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null,
                    $_SESSION['lang'] ?? 'nl',
                    $_SERVER['REQUEST_URI'] ?? '/',
                    $_SERVER['HTTP_REFERER'] ?? null
                ]
            );
        }
    }

    public function trackPageView(string $url, ?int $categoryId = null, ?int $businessId = null): void
    {
        if (!$this->hasConsent('analytics')) {
            return;
        }

        $this->db->query(
            "INSERT INTO page_views (visitor_id, page_url, category_id, business_id)
             VALUES (?, ?, ?, ?)",
            [$this->visitorId, $url, $categoryId, $businessId]
        );

        // Update viewed categories/businesses
        if ($categoryId) {
            $this->addToJsonArray('viewed_categories', $categoryId);
        }
        if ($businessId) {
            $this->addToJsonArray('viewed_businesses', $businessId);
        }
    }

    public function trackSearch(string $query): void
    {
        if (!$this->hasConsent('analytics')) {
            return;
        }

        $this->addToJsonArray('search_queries', $query);
    }

    private function addToJsonArray(string $column, $value): void
    {
        $stmt = $this->db->query(
            "SELECT {$column} FROM visitor_tracking WHERE visitor_id = ?",
            [$this->visitorId]
        );
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $array = json_decode($row[$column] ?? '[]', true) ?: [];

        if (!in_array($value, $array)) {
            $array[] = $value;
            // Keep only last 50 items
            $array = array_slice($array, -50);
        }

        $this->db->query(
            "UPDATE visitor_tracking SET {$column} = ? WHERE visitor_id = ?",
            [json_encode($array), $this->visitorId]
        );
    }

    public function saveConsent(array $preferences): void
    {
        $this->db->query(
            "UPDATE visitor_tracking SET
                cookies_accepted = 1,
                cookies_analytics = ?,
                cookies_marketing = ?,
                cookies_personalization = ?,
                consent_date = NOW()
             WHERE visitor_id = ?",
            [
                $preferences['analytics'] ? 1 : 0,
                $preferences['marketing'] ? 1 : 0,
                $preferences['personalization'] ? 1 : 0,
                $this->visitorId
            ]
        );

        // Set cookie with consent preferences
        setcookie('gs_consent', json_encode($preferences), [
            'expires' => time() + (365 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Lax'
        ]);
    }

    public function hasConsent(string $type = 'essential'): bool
    {
        if ($type === 'essential') {
            return true;
        }

        if (isset($_COOKIE['gs_consent'])) {
            $consent = json_decode($_COOKIE['gs_consent'], true);
            return $consent[$type] ?? false;
        }

        return false;
    }

    public function getConsentStatus(): ?array
    {
        if (isset($_COOKIE['gs_consent'])) {
            return json_decode($_COOKIE['gs_consent'], true);
        }
        return null;
    }

    public function getVisitorData(): ?array
    {
        $stmt = $this->db->query(
            "SELECT * FROM visitor_tracking WHERE visitor_id = ?",
            [$this->visitorId]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function getPersonalizedBusinesses(int $limit = 6): array
    {
        if (!$this->hasConsent('personalization')) {
            // Return featured businesses without personalization
            $stmt = $this->db->query(
                "SELECT b.*, b.company_name as name,
                        COALESCE(AVG(r.rating), 0) as avg_rating,
                        COUNT(r.id) as review_count
                 FROM businesses b
                 LEFT JOIN reviews r ON b.id = r.business_id
                 WHERE b.status = 'active'
                 GROUP BY b.id
                 ORDER BY b.featured DESC, avg_rating DESC
                 LIMIT ?",
                [$limit]
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        $visitor = $this->getVisitorData();
        $viewedCategories = json_decode($visitor['viewed_categories'] ?? '[]', true) ?: [];
        $viewedBusinesses = json_decode($visitor['viewed_businesses'] ?? '[]', true) ?: [];

        if (empty($viewedCategories) && empty($viewedBusinesses)) {
            // No browsing history, return featured
            $stmt = $this->db->query(
                "SELECT b.*, b.company_name as name,
                        COALESCE(AVG(r.rating), 0) as avg_rating,
                        COUNT(r.id) as review_count
                 FROM businesses b
                 LEFT JOIN reviews r ON b.id = r.business_id
                 WHERE b.status = 'active'
                 GROUP BY b.id
                 ORDER BY b.featured DESC, avg_rating DESC
                 LIMIT ?",
                [$limit]
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Personalized query based on viewed categories
        $categoryPlaceholders = implode(',', array_fill(0, count($viewedCategories), '?'));

        $sql = "SELECT b.*, b.company_name as name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(r.id) as review_count,
                       CASE WHEN bc.category_id IN ({$categoryPlaceholders}) THEN 1 ELSE 0 END as relevance
                FROM businesses b
                LEFT JOIN reviews r ON b.id = r.business_id
                LEFT JOIN business_categories bc ON b.id = bc.business_id
                WHERE b.status = 'active'
                GROUP BY b.id
                ORDER BY relevance DESC, b.featured DESC, avg_rating DESC
                LIMIT ?";

        $params = array_merge($viewedCategories, [$limit]);
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function markPwaPromptShown(): void
    {
        $this->db->query(
            "UPDATE visitor_tracking SET pwa_prompt_shown = 1 WHERE visitor_id = ?",
            [$this->visitorId]
        );
    }

    public function markPwaInstalled(): void
    {
        $this->db->query(
            "UPDATE visitor_tracking SET pwa_installed = 1 WHERE visitor_id = ?",
            [$this->visitorId]
        );
    }

    public function shouldShowPwaPrompt(): bool
    {
        $visitor = $this->getVisitorData();
        if (!$visitor) {
            return false;
        }

        // Don't show if already shown or installed
        if ($visitor['pwa_prompt_shown'] || $visitor['pwa_installed']) {
            return false;
        }

        // Show after at least 2 page views
        return $visitor['page_views'] >= 2;
    }

    private function getClientIp(): ?string
    {
        $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Take first IP if multiple
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return null;
    }

    private function getGeoData(?string $ip): array
    {
        if (!$ip || in_array($ip, ['127.0.0.1', '::1']) || strpos($ip, '192.168.') === 0) {
            return [];
        }

        // Check cache
        $cacheKey = 'geo_' . md5($ip);
        if (isset($_SESSION[$cacheKey])) {
            return $_SESSION[$cacheKey];
        }

        try {
            $context = stream_context_create(['http' => ['timeout' => 2]]);
            $response = @file_get_contents(
                "http://ip-api.com/json/{$ip}?fields=status,country,countryCode,city",
                false,
                $context
            );

            if ($response) {
                $data = json_decode($response, true);
                if ($data['status'] === 'success') {
                    $result = [
                        'country' => $data['countryCode'] ?? null,
                        'city' => $data['city'] ?? null
                    ];
                    $_SESSION[$cacheKey] = $result;
                    return $result;
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return [];
    }
}

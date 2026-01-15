<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\WebPush;

class ApiController extends Controller
{
    /**
     * Get translations for a language
     */
    public function translations(string $lang): string
    {
        $availableLangs = ['nl', 'en', 'de', 'fr'];
        if (!in_array($lang, $availableLangs)) {
            $lang = 'nl';
        }

        $langFile = BASE_PATH . '/resources/lang/' . $lang . '/messages.php';
        if (file_exists($langFile)) {
            $translations = require $langFile;
            return $this->json($translations);
        }

        return $this->json([]);
    }

    /**
     * Get services for a business
     */
    public function services(string $businessId): string
    {
        $stmt = $this->db->query(
            "SELECT s.*, c.slug as category_slug
             FROM services s
             LEFT JOIN categories c ON s.category_id = c.id
             WHERE s.business_id = ? AND s.is_active = 1
             ORDER BY s.sort_order, s.name",
            [$businessId]
        );
        return $this->json($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Get availability for a business
     */
    public function availability(string $businessId): string
    {
        $stmt = $this->db->query(
            "SELECT * FROM business_hours WHERE business_id = ? ORDER BY day_of_week",
            [$businessId]
        );
        return $this->json($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Get categories with real-time business counts
     */
    public function categories(): string
    {
        $stmt = $this->db->query(
            "SELECT c.id, c.slug, c.icon, ct.name as translated_name,
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

        return $this->json([
            'categories' => $stmt->fetchAll(\PDO::FETCH_ASSOC),
            'timestamp' => time()
        ]);
    }

    /**
     * Get platform stats for real-time updates
     */
    public function stats(): string
    {
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM businesses WHERE status = 'active'");
        $businesses = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM bookings WHERE status != 'cancelled'");
        $bookings = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM users");
        $users = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        return $this->json([
            'businesses' => (int)$businesses,
            'bookings' => (int)$bookings,
            'users' => (int)$users,
            'timestamp' => time()
        ]);
    }

    /**
     * Save cookie consent preferences
     */
    public function consent(): string
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $visitorId = $_COOKIE['gs_visitor_id'] ?? null;

        if (!$visitorId) {
            // Create visitor record
            $visitorId = bin2hex(random_bytes(32));
        }

        // Check if visitor exists
        $stmt = $this->db->query("SELECT id FROM visitor_tracking WHERE visitor_id = ?", [$visitorId]);
        if (!$stmt->fetch()) {
            // Create new visitor
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
            $this->db->query(
                "INSERT INTO visitor_tracking (visitor_id, ip_address, user_agent, language, page_views)
                 VALUES (?, ?, ?, ?, 1)",
                [$visitorId, $ip, $_SERVER['HTTP_USER_AGENT'] ?? null, $this->lang]
            );
        }

        $this->db->query(
            "UPDATE visitor_tracking SET
                cookies_accepted = 1,
                cookies_analytics = ?,
                cookies_marketing = ?,
                cookies_personalization = ?,
                consent_date = NOW()
             WHERE visitor_id = ?",
            [
                ($data['analytics'] ?? false) ? 1 : 0,
                ($data['marketing'] ?? false) ? 1 : 0,
                ($data['personalization'] ?? false) ? 1 : 0,
                $visitorId
            ]
        );

        return $this->json(['success' => true]);
    }

    /**
     * Mark PWA as installed
     */
    public function pwaInstalled(): string
    {
        $visitorId = $_COOKIE['gs_visitor_id'] ?? null;

        if ($visitorId) {
            $this->db->query(
                "UPDATE visitor_tracking SET pwa_installed = 1 WHERE visitor_id = ?",
                [$visitorId]
            );
        }

        return $this->json(['success' => true]);
    }

    /**
     * Track page view (for personalization)
     */
    public function trackPageView(): string
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $visitorId = $_COOKIE['gs_visitor_id'] ?? null;

        if (!$visitorId) {
            return $this->json(['success' => false]);
        }

        // Check if analytics consent is given
        $consent = json_decode($_COOKIE['gs_consent'] ?? '{}', true);
        if (empty($consent['analytics'])) {
            return $this->json(['success' => false, 'message' => 'No analytics consent']);
        }

        $this->db->query(
            "INSERT INTO page_views (visitor_id, page_url, category_id, business_id)
             VALUES (?, ?, ?, ?)",
            [
                $visitorId,
                $data['url'] ?? $_SERVER['REQUEST_URI'],
                $data['category_id'] ?? null,
                $data['business_id'] ?? null
            ]
        );

        // Update visitor interests
        if (!empty($data['category_id'])) {
            $this->updateViewedCategories($visitorId, (int)$data['category_id']);
        }
        if (!empty($data['business_id'])) {
            $this->updateViewedBusinesses($visitorId, (int)$data['business_id']);
        }

        return $this->json(['success' => true]);
    }

    private function updateViewedCategories(string $visitorId, int $categoryId): void
    {
        $stmt = $this->db->query("SELECT viewed_categories FROM visitor_tracking WHERE visitor_id = ?", [$visitorId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $categories = json_decode($row['viewed_categories'] ?? '[]', true) ?: [];
        if (!in_array($categoryId, $categories)) {
            $categories[] = $categoryId;
            $categories = array_slice($categories, -20); // Keep last 20
        }

        $this->db->query(
            "UPDATE visitor_tracking SET viewed_categories = ? WHERE visitor_id = ?",
            [json_encode($categories), $visitorId]
        );
    }

    private function updateViewedBusinesses(string $visitorId, int $businessId): void
    {
        $stmt = $this->db->query("SELECT viewed_businesses FROM visitor_tracking WHERE visitor_id = ?", [$visitorId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $businesses = json_decode($row['viewed_businesses'] ?? '[]', true) ?: [];
        if (!in_array($businessId, $businesses)) {
            $businesses[] = $businessId;
            $businesses = array_slice($businesses, -20); // Keep last 20
        }

        $this->db->query(
            "UPDATE visitor_tracking SET viewed_businesses = ? WHERE visitor_id = ?",
            [json_encode($businesses), $visitorId]
        );
    }

    /**
     * Get VAPID public key for push notifications
     */
    public function vapidKey(): string
    {
        $publicKey = getenv('VAPID_PUBLIC_KEY') ?: '';
        return $this->json(['publicKey' => $publicKey]);
    }

    /**
     * Subscribe to push notifications
     */
    public function pushSubscribe(): string
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['endpoint']) || empty($data['keys']['p256dh']) || empty($data['keys']['auth'])) {
            return $this->json(['success' => false, 'error' => 'Invalid subscription data'], 400);
        }

        $userId = $_SESSION['user_id'] ?? null;
        $endpoint = $data['endpoint'];
        $p256dh = $data['keys']['p256dh'];
        $auth = $data['keys']['auth'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Check if subscription already exists
        $stmt = $this->db->query(
            "SELECT id FROM push_subscriptions WHERE endpoint = ?",
            [$endpoint]
        );

        $isNewSubscription = false;
        if ($stmt->fetch()) {
            // Update existing subscription
            $this->db->query(
                "UPDATE push_subscriptions SET user_id = ?, p256dh_key = ?, auth_key = ?, user_agent = ?, updated_at = NOW() WHERE endpoint = ?",
                [$userId, $p256dh, $auth, $userAgent, $endpoint]
            );
        } else {
            // Create new subscription
            $this->db->query(
                "INSERT INTO push_subscriptions (user_id, endpoint, p256dh_key, auth_key, user_agent) VALUES (?, ?, ?, ?, ?)",
                [$userId, $endpoint, $p256dh, $auth, $userAgent]
            );
            $isNewSubscription = true;
        }

        // Send test notification for new subscriptions
        if ($isNewSubscription) {
            try {
                $webPush = new WebPush();
                $subscription = [
                    'endpoint' => $endpoint,
                    'p256dh_key' => $p256dh,
                    'auth_key' => $auth
                ];
                $payload = [
                    'title' => 'Perfect! 🎉',
                    'body' => 'Push meldingen werken succesvol! Je ontvangt nu herinneringen voor afspraken.',
                    'icon' => '/images/icon-192.png',
                    'tag' => 'test-notification',
                    'data' => ['url' => '/', 'type' => 'test']
                ];
                $webPush->send($subscription, $payload);
            } catch (\Exception $e) {
                // Silently fail test notification - subscription was still saved
            }
        }

        return $this->json(['success' => true]);
    }

    /**
     * Unsubscribe from push notifications
     */
    public function pushUnsubscribe(): string
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['endpoint'])) {
            return $this->json(['success' => false, 'error' => 'No endpoint provided'], 400);
        }

        $this->db->query("DELETE FROM push_subscriptions WHERE endpoint = ?", [$data['endpoint']]);

        return $this->json(['success' => true]);
    }

    /**
     * Save user theme preference
     */
    public function saveTheme(): string
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $this->json(['success' => false, 'error' => 'Not logged in'], 401);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $theme = $data['theme'] ?? 'light';

        // Validate theme value
        if (!in_array($theme, ['light', 'dark'])) {
            $theme = 'light';
        }

        $this->db->query(
            "UPDATE users SET theme_preference = ? WHERE id = ?",
            [$theme, $userId]
        );

        // Update session
        $_SESSION['theme_preference'] = $theme;

        return $this->json(['success' => true, 'theme' => $theme]);
    }

    /**
     * Get user theme preference
     */
    public function getTheme(): string
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $this->json(['theme' => 'light']);
        }

        $stmt = $this->db->query(
            "SELECT theme_preference FROM users WHERE id = ?",
            [$userId]
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->json(['theme' => $result['theme_preference'] ?? 'light']);
    }
}

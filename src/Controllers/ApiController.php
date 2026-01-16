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
     * Global search across platform
     */
    public function globalSearch(): string
    {
        $query = trim($_GET['q'] ?? '');

        if (strlen($query) < 2) {
            return $this->json(['salons' => [], 'services' => []]);
        }

        $searchTerm = '%' . $query . '%';

        // Search salons
        $stmt = $this->db->query(
            "SELECT id, company_name, slug, city, photos
             FROM businesses
             WHERE status = 'active'
               AND (company_name LIKE ? OR city LIKE ? OR description LIKE ?)
             ORDER BY
                CASE WHEN company_name LIKE ? THEN 1 ELSE 2 END,
                company_name
             LIMIT 5",
            [$searchTerm, $searchTerm, $searchTerm, $searchTerm]
        );
        $salons = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Search services
        $stmt = $this->db->query(
            "SELECT s.id, s.name, b.slug as business_slug, b.company_name as business_name
             FROM services s
             JOIN businesses b ON s.business_id = b.id
             WHERE s.is_active = 1 AND b.status = 'active'
               AND s.name LIKE ?
             ORDER BY s.name
             LIMIT 5",
            [$searchTerm]
        );
        $services = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->json([
            'salons' => $salons,
            'services' => $services
        ]);
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
     * Get category groups for search filtering
     */
    public function categoryGroups(): string
    {
        // Get unique category groups with salon counts
        $stmt = $this->db->query(
            "SELECT c.category_group as slug,
                    COUNT(DISTINCT c.id) as category_count,
                    (SELECT COUNT(DISTINCT bc.business_id)
                     FROM business_categories bc
                     JOIN businesses b ON bc.business_id = b.id
                     JOIN categories cat ON bc.category_id = cat.id
                     WHERE cat.category_group = c.category_group AND b.status = 'active') as salon_count
             FROM categories c
             WHERE c.is_active = 1 AND c.category_group IS NOT NULL AND c.category_group != ''
             GROUP BY c.category_group
             ORDER BY MIN(c.sort_order)"
        );

        $groups = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Add Dutch labels, icons, descriptions and images for groups
        $groupData = [
            'haar' => [
                'label' => 'Haar',
                'icon' => 'cut',
                'desc' => 'Kapper, Barber, Stylist',
                'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=400&h=300&fit=crop'
            ],
            'nagels' => [
                'label' => 'Nagels',
                'icon' => 'hand-sparkles',
                'desc' => 'Manicure, Pedicure, Gel',
                'image' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400&h=300&fit=crop'
            ],
            'huid' => [
                'label' => 'Skincare',
                'icon' => 'spa',
                'desc' => 'Facial, Skincare',
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=400&h=300&fit=crop'
            ],
            'lichaam' => [
                'label' => 'Lichaam',
                'icon' => 'hands',
                'desc' => 'Massage, Body',
                'image' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=400&h=300&fit=crop'
            ],
            'makeup' => [
                'label' => 'Make-up',
                'icon' => 'magic',
                'desc' => 'Visagie, Wimpers',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=300&fit=crop'
            ],
            'ontharing' => [
                'label' => 'Ontharing',
                'icon' => 'leaf',
                'desc' => 'Waxen, Laser',
                'image' => 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=400&h=300&fit=crop'
            ],
            'wellness' => [
                'label' => 'Wellness',
                'icon' => 'hot-tub',
                'desc' => 'Spa, Sauna',
                'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=400&h=300&fit=crop'
            ],
            'bruinen' => [
                'label' => 'Bruinen',
                'icon' => 'sun',
                'desc' => 'Zonnebank, Spray tan',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop'
            ],
            'medisch' => [
                'label' => 'Medisch',
                'icon' => 'stethoscope',
                'desc' => 'Botox, Fillers',
                'image' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&h=300&fit=crop'
            ],
            'tattoo' => [
                'label' => 'Tattoo',
                'icon' => 'pen-fancy',
                'desc' => 'Tattoo, Verwijdering',
                'image' => 'https://images.unsplash.com/photo-1611501275019-9b5cda994e8d?w=400&h=300&fit=crop'
            ],
            'alternatief' => [
                'label' => 'Alternatief',
                'icon' => 'om',
                'desc' => 'Yoga, Reiki',
                'image' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=400&h=300&fit=crop'
            ],
            'fitness' => [
                'label' => 'Fitness',
                'icon' => 'dumbbell',
                'desc' => 'Personal Training',
                'image' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=400&h=300&fit=crop'
            ]
        ];

        foreach ($groups as &$group) {
            $data = $groupData[$group['slug']] ?? null;
            $group['label'] = $data['label'] ?? ucfirst($group['slug']);
            $group['icon'] = $data['icon'] ?? 'tag';
            $group['desc'] = $data['desc'] ?? '';
            $group['image'] = $data['image'] ?? '';
            $group['salon_count'] = (int)$group['salon_count'];
        }

        return $this->json([
            'groups' => $groups,
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

    /**
     * Glamori Chat - Send message
     */
    public function glamoriChat(): string
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $message = trim($data['message'] ?? '');

        if (empty($message)) {
            return $this->json(['error' => 'Message is required'], 400);
        }

        $userId = $_SESSION['user_id'] ?? null;
        $businessId = $_SESSION['business_id'] ?? null;

        $glamori = new \GlamourSchedule\Core\Glamori($this->db, $this->lang, $userId, $businessId);
        $response = $glamori->chat($message);

        return $this->json($response);
    }

    /**
     * Glamori Chat - Get welcome message
     */
    public function glamoriWelcome(): string
    {
        $userId = $_SESSION['user_id'] ?? null;
        $businessId = $_SESSION['business_id'] ?? null;

        $glamori = new \GlamourSchedule\Core\Glamori($this->db, $this->lang, $userId, $businessId);
        $response = $glamori->getWelcomeMessage();

        return $this->json($response);
    }

    /**
     * Glamori Chat - Get history
     */
    public function glamoriHistory(): string
    {
        $userId = $_SESSION['user_id'] ?? null;
        $businessId = $_SESSION['business_id'] ?? null;

        $glamori = new \GlamourSchedule\Core\Glamori($this->db, $this->lang, $userId, $businessId);
        $history = $glamori->getHistory();

        return $this->json(['messages' => $history]);
    }
}

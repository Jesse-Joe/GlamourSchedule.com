<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

class BusinessController extends Controller
{
    /**
     * Show business page by UUID (primary method)
     */
    public function showByUuid(string $uuid): string
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.company_name as name, b.street as address,
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(DISTINCT r.id) as review_count
             FROM businesses b
             LEFT JOIN reviews r ON b.id = r.business_id
             WHERE b.uuid = ?
             GROUP BY b.id",
            [$uuid]
        );

        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => 'Niet gevonden']);
        }

        return $this->renderBusinessPage($business);
    }

    /**
     * Show business page by slug (legacy support)
     */
    public function show(string $slug): string
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.company_name as name, b.street as address,
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(DISTINCT r.id) as review_count
             FROM businesses b
             LEFT JOIN reviews r ON b.id = r.business_id
             WHERE b.slug = ?
             GROUP BY b.id",
            [$slug]
        );

        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            http_response_code(404);
            return $this->view('pages/errors/404', ['pageTitle' => 'Niet gevonden']);
        }

        return $this->renderBusinessPage($business);
    }

    /**
     * Render the business page with all data
     */
    private function renderBusinessPage(array $business): string
    {
        $services = $this->getServices($business['id']);
        $reviews = $this->getReviews($business['id']);
        $hours = $this->getBusinessHours($business['id']);
        $images = $this->getBusinessImages($business['id']);
        $settings = $this->getBusinessSettings($business['id']);
        $reviewStats = $this->getReviewStats($business['id']);

        // Generate business URL using UUID
        $businessUuid = $business['uuid'];
        $businessUrlHelper = function($path = '') use ($businessUuid) {
            $baseUrl = '/s/' . $businessUuid;
            return $path ? $baseUrl . '/' . ltrim($path, '/') : $baseUrl;
        };

        return $this->view('pages/business/show', [
            'pageTitle' => $business['name'],
            'business' => $business,
            'services' => $services,
            'reviews' => $reviews,
            'hours' => $hours,
            'images' => $images,
            'settings' => $settings,
            'reviewStats' => $reviewStats,
            'businessUrl' => $businessUrlHelper,
            'isSubdomain' => false
        ]);
    }

    private function getBusinessSettings(int $businessId): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM business_settings WHERE business_id = ?",
            [$businessId]
        );
        $settings = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $settings ?: [
            'primary_color' => '#000000',
            'secondary_color' => '#333333',
            'accent_color' => '#000000',
            'tagline' => '',
            'about_title' => '',
            'about_text' => '',
            'welcome_message' => '',
            'show_reviews' => 1,
            'show_prices' => 1,
            'show_duration' => 1,
            'gallery_style' => 'grid',
        ];
    }

    private function getReviewStats(int $businessId): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) as total,
                AVG(rating) as average,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
             FROM reviews WHERE business_id = ? AND is_visible = 1",
            [$businessId]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['total' => 0, 'average' => 0];
    }

    private function getServices(int $businessId): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM services WHERE business_id = ? AND is_active = 1 ORDER BY sort_order, name",
            [$businessId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getReviews(int $businessId): array
    {
        $stmt = $this->db->query(
            "SELECT r.*, u.first_name, u.last_name
             FROM reviews r
             LEFT JOIN users u ON r.user_id = u.id
             WHERE r.business_id = ? AND r.is_visible = 1
             ORDER BY r.created_at DESC
             LIMIT 10",
            [$businessId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getBusinessHours(int $businessId): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM business_hours WHERE business_id = ? ORDER BY day_of_week",
            [$businessId]
        );
        $hours = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $days = ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'];
        $formatted = [];
        foreach ($hours as $h) {
            $formatted[$h['day_of_week']] = [
                'day' => $days[$h['day_of_week']] ?? '',
                'open' => $h['open_time'],
                'close' => $h['close_time'],
                'closed' => $h['is_closed']
            ];
        }
        return $formatted;
    }

    private function getBusinessImages(int $businessId): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM business_images WHERE business_id = ? ORDER BY sort_order LIMIT 10",
            [$businessId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

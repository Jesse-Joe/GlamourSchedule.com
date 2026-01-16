<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        http_response_code(200);

        $boostedBusinesses = $this->getBoostedBusinesses();
        $featuredBusinesses = $this->getFeaturedBusinesses();
        $categories = $this->getCategories();
        $stats = $this->getPlatformStats();

        return $this->view('pages/home', [
            'pageTitle' => 'Home',
            'boostedBusinesses' => $boostedBusinesses,
            'featuredBusinesses' => $featuredBusinesses,
            'categories' => $categories,
            'stats' => $stats
        ]);
    }

    /**
     * Get boosted businesses (paid promotion)
     * Only show businesses with active boost (boost_expires_at > NOW())
     */
    private function getBoostedBusinesses(): array
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

    private function getFeaturedBusinesses(): array
    {
        // Get top 9 salons based on monthly bookings (salon leaderboard)
        // Excludes boosted businesses to avoid duplicates
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

    private function getPlatformStats(): array
    {
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM businesses");
        $businesses = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM bookings WHERE status != 'cancelled'");
        $bookings = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM users");
        $users = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        return [
            'businesses' => $businesses,
            'bookings' => $bookings,
            'users' => $users
        ];
    }
}

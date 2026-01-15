<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        http_response_code(200);

        $featuredBusinesses = $this->getFeaturedBusinesses();
        $categories = $this->getCategories();
        $stats = $this->getPlatformStats();

        return $this->view('pages/home', [
            'pageTitle' => 'Home',
            'featuredBusinesses' => $featuredBusinesses,
            'categories' => $categories,
            'stats' => $stats
        ]);
    }

    private function getFeaturedBusinesses(): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, b.company_name as name, COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(DISTINCT r.id) as review_count
             FROM businesses b
             LEFT JOIN reviews r ON b.id = r.business_id
             WHERE b.status = 'active'
             GROUP BY b.id
             ORDER BY avg_rating DESC, review_count DESC
             LIMIT 6"
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

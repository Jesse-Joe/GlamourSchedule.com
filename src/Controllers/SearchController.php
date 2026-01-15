<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

class SearchController extends Controller
{
    public function index(): string
    {
        $query = trim($_GET['q'] ?? '');
        $category = $_GET['category'] ?? '';
        $location = trim($_GET['location'] ?? '');
        $sort = $_GET['sort'] ?? 'rating';

        $businesses = [];
        $categories = $this->getCategories();

        if ($query || $category || $location) {
            $businesses = $this->searchBusinesses($query, $category, $location, $sort);
        } else {
            $businesses = $this->getFeaturedBusinesses($sort);
        }

        // Enrich businesses with additional data
        $businesses = $this->enrichBusinessData($businesses);

        return $this->view('pages/search/index', [
            'pageTitle' => 'Zoeken',
            'businesses' => $businesses,
            'categories' => $categories,
            'query' => $query,
            'category' => $category,
            'location' => $location,
            'sort' => $sort
        ]);
    }

    private function searchBusinesses(string $query, string $category, string $location, string $sort): array
    {
        $sql = "SELECT DISTINCT b.*, b.company_name as name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.id) as review_count
                FROM businesses b
                LEFT JOIN reviews r ON b.id = r.business_id";

        if ($category) {
            $sql .= " INNER JOIN business_categories bc ON b.id = bc.business_id";
        }

        $sql .= " WHERE b.status = 'active'";

        $params = [];

        if ($query) {
            $sql .= " AND (b.company_name LIKE ? OR b.description LIKE ?)";
            $searchTerm = "%$query%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($category) {
            $sql .= " AND bc.category_id = ?";
            $params[] = $category;
        }

        if ($location) {
            $sql .= " AND (b.city LIKE ? OR b.postal_code LIKE ?)";
            $locationTerm = "%$location%";
            $params[] = $locationTerm;
            $params[] = $locationTerm;
        }

        $sql .= " GROUP BY b.id";
        $sql .= $this->getOrderByClause($sort);
        $sql .= " LIMIT 50";

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getFeaturedBusinesses(string $sort): array
    {
        $sql = "SELECT b.*, b.company_name as name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.id) as review_count
                FROM businesses b
                LEFT JOIN reviews r ON b.id = r.business_id
                WHERE b.status = 'active'
                GROUP BY b.id";
        $sql .= $this->getOrderByClause($sort);
        $sql .= " LIMIT 24";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getOrderByClause(string $sort): string
    {
        switch ($sort) {
            case 'name':
                return " ORDER BY b.company_name ASC";
            case 'reviews':
                return " ORDER BY review_count DESC, avg_rating DESC";
            case 'rating':
            default:
                return " ORDER BY avg_rating DESC, review_count DESC";
        }
    }

    private function enrichBusinessData(array $businesses): array
    {
        if (empty($businesses)) {
            return [];
        }

        $businessIds = array_column($businesses, 'id');
        $placeholders = implode(',', array_fill(0, count($businessIds), '?'));

        // Get minimum prices
        $priceStmt = $this->db->query(
            "SELECT business_id, MIN(price) as min_price
             FROM services
             WHERE business_id IN ($placeholders) AND is_active = 1
             GROUP BY business_id",
            $businessIds
        );
        $prices = [];
        while ($row = $priceStmt->fetch(\PDO::FETCH_ASSOC)) {
            $prices[$row['business_id']] = $row['min_price'];
        }

        // Get services preview (first 4 service names)
        $servicesStmt = $this->db->query(
            "SELECT business_id, GROUP_CONCAT(name ORDER BY price ASC SEPARATOR ', ') as services
             FROM (
                 SELECT business_id, name, price,
                        ROW_NUMBER() OVER (PARTITION BY business_id ORDER BY price ASC) as rn
                 FROM services
                 WHERE business_id IN ($placeholders) AND is_active = 1
             ) sub
             WHERE rn <= 4
             GROUP BY business_id",
            $businessIds
        );
        $servicesMap = [];
        while ($row = $servicesStmt->fetch(\PDO::FETCH_ASSOC)) {
            $servicesMap[$row['business_id']] = $row['services'];
        }

        // Get primary category for each business
        $lang = $this->lang ?? 'nl';
        $categoryStmt = $this->db->query(
            "SELECT bc.business_id, COALESCE(ct.name, c.slug) as category_name
             FROM business_categories bc
             INNER JOIN categories c ON bc.category_id = c.id
             LEFT JOIN category_translations ct ON c.id = ct.category_id AND ct.language = ?
             WHERE bc.business_id IN ($placeholders)
             GROUP BY bc.business_id",
            array_merge([$lang], $businessIds)
        );
        $categoriesMap = [];
        while ($row = $categoryStmt->fetch(\PDO::FETCH_ASSOC)) {
            $categoriesMap[$row['business_id']] = $row['category_name'];
        }

        // Enrich each business
        foreach ($businesses as &$biz) {
            $biz['min_price'] = $prices[$biz['id']] ?? null;
            $biz['services_preview'] = $servicesMap[$biz['id']] ?? '';
            $biz['category_name'] = $categoriesMap[$biz['id']] ?? '';
        }

        return $businesses;
    }

    private function getCategories(): array
    {
        $stmt = $this->db->query(
            "SELECT c.*, ct.name as translated_name
             FROM categories c
             LEFT JOIN category_translations ct ON c.id = ct.category_id AND ct.language = ?
             WHERE c.is_active = 1
             ORDER BY c.sort_order",
            [$this->lang]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

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

        // New filter parameters
        $filters = [
            'price_min' => !empty($_GET['price_min']) ? (float)$_GET['price_min'] : null,
            'price_max' => !empty($_GET['price_max']) ? (float)$_GET['price_max'] : null,
            'high_rated' => isset($_GET['high_rated']) && $_GET['high_rated'] === '1',
            'open_now' => isset($_GET['open_now']) && $_GET['open_now'] === '1',
            'open_weekend' => isset($_GET['open_weekend']) && $_GET['open_weekend'] === '1',
            'open_evening' => isset($_GET['open_evening']) && $_GET['open_evening'] === '1',
        ];

        $businesses = [];
        $categories = $this->getCategories();

        $hasFilters = $query || $category || $location ||
                      $filters['price_min'] || $filters['price_max'] ||
                      $filters['high_rated'] || $filters['open_now'] ||
                      $filters['open_weekend'] || $filters['open_evening'];

        if ($hasFilters) {
            $businesses = $this->searchBusinesses($query, $category, $location, $sort, $filters);
        } else {
            $businesses = $this->getFeaturedBusinesses($sort, $filters);
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
            'sort' => $sort,
            'filters' => $filters
        ]);
    }

    private function searchBusinesses(string $query, string $category, string $location, string $sort, array $filters = []): array
    {
        $sql = "SELECT DISTINCT b.*, b.company_name as name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.id) as review_count,
                       MIN(s.price) as min_price,
                       MAX(s.price) as max_price
                FROM businesses b
                LEFT JOIN reviews r ON b.id = r.business_id
                LEFT JOIN services s ON b.id = s.business_id AND s.is_active = 1";

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

        // Opening hours filters
        if (!empty($filters['open_now'])) {
            $currentDay = (int)date('w'); // 0=Sunday, 6=Saturday
            $currentTime = date('H:i:s');
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.day_of_week = ?
                AND bh.is_closed = 0
                AND bh.open_time <= ?
                AND bh.close_time >= ?
            )";
            $params[] = $currentDay;
            $params[] = $currentTime;
            $params[] = $currentTime;
        }

        if (!empty($filters['open_weekend'])) {
            // Saturday (6) or Sunday (0)
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.day_of_week IN (0, 6)
                AND bh.is_closed = 0
            )";
        }

        if (!empty($filters['open_evening'])) {
            // Evening = closes at 18:00 or later
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.is_closed = 0
                AND bh.close_time >= '18:00:00'
            )";
        }

        $sql .= " GROUP BY b.id";

        // Price filters (applied after GROUP BY via HAVING)
        $havingClauses = [];
        if (!empty($filters['price_min'])) {
            $havingClauses[] = "min_price >= ?";
            $params[] = $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $havingClauses[] = "min_price <= ?";
            $params[] = $filters['price_max'];
        }

        // High rated filter (4+ stars)
        if (!empty($filters['high_rated'])) {
            $havingClauses[] = "avg_rating >= 4";
        }

        if (!empty($havingClauses)) {
            $sql .= " HAVING " . implode(" AND ", $havingClauses);
        }

        $sql .= $this->getOrderByClause($sort);
        $sql .= " LIMIT 50";

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getFeaturedBusinesses(string $sort, array $filters = []): array
    {
        $sql = "SELECT b.*, b.company_name as name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.id) as review_count,
                       MIN(s.price) as min_price,
                       MAX(s.price) as max_price
                FROM businesses b
                LEFT JOIN reviews r ON b.id = r.business_id
                LEFT JOIN services s ON b.id = s.business_id AND s.is_active = 1
                WHERE b.status = 'active'";

        $params = [];

        // Opening hours filters
        if (!empty($filters['open_now'])) {
            $currentDay = (int)date('w');
            $currentTime = date('H:i:s');
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.day_of_week = ?
                AND bh.is_closed = 0
                AND bh.open_time <= ?
                AND bh.close_time >= ?
            )";
            $params[] = $currentDay;
            $params[] = $currentTime;
            $params[] = $currentTime;
        }

        if (!empty($filters['open_weekend'])) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.day_of_week IN (0, 6)
                AND bh.is_closed = 0
            )";
        }

        if (!empty($filters['open_evening'])) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.is_closed = 0
                AND bh.close_time >= '18:00:00'
            )";
        }

        $sql .= " GROUP BY b.id";

        // Price and rating filters via HAVING
        $havingClauses = [];
        if (!empty($filters['price_min'])) {
            $havingClauses[] = "min_price >= ?";
            $params[] = $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $havingClauses[] = "min_price <= ?";
            $params[] = $filters['price_max'];
        }
        if (!empty($filters['high_rated'])) {
            $havingClauses[] = "avg_rating >= 4";
        }

        if (!empty($havingClauses)) {
            $sql .= " HAVING " . implode(" AND ", $havingClauses);
        }

        $sql .= $this->getOrderByClause($sort);
        $sql .= " LIMIT 24";

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getOrderByClause(string $sort): string
    {
        switch ($sort) {
            case 'name':
                return " ORDER BY b.company_name ASC";
            case 'reviews':
                return " ORDER BY review_count DESC, avg_rating DESC";
            case 'price_asc':
                return " ORDER BY min_price ASC, avg_rating DESC";
            case 'price_desc':
                return " ORDER BY min_price DESC, avg_rating DESC";
            case 'newest':
                return " ORDER BY b.created_at DESC, avg_rating DESC";
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

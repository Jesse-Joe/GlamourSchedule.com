<?php
/**
 * API Entry Point
 * Handles all /api/* routes
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load configuration
define('GLAMOUR_LOADED', true);
$config = require dirname(__DIR__) . '/config/config.php';

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'NewGlamourSchedule\\';
    $baseDir = dirname(__DIR__) . '/src/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use NewGlamourSchedule\Core\Database;

// Initialize database
try {
    $db = Database::getInstance($config['database']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get request path
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = preg_replace('#^/api\.php#', '', $path);
$path = preg_replace('#^/api#', '', $path);
$path = $path ?: '/';

// Simple routing
switch (true) {
    // Stats endpoint
    case $path === '/stats':
        $stats = [
            'businesses' => $db->fetchColumn("SELECT COUNT(*) FROM businesses WHERE status = 'active'") ?: 0,
            'bookings' => $db->fetchColumn("SELECT COUNT(*) FROM bookings") ?: 0,
            'users' => $db->fetchColumn("SELECT COUNT(*) FROM users") ?: 0,
        ];
        echo json_encode(['success' => true, 'data' => $stats]);
        break;

    // Categories endpoint
    case $path === '/categories':
        $categories = $db->fetchAll(
            "SELECT c.id, ct.name, c.slug, c.icon, COUNT(bc.business_id) as business_count
             FROM categories c
             LEFT JOIN category_translations ct ON c.id = ct.category_id AND ct.language = 'nl'
             LEFT JOIN business_categories bc ON c.id = bc.category_id
             WHERE c.is_active = 1
             GROUP BY c.id
             ORDER BY business_count DESC"
        ) ?: [];
        echo json_encode(['success' => true, 'data' => $categories]);
        break;

    // Businesses endpoint
    case $path === '/businesses':
        $limit = (int) ($_GET['limit'] ?? 12);
        $offset = (int) ($_GET['offset'] ?? 0);
        $category = $_GET['category'] ?? null;
        $location = $_GET['location'] ?? null;
        $search = $_GET['q'] ?? null;

        $sql = "SELECT b.id, b.company_name as name, b.slug, b.description, b.city,
                CONCAT(b.street, ' ', b.house_number) as address, b.cover_image, b.logo,
                (SELECT AVG(rating) FROM reviews WHERE business_id = b.id) as avg_rating,
                (SELECT COUNT(*) FROM reviews WHERE business_id = b.id) as review_count
                FROM businesses b
                WHERE b.status = 'active'";

        $params = [];

        if ($category) {
            $sql .= " AND b.id IN (SELECT business_id FROM business_categories bc
                      JOIN categories c ON bc.category_id = c.id WHERE c.slug = ?)";
            $params[] = $category;
        }

        if ($location) {
            $sql .= " AND (b.city LIKE ? OR b.street LIKE ?)";
            $params[] = "%{$location}%";
            $params[] = "%{$location}%";
        }

        if ($search) {
            $sql .= " AND (b.company_name LIKE ? OR b.description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY avg_rating DESC LIMIT {$limit} OFFSET {$offset}";

        $businesses = $db->fetchAll($sql, $params) ?: [];
        echo json_encode(['success' => true, 'data' => $businesses]);
        break;

    // Services endpoint
    case preg_match('#^/services/(\d+)$#', $path, $matches):
        $businessId = $matches[1];
        $services = $db->fetchAll(
            "SELECT id, name, description, duration, price FROM services
             WHERE business_id = ? AND status = 'active' ORDER BY sort_order",
            [$businessId]
        ) ?: [];
        echo json_encode(['success' => true, 'data' => $services]);
        break;

    // Default - 404
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Endpoint not found', 'path' => $path]);
}

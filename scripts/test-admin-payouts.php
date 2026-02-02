<?php
/**
 * Test Admin Payouts Page Data
 */

require dirname(__DIR__) . '/vendor/autoload.php';

// Load env
$lines = file(dirname(__DIR__) . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos($line, '#') === 0) continue;
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

require_once dirname(__DIR__) . '/src/Services/WiseService.php';

use GlamourSchedule\Services\WiseService;

echo "=== Admin Payouts Page Test ===\n\n";

// Test Wise Service
$wise = new WiseService();

echo "1. Wise Pending Transfers\n";
echo "   ------------------------\n";
$pending = $wise->getPendingTransfers();
echo "   Aantal: " . count($pending) . "\n";
if (!empty($pending)) {
    foreach ($pending as $t) {
        echo "   - #{$t['id']}: €{$t['sourceValue']} ({$t['status']})\n";
    }
} else {
    echo "   Geen pending transfers (OK)\n";
}

echo "\n2. Wise Balance\n";
echo "   ------------\n";
$balance = $wise->getEurBalance();
echo "   EUR: " . ($balance !== null ? "€" . number_format($balance, 2) : "N/A") . "\n";

// Test database connection for payouts
echo "\n3. Database Connectie\n";
echo "   ------------------\n";
try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        getenv('DB_HOST'),
        getenv('DB_NAME')
    );
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   Verbonden met database\n";

    // Check for pending business payouts
    $stmt = $pdo->query("
        SELECT COUNT(*) as cnt, COALESCE(SUM(service_price - 1.75), 0) as total
        FROM bookings
        WHERE status = 'checked_in'
        AND business_paid = 0
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Openstaande bedrijfsuitbetalingen: {$result['cnt']} boekingen, €" . number_format($result['total'], 2) . "\n";

    // Check for pending sales payouts
    $stmt = $pdo->query("
        SELECT COUNT(DISTINCT sales_user_id) as partners
        FROM businesses
        WHERE sales_user_id IS NOT NULL
        AND sales_paid = 0
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Openstaande sales partners: {$result['partners']}\n";

} catch (PDOException $e) {
    echo "   Database fout: " . $e->getMessage() . "\n";
}

echo "\n4. Fee Tabel Check\n";
echo "   ---------------\n";
$feeFile = dirname(__DIR__) . '/resources/views/pages/admin/payouts.php';
if (file_exists($feeFile)) {
    $content = file_get_contents($feeFile);
    if (strpos($content, 'Wise Transactiekosten per Land') !== false) {
        echo "   Fee tabel aanwezig in payouts.php\n";

        // Count countries
        preg_match_all('/<td><code>([A-Z]{2})<\/code><\/td>/', $content, $matches);
        echo "   Landen in tabel: " . count($matches[1]) . " (" . implode(', ', $matches[1]) . ")\n";
    } else {
        echo "   WAARSCHUWING: Fee tabel niet gevonden!\n";
    }
} else {
    echo "   FOUT: payouts.php niet gevonden!\n";
}

echo "\n=== Test Voltooid ===\n";

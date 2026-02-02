<?php
/**
 * Test Business Payout via Wise
 */

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

echo "=== Test Bedrijfsuitbetaling via Wise ===\n\n";

// Database connection
try {
    $pdo = new PDO(
        'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8mb4',
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database verbonden\n\n";
} catch (PDOException $e) {
    die("Database fout: " . $e->getMessage() . "\n");
}

// Find a business with IBAN
echo "Zoeken naar bedrijf met IBAN...\n";
$stmt = $pdo->query("
    SELECT b.id, b.company_name, b.iban, b.email, CONCAT(u.first_name, ' ', u.last_name) as owner_name
    FROM businesses b
    LEFT JOIN users u ON b.user_id = u.id
    WHERE b.iban IS NOT NULL AND b.iban != ''
    LIMIT 5
");
$businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($businesses)) {
    die("Geen bedrijven met IBAN gevonden!\n");
}

echo "\nBedrijven met IBAN:\n";
foreach ($businesses as $i => $biz) {
    echo ($i + 1) . ". {$biz['company_name']} - {$biz['iban']}\n";
    echo "   Email: {$biz['email']}\n";
    echo "   Eigenaar: {$biz['owner_name']}\n\n";
}

// Use the first business for testing
$testBusiness = $businesses[0];
echo "=== Test met: {$testBusiness['company_name']} ===\n";
echo "IBAN: {$testBusiness['iban']}\n\n";

// Initialize Wise
$wise = new WiseService();

echo "Wise saldo: €" . number_format($wise->getEurBalance() ?? 0, 2) . "\n\n";

// Test payment - €5.00 (hoger dan minimum)
$testAmount = 5.00;
$reference = "Test uitbetaling " . date('d-m-Y H:i');

echo "Test betaling aanmaken:\n";
echo "  Bedrag: €" . number_format($testAmount, 2) . "\n";
echo "  Naar: {$testBusiness['company_name']}\n";
echo "  IBAN: {$testBusiness['iban']}\n";
echo "  Referentie: $reference\n\n";

// Make the payment
$result = $wise->makePayment(
    $testBusiness['iban'],
    $testBusiness['company_name'],
    $testAmount,
    $reference
);

echo "=== Resultaat ===\n";
if ($result['success']) {
    echo "Status: SUCCESVOL\n";
    echo "Transfer ID: {$result['transfer_id']}\n";
    echo "Quote ID: {$result['quote_id']}\n";
    echo "Recipient ID: {$result['recipient_id']}\n";
    echo "Status: {$result['status']}\n";

    if ($result['status'] === 'incoming_payment_waiting') {
        echo "\n⚠️  Transfer wacht op funding in Wise dashboard\n";
        echo "   Open: https://wise.com/transactions\n";
    }
} else {
    echo "Status: MISLUKT\n";
    echo "Fout: {$result['error']}\n";
    if (isset($result['details'])) {
        echo "Details: " . json_encode($result['details'], JSON_PRETTY_PRINT) . "\n";
    }
}

echo "\n=== Test Voltooid ===\n";

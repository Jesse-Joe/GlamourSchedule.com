<?php
/**
 * Test IBAN Change Flow for JJT-Services
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

// Database connection
$pdo = new PDO(
    'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8mb4',
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD')
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Test IBAN Flow voor JJT-Services ===\n\n";

// Get business
$stmt = $pdo->query("SELECT * FROM businesses WHERE company_name = 'JJT-Services'");
$business = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$business) {
    die("Business niet gevonden!\n");
}

echo "Bedrijf: {$business['company_name']}\n";
echo "Email: {$business['email']}\n";
echo "Huidige IBAN: " . ($business['iban'] ?? 'Geen') . "\n";
echo "IBAN Verified: " . ($business['iban_verified'] ? 'Ja' : 'Nee') . "\n";
echo "Laatst gewijzigd: " . ($business['iban_changed_at'] ?? 'Nooit') . "\n\n";

// Check 30-day restriction
echo "=== 30-Dagen Check ===\n";
if (!empty($business['iban_changed_at'])) {
    $lastChange = strtotime($business['iban_changed_at']);
    $daysSinceChange = (time() - $lastChange) / 86400;

    if ($daysSinceChange < 30) {
        $daysRemaining = ceil(30 - $daysSinceChange);
        echo "GEBLOKKEERD: IBAN kan pas over $daysRemaining dag(en) worden gewijzigd.\n";
    } else {
        echo "OK: Meer dan 30 dagen sinds laatste wijziging.\n";
    }
} else {
    echo "OK: Geen eerdere IBAN wijziging geregistreerd.\n";
}

// Simulate 2FA code generation
echo "\n=== 2FA Simulatie ===\n";
$code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
echo "Gegenereerde 2FA code: $code\n";
echo "(Zou naar {$business['email']} worden gestuurd)\n";

// Simulate IBAN entry
echo "\n=== IBAN Invoer Simulatie ===\n";
$newIban = "NL10KNAB0417886977"; // Same IBAN for test
$accountHolder = "JJT-Services B.V.";

// Validate IBAN
function validateIban($iban) {
    $iban = strtoupper(preg_replace('/\s+/', '', $iban));
    if (strlen($iban) < 15 || strlen($iban) > 34) return false;
    if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban)) return false;
    return true;
}

if (validateIban($newIban)) {
    echo "IBAN validatie: OK\n";
} else {
    echo "IBAN validatie: MISLUKT\n";
}

echo "Nieuwe IBAN: $newIban\n";
echo "Rekeninghouder: $accountHolder\n";

// Simulate save
echo "\n=== Database Update Simulatie ===\n";
echo "Query: UPDATE businesses SET iban = '$newIban', account_holder = '$accountHolder', iban_changed_at = NOW() WHERE id = {$business['id']}\n";

// Actually update if --execute flag is passed
if (in_array('--execute', $argv ?? [])) {
    $stmt = $pdo->prepare("UPDATE businesses SET iban = ?, account_holder = ?, iban_changed_at = NOW() WHERE id = ?");
    $stmt->execute([$newIban, $accountHolder, $business['id']]);
    echo "\nDatabase GEÜPDATET!\n";

    // Verify
    $stmt = $pdo->query("SELECT iban, account_holder, iban_changed_at FROM businesses WHERE id = {$business['id']}");
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Nieuwe waarden:\n";
    echo "  IBAN: {$updated['iban']}\n";
    echo "  Rekeninghouder: {$updated['account_holder']}\n";
    echo "  Gewijzigd op: {$updated['iban_changed_at']}\n";
} else {
    echo "\n(Gebruik --execute om daadwerkelijk te updaten)\n";
}

echo "\n=== Test Voltooid ===\n";

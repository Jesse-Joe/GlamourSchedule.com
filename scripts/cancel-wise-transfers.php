<?php
/**
 * Cancel Wise Test Transfers
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

$apiKey = getenv('WISE_API_KEY');
$profileId = getenv('WISE_PROFILE_ID');

echo "=== Wise Test Transfers Annuleren ===\n\n";

// Get pending transfers
$ch = curl_init("https://api.wise.com/v1/transfers?profile=$profileId&status=incoming_payment_waiting");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $apiKey]
]);
$response = curl_exec($ch);
curl_close($ch);

$transfers = json_decode($response, true);

if (empty($transfers)) {
    echo "Geen pending transfers gevonden.\n";
    exit;
}

echo "Gevonden: " . count($transfers) . " pending transfer(s)\n\n";

foreach ($transfers as $t) {
    echo "Transfer #{$t['id']}: {$t['sourceValue']} {$t['sourceCurrency']}\n";
    echo "  Referentie: " . ($t['reference'] ?? '-') . "\n";

    // Cancel the transfer
    $ch = curl_init("https://api.wise.com/v1/transfers/{$t['id']}/cancel");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]
    ]);
    $cancelResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $result = json_decode($cancelResponse, true);
        echo "  Status: GEANNULEERD (nieuwe status: {$result['status']})\n";
    } else {
        echo "  FOUT bij annuleren (HTTP $httpCode): $cancelResponse\n";
    }
    echo "\n";
}

echo "=== Klaar ===\n";

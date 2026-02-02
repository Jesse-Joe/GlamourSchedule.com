<?php
/**
 * Check Wise Transfer Statuses
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

echo "=== Wise Transfers Overzicht ===\n\n";

// Get all recent transfers (any status)
$ch = curl_init("https://api.wise.com/v1/transfers?profile=$profileId&limit=10");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $apiKey]
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP: $httpCode\n\n";

$transfers = json_decode($response, true);

if (empty($transfers)) {
    echo "Geen transfers gevonden\n";
} else {
    echo "Gevonden: " . count($transfers) . " transfer(s)\n\n";

    foreach ($transfers as $t) {
        $statusEmojis = [
            'incoming_payment_waiting' => '⏳',
            'processing' => '🔄',
            'outgoing_payment_sent' => '✅',
            'funds_converted' => '💱',
            'cancelled' => '❌'
        ];
        $statusEmoji = $statusEmojis[$t['status']] ?? '❓';

        echo "$statusEmoji Transfer #{$t['id']}\n";
        echo "   Status: {$t['status']}\n";
        echo "   Bedrag: €{$t['sourceValue']} {$t['sourceCurrency']} → €{$t['targetValue']} {$t['targetCurrency']}\n";
        echo "   Referentie: " . ($t['reference'] ?? '-') . "\n";
        echo "   Aangemaakt: {$t['created']}\n";

        // Check if it has issues
        if (!empty($t['hasActiveIssues'])) {
            echo "   ⚠️  HEEFT ACTIEVE ISSUES\n";
        }

        echo "\n";
    }
}

echo "=== Bekijk in Wise ===\n";
echo "https://wise.com/transactions\n";

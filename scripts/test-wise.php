<?php
/**
 * Test Wise Integration
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load environment variables
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

use GlamourSchedule\Services\WiseService;

echo "=== Wise Integration Test ===\n\n";

$wise = new WiseService();

// Check configuration
echo "1. Configuration Check\n";
echo "   Configured: " . ($wise->isConfigured() ? 'YES' : 'NO') . "\n\n";

if (!$wise->isConfigured()) {
    echo "   WISE_API_KEY: " . (getenv('WISE_API_KEY') ? 'Set' : 'Not set') . "\n";
    echo "   WISE_PROFILE_ID: " . (getenv('WISE_PROFILE_ID') ? 'Set' : 'Not set') . "\n";
    echo "\n   Wise is not configured. Please add WISE_API_KEY and WISE_PROFILE_ID to .env\n";
    echo "   Get your API key at: https://wise.com/settings/api-tokens\n\n";
    exit(0);
}

// Check supported currencies
echo "2. Supported Currencies\n";
$currencies = $wise->getSupportedCurrencies();
echo "   " . implode(', ', $currencies) . "\n\n";

// Try to get balances
echo "3. Account Balances\n";
$balances = $wise->getBalances();
if (!empty($balances)) {
    foreach ($balances as $currency => $balance) {
        echo "   $currency: " . number_format($balance['amount'], 2) . "\n";
    }
} else {
    echo "   Could not retrieve balances\n";
}
echo "\n";

// Test quote creation (simulation only)
echo "4. Quote Creation Test (EUR to EUR)\n";
$quote = $wise->createQuote(10.00, 'EUR', 'EUR');
if ($quote) {
    echo "   Quote ID: {$quote['quote_id']}\n";
    echo "   Source Amount: €{$quote['source_amount']}\n";
    echo "   Target Amount: €{$quote['target_amount']}\n";
    echo "   Fee: €{$quote['fee']}\n";
} else {
    echo "   Could not create quote\n";
}
echo "\n";

echo "=== Test Complete ===\n";

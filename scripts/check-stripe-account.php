<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$lines = file(dirname(__DIR__) . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos($line, '#') === 0) continue;
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

$secretKey = getenv('STRIPE_SECRET_KEY');
\Stripe\Stripe::setApiKey($secretKey);

echo "=== Stripe Account Check ===\n\n";

// Check key type
$isLiveKey = strpos($secretKey, 'sk_live_') === 0;
$isTestKey = strpos($secretKey, 'sk_test_') === 0;

echo "1. API Key Type\n";
echo "   Key: " . substr($secretKey, 0, 12) . "...\n";
echo "   Mode: " . ($isLiveKey ? "LIVE" : ($isTestKey ? "TEST" : "ONBEKEND")) . "\n\n";

// Get account info
echo "2. Account Info\n";
try {
    $account = \Stripe\Account::retrieve();

    echo "   Account ID: {$account->id}\n";
    echo "   Business Name: " . ($account->business_profile->name ?? 'Niet ingesteld') . "\n";
    echo "   Country: {$account->country}\n";
    echo "   Default Currency: {$account->default_currency}\n";
    echo "   Email: {$account->email}\n";

    echo "\n3. Account Status\n";
    echo "   Charges Enabled: " . ($account->charges_enabled ? "JA" : "NEE") . "\n";
    echo "   Payouts Enabled: " . ($account->payouts_enabled ? "JA" : "NEE") . "\n";
    echo "   Details Submitted: " . ($account->details_submitted ? "JA" : "NEE") . "\n";

    if (!empty($account->requirements->currently_due)) {
        echo "\n4. ACTIE VEREIST - Ontbrekende informatie:\n";
        foreach ($account->requirements->currently_due as $req) {
            echo "   - $req\n";
        }
    } else {
        echo "\n4. Account Volledig: JA\n";
    }

    if (!empty($account->requirements->disabled_reason)) {
        echo "\n   WAARSCHUWING: Account uitgeschakeld!\n";
        echo "   Reden: {$account->requirements->disabled_reason}\n";
    }

    echo "\n5. Capabilities\n";
    if (isset($account->capabilities)) {
        foreach ($account->capabilities as $cap => $status) {
            $icon = $status === 'active' ? '✓' : ($status === 'pending' ? '⏳' : '✗');
            echo "   $icon $cap: $status\n";
        }
    }

} catch (\Stripe\Exception\AuthenticationException $e) {
    echo "   ERROR: API key ongeldig of geen toegang\n";
    echo "   " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Check Voltooid ===\n";

<?php
/**
 * Wise API Setup Script
 *
 * This script helps configure Wise for international payouts.
 * Run: php scripts/setup-wise.php YOUR_API_KEY
 */

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           WISE API SETUP - GlamourSchedule                   ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Check for API key argument
if (!isset($argv[1]) || empty($argv[1])) {
    echo "❌ Gebruik: php scripts/setup-wise.php JOUW_API_KEY\n\n";
    echo "Hoe krijg je een API key:\n";
    echo "1. Ga naar https://wise.com en log in\n";
    echo "2. Ga naar Settings → API tokens\n";
    echo "3. Klik op 'Add new token'\n";
    echo "4. Selecteer 'Full access'\n";
    echo "5. Kopieer de API key\n\n";
    exit(1);
}

$apiKey = trim($argv[1]);
$baseUrl = 'https://api.wise.com';

// Check if it's a sandbox key
if (strpos($apiKey, 'sandbox') !== false) {
    $baseUrl = 'https://api.sandbox.transferwise.tech';
    echo "⚠️  Sandbox modus gedetecteerd\n\n";
}

echo "🔄 Verbinden met Wise API...\n\n";

/**
 * Make API request
 */
function wiseRequest(string $method, string $endpoint, string $apiKey, string $baseUrl, ?array $data = null): ?array
{
    $url = $baseUrl . $endpoint;
    $ch = curl_init($url);

    $headers = [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "❌ CURL Error: $error\n";
        return null;
    }

    if ($httpCode >= 400) {
        $result = json_decode($response, true);
        $errorMsg = $result['errors'][0]['message'] ?? $result['error_description'] ?? "HTTP $httpCode";
        echo "❌ API Error ($httpCode): $errorMsg\n";
        return null;
    }

    return json_decode($response, true);
}

// Step 1: Get profiles
echo "1️⃣  Ophalen van profielen...\n";
$profiles = wiseRequest('GET', '/v1/profiles', $apiKey, $baseUrl);

if (!$profiles || empty($profiles)) {
    echo "❌ Kon geen profielen ophalen. Controleer je API key.\n";
    exit(1);
}

echo "   ✅ " . count($profiles) . " profiel(en) gevonden\n\n";

// Display profiles
echo "📋 Beschikbare profielen:\n";
echo str_repeat('-', 60) . "\n";

$businessProfiles = [];
$personalProfiles = [];

foreach ($profiles as $profile) {
    $type = $profile['type'];
    $id = $profile['id'];

    if ($type === 'business') {
        $name = $profile['details']['name'] ?? 'Business';
        $businessProfiles[] = $profile;
        echo "   🏢 [$id] $name (Business)\n";
    } else {
        $firstName = $profile['details']['firstName'] ?? '';
        $lastName = $profile['details']['lastName'] ?? '';
        $name = trim("$firstName $lastName") ?: 'Personal';
        $personalProfiles[] = $profile;
        echo "   👤 [$id] $name (Personal)\n";
    }
}
echo str_repeat('-', 60) . "\n\n";

// Select best profile (prefer business)
$selectedProfile = !empty($businessProfiles) ? $businessProfiles[0] : $personalProfiles[0];
$profileId = $selectedProfile['id'];
$profileType = $selectedProfile['type'];

echo "2️⃣  Geselecteerd profiel: $profileId ($profileType)\n\n";

// Step 2: Get balances
echo "3️⃣  Ophalen van saldi...\n";
$balances = wiseRequest('GET', "/v4/profiles/$profileId/balances?types=STANDARD", $apiKey, $baseUrl);

if ($balances && !empty($balances)) {
    echo "   💰 Beschikbare saldi:\n";
    foreach ($balances as $balance) {
        $currency = $balance['currency'];
        $amount = $balance['amount']['value'];
        echo "      $currency: " . number_format($amount, 2) . "\n";
    }
} else {
    echo "   ⚠️  Geen saldi gevonden of kon niet ophalen\n";
}
echo "\n";

// Step 3: Test quote creation
echo "4️⃣  Test quote aanmaken (€10 EUR→EUR)...\n";
$quoteData = [
    'profile' => (int) $profileId,
    'sourceCurrency' => 'EUR',
    'targetCurrency' => 'EUR',
    'sourceAmount' => 10.00,
    'payOut' => 'BANK_TRANSFER'
];

$quote = wiseRequest('POST', '/v3/quotes', $apiKey, $baseUrl, $quoteData);

if ($quote && isset($quote['id'])) {
    echo "   ✅ Quote succesvol aangemaakt\n";
    echo "      Quote ID: {$quote['id']}\n";
    echo "      Fee: €" . ($quote['fee'] ?? 0) . "\n";
} else {
    echo "   ⚠️  Kon geen quote aanmaken (mogelijk geen EUR balans)\n";
}
echo "\n";

// Step 4: Update .env file
echo "5️⃣  Bijwerken van .env bestand...\n";

$envFile = dirname(__DIR__) . '/.env';
$envContent = file_get_contents($envFile);

// Update or add WISE_API_KEY
if (preg_match('/^WISE_API_KEY=.*$/m', $envContent)) {
    $envContent = preg_replace('/^WISE_API_KEY=.*$/m', "WISE_API_KEY=$apiKey", $envContent);
} else {
    $envContent .= "\nWISE_API_KEY=$apiKey";
}

// Update or add WISE_PROFILE_ID
if (preg_match('/^WISE_PROFILE_ID=.*$/m', $envContent)) {
    $envContent = preg_replace('/^WISE_PROFILE_ID=.*$/m', "WISE_PROFILE_ID=$profileId", $envContent);
} else {
    $envContent .= "\nWISE_PROFILE_ID=$profileId";
}

file_put_contents($envFile, $envContent);
echo "   ✅ .env bijgewerkt\n\n";

// Step 5: Save config
echo "6️⃣  Opslaan configuratie...\n";

$configDir = dirname(__DIR__) . '/storage/wise';
if (!is_dir($configDir)) {
    mkdir($configDir, 0755, true);
}

$config = [
    'api_key' => substr($apiKey, 0, 10) . '...' . substr($apiKey, -4),
    'profile_id' => $profileId,
    'profile_type' => $profileType,
    'configured_at' => date('Y-m-d H:i:s'),
    'sandbox' => strpos($apiKey, 'sandbox') !== false
];

file_put_contents($configDir . '/config.json', json_encode($config, JSON_PRETTY_PRINT));
echo "   ✅ Configuratie opgeslagen in storage/wise/config.json\n\n";

// Summary
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    SETUP VOLTOOID! ✅                        ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║  Wise is nu geconfigureerd voor internationale uitbetalingen ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
printf("║  Profile ID: %-46s ║\n", $profileId);
printf("║  Type: %-52s ║\n", $profileType);
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "🔄 Hoe het werkt:\n";
echo "   • Nederlandse IBANs (NL...) → Bunq\n";
echo "   • Internationale IBANs (BE, DE, FR, etc.) → Wise\n\n";

echo "📝 Test de configuratie:\n";
echo "   php scripts/test-wise.php\n\n";

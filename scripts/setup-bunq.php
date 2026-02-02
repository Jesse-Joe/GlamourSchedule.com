<?php
/**
 * Bunq API Setup Script
 *
 * Run this once to set up Bunq authentication:
 * php scripts/setup-bunq.php YOUR_API_KEY
 */

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

define('GLAMOUR_LOADED', true);
define('BASE_PATH', dirname(__DIR__));

$apiKey = $argv[1] ?? null;

if (!$apiKey) {
    echo "Usage: php setup-bunq.php YOUR_BUNQ_API_KEY\n";
    echo "\nGet your API key from:\n";
    echo "1. Open bunq app\n";
    echo "2. Go to Profile → Security → API Keys\n";
    echo "3. Create a new API key\n";
    exit(1);
}

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║              BUNQ API SETUP                                   ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

// Generate RSA key pair
echo "Stap 1: RSA key pair genereren...\n";
$config = [
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
];
$keypair = openssl_pkey_new($config);
openssl_pkey_export($keypair, $privateKey);
$publicKeyDetails = openssl_pkey_get_details($keypair);
$publicKey = $publicKeyDetails['key'];

echo "  ✓ RSA keys gegenereerd\n";

// Save keys
$keysDir = BASE_PATH . '/storage/bunq';
if (!is_dir($keysDir)) {
    mkdir($keysDir, 0700, true);
}
file_put_contents($keysDir . '/private.pem', $privateKey);
file_put_contents($keysDir . '/public.pem', $publicKey);
echo "  ✓ Keys opgeslagen in storage/bunq/\n\n";

// Step 2: Create installation
echo "Stap 2: Bunq installation aanmaken...\n";

$ch = curl_init('https://api.bunq.com/v1/installation');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode([
        'client_public_key' => $publicKey
    ])
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$installData = json_decode($response, true);

if ($httpCode !== 200 || isset($installData['Error'])) {
    echo "  ✗ Fout: " . ($installData['Error'][0]['error_description'] ?? 'Unknown error') . "\n";
    exit(1);
}

$installationToken = $installData['Response'][0]['Token']['token'];
$serverPublicKey = $installData['Response'][2]['ServerPublicKey']['server_public_key'];

echo "  ✓ Installation token ontvangen\n";

// Save server public key
file_put_contents($keysDir . '/server_public.pem', $serverPublicKey);

// Step 3: Register device
echo "\nStap 3: Device registreren...\n";

$ch = curl_init('https://api.bunq.com/v1/device-server');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-Bunq-Client-Authentication: ' . $installationToken
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'description' => 'GlamourSchedule Payment Server',
        'secret' => $apiKey,
        'permitted_ips' => ['*']
    ])
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$deviceData = json_decode($response, true);

if ($httpCode !== 200 || isset($deviceData['Error'])) {
    echo "  ✗ Fout: " . ($deviceData['Error'][0]['error_description'] ?? 'Unknown error') . "\n";
    exit(1);
}

echo "  ✓ Device geregistreerd\n";

// Step 4: Create session
echo "\nStap 4: Sessie aanmaken...\n";

$ch = curl_init('https://api.bunq.com/v1/session-server');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-Bunq-Client-Authentication: ' . $installationToken
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'secret' => $apiKey
    ])
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$sessionData = json_decode($response, true);

if ($httpCode !== 200 || isset($sessionData['Error'])) {
    echo "  ✗ Fout: " . ($sessionData['Error'][0]['error_description'] ?? 'Unknown error') . "\n";
    exit(1);
}

$sessionToken = $sessionData['Response'][1]['Token']['token'];
$userId = null;

// Find user ID
foreach ($sessionData['Response'] as $item) {
    if (isset($item['UserCompany'])) {
        $userId = $item['UserCompany']['id'];
        break;
    } elseif (isset($item['UserPerson'])) {
        $userId = $item['UserPerson']['id'];
        break;
    } elseif (isset($item['UserApiKey'])) {
        $userId = $item['UserApiKey']['id'];
        break;
    }
}

echo "  ✓ Sessie aangemaakt\n";
echo "  ✓ User ID: $userId\n";

// Step 5: Get monetary accounts
echo "\nStap 5: Bankrekeningen ophalen...\n";

$ch = curl_init("https://api.bunq.com/v1/user/$userId/monetary-account");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-Bunq-Client-Authentication: ' . $sessionToken
    ]
]);
$response = curl_exec($ch);
curl_close($ch);

$accountsData = json_decode($response, true);

if (isset($accountsData['Error'])) {
    echo "  ✗ Fout: " . $accountsData['Error'][0]['error_description'] . "\n";
    exit(1);
}

echo "\n  Gevonden rekeningen:\n";
$accounts = [];
foreach ($accountsData['Response'] as $item) {
    $accountType = array_key_first($item);
    $account = $item[$accountType];
    $accounts[] = [
        'id' => $account['id'],
        'description' => $account['description'],
        'balance' => $account['balance']['value'] ?? '0.00',
        'iban' => $account['alias'][0]['value'] ?? 'N/A'
    ];
    echo "  - [{$account['id']}] {$account['description']} - €{$account['balance']['value']} ({$account['alias'][0]['value']})\n";
}

// Save configuration
$configData = [
    'installation_token' => $installationToken,
    'session_token' => $sessionToken,
    'user_id' => $userId,
    'accounts' => $accounts,
    'created_at' => date('Y-m-d H:i:s')
];
file_put_contents($keysDir . '/config.json', json_encode($configData, JSON_PRETTY_PRINT));

// Select first account as default
$defaultAccount = $accounts[0]['id'] ?? null;

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║  ✅ BUNQ SETUP COMPLEET!                                      ║\n";
echo "╠═══════════════════════════════════════════════════════════════╣\n";
echo "║                                                               ║\n";
echo "║  Voeg toe aan .env:                                           ║\n";
echo "║                                                               ║\n";
echo "║  BUNQ_API_KEY=$apiKey\n";
echo "║  BUNQ_ACCOUNT_ID=$defaultAccount\n";
echo "║  BUNQ_SESSION_TOKEN=$sessionToken\n";
echo "║                                                               ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n";

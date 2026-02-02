<?php
/**
 * Test Wise Payment
 * Simulates a small payment to verify the integration works
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load environment variables
$envFile = dirname(__DIR__) . '/.env';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos($line, '#') === 0) continue;
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

use GlamourSchedule\Services\WiseService;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║              WISE PAYMENT TEST                               ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$wise = new WiseService();

// Check configuration
echo "1️⃣  Configuratie check...\n";
if (!$wise->isConfigured()) {
    echo "   ❌ Wise is niet geconfigureerd\n";
    exit(1);
}
echo "   ✅ Wise is geconfigureerd\n\n";

// Check balance
echo "2️⃣  Saldo ophalen...\n";
$balance = $wise->getEurBalance();
if ($balance === null) {
    echo "   ❌ Kon saldo niet ophalen\n";
    exit(1);
}
echo "   💰 EUR Saldo: €" . number_format($balance, 2) . "\n\n";

// Test payment details
$testAmount = 1.00; // €1 test
$testIban = 'NL49BUNQ2181299099'; // Your own Bunq IBAN for testing
$testName = 'GlamourSchedule Test';
$testDescription = 'Wise test betaling - ' . date('Y-m-d H:i:s');

echo "3️⃣  Test betaling voorbereiden...\n";
echo "   Bedrag: €" . number_format($testAmount, 2) . "\n";
echo "   Naar: $testName\n";
echo "   IBAN: $testIban\n";
echo "   Omschrijving: $testDescription\n\n";

// Check if we have enough balance
if ($balance < $testAmount) {
    echo "   ❌ Onvoldoende saldo voor test (nodig: €" . number_format($testAmount, 2) . ")\n";
    exit(1);
}

echo "4️⃣  Quote aanmaken...\n";
$quote = $wise->createQuote($testAmount, 'EUR', 'EUR');
if (!$quote) {
    echo "   ❌ Kon geen quote aanmaken\n";
    exit(1);
}
echo "   ✅ Quote ID: {$quote['quote_id']}\n";
echo "   Fee: €" . number_format($quote['fee'], 2) . "\n\n";

echo "5️⃣  Ontvanger aanmaken/ophalen...\n";
$recipient = $wise->getOrCreateRecipient($testName, $testIban, 'EUR');
if (!$recipient) {
    echo "   ❌ Kon ontvanger niet aanmaken\n";
    exit(1);
}
echo "   ✅ Recipient ID: {$recipient['recipient_id']}\n\n";

echo "6️⃣  Transfer aanmaken...\n";
$transfer = $wise->createTransfer($quote['quote_id'], $recipient['recipient_id'], $testDescription);
if (!$transfer) {
    echo "   ❌ Kon transfer niet aanmaken\n";
    exit(1);
}
echo "   ✅ Transfer ID: {$transfer['transfer_id']}\n";
echo "   Status: {$transfer['status']}\n\n";

echo "7️⃣  Transfer financieren (uitvoeren)...\n";
$funding = $wise->fundTransfer($transfer['transfer_id']);
if (!$funding) {
    echo "   ❌ Kon transfer niet financieren\n";
    exit(1);
}
echo "   ✅ Funding status: {$funding['status']}\n\n";

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    TEST GESLAAGD! ✅                         ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║  Transfer ID: " . str_pad($transfer['transfer_id'], 44) . " ║\n";
echo "║  Bedrag: €" . str_pad(number_format($testAmount, 2), 48) . " ║\n";
echo "║  Status: " . str_pad($funding['status'], 49) . " ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Check final status
echo "8️⃣  Finale status controleren...\n";
sleep(2); // Wait a moment
$status = $wise->getTransferStatus($transfer['transfer_id']);
if ($status) {
    echo "   Transfer Status: {$status['status']}\n";
    echo "   Voltooid: " . ($status['completed'] ? 'Ja' : 'Nee (in verwerking)') . "\n";
}

echo "\n✅ Wise uitbetaling werkt correct!\n";

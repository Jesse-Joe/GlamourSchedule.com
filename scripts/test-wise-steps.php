<?php
/**
 * Test Wise Payment Steps in Detail
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

$wise = new WiseService();

$testName = "JJT-Services";
$testIban = "NL10KNAB0417886977";
$testAmount = 5.00;
$testRef = "Test " . date('H:i:s');

echo "=== Wise Payment Test (Stap voor Stap) ===\n\n";
echo "Bedrag: €$testAmount\n";
echo "Naar: $testName\n";
echo "IBAN: $testIban\n";
echo "Saldo: €" . number_format($wise->getEurBalance() ?? 0, 2) . "\n\n";

// Step 1: Create Recipient
echo "STAP 1: Ontvanger aanmaken...\n";
$recipient = $wise->getOrCreateRecipient($testName, $testIban, 'EUR');
if ($recipient) {
    echo "   OK - Recipient ID: {$recipient['recipient_id']}\n";
    print_r($recipient);
} else {
    die("   FOUT bij aanmaken recipient\n");
}

// Step 2: Create Quote
echo "\nSTAP 2: Quote aanmaken (€$testAmount)...\n";
$quote = $wise->createQuote($testAmount, 'EUR', 'EUR', $recipient['recipient_id']);
if ($quote) {
    echo "   OK - Quote ID: {$quote['quote_id']}\n";
    echo "   Source Amount: €{$quote['source_amount']}\n";
    echo "   Target Amount: €{$quote['target_amount']}\n";
    echo "   Fee: €{$quote['fee']}\n";
    print_r($quote);
} else {
    die("   FOUT bij aanmaken quote\n");
}

// Step 3: Create Transfer
echo "\nSTAP 3: Transfer aanmaken...\n";
$transfer = $wise->createTransfer($quote['quote_id'], $recipient['recipient_id'], $testRef);
if ($transfer) {
    echo "   OK - Transfer ID: {$transfer['transfer_id']}\n";
    echo "   Status: {$transfer['status']}\n";
    print_r($transfer);
} else {
    die("   FOUT bij aanmaken transfer\n");
}

// Step 4: Fund Transfer (will likely fail without API permission)
echo "\nSTAP 4: Transfer financieren...\n";
$fund = $wise->fundTransfer($transfer['transfer_id']);
if ($fund) {
    echo "   OK - Funding status: {$fund['status']}\n";
    print_r($fund);
} else {
    echo "   WAARSCHUWING: Funding mislukt (verwacht zonder API permissie)\n";
    echo "   Transfer staat nu als 'incoming_payment_waiting' in Wise\n";
}

echo "\n=== Test Voltooid ===\n";
echo "Check: https://wise.com/transactions\n";

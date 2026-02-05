<?php
/**
 * Test Stripe Integration
 */

require dirname(__DIR__) . '/vendor/autoload.php';

// Load env
$lines = file(dirname(__DIR__) . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos($line, '#') === 0) continue;
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

echo "=== Stripe Integratie Test ===\n\n";

$secretKey = getenv('STRIPE_SECRET_KEY');
$publicKey = getenv('STRIPE_PUBLIC_KEY');

echo "1. API Keys Check\n";
echo "   Secret Key: " . (empty($secretKey) ? "NIET INGESTELD" : substr($secretKey, 0, 12) . "..." . substr($secretKey, -4)) . "\n";
echo "   Public Key: " . (empty($publicKey) ? "NIET INGESTELD" : substr($publicKey, 0, 12) . "..." . substr($publicKey, -4)) . "\n\n";

if (empty($secretKey)) {
    die("ERROR: Stripe secret key niet ingesteld!\n");
}

// Initialize Stripe
\Stripe\Stripe::setApiKey($secretKey);

echo "2. Stripe Connectie Test\n";
try {
    $balance = \Stripe\Balance::retrieve();
    echo "   Status: VERBONDEN\n";

    foreach ($balance->available as $b) {
        echo "   Beschikbaar: " . strtoupper($b->currency) . " " . number_format($b->amount / 100, 2) . "\n";
    }
    foreach ($balance->pending as $b) {
        echo "   Pending: " . strtoupper($b->currency) . " " . number_format($b->amount / 100, 2) . "\n";
    }
} catch (\Stripe\Exception\AuthenticationException $e) {
    echo "   ERROR: Authenticatie mislukt - " . $e->getMessage() . "\n";
    exit(1);
} catch (\Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n3. Test Checkout Session Aanmaken\n";
try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Test Betaling GlamourSchedule'
                ],
                'unit_amount' => 500 // €5.00
            ],
            'quantity' => 1
        ]],
        'mode' => 'payment',
        'success_url' => 'https://glamourschedule.com/payment/success?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'https://glamourschedule.com/payment/cancelled'
    ]);

    echo "   Session ID: " . $session->id . "\n";
    echo "   Status: " . $session->status . "\n";
    echo "   Bedrag: EUR " . number_format($session->amount_total / 100, 2) . "\n";
    echo "   Checkout URL: " . $session->url . "\n";

    echo "\n   TEST CHECKOUT LINK (€5.00):\n";
    echo "   " . $session->url . "\n";

} catch (\Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n4. Ondersteunde Betaalmethodes\n";
try {
    $paymentMethods = \Stripe\PaymentMethod::all(['type' => 'card', 'limit' => 1]);
    echo "   Cards: Ondersteund\n";
} catch (\Exception $e) {
    echo "   Cards: " . $e->getMessage() . "\n";
}

echo "\n=== Test Voltooid ===\n";
echo "\nStripe is correct geconfigureerd!\n";

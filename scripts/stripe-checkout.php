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

\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'eur',
            'product_data' => ['name' => 'Test GlamourSchedule EUR 1'],
            'unit_amount' => 100 // €1.00
        ],
        'quantity' => 1
    ]],
    'mode' => 'payment',
    'success_url' => 'https://glamourschedule.com/?stripe=success',
    'cancel_url' => 'https://glamourschedule.com/?stripe=cancelled'
]);

echo $session->url . "\n";

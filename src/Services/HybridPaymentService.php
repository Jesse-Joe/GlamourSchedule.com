<?php
namespace GlamourSchedule\Services;

use Mollie\Api\MollieApiClient;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session as StripeSession;

class HybridPaymentService
{
    private MollieApiClient $mollie;
    private array $config;
    private bool $stripeEnabled;
    private ?\PDO $db = null;

    // Platform fee per booking (fixed)
    public const PLATFORM_FEE = 1.75;

    // Landen waar Mollie de voorkeur heeft (Benelux + SEPA)
    private const MOLLIE_COUNTRIES = [
        'NL', 'BE', 'DE', 'AT', 'FR', 'ES', 'IT', 'PT', 'FI', 'LU'
    ];

    // Mollie betaalmethodes
    private const MOLLIE_METHODS = [
        'ideal', 'bancontact', 'sofort', 'giropay', 'eps',
        'belfius', 'kbc', 'przelewy24', 'mybank'
    ];

    public function __construct(array $config)
    {
        $this->config = $config;

        // Initialize Mollie
        $this->mollie = new MollieApiClient();
        $this->mollie->setApiKey($config['mollie']['api_key'] ?? '');

        // Initialize Stripe if configured
        $this->stripeEnabled = !empty($config['stripe']['secret_key']);
        if ($this->stripeEnabled) {
            Stripe::setApiKey($config['stripe']['secret_key']);
        }

        // Initialize database connection for split payment checks
        if (isset($config['database'])) {
            $dbConfig = $config['database'];
            try {
                $this->db = new \PDO(
                    "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset={$dbConfig['charset']}",
                    $dbConfig['user'],
                    $dbConfig['pass'],
                    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                );
            } catch (\Exception $e) {
                // Database not available - split payments disabled
            }
        }
    }

    /**
     * Determine which provider to use based on country and method
     */
    public function getProvider(string $country, ?string $method = null): string
    {
        // If specific Mollie method requested, use Mollie
        if ($method && in_array($method, self::MOLLIE_METHODS)) {
            return 'mollie';
        }

        // If Mollie country, prefer Mollie
        if (in_array(strtoupper($country), self::MOLLIE_COUNTRIES)) {
            return 'mollie';
        }

        // International: use Stripe if available, otherwise Mollie
        return $this->stripeEnabled ? 'stripe' : 'mollie';
    }

    /**
     * Get available payment methods for a country
     */
    public function getPaymentMethods(string $country, float $amount): array
    {
        $methods = [];
        $country = strtoupper($country);

        // Always include card payments
        $methods[] = [
            'id' => 'card',
            'name' => 'Credit/Debit Card',
            'icon' => '/images/payments/card.svg',
            'provider' => $this->stripeEnabled ? 'stripe' : 'mollie'
        ];

        // Mollie methods for specific countries
        if ($country === 'NL') {
            $methods[] = [
                'id' => 'ideal',
                'name' => 'iDEAL',
                'icon' => '/images/payments/ideal.svg',
                'provider' => 'mollie'
            ];
        }

        if ($country === 'BE') {
            $methods[] = [
                'id' => 'bancontact',
                'name' => 'Bancontact',
                'icon' => '/images/payments/bancontact.svg',
                'provider' => 'mollie'
            ];
            $methods[] = [
                'id' => 'belfius',
                'name' => 'Belfius',
                'icon' => '/images/payments/belfius.svg',
                'provider' => 'mollie'
            ];
            $methods[] = [
                'id' => 'kbc',
                'name' => 'KBC/CBC',
                'icon' => '/images/payments/kbc.svg',
                'provider' => 'mollie'
            ];
        }

        if ($country === 'DE') {
            $methods[] = [
                'id' => 'giropay',
                'name' => 'Giropay',
                'icon' => '/images/payments/giropay.svg',
                'provider' => 'mollie'
            ];
            $methods[] = [
                'id' => 'sofort',
                'name' => 'SOFORT',
                'icon' => '/images/payments/sofort.svg',
                'provider' => 'mollie'
            ];
        }

        if ($country === 'AT') {
            $methods[] = [
                'id' => 'eps',
                'name' => 'EPS',
                'icon' => '/images/payments/eps.svg',
                'provider' => 'mollie'
            ];
        }

        // PayPal available everywhere via Mollie
        $methods[] = [
            'id' => 'paypal',
            'name' => 'PayPal',
            'icon' => '/images/payments/paypal.svg',
            'provider' => 'mollie'
        ];

        return $methods;
    }

    /**
     * Create a payment with the appropriate provider
     */
    public function createPayment(array $data): array
    {
        $provider = $data['provider'] ?? $this->getProvider(
            $data['country'] ?? 'NL',
            $data['method'] ?? null
        );

        if ($provider === 'stripe' && $this->stripeEnabled) {
            return $this->createStripePayment($data);
        }

        return $this->createMolliePayment($data);
    }

    /**
     * Create Mollie payment (with optional split payment for Mollie Connect businesses)
     */
    private function createMolliePayment(array $data): array
    {
        $businessId = $data['business_id'] ?? null;
        $amount = (float)$data['amount'];

        // Check if business has Mollie Connect for split payments
        $businessMollie = $this->getBusinessMollieInfo($businessId);
        // Only use split payment if business has a valid (non-test) Mollie account ID
        $useSplitPayment = $businessMollie
                          && !empty($businessMollie['mollie_account_id'])
                          && $businessMollie['mollie_onboarding_status'] === 'completed'
                          && strpos($businessMollie['mollie_account_id'], 'org_test_') === false
                          && strpos($businessMollie['mollie_account_id'], 'org_') === 0;

        // Calculate split amounts
        $platformFee = self::PLATFORM_FEE;
        $businessAmount = $amount - $platformFee;

        $paymentData = [
            'amount' => [
                'currency' => $data['currency'] ?? 'EUR',
                'value' => number_format($amount, 2, '.', '')
            ],
            'description' => $data['description'],
            'redirectUrl' => $data['redirect_url'],
            'webhookUrl' => $data['webhook_url'],
            'metadata' => array_merge($data['metadata'] ?? [], [
                'split_payment' => $useSplitPayment,
                'business_id' => $businessId,
                'platform_fee' => $platformFee,
                'business_amount' => $businessAmount
            ])
        ];

        // Add routing for split payment (funds go directly to business minus platform fee)
        if ($useSplitPayment && $businessAmount > 0) {
            $paymentData['routing'] = [
                [
                    'amount' => [
                        'currency' => 'EUR',
                        'value' => number_format($businessAmount, 2, '.', '')
                    ],
                    'destination' => [
                        'type' => 'organization',
                        'organizationId' => $businessMollie['mollie_account_id']
                    ]
                    // Note: releaseDate not set - funds available after settlement (T+1/T+2)
                    // Actual release will happen 24h after QR check-in via cron
                ]
            ];
        }

        // Add specific method if requested
        if (!empty($data['method']) && $data['method'] !== 'card') {
            $paymentData['method'] = $data['method'];
        }

        $payment = $this->mollie->payments->create($paymentData);

        return [
            'provider' => 'mollie',
            'payment_id' => $payment->id,
            'checkout_url' => $payment->getCheckoutUrl(),
            'status' => $payment->status,
            'split_payment' => $useSplitPayment,
            'platform_fee' => $useSplitPayment ? $platformFee : null,
            'business_amount' => $useSplitPayment ? $businessAmount : null
        ];
    }

    /**
     * Get business Mollie Connect information
     */
    private function getBusinessMollieInfo(?int $businessId): ?array
    {
        if (!$businessId || !$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare(
                "SELECT id, mollie_account_id, mollie_profile_id, mollie_onboarding_status
                 FROM businesses WHERE id = ?"
            );
            $stmt->execute([$businessId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get business Stripe Connect information
     */
    private function getBusinessStripeInfo(?int $businessId): ?array
    {
        if (!$businessId || !$this->db) {
            return null;
        }

        try {
            $stmt = $this->db->prepare(
                "SELECT id, stripe_account_id, stripe_onboarding_status, stripe_charges_enabled, stripe_payouts_enabled
                 FROM businesses WHERE id = ?"
            );
            $stmt->execute([$businessId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create Stripe payment (with optional split payment for Stripe Connect businesses)
     */
    private function createStripePayment(array $data): array
    {
        $businessId = $data['business_id'] ?? null;
        $amount = (float)$data['amount'];
        $amountCents = (int)($amount * 100);

        // Check if business has Stripe Connect for split payments
        $businessStripe = $this->getBusinessStripeInfo($businessId);
        $useSplitPayment = $businessStripe
                          && !empty($businessStripe['stripe_account_id'])
                          && $businessStripe['stripe_onboarding_status'] === 'completed'
                          && $businessStripe['stripe_charges_enabled'] == 1;

        // Calculate split amounts
        $platformFeeCents = (int)(self::PLATFORM_FEE * 100);
        $businessAmountCents = $amountCents - $platformFeeCents;

        $sessionData = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($data['currency'] ?? 'eur'),
                    'product_data' => [
                        'name' => $data['description']
                    ],
                    'unit_amount' => $amountCents
                ],
                'quantity' => 1
            ]],
            'mode' => 'payment',
            'success_url' => $data['redirect_url'] . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $data['cancel_url'] ?? $data['redirect_url'] . '?cancelled=1',
            'metadata' => array_merge($data['metadata'] ?? [], [
                'split_payment' => $useSplitPayment ? 'true' : 'false',
                'business_id' => $businessId,
                'platform_fee' => self::PLATFORM_FEE,
                'business_amount' => $amount - self::PLATFORM_FEE
            ])
        ];

        // Add Stripe Connect split payment (destination charge)
        if ($useSplitPayment && $businessAmountCents > 0) {
            $sessionData['payment_intent_data'] = [
                'application_fee_amount' => $platformFeeCents,
                'transfer_data' => [
                    'destination' => $businessStripe['stripe_account_id']
                ],
                'metadata' => [
                    'business_id' => $businessId,
                    'split_payment' => 'true',
                    'platform' => 'glamourschedule'
                ]
            ];
        }

        $session = StripeSession::create($sessionData);

        return [
            'provider' => 'stripe',
            'payment_id' => $session->id,
            'checkout_url' => $session->url,
            'status' => $session->payment_status,
            'split_payment' => $useSplitPayment,
            'platform_fee' => $useSplitPayment ? self::PLATFORM_FEE : null,
            'business_amount' => $useSplitPayment ? ($amount - self::PLATFORM_FEE) : null
        ];
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentId, string $provider): array
    {
        if ($provider === 'stripe') {
            return $this->getStripeStatus($paymentId);
        }

        return $this->getMollieStatus($paymentId);
    }

    private function getMollieStatus(string $paymentId): array
    {
        $payment = $this->mollie->payments->get($paymentId);

        return [
            'provider' => 'mollie',
            'payment_id' => $paymentId,
            'status' => $payment->status,
            'paid' => $payment->isPaid(),
            'failed' => $payment->isFailed(),
            'cancelled' => $payment->isCanceled(),
            'expired' => $payment->isExpired(),
            'amount' => $payment->amount->value,
            'currency' => $payment->amount->currency
        ];
    }

    private function getStripeStatus(string $sessionId): array
    {
        $session = StripeSession::retrieve($sessionId);

        $paid = $session->payment_status === 'paid';
        $failed = $session->status === 'expired';

        return [
            'provider' => 'stripe',
            'payment_id' => $sessionId,
            'status' => $session->payment_status,
            'paid' => $paid,
            'failed' => $failed,
            'cancelled' => false,
            'expired' => $session->status === 'expired',
            'amount' => $session->amount_total / 100,
            'currency' => strtoupper($session->currency)
        ];
    }

    /**
     * Check if Stripe is enabled
     */
    public function isStripeEnabled(): bool
    {
        return $this->stripeEnabled;
    }

    /**
     * Check if Mollie is enabled
     */
    public function isMollieEnabled(): bool
    {
        return !empty($this->config['mollie']['api_key']);
    }
}

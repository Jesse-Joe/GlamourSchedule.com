<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripeConnectService
{
    private $db;
    private $config;

    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
        Stripe::setApiKey($config['stripe']['secret_key']);
    }

    /**
     * Create a Stripe Connect Express account for a business
     */
    public function createConnectedAccount(array $business): ?array
    {
        try {
            $account = Account::create([
                'type' => 'express',
                'country' => $business['country'] ?? 'NL',
                'email' => $business['email'],
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'business_type' => 'individual',
                'metadata' => [
                    'business_id' => $business['id'],
                    'business_name' => $business['name'],
                    'platform' => 'glamourschedule'
                ]
            ]);

            // Update business with Stripe account ID
            $stmt = $this->db->prepare("
                UPDATE businesses
                SET stripe_account_id = ?,
                    stripe_onboarding_status = 'pending'
                WHERE id = ?
            ");
            $stmt->execute([$account->id, $business['id']]);

            return [
                'account_id' => $account->id,
                'status' => 'pending'
            ];

        } catch (ApiErrorException $e) {
            error_log("Stripe Connect create account error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate an account link for onboarding
     */
    public function createAccountLink(string $accountId, string $businessSlug): ?string
    {
        try {
            $baseUrl = $this->config['app']['url'];

            $accountLink = AccountLink::create([
                'account' => $accountId,
                'refresh_url' => $baseUrl . '/business/stripe-connect/refresh?slug=' . urlencode($businessSlug),
                'return_url' => $baseUrl . '/business/stripe-connect/return?slug=' . urlencode($businessSlug),
                'type' => 'account_onboarding',
            ]);

            return $accountLink->url;

        } catch (ApiErrorException $e) {
            error_log("Stripe Connect account link error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check account status and update database
     */
    public function syncAccountStatus(int $businessId): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT stripe_account_id FROM businesses WHERE id = ?");
            $stmt->execute([$businessId]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$business || !$business['stripe_account_id']) {
                return null;
            }

            $account = Account::retrieve($business['stripe_account_id']);

            $status = 'pending';
            if ($account->charges_enabled && $account->payouts_enabled) {
                $status = 'completed';
            } elseif ($account->details_submitted) {
                $status = 'in_progress';
            }

            // Update business
            $stmt = $this->db->prepare("
                UPDATE businesses
                SET stripe_onboarding_status = ?,
                    stripe_charges_enabled = ?,
                    stripe_payouts_enabled = ?,
                    stripe_connected_at = CASE WHEN ? = 'completed' AND stripe_connected_at IS NULL THEN NOW() ELSE stripe_connected_at END
                WHERE id = ?
            ");
            $stmt->execute([
                $status,
                $account->charges_enabled ? 1 : 0,
                $account->payouts_enabled ? 1 : 0,
                $status,
                $businessId
            ]);

            return [
                'account_id' => $account->id,
                'status' => $status,
                'charges_enabled' => $account->charges_enabled,
                'payouts_enabled' => $account->payouts_enabled,
                'details_submitted' => $account->details_submitted
            ];

        } catch (ApiErrorException $e) {
            error_log("Stripe Connect sync error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a Checkout Session with automatic split to connected account
     */
    public function createSplitCheckoutSession(array $booking, array $business, string $returnUrl, string $cancelUrl): ?array
    {
        try {
            if (!$business['stripe_account_id'] || !$business['stripe_charges_enabled']) {
                return null;
            }

            $amount = (int)($booking['total_price'] * 100); // Convert to cents
            $platformFee = (int)(($this->config['pricing']['admin_fee_per_booking'] ?? 1.75) * 100);

            // Create checkout session with destination charge
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $booking['service_name'] ?? 'Booking',
                            'description' => 'Afspraak bij ' . $business['name'],
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $returnUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
                'payment_intent_data' => [
                    'application_fee_amount' => $platformFee,
                    'transfer_data' => [
                        'destination' => $business['stripe_account_id'],
                    ],
                    'metadata' => [
                        'booking_id' => $booking['id'],
                        'business_id' => $business['id'],
                        'platform' => 'glamourschedule',
                        'split_payment' => 'true'
                    ]
                ],
                'metadata' => [
                    'booking_id' => $booking['id'],
                    'business_id' => $business['id']
                ]
            ]);

            return [
                'session_id' => $session->id,
                'checkout_url' => $session->url,
                'amount' => $amount,
                'platform_fee' => $platformFee,
                'business_payout' => $amount - $platformFee
            ];

        } catch (ApiErrorException $e) {
            error_log("Stripe Connect checkout error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a direct PaymentIntent with split
     */
    public function createSplitPaymentIntent(array $booking, array $business): ?array
    {
        try {
            if (!$business['stripe_account_id'] || !$business['stripe_charges_enabled']) {
                return null;
            }

            $amount = (int)($booking['total_price'] * 100);
            $platformFee = (int)(($this->config['pricing']['admin_fee_per_booking'] ?? 1.75) * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'eur',
                'application_fee_amount' => $platformFee,
                'transfer_data' => [
                    'destination' => $business['stripe_account_id'],
                ],
                'metadata' => [
                    'booking_id' => $booking['id'],
                    'business_id' => $business['id'],
                    'platform' => 'glamourschedule',
                    'split_payment' => 'true'
                ]
            ]);

            return [
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $amount,
                'platform_fee' => $platformFee
            ];

        } catch (ApiErrorException $e) {
            error_log("Stripe Connect payment intent error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Stripe dashboard login link for connected account
     */
    public function getLoginLink(string $accountId): ?string
    {
        try {
            $loginLink = Account::createLoginLink($accountId);
            return $loginLink->url;
        } catch (ApiErrorException $e) {
            error_log("Stripe Connect login link error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if business has active Stripe Connect
     */
    public function isConnected(array $business): bool
    {
        return !empty($business['stripe_account_id'])
            && $business['stripe_charges_enabled'] == 1
            && $business['stripe_onboarding_status'] === 'completed';
    }

    /**
     * Disconnect Stripe account (remove from platform, not delete account)
     */
    public function disconnect(int $businessId): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE businesses
                SET stripe_account_id = NULL,
                    stripe_onboarding_status = 'pending',
                    stripe_charges_enabled = 0,
                    stripe_payouts_enabled = 0,
                    stripe_connected_at = NULL
                WHERE id = ?
            ");
            return $stmt->execute([$businessId]);
        } catch (\Exception $e) {
            error_log("Stripe Connect disconnect error: " . $e->getMessage());
            return false;
        }
    }
}

<?php
namespace GlamourSchedule\Services;

/**
 * Wise (TransferWise) API Service
 * Handles automated international bank transfers to salon bank accounts
 *
 * API Docs: https://docs.wise.com/api-docs
 */
class WiseService
{
    private string $apiKey;
    private string $profileId;
    private string $baseUrl;
    private bool $sandbox;

    // Supported currencies for payouts
    private const SUPPORTED_CURRENCIES = [
        'EUR', 'GBP', 'USD', 'CHF', 'SEK', 'NOK', 'DKK', 'PLN', 'CZK', 'HUF',
        'RON', 'BGN', 'HRK', 'AUD', 'NZD', 'CAD', 'SGD', 'HKD', 'JPY', 'INR',
        'TRY', 'MAD', 'AED', 'ZAR', 'BRL', 'MXN', 'THB', 'MYR', 'PHP', 'IDR'
    ];

    // Country to currency mapping
    private const COUNTRY_CURRENCIES = [
        'NL' => 'EUR', 'BE' => 'EUR', 'DE' => 'EUR', 'FR' => 'EUR', 'ES' => 'EUR',
        'IT' => 'EUR', 'PT' => 'EUR', 'AT' => 'EUR', 'IE' => 'EUR', 'FI' => 'EUR',
        'LU' => 'EUR', 'GR' => 'EUR', 'SK' => 'EUR', 'SI' => 'EUR', 'EE' => 'EUR',
        'LV' => 'EUR', 'LT' => 'EUR', 'CY' => 'EUR', 'MT' => 'EUR',
        'GB' => 'GBP', 'US' => 'USD', 'CA' => 'CAD', 'AU' => 'AUD', 'NZ' => 'NZD',
        'CH' => 'CHF', 'SE' => 'SEK', 'NO' => 'NOK', 'DK' => 'DKK', 'PL' => 'PLN',
        'CZ' => 'CZK', 'HU' => 'HUF', 'RO' => 'RON', 'BG' => 'BGN', 'HR' => 'EUR',
        'TR' => 'TRY', 'MA' => 'MAD', 'AE' => 'AED', 'ZA' => 'ZAR',
        'BR' => 'BRL', 'MX' => 'MXN', 'JP' => 'JPY', 'SG' => 'SGD', 'HK' => 'HKD',
        'IN' => 'INR', 'TH' => 'THB', 'MY' => 'MYR', 'PH' => 'PHP', 'ID' => 'IDR'
    ];

    // Exchange rate cache (in-memory for single request)
    private array $rateCache = [];

    public function __construct()
    {
        $sandboxEnv = strtolower(getenv('WISE_SANDBOX') ?: 'false');
        $this->sandbox = in_array($sandboxEnv, ['true', '1', 'yes'], true);
        $this->apiKey = getenv('WISE_API_KEY') ?: '';
        $this->profileId = getenv('WISE_PROFILE_ID') ?: '';

        $this->baseUrl = $this->sandbox
            ? 'https://api.sandbox.transferwise.tech'
            : 'https://api.wise.com';
    }

    /**
     * Check if Wise is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->profileId);
    }

    /**
     * Get account balance(s)
     */
    public function getBalances(): array
    {
        $response = $this->request('GET', "/v4/profiles/{$this->profileId}/balances?types=STANDARD");

        if ($response && is_array($response)) {
            $balances = [];
            foreach ($response as $balance) {
                $balances[$balance['currency']] = [
                    'currency' => $balance['currency'],
                    'amount' => $balance['amount']['value'],
                    'available' => $balance['amount']['value']
                ];
            }
            return $balances;
        }

        return [];
    }

    /**
     * Get EUR balance
     */
    public function getEurBalance(): ?float
    {
        $balances = $this->getBalances();
        return $balances['EUR']['amount'] ?? null;
    }

    /**
     * Create a quote for a transfer (using v3 profile-specific endpoint)
     */
    public function createQuote(float $amount, string $sourceCurrency = 'EUR', string $targetCurrency = 'EUR', ?int $targetAccount = null): ?array
    {
        $data = [
            'sourceCurrency' => $sourceCurrency,
            'targetCurrency' => $targetCurrency,
            'sourceAmount' => $amount
        ];

        // If target account is specified, include it for better quote accuracy
        if ($targetAccount) {
            $data['targetAccount'] = $targetAccount;
        }

        // Use profile-specific endpoint to ensure quote is linked to profile
        $response = $this->request('POST', "/v3/profiles/{$this->profileId}/quotes", $data);

        if ($response && isset($response['id'])) {
            return [
                'quote_id' => $response['id'],
                'source_amount' => $response['sourceAmount'] ?? $amount,
                'target_amount' => $response['targetAmount'] ?? $amount,
                'fee' => $response['fee'] ?? 0,
                'rate' => $response['rate'] ?? 1,
                'expires_at' => $response['expirationTime'] ?? null
            ];
        }

        return null;
    }

    /**
     * Create a recipient (bank account)
     */
    public function createRecipient(string $name, string $iban, string $currency = 'EUR'): ?array
    {
        // Determine account type based on IBAN country
        $country = substr($iban, 0, 2);

        $data = [
            'profile' => (int) $this->profileId,
            'accountHolderName' => $name,
            'currency' => $currency,
            'type' => 'iban',
            'details' => [
                'iban' => strtoupper(str_replace(' ', '', $iban))
            ]
        ];

        // For non-SEPA countries, might need additional details
        if (!in_array($country, ['NL', 'BE', 'DE', 'FR', 'ES', 'IT', 'AT', 'PT', 'IE', 'FI', 'LU'])) {
            $data['details']['legalType'] = 'BUSINESS';
        }

        $response = $this->request('POST', '/v1/accounts', $data);

        if ($response && isset($response['id'])) {
            return [
                'recipient_id' => $response['id'],
                'name' => $response['accountHolderName'],
                'currency' => $response['currency'],
                'iban' => $response['details']['iban'] ?? $iban
            ];
        }

        return null;
    }

    /**
     * Get or create recipient by IBAN
     */
    public function getOrCreateRecipient(string $name, string $iban, string $currency = 'EUR'): ?array
    {
        // First, try to find existing recipient
        $response = $this->request('GET', "/v1/accounts?profile={$this->profileId}&currency=$currency");

        if ($response && is_array($response)) {
            $cleanIban = strtoupper(str_replace(' ', '', $iban));
            foreach ($response as $account) {
                if (isset($account['details']['iban']) &&
                    strtoupper(str_replace(' ', '', $account['details']['iban'])) === $cleanIban) {
                    return [
                        'recipient_id' => $account['id'],
                        'name' => $account['accountHolderName'],
                        'currency' => $account['currency'],
                        'iban' => $account['details']['iban']
                    ];
                }
            }
        }

        // Create new recipient
        return $this->createRecipient($name, $iban, $currency);
    }

    /**
     * Create a transfer
     */
    public function createTransfer(string $quoteId, int $recipientId, string $reference): ?array
    {
        $data = [
            'targetAccount' => $recipientId,
            'quoteUuid' => $quoteId,
            'customerTransactionId' => $this->generateTransactionId(),
            'details' => [
                'reference' => substr($reference, 0, 35) // Max 35 chars for SEPA
            ]
        ];

        $response = $this->request('POST', '/v1/transfers', $data);

        if ($response && isset($response['id'])) {
            return [
                'transfer_id' => $response['id'],
                'status' => $response['status'],
                'source_amount' => $response['sourceValue'],
                'target_amount' => $response['targetValue'],
                'reference' => $reference
            ];
        }

        return null;
    }

    /**
     * Fund a transfer (execute the payment)
     */
    public function fundTransfer(int $transferId): ?array
    {
        $data = [
            'type' => 'BALANCE'
        ];

        $response = $this->request('POST', "/v3/profiles/{$this->profileId}/transfers/{$transferId}/payments", $data);

        if ($response && isset($response['status'])) {
            return [
                'status' => $response['status'],
                'balance_transaction_id' => $response['balanceTransactionId'] ?? null
            ];
        }

        return null;
    }

    /**
     * Make a complete payment (recipient + quote + transfer + fund)
     */
    public function makePayment(string $iban, string $name, float $amount, string $description, string $currency = 'EUR'): array
    {
        $this->log("Starting payment: €$amount to $name ($iban)");

        // Step 1: Get or create recipient FIRST (needed for quote)
        $recipient = $this->getOrCreateRecipient($name, $iban, $currency);
        if (!$recipient) {
            $this->log("Failed to create recipient");
            return ['success' => false, 'error' => 'Kon ontvanger niet aanmaken'];
        }
        $this->log("Recipient: {$recipient['recipient_id']}");

        // Step 2: Create quote with recipient for accurate pricing
        $quote = $this->createQuote($amount, 'EUR', $currency, $recipient['recipient_id']);
        if (!$quote) {
            $this->log("Failed to create quote");
            return ['success' => false, 'error' => 'Kon geen quote aanmaken'];
        }
        $this->log("Quote created: {$quote['quote_id']}");

        // Step 3: Create transfer
        $transfer = $this->createTransfer($quote['quote_id'], $recipient['recipient_id'], $description);
        if (!$transfer) {
            $this->log("Failed to create transfer");
            return ['success' => false, 'error' => 'Kon transfer niet aanmaken'];
        }
        $this->log("Transfer created: {$transfer['transfer_id']}");

        // Step 4: Fund the transfer
        $funding = $this->fundTransfer($transfer['transfer_id']);
        if (!$funding || $funding['status'] === 'REJECTED') {
            $this->log("Failed to fund transfer");
            return ['success' => false, 'error' => 'Kon transfer niet financieren (onvoldoende saldo?)'];
        }
        $this->log("Transfer funded: {$funding['status']}");

        return [
            'success' => true,
            'transfer_id' => $transfer['transfer_id'],
            'quote_id' => $quote['quote_id'],
            'recipient_id' => $recipient['recipient_id'],
            'amount' => $amount,
            'fee' => $quote['fee'],
            'status' => $funding['status']
        ];
    }

    /**
     * Make batch payments
     */
    public function makeBatchPayments(array $payments): array
    {
        $results = [];

        foreach ($payments as $payment) {
            $result = $this->makePayment(
                $payment['iban'],
                $payment['name'],
                $payment['amount'],
                $payment['description'],
                $payment['currency'] ?? 'EUR'
            );

            $results[] = array_merge($payment, $result);

            // Small delay to avoid rate limiting
            usleep(200000); // 200ms
        }

        return $results;
    }

    /**
     * Get transfer status
     */
    public function getTransferStatus(int $transferId): ?array
    {
        $response = $this->request('GET', "/v1/transfers/{$transferId}");

        if ($response && isset($response['id'])) {
            return [
                'transfer_id' => $response['id'],
                'status' => $response['status'],
                'source_amount' => $response['sourceValue'],
                'target_amount' => $response['targetValue'],
                'created' => $response['created'],
                'completed' => $response['status'] === 'outgoing_payment_sent'
            ];
        }

        return null;
    }

    /**
     * Get all transfers with a specific status
     * Statuses: incoming_payment_waiting, processing, funds_converted, outgoing_payment_sent, cancelled, etc.
     */
    public function getTransfersByStatus(string $status = 'incoming_payment_waiting', int $limit = 50): array
    {
        $response = $this->request('GET', "/v1/transfers?profile={$this->profileId}&status={$status}&limit={$limit}");

        if ($response && is_array($response)) {
            $transfers = [];
            foreach ($response as $transfer) {
                $transfers[] = [
                    'transfer_id' => $transfer['id'],
                    'status' => $transfer['status'],
                    'source_currency' => $transfer['sourceCurrency'],
                    'source_amount' => $transfer['sourceValue'],
                    'target_currency' => $transfer['targetCurrency'],
                    'target_amount' => $transfer['targetValue'],
                    'reference' => $transfer['reference'] ?? '',
                    'created' => $transfer['created'],
                    'recipient_name' => $transfer['details']['recipient']['name'] ?? 'Unknown'
                ];
            }
            return $transfers;
        }

        return [];
    }

    /**
     * Get all pending transfers (waiting for funding)
     */
    public function getPendingTransfers(): array
    {
        return $this->getTransfersByStatus('incoming_payment_waiting');
    }

    /**
     * Generate unique transaction ID (UUID v4 format required by Wise)
     */
    private function generateTransactionId(): string
    {
        // Generate UUID v4
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variant

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Make API request
     */
    private function request(string $method, string $endpoint, ?array $data = null): ?array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
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
            $this->log("CURL Error: $error");
            return null;
        }

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMsg = $result['errors'][0]['message'] ?? $result['error_description'] ?? "HTTP $httpCode";
            $this->log("API Error ($httpCode): $errorMsg");
            return null;
        }

        return $result;
    }

    /**
     * Log messages
     */
    private function log(string $message): void
    {
        $logFile = (defined('BASE_PATH') ? BASE_PATH : '/var/www/glamourschedule') . '/storage/logs/wise.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    /**
     * Check if currency is supported
     */
    public function isCurrencySupported(string $currency): bool
    {
        return in_array(strtoupper($currency), self::SUPPORTED_CURRENCIES);
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return self::SUPPORTED_CURRENCIES;
    }

    /**
     * Get currency for a country code
     */
    public function getCurrencyForCountry(string $countryCode): string
    {
        return self::COUNTRY_CURRENCIES[strtoupper($countryCode)] ?? 'EUR';
    }

    /**
     * Get all country currency mappings
     */
    public function getCountryCurrencies(): array
    {
        return self::COUNTRY_CURRENCIES;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // EXCHANGE RATE & MARGIN CALCULATIONS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Get live exchange rate from Wise API
     * Returns both the Wise rate and mid-market rate for comparison
     */
    public function getExchangeRate(string $sourceCurrency = 'EUR', string $targetCurrency = 'EUR'): ?array
    {
        if ($sourceCurrency === $targetCurrency) {
            return [
                'source' => $sourceCurrency,
                'target' => $targetCurrency,
                'rate' => 1.0,
                'mid_market_rate' => 1.0,
                'margin_percentage' => 0.0,
                'margin_amount' => 0.0,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }

        $cacheKey = "{$sourceCurrency}_{$targetCurrency}";
        if (isset($this->rateCache[$cacheKey])) {
            return $this->rateCache[$cacheKey];
        }

        // Get rate via quote (most accurate for actual transfers)
        $response = $this->request('GET', "/v1/rates?source={$sourceCurrency}&target={$targetCurrency}");

        if ($response && is_array($response) && !empty($response)) {
            $rateData = $response[0];
            $wiseRate = $rateData['rate'] ?? 1.0;

            // Get mid-market rate for comparison
            $midMarketRate = $this->getMidMarketRate($sourceCurrency, $targetCurrency) ?? $wiseRate;

            // Calculate margin
            $marginPercentage = $midMarketRate > 0
                ? (($midMarketRate - $wiseRate) / $midMarketRate) * 100
                : 0;

            $result = [
                'source' => $sourceCurrency,
                'target' => $targetCurrency,
                'rate' => $wiseRate,
                'mid_market_rate' => $midMarketRate,
                'margin_percentage' => round(abs($marginPercentage), 4),
                'margin_direction' => $marginPercentage >= 0 ? 'below' : 'above',
                'timestamp' => $rateData['time'] ?? date('Y-m-d H:i:s')
            ];

            $this->rateCache[$cacheKey] = $result;
            return $result;
        }

        return null;
    }

    /**
     * Get mid-market rate (interbank rate) for comparison
     * Uses ECB rates as reference for EUR pairs
     */
    private function getMidMarketRate(string $source, string $target): ?float
    {
        // Try to get from Wise's comparison endpoint
        $response = $this->request('GET', "/v1/rates?source={$source}&target={$target}");

        if ($response && is_array($response) && !empty($response)) {
            // Wise returns the mid-market rate
            return $response[0]['rate'] ?? null;
        }

        return null;
    }

    /**
     * Get detailed quote with all fees and margins
     */
    public function getDetailedQuote(float $sourceAmount, string $sourceCurrency = 'EUR', string $targetCurrency = 'EUR'): ?array
    {
        $data = [
            'sourceCurrency' => $sourceCurrency,
            'targetCurrency' => $targetCurrency,
            'sourceAmount' => $sourceAmount,
            'payOut' => 'BANK_TRANSFER'
        ];

        $response = $this->request('POST', "/v3/profiles/{$this->profileId}/quotes", $data);

        if ($response && isset($response['id'])) {
            $paymentOption = $response['paymentOptions'][0] ?? [];
            $fee = $paymentOption['fee']['total'] ?? $response['fee'] ?? 0;
            $rate = $response['rate'] ?? 1;
            $targetAmount = $paymentOption['targetAmount'] ?? $response['targetAmount'] ?? ($sourceAmount * $rate);

            // Get mid-market for comparison
            $exchangeInfo = $this->getExchangeRate($sourceCurrency, $targetCurrency);
            $midMarketRate = $exchangeInfo['mid_market_rate'] ?? $rate;

            // Calculate what you'd get at mid-market (theoretical)
            $midMarketAmount = ($sourceAmount - $fee) * $midMarketRate;
            $marginLoss = $midMarketAmount - $targetAmount;
            $marginPercentage = $midMarketAmount > 0 ? ($marginLoss / $midMarketAmount) * 100 : 0;

            return [
                'quote_id' => $response['id'],
                'source_currency' => $sourceCurrency,
                'target_currency' => $targetCurrency,
                'source_amount' => $sourceAmount,
                'target_amount' => round($targetAmount, 2),
                'fee' => [
                    'total' => round($fee, 2),
                    'currency' => $sourceCurrency,
                    'breakdown' => $this->extractFeeBreakdown($response)
                ],
                'rate' => [
                    'wise_rate' => $rate,
                    'mid_market_rate' => $midMarketRate,
                    'margin_percentage' => round(abs($marginPercentage), 4),
                    'margin_amount' => round(abs($marginLoss), 2),
                    'margin_currency' => $targetCurrency
                ],
                'delivery' => [
                    'estimate' => $paymentOption['estimatedDelivery'] ?? null,
                    'speed' => $response['deliveryEstimate']['date'] ?? 'Unknown'
                ],
                'expires_at' => $response['expirationTime'] ?? null,
                'comparison' => [
                    'wise_total_cost' => round($fee + abs($marginLoss / $rate), 2),
                    'wise_total_cost_percentage' => round((($fee + abs($marginLoss / $rate)) / $sourceAmount) * 100, 2),
                    'theoretical_at_midmarket' => round($midMarketAmount, 2),
                    'actual_received' => round($targetAmount, 2),
                    'difference' => round($midMarketAmount - $targetAmount, 2)
                ]
            ];
        }

        return null;
    }

    /**
     * Extract fee breakdown from quote response
     */
    private function extractFeeBreakdown(array $response): array
    {
        $breakdown = [];

        if (isset($response['paymentOptions'][0]['fee'])) {
            $feeDetails = $response['paymentOptions'][0]['fee'];

            if (isset($feeDetails['transferwise'])) {
                $breakdown['wise_fee'] = $feeDetails['transferwise'];
            }
            if (isset($feeDetails['payIn'])) {
                $breakdown['pay_in_fee'] = $feeDetails['payIn'];
            }
            if (isset($feeDetails['discount'])) {
                $breakdown['discount'] = $feeDetails['discount'];
            }
        }

        return $breakdown;
    }

    /**
     * Calculate payout for a business in their local currency
     * Takes EUR amount and calculates what they'll receive
     */
    public function calculatePayout(float $eurAmount, string $targetCurrency, bool $includeWiseFee = true): array
    {
        if ($targetCurrency === 'EUR') {
            return [
                'source_amount' => $eurAmount,
                'source_currency' => 'EUR',
                'target_amount' => $eurAmount,
                'target_currency' => 'EUR',
                'exchange_rate' => 1.0,
                'wise_fee' => 0.0,
                'margin_cost' => 0.0,
                'total_cost' => 0.0,
                'cost_percentage' => 0.0,
                'net_amount' => $eurAmount
            ];
        }

        $quote = $this->getDetailedQuote($eurAmount, 'EUR', $targetCurrency);

        if (!$quote) {
            // Fallback calculation if API fails
            $rate = $this->getExchangeRate('EUR', $targetCurrency);
            $estimatedRate = $rate['rate'] ?? 1.0;
            $estimatedFee = $eurAmount * 0.005; // Estimate 0.5% fee

            return [
                'source_amount' => $eurAmount,
                'source_currency' => 'EUR',
                'target_amount' => round(($eurAmount - $estimatedFee) * $estimatedRate, 2),
                'target_currency' => $targetCurrency,
                'exchange_rate' => $estimatedRate,
                'wise_fee' => round($estimatedFee, 2),
                'margin_cost' => 0.0,
                'total_cost' => round($estimatedFee, 2),
                'cost_percentage' => 0.5,
                'net_amount' => round($eurAmount - $estimatedFee, 2),
                'is_estimate' => true
            ];
        }

        return [
            'source_amount' => $eurAmount,
            'source_currency' => 'EUR',
            'target_amount' => $quote['target_amount'],
            'target_currency' => $targetCurrency,
            'exchange_rate' => $quote['rate']['wise_rate'],
            'mid_market_rate' => $quote['rate']['mid_market_rate'],
            'wise_fee' => $quote['fee']['total'],
            'margin_cost' => $quote['rate']['margin_amount'],
            'margin_percentage' => $quote['rate']['margin_percentage'],
            'total_cost' => $quote['comparison']['wise_total_cost'],
            'cost_percentage' => $quote['comparison']['wise_total_cost_percentage'],
            'net_amount_eur' => round($eurAmount - $quote['fee']['total'], 2),
            'delivery_estimate' => $quote['delivery']['estimate'],
            'quote_id' => $quote['quote_id'],
            'is_estimate' => false
        ];
    }

    /**
     * Calculate margin for multiple currencies at once
     * Useful for showing business their payout options
     */
    public function calculateMultiCurrencyPayout(float $eurAmount, array $currencies = []): array
    {
        if (empty($currencies)) {
            $currencies = ['EUR', 'GBP', 'USD', 'CHF', 'PLN', 'SEK', 'NOK', 'DKK'];
        }

        $results = [];
        foreach ($currencies as $currency) {
            $results[$currency] = $this->calculatePayout($eurAmount, $currency);
        }

        return $results;
    }

    /**
     * Get exchange rate comparison table
     * Shows rates and margins for common currency pairs
     */
    public function getExchangeRateComparison(string $baseCurrency = 'EUR'): array
    {
        $compareCurrencies = ['GBP', 'USD', 'CHF', 'PLN', 'SEK', 'NOK', 'DKK', 'CZK', 'HUF', 'TRY', 'AUD', 'CAD'];
        $results = [];

        foreach ($compareCurrencies as $target) {
            if ($target === $baseCurrency) continue;

            $rate = $this->getExchangeRate($baseCurrency, $target);
            if ($rate) {
                $results[$target] = [
                    'currency' => $target,
                    'rate' => $rate['rate'],
                    'mid_market' => $rate['mid_market_rate'],
                    'margin' => $rate['margin_percentage'] . '%',
                    'updated' => $rate['timestamp']
                ];
            }
        }

        return $results;
    }

    /**
     * Store exchange rate in database for historical tracking
     */
    public function storeExchangeRate(\PDO $pdo, string $source, string $target, float $rate, float $midMarketRate): bool
    {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO exchange_rates (source_currency, target_currency, rate, mid_market_rate, margin_percentage, recorded_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");

            $margin = $midMarketRate > 0 ? (($midMarketRate - $rate) / $midMarketRate) * 100 : 0;

            return $stmt->execute([$source, $target, $rate, $midMarketRate, abs($margin)]);
        } catch (\Exception $e) {
            $this->log("Failed to store exchange rate: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get historical exchange rates from database
     */
    public function getHistoricalRates(\PDO $pdo, string $source, string $target, int $days = 30): array
    {
        try {
            $stmt = $pdo->prepare("
                SELECT rate, mid_market_rate, margin_percentage, recorded_at
                FROM exchange_rates
                WHERE source_currency = ? AND target_currency = ?
                AND recorded_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY recorded_at DESC
            ");
            $stmt->execute([$source, $target, $days]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Format currency amount for display
     */
    public function formatCurrency(float $amount, string $currency): string
    {
        $symbols = [
            'EUR' => '€', 'GBP' => '£', 'USD' => '$', 'CHF' => 'CHF ',
            'SEK' => 'kr ', 'NOK' => 'kr ', 'DKK' => 'kr ', 'PLN' => 'zł ',
            'CZK' => 'Kč ', 'HUF' => 'Ft ', 'TRY' => '₺', 'AUD' => 'A$',
            'CAD' => 'C$', 'JPY' => '¥', 'INR' => '₹', 'AED' => 'د.إ ',
            'MAD' => 'MAD ', 'ZAR' => 'R '
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';
        $decimals = in_array($currency, ['JPY', 'HUF', 'KRW']) ? 0 : 2;

        return $symbol . number_format($amount, $decimals, ',', '.');
    }
}

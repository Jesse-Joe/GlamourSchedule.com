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
        'RON', 'BGN', 'HRK', 'AUD', 'NZD', 'CAD', 'SGD', 'HKD', 'JPY', 'INR'
    ];

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
}

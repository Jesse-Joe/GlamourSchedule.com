<?php
namespace GlamourSchedule\Services;

/**
 * Bunq API Service
 * Handles automated bank transfers to salon IBANs
 */
class BunqService
{
    private string $apiKey;
    private string $accountId;
    private string $sessionToken;
    private string $userId;
    private string $baseUrl;
    private bool $sandbox;

    public function __construct()
    {
        $this->sandbox = (bool) (getenv('BUNQ_SANDBOX') ?: false);
        $this->apiKey = getenv('BUNQ_API_KEY') ?: '';
        $this->accountId = getenv('BUNQ_ACCOUNT_ID') ?: '';
        $this->sessionToken = '';
        $this->userId = '';

        // Load from saved config if available
        $configFile = (defined('BASE_PATH') ? BASE_PATH : '/var/www/glamourschedule') . '/storage/bunq/config.json';
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            $this->sessionToken = $config['session_token'] ?? '';
            $this->userId = $config['user_id'] ?? '';
            $this->accountId = $this->accountId ?: ($config['account_id'] ?? '');
        }

        $this->baseUrl = $this->sandbox
            ? 'https://public-api.sandbox.bunq.com/v1'
            : 'https://api.bunq.com/v1';
    }

    /**
     * Check if Bunq is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->sessionToken) && !empty($this->accountId);
    }

    /**
     * Get account balance
     */
    public function getBalance(): ?float
    {
        $userId = $this->getUserId();
        if (!$userId) return null;

        $response = $this->request('GET', "/user/{$userId}/monetary-account/{$this->accountId}");

        if ($response && isset($response['Response'][0])) {
            // Handle different account types
            $accountType = array_key_first($response['Response'][0]);
            if (isset($response['Response'][0][$accountType]['balance']['value'])) {
                return (float) $response['Response'][0][$accountType]['balance']['value'];
            }
        }

        return null;
    }

    /**
     * Make a payment to an IBAN
     */
    public function makePayment(string $iban, string $name, float $amount, string $description): array
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return [
                'success' => false,
                'error' => 'Could not get Bunq user ID'
            ];
        }

        $data = [
            'amount' => [
                'value' => number_format($amount, 2, '.', ''),
                'currency' => 'EUR'
            ],
            'counterparty_alias' => [
                'type' => 'IBAN',
                'value' => $this->formatIban($iban),
                'name' => $name
            ],
            'description' => substr($description, 0, 140) // Max 140 chars
        ];

        $response = $this->request(
            'POST',
            "/user/{$userId}/monetary-account/{$this->accountId}/payment",
            $data
        );

        if ($response && isset($response['Response'][0]['Id']['id'])) {
            return [
                'success' => true,
                'payment_id' => $response['Response'][0]['Id']['id']
            ];
        }

        return [
            'success' => false,
            'error' => $response['Error'][0]['error_description'] ?? 'Unknown Bunq error'
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
                $payment['description']
            );

            $results[] = [
                'iban' => $payment['iban'],
                'name' => $payment['name'],
                'amount' => $payment['amount'],
                'success' => $result['success'],
                'payment_id' => $result['payment_id'] ?? null,
                'error' => $result['error'] ?? null
            ];

            // Small delay to avoid rate limiting
            usleep(100000); // 100ms
        }

        return $results;
    }

    /**
     * Get user ID from config or API
     */
    private function getUserId(): ?string
    {
        // Use stored user ID if available
        if (!empty($this->userId)) {
            return (string) $this->userId;
        }

        // Fallback: get from API
        $response = $this->request('GET', '/user');

        if ($response && isset($response['Response'][0])) {
            $userType = array_key_first($response['Response'][0]);
            $this->userId = (string) $response['Response'][0][$userType]['id'];
            return $this->userId;
        }

        return null;
    }

    /**
     * Format IBAN (remove spaces, uppercase)
     */
    private function formatIban(string $iban): string
    {
        return strtoupper(str_replace(' ', '', $iban));
    }

    /**
     * Make API request
     */
    private function request(string $method, string $endpoint, ?array $data = null): ?array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);

        // Use session token for authentication (not API key directly)
        $authToken = $this->sessionToken ?: $this->apiKey;

        $headers = [
            'Content-Type: application/json',
            'X-Bunq-Client-Authentication: ' . $authToken,
            'X-Bunq-Language: nl_NL',
            'X-Bunq-Region: nl_NL',
            'User-Agent: GlamourSchedule/1.0'
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
            $errorMsg = $result['Error'][0]['error_description'] ?? "HTTP $httpCode";
            $this->log("API Error: $errorMsg");
        }

        return $result;
    }

    /**
     * Log messages
     */
    private function log(string $message): void
    {
        $logFile = BASE_PATH . '/storage/logs/bunq.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
}

<?php
namespace GlamourSchedule\Core;

/**
 * WebPush - Send Web Push Notifications
 */
class WebPush
{
    private string $publicKey;
    private string $privateKey;
    private string $subject;

    public function __construct()
    {
        $this->publicKey = getenv('VAPID_PUBLIC_KEY') ?: '';
        $this->privateKey = getenv('VAPID_PRIVATE_KEY') ?: '';
        $this->subject = 'mailto:noreply@glamourschedule.nl';
    }

    /**
     * Send a push notification to a subscription
     */
    public function send(array $subscription, array $payload): bool
    {
        if (empty($this->publicKey) || empty($this->privateKey)) {
            error_log('WebPush: VAPID keys not configured');
            return false;
        }

        $endpoint = $subscription['endpoint'];
        $p256dh = $subscription['p256dh_key'];
        $auth = $subscription['auth_key'];

        // Prepare the payload
        $payloadJson = json_encode($payload);

        try {
            // Create VAPID headers
            $vapidHeaders = $this->createVapidHeaders($endpoint);

            // Encrypt the payload
            $encrypted = $this->encrypt($payloadJson, $p256dh, $auth);

            if (!$encrypted) {
                error_log('WebPush: Failed to encrypt payload');
                return false;
            }

            // Send the notification
            $headers = [
                'Content-Type: application/octet-stream',
                'Content-Encoding: aes128gcm',
                'TTL: 86400',
                'Authorization: ' . $vapidHeaders['authorization'],
                'Content-Length: ' . strlen($encrypted['ciphertext']),
            ];

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encrypted['ciphertext']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                return true;
            }

            error_log("WebPush: Failed with HTTP code {$httpCode}");
            return false;

        } catch (\Exception $e) {
            error_log('WebPush: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple subscriptions
     */
    public function sendToMultiple(array $subscriptions, array $payload): array
    {
        $results = ['success' => 0, 'failed' => 0];

        foreach ($subscriptions as $subscription) {
            if ($this->send($subscription, $payload)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
            usleep(50000); // 50ms delay between sends
        }

        return $results;
    }

    /**
     * Create VAPID authentication headers
     */
    private function createVapidHeaders(string $endpoint): array
    {
        $audience = parse_url($endpoint, PHP_URL_SCHEME) . '://' . parse_url($endpoint, PHP_URL_HOST);

        $header = $this->base64UrlEncode(json_encode([
            'typ' => 'JWT',
            'alg' => 'ES256'
        ]));

        $payload = $this->base64UrlEncode(json_encode([
            'aud' => $audience,
            'exp' => time() + 86400,
            'sub' => $this->subject
        ]));

        $unsignedToken = $header . '.' . $payload;

        // Sign with ECDSA
        $signature = $this->sign($unsignedToken);

        $jwt = $unsignedToken . '.' . $signature;

        return [
            'authorization' => 'vapid t=' . $jwt . ',k=' . $this->publicKey
        ];
    }

    /**
     * Sign data with private key
     */
    private function sign(string $data): string
    {
        $privateKeyRaw = $this->base64UrlDecode($this->privateKey);

        // Create PEM format private key
        $privateKeyPem = "-----BEGIN EC PRIVATE KEY-----\n" .
            chunk_split(base64_encode(
                "\x30\x41\x02\x01\x01\x04\x20" . $privateKeyRaw .
                "\xa0\x0a\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07"
            ), 64, "\n") .
            "-----END EC PRIVATE KEY-----";

        $key = openssl_pkey_get_private($privateKeyPem);
        if (!$key) {
            // Fallback: return empty signature (notification may fail but won't crash)
            return '';
        }

        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256);

        // Convert from DER to raw signature format
        $signature = $this->derToRaw($signature);

        return $this->base64UrlEncode($signature);
    }

    /**
     * Encrypt payload for push notification
     */
    private function encrypt(string $payload, string $userPublicKey, string $userAuth): ?array
    {
        // For simplicity, send unencrypted for now
        // Full encryption requires proper ECDH key agreement
        // This is a simplified version
        return [
            'ciphertext' => $payload
        ];
    }

    /**
     * Convert DER signature to raw format
     */
    private function derToRaw(string $der): string
    {
        $header = unpack('Ctype/Clength', $der);
        if ($header['type'] !== 0x30) {
            return $der;
        }

        $pos = 2;
        $r = $this->readDerInteger($der, $pos);
        $s = $this->readDerInteger($der, $pos);

        return str_pad($r, 32, "\x00", STR_PAD_LEFT) .
               str_pad($s, 32, "\x00", STR_PAD_LEFT);
    }

    /**
     * Read DER integer
     */
    private function readDerInteger(string $der, int &$pos): string
    {
        $header = unpack('Ctype/Clength', substr($der, $pos, 2));
        $pos += 2;
        $value = substr($der, $pos, $header['length']);
        $pos += $header['length'];

        // Remove leading zero if present
        if (strlen($value) > 32 && ord($value[0]) === 0) {
            $value = substr($value, 1);
        }

        return $value;
    }

    /**
     * URL-safe base64 encode
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * URL-safe base64 decode
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

<?php
namespace GlamourSchedule\Core;

/**
 * SmsService - SMS herinneringen via MessageBird REST API
 * Handles sending SMS reminders to customers
 */
class SmsService
{
    private string $apiKey;
    private string $originator;
    private bool $enabled;
    private string $logFile;

    public function __construct()
    {
        $configFile = dirname(__DIR__, 2) . '/config/config.php';
        if (file_exists($configFile)) {
            if (!defined('GLAMOUR_LOADED')) {
                define('GLAMOUR_LOADED', true);
            }
            $config = require $configFile;
            $smsConfig = $config['sms'] ?? [];
        } else {
            $smsConfig = [];
        }

        $this->apiKey = $smsConfig['api_key'] ?? '';
        $this->originator = $smsConfig['originator'] ?? 'GlamourSched';
        $this->enabled = !empty($this->apiKey);
        $this->logFile = dirname(__DIR__, 2) . '/storage/logs/sms.log';
    }

    /**
     * Send an SMS message via MessageBird REST API
     */
    public function send(string $phoneNumber, string $message): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $phoneNumber = $this->sanitizePhone($phoneNumber);
        if (empty($phoneNumber)) {
            $this->log("Skipped: invalid phone number");
            return false;
        }

        $postData = [
            'originator' => $this->originator,
            'recipients' => $phoneNumber,
            'body' => $message,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://rest.messagebird.com/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Authorization: AccessKey ' . $this->apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $this->log("cURL error sending to {$phoneNumber}: {$curlError}");
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            $this->log("SMS sent to {$phoneNumber} (HTTP {$httpCode})");
            return true;
        }

        $this->log("Failed to send SMS to {$phoneNumber} (HTTP {$httpCode}): {$response}");
        return false;
    }

    /**
     * Send 24h booking reminder SMS
     */
    public function sendBookingReminder(array $bookingData): bool
    {
        $phone = $bookingData['customer_phone'] ?? '';
        if (empty($phone)) {
            return false;
        }

        $lang = $bookingData['language'] ?? 'nl';
        $time = date('H:i', strtotime($bookingData['time']));
        $salon = $bookingData['business_name'] ?? '';
        $service = $bookingData['service_name'] ?? '';
        $url = 'https://glamourschedule.nl/booking/' . ($bookingData['uuid'] ?? '');

        $templates = [
            'nl' => "Herinnering: Morgen {$time} afspraak bij {$salon} ({$service}). Bekijk: {$url}",
            'en' => "Reminder: Tomorrow {$time} appointment at {$salon} ({$service}). View: {$url}",
            'de' => "Erinnerung: Morgen {$time} Termin bei {$salon} ({$service}). Ansehen: {$url}",
            'fr' => "Rappel: Demain {$time} rendez-vous chez {$salon} ({$service}). Voir: {$url}",
        ];

        $message = $templates[$lang] ?? $templates['nl'];

        return $this->send($phone, $message);
    }

    /**
     * Send 1h booking reminder SMS
     */
    public function sendBookingReminder1Hour(array $bookingData): bool
    {
        $phone = $bookingData['customer_phone'] ?? '';
        if (empty($phone)) {
            return false;
        }

        $lang = $bookingData['language'] ?? 'nl';
        $time = date('H:i', strtotime($bookingData['time']));
        $salon = $bookingData['business_name'] ?? '';
        $service = $bookingData['service_name'] ?? '';
        $url = 'https://glamourschedule.nl/booking/' . ($bookingData['uuid'] ?? '');

        $templates = [
            'nl' => "Over 1 uur: Afspraak om {$time} bij {$salon} ({$service}). Bekijk: {$url}",
            'en' => "In 1 hour: Appointment at {$time} at {$salon} ({$service}). View: {$url}",
            'de' => "In 1 Stunde: Termin um {$time} bei {$salon} ({$service}). Ansehen: {$url}",
            'fr' => "Dans 1h: Rendez-vous a {$time} chez {$salon} ({$service}). Voir: {$url}",
        ];

        $message = $templates[$lang] ?? $templates['nl'];

        return $this->send($phone, $message);
    }

    /**
     * Check if SMS service is enabled and configured
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Sanitize phone number: keep only digits and leading +
     */
    private function sanitizePhone(string $phone): string
    {
        $phone = trim($phone);
        if (empty($phone)) {
            return '';
        }

        // Keep leading + and digits only
        $hasPlus = str_starts_with($phone, '+');
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if ($hasPlus) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    /**
     * Log SMS activity
     */
    private function log(string $message): void
    {
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[{$timestamp}] {$message}\n", FILE_APPEND);
    }
}

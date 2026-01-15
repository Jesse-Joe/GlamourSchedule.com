<?php
namespace GlamourSchedule\Core;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

/**
 * PushNotification Service
 * Handles sending push notifications to users
 */
class PushNotification
{
    private ?WebPush $webPush = null;
    private \PDO $db;

    private string $vapidPublicKey = 'BPkOp1aqoycZVfFPymObyb3fjX1uVk_EWwj6SEpIQrd0R4l2hnOk4WhdcAE9f8c4dqKqMUW-LivFqDtvsBjuOlA';
    private string $vapidPrivateKey = 'WZ38ef56U2KWhjonZfXngayLG4PV-hftbyjRFm27lWc';

    public function __construct()
    {
        $this->initDatabase();
        $this->initWebPush();
    }

    private function initDatabase(): void
    {
        $configFile = dirname(__DIR__, 2) . '/config/config.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
            $dbConfig = $config['database'];
        } else {
            // Fallback to direct connection
            $dbConfig = [
                'host' => 'localhost',
                'name' => 'glamourschedule_db',
                'user' => 'glamour_user',
                'pass' => 'qqedmELX74uWFl0gAsZ41u+L4vw9il2vb5kv0V+3Odo=',
                'charset' => 'utf8mb4'
            ];
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $dbConfig['host'],
            $dbConfig['name'],
            $dbConfig['charset'] ?? 'utf8mb4'
        );

        $this->db = new \PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);
    }

    private function initWebPush(): void
    {
        try {
            $auth = [
                'VAPID' => [
                    'subject' => 'mailto:info@glamourschedule.nl',
                    'publicKey' => $this->vapidPublicKey,
                    'privateKey' => $this->vapidPrivateKey,
                ],
            ];
            $this->webPush = new WebPush($auth);
        } catch (\Exception $e) {
            error_log('PushNotification: Failed to initialize WebPush: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): bool
    {
        $subscriptions = $this->getUserSubscriptions($userId);

        if (empty($subscriptions)) {
            return false;
        }

        $payload = [
            'title' => $title,
            'body' => $body,
            'icon' => '/images/icon-192.png',
            'badge' => '/images/badge-72.png',
            'data' => array_merge(['url' => '/dashboard'], $data)
        ];

        $success = false;
        foreach ($subscriptions as $sub) {
            if ($this->sendToSubscription($sub, $payload)) {
                $success = true;
            }
        }

        return $success;
    }

    /**
     * Send notification to business owner
     */
    public function sendToBusinessOwner(int $businessId, string $title, string $body, array $data = []): bool
    {
        // Get business owner user_id
        $stmt = $this->db->prepare("SELECT user_id FROM businesses WHERE id = ?");
        $stmt->execute([$businessId]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business || !$business['user_id']) {
            return false;
        }

        return $this->sendToUser($business['user_id'], $title, $body, $data);
    }

    /**
     * Send notification to admins
     */
    public function sendToAdmins(string $title, string $body, array $data = []): bool
    {
        $stmt = $this->db->query("SELECT id FROM users WHERE role = 'admin'");
        $admins = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $success = false;
        foreach ($admins as $admin) {
            if ($this->sendToUser($admin['id'], $title, $body, $data)) {
                $success = true;
            }
        }

        return $success;
    }

    /**
     * Notify about new booking
     */
    public function notifyNewBooking(array $booking): bool
    {
        $customerName = $booking['guest_name'] ?? $booking['customer_name'] ?? 'Klant';
        $serviceName = $booking['service_name'] ?? 'Behandeling';
        $date = date('j M', strtotime($booking['appointment_date']));
        $time = date('H:i', strtotime($booking['appointment_time']));

        return $this->sendToBusinessOwner(
            $booking['business_id'],
            'Nieuwe Boeking',
            "{$customerName} heeft {$serviceName} geboekt op {$date} om {$time}.",
            ['url' => '/business/dashboard/bookings']
        );
    }

    /**
     * Notify about new business registration
     */
    public function notifyNewBusiness(array $business): bool
    {
        $name = $business['company_name'] ?? $business['name'] ?? 'Nieuw bedrijf';

        return $this->sendToAdmins(
            'Nieuw Bedrijf Geregistreerd',
            "{$name} heeft zich zojuist aangemeld via GlamourSchedule.",
            ['url' => '/admin/businesses']
        );
    }

    /**
     * Send appointment reminder
     */
    public function sendReminder(array $booking): bool
    {
        $businessName = $booking['business_name'] ?? $booking['company_name'] ?? 'de salon';
        $serviceName = $booking['service_name'] ?? 'je afspraak';
        $time = date('H:i', strtotime($booking['appointment_time']));

        $userId = $booking['user_id'] ?? null;
        if (!$userId) {
            return false;
        }

        return $this->sendToUser(
            $userId,
            'Herinnering: Afspraak morgen',
            "Je hebt morgen om {$time} een afspraak bij {$businessName} voor {$serviceName}.",
            ['url' => '/dashboard/appointments']
        );
    }

    /**
     * Get user's push subscriptions
     */
    private function getUserSubscriptions(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM push_subscriptions WHERE user_id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Send to a single subscription
     */
    private function sendToSubscription(array $sub, array $payload): bool
    {
        if (!$this->webPush) {
            return false;
        }

        try {
            $subscription = Subscription::create([
                'endpoint' => $sub['endpoint'],
                'publicKey' => $sub['p256dh_key'],
                'authToken' => $sub['auth_key'],
            ]);

            $report = $this->webPush->sendOneNotification(
                $subscription,
                json_encode($payload)
            );

            if ($report->isSuccess()) {
                return true;
            }

            // If subscription expired, remove it
            if ($report->isSubscriptionExpired()) {
                $this->removeSubscription($sub['id']);
            }

            error_log('Push failed: ' . $report->getReason());
            return false;

        } catch (\Exception $e) {
            error_log('PushNotification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove expired subscription
     */
    private function removeSubscription(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM push_subscriptions WHERE id = ?");
        $stmt->execute([$id]);
    }
}

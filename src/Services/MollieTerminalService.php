<?php
namespace GlamourSchedule\Services;

use Mollie\Api\MollieApiClient;
use GlamourSchedule\Core\Database;

/**
 * Mollie Terminal Service
 * Handles all Mollie Terminal/POS operations
 */
class MollieTerminalService
{
    private MollieApiClient $mollie;
    private Database $db;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->db = new Database($config['database']);
        $this->mollie = new MollieApiClient();
        $this->mollie->setApiKey($config['mollie']['api_key'] ?? '');
    }

    /**
     * List all terminals from Mollie API
     */
    public function listTerminals(): array
    {
        try {
            $terminals = $this->mollie->terminals->page();
            $result = [];

            foreach ($terminals as $terminal) {
                $result[] = [
                    'id' => $terminal->id,
                    'description' => $terminal->description ?? 'Terminal',
                    'status' => $terminal->status,
                    'brand' => $terminal->brand ?? 'Unknown',
                    'model' => $terminal->model ?? 'Unknown',
                    'serial_number' => $terminal->serialNumber ?? null,
                    'currency' => $terminal->currency ?? 'EUR',
                    'profile_id' => $terminal->profileId ?? null,
                    'created_at' => $terminal->createdAt ?? null
                ];
            }

            return $result;
        } catch (\Exception $e) {
            error_log("Mollie list terminals error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a specific terminal
     */
    public function getTerminal(string $terminalId): ?array
    {
        try {
            $terminal = $this->mollie->terminals->get($terminalId);

            return [
                'id' => $terminal->id,
                'description' => $terminal->description ?? 'Terminal',
                'status' => $terminal->status,
                'brand' => $terminal->brand ?? 'Unknown',
                'model' => $terminal->model ?? 'Unknown',
                'serial_number' => $terminal->serialNumber ?? null,
                'currency' => $terminal->currency ?? 'EUR',
                'profile_id' => $terminal->profileId ?? null,
                'created_at' => $terminal->createdAt ?? null
            ];
        } catch (\Exception $e) {
            error_log("Mollie get terminal error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a point-of-sale payment on a terminal
     */
    public function createTerminalPayment(array $data): array
    {
        try {
            $payment = $this->mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format((float)$data['amount'], 2, '.', '')
                ],
                'description' => $data['description'] ?? 'POS Payment',
                'method' => 'pointofsale',
                'terminalId' => $data['terminal_id'],
                'webhookUrl' => $data['webhook_url'] ?? 'https://glamourschedule.nl/api/webhooks/mollie-terminal',
                'metadata' => $data['metadata'] ?? []
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount->value,
                'description' => $payment->description
            ];
        } catch (\Exception $e) {
            error_log("Mollie terminal payment error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentId): ?array
    {
        try {
            $payment = $this->mollie->payments->get($paymentId);

            return [
                'id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount->value,
                'currency' => $payment->amount->currency,
                'description' => $payment->description,
                'is_paid' => $payment->isPaid(),
                'is_pending' => $payment->isPending(),
                'is_open' => $payment->isOpen(),
                'is_failed' => $payment->isFailed(),
                'is_canceled' => $payment->isCanceled(),
                'is_expired' => $payment->isExpired(),
                'paid_at' => $payment->paidAt ?? null,
                'method' => $payment->method ?? null
            ];
        } catch (\Exception $e) {
            error_log("Mollie get payment error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancel a terminal payment
     */
    public function cancelPayment(string $paymentId): bool
    {
        try {
            $this->mollie->payments->cancel($paymentId);
            return true;
        } catch (\Exception $e) {
            error_log("Mollie cancel payment error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Link terminal to a business
     */
    public function linkTerminalToBusiness(int $businessId, string $terminalId, string $terminalName = null): bool
    {
        try {
            // Check if terminal exists in Mollie
            $terminal = $this->getTerminal($terminalId);
            if (!$terminal) {
                return false;
            }

            // Check if already linked
            $stmt = $this->db->query(
                "SELECT id FROM business_terminals WHERE terminal_id = ?",
                [$terminalId]
            );

            if ($stmt->fetch()) {
                // Update existing link
                $this->db->query(
                    "UPDATE business_terminals
                     SET business_id = ?, terminal_name = ?, status = 'active', updated_at = NOW()
                     WHERE terminal_id = ?",
                    [$businessId, $terminalName ?? $terminal['description'], $terminalId]
                );
            } else {
                // Create new link
                $this->db->query(
                    "INSERT INTO business_terminals (business_id, terminal_id, terminal_name, terminal_brand, terminal_model, status, created_at)
                     VALUES (?, ?, ?, ?, ?, 'active', NOW())",
                    [$businessId, $terminalId, $terminalName ?? $terminal['description'], $terminal['brand'], $terminal['model']]
                );
            }

            return true;
        } catch (\Exception $e) {
            error_log("Link terminal error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unlink terminal from business
     */
    public function unlinkTerminal(int $businessId, string $terminalId): bool
    {
        try {
            $this->db->query(
                "UPDATE business_terminals SET status = 'inactive', updated_at = NOW()
                 WHERE business_id = ? AND terminal_id = ?",
                [$businessId, $terminalId]
            );
            return true;
        } catch (\Exception $e) {
            error_log("Unlink terminal error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get terminals linked to a business
     */
    public function getBusinessTerminals(int $businessId): array
    {
        try {
            $stmt = $this->db->query(
                "SELECT * FROM business_terminals
                 WHERE business_id = ? AND status = 'active'
                 ORDER BY terminal_name",
                [$businessId]
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            error_log("Get business terminals error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Log terminal transaction
     */
    public function logTransaction(array $data): int
    {
        try {
            $this->db->query(
                "INSERT INTO terminal_transactions
                 (business_id, terminal_id, payment_id, amount, currency, description, status, metadata, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [
                    $data['business_id'],
                    $data['terminal_id'],
                    $data['payment_id'],
                    $data['amount'],
                    $data['currency'] ?? 'EUR',
                    $data['description'] ?? '',
                    $data['status'] ?? 'pending',
                    json_encode($data['metadata'] ?? [])
                ]
            );
            return (int)$this->db->lastInsertId();
        } catch (\Exception $e) {
            error_log("Log transaction error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update transaction status
     */
    public function updateTransactionStatus(string $paymentId, string $status, ?string $paidAt = null): bool
    {
        try {
            $sql = "UPDATE terminal_transactions SET status = ?, updated_at = NOW()";
            $params = [$status];

            if ($paidAt) {
                $sql .= ", paid_at = ?";
                $params[] = $paidAt;
            }

            $sql .= " WHERE payment_id = ?";
            $params[] = $paymentId;

            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            error_log("Update transaction status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get transaction history for a business
     */
    public function getTransactionHistory(int $businessId, int $limit = 50, int $offset = 0): array
    {
        try {
            $stmt = $this->db->query(
                "SELECT tt.*, bt.terminal_name
                 FROM terminal_transactions tt
                 LEFT JOIN business_terminals bt ON tt.terminal_id = bt.terminal_id
                 WHERE tt.business_id = ?
                 ORDER BY tt.created_at DESC
                 LIMIT ? OFFSET ?",
                [$businessId, $limit, $offset]
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            error_log("Get transaction history error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get daily totals for a business
     */
    public function getDailyTotals(int $businessId, string $date = null): array
    {
        $date = $date ?? date('Y-m-d');

        try {
            $stmt = $this->db->query(
                "SELECT
                    COUNT(*) as transaction_count,
                    SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_paid,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                    SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled_count
                 FROM terminal_transactions
                 WHERE business_id = ? AND DATE(created_at) = ?",
                [$businessId, $date]
            );
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [
                'transaction_count' => 0,
                'total_paid' => 0,
                'paid_count' => 0,
                'failed_count' => 0,
                'canceled_count' => 0
            ];
        } catch (\Exception $e) {
            error_log("Get daily totals error: " . $e->getMessage());
            return [];
        }
    }
}

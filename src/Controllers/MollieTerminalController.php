<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Services\MollieTerminalService;

/**
 * Mollie Terminal Controller
 * Handles terminal management and POS payments for businesses
 */
class MollieTerminalController extends Controller
{
    private MollieTerminalService $terminalService;

    public function __construct()
    {
        parent::__construct();
        $this->terminalService = new MollieTerminalService($this->config);
    }

    /**
     * Show terminal management page
     */
    public function index(): string
    {
        $business = $this->requireBusinessAuth();
        if (!$business) {
            return $this->redirect('/login');
        }

        // Get linked terminals
        $linkedTerminals = $this->terminalService->getBusinessTerminals($business['id']);

        // Get available terminals from Mollie
        $availableTerminals = $this->terminalService->listTerminals();

        // Filter out already linked terminals
        $linkedIds = array_column($linkedTerminals, 'terminal_id');
        $unlinkedTerminals = array_filter($availableTerminals, function($t) use ($linkedIds) {
            return !in_array($t['id'], $linkedIds);
        });

        // Get today's stats
        $todayStats = $this->terminalService->getDailyTotals($business['id']);

        // Get recent transactions
        $recentTransactions = $this->terminalService->getTransactionHistory($business['id'], 10);

        return $this->view('pages/business/dashboard/terminals', [
            'pageTitle' => $this->translations['terminals'] ?? 'PIN Terminals',
            'business' => $business,
            'linkedTerminals' => $linkedTerminals,
            'unlinkedTerminals' => array_values($unlinkedTerminals),
            'todayStats' => $todayStats,
            'recentTransactions' => $recentTransactions,
            'csrfToken' => $this->csrf()
        ], 'business');
    }

    /**
     * Link a terminal to the business
     */
    public function linkTerminal(): string
    {
        $business = $this->requireBusinessAuth();
        if (!$business) {
            return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        if (!$this->verifyCsrf()) {
            return $this->json(['success' => false, 'error' => 'Invalid session'], 403);
        }

        $terminalId = $_POST['terminal_id'] ?? '';
        $terminalName = $_POST['terminal_name'] ?? '';

        if (empty($terminalId)) {
            return $this->json(['success' => false, 'error' => 'Terminal ID is required']);
        }

        $success = $this->terminalService->linkTerminalToBusiness(
            $business['id'],
            $terminalId,
            $terminalName ?: null
        );

        if ($success) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => $this->translations['terminal_linked'] ?? 'Terminal successfully linked'];
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'error' => $this->translations['terminal_link_failed'] ?? 'Failed to link terminal']);
    }

    /**
     * Unlink a terminal
     */
    public function unlinkTerminal(): string
    {
        $business = $this->requireBusinessAuth();
        if (!$business) {
            return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        if (!$this->verifyCsrf()) {
            return $this->json(['success' => false, 'error' => 'Invalid session'], 403);
        }

        $terminalId = $_POST['terminal_id'] ?? '';

        if (empty($terminalId)) {
            return $this->json(['success' => false, 'error' => 'Terminal ID is required']);
        }

        $success = $this->terminalService->unlinkTerminal($business['id'], $terminalId);

        if ($success) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => $this->translations['terminal_unlinked'] ?? 'Terminal removed'];
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'error' => $this->translations['terminal_unlink_failed'] ?? 'Failed to remove terminal']);
    }

    /**
     * Create a payment on a terminal
     */
    public function createPayment(): string
    {
        $business = $this->requireBusinessAuth();
        if (!$business) {
            return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $terminalId = $_POST['terminal_id'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $description = $_POST['description'] ?? '';
        $bookingId = $_POST['booking_id'] ?? null;
        $posBookingUuid = $_POST['pos_booking_uuid'] ?? null;

        if (empty($terminalId) || $amount <= 0) {
            return $this->json(['success' => false, 'error' => 'Terminal and amount are required']);
        }

        // Verify terminal belongs to business
        $terminals = $this->terminalService->getBusinessTerminals($business['id']);
        $terminalIds = array_column($terminals, 'terminal_id');

        if (!in_array($terminalId, $terminalIds)) {
            return $this->json(['success' => false, 'error' => 'Terminal not linked to your business']);
        }

        // Create metadata
        $metadata = [
            'type' => 'terminal_payment',
            'business_id' => $business['id'],
            'business_name' => $business['company_name']
        ];

        if ($bookingId) {
            $metadata['booking_id'] = $bookingId;
        }
        if ($posBookingUuid) {
            $metadata['pos_booking_uuid'] = $posBookingUuid;
        }

        // Create payment
        $result = $this->terminalService->createTerminalPayment([
            'terminal_id' => $terminalId,
            'amount' => $amount,
            'description' => $description ?: "Betaling - {$business['company_name']}",
            'webhook_url' => 'https://glamourschedule.nl/api/webhooks/mollie-terminal',
            'metadata' => $metadata
        ]);

        if ($result['success']) {
            // Log transaction
            $this->terminalService->logTransaction([
                'business_id' => $business['id'],
                'terminal_id' => $terminalId,
                'payment_id' => $result['payment_id'],
                'amount' => $amount,
                'description' => $description,
                'status' => 'pending',
                'metadata' => $metadata
            ]);

            return $this->json([
                'success' => true,
                'payment_id' => $result['payment_id'],
                'status' => $result['status'],
                'message' => $this->translations['payment_started'] ?? 'Payment started on terminal'
            ]);
        }

        return $this->json(['success' => false, 'error' => $result['error'] ?? 'Payment failed']);
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(): string
    {
        $business = $this->requireBusinessAuth();
        if (!$business) {
            return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $paymentId = $_GET['payment_id'] ?? '';

        if (empty($paymentId)) {
            return $this->json(['success' => false, 'error' => 'Payment ID required']);
        }

        $status = $this->terminalService->getPaymentStatus($paymentId);

        if ($status) {
            return $this->json([
                'success' => true,
                'payment' => $status
            ]);
        }

        return $this->json(['success' => false, 'error' => 'Payment not found']);
    }

    /**
     * Cancel a pending payment
     */
    public function cancelPayment(): string
    {
        $business = $this->requireBusinessAuth();
        if (!$business) {
            return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $paymentId = $_POST['payment_id'] ?? '';

        if (empty($paymentId)) {
            return $this->json(['success' => false, 'error' => 'Payment ID required']);
        }

        $success = $this->terminalService->cancelPayment($paymentId);

        if ($success) {
            $this->terminalService->updateTransactionStatus($paymentId, 'canceled');
            return $this->json(['success' => true, 'message' => 'Payment canceled']);
        }

        return $this->json(['success' => false, 'error' => 'Could not cancel payment']);
    }

    /**
     * Get transaction history (AJAX)
     */
    public function getTransactions(): string
    {
        $business = $this->requireBusinessAuth();
        if (!$business) {
            return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $limit = (int)($_GET['limit'] ?? 50);
        $offset = (int)($_GET['offset'] ?? 0);

        $transactions = $this->terminalService->getTransactionHistory($business['id'], $limit, $offset);
        $todayStats = $this->terminalService->getDailyTotals($business['id']);

        return $this->json([
            'success' => true,
            'transactions' => $transactions,
            'stats' => $todayStats
        ]);
    }

    /**
     * Handle Mollie terminal webhook
     */
    public function webhook(): string
    {
        $paymentId = $_POST['id'] ?? '';

        if (empty($paymentId)) {
            http_response_code(400);
            return 'Missing payment ID';
        }

        $status = $this->terminalService->getPaymentStatus($paymentId);

        if (!$status) {
            http_response_code(404);
            return 'Payment not found';
        }

        // Update transaction status
        $newStatus = 'pending';
        if ($status['is_paid']) {
            $newStatus = 'paid';
        } elseif ($status['is_failed']) {
            $newStatus = 'failed';
        } elseif ($status['is_canceled']) {
            $newStatus = 'canceled';
        } elseif ($status['is_expired']) {
            $newStatus = 'expired';
        }

        $this->terminalService->updateTransactionStatus(
            $paymentId,
            $newStatus,
            $status['paid_at'] ?? null
        );

        // If this is linked to a POS booking, update that too
        // This is handled by the metadata in the webhook

        http_response_code(200);
        return 'OK';
    }

    /**
     * Require business authentication
     */
    private function requireBusinessAuth(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $stmt = $this->db->query(
            "SELECT b.* FROM businesses b
             JOIN users u ON b.user_id = u.id
             WHERE u.id = ? AND b.status = 'active'",
            [$_SESSION['user_id']]
        );

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}

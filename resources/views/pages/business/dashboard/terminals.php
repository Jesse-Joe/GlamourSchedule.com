<?php ob_start(); ?>

<div class="dashboard-header">
    <div>
        <h1><i class="fas fa-credit-card"></i> <?= $__('terminals') ?></h1>
        <p class="text-muted"><?= $__('terminals_desc') ?></p>
    </div>
</div>

<!-- Today's Stats -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-receipt"></i></div>
        <div class="stat-content">
            <span class="stat-value" id="statTransactions"><?= $todayStats['transaction_count'] ?? 0 ?></span>
            <span class="stat-label"><?= $__('transactions_today') ?></span>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-content">
            <span class="stat-value" id="statPaid">&euro;<?= number_format($todayStats['total_paid'] ?? 0, 2, ',', '.') ?></span>
            <span class="stat-label"><?= $__('total_received') ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-content">
            <span class="stat-value" id="statPaidCount"><?= $todayStats['paid_count'] ?? 0 ?></span>
            <span class="stat-label"><?= $__('successful') ?></span>
        </div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="stat-content">
            <span class="stat-value" id="statFailed"><?= ($todayStats['failed_count'] ?? 0) + ($todayStats['canceled_count'] ?? 0) ?></span>
            <span class="stat-label"><?= $__('failed_canceled') ?></span>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Linked Terminals -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-link"></i> <?= $__('linked_terminals') ?></h3>
        </div>
        <div class="card-body">
            <?php if (empty($linkedTerminals)): ?>
                <div class="empty-state">
                    <i class="fas fa-credit-card"></i>
                    <p><?= $__('no_terminals_linked') ?></p>
                    <p class="text-muted"><?= $__('link_terminal_hint') ?></p>
                </div>
            <?php else: ?>
                <div class="terminal-list">
                    <?php foreach ($linkedTerminals as $terminal): ?>
                        <div class="terminal-item" data-terminal-id="<?= htmlspecialchars($terminal['terminal_id']) ?>">
                            <div class="terminal-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="terminal-info">
                                <h4><?= htmlspecialchars($terminal['terminal_name']) ?></h4>
                                <p class="text-muted">
                                    <?= htmlspecialchars($terminal['terminal_brand']) ?> <?= htmlspecialchars($terminal['terminal_model']) ?>
                                    <br>
                                    <small>ID: <?= htmlspecialchars($terminal['terminal_id']) ?></small>
                                </p>
                            </div>
                            <div class="terminal-status">
                                <span class="badge badge-success"><?= $__('active') ?></span>
                            </div>
                            <div class="terminal-actions">
                                <button class="btn btn-sm btn-primary" onclick="showPaymentModal('<?= htmlspecialchars($terminal['terminal_id']) ?>', '<?= htmlspecialchars($terminal['terminal_name']) ?>')">
                                    <i class="fas fa-euro-sign"></i> <?= $__('new_payment') ?>
                                </button>
                                <button class="btn btn-sm btn-outline" onclick="unlinkTerminal('<?= htmlspecialchars($terminal['terminal_id']) ?>')">
                                    <i class="fas fa-unlink"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Available Terminals -->
    <?php if (!empty($unlinkedTerminals)): ?>
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-plus-circle"></i> <?= $__('available_terminals') ?></h3>
        </div>
        <div class="card-body">
            <div class="terminal-list">
                <?php foreach ($unlinkedTerminals as $terminal): ?>
                    <div class="terminal-item available">
                        <div class="terminal-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="terminal-info">
                            <h4><?= htmlspecialchars($terminal['description']) ?></h4>
                            <p class="text-muted">
                                <?= htmlspecialchars($terminal['brand']) ?> <?= htmlspecialchars($terminal['model']) ?>
                                <br>
                                <small>ID: <?= htmlspecialchars($terminal['id']) ?></small>
                            </p>
                        </div>
                        <div class="terminal-status">
                            <span class="badge badge-<?= $terminal['status'] === 'active' ? 'success' : 'warning' ?>">
                                <?= ucfirst($terminal['status']) ?>
                            </span>
                        </div>
                        <div class="terminal-actions">
                            <button class="btn btn-sm btn-success" onclick="linkTerminal('<?= htmlspecialchars($terminal['id']) ?>', '<?= htmlspecialchars($terminal['description']) ?>')">
                                <i class="fas fa-link"></i> <?= $__('link') ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Transactions -->
<div class="dashboard-card mt-4">
    <div class="card-header">
        <h3><i class="fas fa-history"></i> <?= $__('recent_transactions') ?></h3>
        <button class="btn btn-sm btn-outline" onclick="refreshTransactions()">
            <i class="fas fa-sync"></i>
        </button>
    </div>
    <div class="card-body">
        <?php if (empty($recentTransactions)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <p><?= $__('no_transactions') ?></p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table" id="transactionsTable">
                    <thead>
                        <tr>
                            <th><?= $__('date') ?></th>
                            <th><?= $__('terminal') ?></th>
                            <th><?= $__('description') ?></th>
                            <th><?= $__('amount') ?></th>
                            <th><?= $__('status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTransactions as $tx): ?>
                            <tr>
                                <td><?= date('d-m-Y H:i', strtotime($tx['created_at'])) ?></td>
                                <td><?= htmlspecialchars($tx['terminal_name'] ?? $tx['terminal_id']) ?></td>
                                <td><?= htmlspecialchars($tx['description']) ?></td>
                                <td>&euro;<?= number_format($tx['amount'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge badge-<?= $tx['status'] === 'paid' ? 'success' : ($tx['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($tx['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal" id="paymentModal">
    <div class="modal-overlay" onclick="closePaymentModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-credit-card"></i> <?= $__('new_terminal_payment') ?></h3>
            <button class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="paymentForm" onsubmit="return processPayment(event)">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="terminal_id" id="paymentTerminalId">

                <div class="form-group">
                    <label><?= $__('terminal') ?></label>
                    <input type="text" id="paymentTerminalName" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label><?= $__('amount') ?> *</label>
                    <div class="input-group">
                        <span class="input-prefix">&euro;</span>
                        <input type="number" name="amount" id="paymentAmount" class="form-control" step="0.01" min="0.01" required placeholder="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label><?= $__('description') ?></label>
                    <input type="text" name="description" id="paymentDescription" class="form-control" placeholder="<?= $__('optional') ?>">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closePaymentModal()"><?= $__('cancel') ?></button>
                    <button type="submit" class="btn btn-primary" id="startPaymentBtn">
                        <i class="fas fa-play"></i> <?= $__('start_payment') ?>
                    </button>
                </div>
            </form>

            <!-- Payment Status -->
            <div id="paymentStatus" style="display:none;">
                <div class="payment-status-content">
                    <div class="status-icon pending">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <h4 id="paymentStatusTitle"><?= $__('waiting_for_payment') ?></h4>
                    <p class="text-muted" id="paymentStatusMessage"><?= $__('customer_should_tap') ?></p>
                    <div class="payment-amount" id="paymentStatusAmount"></div>

                    <div class="payment-actions mt-4">
                        <button class="btn btn-outline" onclick="cancelCurrentPayment()">
                            <i class="fas fa-times"></i> <?= $__('cancel_payment') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--card-bg, #111);
    border: 1px solid var(--border-color, #222);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card.success { border-left: 3px solid #22c55e; }
.stat-card.warning { border-left: 3px solid #f59e0b; }

.stat-icon {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--text-muted);
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-color);
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-muted);
}

.terminal-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.terminal-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--border-color, #222);
    border-radius: 12px;
    transition: all 0.2s;
}

.terminal-item:hover {
    border-color: var(--primary-color, #fff);
    background: rgba(255,255,255,0.05);
}

.terminal-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #fff;
}

.terminal-info {
    flex: 1;
}

.terminal-info h4 {
    margin: 0 0 0.25rem;
    font-size: 1rem;
    font-weight: 600;
}

.terminal-info p {
    margin: 0;
    font-size: 0.875rem;
}

.terminal-actions {
    display: flex;
    gap: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(4px);
}

.modal-content {
    position: relative;
    background: var(--card-bg, #111);
    border: 1px solid var(--border-color, #222);
    border-radius: 16px;
    width: 90%;
    max-width: 450px;
    max-height: 90vh;
    overflow: auto;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem;
    border-bottom: 1px solid var(--border-color, #222);
}

.modal-header h3 {
    margin: 0;
    font-size: 1.125rem;
}

.modal-close {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.modal-body {
    padding: 1.5rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.form-actions .btn {
    flex: 1;
}

.input-group {
    display: flex;
    align-items: center;
}

.input-prefix {
    padding: 0.75rem 1rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--border-color, #333);
    border-right: none;
    border-radius: 8px 0 0 8px;
    color: var(--text-muted);
}

.input-group .form-control {
    border-radius: 0 8px 8px 0;
}

.payment-status-content {
    text-align: center;
    padding: 2rem 1rem;
}

.status-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.status-icon.pending {
    background: rgba(251, 191, 36, 0.1);
    color: #fbbf24;
}

.status-icon.success {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}

.status-icon.failed {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.payment-amount {
    font-size: 2rem;
    font-weight: 700;
    margin-top: 1rem;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
.badge-warning { background: rgba(251, 191, 36, 0.1); color: #fbbf24; }
.badge-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color, #222);
}

.table th {
    font-weight: 600;
    color: var(--text-muted);
    font-size: 0.875rem;
}

.mt-4 { margin-top: 1.5rem; }
</style>

<script>
const csrfToken = '<?= $csrfToken ?>';
let currentPaymentId = null;
let paymentCheckInterval = null;

function showPaymentModal(terminalId, terminalName) {
    document.getElementById('paymentTerminalId').value = terminalId;
    document.getElementById('paymentTerminalName').value = terminalName;
    document.getElementById('paymentAmount').value = '';
    document.getElementById('paymentDescription').value = '';
    document.getElementById('paymentForm').style.display = 'block';
    document.getElementById('paymentStatus').style.display = 'none';
    document.getElementById('paymentModal').classList.add('active');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.remove('active');
    if (paymentCheckInterval) {
        clearInterval(paymentCheckInterval);
        paymentCheckInterval = null;
    }
}

async function processPayment(e) {
    e.preventDefault();

    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);

    const btn = document.getElementById('startPaymentBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';

    try {
        const response = await fetch('/business/terminals/payment', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            currentPaymentId = data.payment_id;
            showPaymentStatus(formData.get('amount'));
            startPaymentCheck();
        } else {
            alert(data.error || <?= json_encode($__('terminal_payment_failed_start')) ?>);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play"></i> <?= $__('start_payment') ?>';
        }
    } catch (error) {
        console.error('Payment error:', error);
        alert(<?= json_encode($__('terminal_payment_failed_start')) ?>);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> <?= $__('start_payment') ?>';
    }
}

function showPaymentStatus(amount) {
    document.getElementById('paymentForm').style.display = 'none';
    document.getElementById('paymentStatus').style.display = 'block';
    document.getElementById('paymentStatusAmount').textContent = '€' + parseFloat(amount).toFixed(2).replace('.', ',');
}

function startPaymentCheck() {
    paymentCheckInterval = setInterval(async () => {
        try {
            const response = await fetch(`/business/terminals/payment/status?payment_id=${currentPaymentId}`);
            const data = await response.json();

            if (data.success && data.payment) {
                const payment = data.payment;

                if (payment.is_paid) {
                    clearInterval(paymentCheckInterval);
                    showPaymentSuccess();
                } else if (payment.is_failed || payment.is_canceled || payment.is_expired) {
                    clearInterval(paymentCheckInterval);
                    showPaymentFailed(payment.status);
                }
            }
        } catch (error) {
            console.error('Status check error:', error);
        }
    }, 2000);
}

function showPaymentSuccess() {
    document.querySelector('.status-icon').className = 'status-icon success';
    document.querySelector('.status-icon i').className = 'fas fa-check';
    document.getElementById('paymentStatusTitle').textContent = '<?= $__('payment_successful') ?>';
    document.getElementById('paymentStatusMessage').textContent = '<?= $__('payment_received') ?>';
    document.querySelector('.payment-actions').innerHTML = `
        <button class="btn btn-primary" onclick="closePaymentModal(); refreshTransactions();">
            <i class="fas fa-check"></i> <?= $__('done') ?>
        </button>
    `;
}

function showPaymentFailed(status) {
    document.querySelector('.status-icon').className = 'status-icon failed';
    document.querySelector('.status-icon i').className = 'fas fa-times';
    document.getElementById('paymentStatusTitle').textContent = '<?= $__('payment_failed') ?>';
    document.getElementById('paymentStatusMessage').textContent = status === 'canceled' ? '<?= $__('payment_canceled') ?>' : '<?= $__('payment_not_completed') ?>';
    document.querySelector('.payment-actions').innerHTML = `
        <button class="btn btn-outline" onclick="closePaymentModal();">
            <?= $__('close') ?>
        </button>
        <button class="btn btn-primary" onclick="resetPaymentForm();">
            <i class="fas fa-redo"></i> <?= $__('try_again') ?>
        </button>
    `;
}

function resetPaymentForm() {
    document.getElementById('paymentForm').style.display = 'block';
    document.getElementById('paymentStatus').style.display = 'none';
    document.getElementById('startPaymentBtn').disabled = false;
    document.getElementById('startPaymentBtn').innerHTML = '<i class="fas fa-play"></i> <?= $__('start_payment') ?>';
    document.querySelector('.status-icon').className = 'status-icon pending';
    document.querySelector('.status-icon i').className = 'fas fa-spinner fa-spin';
}

async function cancelCurrentPayment() {
    if (!currentPaymentId) return;

    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('payment_id', currentPaymentId);

    try {
        const response = await fetch('/business/terminals/payment/cancel', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            clearInterval(paymentCheckInterval);
            showPaymentFailed('canceled');
        }
    } catch (error) {
        console.error('Cancel error:', error);
    }
}

async function linkTerminal(terminalId, terminalName) {
    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('terminal_id', terminalId);
    formData.append('terminal_name', terminalName);

    try {
        const response = await fetch('/business/terminals/link', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || <?= json_encode($__('terminal_link_failed')) ?>);
        }
    } catch (error) {
        console.error('Link error:', error);
        alert(<?= json_encode($__('terminal_link_failed')) ?>);
    }
}

async function unlinkTerminal(terminalId) {
    if (!confirm('<?= $__('confirm_unlink') ?>')) {
        return;
    }

    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('terminal_id', terminalId);

    try {
        const response = await fetch('/business/terminals/unlink', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || <?= json_encode($__('terminal_unlink_failed')) ?>);
        }
    } catch (error) {
        console.error('Unlink error:', error);
        alert(<?= json_encode($__('terminal_unlink_failed')) ?>);
    }
}

async function refreshTransactions() {
    try {
        const response = await fetch('/business/terminals/transactions');
        const data = await response.json();

        if (data.success) {
            // Update stats
            document.getElementById('statTransactions').textContent = data.stats.transaction_count;
            document.getElementById('statPaid').textContent = '€' + parseFloat(data.stats.total_paid).toFixed(2).replace('.', ',');
            document.getElementById('statPaidCount').textContent = data.stats.paid_count;
            document.getElementById('statFailed').textContent = parseInt(data.stats.failed_count) + parseInt(data.stats.canceled_count);

            // Reload page for full refresh
            location.reload();
        }
    } catch (error) {
        console.error('Refresh error:', error);
    }
}
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/resources/views/layouts/business.php';
?>

<?php ob_start(); ?>

<div class="grid grid-2">
    <!-- Pending Balance -->
    <div class="card" style="background:linear-gradient(135deg,var(--success),#000000);color:white">
        <div style="display:flex;align-items:center;gap:1rem">
            <div style="width:60px;height:60px;background:rgba(255,255,255,0.2);border-radius:15px;display:flex;align-items:center;justify-content:center">
                <i class="fas fa-wallet" style="font-size:1.5rem"></i>
            </div>
            <div>
                <p style="opacity:0.9;margin:0"><?= $__('payout_pending_balance') ?></p>
                <h2 style="margin:0.25rem 0 0 0;font-size:2rem">&euro;<?= number_format($pendingAmount ?? 0, 2, ',', '.') ?></h2>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card">
        <div style="display:flex;align-items:center;gap:1rem">
            <div style="width:60px;height:60px;background:var(--secondary);border-radius:15px;display:flex;align-items:center;justify-content:center">
                <i class="fas fa-info-circle" style="font-size:1.5rem;color:var(--primary)"></i>
            </div>
            <div>
                <h4 style="margin:0"><?= $__('payout_title') ?></h4>
                <p class="text-muted" style="margin:0.25rem 0 0 0;font-size:0.9rem"><?= $__('payout_info_desc') ?></p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <!-- Payout History -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history"></i> <?= $__('payout_history') ?></h3>
        </div>

        <?php if (empty($payouts)): ?>
            <div class="text-center" style="padding:3rem">
                <i class="fas fa-money-bill-wave" style="font-size:4rem;color:var(--border);margin-bottom:1rem"></i>
                <h4><?= $__('payout_no_payouts') ?></h4>
                <p class="text-muted"><?= $__('payout_history_hint') ?></p>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border)">
                            <th style="text-align:left;padding:0.75rem 0;font-weight:600"><?= $__('date') ?></th>
                            <th style="text-align:left;padding:0.75rem 0;font-weight:600"><?= $__('amount') ?></th>
                            <th style="text-align:left;padding:0.75rem 0;font-weight:600"><?= $__('status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payouts as $payout): ?>
                            <tr style="border-bottom:1px solid var(--border)">
                                <td style="padding:0.75rem 0">
                                    <?= !empty($payout['created_at']) ? date('d-m-Y', strtotime($payout['created_at'])) : '-' ?>
                                </td>
                                <td style="padding:0.75rem 0;font-weight:600">
                                    &euro;<?= number_format($payout['amount'] ?? 0, 2, ',', '.') ?>
                                </td>
                                <td style="padding:0.75rem 0">
                                    <?php
                                    $status = $payout['status'] ?? 'pending';
                                    $statusColors = ['pending' => 'var(--warning)', 'completed' => 'var(--success)', 'failed' => 'var(--danger)'];
                                    $statusLabels = ['pending' => $__('payout_status_pending'), 'completed' => $__('payout_status_completed'), 'failed' => $__('payout_status_failed')];
                                    ?>
                                    <span style="background:<?= $statusColors[$status] ?? 'var(--warning)' ?>;color:white;padding:0.25rem 0.75rem;border-radius:15px;font-size:0.75rem">
                                        <?= $statusLabels[$status] ?? $__('unknown') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Payout Info -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-university"></i> <?= $__('payout_bank_details') ?></h3>
            </div>

            <div style="padding:1rem;background:var(--secondary);border-radius:10px">
                <p class="text-muted" style="font-size:0.85rem;margin:0">IBAN</p>
                <p style="font-family:monospace;font-size:1.1rem;margin:0.25rem 0 0 0">
                    <?= htmlspecialchars($business['iban'] ?? $__('not_specified')) ?>
                </p>
                <?php if (!empty($business['account_holder'])): ?>
                <p class="text-muted" style="font-size:0.85rem;margin:0.5rem 0 0 0">
                    <?= $__('payout_account_holder') ?>: <?= htmlspecialchars($business['account_holder']) ?>
                </p>
                <?php endif; ?>
            </div>

            <?php if (empty($business['iban'])): ?>
                <div class="alert alert-warning" style="margin-top:1rem">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?= $__('payout_add_iban_warning') ?></span>
                </div>
                <a href="/business/change-iban" class="btn btn-primary" style="margin-top:0.5rem">
                    <i class="fas fa-plus"></i> <?= $__('payout_add_iban') ?>
                </a>
            <?php else: ?>
                <?php
                $canChangeIban = true;
                $daysRemaining = 0;
                if (!empty($business['iban_changed_at'])) {
                    $lastChange = strtotime($business['iban_changed_at']);
                    $daysSinceChange = (time() - $lastChange) / 86400;
                    if ($daysSinceChange < 30) {
                        $canChangeIban = false;
                        $daysRemaining = ceil(30 - $daysSinceChange);
                    }
                }
                ?>
                <?php if ($canChangeIban): ?>
                    <a href="/business/change-iban" class="btn btn-secondary btn-sm" style="margin-top:1rem">
                        <i class="fas fa-edit"></i> <?= $__('payout_change_iban') ?>
                    </a>
                <?php else: ?>
                    <div style="margin-top:1rem;padding:0.75rem;background:var(--bg);border-radius:8px;font-size:0.85rem">
                        <i class="fas fa-lock" style="color:var(--text-muted)"></i>
                        <span class="text-muted"><?= $__('payout_iban_change_cooldown', ['days' => $daysRemaining]) ?></span>
                    </div>
                <?php endif; ?>
                <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.5rem">
                    <i class="fas fa-shield-alt"></i> <?= $__('payout_iban_change_frequency') ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calculator"></i> <?= $__('payout_calculation_title') ?></h3>
            </div>

            <ul style="padding-left:1.25rem;color:var(--text-light);line-height:2;font-size:0.9rem">
                <li><?= $__('payout_calc_booking', ['fee' => $feeData['fee_display'] ?? '€1,75']) ?></li>
                <li><?= $__('payout_calc_frequency') ?></li>
                <li><?= $__('payout_calc_minimum') ?></li>
                <li><?= $__('payout_calc_processing') ?></li>
            </ul>
        </div>

        <!-- Payment Provider Connections -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-link"></i> <?= $__('payout_auto_title') ?></h3>
            </div>
            <p class="text-muted" style="font-size:0.9rem;margin-bottom:1rem"><?= $__('payout_auto_desc') ?></p>

            <!-- Mollie Connect Status -->
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem;background:var(--secondary);border-radius:10px;margin-bottom:0.75rem">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <img src="https://www.mollie.com/external/icons/mollie-logo.svg" alt="Mollie" style="height:24px" onerror="this.innerHTML='Mollie'">
                    <div>
                        <strong>Mollie Connect</strong>
                        <span style="font-size:0.8rem;color:var(--text-muted);display:block">iDEAL, Bancontact, SOFORT</span>
                    </div>
                </div>
                <?php if (!empty($business['mollie_account_id']) && $business['mollie_onboarding_status'] === 'completed'): ?>
                    <span style="background:#10b981;color:white;padding:0.25rem 0.75rem;border-radius:15px;font-size:0.75rem">
                        <i class="fas fa-check"></i> <?= $__('active') ?>
                    </span>
                <?php else: ?>
                    <a href="/business/mollie/connect" class="btn btn-sm" style="background:#000;color:#fff"><?= $__('connect') ?></a>
                <?php endif; ?>
            </div>

            <!-- Stripe Connect Status -->
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem;background:var(--secondary);border-radius:10px">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <svg viewBox="0 0 60 25" style="height:20px;width:auto">
                        <path fill="#635bff" d="M59.64 14.28h-8.06c.19 1.93 1.6 2.55 3.2 2.55 1.64 0 2.96-.37 4.05-.95v3.32a8.33 8.33 0 0 1-4.56 1.1c-4.01 0-6.83-2.5-6.83-7.48 0-4.19 2.39-7.52 6.3-7.52 3.92 0 5.96 3.28 5.96 7.5 0 .4-.02 1.04-.06 1.48zm-6.3-5.63c-1.03 0-1.93.76-2.12 2.39h4.25c-.1-1.46-.78-2.39-2.13-2.39zM37.6 19.52h-4.14V7.24l4.14-.89v13.17zM37.6 6.07h-4.14V2.3l4.14-.86V6.07zM31.12 15.64V5.3h4.14v9.49c0 1.19.58 1.54 1.4 1.54.28 0 .6-.04.95-.14v3.36c-.61.2-1.43.3-2.36.3-2.69 0-4.13-1.48-4.13-4.21zM22.68 5.3h3.18v1.38a4.05 4.05 0 0 1 3.33-1.63c2.75 0 4.08 1.94 4.08 4.88v9.59h-4.14v-8.42c0-1.67-.64-2.3-1.84-2.3-1.3 0-2.47.86-2.47 2.79v7.93h-4.14V5.3z"/>
                    </svg>
                    <div>
                        <strong>Stripe Connect</strong>
                        <span style="font-size:0.8rem;color:var(--text-muted);display:block">Creditcard, Apple Pay, Google Pay</span>
                    </div>
                </div>
                <?php if (!empty($business['stripe_account_id']) && $business['stripe_onboarding_status'] === 'completed'): ?>
                    <span style="background:#10b981;color:white;padding:0.25rem 0.75rem;border-radius:15px;font-size:0.75rem">
                        <i class="fas fa-check"></i> <?= $__('active') ?>
                    </span>
                <?php else: ?>
                    <a href="/business/stripe-connect" class="btn btn-sm" style="background:#635bff;color:#fff"><?= $__('connect') ?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card" style="background:linear-gradient(135deg,#000000,#1a1a1a);border:1px solid #333333;color:#ffffff">
            <h4 style="margin-bottom:0.5rem;color:#ffffff"><i class="fas fa-question-circle"></i> <?= $__('payout_questions') ?></h4>
            <p style="font-size:0.9rem;color:#999999"><?= $__('payout_questions_desc') ?></p>
            <a href="/contact" class="btn" style="background:#ffffff;color:#000000;margin-top:0.5rem">
                <i class="fas fa-envelope"></i> <?= $__('contact') ?>
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

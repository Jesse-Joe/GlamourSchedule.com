<?php ob_start(); ?>

<!-- Stats Grid - Extended -->
<div class="stats-overview">
    <div class="stats-row">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="fas fa-euro-sign"></i></div>
            <div class="stat-content">
                <h4><?= $__('net_earned') ?></h4>
                <p class="value">&euro;<?= number_format($stats['totalEarnings'], 2, ',', '.') ?></p>
                <span class="stat-label"><?= $__('paid_out') ?></span>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-content">
                <h4><?= $__('waiting_payout') ?></h4>
                <p class="value">&euro;<?= number_format($stats['pendingEarnings'], 2, ',', '.') ?></p>
                <span class="stat-label"><?= $stats['convertedReferrals'] ?> <?= $__('converted') ?></span>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-content">
                <h4><?= $__('potential') ?></h4>
                <p class="value">&euro;<?= number_format($stats['potentialEarnings'], 2, ',', '.') ?></p>
                <span class="stat-label"><?= $stats['pendingReferrals'] ?> <?= $__('in_trial') ?></span>
            </div>
        </div>
    </div>

    <div class="stats-row secondary">
        <div class="stat-mini">
            <span class="stat-number"><?= $stats['totalReferrals'] ?></span>
            <span class="stat-text"><?= $__('total') ?></span>
        </div>
        <div class="stat-mini">
            <span class="stat-number success"><?= $stats['paidReferrals'] ?></span>
            <span class="stat-text"><?= $__('paid_out') ?></span>
        </div>
        <div class="stat-mini">
            <span class="stat-number warning"><?= $stats['convertedReferrals'] ?></span>
            <span class="stat-text"><?= $__('converted') ?></span>
        </div>
        <div class="stat-mini">
            <span class="stat-number info"><?= $stats['pendingReferrals'] ?></span>
            <span class="stat-text"><?= $__('in_trial') ?></span>
        </div>
        <div class="stat-mini">
            <span class="stat-number danger"><?= $stats['cancelledReferrals'] ?></span>
            <span class="stat-text"><?= $__('cancelled') ?></span>
        </div>
        <div class="stat-mini">
            <span class="stat-number"><?= $stats['conversionRate'] ?>%</span>
            <span class="stat-text"><?= $__('conversion') ?></span>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid-2" style="margin-bottom:1.5rem">
    <div class="card" style="border:2px solid #333333">
        <h3><i class="fas fa-link"></i> <?= $__('share_your_link') ?></h3>
        <div style="background:#ffffff;padding:0.75rem;border-radius:8px;word-break:break-all;font-family:monospace;font-size:0.85rem;color:#000000;margin-bottom:1rem;border:1px solid rgba(0,0,0,0.1)">
            glamourschedule.nl/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?>
        </div>
        <button onclick="copyLink()" class="btn btn-primary" style="width:100%">
            <i class="fas fa-copy"></i> <?= $__('copy_link') ?>
        </button>
    </div>

    <div class="card" style="background:#1a1a1a;border:2px solid #333">
        <h3 style="color:#fff"><i class="fas fa-euro-sign"></i> <?= $__('earn_amount', ['amount' => '€49,99']) ?></h3>
        <p style="margin:0;color:#a1a1a1;line-height:1.6">
            <?= $__('for_each_salon') ?>
        </p>
        <a href="/sales/materials" style="display:inline-block;margin-top:1rem;color:#fff;text-decoration:none;font-weight:600">
            <i class="fas fa-arrow-right"></i> <?= $__('view_promo_materials') ?>
        </a>
    </div>
</div>

<!-- Early Bird Promo -->
<div class="card" style="background:linear-gradient(135deg,#1a1a2e,#16213e);border:2px solid #f59e0b;margin-bottom:1.5rem">
    <div style="display:flex;gap:1.25rem;align-items:flex-start;flex-wrap:wrap">
        <div style="width:60px;height:60px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-seedling" style="font-size:1.75rem;color:#000"></i>
        </div>
        <div style="flex:1;min-width:200px">
            <h3 style="margin:0 0 0.5rem;color:#fff;display:flex;align-items:center;gap:0.5rem">
                Early Bird Actie
                <span style="background:#f59e0b;color:#000;font-size:0.7rem;padding:0.25rem 0.5rem;border-radius:4px;font-weight:700">NIEUW</span>
            </h3>
            <p style="margin:0 0 1rem;color:#a1a1a1;line-height:1.6">
                Registreer salons voor de Early Bird actie (&euro;0,99) en verdien <strong style="color:#f59e0b">&euro;9,99</strong> per conversie!
            </p>
            <a href="/sales/early-birds" class="btn" style="background:#f59e0b;color:#000;padding:0.75rem 1.5rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem">
                <i class="fas fa-plus"></i> Registreer Early Bird
            </a>
        </div>
    </div>
</div>

<!-- Trial Referrals (In Progress) -->
<?php if (!empty($trialReferrals)): ?>
<div class="card trial-section">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem">
        <h3 style="margin:0"><i class="fas fa-hourglass-half"></i> Lopende Proeftijden</h3>
        <span class="badge-info"><?= count($trialReferrals) ?> actief</span>
    </div>

    <div class="trial-list">
        <?php foreach ($trialReferrals as $trial): ?>
            <?php
            $daysLeft = max(0, (int)$trial['days_remaining']);
            $urgency = $daysLeft <= 3 ? 'urgent' : ($daysLeft <= 7 ? 'warning' : 'normal');
            ?>
            <div class="trial-item <?= $urgency ?>">
                <div class="trial-info">
                    <strong><?= htmlspecialchars($trial['company_name']) ?></strong>
                    <span class="trial-date">Gestart: <?= !empty($trial['business_created']) ? date('d-m-Y', strtotime($trial['business_created'])) : '-' ?></span>
                </div>
                <div class="trial-countdown">
                    <span class="days-badge <?= $urgency ?>">
                        <?php if ($daysLeft == 0): ?>
                            <i class="fas fa-exclamation-triangle"></i> Vandaag
                        <?php elseif ($daysLeft == 1): ?>
                            <i class="fas fa-clock"></i> Nog 1 dag
                        <?php else: ?>
                            <i class="fas fa-clock"></i> Nog <?= $daysLeft ?> dagen
                        <?php endif; ?>
                    </span>
                    <span class="potential-earn">+&euro;<?= number_format($trial['commission'], 2, ',', '.') ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="trial-summary">
        <i class="fas fa-info-circle"></i>
        Als al deze salons converteren verdien je: <strong>&euro;<?= number_format($stats['potentialEarnings'], 2, ',', '.') ?></strong>
    </div>
</div>
<?php endif; ?>

<!-- Recent Referrals -->
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem">
        <h3 style="margin:0"><i class="fas fa-users"></i> Recente Referrals</h3>
        <a href="/sales/referrals" style="color:#333333;text-decoration:none;font-size:0.9rem">
            Bekijk alle <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <?php if (empty($recentReferrals)): ?>
        <div style="text-align:center;padding:2rem 0">
            <i class="fas fa-user-plus" style="font-size:3rem;color:rgba(0,0,0,0.1);margin-bottom:1rem;display:block"></i>
            <p style="color:#666666;margin:0">Nog geen referrals. Deel je code om te beginnen!</p>
            <a href="/sales/materials" class="btn btn-primary" style="margin-top:1rem;display:inline-flex">
                <i class="fas fa-bullhorn"></i> Promotiemateriaal
            </a>
        </div>
    <?php else: ?>
        <!-- Desktop table -->
        <div class="referral-table-desktop" style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>Bedrijf</th>
                        <th>Status</th>
                        <th style="text-align:right">Commissie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentReferrals as $ref): ?>
                        <tr>
                            <td>
                                <strong style="color:#333333"><?= htmlspecialchars($ref['company_name'] ?? '') ?></strong><br>
                                <small style="color:#666666"><?= !empty($ref['created_at']) ? date('d-m-Y', strtotime($ref['created_at'])) : '-' ?></small>
                            </td>
                            <td>
                                <?php
                                $statusColors = [
                                    'pending' => '#f59e0b',
                                    'converted' => '#3b82f6',
                                    'paid' => '#22c55e',
                                    'cancelled' => '#ef4444',
                                    'failed' => '#ef4444',
                                    'expired' => '#6b7280'
                                ];
                                $statusLabels = [
                                    'pending' => 'In proeftijd',
                                    'converted' => 'Geconverteerd',
                                    'paid' => 'Uitbetaald',
                                    'cancelled' => 'Geannuleerd',
                                    'failed' => 'Mislukt',
                                    'expired' => 'Verlopen'
                                ];
                                $status = $ref['status'] ?? 'pending';
                                ?>
                                <span style="background:<?= $statusColors[$status] ?? '#6b7280' ?>20;color:<?= $statusColors[$status] ?? '#6b7280' ?>;padding:0.25rem 0.75rem;border-radius:15px;font-size:0.8rem;white-space:nowrap">
                                    <?= $statusLabels[$status] ?? $status ?>
                                </span>
                            </td>
                            <td style="text-align:right;font-weight:600;color:<?= $status === 'paid' ? '#22c55e' : ($status === 'cancelled' || $status === 'failed' || $status === 'expired' ? '#ef4444' : '#333333') ?>">
                                <?php if ($status === 'cancelled' || $status === 'failed' || $status === 'expired'): ?>
                                    <s style="color:#999">&euro;<?= number_format($ref['commission'], 2, ',', '.') ?></s>
                                <?php else: ?>
                                    &euro;<?= number_format($ref['commission'], 2, ',', '.') ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile cards -->
        <div class="referral-cards-mobile">
            <?php foreach ($recentReferrals as $ref): ?>
                <?php
                $statusColors = [
                    'pending' => '#f59e0b',
                    'converted' => '#3b82f6',
                    'paid' => '#22c55e',
                    'cancelled' => '#ef4444',
                    'failed' => '#ef4444',
                    'expired' => '#6b7280'
                ];
                $statusLabels = [
                    'pending' => 'In proeftijd',
                    'converted' => 'Geconverteerd',
                    'paid' => 'Uitbetaald',
                    'cancelled' => 'Geannuleerd',
                    'failed' => 'Mislukt',
                    'expired' => 'Verlopen'
                ];
                $status = $ref['status'] ?? 'pending';
                ?>
                <div style="background:#ffffff;border-radius:12px;padding:1rem;margin-bottom:0.75rem;border:1px solid rgba(0,0,0,0.1)">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem">
                        <strong style="color:#333333"><?= htmlspecialchars($ref['company_name']) ?></strong>
                        <span style="background:<?= $statusColors[$status] ?? '#6b7280' ?>20;color:<?= $statusColors[$status] ?? '#6b7280' ?>;padding:0.25rem 0.5rem;border-radius:10px;font-size:0.75rem">
                            <?= $statusLabels[$status] ?? $status ?>
                        </span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <small style="color:#666666"><?= !empty($ref['created_at']) ? date('d-m-Y', strtotime($ref['created_at'])) : '-' ?></small>
                        <span style="font-weight:700;color:<?= $status === 'paid' ? '#22c55e' : '#333333' ?>">&euro;<?= number_format($ref['commission'] ?? 0, 2, ',', '.') ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
/* Stats Overview */
.stats-overview {
    margin-bottom: 1.5rem;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1rem;
}

.stats-row.secondary {
    grid-template-columns: repeat(6, 1fr);
    gap: 0.5rem;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stat-card.primary {
    border-left: 4px solid #22c55e;
}

.stat-card.success {
    border-left: 4px solid #3b82f6;
}

.stat-card.warning {
    border-left: 4px solid #f59e0b;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-card.primary .stat-icon {
    background: #f0fdf4;
    color: #22c55e;
}

.stat-card.success .stat-icon {
    background: #eff6ff;
    color: #3b82f6;
}

.stat-card.warning .stat-icon {
    background: #fffbeb;
    color: #f59e0b;
}

.stat-content h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.85rem;
    color: #666;
    font-weight: 500;
}

.stat-content .value {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #000;
}

.stat-content .stat-label {
    font-size: 0.75rem;
    color: #999;
}

.stat-mini {
    background: #fff;
    border-radius: 8px;
    padding: 0.75rem;
    text-align: center;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.stat-mini .stat-number {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    color: #333;
}

.stat-mini .stat-number.success { color: #22c55e; }
.stat-mini .stat-number.warning { color: #f59e0b; }
.stat-mini .stat-number.info { color: #3b82f6; }
.stat-mini .stat-number.danger { color: #ef4444; }

.stat-mini .stat-text {
    font-size: 0.7rem;
    color: #999;
    text-transform: uppercase;
}

/* Trial Section */
.trial-section {
    background: #fffbeb;
    border: 1px solid #f59e0b;
}

.badge-info {
    background: #f59e0b;
    color: #fff;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.trial-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.trial-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    border-radius: 10px;
    padding: 1rem;
    border-left: 4px solid #3b82f6;
}

.trial-item.warning {
    border-left-color: #f59e0b;
}

.trial-item.urgent {
    border-left-color: #ef4444;
    background: #fef2f2;
}

.trial-info strong {
    display: block;
    color: #333;
}

.trial-info .trial-date {
    font-size: 0.8rem;
    color: #666;
}

.trial-countdown {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.days-badge {
    font-size: 0.85rem;
    font-weight: 600;
    color: #3b82f6;
}

.days-badge.warning {
    color: #f59e0b;
}

.days-badge.urgent {
    color: #ef4444;
}

.potential-earn {
    font-size: 0.9rem;
    font-weight: 700;
    color: #22c55e;
}

.trial-summary {
    margin-top: 1rem;
    padding: 0.75rem;
    background: #fef3c7;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #92400e;
}

.trial-summary i {
    margin-right: 0.5rem;
}

/* Tables */
.referral-table-desktop { display: block; }
.referral-cards-mobile { display: none; }

@media (max-width: 992px) {
    .stats-row {
        grid-template-columns: 1fr;
    }

    .stats-row.secondary {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .referral-table-desktop { display: none; }
    .referral-cards-mobile { display: block; }

    .stats-row.secondary {
        grid-template-columns: repeat(2, 1fr);
    }

    .trial-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .trial-countdown {
        flex-direction: row;
        width: 100%;
        justify-content: space-between;
    }
}
</style>

<script>
    function copyLink() {
        const link = 'https://glamourschedule.nl/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?>';
        navigator.clipboard.writeText(link).then(() => {
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#333333;color:#ffffff;padding:0.75rem 1.5rem;border-radius:10px;font-weight:600;z-index:9999';
            toast.innerHTML = '<i class="fas fa-check"></i> Link gekopieerd!';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        });
    }
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/sales.php'; ?>

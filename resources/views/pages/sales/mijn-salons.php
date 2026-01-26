<?php ob_start(); ?>

<!-- Stats -->
<div class="salon-stats">
    <div class="stat-box">
        <span class="stat-number"><?= $stats['total'] ?></span>
        <span class="stat-label">Totaal</span>
    </div>
    <div class="stat-box active">
        <span class="stat-number"><?= $stats['active'] ?></span>
        <span class="stat-label">Actief</span>
    </div>
    <div class="stat-box trial">
        <span class="stat-number"><?= $stats['in_trial'] ?></span>
        <span class="stat-label">In proeftijd</span>
    </div>
    <div class="stat-box pending">
        <span class="stat-number"><?= $stats['pending'] ?></span>
        <span class="stat-label">In afwachting</span>
    </div>
    <div class="stat-box inactive">
        <span class="stat-number"><?= $stats['inactive'] ?></span>
        <span class="stat-label">Inactief</span>
    </div>
</div>

<!-- Salons List -->
<div class="card">
    <h3 style="margin:0 0 1.5rem 0"><i class="fas fa-store"></i> Salons via jouw code</h3>

    <?php if (empty($salons)): ?>
        <div style="text-align:center;padding:3rem 0">
            <i class="fas fa-store-slash" style="font-size:4rem;color:rgba(0,0,0,0.1);margin-bottom:1rem;display:block"></i>
            <p style="color:#666;margin:0 0 1rem">Nog geen salons aangemeld via jouw code.</p>
            <a href="/sales/materials" class="btn btn-primary">
                <i class="fas fa-bullhorn"></i> Bekijk promotiemateriaal
            </a>
        </div>
    <?php else: ?>
        <!-- Desktop Table -->
        <div class="table-responsive">
            <table class="salon-table">
                <thead>
                    <tr>
                        <th>Salon</th>
                        <th>Contact</th>
                        <th>Locatie</th>
                        <th>Aangemeld</th>
                        <th>Status</th>
                        <th style="text-align:right">Commissie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salons as $salon): ?>
                        <?php
                        $statusColors = [
                            'pending' => '#f59e0b',
                            'active' => '#22c55e',
                            'trial' => '#3b82f6',
                            'inactive' => '#6b7280',
                            'suspended' => '#ef4444'
                        ];
                        $statusLabels = [
                            'pending' => 'In afwachting',
                            'active' => 'Actief',
                            'trial' => 'Proeftijd',
                            'inactive' => 'Inactief',
                            'suspended' => 'Geschorst'
                        ];
                        $status = $salon['status'] ?? 'pending';
                        if (($salon['subscription_status'] ?? '') === 'trial') {
                            $status = 'trial';
                        }
                        $referralStatusColors = [
                            'pending' => '#f59e0b',
                            'converted' => '#3b82f6',
                            'paid' => '#22c55e',
                            'cancelled' => '#ef4444'
                        ];
                        $referralStatusLabels = [
                            'pending' => 'In afwachting',
                            'converted' => 'Geconverteerd',
                            'paid' => 'Uitbetaald',
                            'cancelled' => 'Geannuleerd'
                        ];
                        ?>
                        <tr>
                            <td>
                                <strong style="color:#000"><?= htmlspecialchars($salon['company_name'] ?? '') ?></strong>
                            </td>
                            <td>
                                <div style="font-size:0.9rem">
                                    <div style="color:#333"><?= htmlspecialchars($salon['email']) ?></div>
                                    <?php if (!empty($salon['phone'])): ?>
                                        <div style="color:#666;font-size:0.85rem"><?= htmlspecialchars($salon['phone']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="color:#666">
                                <?= !empty($salon['city']) ? htmlspecialchars($salon['city']) : '-' ?>
                            </td>
                            <td style="color:#666">
                                <?= !empty($salon['created_at']) ? date('d-m-Y', strtotime($salon['created_at'])) : '-' ?>
                            </td>
                            <td>
                                <span class="status-badge" style="background:<?= $statusColors[$status] ?>20;color:<?= $statusColors[$status] ?>">
                                    <?= $statusLabels[$status] ?>
                                </span>
                            </td>
                            <td style="text-align:right">
                                <?php $refStatus = $salon['referral_status'] ?? 'pending'; ?>
                                <div style="font-weight:600;color:<?= $refStatus === 'paid' ? '#22c55e' : ($refStatus === 'cancelled' ? '#ef4444' : '#000') ?>">
                                    <?php if ($refStatus === 'cancelled'): ?>
                                        <s style="color:#999">&euro;<?= number_format($salon['commission'] ?? 0, 2, ',', '.') ?></s>
                                    <?php else: ?>
                                        &euro;<?= number_format($salon['commission'] ?? 0, 2, ',', '.') ?>
                                    <?php endif; ?>
                                </div>
                                <small style="color:<?= $referralStatusColors[$refStatus] ?? '#6b7280' ?>;font-size:0.75rem">
                                    <?= $referralStatusLabels[$refStatus] ?? 'Onbekend' ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="salon-cards-mobile">
            <?php foreach ($salons as $salon): ?>
                <?php
                $status = $salon['status'] ?? 'pending';
                if (($salon['subscription_status'] ?? '') === 'trial') {
                    $status = 'trial';
                }
                $statusColors = [
                    'pending' => '#f59e0b',
                    'active' => '#22c55e',
                    'trial' => '#3b82f6',
                    'inactive' => '#6b7280',
                    'suspended' => '#ef4444'
                ];
                $statusLabels = [
                    'pending' => 'In afwachting',
                    'active' => 'Actief',
                    'trial' => 'Proeftijd',
                    'inactive' => 'Inactief',
                    'suspended' => 'Geschorst'
                ];
                ?>
                <div class="salon-card">
                    <div class="salon-card-header">
                        <strong><?= htmlspecialchars($salon['company_name'] ?? '') ?></strong>
                        <span class="status-badge" style="background:<?= $statusColors[$status] ?>20;color:<?= $statusColors[$status] ?>">
                            <?= $statusLabels[$status] ?>
                        </span>
                    </div>
                    <div class="salon-card-body">
                        <div class="salon-info-row">
                            <i class="fas fa-envelope"></i>
                            <span><?= htmlspecialchars($salon['email']) ?></span>
                        </div>
                        <?php if (!empty($salon['phone'])): ?>
                            <div class="salon-info-row">
                                <i class="fas fa-phone"></i>
                                <span><?= htmlspecialchars($salon['phone']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($salon['city'])): ?>
                            <div class="salon-info-row">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($salon['city']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="salon-info-row">
                            <i class="fas fa-calendar"></i>
                            <span><?= !empty($salon['created_at']) ? date('d-m-Y', strtotime($salon['created_at'])) : '-' ?></span>
                        </div>
                    </div>
                    <div class="salon-card-footer">
                        <span style="color:#666;font-size:0.85rem">Commissie</span>
                        <span style="font-weight:700;font-size:1.1rem">&euro;<?= number_format($salon['commission'] ?? 0, 2, ',', '.') ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.salon-stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-box {
    background: #fff;
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-top: 3px solid #e5e7eb;
}

.stat-box.active { border-top-color: #22c55e; }
.stat-box.trial { border-top-color: #3b82f6; }
.stat-box.pending { border-top-color: #f59e0b; }
.stat-box.inactive { border-top-color: #6b7280; }

.stat-box .stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: #000;
}

.stat-box .stat-label {
    font-size: 0.85rem;
    color: #666;
}

.table-responsive {
    overflow-x: auto;
}

.salon-table {
    width: 100%;
    border-collapse: collapse;
}

.salon-table th {
    text-align: left;
    padding: 1rem 0.75rem;
    border-bottom: 2px solid #e5e7eb;
    color: #666;
    font-weight: 600;
    font-size: 0.85rem;
}

.salon-table td {
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    white-space: nowrap;
}

.salon-cards-mobile {
    display: none;
}

.salon-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    margin-bottom: 1rem;
    overflow: hidden;
}

.salon-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.salon-card-body {
    padding: 1rem;
}

.salon-info-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    color: #666;
    font-size: 0.9rem;
}

.salon-info-row i {
    width: 20px;
    text-align: center;
    color: #999;
}

.salon-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

@media (max-width: 992px) {
    .salon-stats {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .salon-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .table-responsive {
        display: none;
    }

    .salon-cards-mobile {
        display: block;
    }
}

@media (max-width: 480px) {
    .salon-stats {
        grid-template-columns: 1fr 1fr;
    }

    .salon-stats .stat-box:last-child {
        grid-column: span 2;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/sales.php'; ?>

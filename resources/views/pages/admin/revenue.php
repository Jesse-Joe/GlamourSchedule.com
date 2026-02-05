<?php ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-label"><?= $__('total_revenue') ?></div>
        <div class="stat-value">&euro;<?= number_format($stats['totalRevenue'], 2, ',', '.') ?></div>
    </div>
    <div class="stat-card success">
        <div class="stat-label"><?= $__('revenue_this_month') ?></div>
        <div class="stat-value">&euro;<?= number_format($stats['revenueThisMonth'], 2, ',', '.') ?></div>
    </div>
    <div class="stat-card warning">
        <div class="stat-label"><?= $__('registration_fees') ?></div>
        <div class="stat-value">&euro;<?= number_format($stats['registrationFees'], 2, ',', '.') ?></div>
    </div>
    <div class="stat-card info">
        <div class="stat-label"><?= $__('admin_fees_bookings') ?></div>
        <div class="stat-value">&euro;<?= number_format($stats['adminFees'], 2, ',', '.') ?></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-line"></i> <?= $__('revenue_trend') ?></h3>
        </div>
        <div style="height:250px;display:flex;align-items:flex-end;gap:4px;padding:1rem 0;">
            <?php
            $maxRevenue = max(array_column($stats['revenuePerDay'], 'total')) ?: 1;
            foreach ($stats['revenuePerDay'] as $day):
                $height = ($day['total'] / $maxRevenue) * 200;
            ?>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                <div style="width:100%;background:var(--success);border-radius:4px 4px 0 0;height:<?= max($height, 4) ?>px;transition:height 0.3s;" title="&euro;<?= number_format($day['total'], 2, ',', '.') ?>"></div>
                <div style="font-size:0.65rem;color:var(--text-light);margin-top:0.5rem;transform:rotate(-45deg);white-space:nowrap;">
                    <?= date('d/m', strtotime($day['date'])) ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($stats['revenuePerDay'])): ?>
            <div style="width:100%;text-align:center;color:var(--text-light);padding:2rem;">
                <?= $__('no_data_available') ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calculator"></i> <?= $__('distribution') ?></h3>
        </div>
        <div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)"><?= $__('registration_fees') ?></span>
                <strong>&euro;<?= number_format($stats['registrationFees'], 2, ',', '.') ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)"><?= $__('admin_fees') ?></span>
                <strong>&euro;<?= number_format($stats['adminFees'], 2, ',', '.') ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)"><?= $__('sales_commissions') ?></span>
                <strong style="color:var(--danger);">-&euro;<?= number_format($stats['salesCommissions'], 2, ',', '.') ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;background:var(--bg);margin:0.5rem -1.5rem -1.5rem;padding:1rem 1.5rem;border-radius:0 0 12px 12px;">
                <span><strong><?= $__('net_revenue') ?></strong></span>
                <strong style="color:var(--success);">&euro;<?= number_format($stats['totalRevenue'] - $stats['salesCommissions'], 2, ',', '.') ?></strong>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> <?= $__('recent_transactions') ?></h3>
        <form method="GET" class="search-box" style="max-width:400px;">
            <select name="type" class="form-control" style="width:auto;" onchange="this.form.submit()">
                <option value=""><?= $__('all_types') ?></option>
                <option value="registration" <?= $type === 'registration' ? 'selected' : '' ?>><?= $__('registrations') ?></option>
                <option value="booking" <?= $type === 'booking' ? 'selected' : '' ?>><?= $__('bookings') ?></option>
                <option value="commission" <?= $type === 'commission' ? 'selected' : '' ?>><?= $__('commissions') ?></option>
            </select>
            <a href="/admin/revenue/export?type=<?= urlencode($type) ?>" class="btn btn-secondary">
                <i class="fas fa-download"></i> <?= $__('export_csv') ?>
            </a>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $__('date') ?></th>
                    <th><?= $__('type') ?></th>
                    <th><?= $__('description') ?></th>
                    <th><?= $__('business') ?></th>
                    <th style="text-align:right;"><?= $__('amount') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;color:var(--text-light);padding:2rem;">
                        <?= $__('no_transactions_found') ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($transactions as $tx): ?>
                <tr>
                    <td><?= $formatDateTime($tx['created_at']) ?></td>
                    <td>
                        <?php if ($tx['type'] === 'registration'): ?>
                            <span class="badge badge-info"><?= $__('registration') ?></span>
                        <?php elseif ($tx['type'] === 'booking'): ?>
                            <span class="badge badge-success"><?= $__('booking') ?></span>
                        <?php elseif ($tx['type'] === 'commission'): ?>
                            <span class="badge badge-warning"><?= $__('commission') ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= $__($tx['type']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($tx['description'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($tx['business_name'] ?? '-') ?></td>
                    <td style="text-align:right;">
                        <?php if (($tx['amount'] ?? 0) >= 0): ?>
                            <strong style="color:var(--success);">&euro;<?= number_format($tx['amount'], 2, ',', '.') ?></strong>
                        <?php else: ?>
                            <strong style="color:var(--danger);">-&euro;<?= number_format(abs($tx['amount']), 2, ',', '.') ?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>&type=<?= urlencode($type) ?>"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>

        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
            <?php if ($i === $currentPage): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="?page=<?= $i ?>&type=<?= urlencode($type) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>&type=<?= urlencode($type) ?>"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar-alt"></i> <?= $__('monthly_overview') ?></h3>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th><?= $__('month') ?></th>
                        <th style="text-align:right;"><?= $__('registrations') ?></th>
                        <th style="text-align:right;"><?= $__('admin_fees') ?></th>
                        <th style="text-align:right;"><?= $__('total') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['monthlyOverview'] as $month): ?>
                    <tr>
                        <td><?= $month['month_name'] ?></td>
                        <td style="text-align:right;">&euro;<?= number_format($month['registrations'] ?? 0, 2, ',', '.') ?></td>
                        <td style="text-align:right;">&euro;<?= number_format($month['admin_fees'] ?? 0, 2, ',', '.') ?></td>
                        <td style="text-align:right;"><strong>&euro;<?= number_format(($month['registrations'] ?? 0) + ($month['admin_fees'] ?? 0), 2, ',', '.') ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stats['monthlyOverview'])): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--text-light);"><?= $__('no_data') ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-trophy"></i> <?= $__('top_businesses_revenue') ?></h3>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?= $__('business') ?></th>
                        <th style="text-align:right;"><?= $__('admin_fees') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; foreach ($stats['topBusinesses'] as $biz): ?>
                    <tr>
                        <td><?= $rank++ ?></td>
                        <td><?= htmlspecialchars($biz['company_name']) ?></td>
                        <td style="text-align:right;"><strong>&euro;<?= number_format($biz['total_fees'] ?? 0, 2, ',', '.') ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stats['topBusinesses'])): ?>
                    <tr>
                        <td colspan="3" style="text-align:center;color:var(--text-light);"><?= $__('no_data') ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/admin.php'; ?>

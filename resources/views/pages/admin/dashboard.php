<?php ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-label"><?= $translations['admin_total_users'] ?? 'Total Users' ?></div>
        <div class="stat-value"><?= number_format($stats['totalUsers']) ?></div>
    </div>
    <div class="stat-card success">
        <div class="stat-label"><?= $translations['admin_active_businesses'] ?? 'Active Businesses' ?></div>
        <div class="stat-value"><?= number_format($stats['activeBusinesses']) ?></div>
        <div class="stat-change"><?= $stats['pendingBusinesses'] ?> <?= $translations['admin_pending'] ?? 'pending' ?></div>
    </div>
    <div class="stat-card warning">
        <div class="stat-label"><?= $translations['admin_bookings_today'] ?? 'Bookings Today' ?></div>
        <div class="stat-value"><?= number_format($stats['bookingsToday']) ?></div>
        <div class="stat-change"><?= number_format($stats['bookingsThisMonth']) ?> <?= $translations['admin_this_month'] ?? 'this month' ?></div>
    </div>
    <div class="stat-card info">
        <div class="stat-label"><?= $translations['admin_revenue_this_month'] ?? 'Revenue This Month' ?></div>
        <div class="stat-value">&euro;<?= number_format($stats['revenueThisMonth'], 2, ',', '.') ?></div>
        <div class="stat-change">&euro;<?= number_format($stats['totalRevenue'], 2, ',', '.') ?> <?= $translations['admin_total'] ?? 'total' ?></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-bar"></i> <?= $translations['admin_bookings_chart_title'] ?? 'Bookings (14 days)' ?></h3>
        </div>
        <div style="height:250px;display:flex;align-items:flex-end;gap:4px;padding:1rem 0;">
            <?php
            $maxBookings = max(array_column($stats['bookingsPerDay'], 'count')) ?: 1;
            foreach ($stats['bookingsPerDay'] as $day):
                $height = ($day['count'] / $maxBookings) * 200;
            ?>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                <div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;height:<?= max($height, 4) ?>px;transition:height 0.3s;" title="<?= $day['count'] ?> <?= $translations['admin_bookings'] ?? 'bookings' ?>"></div>
                <div style="font-size:0.65rem;color:var(--text-light);margin-top:0.5rem;transform:rotate(-45deg);white-space:nowrap;">
                    <?= date('d/m', strtotime($day['date'])) ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($stats['bookingsPerDay'])): ?>
            <div style="width:100%;text-align:center;color:var(--text-light);padding:2rem;">
                <?= $translations['admin_no_data'] ?? 'No data available' ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-info-circle"></i> <?= $translations['admin_quick_overview'] ?? 'Quick Overview' ?></h3>
        </div>
        <div style="space-y:1rem;">
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)"><?= $translations['admin_total_businesses'] ?? 'Total Businesses' ?></span>
                <strong><?= number_format($stats['totalBusinesses']) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)"><?= $translations['admin_total_bookings'] ?? 'Total Bookings' ?></span>
                <strong><?= number_format($stats['totalBookings']) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)"><?= $translations['admin_sales_partners'] ?? 'Sales Partners' ?></span>
                <strong><?= number_format($stats['activeSalesPartners']) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)"><?= $translations['admin_new_registrations'] ?? 'New Registrations (7d)' ?></span>
                <strong><?= number_format($stats['recentRegistrations']) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;">
                <span style="color:var(--text-light)"><?= $translations['admin_total_revenue'] ?? 'Total Revenue' ?></span>
                <strong>&euro;<?= number_format($stats['totalRevenue'], 2, ',', '.') ?></strong>
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-rocket"></i> <?= $translations['admin_quick_actions'] ?? 'Quick Actions' ?></h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <a href="/admin/users" class="btn btn-secondary" style="justify-content:center;">
                <i class="fas fa-users"></i> <?= $translations['admin_users'] ?? 'Users' ?>
            </a>
            <a href="/admin/businesses" class="btn btn-secondary" style="justify-content:center;">
                <i class="fas fa-store"></i> <?= $translations['admin_businesses'] ?? 'Businesses' ?>
            </a>
            <a href="/admin/sales-partners" class="btn btn-secondary" style="justify-content:center;">
                <i class="fas fa-handshake"></i> <?= $translations['admin_sales_partners'] ?? 'Sales Partners' ?>
            </a>
            <a href="/admin/revenue" class="btn btn-secondary" style="justify-content:center;">
                <i class="fas fa-chart-line"></i> <?= $translations['admin_revenue_report'] ?? 'Revenue Report' ?>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-bell"></i> <?= $translations['admin_status'] ?? 'Status' ?></h3>
        </div>
        <div>
            <?php if ($stats['pendingBusinesses'] > 0): ?>
            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:#fff3cd;border-radius:8px;margin-bottom:0.75rem;">
                <i class="fas fa-exclamation-triangle" style="color:#856404;"></i>
                <span style="color:#856404;"><?= $stats['pendingBusinesses'] ?> <?= $translations['admin_waiting_activation'] ?? 'business(es) waiting for activation' ?></span>
            </div>
            <?php endif; ?>

            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:#d4edda;border-radius:8px;">
                <i class="fas fa-check-circle" style="color:#155724;"></i>
                <span style="color:#155724;"><?= $translations['admin_system_normal'] ?? 'System running normally' ?></span>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/admin.php'; ?>

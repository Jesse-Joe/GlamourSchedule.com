<?php ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-label">Totaal Gebruikers</div>
        <div class="stat-value"><?= number_format($stats['totalUsers']) ?></div>
    </div>
    <div class="stat-card success">
        <div class="stat-label">Actieve Bedrijven</div>
        <div class="stat-value"><?= number_format($stats['activeBusinesses']) ?></div>
        <div class="stat-change"><?= $stats['pendingBusinesses'] ?> pending</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-label">Boekingen Vandaag</div>
        <div class="stat-value"><?= number_format($stats['bookingsToday']) ?></div>
        <div class="stat-change"><?= number_format($stats['bookingsThisMonth']) ?> deze maand</div>
    </div>
    <div class="stat-card info">
        <div class="stat-label">Omzet Deze Maand</div>
        <div class="stat-value">&euro;<?= number_format($stats['revenueThisMonth'], 2, ',', '.') ?></div>
        <div class="stat-change">&euro;<?= number_format($stats['totalRevenue'], 2, ',', '.') ?> totaal</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Boekingen (14 dagen)</h3>
        </div>
        <div style="height:250px;display:flex;align-items:flex-end;gap:4px;padding:1rem 0;">
            <?php
            $maxBookings = max(array_column($stats['bookingsPerDay'], 'count')) ?: 1;
            foreach ($stats['bookingsPerDay'] as $day):
                $height = ($day['count'] / $maxBookings) * 200;
            ?>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                <div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;height:<?= max($height, 4) ?>px;transition:height 0.3s;" title="<?= $day['count'] ?> boekingen"></div>
                <div style="font-size:0.65rem;color:var(--text-light);margin-top:0.5rem;transform:rotate(-45deg);white-space:nowrap;">
                    <?= date('d/m', strtotime($day['date'])) ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($stats['bookingsPerDay'])): ?>
            <div style="width:100%;text-align:center;color:var(--text-light);padding:2rem;">
                Geen data beschikbaar
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-info-circle"></i> Snel Overzicht</h3>
        </div>
        <div style="space-y:1rem;">
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)">Totaal Bedrijven</span>
                <strong><?= number_format($stats['totalBusinesses']) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)">Totaal Boekingen</span>
                <strong><?= number_format($stats['totalBookings']) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)">Sales Partners</span>
                <strong><?= number_format($stats['activeSalesPartners']) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                <span style="color:var(--text-light)">Nieuwe Registraties (7d)</span>
                <strong><?= number_format($stats['recentRegistrations']) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:0.75rem 0;">
                <span style="color:var(--text-light)">Totale Omzet</span>
                <strong>&euro;<?= number_format($stats['totalRevenue'], 2, ',', '.') ?></strong>
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-rocket"></i> Snelle Acties</h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <a href="/admin/users" class="btn btn-secondary" style="justify-content:center;">
                <i class="fas fa-users"></i> Gebruikers
            </a>
            <a href="/admin/businesses" class="btn btn-secondary" style="justify-content:center;">
                <i class="fas fa-store"></i> Bedrijven
            </a>
            <a href="/admin/sales-partners" class="btn btn-secondary" style="justify-content:center;">
                <i class="fas fa-handshake"></i> Sales Partners
            </a>
            <a href="/admin/revenue" class="btn btn-secondary" style="justify-content:center;">
                <i class="fas fa-chart-line"></i> Omzet Rapport
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-bell"></i> Status</h3>
        </div>
        <div>
            <?php if ($stats['pendingBusinesses'] > 0): ?>
            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:#fff3cd;border-radius:8px;margin-bottom:0.75rem;">
                <i class="fas fa-exclamation-triangle" style="color:#856404;"></i>
                <span style="color:#856404;"><?= $stats['pendingBusinesses'] ?> bedrijf(en) wachten op activatie</span>
            </div>
            <?php endif; ?>

            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:#d4edda;border-radius:8px;">
                <i class="fas fa-check-circle" style="color:#155724;"></i>
                <span style="color:#155724;">Systeem draait normaal</span>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/admin.php'; ?>

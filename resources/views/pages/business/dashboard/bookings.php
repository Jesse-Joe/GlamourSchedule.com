<?php ob_start(); ?>

<style>
    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .filter-tab {
        padding: 0.6rem 1.25rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
        background: var(--b-bg-card);
        color: var(--b-text);
        border: 2px solid var(--b-border);
    }
    .filter-tab:hover {
        border-color: var(--b-accent);
        color: var(--b-accent);
    }
    .filter-tab.active {
        background: linear-gradient(135deg, #333333, #111111);
        color: white;
        border-color: transparent;
    }
    .booking-row {
        display: grid;
        grid-template-columns: 100px 1.5fr 1fr 1fr 120px;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid var(--b-border);
        gap: 1rem;
    }
    .booking-row:hover {
        background: var(--b-bg-surface);
        margin: 0 -1.5rem;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    @media (max-width: 768px) {
        .booking-row {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.3rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-confirmed { background: var(--b-bg-card); color: var(--b-text); }
    .status-pending { background: var(--b-bg-card); color: var(--b-text); }
    .status-cancelled { background: var(--b-bg-surface); color: var(--b-text); }
    .status-completed { background: var(--b-bg-surface); color: var(--b-text); }
</style>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="/business/bookings?filter=upcoming" class="filter-tab <?= ($filter ?? 'upcoming') === 'upcoming' ? 'active' : '' ?>">
        <i class="fas fa-calendar-alt"></i> Aankomend
    </a>
    <a href="/business/bookings?filter=past" class="filter-tab <?= ($filter ?? '') === 'past' ? 'active' : '' ?>">
        <i class="fas fa-history"></i> Afgelopen
    </a>
    <a href="/business/bookings?filter=cancelled" class="filter-tab <?= ($filter ?? '') === 'cancelled' ? 'active' : '' ?>">
        <i class="fas fa-times-circle"></i> Geannuleerd
    </a>
    <a href="/business/bookings?filter=all" class="filter-tab <?= ($filter ?? '') === 'all' ? 'active' : '' ?>">
        <i class="fas fa-list"></i> Alle
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clipboard-list"></i>
            <?php
            $titles = ['upcoming' => 'Aankomende Boekingen', 'past' => 'Afgelopen Boekingen', 'cancelled' => 'Geannuleerde Boekingen', 'all' => 'Alle Boekingen'];
            echo $titles[$filter ?? 'upcoming'] ?? 'Boekingen';
            ?>
            (<?= count($bookings) ?>)
        </h3>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="text-center" style="padding:3rem">
            <i class="fas fa-calendar-check" style="font-size:4rem;color:var(--b-border);margin-bottom:1rem"></i>
            <h4>Geen boekingen gevonden</h4>
            <p class="text-muted">Er zijn geen boekingen in deze categorie.</p>
        </div>
    <?php else: ?>
        <!-- Header -->
        <div class="booking-row" style="font-weight:600;font-size:0.85rem;color:var(--b-text-muted);border-bottom:2px solid var(--b-border)">
            <div>Datum</div>
            <div>Klant</div>
            <div>Dienst</div>
            <div>Tijd</div>
            <div>Status</div>
        </div>

        <?php foreach ($bookings as $booking): ?>
            <div class="booking-row">
                <div>
                    <strong><?= !empty($booking['appointment_date']) ? date('d-m-Y', strtotime($booking['appointment_date'])) : '-' ?></strong>
                </div>
                <div>
                    <strong><?= htmlspecialchars($booking['first_name'] ?? $booking['guest_name'] ?? 'Gast') ?></strong>
                    <?php if (!empty($booking['last_name'])): ?>
                        <?= htmlspecialchars($booking['last_name']) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <?= htmlspecialchars($booking['service_name'] ?? 'Onbekende dienst') ?>
                    <?php if (!empty($booking['total_price'])): ?>
                        <br><small class="text-muted">&euro;<?= number_format($booking['total_price'], 2, ',', '.') ?></small>
                    <?php endif; ?>
                </div>
                <div>
                    <i class="fas fa-clock" style="color:var(--b-text-muted)"></i>
                    <?= !empty($booking['appointment_time']) ? date('H:i', strtotime($booking['appointment_time'])) : '-' ?>
                    <?php if (!empty($booking['duration_minutes'])): ?>
                        - <?= date('H:i', strtotime($booking['appointment_time'] . ' +' . $booking['duration_minutes'] . ' minutes')) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <?php
                    $statusClasses = [
                        'confirmed' => 'status-confirmed',
                        'pending' => 'status-pending',
                        'cancelled' => 'status-cancelled',
                        'completed' => 'status-completed'
                    ];
                    $statusLabels = [
                        'confirmed' => 'Bevestigd',
                        'pending' => 'In afwachting',
                        'cancelled' => 'Geannuleerd',
                        'completed' => 'Afgerond'
                    ];
                    $statusIcons = [
                        'confirmed' => 'check-circle',
                        'pending' => 'clock',
                        'cancelled' => 'times-circle',
                        'completed' => 'flag-checkered'
                    ];
                    $status = $booking['status'] ?? 'pending';
                    ?>
                    <span class="status-badge <?= $statusClasses[$status] ?? 'status-pending' ?>">
                        <i class="fas fa-<?= $statusIcons[$status] ?? 'clock' ?>"></i>
                        <?= $statusLabels[$status] ?? 'Onbekend' ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

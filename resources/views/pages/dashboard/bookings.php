<?php ob_start(); ?>

<style>
/* Dark Dashboard Theme */
.dashboard-page {
    background: #000000;
    min-height: 100vh;
    padding-bottom: 2rem;
}
.dashboard-page .container {
    padding-top: 1rem;
}
.dashboard-page h2 {
    color: #ffffff;
}
.dashboard-page .card {
    background: #111111;
    border: 1px solid #333333;
    color: #ffffff;
}

/* Booking tabs */
.booking-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #333333;
    padding-bottom: 0.5rem;
}
.booking-tab {
    padding: 0.75rem 1.25rem;
    border: none;
    background: none;
    font-size: 0.95rem;
    font-weight: 500;
    color: rgba(255,255,255,0.6);
    cursor: pointer;
    border-radius: 8px 8px 0 0;
    transition: all 0.2s;
}
.booking-tab.active {
    color: #ffffff;
    background: #1a1a1a;
}
.booking-tab:hover:not(.active) {
    color: rgba(255,255,255,0.8);
}

/* Booking cards */
.booking-card {
    background: #1a1a1a;
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    border: 2px solid #333333;
    transition: all 0.2s;
}
.booking-card:hover {
    border-color: #ffffff;
}
.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}
.booking-card-business {
    font-weight: 700;
    font-size: 1.1rem;
    color: #ffffff;
}
.booking-card-service {
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
    margin-top: 0.25rem;
}
.booking-card-datetime {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
    color: #ffffff;
}
.booking-card-date, .booking-card-time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #ffffff;
}
.booking-card-date i, .booking-card-time i {
    color: #ffffff;
    width: 20px;
}
.booking-card-price {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #ffffff;
}
.booking-card-qr {
    background: linear-gradient(135deg, #ffffff, #f5f5f5);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}
.booking-card-qr img {
    width: 60px;
    height: 60px;
    border-radius: 8px;
}
.booking-card-qr-text {
    flex: 1;
    font-size: 0.85rem;
    color: #000000;
}
.booking-card-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.booking-card-actions .btn {
    flex: 1;
    min-width: 100px;
    text-align: center;
    padding: 0.75rem;
}
.dashboard-page .btn-secondary {
    background: #333333;
    border-color: #444444;
    color: #ffffff;
}
.dashboard-page .btn {
    background: #ffffff;
    color: #000000;
}

/* Status badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}
.status-confirmed { background: rgba(34,197,94,0.2); color: #22c55e; }
.status-pending { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8); }
.status-checked_in { background: rgba(59,130,246,0.2); color: #3b82f6; }
.status-completed { background: rgba(139,92,246,0.2); color: #a78bfa; }
.status-cancelled { background: rgba(239,68,68,0.2); color: #f87171; }
.status-no_show { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}
.empty-state i {
    font-size: 4rem;
    color: rgba(255,255,255,0.3);
    margin-bottom: 1rem;
}
.empty-state p {
    color: rgba(255,255,255,0.6);
    margin-bottom: 1.5rem;
}

@media (max-width: 480px) {
    .booking-tabs {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .booking-tab {
        white-space: nowrap;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }
    .booking-card-datetime {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    .booking-card-actions {
        flex-direction: column;
    }
    .booking-card-actions .btn {
        width: 100%;
    }
}
</style>

<div class="dashboard-page">
<div class="container">
    <h2 style="font-size:1.5rem;margin-bottom:1.5rem">
        <i class="fas fa-calendar-alt"></i> Mijn Boekingen
    </h2>

    <!-- Booking Tabs -->
    <div class="booking-tabs">
        <button class="booking-tab active" onclick="showTab('upcoming')">
            <i class="fas fa-clock"></i> Aankomend
        </button>
        <button class="booking-tab" onclick="showTab('past')">
            <i class="fas fa-history"></i> Afgelopen
        </button>
        <button class="booking-tab" onclick="showTab('all')">
            <i class="fas fa-list"></i> Alles
        </button>
    </div>

    <?php
    $now = new DateTime();
    $upcoming = [];
    $past = [];

    foreach ($bookings as $booking) {
        $bookingDate = new DateTime($booking['appointment_date'] . ' ' . $booking['appointment_time']);
        if ($bookingDate > $now && !in_array($booking['status'], ['cancelled', 'completed', 'no_show'])) {
            $upcoming[] = $booking;
        } else {
            $past[] = $booking;
        }
    }
    ?>

    <!-- Upcoming Bookings -->
    <div id="tab-upcoming" class="booking-list">
        <?php if (empty($upcoming)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-plus"></i>
                <p>Je hebt geen aankomende afspraken</p>
                <a href="/search" class="btn">
                    <i class="fas fa-plus"></i> Nieuwe afspraak maken
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($upcoming as $booking):
                $checkinUrl = 'https://glamourschedule.nl/checkin/' . $booking['uuid'];
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($checkinUrl);
                $statusLabels = [
                    'pending' => $translations['status_pending'] ?? 'In afwachting',
                    'confirmed' => $translations['status_confirmed'] ?? 'Bevestigd',
                    'checked_in' => $translations['status_checked_in'] ?? 'Ingecheckt',
                    'completed' => $translations['status_completed'] ?? 'Voltooid',
                    'cancelled' => $translations['status_cancelled'] ?? 'Geannuleerd',
                    'no_show' => $translations['status_no_show'] ?? 'Niet verschenen'
                ];
            ?>
                <div class="booking-card">
                    <div class="booking-card-header">
                        <div>
                            <div class="booking-card-business"><?= htmlspecialchars($booking['business_name']) ?></div>
                            <div class="booking-card-service"><?= htmlspecialchars($booking['service_name']) ?></div>
                        </div>
                        <span class="status-badge status-<?= $booking['status'] ?>">
                            <?= $statusLabels[$booking['status']] ?? $booking['status'] ?>
                        </span>
                    </div>

                    <div class="booking-card-datetime">
                        <div class="booking-card-date">
                            <i class="fas fa-calendar"></i>
                            <strong><?= date('D d M Y', strtotime($booking['appointment_date'])) ?></strong>
                        </div>
                        <div class="booking-card-time">
                            <i class="fas fa-clock"></i>
                            <strong><?= date('H:i', strtotime($booking['appointment_time'])) ?></strong>
                        </div>
                        <div class="booking-card-price">
                            <i class="fas fa-euro-sign"></i>
                            <?= number_format($booking['total_price'] ?? 0, 2, ',', '.') ?>
                        </div>
                    </div>

                    <?php if ($booking['payment_status'] === 'paid' && $booking['status'] !== 'checked_in'): ?>
                        <div class="booking-card-qr">
                            <img src="<?= $qrUrl ?>" alt="QR Code">
                            <div class="booking-card-qr-text">
                                <strong>Check-in Code</strong><br>
                                <span style="font-size:1.25rem;font-weight:700;letter-spacing:1px"><?= htmlspecialchars($booking['booking_number']) ?></span><br>
                                <small>Toon QR of noem dit nummer bij aankomst</small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="booking-card-actions">
                        <a href="/booking/<?= $booking['uuid'] ?>" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Details
                        </a>
                        <?php if ($booking['status'] === 'confirmed' || $booking['status'] === 'pending'): ?>
                            <a href="/booking/<?= $booking['uuid'] ?>" class="btn" style="background:var(--primary);color:white">
                                <i class="fas fa-qrcode"></i> QR Code
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Past Bookings -->
    <div id="tab-past" class="booking-list" style="display:none">
        <?php if (empty($past)): ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>Je hebt nog geen afgelopen afspraken</p>
            </div>
        <?php else: ?>
            <?php foreach ($past as $booking):
                $statusLabels = [
                    'pending' => $translations['status_pending'] ?? 'In afwachting',
                    'confirmed' => $translations['status_confirmed'] ?? 'Bevestigd',
                    'checked_in' => $translations['status_checked_in'] ?? 'Ingecheckt',
                    'completed' => $translations['status_completed'] ?? 'Voltooid',
                    'cancelled' => $translations['status_cancelled'] ?? 'Geannuleerd',
                    'no_show' => $translations['status_no_show'] ?? 'Niet verschenen'
                ];
            ?>
                <div class="booking-card">
                    <div class="booking-card-header">
                        <div>
                            <div class="booking-card-business"><?= htmlspecialchars($booking['business_name']) ?></div>
                            <div class="booking-card-service"><?= htmlspecialchars($booking['service_name']) ?></div>
                        </div>
                        <span class="status-badge status-<?= $booking['status'] ?>">
                            <?= $statusLabels[$booking['status']] ?? $booking['status'] ?>
                        </span>
                    </div>

                    <div class="booking-card-datetime">
                        <div class="booking-card-date">
                            <i class="fas fa-calendar"></i>
                            <strong><?= date('D d M Y', strtotime($booking['appointment_date'])) ?></strong>
                        </div>
                        <div class="booking-card-time">
                            <i class="fas fa-clock"></i>
                            <strong><?= date('H:i', strtotime($booking['appointment_time'])) ?></strong>
                        </div>
                        <div class="booking-card-price">
                            <i class="fas fa-euro-sign"></i>
                            <?= number_format($booking['total_price'] ?? 0, 2, ',', '.') ?>
                        </div>
                    </div>

                    <div class="booking-card-actions">
                        <a href="/booking/<?= $booking['uuid'] ?>" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Details
                        </a>
                        <?php if ($booking['status'] === 'completed'): ?>
                            <a href="/business/<?= $booking['business_slug'] ?? '' ?>#reviews" class="btn" style="background:var(--warning);color:white">
                                <i class="fas fa-star"></i> Beoordelen
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- All Bookings -->
    <div id="tab-all" class="booking-list" style="display:none">
        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <p>Je hebt nog geen boekingen</p>
                <a href="/search" class="btn">
                    <i class="fas fa-plus"></i> Nieuwe afspraak maken
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking):
                $statusLabels = [
                    'pending' => $translations['status_pending'] ?? 'In afwachting',
                    'confirmed' => $translations['status_confirmed'] ?? 'Bevestigd',
                    'checked_in' => $translations['status_checked_in'] ?? 'Ingecheckt',
                    'completed' => $translations['status_completed'] ?? 'Voltooid',
                    'cancelled' => $translations['status_cancelled'] ?? 'Geannuleerd',
                    'no_show' => $translations['status_no_show'] ?? 'Niet verschenen'
                ];
            ?>
                <div class="booking-card">
                    <div class="booking-card-header">
                        <div>
                            <div class="booking-card-business"><?= htmlspecialchars($booking['business_name']) ?></div>
                            <div class="booking-card-service"><?= htmlspecialchars($booking['service_name']) ?></div>
                        </div>
                        <span class="status-badge status-<?= $booking['status'] ?>">
                            <?= $statusLabels[$booking['status']] ?? $booking['status'] ?>
                        </span>
                    </div>

                    <div class="booking-card-datetime">
                        <div class="booking-card-date">
                            <i class="fas fa-calendar"></i>
                            <strong><?= date('D d M Y', strtotime($booking['appointment_date'])) ?></strong>
                        </div>
                        <div class="booking-card-time">
                            <i class="fas fa-clock"></i>
                            <strong><?= date('H:i', strtotime($booking['appointment_time'])) ?></strong>
                        </div>
                        <div class="booking-card-price">
                            <i class="fas fa-euro-sign"></i>
                            <?= number_format($booking['total_price'] ?? 0, 2, ',', '.') ?>
                        </div>
                    </div>

                    <div class="booking-card-actions">
                        <a href="/booking/<?= $booking['uuid'] ?>" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<script>
function showTab(tab) {
    // Update tabs
    document.querySelectorAll('.booking-tab').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');

    // Show/hide content
    document.querySelectorAll('.booking-list').forEach(l => l.style.display = 'none');
    document.getElementById('tab-' + tab).style.display = 'block';
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

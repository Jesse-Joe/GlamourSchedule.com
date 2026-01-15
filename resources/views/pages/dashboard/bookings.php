<?php ob_start(); ?>

<style>
/* Booking tabs */
.booking-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--border);
    padding-bottom: 0.5rem;
}
.booking-tab {
    padding: 0.75rem 1.25rem;
    border: none;
    background: none;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--text-light);
    cursor: pointer;
    border-radius: 8px 8px 0 0;
    transition: all 0.2s;
}
.booking-tab.active {
    color: var(--primary);
    background: var(--secondary);
}
.booking-tab:hover:not(.active) {
    color: var(--text);
}

/* Booking cards */
.booking-card {
    background: var(--secondary);
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    border: 2px solid transparent;
    transition: all 0.2s;
}
.booking-card:hover {
    border-color: var(--primary);
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
    color: var(--text);
}
.booking-card-service {
    color: var(--text-light);
    font-size: 0.9rem;
    margin-top: 0.25rem;
}
.booking-card-datetime {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.booking-card-date, .booking-card-time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.booking-card-date i, .booking-card-time i {
    color: var(--primary);
    width: 20px;
}
.booking-card-price {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--primary);
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

/* Status badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}
.status-confirmed { background: #f5f5f5; color: #166534; }
.status-pending { background: #ffffff; color: #000000; }
.status-checked_in { background: #f5f5f5; color: #000000; }
.status-completed { background: #e0e7ff; color: #3730a3; }
.status-cancelled { background: #f5f5f5; color: #000000; }
.status-no_show { background: #ffffff; color: #000000; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}
.empty-state i {
    font-size: 4rem;
    color: var(--text-light);
    opacity: 0.3;
    margin-bottom: 1rem;
}
.empty-state p {
    color: var(--text-light);
    margin-bottom: 1.5rem;
}

/* Dark mode */
[data-theme="dark"] .booking-card {
    background: var(--bg-secondary);
}
[data-theme="dark"] .booking-card-qr {
    background: linear-gradient(135deg, #052e16, #064e3b);
}
[data-theme="dark"] .booking-card-qr-text {
    color: #ffffff;
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
                    'pending' => 'In afwachting',
                    'confirmed' => 'Bevestigd',
                    'checked_in' => 'Ingecheckt',
                    'completed' => 'Voltooid',
                    'cancelled' => 'Geannuleerd',
                    'no_show' => 'Niet verschenen'
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
                    'pending' => 'In afwachting',
                    'confirmed' => 'Bevestigd',
                    'checked_in' => 'Ingecheckt',
                    'completed' => 'Voltooid',
                    'cancelled' => 'Geannuleerd',
                    'no_show' => 'Niet verschenen'
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
                    'pending' => 'In afwachting',
                    'confirmed' => 'Bevestigd',
                    'checked_in' => 'Ingecheckt',
                    'completed' => 'Voltooid',
                    'cancelled' => 'Geannuleerd',
                    'no_show' => 'Niet verschenen'
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

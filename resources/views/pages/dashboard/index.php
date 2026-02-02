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
.dashboard-page h2,
.dashboard-page h3 {
    color: #ffffff;
}
.dashboard-page .card {
    background: #111111;
    border: 1px solid #333333;
    color: #ffffff;
}
.dashboard-page .text-light,
.dashboard-page [style*="text-light"] {
    color: rgba(255,255,255,0.7) !important;
}
.dashboard-page p {
    color: rgba(255,255,255,0.7);
}
.dashboard-page .card h3 {
    color: #ffffff;
}

/* Mobile-friendly dashboard styles */
.quick-links {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-top: 2rem;
}
@media (max-width: 992px) {
    .quick-links {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    .quick-links {
        grid-template-columns: 1fr;
    }
}
.quick-link-card {
    text-decoration: none;
    color: inherit;
}
.quick-link-card .card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.quick-link-card:hover .card {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(255,255,255,0.1);
}

/* Booking cards for mobile */
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
.booking-card-qr {
    background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
    border: 1px solid #333333;
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
    color: #ffffff;
}
.booking-card-qr-text small {
    color: #888888;
}
[data-theme="light"] .booking-card-qr {
    background: linear-gradient(135deg, #ffffff, #f5f5f5);
    border-color: #e0e0e0;
}
[data-theme="light"] .booking-card-qr-text {
    color: #000000;
}
[data-theme="light"] .booking-card-qr-text small {
    color: #666666;
}
.booking-card-actions {
    display: flex;
    gap: 0.5rem;
}
.booking-card-actions .btn {
    flex: 1;
    text-align: center;
    padding: 0.75rem;
}
.dashboard-page .btn-secondary {
    background: #333333;
    border-color: #444444;
    color: #ffffff;
}
.dashboard-page .btn-primary {
    background: #ffffff;
    color: #000000;
}
@media (max-width: 480px) {
    .booking-card-datetime {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<div class="dashboard-page">
<div class="container">
    <h2 style="font-size:1.5rem"><i class="fas fa-tachometer-alt"></i> <?= $__('welcome_user') ?>, <?= htmlspecialchars($user['first_name']) ?>!</h2>

    <div class="quick-links">
        <a href="/dashboard/bookings" class="quick-link-card">
            <div class="card">
                <div style="display:flex;align-items:center;gap:1rem">
                    <div style="width:50px;height:50px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-calendar-alt" style="color:white;font-size:1.3rem"></i>
                    </div>
                    <div>
                        <h3 style="margin:0;font-size:1rem"><?= $__('my_bookings') ?></h3>
                        <p style="color:var(--text-light);margin:0;font-size:0.85rem"><?= $__('view_appointments') ?></p>
                    </div>
                </div>
            </div>
        </a>
        <a href="/dashboard/profile" class="quick-link-card">
            <div class="card">
                <div style="display:flex;align-items:center;gap:1rem">
                    <div style="width:50px;height:50px;background:linear-gradient(135deg,#28a745,#20c997);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-user" style="color:white;font-size:1.3rem"></i>
                    </div>
                    <div>
                        <h3 style="margin:0;font-size:1rem"><?= $__('my_profile') ?></h3>
                        <p style="color:var(--text-light);margin:0;font-size:0.85rem"><?= $__('edit_details') ?></p>
                    </div>
                </div>
            </div>
        </a>
        <a href="/search" class="quick-link-card">
            <div class="card">
                <div style="display:flex;align-items:center;gap:1rem">
                    <div style="width:50px;height:50px;background:linear-gradient(135deg,#fd7e14,#e83e8c);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-search" style="color:white;font-size:1.3rem"></i>
                    </div>
                    <div>
                        <h3 style="margin:0;font-size:1rem"><?= $__('search_salons') ?></h3>
                        <p style="color:var(--text-light);margin:0;font-size:0.85rem"><?= $__('make_new_appointment') ?></p>
                    </div>
                </div>
            </div>
        </a>
        <a href="/dashboard/loyalty" class="quick-link-card">
            <div class="card">
                <div style="display:flex;align-items:center;gap:1rem">
                    <div style="width:50px;height:50px;background:linear-gradient(135deg,#f59e0b,#eab308);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-star" style="color:white;font-size:1.3rem"></i>
                    </div>
                    <div>
                        <h3 style="margin:0;font-size:1rem"><?= $translations['loyalty_points'] ?? 'Loyaliteitspunten' ?></h3>
                        <p style="color:var(--text-light);margin:0;font-size:0.85rem"><?= $translations['view_your_points'] ?? 'Bekijk je punten' ?></p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Upcoming Bookings -->
    <div class="card" style="margin-top:2rem">
        <h3 style="margin-bottom:1.5rem"><i class="fas fa-clock"></i> <?= $__('upcoming_appointments') ?></h3>
        <?php if (empty($upcomingBookings)): ?>
            <div style="text-align:center;padding:2rem 1rem">
                <i class="fas fa-calendar-plus" style="font-size:3rem;color:var(--text-light);opacity:0.5;margin-bottom:1rem;display:block"></i>
                <p style="color:var(--text-light);margin-bottom:1.5rem"><?= $__('no_upcoming_appointments') ?></p>
                <a href="/search" class="btn btn-primary">
                    <i class="fas fa-plus"></i> <?= $__('make_appointment') ?>
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($upcomingBookings as $booking):
                $checkinUrl = 'https://glamourschedule.nl/checkin/' . $booking['uuid'];
                $theme = $_COOKIE['theme'] ?? 'dark';
                $qrColor = $theme === 'light' ? '000000' : 'ffffff';
                $qrBg = $theme === 'light' ? 'ffffff' : '000000';
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&color={$qrColor}&bgcolor={$qrBg}&data=" . urlencode($checkinUrl);
            ?>
                <div class="booking-card">
                    <div class="booking-card-header">
                        <div>
                            <div class="booking-card-business"><?= htmlspecialchars($booking['business_name']) ?></div>
                            <div class="booking-card-service"><?= htmlspecialchars($booking['service_name']) ?></div>
                        </div>
                        <?php if ($booking['status'] === 'checked_in'): ?>
                            <span style="background:#333333;color:white;padding:0.25rem 0.75rem;border-radius:20px;font-size:0.8rem">
                                <i class="fas fa-check"></i> Ingecheckt
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="booking-card-datetime">
                        <div class="booking-card-date">
                            <i class="fas fa-calendar"></i>
                            <strong><?= date('D d M', strtotime($booking['appointment_date'])) ?></strong>
                        </div>
                        <div class="booking-card-time">
                            <i class="fas fa-clock"></i>
                            <strong><?= date('H:i', strtotime($booking['appointment_time'])) ?></strong>
                        </div>
                    </div>

                    <?php if ($booking['payment_status'] === 'paid' && $booking['status'] !== 'checked_in'): ?>
                        <div class="booking-card-qr">
                            <img src="<?= $qrUrl ?>" alt="QR Code">
                            <div class="booking-card-qr-text">
                                <strong>Check-in Code</strong><br>
                                <span style="font-size:1.25rem;font-weight:700;letter-spacing:1px"><?= htmlspecialchars($booking['booking_number']) ?></span><br>
                                <small>Toon QR of noem dit nummer</small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="booking-card-actions">
                        <a href="/booking/<?= $booking['uuid'] ?>" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Bekijken
                        </a>
                        <?php if ($booking['status'] !== 'checked_in' && $booking['status'] !== 'cancelled'): ?>
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
    <?php if (!empty($pastBookings)): ?>
        <div class="card" style="margin-top:1rem">
            <h3><i class="fas fa-history"></i> <?= $__('recent_appointments') ?></h3>
            <div style="margin-top:1rem">
                <?php foreach ($pastBookings as $booking): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 0;border-bottom:1px solid var(--border)">
                        <div>
                            <span><?= htmlspecialchars($booking['business_name']) ?></span>
                            <span style="color:var(--text-light)"> - <?= htmlspecialchars($booking['service_name']) ?></span>
                        </div>
                        <span style="color:var(--text-light)"><?= date('d-m-Y', strtotime($booking['appointment_date'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

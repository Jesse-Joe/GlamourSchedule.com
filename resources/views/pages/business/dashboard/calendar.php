<?php ob_start(); ?>

<?php
$today = date('Y-m-d');
$selectedDate = $selectedDate ?? $today;
$dateObj = new DateTime($selectedDate);
$dayNames = [
    $translations['sunday'] ?? 'Sunday',
    $translations['monday'] ?? 'Monday',
    $translations['tuesday'] ?? 'Tuesday',
    $translations['wednesday'] ?? 'Wednesday',
    $translations['thursday'] ?? 'Thursday',
    $translations['friday'] ?? 'Friday',
    $translations['saturday'] ?? 'Saturday'
];
$dayName = $dayNames[$dateObj->format('w')];
$monthNames = [
    $translations['month_january'] ?? 'January',
    $translations['month_february'] ?? 'February',
    $translations['month_march'] ?? 'March',
    $translations['month_april'] ?? 'April',
    $translations['month_may'] ?? 'May',
    $translations['month_june'] ?? 'June',
    $translations['month_july'] ?? 'July',
    $translations['month_august'] ?? 'August',
    $translations['month_september'] ?? 'September',
    $translations['month_october'] ?? 'October',
    $translations['month_november'] ?? 'November',
    $translations['month_december'] ?? 'December'
];
$monthName = $monthNames[$dateObj->format('n') - 1];
?>

<style>
    .calendar-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 1.5rem;
    }
    @media (max-width: 900px) {
        .calendar-layout {
            grid-template-columns: 1fr;
        }
        .calendar-sidebar {
            order: -1;
        }
    }
    .calendar-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        gap: 0.5rem;
    }
    .calendar-nav-btn {
        padding: 0.75rem 1rem;
        border: 2px solid #333333;
        background: #1a1a1a;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .calendar-nav-btn:hover {
        border-color: #ffffff;
        color: #ffffff;
        background: #333333;
    }
    @media (max-width: 480px) {
        .calendar-nav-btn span {
            display: none;
        }
        .calendar-nav-btn {
            padding: 0.75rem;
        }
    }
    .calendar-date {
        text-align: center;
        flex: 1;
    }
    .calendar-date h2 {
        margin: 0;
        font-size: 1.25rem;
    }
    .calendar-date h2 {
        color: #ffffff;
    }
    .calendar-date p {
        margin: 0.25rem 0 0 0;
        color: #999999;
        font-size: 0.9rem;
    }
    @media (max-width: 480px) {
        .calendar-date h2 {
            font-size: 1.1rem;
        }
        .calendar-date p {
            font-size: 0.8rem;
        }
    }
    .time-grid {
        display: grid;
        gap: 0;
    }
    .time-slot {
        display: grid;
        grid-template-columns: 60px 1fr;
        min-height: 60px;
        border-bottom: 1px solid #333333;
    }
    @media (max-width: 480px) {
        .time-slot {
            grid-template-columns: 50px 1fr;
            min-height: 50px;
        }
    }
    .time-label {
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
        color: #999999;
        border-right: 1px solid #333333;
        text-align: center;
        font-weight: 600;
    }
    .time-content {
        padding: 0.5rem;
        position: relative;
    }
    .booking-block {
        background: linear-gradient(135deg, #ffffff, #e0e0e0);
        color: #000000;
        border-radius: 10px;
        padding: 0.75rem;
        margin-bottom: 0.25rem;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .booking-block:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(255,255,255,0.2);
    }
    .booking-block-title {
        font-weight: 600;
        font-size: 0.9rem;
        color: #000000;
    }
    .booking-block-info {
        font-size: 0.75rem;
        color: #333333;
        margin-top: 0.25rem;
    }
    @media (max-width: 480px) {
        .booking-block {
            padding: 0.5rem;
        }
        .booking-block-title {
            font-size: 0.8rem;
        }
        .booking-block-info {
            font-size: 0.7rem;
        }
    }
    .date-picker {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .quick-dates {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    .quick-dates .btn {
        flex: 1;
        min-width: 80px;
        padding: 0.5rem;
        font-size: 0.85rem;
    }
    .appointment-list-item {
        padding: 1rem;
        border-bottom: 1px solid #333333;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    .appointment-list-item:last-child {
        border-bottom: none;
    }
    .appointment-time-badge {
        background: #ffffff;
        color: #000000;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        white-space: nowrap;
    }
    .appointment-details {
        flex: 1;
    }
    .appointment-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: #ffffff;
    }
    .appointment-service {
        font-size: 0.85rem;
        color: #999999;
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-confirmed { background: #1a1a1a; color: #22c55e; border: 1px solid #22c55e; }
    .status-checked_in { background: #1a1a1a; color: #ffffff; border: 1px solid #ffffff; }
    .status-pending { background: #1a1a1a; color: #f59e0b; border: 1px solid #f59e0b; }

    /* Booking Details Modal */
    .booking-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.6);
        z-index: 1000;
    }
    .booking-modal-overlay.active {
        display: block;
    }
    .booking-modal {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #0a0a0a;
        border: 1px solid #333333;
        border-radius: 24px 24px 0 0;
        max-height: 85vh;
        overflow-y: auto;
        z-index: 1001;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    .booking-modal.active {
        display: block;
        transform: translateY(0);
    }
    .booking-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid #333333;
        position: sticky;
        top: 0;
        background: #0a0a0a;
        z-index: 10;
    }
    .booking-modal-header h3 {
        margin: 0;
        font-size: 1.1rem;
        color: #ffffff;
    }
    .booking-modal-close {
        background: #1a1a1a;
        border: 1px solid #333333;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
    }
    .booking-modal-close:hover {
        background: #333333;
    }
    .booking-modal-content {
        padding: 1.25rem;
    }
    .booking-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #333333;
    }
    .booking-detail-row:last-child {
        border-bottom: none;
    }
    .booking-detail-label {
        color: #999999;
        font-size: 0.9rem;
    }
    .booking-detail-value {
        font-weight: 600;
        text-align: right;
        color: #ffffff;
    }
    .booking-modal-actions {
        display: flex;
        gap: 0.75rem;
        padding: 1.25rem;
        border-top: 1px solid #333333;
        flex-wrap: wrap;
    }
    .booking-modal-actions .btn {
        flex: 1;
        min-width: 120px;
    }
    @media (min-width: 768px) {
        .booking-modal {
            top: 50%;
            left: 50%;
            right: auto;
            bottom: auto;
            transform: translate(-50%, -50%) scale(0.95);
            max-width: 500px;
            width: 100%;
            border-radius: 20px;
            max-height: 80vh;
        }
        .booking-modal.active {
            transform: translate(-50%, -50%) scale(1);
        }
    }
</style>

<div class="calendar-layout">
    <div>
        <!-- Navigation -->
        <div class="calendar-nav">
            <a href="/business/calendar?date=<?= date('Y-m-d', strtotime($selectedDate . ' -1 day')) ?>" class="calendar-nav-btn">
                <i class="fas fa-chevron-left"></i> <span><?= $translations['previous_day'] ?? 'Previous' ?></span>
            </a>

            <div class="calendar-date">
                <h2><?= $dayName ?></h2>
                <p><?= $dateObj->format('d') ?> <?= $monthName ?> <?= $dateObj->format('Y') ?></p>
            </div>

            <a href="/business/calendar?date=<?= date('Y-m-d', strtotime($selectedDate . ' +1 day')) ?>" class="calendar-nav-btn">
                <span><?= $translations['next_day'] ?? 'Next' ?></span> <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <!-- Time Grid -->
        <div class="card" style="padding:0;overflow:hidden">
            <div class="time-grid">
                <?php
                $hours = range(8, 20);
                foreach ($hours as $hour):
                    $timeStart = sprintf('%02d:00', $hour);
                    $timeEnd = sprintf('%02d:00', $hour + 1);

                    // Find bookings for this hour
                    $hourBookings = array_filter($bookings, function($b) use ($hour) {
                        $bookingHour = (int)date('G', strtotime($b['appointment_time']));
                        return $bookingHour === $hour;
                    });
                ?>
                    <div class="time-slot">
                        <div class="time-label">
                            <?= $timeStart ?>
                        </div>
                        <div class="time-content">
                            <?php foreach ($hourBookings as $booking):
                                $customerName = $booking['first_name'] ?? $booking['guest_name'] ?? 'Gast';
                                $customerEmail = $booking['guest_email'] ?? $booking['user_email'] ?? '';
                                $customerPhone = $booking['guest_phone'] ?? $booking['phone'] ?? '';
                                $endTime = !empty($booking['duration_minutes'])
                                    ? date('H:i', strtotime($booking['appointment_time'] . ' +' . $booking['duration_minutes'] . ' minutes'))
                                    : '';
                            ?>
                                <div class="booking-block" onclick="showBookingDetails(this)"
                                     data-id="<?= $booking['id'] ?>"
                                     data-number="<?= htmlspecialchars($booking['booking_number'] ?? '') ?>"
                                     data-customer="<?= htmlspecialchars($customerName) ?>"
                                     data-email="<?= htmlspecialchars($customerEmail) ?>"
                                     data-phone="<?= htmlspecialchars($customerPhone) ?>"
                                     data-service="<?= htmlspecialchars($booking['service_name']) ?>"
                                     data-time="<?= date('H:i', strtotime($booking['appointment_time'])) ?>"
                                     data-endtime="<?= $endTime ?>"
                                     data-price="<?= number_format($booking['total_price'] ?? 0, 2, ',', '.') ?>"
                                     data-status="<?= htmlspecialchars($booking['status'] ?? 'pending') ?>"
                                     data-notes="<?= htmlspecialchars($booking['notes'] ?? '') ?>">
                                    <div class="booking-block-title">
                                        <?= htmlspecialchars($customerName) ?>
                                    </div>
                                    <div class="booking-block-info">
                                        <?= htmlspecialchars($booking['service_name']) ?>
                                        <span style="opacity:0.7">|</span>
                                        <?= date('H:i', strtotime($booking['appointment_time'])) ?>
                                        <?php if ($endTime): ?>
                                            - <?= $endTime ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar"></i> <?= $translations['choose_date'] ?? 'Choose Date' ?></h3>
            </div>

            <form method="GET" action="/business/calendar" class="date-picker">
                <input type="date" name="date" class="form-control" value="<?= $selectedDate ?>" onchange="this.form.submit()">
            </form>

            <div style="margin-top:1rem;display:flex;gap:0.5rem;flex-wrap:wrap">
                <a href="/business/calendar?date=<?= date('Y-m-d') ?>" class="btn btn-secondary btn-sm <?= $selectedDate === date('Y-m-d') ? 'btn-primary' : '' ?>">
                    <?= $translations['today'] ?? 'Today' ?>
                </a>
                <a href="/business/calendar?date=<?= date('Y-m-d', strtotime('tomorrow')) ?>" class="btn btn-secondary btn-sm">
                    <?= $translations['tomorrow'] ?? 'Tomorrow' ?>
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> <?= $translations['appointments'] ?? 'Appointments' ?> (<?= count($bookings) ?>)</h3>
            </div>

            <?php if (empty($bookings)): ?>
                <p class="text-muted text-center" style="padding:1rem 0"><?= $translations['no_appointments'] ?? 'No appointments this day' ?></p>
            <?php else: ?>
                <?php foreach ($bookings as $booking):
                    $sidebarCustomerName = $booking['first_name'] ?? $booking['guest_name'] ?? 'Gast';
                    $sidebarCustomerEmail = $booking['guest_email'] ?? $booking['user_email'] ?? '';
                    $sidebarCustomerPhone = $booking['guest_phone'] ?? $booking['phone'] ?? '';
                    $sidebarEndTime = !empty($booking['duration_minutes'])
                        ? date('H:i', strtotime($booking['appointment_time'] . ' +' . $booking['duration_minutes'] . ' minutes'))
                        : '';
                ?>
                    <div style="padding:0.75rem 0;border-bottom:1px solid var(--border);cursor:pointer" onclick="showBookingDetails(this)"
                         data-id="<?= $booking['id'] ?>"
                         data-number="<?= htmlspecialchars($booking['booking_number'] ?? '') ?>"
                         data-customer="<?= htmlspecialchars($sidebarCustomerName) ?>"
                         data-email="<?= htmlspecialchars($sidebarCustomerEmail) ?>"
                         data-phone="<?= htmlspecialchars($sidebarCustomerPhone) ?>"
                         data-service="<?= htmlspecialchars($booking['service_name']) ?>"
                         data-time="<?= date('H:i', strtotime($booking['appointment_time'])) ?>"
                         data-endtime="<?= $sidebarEndTime ?>"
                         data-price="<?= number_format($booking['total_price'] ?? 0, 2, ',', '.') ?>"
                         data-status="<?= htmlspecialchars($booking['status'] ?? 'pending') ?>"
                         data-notes="<?= htmlspecialchars($booking['notes'] ?? '') ?>">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <strong><?= date('H:i', strtotime($booking['appointment_time'])) ?></strong>
                            <span style="background:<?= ($booking['status'] ?? 'pending') === 'confirmed' ? 'var(--success)' : 'var(--warning)' ?>;color:white;padding:0.2rem 0.5rem;border-radius:10px;font-size:0.7rem">
                                <?= ($booking['status'] ?? 'pending') === 'confirmed' ? ($translations['status_confirmed'] ?? 'Confirmed') : ($translations['status_pending'] ?? 'Pending') ?>
                            </span>
                        </div>
                        <p style="margin:0.25rem 0 0 0;font-size:0.9rem">
                            <?= htmlspecialchars($sidebarCustomerName) ?>
                        </p>
                        <small class="text-muted"><?= htmlspecialchars($booking['service_name']) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="card" style="background:linear-gradient(135deg,#ffffff,#f0f0f0);border:1px solid #333333">
            <h4 style="margin-bottom:0.5rem;color:#000000"><i class="fas fa-info-circle"></i> <?= $translations['calendar_tips'] ?? 'Calendar Tips' ?></h4>
            <p style="font-size:0.85rem;color:#333333"><?= $translations['calendar_tips_text'] ?? 'Keep your calendar up-to-date so customers only see available times when booking.' ?></p>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="booking-modal-overlay" id="bookingOverlay" onclick="closeBookingModal()"></div>
<div class="booking-modal" id="bookingModal">
    <div class="booking-modal-header">
        <h3><i class="fas fa-calendar-check"></i> <?= $translations['appointment_details'] ?? 'Appointment Details' ?></h3>
        <button class="booking-modal-close" onclick="closeBookingModal()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="booking-modal-content">
        <div class="booking-detail-row">
            <span class="booking-detail-label"><?= $translations['booking_number'] ?? 'Booking number' ?></span>
            <span class="booking-detail-value" id="modalNumber">-</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label"><?= $translations['customer'] ?? 'Customer' ?></span>
            <span class="booking-detail-value" id="modalCustomer">-</span>
        </div>
        <div class="booking-detail-row" id="modalEmailRow">
            <span class="booking-detail-label"><?= $translations['email_label'] ?? 'Email' ?></span>
            <span class="booking-detail-value" id="modalEmail">-</span>
        </div>
        <div class="booking-detail-row" id="modalPhoneRow">
            <span class="booking-detail-label"><?= $translations['phone_label'] ?? 'Phone' ?></span>
            <span class="booking-detail-value" id="modalPhone">-</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label"><?= $translations['service'] ?? 'Service' ?></span>
            <span class="booking-detail-value" id="modalService">-</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label"><?= $translations['time'] ?? 'Time' ?></span>
            <span class="booking-detail-value" id="modalTime">-</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label"><?= $translations['price'] ?? 'Price' ?></span>
            <span class="booking-detail-value" id="modalPrice">-</span>
        </div>
        <div class="booking-detail-row">
            <span class="booking-detail-label"><?= $translations['status'] ?? 'Status' ?></span>
            <span class="booking-detail-value" id="modalStatus">-</span>
        </div>
        <div class="booking-detail-row" id="modalNotesRow" style="display:none">
            <span class="booking-detail-label"><?= $translations['notes'] ?? 'Notes' ?></span>
            <span class="booking-detail-value" id="modalNotes" style="max-width:60%;word-break:break-word">-</span>
        </div>
    </div>
    <div class="booking-modal-actions">
        <a href="#" id="modalPhoneBtn" class="btn btn-secondary">
            <i class="fas fa-phone"></i> <?= $translations['call'] ?? 'Call' ?>
        </a>
        <a href="#" id="modalEmailBtn" class="btn btn-secondary">
            <i class="fas fa-envelope"></i> <?= $translations['email'] ?? 'Email' ?>
        </a>
    </div>
</div>

<script>
function showBookingDetails(element) {
    const data = element.dataset;

    // Populate modal
    document.getElementById('modalNumber').textContent = data.number || '-';
    document.getElementById('modalCustomer').textContent = data.customer || '-';

    // Email
    const emailRow = document.getElementById('modalEmailRow');
    const emailBtn = document.getElementById('modalEmailBtn');
    if (data.email) {
        emailRow.style.display = 'flex';
        document.getElementById('modalEmail').textContent = data.email;
        emailBtn.href = 'mailto:' + data.email;
        emailBtn.style.display = 'flex';
    } else {
        emailRow.style.display = 'none';
        emailBtn.style.display = 'none';
    }

    // Phone
    const phoneRow = document.getElementById('modalPhoneRow');
    const phoneBtn = document.getElementById('modalPhoneBtn');
    if (data.phone) {
        phoneRow.style.display = 'flex';
        document.getElementById('modalPhone').textContent = data.phone;
        phoneBtn.href = 'tel:' + data.phone.replace(/\s/g, '');
        phoneBtn.style.display = 'flex';
    } else {
        phoneRow.style.display = 'none';
        phoneBtn.style.display = 'none';
    }

    document.getElementById('modalService').textContent = data.service || '-';

    // Time
    let timeText = data.time || '-';
    if (data.endtime) {
        timeText += ' - ' + data.endtime;
    }
    document.getElementById('modalTime').textContent = timeText;

    document.getElementById('modalPrice').textContent = data.price ? '€' + data.price : '-';

    // Status
    const statusMap = {
        'pending': <?= json_encode($translations['status_pending'] ?? 'Pending') ?>,
        'confirmed': <?= json_encode($translations['status_confirmed'] ?? 'Confirmed') ?>,
        'checked_in': <?= json_encode($translations['status_checked_in'] ?? 'Checked in') ?>,
        'completed': <?= json_encode($translations['status_completed'] ?? 'Completed') ?>,
        'cancelled': <?= json_encode($translations['status_cancelled'] ?? 'Cancelled') ?>,
        'no_show': <?= json_encode($translations['status_no_show'] ?? 'No show') ?>
    };
    const statusEl = document.getElementById('modalStatus');
    statusEl.textContent = statusMap[data.status] || data.status || '-';

    // Status color
    const statusColors = {
        'pending': '#000000',
        'confirmed': '#166534',
        'checked_in': '#000000',
        'completed': '#166534',
        'cancelled': '#000000',
        'no_show': '#000000'
    };
    statusEl.style.color = statusColors[data.status] || 'inherit';

    // Notes
    const notesRow = document.getElementById('modalNotesRow');
    if (data.notes) {
        notesRow.style.display = 'flex';
        document.getElementById('modalNotes').textContent = data.notes;
    } else {
        notesRow.style.display = 'none';
    }

    // Show modal
    document.getElementById('bookingOverlay').classList.add('active');
    document.getElementById('bookingModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeBookingModal() {
    document.getElementById('bookingOverlay').classList.remove('active');
    document.getElementById('bookingModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBookingModal();
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

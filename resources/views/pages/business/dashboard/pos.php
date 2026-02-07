<?php ob_start(); ?>

<?php
$today = date('Y-m-d');
$dayNames = [
    $__('sunday'),
    $__('monday'),
    $__('tuesday'),
    $__('wednesday'),
    $__('thursday'),
    $__('friday'),
    $__('saturday')
];
$monthNames = [
    $__('month_january'),
    $__('month_february'),
    $__('month_march'),
    $__('month_april'),
    $__('month_may'),
    $__('month_june'),
    $__('month_july'),
    $__('month_august'),
    $__('month_september'),
    $__('month_october'),
    $__('month_november'),
    $__('month_december')
];
?>

<style>
/* POS System Styles */
.pos-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 1.5rem;
}
@media (max-width: 1100px) {
    .pos-layout {
        grid-template-columns: 1fr;
    }
}

.pos-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.pos-header h1 {
    margin: 0;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.pos-header h1 i {
    background: linear-gradient(135deg, #000, #333);
    color: white;
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* New Booking Form */
.pos-form-section {
    background: #0a0a0a;
    border: 1px solid #333333;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.pos-form-section h3 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #ffffff;
}
.pos-form-section h3 i {
    color: #ffffff;
}

/* Customer Search */
.customer-search-wrapper {
    position: relative;
}
.customer-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #0a0a0a;
    border: 1px solid #333333;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    z-index: 100;
    display: none;
    max-height: 250px;
    overflow-y: auto;
}
.customer-search-results.show {
    display: block;
}
.customer-result-item {
    padding: 0.875rem 1rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #333333;
    transition: background 0.15s;
    color: #ffffff;
}
.customer-result-item:last-child {
    border-bottom: none;
}
.customer-result-item:hover {
    background: #1a1a1a;
}
.customer-result-item .name {
    font-weight: 600;
    color: #ffffff;
}
.customer-result-item .details {
    font-size: 0.8rem;
    color: #999999;
}
.customer-result-item .badge {
    background: #ffffff;
    color: #000000;
    padding: 0.2rem 0.5rem;
    border-radius: 8px;
    font-size: 0.7rem;
}
.add-new-customer {
    padding: 0.875rem 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #1a1a1a;
    color: #ffffff;
    font-weight: 600;
    border-top: 2px solid #333333;
}
.add-new-customer:hover {
    background: #ffffff;
    color: #000000;
}

/* Selected Customer Badge */
.selected-customer {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
    border: 1px solid #333333;
    padding: 0.875rem 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    color: #ffffff;
}
.selected-customer .avatar {
    width: 40px;
    height: 40px;
    background: #ffffff;
    color: #000000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}
.selected-customer .info {
    flex: 1;
}
.selected-customer .info .name {
    font-weight: 600;
}
.selected-customer .info .contact {
    font-size: 0.8rem;
    color: var(--text-light);
}
.selected-customer .remove {
    background: none;
    border: none;
    color: var(--text-light);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
}
.selected-customer .remove:hover {
    background: rgba(220,38,38,0.1);
    color: #dc2626;
}

/* Service Selection */
.service-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 0.75rem;
}
.service-item {
    background: #1a1a1a;
    border: 2px solid #333333;
    border-radius: 12px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    color: #ffffff;
}
.service-item:hover {
    border-color: #ffffff;
}
.service-item.selected {
    border-color: #ffffff;
    background: #0a0a0a;
}
.service-item .name {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #ffffff;
}
.service-item .meta {
    font-size: 0.8rem;
    color: #999999;
    display: flex;
    gap: 0.5rem;
}
.service-item .price {
    color: #ffffff;
    font-weight: 700;
}

/* Date Time Row */
.datetime-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
@media (max-width: 500px) {
    .datetime-row {
        grid-template-columns: 1fr;
    }
}

/* Payment Method */
.payment-methods {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}
.payment-method {
    background: #1a1a1a;
    border: 2px solid #333333;
    border-radius: 12px;
    padding: 1.25rem;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s;
    color: #ffffff;
}
.payment-method:hover {
    border-color: #ffffff;
}
.payment-method.selected {
    border-color: #ffffff;
    background: #0a0a0a;
}
.payment-method i {
    font-size: 1.5rem;
    color: #ffffff;
    margin-bottom: 0.5rem;
}
.payment-method .title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #ffffff;
}
.payment-method .desc {
    font-size: 0.75rem;
    color: #999999;
}

/* Today's Bookings */
.bookings-list {
    max-height: 500px;
    overflow-y: auto;
}
.booking-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #333333;
    align-items: flex-start;
}
.booking-item:last-child {
    border-bottom: none;
}
.booking-time {
    background: linear-gradient(135deg, #000, #333);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.9rem;
    white-space: nowrap;
}
.booking-details {
    flex: 1;
}
.booking-customer {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #ffffff;
}
.booking-service {
    font-size: 0.85rem;
    color: #999999;
}
.booking-badges {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
    flex-wrap: wrap;
}
.booking-badge {
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
}
.badge-payment-pending { background: #fef3c7; color: #92400e; }
.badge-payment-paid { background: #d1fae5; color: #065f46; }
.badge-status-pending { background: #e5e5e5; color: #333; }
.badge-status-confirmed { background: #d1fae5; color: #065f46; }
.badge-status-completed { background: #dbeafe; color: #1e40af; }
.badge-status-cancelled { background: #fee2e2; color: #991b1b; }
.badge-cash { background: #fef3c7; color: #92400e; }
.badge-online { background: #dbeafe; color: #1e40af; }

.booking-actions {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.booking-action-btn {
    background: #1a1a1a;
    border: 1px solid #333333;
    padding: 0.4rem 0.6rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    white-space: nowrap;
    color: #ffffff;
}
.booking-action-btn:hover {
    background: #ffffff;
    color: #000000;
    border-color: #ffffff;
}
.booking-action-btn.danger:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
}

/* Summary Card */
.summary-card {
    background: linear-gradient(135deg, #000, #333);
    color: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-top: 1rem;
}
.summary-card h4 {
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.summary-row:last-child {
    border-bottom: none;
}
.summary-row.total {
    font-size: 1.25rem;
    font-weight: 700;
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 2px solid rgba(255,255,255,0.2);
}

/* Modal Styles */
.pos-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
}
.pos-modal-overlay.active {
    display: block;
}
.pos-modal {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #0a0a0a;
    border-radius: 24px 24px 0 0;
    max-height: 90vh;
    overflow-y: auto;
    z-index: 1001;
    transform: translateY(100%);
    transition: transform 0.3s ease;
    border: 1px solid #333333;
    color: #ffffff;
}
.pos-modal.active {
    display: block;
    transform: translateY(0);
}
@media (min-width: 768px) {
    .pos-modal {
        top: 50%;
        left: 50%;
        right: auto;
        bottom: auto;
        transform: translate(-50%, -50%) scale(0.95);
        max-width: 500px;
        width: 100%;
        border-radius: 20px;
        max-height: 85vh;
    }
    .pos-modal.active {
        transform: translate(-50%, -50%) scale(1);
    }
}
.pos-modal-header {
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
.pos-modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #ffffff;
}
.pos-modal-close {
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
.pos-modal-close:hover {
    background: #ffffff;
    color: #000000;
}
.pos-modal-content {
    padding: 1.25rem;
}
.pos-modal-actions {
    display: flex;
    gap: 0.75rem;
    padding: 1.25rem;
    border-top: 1px solid #333333;
}
.pos-modal-actions .btn {
    flex: 1;
}

/* Success Animation */
.success-animation {
    text-align: center;
    padding: 2rem;
}
.success-animation .checkmark {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2.5rem;
    color: white;
    animation: scaleIn 0.3s ease;
}
@keyframes scaleIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
.payment-link-box {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 1rem;
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border: 1px solid #333333;
}
.payment-link-box input {
    flex: 1;
    background: #0a0a0a;
    border: 1px solid #333333;
    padding: 0.75rem;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #ffffff;
}
.copy-btn {
    background: #ffffff;
    color: #000000;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}
.copy-btn:hover {
    background: #e0e0e0;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #999999;
}
.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}
.empty-state p {
    margin: 0;
}

/* Digital POS: QR Code Styles */
.qr-code-section {
    background: #ffffff;
    border: 2px dashed #333333;
    border-radius: 16px;
    padding: 1.5rem;
    margin: 1rem 0;
    text-align: center;
}
.qr-code-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
    color: #000000;
}
.qr-code-header i {
    font-size: 1.25rem;
    color: #000000;
}
.qr-code-display {
    display: flex;
    justify-content: center;
    margin-bottom: 0.75rem;
}
.qr-code-display canvas,
.qr-code-display img {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.qr-code-hint {
    font-size: 0.8rem;
    color: #333333;
    margin: 0;
}
.qr-code-section .enlarge-btn {
    margin-top: 0.75rem;
    background: #000000;
    color: #ffffff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
}
.qr-code-section .enlarge-btn:hover {
    background: #333333;
}

/* Digital Share Options */
.digital-share-options {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
}
.share-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border: 1px solid #333333;
    border-radius: 10px;
    background: #1a1a1a;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s;
    color: #ffffff;
}
.share-btn:hover {
    border-color: #ffffff;
    background: #ffffff;
    color: #000000;
}
.share-btn i {
    font-size: 1.1rem;
}
.share-btn .fa-whatsapp { color: #25D366; }
.share-btn .fa-sms { color: #4A90D9; }
.share-btn .fa-print { color: #999999; }
.share-btn:hover .fa-print { color: #333333; }

/* Full Screen QR Modal */
.fullscreen-qr-modal {
    max-width: 600px;
}
.fullscreen-qr-modal #fullQrCodeContainer {
    width: 280px;
    height: 280px;
    background: #ffffff;
    border-radius: 16px;
    padding: 1rem;
}
.fullscreen-qr-modal #fullQrCodeContainer canvas,
.fullscreen-qr-modal #fullQrCodeContainer img {
    width: 100% !important;
    height: 100% !important;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}
.full-qr-amount {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 0.5rem;
}

/* Payment Status Indicator */
.payment-status-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-top: 1.5rem;
    padding: 1rem;
    background: #1a1a1a;
    border-radius: 12px;
    border: 1px solid #333333;
    color: #ffffff;
}
.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    animation: pulse 1.5s infinite;
}
.status-dot.pending {
    background: #f59e0b;
}
.status-dot.paid {
    background: #22c55e;
    animation: none;
}
.status-dot.failed {
    background: #ef4444;
    animation: none;
}
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.9); }
}
</style>

<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

<div class="pos-header">
    <h1 style="color:#ffffff">
        <i class="fas fa-cash-register"></i>
        <?= $__('pos_system') ?>
    </h1>
    <span style="color:#999999">
        <i class="fas fa-calendar"></i>
        <?= $dayNames[date('w')] ?>, <?= $formatDate(date('Y-m-d')) ?>
    </span>
</div>

<div class="pos-layout">
    <!-- Left Column: New Booking Form -->
    <div>
        <!-- Customer Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-user"></i> <?= $__('customer') ?></h3>

            <div id="selectedCustomerBadge" style="display:none" class="selected-customer">
                <div class="avatar" id="customerAvatar">J</div>
                <div class="info">
                    <div class="name" id="customerName">Jan de Vries</div>
                    <div class="contact" id="customerContact">jan@email.nl</div>
                </div>
                <button class="remove" onclick="clearSelectedCustomer()" title="<?= $__('remove') ?>">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="customerSearchSection">
                <div class="customer-search-wrapper">
                    <input type="text"
                           class="form-control"
                           id="customerSearch"
                           placeholder="<?= $__('search_customer_placeholder') ?>"
                           autocomplete="off"
                           style="background:#1a1a1a;color:#ffffff;border-color:#333333">
                    <div class="customer-search-results" id="customerResults">
                        <!-- Results will be populated via JS -->
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-top:0.75rem">
                    <input type="email" class="form-control" id="newCustomerEmail" placeholder="<?= $__('email_optional') ?>" style="background:#1a1a1a;color:#ffffff;border-color:#333333">
                    <input type="tel" class="form-control" id="newCustomerPhone" placeholder="<?= $__('phone_optional') ?>" style="background:#1a1a1a;color:#ffffff;border-color:#333333">
                </div>
            </div>

            <input type="hidden" id="selectedCustomerId" value="">
        </div>

        <!-- Service Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-concierge-bell"></i> <?= $__('service') ?></h3>

            <?php if (empty($services)): ?>
                <p style="color:#999999"><?= $__('no_services_found') ?> <a href="/business/services" style="color:#ffffff"><?= $__('add_services') ?></a></p>
            <?php else: ?>
                <div class="service-grid">
                    <?php foreach ($services as $service): ?>
                        <div class="service-item"
                             data-id="<?= $service['id'] ?>"
                             data-name="<?= htmlspecialchars($service['name']) ?>"
                             data-price="<?= $service['price'] ?>"
                             data-duration="<?= $service['duration_minutes'] ?>"
                             onclick="selectService(this)">
                            <div class="name"><?= htmlspecialchars($service['name']) ?></div>
                            <div class="meta">
                                <span class="price">€<?= number_format($service['price'], 2, ',', '.') ?></span>
                                <span><?= $service['duration_minutes'] ?> min</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <input type="hidden" id="selectedServiceId" value="">
        </div>

        <?php if (!empty($employees)): ?>
        <!-- Employee Section (only for BV) -->
        <div class="pos-form-section">
            <h3><i class="fas fa-user-tie"></i> <?= $__('employee') ?></h3>
            <select class="form-control" id="selectedEmployee" style="background:#1a1a1a;color:#ffffff;border-color:#333333">
                <option value="">-- <?= $__('no_specific_employee') ?> --</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- Date & Time Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-clock"></i> <?= $__('date_and_time') ?></h3>
            <div class="datetime-row">
                <div>
                    <label class="form-label" style="color:#ffffff"><?= $__('date') ?></label>
                    <input type="date" class="form-control" id="appointmentDate" value="<?= $today ?>" min="<?= $today ?>" style="background:#1a1a1a;color:#ffffff;border-color:#333333">
                </div>
                <div>
                    <label class="form-label" style="color:#ffffff"><?= $__('time') ?></label>
                    <input type="time" class="form-control" id="appointmentTime" value="<?= date('H:00', strtotime('+1 hour')) ?>" style="background:#1a1a1a;color:#ffffff;border-color:#333333">
                </div>
            </div>
        </div>

        <!-- Payment Method Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-credit-card"></i> <?= $__('payment_method') ?></h3>
            <div class="payment-methods">
                <div class="payment-method selected" data-method="online" onclick="selectPaymentMethod(this)">
                    <i class="fas fa-globe"></i>
                    <div class="title"><?= $__('online_payment') ?></div>
                    <div class="desc"><?= $__('online_payment_desc') ?></div>
                </div>
                <div class="payment-method" data-method="cash" onclick="selectPaymentMethod(this)">
                    <i class="fas fa-money-bill-wave"></i>
                    <div class="title"><?= $__('cash_at_appointment') ?></div>
                    <div class="desc"><?= str_replace(':fee', $feeData['fee_display'] ?? '€1.75', $__('cash_payment_desc')) ?></div>
                </div>
            </div>
            <input type="hidden" id="selectedPaymentMethod" value="online">
        </div>

        <!-- Notes Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-sticky-note"></i> <?= $__('notes_optional') ?></h3>
            <textarea class="form-control" id="bookingNotes" rows="2" placeholder="<?= $__('notes_placeholder') ?>" style="background:#1a1a1a;color:#ffffff;border-color:#333333"></textarea>
        </div>

        <!-- Summary & Submit -->
        <div class="summary-card">
            <h4><i class="fas fa-receipt"></i> <?= $__('overview') ?></h4>
            <div class="summary-row">
                <span><?= $__('customer') ?></span>
                <span id="summaryCustomer">-</span>
            </div>
            <div class="summary-row">
                <span><?= $__('service') ?></span>
                <span id="summaryService">-</span>
            </div>
            <div class="summary-row">
                <span><?= $__('date_and_time') ?></span>
                <span id="summaryDateTime">-</span>
            </div>
            <div class="summary-row">
                <span><?= $__('payment_method') ?></span>
                <span id="summaryPayment"><?= $__('online') ?></span>
            </div>
            <div class="summary-row">
                <span><?= $__('online_amount') ?></span>
                <span id="summaryOnlineAmount">€0,00</span>
            </div>
            <div class="summary-row total">
                <span><?= $__('total') ?></span>
                <span id="summaryTotal">€0,00</span>
            </div>

            <button class="btn btn-primary" style="width:100%;margin-top:1.25rem;padding:1rem" onclick="createBooking()" id="createBookingBtn">
                <i class="fas fa-plus-circle"></i> <?= $__('create_appointment_send_link') ?>
            </button>
        </div>
    </div>

    <!-- Right Column: Today's Bookings -->
    <div>
        <div class="card" style="padding:0;background:#0a0a0a;border:1px solid #333333">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid #333333;display:flex;justify-content:space-between;align-items:center">
                <h3 style="margin:0;font-size:1rem;color:#ffffff">
                    <i class="fas fa-calendar-day"></i> <?= $__('today') ?>
                </h3>
                <span class="badge" style="background:#ffffff;color:#000000"><?= count($todayBookings) ?> <?= $__('appointments') ?></span>
            </div>

            <div class="bookings-list">
                <?php if (empty($todayBookings)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-plus"></i>
                        <p><?= $__('no_appointments_today') ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($todayBookings as $booking): ?>
                        <div class="booking-item" data-uuid="<?= $booking['uuid'] ?>">
                            <div class="booking-time"><?= $formatTime($booking['appointment_time']) ?></div>
                            <div class="booking-details">
                                <div class="booking-customer"><?= htmlspecialchars($booking['customer_name']) ?></div>
                                <div class="booking-service"><?= htmlspecialchars($booking['service_name']) ?></div>
                                <div class="booking-badges">
                                    <span class="booking-badge badge-<?= $booking['payment_method'] ?>">
                                        <?= $booking['payment_method'] === 'cash' ? $__('booking_payment_cash') : $__('booking_payment_online') ?>
                                    </span>
                                    <span class="booking-badge badge-payment-<?= $booking['payment_status'] ?>">
                                        <?= $booking['payment_status'] === 'paid' ? $__('booking_payment_paid') : $__('booking_payment_pending') ?>
                                    </span>
                                    <span class="booking-badge badge-status-<?= $booking['booking_status'] ?>">
                                        <?php
                                        $statusLabels = [
                                            'pending' => $__('status_pending'),
                                            'confirmed' => $__('status_confirmed'),
                                            'completed' => $__('status_completed'),
                                            'cancelled' => $__('status_cancelled'),
                                            'no_show' => $__('status_no_show')
                                        ];
                                        echo $statusLabels[$booking['booking_status']] ?? $booking['booking_status'];
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="booking-actions">
                                <?php if ($booking['payment_status'] !== 'paid' && !empty($booking['customer_email'])): ?>
                                    <button class="booking-action-btn" onclick="resendPaymentLink('<?= $booking['uuid'] ?>')">
                                        <i class="fas fa-paper-plane"></i> Link
                                    </button>
                                <?php endif; ?>
                                <?php if ($booking['booking_status'] === 'confirmed'): ?>
                                    <button class="booking-action-btn" onclick="markCompleted('<?= $booking['uuid'] ?>')">
                                        <i class="fas fa-check"></i> <?= $__('completed') ?>
                                    </button>
                                <?php endif; ?>
                                <?php if ($booking['booking_status'] !== 'cancelled' && $booking['booking_status'] !== 'completed'): ?>
                                    <button class="booking-action-btn danger" onclick="cancelBooking('<?= $booking['uuid'] ?>')">
                                        <i class="fas fa-times"></i> <?= $__('cancel') ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Customers -->
        <?php if (!empty($recentCustomers)): ?>
        <div class="card" style="margin-top:1rem;background:#0a0a0a;border:1px solid #333333">
            <div class="card-header">
                <h3 class="card-title" style="font-size:0.95rem;color:#ffffff"><i class="fas fa-users"></i> <?= $__('recent_customers') ?></h3>
            </div>
            <?php foreach (array_slice($recentCustomers, 0, 5) as $customer): ?>
                <div style="padding:0.75rem 0;border-bottom:1px solid #333333;cursor:pointer;color:#ffffff"
                     onclick="quickSelectCustomer(<?= (int)$customer['id'] ?>, <?= json_encode($customer['name'] ?? '') ?>, <?= json_encode($customer['email'] ?? '') ?>, <?= json_encode($customer['phone'] ?? '') ?>)">
                    <strong><?= htmlspecialchars($customer['name'] ?? '') ?></strong> <span style="color:#999999;font-size:0.75rem">#<?= (int)$customer['id'] ?></span>
                    <?php if (!empty($customer['email'])): ?>
                        <br><small style="color:#999999"><?= htmlspecialchars($customer['email']) ?></small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Help Card -->
        <div class="card" style="background:linear-gradient(135deg, #000, #333);color:white;margin-top:1rem">
            <h4 style="margin-bottom:0.5rem"><i class="fas fa-info-circle"></i> <?= $__('pos_tips') ?></h4>
            <ul style="margin:0;padding-left:1.25rem;font-size:0.85rem;opacity:0.9">
                <li><?= str_replace(':fee', $feeData['fee_display'] ?? '€1.75', $__('pos_tip_1')) ?></li>
                <li><?= $__('pos_tip_2') ?></li>
                <li><?= $__('pos_tip_3') ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- Success Modal with Digital POS Features -->
<div class="pos-modal-overlay" id="successOverlay" onclick="closeSuccessModal()"></div>
<div class="pos-modal" id="successModal">
    <div class="pos-modal-header">
        <h3><i class="fas fa-check-circle" style="color:#22c55e"></i> <?= $__('appointment_created') ?></h3>
        <button class="pos-modal-close" onclick="closeSuccessModal()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="pos-modal-content">
        <div class="success-animation">
            <div class="checkmark"><i class="fas fa-check"></i></div>
            <h3 style="margin:0 0 0.5rem 0;color:#ffffff" id="successTitle"><?= $__('appointment_created_success') ?></h3>
            <p style="color:#999999" id="successSubtitle"><?= $__('payment_link_sent') ?></p>
        </div>

        <!-- Digital POS: QR Code for instant payment -->
        <div class="qr-code-section">
            <div class="qr-code-header">
                <i class="fas fa-qrcode"></i>
                <span><?= $__('scan_to_pay') ?></span>
            </div>
            <div id="qrCodeContainer" class="qr-code-display"></div>
            <p class="qr-code-hint"><?= $__('qr_scan_hint') ?></p>
        </div>

        <div class="payment-link-box">
            <input type="text" id="paymentLinkInput" readonly value="">
            <button class="copy-btn" onclick="copyPaymentLink()">
                <i class="fas fa-copy"></i> <?= $__('copy') ?>
            </button>
        </div>

        <div style="background:#1a1a1a;border-radius:12px;padding:1rem;margin-top:1rem;border:1px solid #333333">
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;color:#999999">
                <span><?= $__('customer') ?></span>
                <strong id="successCustomer" style="color:#ffffff">-</strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;color:#999999">
                <span><?= $__('service') ?></span>
                <strong id="successService" style="color:#ffffff">-</strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;color:#999999">
                <span><?= $__('date_and_time') ?></span>
                <strong id="successDateTime" style="color:#ffffff">-</strong>
            </div>
            <div style="display:flex;justify-content:space-between;color:#999999">
                <span><?= $__('online_amount') ?></span>
                <strong id="successAmount" style="color:#ffffff">-</strong>
            </div>
        </div>

        <!-- Digital POS: Share Options -->
        <div class="digital-share-options">
            <button class="share-btn" onclick="shareViaWhatsApp()">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </button>
            <button class="share-btn" onclick="shareViaSMS()">
                <i class="fas fa-sms"></i> SMS
            </button>
            <button class="share-btn" onclick="printReceipt()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
    <div class="pos-modal-actions">
        <button class="btn btn-secondary" onclick="closeSuccessModal()"><?= $__('close') ?></button>
        <button class="btn btn-primary" onclick="sendPaymentLinkManual()">
            <i class="fas fa-paper-plane"></i> <?= $__('send_email') ?>
        </button>
    </div>
</div>

<!-- Digital POS: Full Screen QR Modal (for customer facing display) -->
<div class="pos-modal-overlay" id="fullQrOverlay" onclick="closeFullQrModal()"></div>
<div class="pos-modal fullscreen-qr-modal" id="fullQrModal">
    <div class="pos-modal-header">
        <h3><i class="fas fa-qrcode"></i> <?= $__('scan_to_pay') ?></h3>
        <button class="pos-modal-close" onclick="closeFullQrModal()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="pos-modal-content" style="text-align:center;padding:2rem">
        <div id="fullQrCodeContainer" style="margin:0 auto 1.5rem"></div>
        <div class="full-qr-amount" id="fullQrAmount">-</div>
        <p style="color:#999999;margin:0"><?= $__('scan_phone_to_pay') ?></p>

        <!-- Payment status indicator -->
        <div class="payment-status-indicator" id="paymentStatusIndicator">
            <div class="status-dot pending"></div>
            <span><?= $__('waiting_for_payment') ?></span>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="pos-modal-overlay" id="addCustomerOverlay" onclick="closeAddCustomerModal()"></div>
<div class="pos-modal" id="addCustomerModal">
    <div class="pos-modal-header">
        <h3><i class="fas fa-user-plus"></i> <?= $__('new_customer') ?></h3>
        <button class="pos-modal-close" onclick="closeAddCustomerModal()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="pos-modal-content">
        <div style="margin-bottom:1rem">
            <label class="form-label" style="color:#ffffff"><?= $__('name') ?> *</label>
            <input type="text" class="form-control" id="newCustomerName" placeholder="<?= $__('full_name') ?>" style="background:#1a1a1a;color:#ffffff;border-color:#333333">
        </div>
        <div style="margin-bottom:1rem">
            <label class="form-label" style="color:#ffffff"><?= $__('email') ?></label>
            <input type="email" class="form-control" id="modalCustomerEmail" placeholder="<?= $__('email_placeholder') ?>" style="background:#1a1a1a;color:#ffffff;border-color:#333333">
        </div>
        <div style="margin-bottom:1rem">
            <label class="form-label" style="color:#ffffff"><?= $__('phone') ?></label>
            <input type="tel" class="form-control" id="modalCustomerPhone" placeholder="<?= $__('phone_placeholder') ?>" style="background:#1a1a1a;color:#ffffff;border-color:#333333">
        </div>
        <div>
            <label class="form-label" style="color:#ffffff"><?= $__('notes') ?></label>
            <textarea class="form-control" id="modalCustomerNotes" rows="2" placeholder="<?= $__('optional_notes') ?>" style="background:#1a1a1a;color:#ffffff;border-color:#333333"></textarea>
        </div>
    </div>
    <div class="pos-modal-actions">
        <button class="btn btn-secondary" onclick="closeAddCustomerModal()"><?= $__('cancel') ?></button>
        <button class="btn btn-primary" onclick="addNewCustomer()">
            <i class="fas fa-plus"></i> <?= $__('add_customer') ?>
        </button>
    </div>
</div>

<script>
const csrfToken = '<?= $csrfToken ?>';
const T = <?= json_encode([
    'visits' => $__('visits'),
    'add_new_customer' => '<i class="fas fa-plus"></i> ' . $__('add_new_customer'),
    'guest' => $__('guest'),
    'no_contact_details' => $__('no_contact_details'),
    'at' => $__('at'),
    'cash' => $__('cash'),
    'online' => $__('online'),
    'enter_customer_name' => $__('enter_customer_name'),
    'select_service' => $__('select_service'),
    'select_date_time' => $__('select_date_time'),
    'processing' => '<i class="fas fa-spinner fa-spin"></i> ' . $__('processing'),
    'create_appointment_send_link' => $__('create_appointment_send_link'),
    'error_occurred' => $__('error_occurred'),
    'copied' => '<i class="fas fa-check"></i> ' . $__('copied'),
    'could_not_send_link' => $__('could_not_send_link'),
    'error_sending' => $__('error_sending'),
    'enter_name' => $__('enter_name'),
    'could_not_add_customer' => $__('could_not_add_customer'),
    'something_went_wrong' => $__('something_went_wrong'),
    'qr_not_available' => $__('qr_not_available'),
    'qr_loading' => $__('qr_loading'),
    'whatsapp_template' => $__('pos_whatsapp_template'),
    'sms_template' => $__('pos_sms_template'),
    'receipt_confirmation' => $__('receipt_confirmation'),
    'receipt_customer' => $__('receipt_customer'),
    'receipt_service' => $__('receipt_service'),
    'receipt_datetime' => $__('receipt_datetime'),
    'receipt_to_pay' => $__('receipt_to_pay'),
    'receipt_scan_to_pay' => $__('receipt_scan_to_pay'),
    'receipt_payment_link' => $__('receipt_payment_link'),
    'receipt_thanks' => $__('receipt_thanks'),
    'payment_received' => $__('payment_received'),
    'enlarge_for_customer' => '<i class="fas fa-expand"></i> ' . $__('enlarge_for_customer'),
]) ?>;
const dateCountry = '<?= $dateFormatter->getCountry() ?>';
const dateFormatPHP = '<?= addslashes($dateFormatter->formatDate('2000-01-02')) ?>';
const timeIs12h = <?= in_array($dateFormatter->getCountry(), ['US','PH','GB','IE','AU','NZ','IN','PK','SA','AE','EG','CA']) ? 'true' : 'false' ?>;
let selectedService = null;
let selectedCustomer = null;
let lastCreatedBookingUuid = null;

// Customer Search
let searchTimeout;
document.getElementById('customerSearch').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();

    if (query.length < 2) {
        document.getElementById('customerResults').classList.remove('show');
        return;
    }

    searchTimeout = setTimeout(() => searchCustomers(query), 300);
});

function searchCustomers(query) {
    fetch('/business/pos/customers?q=' + encodeURIComponent(query))
        .then(r => r.json())
        .then(data => {
            const results = document.getElementById('customerResults');
            results.innerHTML = '';

            data.customers.forEach(c => {
                const div = document.createElement('div');
                div.className = 'customer-result-item';

                // Build content safely without innerHTML for user data
                const infoDiv = document.createElement('div');
                const nameDiv = document.createElement('div');
                nameDiv.className = 'name';
                nameDiv.textContent = c.name || '';
                const idSpan = document.createElement('span');
                idSpan.style.cssText = 'color:var(--text-light);font-size:0.75rem;font-weight:normal';
                idSpan.textContent = ' #' + c.id;
                nameDiv.appendChild(idSpan);

                const detailsDiv = document.createElement('div');
                detailsDiv.className = 'details';
                detailsDiv.textContent = (c.email || '') + (c.phone ? ' • ' + c.phone : '');

                infoDiv.appendChild(nameDiv);
                infoDiv.appendChild(detailsDiv);
                div.appendChild(infoDiv);

                if (c.total_appointments > 0) {
                    const badge = document.createElement('span');
                    badge.className = 'badge';
                    badge.textContent = c.total_appointments + ' ' + T.visits;
                    div.appendChild(badge);
                }

                div.onclick = () => selectCustomer(c);
                results.appendChild(div);
            });

            // Add "New Customer" option
            const addNew = document.createElement('div');
            addNew.className = 'add-new-customer';
            addNew.innerHTML = T.add_new_customer;
            addNew.onclick = () => openAddCustomerModal(query);
            results.appendChild(addNew);

            results.classList.add('show');
        })
        .catch(err => {
            console.error('Customer search error:', err);
            const results = document.getElementById('customerResults');
            if (results) results.classList.remove('show');
        });
}

function selectCustomer(customer) {
    selectedCustomer = customer;
    document.getElementById('selectedCustomerId').value = customer.id;
    document.getElementById('customerAvatar').textContent = (customer.name || 'G').charAt(0).toUpperCase();
    // Safe way to set customer name with ID span
    const nameEl = document.getElementById('customerName');
    nameEl.textContent = '';
    nameEl.appendChild(document.createTextNode(customer.name || T.guest));
    const idSpan = document.createElement('span');
    idSpan.style.cssText = 'color:var(--text-light);font-size:0.8rem;font-weight:normal';
    idSpan.textContent = ' #' + customer.id;
    nameEl.appendChild(idSpan);
    document.getElementById('customerContact').textContent = [customer.email, customer.phone].filter(Boolean).join(' • ') || T.no_contact_details;

    document.getElementById('selectedCustomerBadge').style.display = 'flex';
    document.getElementById('customerSearchSection').style.display = 'none';
    document.getElementById('customerResults').classList.remove('show');

    updateSummary();
}

function clearSelectedCustomer() {
    selectedCustomer = null;
    document.getElementById('selectedCustomerId').value = '';
    document.getElementById('selectedCustomerBadge').style.display = 'none';
    document.getElementById('customerSearchSection').style.display = 'block';
    document.getElementById('customerSearch').value = '';
    document.getElementById('newCustomerEmail').value = '';
    document.getElementById('newCustomerPhone').value = '';
    updateSummary();
}

function quickSelectCustomer(id, name, email, phone) {
    selectCustomer({ id, name, email, phone, total_appointments: 0 });
}

// Service Selection
function selectService(element) {
    document.querySelectorAll('.service-item').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');

    selectedService = {
        id: element.dataset.id,
        name: element.dataset.name,
        price: parseFloat(element.dataset.price),
        duration: parseInt(element.dataset.duration)
    };

    document.getElementById('selectedServiceId').value = selectedService.id;
    updateSummary();
}

// Payment Method Selection
function selectPaymentMethod(element) {
    document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    document.getElementById('selectedPaymentMethod').value = element.dataset.method;
    updateSummary();
}

// Update Summary
function updateSummary() {
    const customerName = selectedCustomer ? selectedCustomer.name : (document.getElementById('customerSearch').value || '-');
    const serviceName = selectedService ? selectedService.name : '-';
    const servicePrice = selectedService ? selectedService.price : 0;
    const paymentMethod = document.getElementById('selectedPaymentMethod').value;
    const date = document.getElementById('appointmentDate').value;
    const time = document.getElementById('appointmentTime').value;

    document.getElementById('summaryCustomer').textContent = customerName;
    document.getElementById('summaryService').textContent = serviceName;
    document.getElementById('summaryDateTime').textContent = date && time ? formatDate(date) + ' ' + T.at + ' ' + formatTime(time) : '-';
    document.getElementById('summaryPayment').textContent = paymentMethod === 'cash' ? T.cash : T.online;

    const platformFee = <?= $feeData['fee_amount'] ?? 1.75 ?>;
    const currencySymbol = '<?= $feeData['currency_symbol'] ?? '€' ?>';
    const onlineAmount = paymentMethod === 'cash' ? platformFee : servicePrice;
    document.getElementById('summaryOnlineAmount').textContent = currencySymbol + onlineAmount.toFixed(2).replace('.', ',');
    document.getElementById('summaryTotal').textContent = currencySymbol + servicePrice.toFixed(2).replace('.', ',');
}

// Listen for date/time changes
document.getElementById('appointmentDate').addEventListener('change', updateSummary);
document.getElementById('appointmentTime').addEventListener('change', updateSummary);

// Create Booking
function createBooking() {
    const customerName = selectedCustomer ? selectedCustomer.name : document.getElementById('customerSearch').value.trim();
    const customerEmail = selectedCustomer ? selectedCustomer.email : document.getElementById('newCustomerEmail').value.trim();
    const customerPhone = selectedCustomer ? selectedCustomer.phone : document.getElementById('newCustomerPhone').value.trim();
    const serviceId = document.getElementById('selectedServiceId').value;
    const employeeId = document.getElementById('selectedEmployee')?.value || '';
    const date = document.getElementById('appointmentDate').value;
    const time = document.getElementById('appointmentTime').value;
    const paymentMethod = document.getElementById('selectedPaymentMethod').value;
    const notes = document.getElementById('bookingNotes').value.trim();

    if (!customerName) {
        alert(T.enter_customer_name);
        return;
    }
    if (!serviceId) {
        alert(T.select_service);
        return;
    }
    if (!date || !time) {
        alert(T.select_date_time);
        return;
    }

    const btn = document.getElementById('createBookingBtn');
    btn.disabled = true;
    btn.innerHTML = T.processing;

    fetch('/business/pos/booking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({
            csrf_token: csrfToken,
            customer_id: selectedCustomer?.id || null,
            customer_name: customerName,
            customer_email: customerEmail,
            customer_phone: customerPhone,
            service_id: serviceId,
            employee_id: employeeId || null,
            appointment_date: date,
            appointment_time: time,
            payment_method: paymentMethod,
            notes: notes
        })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus-circle"></i> ' + T.create_appointment_send_link;

        if (data.success) {
            lastCreatedBookingUuid = data.booking.uuid;
            showSuccessModal(data.booking);

            // Auto-send if email provided
            if (customerEmail) {
                sendPaymentLinkManual();
            }

            // Reset form
            clearSelectedCustomer();
            document.querySelectorAll('.service-item').forEach(el => el.classList.remove('selected'));
            selectedService = null;
            document.getElementById('selectedServiceId').value = '';
            document.getElementById('bookingNotes').value = '';
            updateSummary();

            // Reload page to show new booking
            setTimeout(() => location.reload(), 3000);
        } else {
            alert(data.error || T.error_occurred);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus-circle"></i> ' + T.create_appointment_send_link;
        alert(T.error_occurred);
        console.error(err);
    });
}

// Success Modal
function showSuccessModal(booking) {
    document.getElementById('paymentLinkInput').value = booking.payment_link;
    document.getElementById('successCustomer').textContent = booking.customer_name;
    document.getElementById('successService').textContent = booking.service_name;
    document.getElementById('successDateTime').textContent = booking.date + ' ' + T.at + ' ' + booking.time;
    document.getElementById('successAmount').textContent = '€' + booking.online_amount;

    document.getElementById('successOverlay').classList.add('active');
    document.getElementById('successModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeSuccessModal() {
    document.getElementById('successOverlay').classList.remove('active');
    document.getElementById('successModal').classList.remove('active');
    document.body.style.overflow = '';
}

function copyPaymentLink() {
    const input = document.getElementById('paymentLinkInput');
    input.select();
    document.execCommand('copy');

    const btn = event.target.closest('.copy-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = T.copied;
    setTimeout(() => btn.innerHTML = originalText, 2000);
}

function sendPaymentLinkManual() {
    if (!lastCreatedBookingUuid) return;

    fetch('/business/pos/send-payment-link', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({
            csrf_token: csrfToken,
            uuid: lastCreatedBookingUuid
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('successSubtitle').textContent = data.message;
        } else {
            alert(data.error || T.could_not_send_link);
        }
    })
    .catch(err => {
        console.error('Send payment link error:', err);
        alert(T.error_sending);
    });
}

// Add Customer Modal
function openAddCustomerModal(prefillName = '') {
    document.getElementById('newCustomerName').value = prefillName;
    document.getElementById('modalCustomerEmail').value = '';
    document.getElementById('modalCustomerPhone').value = '';
    document.getElementById('modalCustomerNotes').value = '';

    document.getElementById('customerResults').classList.remove('show');
    document.getElementById('addCustomerOverlay').classList.add('active');
    document.getElementById('addCustomerModal').classList.add('active');
    document.body.style.overflow = 'hidden';

    setTimeout(() => document.getElementById('newCustomerName').focus(), 100);
}

function closeAddCustomerModal() {
    document.getElementById('addCustomerOverlay').classList.remove('active');
    document.getElementById('addCustomerModal').classList.remove('active');
    document.body.style.overflow = '';
}

function addNewCustomer() {
    const name = document.getElementById('newCustomerName').value.trim();
    const email = document.getElementById('modalCustomerEmail').value.trim();
    const phone = document.getElementById('modalCustomerPhone').value.trim();
    const notes = document.getElementById('modalCustomerNotes').value.trim();

    if (!name) {
        alert(T.enter_name);
        return;
    }

    fetch('/business/pos/customer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({
            csrf_token: csrfToken,
            name, email, phone, notes
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            selectCustomer(data.customer);
            closeAddCustomerModal();
        } else {
            alert(data.error || T.could_not_add_customer);
        }
    })
    .catch(err => {
        console.error('Add customer error:', err);
        alert(T.error_occurred);
    });
}

// Booking Actions
function resendPaymentLink(uuid) {
    if (!confirm('<?= $__('confirm_resend_link') ?>')) return;

    fetch('/business/pos/send-payment-link', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({ csrf_token: csrfToken, uuid })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? data.message : (data.error || T.something_went_wrong));
    })
    .catch(err => {
        console.error('Resend payment link error:', err);
        alert(T.error_occurred);
    });
}

function markCompleted(uuid) {
    if (!confirm('<?= $__('confirm_mark_completed') ?>')) return;

    fetch('/business/pos/booking/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({ csrf_token: csrfToken, uuid, status: 'completed' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.error || T.something_went_wrong);
    })
    .catch(err => {
        console.error('Mark completed error:', err);
        alert(T.error_occurred);
    });
}

function cancelBooking(uuid) {
    if (!confirm('<?= $__('confirm_cancel_booking') ?>')) return;

    fetch('/business/pos/booking/cancel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({ csrf_token: csrfToken, uuid })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.error || T.something_went_wrong);
    })
    .catch(err => {
        console.error('Cancel booking error:', err);
        alert(T.error_occurred);
    });
}

// Helpers
// dateFormatPHP = PHP-rendered "02-01-2000" (Jan 2) to detect order & separator
function formatDate(dateStr) {
    const d = new Date(dateStr + 'T00:00:00');
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    if (dateFormatPHP.startsWith('2000')) {
        const sep = dateFormatPHP.charAt(4);
        return year + sep + month + sep + day;
    } else if (dateFormatPHP.startsWith('01')) {
        const sep = dateFormatPHP.charAt(2);
        return month + sep + day + sep + year;
    } else {
        const sep = dateFormatPHP.charAt(2);
        return day + sep + month + sep + year;
    }
}

function formatTime(timeStr) {
    if (!timeIs12h) return timeStr;
    const [h, m] = timeStr.split(':').map(Number);
    const period = h >= 12 ? 'PM' : 'AM';
    const h12 = h % 12 || 12;
    return h12 + ':' + String(m).padStart(2, '0') + ' ' + period;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.customer-search-wrapper')) {
        document.getElementById('customerResults').classList.remove('show');
    }
});

// Initialize
updateSummary();

// ===== DIGITAL POS FUNCTIONS =====

// Generate QR Code for payment link
function generateQRCode(paymentLink, containerId, size = 180) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = '';

    if (typeof QRCode !== 'undefined') {
        QRCode.toCanvas(paymentLink, {
            width: size,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#ffffff'
            }
        }, function(err, canvas) {
            if (err) {
                console.error('QR generation error:', err);
                container.innerHTML = '<p style="color:#999;font-size:0.8rem">' + T.qr_not_available + '</p>';
                return;
            }
            container.appendChild(canvas);
        });
    } else {
        container.innerHTML = '<p style="color:#999;font-size:0.8rem">' + T.qr_loading + '</p>';
    }
}

// Share via WhatsApp
function shareViaWhatsApp() {
    const paymentLink = document.getElementById('paymentLinkInput').value;
    const customerName = document.getElementById('successCustomer').textContent;
    const amount = document.getElementById('successAmount').textContent;
    const businessName = '<?= htmlspecialchars($business['company_name'] ?? 'GlamourSchedule') ?>';

    const message = T.whatsapp_template.replace(':customer', customerName).replace(':business', businessName).replace(':amount', amount).replace(':link', paymentLink);

    window.open('https://wa.me/?text=' + encodeURIComponent(message), '_blank');
}

// Share via SMS
function shareViaSMS() {
    const paymentLink = document.getElementById('paymentLinkInput').value;
    const amount = document.getElementById('successAmount').textContent;
    const businessName = '<?= htmlspecialchars($business['company_name'] ?? 'GlamourSchedule') ?>';

    const message = T.sms_template.replace(':amount', amount).replace(':business', businessName).replace(':link', paymentLink);

    // Mobile SMS link
    window.location.href = 'sms:?body=' + encodeURIComponent(message);
}

// Print receipt/payment details
function printReceipt() {
    const customerName = document.getElementById('successCustomer').textContent;
    const serviceName = document.getElementById('successService').textContent;
    const dateTime = document.getElementById('successDateTime').textContent;
    const amount = document.getElementById('successAmount').textContent;
    const paymentLink = document.getElementById('paymentLinkInput').value;
    const businessName = '<?= htmlspecialchars($business['company_name'] ?? 'GlamourSchedule') ?>';

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Afspraak - ${customerName}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; max-width: 400px; margin: 0 auto; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { margin: 0; font-size: 1.5rem; }
                .header p { color: #666; margin: 5px 0 0; }
                .details { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 20px 0; margin: 20px 0; }
                .row { display: flex; justify-content: space-between; padding: 8px 0; }
                .row span:last-child { font-weight: bold; }
                .total { font-size: 1.25rem; border-top: 1px solid #ddd; margin-top: 10px; padding-top: 15px; }
                .qr-section { text-align: center; margin: 30px 0; }
                .qr-section p { font-size: 0.9rem; color: #666; margin-top: 10px; }
                .payment-link { word-break: break-all; font-size: 0.85rem; color: #666; text-align: center; margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 8px; }
                .footer { text-align: center; margin-top: 30px; font-size: 0.8rem; color: #999; }
                @media print { body { padding: 20px; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>${businessName}</h1>
                <p>${T.receipt_confirmation}</p>
            </div>

            <div class="details">
                <div class="row"><span>${T.receipt_customer}</span><span>${customerName}</span></div>
                <div class="row"><span>${T.receipt_service}</span><span>${serviceName}</span></div>
                <div class="row"><span>${T.receipt_datetime}</span><span>${dateTime}</span></div>
                <div class="row total"><span>${T.receipt_to_pay}</span><span>${amount}</span></div>
            </div>

            <div class="qr-section">
                <div id="printQrCode"></div>
                <p>${T.receipt_scan_to_pay}</p>
            </div>

            <div class="payment-link">
                <strong>${T.receipt_payment_link}</strong><br>
                ${paymentLink}
            </div>

            <div class="footer">
                <p>${T.receipt_thanks}</p>
                <p>GlamourSchedule</p>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"><\/script>
            <script>
                QRCode.toCanvas('${paymentLink}', { width: 150, margin: 1 }, function(err, canvas) {
                    if (!err) document.getElementById('printQrCode').appendChild(canvas);
                });
                setTimeout(function() { window.print(); }, 500);
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

// Show full screen QR for customer facing display
function showFullScreenQR(paymentLink, amount) {
    document.getElementById('fullQrAmount').textContent = amount;

    generateQRCode(paymentLink, 'fullQrCodeContainer', 280);

    document.getElementById('fullQrOverlay').classList.add('active');
    document.getElementById('fullQrModal').classList.add('active');

    // Start polling for payment status
    startPaymentStatusPolling(lastCreatedBookingUuid);
}

function closeFullQrModal() {
    document.getElementById('fullQrOverlay').classList.remove('active');
    document.getElementById('fullQrModal').classList.remove('active');
    stopPaymentStatusPolling();
}

// Payment status polling
let paymentStatusInterval = null;

function startPaymentStatusPolling(uuid) {
    if (!uuid) return;

    const indicator = document.getElementById('paymentStatusIndicator');
    const dot = indicator.querySelector('.status-dot');
    const text = indicator.querySelector('span');

    paymentStatusInterval = setInterval(() => {
        fetch('/business/pos/booking/status?uuid=' + uuid)
            .then(r => r.json())
            .then(data => {
                if (data.payment_status === 'paid') {
                    dot.className = 'status-dot paid';
                    text.textContent = T.payment_received;
                    stopPaymentStatusPolling();

                    // Auto close and refresh
                    setTimeout(() => {
                        closeFullQrModal();
                        closeSuccessModal();
                        location.reload();
                    }, 2000);
                }
            })
            .catch(err => console.error('Status check error:', err));
    }, 3000); // Check every 3 seconds
}

function stopPaymentStatusPolling() {
    if (paymentStatusInterval) {
        clearInterval(paymentStatusInterval);
        paymentStatusInterval = null;
    }
}

// Update showSuccessModal to generate QR code
const originalShowSuccessModal = showSuccessModal;
showSuccessModal = function(booking) {
    originalShowSuccessModal(booking);

    // Generate QR code for the payment link
    setTimeout(() => {
        generateQRCode(booking.payment_link, 'qrCodeContainer', 180);
    }, 100);
};

// Add enlarge button functionality
document.addEventListener('DOMContentLoaded', function() {
    const qrSection = document.querySelector('.qr-code-section');
    if (qrSection) {
        const enlargeBtn = document.createElement('button');
        enlargeBtn.className = 'enlarge-btn';
        enlargeBtn.innerHTML = T.enlarge_for_customer;
        enlargeBtn.onclick = function() {
            const paymentLink = document.getElementById('paymentLinkInput').value;
            const amount = document.getElementById('successAmount').textContent;
            showFullScreenQR(paymentLink, amount);
        };
        qrSection.appendChild(enlargeBtn);
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

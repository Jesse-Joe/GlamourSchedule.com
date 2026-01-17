<?php ob_start(); ?>

<?php
$today = date('Y-m-d');
$dayNames = ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'];
$monthNames = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
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
    background: var(--bg-card);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.pos-form-section h3 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-primary);
}
.pos-form-section h3 i {
    color: var(--primary);
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
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
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
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
.customer-result-item:last-child {
    border-bottom: none;
}
.customer-result-item:hover {
    background: var(--secondary);
}
.customer-result-item .name {
    font-weight: 600;
}
.customer-result-item .details {
    font-size: 0.8rem;
    color: var(--text-light);
}
.customer-result-item .badge {
    background: var(--primary);
    color: white;
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
    background: var(--secondary);
    color: var(--primary);
    font-weight: 600;
    border-top: 2px solid var(--border);
}
.add-new-customer:hover {
    background: var(--primary);
    color: white;
}

/* Selected Customer Badge */
.selected-customer {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: linear-gradient(135deg, #f0f0f0, #e5e5e5);
    padding: 0.875rem 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
}
.selected-customer .avatar {
    width: 40px;
    height: 40px;
    background: var(--primary);
    color: white;
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
    background: var(--secondary);
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.2s;
}
.service-item:hover {
    border-color: var(--primary);
}
.service-item.selected {
    border-color: var(--primary);
    background: rgba(0,0,0,0.05);
}
.service-item .name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}
.service-item .meta {
    font-size: 0.8rem;
    color: var(--text-light);
    display: flex;
    gap: 0.5rem;
}
.service-item .price {
    color: var(--primary);
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
    background: var(--secondary);
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 1.25rem;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s;
}
.payment-method:hover {
    border-color: var(--primary);
}
.payment-method.selected {
    border-color: var(--primary);
    background: rgba(0,0,0,0.05);
}
.payment-method i {
    font-size: 1.5rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
}
.payment-method .title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}
.payment-method .desc {
    font-size: 0.75rem;
    color: var(--text-light);
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
    border-bottom: 1px solid var(--border);
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
}
.booking-service {
    font-size: 0.85rem;
    color: var(--text-light);
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
    background: var(--secondary);
    border: none;
    padding: 0.4rem 0.6rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    white-space: nowrap;
}
.booking-action-btn:hover {
    background: var(--primary);
    color: white;
}
.booking-action-btn.danger:hover {
    background: #dc2626;
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
    background: var(--bg-card);
    border-radius: 24px 24px 0 0;
    max-height: 90vh;
    overflow-y: auto;
    z-index: 1001;
    transform: translateY(100%);
    transition: transform 0.3s ease;
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
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    background: var(--bg-card);
    z-index: 10;
}
.pos-modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
}
.pos-modal-close {
    background: var(--secondary);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pos-modal-content {
    padding: 1.25rem;
}
.pos-modal-actions {
    display: flex;
    gap: 0.75rem;
    padding: 1.25rem;
    border-top: 1px solid var(--border);
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
    background: var(--secondary);
    border-radius: 12px;
    padding: 1rem;
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.payment-link-box input {
    flex: 1;
    background: white;
    border: 1px solid var(--border);
    padding: 0.75rem;
    border-radius: 8px;
    font-size: 0.85rem;
}
.copy-btn {
    background: var(--primary);
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}
.copy-btn:hover {
    opacity: 0.9;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-light);
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
    background: linear-gradient(135deg, #f8f9fa, #fff);
    border: 2px dashed var(--border);
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
    color: var(--text-primary);
}
.qr-code-header i {
    font-size: 1.25rem;
    color: var(--primary);
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
    color: var(--text-light);
    margin: 0;
}
.qr-code-section .enlarge-btn {
    margin-top: 0.75rem;
    background: var(--primary);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
}
.qr-code-section .enlarge-btn:hover {
    opacity: 0.9;
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
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--bg-card);
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s;
}
.share-btn:hover {
    border-color: var(--primary);
    background: rgba(0,0,0,0.02);
}
.share-btn i {
    font-size: 1.1rem;
}
.share-btn .fa-whatsapp { color: #25D366; }
.share-btn .fa-sms { color: #4A90D9; }
.share-btn .fa-print { color: #666; }

/* Full Screen QR Modal */
.fullscreen-qr-modal {
    max-width: 600px;
}
.fullscreen-qr-modal #fullQrCodeContainer {
    width: 280px;
    height: 280px;
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
    color: var(--primary);
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
    background: var(--secondary);
    border-radius: 12px;
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
    <h1>
        <i class="fas fa-cash-register"></i>
        POS Systeem
    </h1>
    <span style="color:var(--text-light)">
        <i class="fas fa-calendar"></i>
        <?= $dayNames[date('w')] ?>, <?= date('d') ?> <?= $monthNames[date('n') - 1] ?> <?= date('Y') ?>
    </span>
</div>

<div class="pos-layout">
    <!-- Left Column: New Booking Form -->
    <div>
        <!-- Customer Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-user"></i> Klant</h3>

            <div id="selectedCustomerBadge" style="display:none" class="selected-customer">
                <div class="avatar" id="customerAvatar">J</div>
                <div class="info">
                    <div class="name" id="customerName">Jan de Vries</div>
                    <div class="contact" id="customerContact">jan@email.nl</div>
                </div>
                <button class="remove" onclick="clearSelectedCustomer()" title="Verwijderen">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="customerSearchSection">
                <div class="customer-search-wrapper">
                    <input type="text"
                           class="form-control"
                           id="customerSearch"
                           placeholder="Zoek klant op ID, naam, email of telefoon..."
                           autocomplete="off">
                    <div class="customer-search-results" id="customerResults">
                        <!-- Results will be populated via JS -->
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-top:0.75rem">
                    <input type="email" class="form-control" id="newCustomerEmail" placeholder="E-mail (optioneel)">
                    <input type="tel" class="form-control" id="newCustomerPhone" placeholder="Telefoon (optioneel)">
                </div>
            </div>

            <input type="hidden" id="selectedCustomerId" value="">
        </div>

        <!-- Service Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-concierge-bell"></i> Dienst</h3>

            <?php if (empty($services)): ?>
                <p class="text-muted">Geen diensten gevonden. <a href="/business/services">Voeg diensten toe</a></p>
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
            <h3><i class="fas fa-user-tie"></i> Medewerker</h3>
            <select class="form-control" id="selectedEmployee">
                <option value="">-- Geen specifieke medewerker --</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- Date & Time Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-clock"></i> Datum & Tijd</h3>
            <div class="datetime-row">
                <div>
                    <label class="form-label">Datum</label>
                    <input type="date" class="form-control" id="appointmentDate" value="<?= $today ?>" min="<?= $today ?>">
                </div>
                <div>
                    <label class="form-label">Tijd</label>
                    <input type="time" class="form-control" id="appointmentTime" value="<?= date('H:00', strtotime('+1 hour')) ?>">
                </div>
            </div>
        </div>

        <!-- Payment Method Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-credit-card"></i> Betaalmethode</h3>
            <div class="payment-methods">
                <div class="payment-method selected" data-method="online" onclick="selectPaymentMethod(this)">
                    <i class="fas fa-globe"></i>
                    <div class="title">Online Betaling</div>
                    <div class="desc">Klant betaalt volledige bedrag online</div>
                </div>
                <div class="payment-method" data-method="cash" onclick="selectPaymentMethod(this)">
                    <i class="fas fa-money-bill-wave"></i>
                    <div class="title">Contant bij Afspraak</div>
                    <div class="desc">Klant betaalt €1,75 online + rest contant</div>
                </div>
            </div>
            <input type="hidden" id="selectedPaymentMethod" value="online">
        </div>

        <!-- Notes Section -->
        <div class="pos-form-section">
            <h3><i class="fas fa-sticky-note"></i> Notities (optioneel)</h3>
            <textarea class="form-control" id="bookingNotes" rows="2" placeholder="Eventuele opmerkingen..."></textarea>
        </div>

        <!-- Summary & Submit -->
        <div class="summary-card">
            <h4><i class="fas fa-receipt"></i> Overzicht</h4>
            <div class="summary-row">
                <span>Klant</span>
                <span id="summaryCustomer">-</span>
            </div>
            <div class="summary-row">
                <span>Dienst</span>
                <span id="summaryService">-</span>
            </div>
            <div class="summary-row">
                <span>Datum & Tijd</span>
                <span id="summaryDateTime">-</span>
            </div>
            <div class="summary-row">
                <span>Betaalmethode</span>
                <span id="summaryPayment">Online</span>
            </div>
            <div class="summary-row">
                <span>Online te betalen</span>
                <span id="summaryOnlineAmount">€0,00</span>
            </div>
            <div class="summary-row total">
                <span>Totaal</span>
                <span id="summaryTotal">€0,00</span>
            </div>

            <button class="btn btn-primary" style="width:100%;margin-top:1.25rem;padding:1rem" onclick="createBooking()" id="createBookingBtn">
                <i class="fas fa-plus-circle"></i> Afspraak Aanmaken & Link Versturen
            </button>
        </div>
    </div>

    <!-- Right Column: Today's Bookings -->
    <div>
        <div class="card" style="padding:0">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
                <h3 style="margin:0;font-size:1rem">
                    <i class="fas fa-calendar-day"></i> Vandaag
                </h3>
                <span class="badge" style="background:var(--primary);color:white"><?= count($todayBookings) ?> afspraken</span>
            </div>

            <div class="bookings-list">
                <?php if (empty($todayBookings)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-plus"></i>
                        <p>Geen afspraken vandaag</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($todayBookings as $booking): ?>
                        <div class="booking-item" data-uuid="<?= $booking['uuid'] ?>">
                            <div class="booking-time"><?= date('H:i', strtotime($booking['appointment_time'])) ?></div>
                            <div class="booking-details">
                                <div class="booking-customer"><?= htmlspecialchars($booking['customer_name']) ?></div>
                                <div class="booking-service"><?= htmlspecialchars($booking['service_name']) ?></div>
                                <div class="booking-badges">
                                    <span class="booking-badge badge-<?= $booking['payment_method'] ?>">
                                        <?= $booking['payment_method'] === 'cash' ? 'Contant' : 'Online' ?>
                                    </span>
                                    <span class="booking-badge badge-payment-<?= $booking['payment_status'] ?>">
                                        <?= $booking['payment_status'] === 'paid' ? 'Betaald' : 'Wacht op betaling' ?>
                                    </span>
                                    <span class="booking-badge badge-status-<?= $booking['booking_status'] ?>">
                                        <?php
                                        $statusLabels = [
                                            'pending' => 'In afwachting',
                                            'confirmed' => 'Bevestigd',
                                            'completed' => 'Voltooid',
                                            'cancelled' => 'Geannuleerd',
                                            'no_show' => 'Niet verschenen'
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
                                        <i class="fas fa-check"></i> Voltooid
                                    </button>
                                <?php endif; ?>
                                <?php if ($booking['booking_status'] !== 'cancelled' && $booking['booking_status'] !== 'completed'): ?>
                                    <button class="booking-action-btn danger" onclick="cancelBooking('<?= $booking['uuid'] ?>')">
                                        <i class="fas fa-times"></i> Annuleer
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
        <div class="card" style="margin-top:1rem">
            <div class="card-header">
                <h3 class="card-title" style="font-size:0.95rem"><i class="fas fa-users"></i> Recente Klanten</h3>
            </div>
            <?php foreach (array_slice($recentCustomers, 0, 5) as $customer): ?>
                <div style="padding:0.75rem 0;border-bottom:1px solid var(--border);cursor:pointer"
                     onclick="quickSelectCustomer(<?= $customer['id'] ?>, '<?= htmlspecialchars(addslashes($customer['name'])) ?>', '<?= htmlspecialchars($customer['email'] ?? '') ?>', '<?= htmlspecialchars($customer['phone'] ?? '') ?>')">
                    <strong><?= htmlspecialchars($customer['name']) ?></strong> <span style="color:var(--text-light);font-size:0.75rem">#<?= $customer['id'] ?></span>
                    <?php if (!empty($customer['email'])): ?>
                        <br><small class="text-muted"><?= htmlspecialchars($customer['email']) ?></small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Help Card -->
        <div class="card" style="background:linear-gradient(135deg, #000, #333);color:white;margin-top:1rem">
            <h4 style="margin-bottom:0.5rem"><i class="fas fa-info-circle"></i> POS Tips</h4>
            <ul style="margin:0;padding-left:1.25rem;font-size:0.85rem;opacity:0.9">
                <li>Bij contante betaling betaalt de klant €1,75 online als reserveringskosten</li>
                <li>Klik op "Link" om betalingslinks opnieuw te versturen</li>
                <li>Bij annuleren wordt de online betaling teruggestort</li>
            </ul>
        </div>
    </div>
</div>

<!-- Success Modal with Digital POS Features -->
<div class="pos-modal-overlay" id="successOverlay" onclick="closeSuccessModal()"></div>
<div class="pos-modal" id="successModal">
    <div class="pos-modal-header">
        <h3><i class="fas fa-check-circle" style="color:#22c55e"></i> Afspraak Aangemaakt</h3>
        <button class="pos-modal-close" onclick="closeSuccessModal()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="pos-modal-content">
        <div class="success-animation">
            <div class="checkmark"><i class="fas fa-check"></i></div>
            <h3 style="margin:0 0 0.5rem 0" id="successTitle">Afspraak succesvol aangemaakt!</h3>
            <p class="text-muted" id="successSubtitle">De betalingslink is verzonden naar de klant.</p>
        </div>

        <!-- Digital POS: QR Code for instant payment -->
        <div class="qr-code-section">
            <div class="qr-code-header">
                <i class="fas fa-qrcode"></i>
                <span>Scan om te betalen</span>
            </div>
            <div id="qrCodeContainer" class="qr-code-display"></div>
            <p class="qr-code-hint">Klant kan deze QR-code scannen met telefoon</p>
        </div>

        <div class="payment-link-box">
            <input type="text" id="paymentLinkInput" readonly value="">
            <button class="copy-btn" onclick="copyPaymentLink()">
                <i class="fas fa-copy"></i> Kopieer
            </button>
        </div>

        <div style="background:var(--secondary);border-radius:12px;padding:1rem;margin-top:1rem">
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem">
                <span>Klant</span>
                <strong id="successCustomer">-</strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem">
                <span>Dienst</span>
                <strong id="successService">-</strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem">
                <span>Datum & Tijd</span>
                <strong id="successDateTime">-</strong>
            </div>
            <div style="display:flex;justify-content:space-between">
                <span>Online te betalen</span>
                <strong id="successAmount">-</strong>
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
        <button class="btn btn-secondary" onclick="closeSuccessModal()">Sluiten</button>
        <button class="btn btn-primary" onclick="sendPaymentLinkManual()">
            <i class="fas fa-paper-plane"></i> E-mail Versturen
        </button>
    </div>
</div>

<!-- Digital POS: Full Screen QR Modal (for customer facing display) -->
<div class="pos-modal-overlay" id="fullQrOverlay" onclick="closeFullQrModal()"></div>
<div class="pos-modal fullscreen-qr-modal" id="fullQrModal">
    <div class="pos-modal-header">
        <h3><i class="fas fa-qrcode"></i> Scan om te Betalen</h3>
        <button class="pos-modal-close" onclick="closeFullQrModal()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="pos-modal-content" style="text-align:center;padding:2rem">
        <div id="fullQrCodeContainer" style="margin:0 auto 1.5rem"></div>
        <div class="full-qr-amount" id="fullQrAmount">-</div>
        <p style="color:var(--text-light);margin:0">Scan met je telefoon om te betalen</p>

        <!-- Payment status indicator -->
        <div class="payment-status-indicator" id="paymentStatusIndicator">
            <div class="status-dot pending"></div>
            <span>Wachten op betaling...</span>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="pos-modal-overlay" id="addCustomerOverlay" onclick="closeAddCustomerModal()"></div>
<div class="pos-modal" id="addCustomerModal">
    <div class="pos-modal-header">
        <h3><i class="fas fa-user-plus"></i> Nieuwe Klant</h3>
        <button class="pos-modal-close" onclick="closeAddCustomerModal()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="pos-modal-content">
        <div style="margin-bottom:1rem">
            <label class="form-label">Naam *</label>
            <input type="text" class="form-control" id="newCustomerName" placeholder="Volledige naam">
        </div>
        <div style="margin-bottom:1rem">
            <label class="form-label">E-mail</label>
            <input type="email" class="form-control" id="modalCustomerEmail" placeholder="email@voorbeeld.nl">
        </div>
        <div style="margin-bottom:1rem">
            <label class="form-label">Telefoon</label>
            <input type="tel" class="form-control" id="modalCustomerPhone" placeholder="06-12345678">
        </div>
        <div>
            <label class="form-label">Notities</label>
            <textarea class="form-control" id="modalCustomerNotes" rows="2" placeholder="Optionele notities..."></textarea>
        </div>
    </div>
    <div class="pos-modal-actions">
        <button class="btn btn-secondary" onclick="closeAddCustomerModal()">Annuleren</button>
        <button class="btn btn-primary" onclick="addNewCustomer()">
            <i class="fas fa-plus"></i> Klant Toevoegen
        </button>
    </div>
</div>

<script>
const csrfToken = '<?= $csrfToken ?>';
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
                div.innerHTML = `
                    <div>
                        <div class="name">${escapeHtml(c.name)} <span style="color:var(--text-light);font-size:0.75rem;font-weight:normal">#${c.id}</span></div>
                        <div class="details">${c.email || ''} ${c.phone ? '• ' + c.phone : ''}</div>
                    </div>
                    ${c.total_appointments > 0 ? '<span class="badge">' + c.total_appointments + ' bezoeken</span>' : ''}
                `;
                div.onclick = () => selectCustomer(c);
                results.appendChild(div);
            });

            // Add "New Customer" option
            const addNew = document.createElement('div');
            addNew.className = 'add-new-customer';
            addNew.innerHTML = '<i class="fas fa-plus"></i> Nieuwe klant toevoegen';
            addNew.onclick = () => openAddCustomerModal(query);
            results.appendChild(addNew);

            results.classList.add('show');
        });
}

function selectCustomer(customer) {
    selectedCustomer = customer;
    document.getElementById('selectedCustomerId').value = customer.id;
    document.getElementById('customerAvatar').textContent = customer.name.charAt(0).toUpperCase();
    document.getElementById('customerName').innerHTML = customer.name + ' <span style="color:var(--text-light);font-size:0.8rem;font-weight:normal">#' + customer.id + '</span>';
    document.getElementById('customerContact').textContent = [customer.email, customer.phone].filter(Boolean).join(' • ') || 'Geen contactgegevens';

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
    document.getElementById('summaryDateTime').textContent = date && time ? formatDate(date) + ' om ' + time : '-';
    document.getElementById('summaryPayment').textContent = paymentMethod === 'cash' ? 'Contant' : 'Online';

    const onlineAmount = paymentMethod === 'cash' ? 1.75 : servicePrice;
    document.getElementById('summaryOnlineAmount').textContent = '€' + onlineAmount.toFixed(2).replace('.', ',');
    document.getElementById('summaryTotal').textContent = '€' + servicePrice.toFixed(2).replace('.', ',');
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
        alert('Vul een klantnaam in');
        return;
    }
    if (!serviceId) {
        alert('Selecteer een dienst');
        return;
    }
    if (!date || !time) {
        alert('Selecteer datum en tijd');
        return;
    }

    const btn = document.getElementById('createBookingBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Bezig...';

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
        btn.innerHTML = '<i class="fas fa-plus-circle"></i> Afspraak Aanmaken & Link Versturen';

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
            alert(data.error || 'Er is een fout opgetreden');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus-circle"></i> Afspraak Aanmaken & Link Versturen';
        alert('Er is een fout opgetreden');
        console.error(err);
    });
}

// Success Modal
function showSuccessModal(booking) {
    document.getElementById('paymentLinkInput').value = booking.payment_link;
    document.getElementById('successCustomer').textContent = booking.customer_name;
    document.getElementById('successService').textContent = booking.service_name;
    document.getElementById('successDateTime').textContent = booking.date + ' om ' + booking.time;
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
    btn.innerHTML = '<i class="fas fa-check"></i> Gekopieerd!';
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
            alert(data.error || 'Kon link niet versturen');
        }
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
        alert('Vul een naam in');
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
            alert(data.error || 'Kon klant niet toevoegen');
        }
    });
}

// Booking Actions
function resendPaymentLink(uuid) {
    if (!confirm('Betalingslink opnieuw versturen?')) return;

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
        alert(data.success ? data.message : (data.error || 'Er ging iets mis'));
    });
}

function markCompleted(uuid) {
    if (!confirm('Afspraak als voltooid markeren?')) return;

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
        else alert(data.error || 'Er ging iets mis');
    });
}

function cancelBooking(uuid) {
    if (!confirm('Weet je zeker dat je deze afspraak wilt annuleren? De online betaling wordt teruggestort.')) return;

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
        else alert(data.error || 'Er ging iets mis');
    });
}

// Helpers
function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' });
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
                container.innerHTML = '<p style="color:#999;font-size:0.8rem">QR code niet beschikbaar</p>';
                return;
            }
            container.appendChild(canvas);
        });
    } else {
        container.innerHTML = '<p style="color:#999;font-size:0.8rem">QR code laden...</p>';
    }
}

// Share via WhatsApp
function shareViaWhatsApp() {
    const paymentLink = document.getElementById('paymentLinkInput').value;
    const customerName = document.getElementById('successCustomer').textContent;
    const amount = document.getElementById('successAmount').textContent;
    const businessName = '<?= htmlspecialchars($business['company_name'] ?? 'GlamourSchedule') ?>';

    const message = `Hallo ${customerName}! Hier is je betalingslink voor je afspraak bij ${businessName}. Bedrag: ${amount}. Betaal via: ${paymentLink}`;

    window.open('https://wa.me/?text=' + encodeURIComponent(message), '_blank');
}

// Share via SMS
function shareViaSMS() {
    const paymentLink = document.getElementById('paymentLinkInput').value;
    const amount = document.getElementById('successAmount').textContent;
    const businessName = '<?= htmlspecialchars($business['company_name'] ?? 'GlamourSchedule') ?>';

    const message = `Betaal ${amount} voor je afspraak bij ${businessName}: ${paymentLink}`;

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
                <p>Afspraakbevestiging</p>
            </div>

            <div class="details">
                <div class="row"><span>Klant:</span><span>${customerName}</span></div>
                <div class="row"><span>Dienst:</span><span>${serviceName}</span></div>
                <div class="row"><span>Datum & Tijd:</span><span>${dateTime}</span></div>
                <div class="row total"><span>Te betalen:</span><span>${amount}</span></div>
            </div>

            <div class="qr-section">
                <div id="printQrCode"></div>
                <p>Scan om te betalen</p>
            </div>

            <div class="payment-link">
                <strong>Betalingslink:</strong><br>
                ${paymentLink}
            </div>

            <div class="footer">
                <p>Bedankt voor uw boeking!</p>
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
                    text.textContent = 'Betaling ontvangen!';
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
        enlargeBtn.innerHTML = '<i class="fas fa-expand"></i> Vergroot voor klant';
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

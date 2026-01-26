<?php ob_start(); ?>
<?php
$primaryColor = $settings['primary_color'] ?? '#000000';
$secondaryColor = $settings['secondary_color'] ?? '#333333';
$accentColor = $settings['accent_color'] ?? '#000000';
?>

<style>
/* Business Theme Colors */
:root {
    --business-primary: <?= htmlspecialchars($primaryColor) ?>;
    --business-secondary: <?= htmlspecialchars($secondaryColor) ?>;
    --business-accent: <?= htmlspecialchars($accentColor) ?>;
}
/* ============================================
   THEME VARIABLES - Dark Mode (Default)
   ============================================ */
:root {
    --page-bg: #0a0a0a;
    --card-bg: #111111;
    --card-border: #222222;
    --text-primary: #ffffff;
    --text-secondary: #a1a1aa;
    --text-muted: #71717a;
    --input-bg: #1a1a1a;
    --input-border: #333333;
    --service-bg: #1a1a1a;
    --service-hover: #222222;
    --service-border: #333333;
    --divider: #333333;
    --btn-primary-bg: var(--business-primary);
    --btn-primary-text: #ffffff;
}

/* ============================================
   LIGHT MODE VARIABLES
   ============================================ */
[data-theme="light"] {
    --page-bg: #f8f9fa;
    --card-bg: #ffffff;
    --card-border: #e5e7eb;
    --text-primary: #111827;
    --text-secondary: #4b5563;
    --text-muted: #9ca3af;
    --input-bg: #ffffff;
    --input-border: #d1d5db;
    --service-bg: #f9fafb;
    --service-hover: #f3f4f6;
    --service-border: #e5e7eb;
    --divider: #e5e7eb;
    --btn-primary-bg: var(--business-primary);
    --btn-primary-text: #ffffff;
}

/* Mobile-first booking page */
.booking-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 1rem;
}
@media (max-width: 768px) {
    .booking-container {
        max-width: 100%;
        padding: 0;
        margin: 0;
    }
    .booking-card {
        border-radius: 0 !important;
        box-shadow: none !important;
    }
    .form-group {
        text-align: left;
    }
    .form-label {
        justify-content: flex-start;
    }
    .form-input,
    .notes-textarea {
        text-align: left;
    }
    .service-option {
        flex-direction: row;
        text-align: left;
        gap: 0.75rem;
    }
    .service-radio {
        margin: 0;
    }
    .service-details {
        flex: 1;
    }
    .service-duration {
        justify-content: flex-start;
    }
    .service-price {
        margin-left: auto;
    }
    .guest-info-box h4 {
        justify-content: flex-start;
    }
    .guest-login-hint {
        text-align: left;
    }
    .terms-box {
        flex-direction: row;
        text-align: left;
        align-items: flex-start;
    }
    .booking-title {
        justify-content: flex-start;
        text-align: left;
    }
}
.booking-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid var(--card-border);
    color: var(--text-primary);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}
.booking-title {
    font-size: 1.25rem;
    margin: 0 0 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-primary);
}
.booking-title i {
    color: var(--text-secondary);
}

/* Service Selection */
.service-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.service-option {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border: 2px solid var(--service-border);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: var(--service-bg);
    color: var(--text-primary);
}
.service-option:active {
    transform: scale(0.98);
}
.service-option:hover {
    border-color: var(--text-primary);
    background: var(--service-hover);
}
.service-option:has(input:checked) {
    border-color: var(--business-primary) !important;
    background: rgba(0,0,0,0.05) !important;
}
.service-radio {
    margin-right: 0.75rem;
    margin-top: 0.2rem;
    width: 20px;
    height: 20px;
    accent-color: var(--business-primary);
    flex-shrink: 0;
}
.service-details {
    flex: 1;
    min-width: 0;
}
.service-name {
    font-weight: 600;
    font-size: 1rem;
    display: block;
    margin-bottom: 0.25rem;
}
.service-duration {
    color: var(--text-light);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.service-price {
    color: var(--business-primary);
    font-size: 1.1rem;
    font-weight: 700;
    white-space: nowrap;
    margin-left: 0.5rem;
}

/* Employee Selection */
.employee-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.employee-option {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 2px solid var(--border);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: var(--white);
    gap: 1rem;
}
.employee-option:hover {
    border-color: var(--business-primary);
    background: rgba(0,0,0,0.02);
}
.employee-option:has(input:checked) {
    border-color: var(--business-primary) !important;
    background: rgba(0,0,0,0.05) !important;
}
.employee-radio {
    width: 20px;
    height: 20px;
    accent-color: var(--business-primary);
    flex-shrink: 0;
}
.employee-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark, #737373));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
    flex-shrink: 0;
    overflow: hidden;
}
.employee-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.employee-details {
    flex: 1;
    min-width: 0;
}
.employee-name {
    font-weight: 600;
    font-size: 1rem;
    display: block;
}
.employee-bio {
    color: var(--text-light);
    font-size: 0.85rem;
    display: block;
    margin-top: 0.25rem;
}

/* Date & Time - Stack on mobile */
.datetime-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1.25rem;
}
.form-label {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-primary);
}
.form-label i {
    color: var(--text-secondary);
}
/* Form Input - Dark Mode Default */
.form-input {
    width: 100%;
    padding: 0.9rem 0;
    border: none;
    border-bottom: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 0;
    font-size: 1rem;
    background: transparent;
    transition: all 0.3s ease;
    color: var(--text-primary);
}
.form-input:focus {
    outline: none;
    border-bottom-color: #ffffff;
    box-shadow: none;
}
.form-input:hover {
    border-bottom-color: rgba(255, 255, 255, 0.5);
}
.form-input::placeholder {
    color: rgba(255, 255, 255, 0.4);
}
/* Form Input - Light Mode */
[data-theme="light"] .form-input {
    border-bottom-color: rgba(0, 0, 0, 0.2);
    color: var(--text-primary);
}
[data-theme="light"] .form-input:focus {
    border-bottom-color: #000000;
}
[data-theme="light"] .form-input:hover {
    border-bottom-color: rgba(0, 0, 0, 0.4);
}
[data-theme="light"] .form-input::placeholder {
    color: rgba(0, 0, 0, 0.4);
}
/* Select Input - Dark Mode Default */
select.form-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0 center;
    background-size: 1rem;
    padding-right: 1.5rem;
    cursor: pointer;
}
[data-theme="light"] select.form-input {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
}
.time-loading {
    display: none;
    margin-top: 0.5rem;
    color: var(--text-light);
    font-size: 0.85rem;
}

/* Guest Info Box - Dark Mode Default */
.guest-info-box {
    background: var(--service-bg);
    padding: 1.25rem;
    border-radius: 12px;
    margin: 1.25rem 0;
    border: 1px solid var(--service-border);
}
.guest-info-box h4 {
    margin: 0 0 0.75rem;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-primary);
}
.guest-info-box h4 i {
    color: var(--text-secondary);
}
.guest-login-hint {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}
.guest-login-hint a {
    color: var(--text-primary);
    font-weight: 600;
}
.guest-fields {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.guest-fields .form-group {
    margin: 0;
}

/* Notes - Dark Mode Default */
.notes-textarea {
    width: 100%;
    padding: 0.9rem 0;
    border: none;
    border-bottom: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 0;
    font-size: 1rem;
    resize: vertical;
    min-height: 80px;
    font-family: inherit;
    background: transparent;
    transition: all 0.3s ease;
    color: var(--text-primary);
}
.notes-textarea:focus {
    outline: none;
    border-bottom-color: #ffffff;
    box-shadow: none;
}
.notes-textarea:hover {
    border-bottom-color: rgba(255, 255, 255, 0.5);
}
.notes-textarea::placeholder {
    color: rgba(255, 255, 255, 0.4);
}
/* Notes - Light Mode */
[data-theme="light"] .notes-textarea {
    border-bottom-color: rgba(0, 0, 0, 0.2);
    color: var(--text-primary);
}
[data-theme="light"] .notes-textarea:focus {
    border-bottom-color: #000000;
}
[data-theme="light"] .notes-textarea:hover {
    border-bottom-color: rgba(0, 0, 0, 0.4);
}
[data-theme="light"] .notes-textarea::placeholder {
    color: rgba(0, 0, 0, 0.4);
}

/* Terms - Dark Mode Default */
.terms-box {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--service-bg);
    border: 1px solid var(--service-border);
    border-radius: 12px;
    margin-top: 1.25rem;
    cursor: pointer;
}
.terms-checkbox {
    margin-top: 0.15rem;
    width: 20px;
    height: 20px;
    accent-color: var(--btn-primary-bg);
    flex-shrink: 0;
}
.terms-text {
    font-size: 0.9rem;
    color: var(--text-primary);
    line-height: 1.5;
}
.terms-text a {
    color: var(--text-primary);
    font-weight: 600;
    text-decoration: underline;
}

/* Submit Button - Dark Mode Default */
.submit-btn {
    width: 100%;
    padding: 1rem;
    margin-top: 1.25rem;
    background: var(--btn-primary-bg);
    color: var(--btn-primary-text);
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s;
}
.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    opacity: 0.9;
}
.submit-btn:active {
    transform: scale(0.98);
}

/* Alert - Works in both modes */
.alert-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

/* QR Code Notice - Dark Mode Default */
.qr-notice {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: var(--btn-primary-bg);
    border: 2px solid var(--btn-primary-bg);
    border-radius: 12px;
    padding: 1.25rem;
    margin: 1.25rem 0;
    color: var(--btn-primary-text);
}
.qr-notice-icon {
    width: 50px;
    height: 50px;
    background: var(--btn-primary-text);
    color: var(--btn-primary-bg);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.qr-notice-content h4 {
    margin: 0 0 0.5rem;
    font-size: 1rem;
    color: var(--btn-primary-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.qr-notice-content h4 i {
    color: #60a5fa;
}
.qr-notice-content p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--btn-primary-text);
    opacity: 0.9;
    line-height: 1.5;
}
@media (max-width: 768px) {
    .qr-notice {
        flex-direction: row;
        text-align: left;
        align-items: flex-start;
        border-radius: 0;
    }
    .qr-notice-content h4 {
        justify-content: flex-start;
    }
}

/* Desktop enhancements */
@media (min-width: 768px) {
    .booking-container {
        padding: 0 2rem;
    }
    .booking-card {
        padding: 2rem;
    }
    .booking-title {
        font-size: 1.5rem;
    }
    .datetime-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .guest-fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    .guest-fields .form-group:last-child {
        grid-column: span 2;
    }
    .service-option {
        padding: 1.25rem;
    }
}

/* ============================================
   NEXT AVAILABLE DATE & WAITLIST STYLES
   ============================================ */
.no-times-info {
    margin-top: 1rem;
}
.next-available-box {
    background: var(--service-bg);
    border: 1px solid var(--service-border);
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
}
.no-times-message {
    color: var(--text-secondary);
    margin-bottom: 1rem;
    font-size: 0.95rem;
}
.no-times-message i {
    color: #ef4444;
    margin-right: 0.5rem;
}
.next-available-date {
    margin-bottom: 1rem;
}
.next-available-date p {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}
.next-date-link {
    display: inline-block;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}
.next-date-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}
.next-date-link i {
    margin-right: 0.5rem;
}
.no-next-date {
    color: var(--text-muted);
    font-size: 0.9rem;
    font-style: italic;
}
.waitlist-divider {
    display: flex;
    align-items: center;
    margin: 1rem 0;
    color: var(--text-muted);
    font-size: 0.85rem;
}
.waitlist-divider::before,
.waitlist-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--divider);
}
.waitlist-divider span {
    padding: 0 1rem;
}
.waitlist-btn {
    background: transparent;
    border: 2px solid var(--btn-primary-bg);
    color: var(--text-primary);
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}
.waitlist-btn:hover {
    background: var(--btn-primary-bg);
    color: var(--btn-primary-text);
}
.waitlist-btn i {
    margin-right: 0.5rem;
}

/* Waitlist Modal */
.waitlist-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 1rem;
}
.waitlist-modal-content {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 2rem;
    max-width: 450px;
    width: 100%;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
}
.waitlist-modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    color: var(--text-secondary);
    font-size: 1.5rem;
    cursor: pointer;
    line-height: 1;
}
.waitlist-modal-close:hover {
    color: var(--text-primary);
}
.waitlist-modal-content h3 {
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
}
.waitlist-modal-content h3 i {
    margin-right: 0.5rem;
    color: #10b981;
}
.waitlist-form .form-group {
    margin-bottom: 1rem;
}
.waitlist-submit {
    width: 100%;
    margin-top: 0.5rem;
}
.waitlist-success {
    text-align: center;
    padding: 2rem 0;
}
.waitlist-success i {
    font-size: 4rem;
    color: #10b981;
    margin-bottom: 1rem;
}
.waitlist-success h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}
.waitlist-success p {
    color: var(--text-secondary);
}
</style>

<div class="booking-container">
    <div class="booking-card">
        <h2 class="booking-title"><i class="fas fa-calendar-plus"></i> <?= $__('book_at') ?> <?= htmlspecialchars($business['name']) ?></h2>

        <?php if (isset($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/book/<?= htmlspecialchars($business['slug']) ?>" id="bookingForm">
            <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">

            <!-- Service Selection -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-cut"></i> <?= $__('select_service') ?></label>
                <div class="service-list">
                    <?php foreach ($services as $service): ?>
                        <label class="service-option">
                            <input type="radio" name="service_id" value="<?= $service['id'] ?>"
                                   data-duration="<?= $service['duration_minutes'] ?>"
                                   <?= $selectedService == $service['id'] ? 'checked' : '' ?>
                                   class="service-radio" required>
                            <div class="service-details">
                                <span class="service-name"><?= htmlspecialchars($service['name']) ?></span>
                                <span class="service-duration">
                                    <i class="fas fa-clock"></i> <?= $service['duration_minutes'] ?> <?= $__('min') ?>
                                </span>
                            </div>
                            <span class="service-price">&euro;<?= number_format($service['price'], 2, ',', '.') ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($employees)): ?>
            <!-- Employee Selection (BV businesses only) -->
            <div class="form-group" style="margin-top:1.25rem">
                <label class="form-label"><i class="fas fa-user"></i> <?= $translations['choose_employee'] ?? 'Choose an employee' ?></label>
                <div class="employee-list">
                    <?php foreach ($employees as $employee): ?>
                        <label class="employee-option">
                            <input type="radio" name="employee_id" value="<?= $employee['id'] ?>" class="employee-radio" required>
                            <div class="employee-avatar" style="<?= !empty($employee['color']) ? 'background:' . htmlspecialchars($employee['color']) : '' ?>">
                                <?php if (!empty($employee['photo'])): ?>
                                    <img src="<?= htmlspecialchars($employee['photo']) ?>" alt="">
                                <?php else: ?>
                                    <?= strtoupper(substr($employee['name'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div class="employee-details">
                                <span class="employee-name"><?= htmlspecialchars($employee['name']) ?></span>
                                <?php if (!empty($employee['bio'])): ?>
                                    <span class="employee-bio"><?= htmlspecialchars(substr($employee['bio'], 0, 60)) ?><?= strlen($employee['bio']) > 60 ? '...' : '' ?></span>
                                <?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Date & Time -->
            <div class="datetime-grid">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-calendar"></i> <?= $__('date') ?></label>
                    <input type="date" name="date" id="bookingDate" class="form-input" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-clock"></i> <?= $__('time') ?></label>
                    <select name="time" id="bookingTime" class="form-input" required disabled>
                        <option value=""><?= $__('select_date_service_first') ?></option>
                    </select>
                    <div id="timeLoading" class="time-loading">
                        <i class="fas fa-spinner fa-spin"></i> <?= $__('loading_availability') ?>
                    </div>
                    <!-- Next Available Date & Waitlist Option -->
                    <div id="noTimesInfo" class="no-times-info" style="display:none;">
                        <div class="next-available-box">
                            <p class="no-times-message"><i class="fas fa-calendar-times"></i> <?= $__('no_times_this_day') ?></p>
                            <div id="nextAvailableDate" class="next-available-date"></div>
                            <div class="waitlist-divider"><span><?= $__('or_join_waitlist') ?></span></div>
                            <button type="button" id="waitlistBtn" class="waitlist-btn">
                                <i class="fas fa-clipboard-list"></i> <?= $__('join_waitlist') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guest Info (if not logged in) -->
            <?php if (!isset($user)): ?>
                <div class="guest-info-box">
                    <h4><i class="fas fa-user"></i> <?= $__('your_details') ?></h4>
                    <p class="guest-login-hint">
                        <a href="/login"><?= $__('login') ?></a> <?= $__('login_faster_booking') ?>
                    </p>
                    <div class="guest-fields">
                        <div class="form-group">
                            <label class="form-label"><?= $__('name') ?></label>
                            <input type="text" name="guest_name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?= $__('email') ?></label>
                            <input type="email" name="guest_email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?= $__('phone') ?></label>
                            <input type="tel" name="guest_phone" class="form-input">
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Notes -->
            <div class="form-group" style="margin-top:1.25rem">
                <label class="form-label"><i class="fas fa-comment"></i> <?= $__('notes') ?> (<?= $__('optional') ?>)</label>
                <textarea name="notes" class="notes-textarea" rows="3" placeholder="<?= $__('notes_placeholder') ?>"></textarea>
            </div>

            <!-- Terms Acceptance -->
            <label class="terms-box">
                <input type="checkbox" name="accept_terms" id="accept_terms" required class="terms-checkbox">
                <span class="terms-text">
                    <?= $__('agree_terms') ?>
                    <a href="/terms" target="_blank"><?= $__('terms') ?></a>
                    <?= $__('and') ?>
                    <a href="/privacy" target="_blank"><?= $__('privacy') ?></a>.
                    <?= $__('terms_cancel_notice') ?>
                </span>
            </label>

            <!-- QR Code Notice -->
            <div class="qr-notice">
                <div class="qr-notice-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="qr-notice-content">
                    <h4><i class="fas fa-info-circle"></i> <?= $translations['qr_scan_required'] ?? 'QR Code Scan Required' ?></h4>
                    <p><?= $translations['qr_scan_desc'] ?? 'Upon arrival at the salon, you must scan the QR code to confirm your booking. Without scanning, the booking will not be registered as completed.' ?></p>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-check"></i> <?= $__('confirm_booking') ?>
            </button>
        </form>
    </div>
</div>

<script>
const businessSlug = '<?= htmlspecialchars($business['slug']) ?>';
const dateInput = document.getElementById('bookingDate');
const timeSelect = document.getElementById('bookingTime');
const serviceInputs = document.querySelectorAll('input[name="service_id"]');
const employeeInputs = document.querySelectorAll('input[name="employee_id"]');
const timeLoading = document.getElementById('timeLoading');
const hasEmployees = <?= !empty($employees) ? 'true' : 'false' ?>;

// Translation strings for JavaScript
const translations = {
    selectDateServiceFirst: '<?= addslashes($__('select_date_service_first')) ?>',
    loadingAvailability: '<?= addslashes($__('loading_availability')) ?>',
    errorLoading: '<?= addslashes($__('error_loading')) ?>',
    selectTimeSlot: '<?= addslashes($__('select_time_slot')) ?>',
    timeBooked: '<?= addslashes($__('time_booked')) ?>',
    noTimesThisDay: '<?= addslashes($__('no_times_this_day')) ?>',
    selectEmployeeFirst: '<?= addslashes($translations['select_employee_first'] ?? 'Please select an employee first') ?>',
    nextAvailableDate: '<?= addslashes($__('next_available_date')) ?>',
    joinWaitlist: '<?= addslashes($__('join_waitlist')) ?>',
    waitlistForDate: '<?= addslashes($__('waitlist_for_date')) ?>',
    noAvailableDates60: '<?= addslashes($translations['no_available_dates_60'] ?? 'No available dates found in the next 60 days.') ?>',
    selectDateAndService: '<?= addslashes($translations['select_date_and_service'] ?? 'Please select a date and service first.') ?>',
    waitlistNameLabel: '<?= addslashes($translations['waitlist_name_label'] ?? 'Name *') ?>',
    waitlistEmailLabel: '<?= addslashes($translations['waitlist_email_label'] ?? 'Email *') ?>',
    waitlistPhoneLabel: '<?= addslashes($translations['waitlist_phone_label'] ?? 'Phone') ?>',
    waitlistNotesLabel: '<?= addslashes($translations['waitlist_notes_label'] ?? 'Notes') ?>',
    waitlistNotesPlaceholder: '<?= addslashes($translations['waitlist_notes_placeholder'] ?? 'E.g. preferred time or other wishes...') ?>',
    waitlistSubmitBtn: '<?= addslashes($translations['waitlist_submit_btn'] ?? 'Sign up for waitlist') ?>',
    waitlistSuccess: '<?= addslashes($translations['waitlist_success'] ?? 'Success!') ?>',
    waitlistError: '<?= addslashes($translations['waitlist_error'] ?? 'An error occurred.') ?>',
    timeAtLabel: '<?= addslashes($translations['time_at_label'] ?? 'at') ?>'
};

const noTimesInfo = document.getElementById('noTimesInfo');
const nextAvailableDateEl = document.getElementById('nextAvailableDate');
const waitlistBtn = document.getElementById('waitlistBtn');

function loadAvailableTimes() {
    const date = dateInput.value;
    const serviceInput = document.querySelector('input[name="service_id"]:checked');
    const employeeInput = document.querySelector('input[name="employee_id"]:checked');

    // For BV businesses, require employee selection
    if (hasEmployees && !employeeInput) {
        timeSelect.innerHTML = `<option value="">${translations.selectEmployeeFirst}</option>`;
        timeSelect.disabled = true;
        return;
    }

    if (!date || !serviceInput) {
        timeSelect.innerHTML = `<option value="">${translations.selectDateServiceFirst}</option>`;
        timeSelect.disabled = true;
        return;
    }

    const serviceId = serviceInput.value;
    const employeeId = employeeInput ? employeeInput.value : '';
    timeSelect.disabled = true;
    timeLoading.style.display = 'block';

    let url = `/api/available-times/${businessSlug}?date=${date}&service_id=${serviceId}`;
    if (employeeId) {
        url += `&employee_id=${employeeId}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            timeLoading.style.display = 'none';

            if (data.error) {
                timeSelect.innerHTML = `<option value="">${translations.errorLoading}</option>`;
                return;
            }

            let html = `<option value="">${translations.selectTimeSlot}</option>`;
            let hasAvailable = false;

            data.slots.forEach(slot => {
                if (slot.available) {
                    html += `<option value="${slot.time}">${slot.time}</option>`;
                    hasAvailable = true;
                } else {
                    html += `<option value="${slot.time}" disabled style="color:#999">${slot.time} (${translations.timeBooked})</option>`;
                }
            });

            if (!hasAvailable) {
                html = `<option value="">${translations.noTimesThisDay}</option>`;

                // Show next available date info and auto-jump to next available date
                noTimesInfo.style.display = 'block';

                // Auto-jump to next available date if found
                if (data.nextAvailable && data.nextAvailable.date) {
                    dateInput.value = data.nextAvailable.date;
                    // Reload times for the new date (with a small delay to prevent infinite loop)
                    setTimeout(() => {
                        noTimesInfo.style.display = 'none';
                        loadAvailableTimes();
                    }, 100);
                    return;
                }

                if (data.nextAvailable) {
                    const nextDate = new Date(data.nextAvailable.date);
                    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                    const formattedDate = nextDate.toLocaleDateString('<?= $lang ?? 'nl' ?>', options);

                    nextAvailableDateEl.innerHTML = `
                        <p><strong>${translations.nextAvailableDate}:</strong></p>
                        <a href="#" class="next-date-link" data-date="${data.nextAvailable.date}" data-time="${data.nextAvailable.time}">
                            <i class="fas fa-calendar-check"></i> ${formattedDate} ${translations.timeAtLabel} ${data.nextAvailable.time}
                        </a>
                    `;

                    // Add click handler to jump to next available date
                    nextAvailableDateEl.querySelector('.next-date-link').addEventListener('click', function(e) {
                        e.preventDefault();
                        dateInput.value = this.dataset.date;
                        loadAvailableTimes();
                    });
                } else {
                    nextAvailableDateEl.innerHTML = `<p class="no-next-date">${translations.noAvailableDates60}</p>`;
                }

                // Store current date for waitlist
                waitlistBtn.dataset.date = date;
            } else {
                noTimesInfo.style.display = 'none';
            }

            timeSelect.innerHTML = html;
            timeSelect.disabled = !hasAvailable;

            // Auto-select first available time slot
            if (hasAvailable) {
                const firstAvailableOption = timeSelect.querySelector('option:not([disabled]):not([value=""])');
                if (firstAvailableOption) {
                    timeSelect.value = firstAvailableOption.value;
                }
            }
        })
        .catch(error => {
            timeLoading.style.display = 'none';
            timeSelect.innerHTML = `<option value="">${translations.errorLoading}</option>`;
            console.error('Error:', error);
        });
}

dateInput.addEventListener('change', loadAvailableTimes);
serviceInputs.forEach(input => input.addEventListener('change', loadAvailableTimes));
employeeInputs.forEach(input => input.addEventListener('change', loadAvailableTimes));

// Auto-select first service if none selected
const firstService = document.querySelector('input[name="service_id"]');
if (firstService && !document.querySelector('input[name="service_id"]:checked')) {
    firstService.checked = true;
}

// Auto-select first employee if available and none selected
const firstEmployee = document.querySelector('input[name="employee_id"]');
if (firstEmployee && !document.querySelector('input[name="employee_id"]:checked')) {
    firstEmployee.checked = true;
}

// Auto-set today's date if not set
if (!dateInput.value) {
    dateInput.value = '<?= date('Y-m-d') ?>';
}

// Load times automatically on page load
loadAvailableTimes();

// Waitlist button handler
waitlistBtn.addEventListener('click', function() {
    const date = this.dataset.date;
    const serviceInput = document.querySelector('input[name="service_id"]:checked');
    const serviceId = serviceInput ? serviceInput.value : '';

    if (!date || !serviceId) {
        alert(translations.selectDateAndService);
        return;
    }

    // Show waitlist modal/form
    showWaitlistModal(date, serviceId);
});

function showWaitlistModal(date, serviceId) {
    const formattedDate = new Date(date).toLocaleDateString('<?= $lang ?? 'nl' ?>', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

    const modal = document.createElement('div');
    modal.className = 'waitlist-modal';
    modal.innerHTML = `
        <div class="waitlist-modal-content">
            <button class="waitlist-modal-close" onclick="this.closest('.waitlist-modal').remove()">&times;</button>
            <h3><i class="fas fa-clipboard-list"></i> ${translations.waitlistForDate} ${formattedDate}</h3>
            <form id="waitlistForm" class="waitlist-form">
                <input type="hidden" name="service_id" value="${serviceId}">
                <input type="hidden" name="date" value="${date}">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <div class="form-group">
                    <label class="form-label">${translations.waitlistNameLabel}</label>
                    <input type="text" name="name" class="form-input" required value="<?= isset($user) ? htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) : '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">${translations.waitlistEmailLabel}</label>
                    <input type="email" name="email" class="form-input" required value="<?= isset($user) ? htmlspecialchars($user['email']) : '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">${translations.waitlistPhoneLabel}</label>
                    <input type="tel" name="phone" class="form-input" value="<?= isset($user) ? htmlspecialchars($user['phone'] ?? '') : '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">${translations.waitlistNotesLabel}</label>
                    <textarea name="notes" class="form-input" rows="2" placeholder="${translations.waitlistNotesPlaceholder}"></textarea>
                </div>
                <button type="submit" class="submit-btn waitlist-submit">
                    <i class="fas fa-check"></i> ${translations.waitlistSubmitBtn}
                </button>
            </form>
        </div>
    `;
    document.body.appendChild(modal);

    // Handle form submission
    document.getElementById('waitlistForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(`/api/waitlist/${businessSlug}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.innerHTML = `
                    <div class="waitlist-modal-content">
                        <button class="waitlist-modal-close" onclick="this.closest('.waitlist-modal').remove()">&times;</button>
                        <div class="waitlist-success">
                            <i class="fas fa-check-circle"></i>
                            <h3>${translations.waitlistSuccess}</h3>
                            <p>${data.message}</p>
                        </div>
                    </div>
                `;
            } else {
                alert(data.error || translations.waitlistError);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(translations.waitlistError);
        });
    });
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

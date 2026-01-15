<?php ob_start(); ?>

<style>
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
    background: var(--white);
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.booking-title {
    font-size: 1.25rem;
    margin: 0 0 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.booking-title i {
    color: var(--primary);
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
    border: 2px solid var(--border);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: var(--white);
}
.service-option:active {
    transform: scale(0.98);
}
.service-option:hover {
    border-color: var(--primary);
    background: rgba(0,0,0,0.02);
}
.service-option:has(input:checked) {
    border-color: var(--primary) !important;
    background: rgba(0,0,0,0.05) !important;
}
.service-radio {
    margin-right: 0.75rem;
    margin-top: 0.2rem;
    width: 20px;
    height: 20px;
    accent-color: var(--primary);
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
    color: var(--primary);
    font-size: 1.1rem;
    font-weight: 700;
    white-space: nowrap;
    margin-left: 0.5rem;
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
}
.form-label i {
    color: var(--primary);
}
.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-size: 1rem;
    background: var(--white);
    transition: border-color 0.2s;
}
.form-input:focus {
    outline: none;
    border-color: var(--primary);
}
.time-loading {
    display: none;
    margin-top: 0.5rem;
    color: var(--text-light);
    font-size: 0.85rem;
}

/* Guest Info Box */
.guest-info-box {
    background: var(--secondary);
    padding: 1.25rem;
    border-radius: 12px;
    margin: 1.25rem 0;
}
.guest-info-box h4 {
    margin: 0 0 0.75rem;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.guest-info-box h4 i {
    color: var(--primary);
}
.guest-login-hint {
    color: var(--text-light);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}
.guest-login-hint a {
    color: var(--primary);
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

/* Notes */
.notes-textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-size: 1rem;
    resize: vertical;
    min-height: 80px;
    font-family: inherit;
}
.notes-textarea:focus {
    outline: none;
    border-color: var(--primary);
}

/* Terms */
.terms-box {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--secondary);
    border-radius: 12px;
    margin-top: 1.25rem;
    cursor: pointer;
}
.terms-checkbox {
    margin-top: 0.15rem;
    width: 20px;
    height: 20px;
    accent-color: var(--primary);
    flex-shrink: 0;
}
.terms-text {
    font-size: 0.9rem;
    color: var(--text);
    line-height: 1.5;
}
.terms-text a {
    color: var(--primary);
    font-weight: 600;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    padding: 1rem;
    margin-top: 1.25rem;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark, #737373));
    color: white;
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
}
.submit-btn:active {
    transform: scale(0.98);
}

/* Alert */
.alert-error {
    background: #f5f5f5;
    color: #dc2626;
    border: 1px solid #e5e5e5;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

/* QR Code Notice */
.qr-notice {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: #000000;
    border: 2px solid #000000;
    border-radius: 12px;
    padding: 1.25rem;
    margin: 1.25rem 0;
    color: #ffffff;
}
.qr-notice-icon {
    width: 50px;
    height: 50px;
    background: #ffffff;
    color: #000000;
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
    color: #ffffff;
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
    color: rgba(255, 255, 255, 0.9);
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
                    <h4><i class="fas fa-info-circle"></i> QR-Code Scannen Verplicht</h4>
                    <p>Bij aankomst in de salon moet je de QR-code scannen om je boeking te bevestigen. Zonder scan wordt de boeking niet als voltooid geregistreerd.</p>
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
const timeLoading = document.getElementById('timeLoading');

// Translation strings for JavaScript
const translations = {
    selectDateServiceFirst: '<?= addslashes($__('select_date_service_first')) ?>',
    loadingAvailability: '<?= addslashes($__('loading_availability')) ?>',
    errorLoading: '<?= addslashes($__('error_loading')) ?>',
    selectTimeSlot: '<?= addslashes($__('select_time_slot')) ?>',
    timeBooked: '<?= addslashes($__('time_booked')) ?>',
    noTimesThisDay: '<?= addslashes($__('no_times_this_day')) ?>'
};

function loadAvailableTimes() {
    const date = dateInput.value;
    const serviceInput = document.querySelector('input[name="service_id"]:checked');

    if (!date || !serviceInput) {
        timeSelect.innerHTML = `<option value="">${translations.selectDateServiceFirst}</option>`;
        timeSelect.disabled = true;
        return;
    }

    const serviceId = serviceInput.value;
    timeSelect.disabled = true;
    timeLoading.style.display = 'block';

    fetch(`/api/available-times/${businessSlug}?date=${date}&service_id=${serviceId}`)
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
            }

            timeSelect.innerHTML = html;
            timeSelect.disabled = !hasAvailable;
        })
        .catch(error => {
            timeLoading.style.display = 'none';
            timeSelect.innerHTML = `<option value="">${translations.errorLoading}</option>`;
            console.error('Error:', error);
        });
}

dateInput.addEventListener('change', loadAvailableTimes);
serviceInputs.forEach(input => input.addEventListener('change', loadAvailableTimes));

// Load times if date and service are already selected
if (dateInput.value && document.querySelector('input[name="service_id"]:checked')) {
    loadAvailableTimes();
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

<?php ob_start(); ?>

<style>
    .register-container {
        max-width: 900px;
        margin: 1rem auto;
        padding: 0 1rem;
    }
    @media (max-width: 768px) {
        .register-container {
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
        .register-container .card {
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
        }
        .register-hero {
            border-radius: 0 !important;
            margin: 0 !important;
        }
        .form-group {
            text-align: left;
        }
        .form-group label {
            justify-content: flex-start;
        }
        .form-control {
            width: 100%;
            max-width: 100%;
            text-align: left;
        }
        .grid-2 {
            grid-template-columns: 1fr !important;
        }
        .section-header {
            justify-content: flex-start;
        }
        .password-wrapper {
            width: 100%;
        }
    }
    .register-hero {
        background: #000000;
        padding: 3rem 2rem;
        text-align: center;
        color: #ffffff;
        margin: -2rem -2rem 2rem -2rem;
        border-radius: 0 0 30px 30px;
        position: relative;
        overflow: hidden;
        border-bottom: 2px solid #333333;
    }
    .register-hero h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #ffffff;
    }
    .register-hero p {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.8);
    }
    .early-adopter-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: transparent;
        border: 2px solid #ffffff;
        color: #ffffff;
        padding: 1rem 1.5rem;
        border-radius: 50px;
        margin-top: 1.5rem;
        font-weight: 600;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
    /* Grid Layout - single column for all fields */
    .grid {
        display: grid;
        gap: 1.5rem;
    }
    .grid-2, .grid-3 {
        grid-template-columns: 1fr;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 0;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 1.5rem;
    }
    .section-header i {
        width: 40px;
        height: 40px;
        background: #ffffff;
        color: #000000;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .section-header h4 {
        margin: 0;
        color: #ffffff;
        font-size: 1.1rem;
    }
    .form-group label {
        font-weight: 500;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .form-group label i {
        color: #ffffff;
        font-size: 0.9rem;
    }
    .form-control {
        background: transparent;
        border: none;
        border-bottom: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 0;
        padding: 0.9rem 0;
        transition: all 0.3s ease;
        font-size: 1rem;
        color: #ffffff;
    }
    .form-control:focus {
        border-bottom-color: #ffffff;
        box-shadow: none;
        outline: none;
    }
    .form-control:hover {
        border-bottom-color: rgba(255, 255, 255, 0.7);
    }
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }
    select.form-control {
        padding: 0.9rem 0;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0 center;
        background-size: 1rem;
        padding-right: 1.5rem;
        cursor: pointer;
    }
    select.form-control option {
        background: #000000;
        color: #ffffff;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    .pricing-card {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        padding: 1.5rem;
        margin: 1.5rem 0;
        position: relative;
        overflow: hidden;
    }
    .pricing-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ffffff;
    }
    .price-display {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .price-amount {
        font-size: 2rem;
        font-weight: 800;
        color: #ffffff;
        -webkit-text-fill-color: #ffffff;
    }
    .price-period {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
    }
    .price-note {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
    }
    .price-note i {
        color: #ffffff;
    }
    .terms-box {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }
    .terms-box label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
        margin: 0;
    }
    .terms-box span {
        color: rgba(255, 255, 255, 0.9);
    }
    .terms-box a {
        color: #ffffff;
    }
    .terms-box input[type="checkbox"] {
        width: 22px;
        height: 22px;
        accent-color: #ffffff;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .submit-btn {
        width: 100%;
        padding: 1.1rem;
        font-size: 1.1rem;
        font-weight: 600;
        background: #ffffff;
        color: #000000;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
    }

    /* Business Type Selector */
    .business-type-selector {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    @media (max-width: 600px) {
        .business-type-selector {
            grid-template-columns: 1fr;
        }
    }
    .business-type-option {
        cursor: pointer;
    }
    .business-type-option input {
        display: none;
    }
    .business-type-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        transition: all 0.3s ease;
    }
    .business-type-card i {
        font-size: 2rem;
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s ease;
    }
    .business-type-card .type-title {
        font-size: 1rem;
        font-weight: 600;
        color: #ffffff;
    }
    .business-type-card .type-desc {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
    }
    .business-type-option input:checked + .business-type-card {
        background: rgba(255, 255, 255, 0.15);
        border-color: #ffffff;
    }
    .business-type-option input:checked + .business-type-card i {
        color: #ffffff;
    }
    .business-type-card:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.4);
    }
    .field-hint {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .field-hint i {
        color: #fbbf24;
    }

    .benefits-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }
    .benefit-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.9);
    }
    .benefit-item i {
        color: #ffffff;
    }
    .input-group {
        position: relative;
    }
    .input-icon {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.6);
    }
    .input-group .form-control {
        padding-left: 1.75rem;
    }

    /* Dark Mode Styles */
    [data-theme="dark"] .card {
        background: var(--bg-card);
    }
    [data-theme="dark"] .section-header {
        border-bottom-color: var(--border);
    }
    [data-theme="dark"] .section-header h4 {
        color: var(--text);
    }
    [data-theme="dark"] .form-group label {
        color: var(--text);
    }
    [data-theme="dark"] .form-control {
        background: transparent;
        border-bottom-color: rgba(255, 255, 255, 0.3);
        color: var(--white);
    }
    [data-theme="dark"] .form-control:focus {
        border-bottom-color: var(--white);
        box-shadow: none;
    }
    [data-theme="dark"] .form-control:hover {
        border-bottom-color: rgba(255, 255, 255, 0.6);
    }
    [data-theme="dark"] .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }
    [data-theme="dark"] .pricing-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.1));
        border-color: var(--primary);
    }
    [data-theme="dark"] .price-period {
        color: var(--text-light);
    }
    [data-theme="dark"] .price-note {
        color: var(--text-light);
    }
    [data-theme="dark"] .terms-box {
        background: var(--bg-secondary);
    }
    [data-theme="dark"] .terms-box span {
        color: var(--text);
    }
    [data-theme="dark"] .benefit-item {
        color: var(--text);
    }
    [data-theme="dark"] .benefits-list {
        border-top-color: var(--border);
    }
    [data-theme="dark"] .alert-danger {
        background: rgba(220, 38, 38, 0.1) !important;
        border-color: rgba(220, 38, 38, 0.3) !important;
        color: #f87171 !important;
    }
    [data-theme="dark"] select.form-control option {
        background: var(--bg-card);
        color: var(--text);
    }

    /* QR Code Warning */
    .qr-warning {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid #ffffff;
        border-radius: 12px;
        padding: 1.25rem;
        margin: 1.5rem 0;
    }
    .qr-warning-icon {
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
    .qr-warning-content h4 {
        margin: 0 0 0.5rem;
        font-size: 1rem;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .qr-warning-content h4 i {
        color: #fbbf24;
    }
    .qr-warning-content p {
        margin: 0;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.5;
    }
    .qr-warning-content strong {
        color: #ffffff;
    }
    @media (max-width: 768px) {
        .qr-warning {
            flex-direction: row;
            text-align: left;
            align-items: flex-start;
        }
        .qr-warning-content h4 {
            justify-content: flex-start;
        }
    }

    /* Password Toggle */
    .password-wrapper {
        position: relative;
    }
    .password-toggle {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .password-toggle:hover {
        color: #ffffff;
    }
    .password-wrapper .form-control {
        padding-right: 2rem;
    }
</style>

<div class="container register-container">
    <div class="card" style="padding:0;overflow:hidden;background:#000000;border-color:#333333">
        <div class="register-hero">
            <h2><i class="fas fa-store"></i> <?= $__('register_your_salon') ?></h2>
            <p><?= $__('start_receiving_bookings') ?></p>

            <?php if ($isEarlyAdopter): ?>
                <div class="early-adopter-badge">
                    <i class="fas fa-star"></i>
                    <span>Early Adopter: <?= $__('early_adopter_badge', ['count' => 100 - ($earlyAdopterCount ?? 0)]) ?> &euro;0,99!</span>
                </div>
            <?php endif; ?>
        </div>

        <div style="padding:2rem;background:#000000">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.3);color:#ffffff;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.5rem">
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong><?= $__('note') ?>:</strong>
                    </div>
                    <ul style="margin:0;padding-left:1.5rem">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="/business/register">
                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">

                <!-- Bedrijfsgegevens -->
                <div class="section-header">
                    <i class="fas fa-building"></i>
                    <h4><?= $__('business_details') ?></h4>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-store"></i> <?= $__('business_name') ?> *</label>
                    <input type="text" name="name" class="form-control" placeholder="<?= $__('business_name_placeholder') ?>" value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-briefcase"></i> Bedrijfsvorm *</label>
                    <div class="business-type-selector">
                        <label class="business-type-option" id="type-eenmanszaak">
                            <input type="radio" name="business_type" value="eenmanszaak" checked onchange="updateBusinessType()">
                            <div class="business-type-card">
                                <i class="fas fa-user"></i>
                                <span class="type-title">Eenmanszaak</span>
                                <span class="type-desc">Ik werk alleen</span>
                            </div>
                        </label>
                        <label class="business-type-option" id="type-bv">
                            <input type="radio" name="business_type" value="bv" onchange="updateBusinessType()">
                            <div class="business-type-card">
                                <i class="fas fa-users"></i>
                                <span class="type-title">BV / Meerdere medewerkers</span>
                                <span class="type-desc">Ik heb personeel</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Employee count (only shown for BV) -->
                <div class="form-group" id="employee-count-group" style="display: none;">
                    <label><i class="fas fa-user-plus"></i> Aantal medewerkers (excl. uzelf)</label>
                    <input type="number" name="employee_count" id="employee_count" class="form-control" placeholder="0" min="0" max="50" value="0" onchange="updatePricing()">
                    <p class="field-hint"><i class="fas fa-info-circle"></i> Per medewerker: +&euro;4,99 eenmalig. U kunt later medewerkers toevoegen in het dashboard.</p>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> <?= $__('email') ?> *</label>
                        <input type="email" name="email" class="form-control" placeholder="info@jouwsalon.nl" value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> <?= $__('phone') ?></label>
                        <input type="tel" name="phone" class="form-control" placeholder="06 12345678" value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> <?= $__('password') ?> * <span style="font-weight:400;color:#9ca3af">(<?= $__('min_chars', ['count' => 8]) ?>)</span></label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-control" placeholder="<?= $__('choose_strong_pw') ?>" minlength="8" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> <?= $__('confirm_password') ?> *</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="<?= $__('repeat_password') ?>" minlength="8" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirm', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tags"></i> <?= $__('category') ?> *</label>
                    <select name="category_id" class="form-control" required>
                        <option value=""><?= $__('select_category') ?></option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['translated_name'] ?? $cat['name'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['category_id'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['category_id']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Locatie -->
                <div class="section-header" style="margin-top:2rem">
                    <i class="fas fa-map-marker-alt"></i>
                    <h4><?= $__('location') ?></h4>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-road"></i> <?= $__('address') ?> *</label>
                    <input type="text" name="address" class="form-control" placeholder="<?= $__('street_example') ?>" value="<?= htmlspecialchars($data['address'] ?? '') ?>" required>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-mail-bulk"></i> <?= $__('postal_code') ?> *</label>
                        <input type="text" name="postal_code" class="form-control" placeholder="1234 AB" value="<?= htmlspecialchars($data['postal_code'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-city"></i> <?= $__('city') ?> *</label>
                        <input type="text" name="city" class="form-control" placeholder="Amsterdam" value="<?= htmlspecialchars($data['city'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-file-contract"></i> <?= $__('kvk_number') ?> <span style="font-weight:400;color:#9ca3af">(<?= $__('optional') ?>)</span></label>
                    <input type="text" name="kvk_number" class="form-control" placeholder="12345678" value="<?= htmlspecialchars($data['kvk_number'] ?? '') ?>" pattern="\d{8}" maxlength="8">
                    <small style="color:#9ca3af;display:block;margin-top:4px;"><?= $__('kvk_verification_note') ?></small>
                    <?php if (isset($errors['kvk_number'])): ?>
                        <span class="error-text"><?= htmlspecialchars($errors['kvk_number']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> <?= $__('description') ?> <span style="font-weight:400;color:#9ca3af">(<?= $__('optional') ?>)</span></label>
                    <textarea name="description" class="form-control" rows="3" placeholder="<?= $__('tell_about_salon') ?>"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                </div>

                <!-- Pricing -->
                <div class="pricing-card">
                    <div class="price-display">
                        <span class="price-amount">&euro;<?= number_format($regFee, 2, ',', '.') ?></span>
                        <span class="price-period">eenmalig (na 14 dagen proeftijd)</span>
                    </div>
                    <div class="price-note" id="employee-price-note" style="display:none; color: #fbbf24;">
                        <i class="fas fa-users"></i>
                        <span>0 medewerker(s): +&euro;0,00</span>
                    </div>
                    <div class="price-note">
                        <i class="fas fa-info-circle"></i>
                        <span>Geen maandelijkse kosten, alleen &euro;1,75 per boeking</span>
                    </div>
                    <div class="price-note" style="font-size: 0.8rem; opacity: 0.7;">
                        <i class="fas fa-tag"></i>
                        <span>Normale prijs na promotie: &euro;99,99</span>
                    </div>

                    <div class="benefits-list">
                        <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $__('benefit_unlimited') ?></div>
                        <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $__('benefit_page') ?></div>
                        <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $__('benefit_email') ?></div>
                        <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $__('benefit_dashboard') ?></div>
                        <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $__('benefit_customers') ?></div>
                        <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $__('benefit_payments') ?></div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="terms-box">
                    <label>
                        <input type="checkbox" name="terms" required>
                        <span><?= $__('agree_terms') ?> <a href="/terms" target="_blank" style="color:#ffffff;font-weight:500"><?= $__('terms') ?></a> <?= $__('and') ?> <a href="/privacy" target="_blank" style="color:#ffffff;font-weight:500"><?= $__('privacy') ?></a> *</span>
                    </label>
                </div>

                <!-- Trial Period Notice -->
                <div class="qr-warning" style="border-color: rgba(34, 197, 94, 0.3); background: rgba(34, 197, 94, 0.1);">
                    <div class="qr-warning-icon" style="background: rgba(34, 197, 94, 0.2);">
                        <i class="fas fa-gift" style="color: #22c55e;"></i>
                    </div>
                    <div class="qr-warning-content">
                        <h4 style="color: #22c55e;"><i class="fas fa-clock"></i> 14 Dagen Gratis Proefperiode</h4>
                        <p>De eerste 14 dagen zijn volledig gratis. Na de proefperiode wordt het afgesproken bedrag in rekening gebracht.</p>
                    </div>
                </div>

                <!-- QR Code Warning -->
                <div class="qr-warning">
                    <div class="qr-warning-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="qr-warning-content">
                        <h4><i class="fas fa-exclamation-triangle"></i> Belangrijk: QR-Code Scannen Verplicht</h4>
                        <p>Let op: bij elke boeking moet de QR-code bij aankomst worden gescand om de afspraak te bevestigen. Zonder gescande QR-code kunnen we de boeking niet goedkeuren en vindt er geen uitbetaling plaats.</p>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-rocket"></i> <?= $__('register_my_salon') ?>
                </button>
            </form>

            <p class="text-center" style="margin-top:1.5rem;color:rgba(255,255,255,0.7)">
                <?= $__('already_registered') ?> <a href="/login" style="color:#ffffff;font-weight:600"><?= $__('login') ?></a>
            </p>
        </div>
    </div>
</div>

<script>
const BASE_PRICE = <?= $regFee ?? 0.99 ?>;
const EMPLOYEE_PRICE = 4.99;

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function updateBusinessType() {
    const businessType = document.querySelector('input[name="business_type"]:checked').value;
    const employeeGroup = document.getElementById('employee-count-group');

    if (businessType === 'bv') {
        employeeGroup.style.display = 'block';
    } else {
        employeeGroup.style.display = 'none';
        document.getElementById('employee_count').value = 0;
    }
    updatePricing();
}

function updatePricing() {
    const businessType = document.querySelector('input[name="business_type"]:checked').value;
    const employeeCount = parseInt(document.getElementById('employee_count').value) || 0;

    let totalPrice = BASE_PRICE;
    if (businessType === 'bv' && employeeCount > 0) {
        totalPrice += (employeeCount * EMPLOYEE_PRICE);
    }

    const priceDisplay = document.querySelector('.price-amount');
    if (priceDisplay) {
        priceDisplay.textContent = '€' + totalPrice.toFixed(2).replace('.', ',');
    }

    // Update employee price note
    const employeePriceNote = document.getElementById('employee-price-note');
    if (employeePriceNote && employeeCount > 0) {
        employeePriceNote.style.display = 'block';
        employeePriceNote.innerHTML = '<i class="fas fa-users"></i> ' + employeeCount + ' medewerker(s): +€' + (employeeCount * EMPLOYEE_PRICE).toFixed(2).replace('.', ',');
    } else if (employeePriceNote) {
        employeePriceNote.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateBusinessType();
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

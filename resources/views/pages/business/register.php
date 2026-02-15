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
                    <span>Early Bird #<?= $earlyAdopterCount + 1 ?>/100:
                        <?php if ($showDualCurrency ?? false): ?>
                            <?= $localPrice ?> (<?= $eurPrice ?>)
                        <?php else: ?>
                            <?= $eurPrice ?>
                        <?php endif; ?>
                    </span>
                </div>
                <p style="color:#888;font-size:0.9rem;margin-top:0.5rem"><?= $__('spots_left', ['count' => $spotsLeft, 'country' => htmlspecialchars($countryName)]) ?></p>
            <?php endif; ?>

            <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:0.75rem;margin-top:1rem;font-size:0.85rem">
                <span style="display:inline-flex;align-items:center;gap:0.4rem;background:rgba(255,255,255,0.1);padding:0.4rem 0.8rem;border-radius:20px">
                    <i class="fas fa-receipt" style="color:#fbbf24;font-size:0.75rem"></i>
                    <?= $__('reg_fee_step1_title') ?>: <?= $feeDisplay ?? '€1,75' ?>
                </span>
                <span style="display:inline-flex;align-items:center;gap:0.4rem;background:rgba(34,197,94,0.15);color:#4ade80;padding:0.4rem 0.8rem;border-radius:20px;font-weight:600">
                    <i class="fas fa-gift" style="font-size:0.75rem"></i>
                    <?= $__('reg_fee_step2_title') ?>
                </span>
                <span style="display:inline-flex;align-items:center;gap:0.4rem;background:rgba(255,255,255,0.06);padding:0.4rem 0.8rem;border-radius:20px;opacity:0.7">
                    <i class="fas fa-sync-alt" style="font-size:0.7rem"></i>
                    <?= $__('reg_fee_reset_monthly') ?>
                </span>
            </div>
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
                    <label><i class="fas fa-briefcase"></i> <?= $__('business_form') ?> *</label>
                    <div class="business-type-selector">
                        <label class="business-type-option" id="type-eenmanszaak">
                            <input type="radio" name="business_type" value="eenmanszaak" checked onchange="updateBusinessType()">
                            <div class="business-type-card">
                                <i class="fas fa-user"></i>
                                <span class="type-title"><?= $__('sole_proprietorship') ?></span>
                                <span class="type-desc"><?= $__('i_work_alone') ?></span>
                            </div>
                        </label>
                        <label class="business-type-option" id="type-bv">
                            <input type="radio" name="business_type" value="bv" onchange="updateBusinessType()">
                            <div class="business-type-card">
                                <i class="fas fa-users"></i>
                                <span class="type-title"><?= $__('company_with_employees') ?></span>
                                <span class="type-desc"><?= $__('i_have_staff') ?></span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Employee count (only shown for BV) -->
                <div class="form-group" id="employee-count-group" style="display: none;">
                    <label><i class="fas fa-user-plus"></i> <?= $__('number_of_employees') ?></label>
                    <input type="number" name="employee_count" id="employee_count" class="form-control" placeholder="0" min="0" max="50" value="0" onchange="updatePricing()">
                    <p class="field-hint"><i class="fas fa-info-circle"></i> <?= $__('employee_price_note') ?></p>
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
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="<?= $__('show_password') ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> <?= $__('confirm_password') ?> *</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="<?= $__('repeat_password') ?>" minlength="8" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirm', this)" aria-label="<?= $__('show_password') ?>">
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

                <?php
                    $selectedCountry = $data['country'] ?? $countryCode ?? '';
                    $countries = [
                        'NL' => 'Nederland',
                        'BE' => 'België / Belgique',
                        'DE' => 'Deutschland',
                        'FR' => 'France',
                        'GB' => 'United Kingdom',
                        'ES' => 'España',
                        'IT' => 'Italia',
                        'PT' => 'Portugal',
                        'AT' => 'Österreich',
                        'CH' => 'Schweiz / Suisse',
                        'LU' => 'Luxembourg',
                        'PL' => 'Polska',
                        'US' => 'United States',
                        'CA' => 'Canada',
                        'AU' => 'Australia',
                        'TR' => 'Türkiye',
                        'MA' => 'المغرب / Maroc',
                        'AE' => 'الإمارات / UAE',
                    ];
                    $flags = [
                        'NL' => '🇳🇱', 'BE' => '🇧🇪', 'DE' => '🇩🇪', 'FR' => '🇫🇷', 'GB' => '🇬🇧',
                        'ES' => '🇪🇸', 'IT' => '🇮🇹', 'PT' => '🇵🇹', 'AT' => '🇦🇹', 'CH' => '🇨🇭',
                        'LU' => '🇱🇺', 'PL' => '🇵🇱', 'US' => '🇺🇸', 'CA' => '🇨🇦', 'AU' => '🇦🇺',
                        'TR' => '🇹🇷', 'MA' => '🇲🇦', 'AE' => '🇦🇪',
                    ];
                ?>
                <div class="form-group">
                    <label><i class="fas fa-globe"></i> <?= $__('country') ?? 'Land' ?> *</label>
                    <select name="country" id="country-select" class="form-control" required onchange="updateBusinessFields()">
                        <option value="">-- <?= $__('select_country') ?? 'Selecteer land' ?> --</option>
                        <?php foreach ($countries as $code => $name): ?>
                            <option value="<?= $code ?>" <?= $selectedCountry === $code ? 'selected' : '' ?>><?= $flags[$code] ?? '' ?> <?= $name ?></option>
                        <?php endforeach; ?>
                        <option value="OTHER" <?= ($selectedCountry === 'OTHER' || (!isset($countries[$selectedCountry]) && !empty($selectedCountry))) ? 'selected' : '' ?>>🌍 <?= $__('other_country') ?? 'Other' ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-road"></i> <span id="street-label"><?= $__('address') ?> *</span></label>
                    <input type="text" name="address" class="form-control" placeholder="<?= $__('street_example') ?>" value="<?= htmlspecialchars($data['address'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-hashtag"></i> <span id="house-label"><?= $__('house_number') ?? 'Huisnummer' ?></span></label>
                    <input type="text" name="house_number" class="form-control" placeholder="123" value="<?= htmlspecialchars($data['house_number'] ?? '') ?>">
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-mail-bulk"></i> <span id="postal-label"><?= $__('postal_code') ?> *</span></label>
                        <input type="text" name="postal_code" class="form-control" placeholder="1234 AB" value="<?= htmlspecialchars($data['postal_code'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-city"></i> <span id="city-label"><?= $__('city') ?> *</span></label>
                        <input type="text" name="city" class="form-control" placeholder="Amsterdam" value="<?= htmlspecialchars($data['city'] ?? '') ?>" required>
                    </div>
                </div>

                <!-- Bedrijfsregistratie (dynamisch per land) -->
                <div class="section-header" style="margin-top:2rem">
                    <i class="fas fa-file-contract"></i>
                    <h4 id="registration-section-title"><?= $__('business_registration') ?? 'Bedrijfsregistratie' ?></h4>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label id="business-reg-label"><i class="fas fa-building"></i> <span id="business-reg-label-text"><?= $__('kvk_number') ?></span> <span style="font-weight:400;color:#9ca3af">(<?= $__('optional') ?>)</span></label>
                        <input type="text" name="business_registration_number" id="business-reg-input" class="form-control" placeholder="12345678" value="<?= htmlspecialchars($data['business_registration_number'] ?? $data['kvk_number'] ?? '') ?>">
                        <input type="hidden" name="business_registration_type" id="business-reg-type" value="KVK">
                        <small id="business-reg-help" style="color:#9ca3af;display:block;margin-top:4px;"><?= $__('kvk_verification_note') ?></small>
                    </div>
                    <div class="form-group">
                        <label id="tax-label"><i class="fas fa-percent"></i> <span id="tax-label-text"><?= $__('btw_number') ?? 'BTW-nummer' ?></span> <span style="font-weight:400;color:#9ca3af">(<?= $__('optional') ?>)</span></label>
                        <input type="text" name="tax_number" id="tax-input" class="form-control" placeholder="NL123456789B01" value="<?= htmlspecialchars($data['tax_number'] ?? $data['btw_number'] ?? '') ?>">
                        <small id="tax-help" style="color:#9ca3af;display:block;margin-top:4px;">Format: NL + 9 cijfers + B + 2 cijfers</small>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> <?= $__('description') ?> <span style="font-weight:400;color:#9ca3af">(<?= $__('optional') ?>)</span></label>
                    <textarea name="description" class="form-control" rows="3" placeholder="<?= $__('tell_about_salon') ?>"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                </div>

                <!-- Pricing -->
                <div class="pricing-card">
                    <div class="price-display">
                        <?php if ($showDualCurrency ?? false): ?>
                            <span class="price-amount"><?= $localPrice ?></span>
                            <span class="price-period">(<?= $eurPrice ?>) <?= $__('one_time_after_trial') ?></span>
                        <?php else: ?>
                            <span class="price-amount"><?= $eurPrice ?></span>
                            <span class="price-period"><?= $__('one_time_after_trial') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="price-note" id="employee-price-note" style="display:none; color: #fbbf24;">
                        <i class="fas fa-users"></i>
                        <span data-employees-text="<?= $__('employees_cost') ?>">0 <?= $__('employees') ?>: +€0,00</span>
                    </div>
                    <div class="fee-explanation" style="margin-top:0.75rem;padding:0.75rem;background:rgba(255,255,255,0.06);border-radius:10px;font-size:0.85rem">
                        <div style="display:flex;align-items:flex-start;gap:0.6rem;margin-bottom:0.5rem">
                            <span style="background:#ffffff;color:#000000;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;flex-shrink:0">1</span>
                            <div>
                                <strong><?= $__('reg_fee_step1_title') ?></strong><br>
                                <span style="opacity:0.8"><?= $__('reg_fee_step1_desc', ['fee' => $feeDisplay ?? '€1,75']) ?></span>
                            </div>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:0.6rem;margin-bottom:0.5rem">
                            <span style="background:#22c55e;color:#ffffff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-check" style="font-size:0.65rem"></i></span>
                            <div>
                                <strong style="color:#4ade80"><?= $__('reg_fee_step2_title') ?></strong><br>
                                <span style="opacity:0.8"><?= $__('reg_fee_step2_desc') ?></span>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.5rem;opacity:0.7;font-size:0.8rem;padding-top:0.25rem;border-top:1px solid rgba(255,255,255,0.1)">
                            <i class="fas fa-sync-alt" style="font-size:0.7rem"></i>
                            <span><?= $__('reg_fee_reset_monthly') ?></span>
                        </div>
                    </div>
                    <?php if ($isEarlyAdopter): ?>
                    <div class="price-note" style="font-size: 0.8rem; opacity: 0.7;">
                        <i class="fas fa-tag"></i>
                        <span><?= $__('normal_price_after_earlybird') ?>: <?= $showDualCurrency ? $localOriginal . ' (' . $eurOriginal . ')' : $eurOriginal ?></span>
                    </div>
                    <?php endif; ?>

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
                        <h4 style="color: #22c55e;"><i class="fas fa-clock"></i> <?= $__('trial_period_title') ?></h4>
                        <p><?= $__('trial_period_description') ?></p>
                    </div>
                </div>

                <!-- QR Code Warning -->
                <div class="qr-warning">
                    <div class="qr-warning-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="qr-warning-content">
                        <h4><i class="fas fa-exclamation-triangle"></i> <?= $__('qr_scan_required_title') ?></h4>
                        <p><?= $__('qr_scan_required_description') ?></p>
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

// Country-specific business registration configurations
// Labels are in the native language of each country
const countryConfig = {
    'NL': {
        registration: { type: 'KVK', label: 'KVK-nummer', placeholder: '12345678', help: 'Je 8-cijferig KVK-nummer (Kamer van Koophandel)', maxlength: 8, pattern: '\\d{8}' },
        tax: { type: 'BTW', label: 'BTW-nummer', placeholder: 'NL123456789B01', help: 'Format: NL + 9 cijfers + B + 2 cijfers', maxlength: 14 },
        address: { street: 'Straat', house: 'Huisnummer', postal: 'Postcode', city: 'Plaats', postalPlaceholder: '1234 AB', cityPlaceholder: 'Amsterdam', streetPlaceholder: 'Keizersgracht' },
        sole: 'Eenmanszaak', company: 'BV / VOF', soleDesc: 'Ik werk alleen', companyDesc: 'Ik heb personeel'
    },
    'BE': {
        registration: { type: 'KBO', label: 'Ondernemingsnummer (KBO)', placeholder: '0123.456.789', help: 'Je 10-cijferig KBO-ondernemingsnummer', maxlength: 12, pattern: '\\d{4}\\.?\\d{3}\\.?\\d{3}' },
        tax: { type: 'BTW', label: 'BTW-nummer', placeholder: 'BE0123456789', help: 'Format: BE + 10 cijfers', maxlength: 12 },
        address: { street: 'Straat', house: 'Huisnummer', postal: 'Postcode', city: 'Gemeente', postalPlaceholder: '1000', cityPlaceholder: 'Brussel', streetPlaceholder: 'Nieuwstraat' },
        sole: 'Eenmanszaak', company: 'BV / NV', soleDesc: 'Ik werk alleen', companyDesc: 'Ik heb personeel'
    },
    'DE': {
        registration: { type: 'HRB', label: 'Handelsregisternummer', placeholder: 'HRB 12345', help: 'Ihr Handelsregisternummer (HRA/HRB)', maxlength: 20 },
        tax: { type: 'USt-IdNr', label: 'Umsatzsteuer-Identifikationsnummer', placeholder: 'DE123456789', help: 'Format: DE + 9 Ziffern', maxlength: 11 },
        address: { street: 'Straße', house: 'Hausnummer', postal: 'Postleitzahl', city: 'Stadt', postalPlaceholder: '10115', cityPlaceholder: 'Berlin', streetPlaceholder: 'Friedrichstraße' },
        sole: 'Einzelunternehmen', company: 'GmbH / UG', soleDesc: 'Ich arbeite allein', companyDesc: 'Ich habe Mitarbeiter'
    },
    'FR': {
        registration: { type: 'SIRET', label: 'Numéro SIRET', placeholder: '123 456 789 01234', help: 'Votre numéro SIRET à 14 chiffres', maxlength: 17, pattern: '\\d{3}\\s?\\d{3}\\s?\\d{3}\\s?\\d{5}' },
        tax: { type: 'TVA', label: 'Numéro de TVA intracommunautaire', placeholder: 'FR12345678901', help: 'Format: FR + 2 caractères + SIREN (9 chiffres)', maxlength: 13 },
        address: { street: 'Adresse', house: 'N°', postal: 'Code postal', city: 'Ville', postalPlaceholder: '75001', cityPlaceholder: 'Paris', streetPlaceholder: 'Rue de Rivoli' },
        sole: 'Auto-entrepreneur', company: 'SARL / SAS', soleDesc: 'Je travaille seul(e)', companyDesc: "J'ai des employés"
    },
    'GB': {
        registration: { type: 'CRN', label: 'Company Registration Number', placeholder: '12345678', help: 'Your Companies House registration number (8 digits)', maxlength: 10 },
        tax: { type: 'VAT', label: 'VAT Registration Number', placeholder: 'GB123456789', help: 'Format: GB + 9 or 12 digits', maxlength: 14 },
        address: { street: 'Street address', house: 'Unit/Suite', postal: 'Postcode', city: 'City', postalPlaceholder: 'SW1A 1AA', cityPlaceholder: 'London', streetPlaceholder: '10 Downing Street' },
        sole: 'Sole Trader', company: 'Limited Company', soleDesc: 'I work alone', companyDesc: 'I have employees'
    },
    'ES': {
        registration: { type: 'NIF', label: 'NIF / CIF', placeholder: 'B12345678', help: 'Tu Número de Identificación Fiscal', maxlength: 9 },
        tax: { type: 'IVA', label: 'Número de IVA', placeholder: 'ESB12345678', help: 'Format: ES + NIF', maxlength: 11 },
        address: { street: 'Dirección', house: 'Número', postal: 'Código postal', city: 'Ciudad', postalPlaceholder: '28001', cityPlaceholder: 'Madrid', streetPlaceholder: 'Calle Gran Vía' },
        sole: 'Autónomo', company: 'Sociedad Limitada (SL)', soleDesc: 'Trabajo solo/a', companyDesc: 'Tengo empleados'
    },
    'IT': {
        registration: { type: 'REA', label: 'Numero REA', placeholder: 'MI-1234567', help: 'Il tuo numero REA (Repertorio Economico Amministrativo)', maxlength: 15 },
        tax: { type: 'P.IVA', label: 'Partita IVA', placeholder: 'IT12345678901', help: 'Format: IT + 11 cifre', maxlength: 13 },
        address: { street: 'Indirizzo', house: 'Civico', postal: 'CAP', city: 'Città', postalPlaceholder: '00100', cityPlaceholder: 'Roma', streetPlaceholder: 'Via del Corso' },
        sole: 'Ditta individuale', company: 'SRL / SPA', soleDesc: 'Lavoro da solo/a', companyDesc: 'Ho dei dipendenti'
    },
    'PT': {
        registration: { type: 'NIPC', label: 'NIPC', placeholder: '123456789', help: 'O seu NIPC de 9 dígitos', maxlength: 9 },
        tax: { type: 'NIF', label: 'Número de contribuinte (NIF)', placeholder: 'PT123456789', help: 'Format: PT + 9 dígitos', maxlength: 11 },
        address: { street: 'Morada', house: 'Número', postal: 'Código postal', city: 'Cidade', postalPlaceholder: '1000-001', cityPlaceholder: 'Lisboa', streetPlaceholder: 'Rua Augusta' },
        sole: 'Empresário individual', company: 'Sociedade (Lda)', soleDesc: 'Trabalho sozinho/a', companyDesc: 'Tenho funcionários'
    },
    'AT': {
        registration: { type: 'FN', label: 'Firmenbuchnummer', placeholder: '123456a', help: 'Ihre Firmenbuchnummer', maxlength: 10 },
        tax: { type: 'UID', label: 'UID-Nummer', placeholder: 'ATU12345678', help: 'Format: ATU + 8 Ziffern', maxlength: 11 },
        address: { street: 'Straße', house: 'Hausnummer', postal: 'PLZ', city: 'Ort', postalPlaceholder: '1010', cityPlaceholder: 'Wien', streetPlaceholder: 'Kärntner Straße' },
        sole: 'Einzelunternehmen', company: 'GmbH / KG', soleDesc: 'Ich arbeite allein', companyDesc: 'Ich habe Mitarbeiter'
    },
    'CH': {
        registration: { type: 'UID', label: 'UID-Nummer (Handelsregister)', placeholder: 'CHE-123.456.789', help: 'Ihre Unternehmens-Identifikationsnummer', maxlength: 15 },
        tax: { type: 'MWST', label: 'MWST-Nummer', placeholder: 'CHE-123.456.789 MWST', help: 'Format: UID + MWST', maxlength: 24 },
        address: { street: 'Strasse', house: 'Nr.', postal: 'PLZ', city: 'Ort', postalPlaceholder: '8001', cityPlaceholder: 'Zürich', streetPlaceholder: 'Bahnhofstrasse' },
        sole: 'Einzelunternehmen', company: 'GmbH / AG', soleDesc: 'Ich arbeite allein', companyDesc: 'Ich habe Mitarbeiter'
    },
    'LU': {
        registration: { type: 'RCS', label: 'Numéro RCS', placeholder: 'B123456', help: 'Votre numéro au Registre de Commerce et des Sociétés', maxlength: 10 },
        tax: { type: 'TVA', label: 'Numéro TVA', placeholder: 'LU12345678', help: 'Format: LU + 8 chiffres', maxlength: 10 },
        address: { street: 'Adresse', house: 'N°', postal: 'Code postal', city: 'Ville', postalPlaceholder: '1111', cityPlaceholder: 'Luxembourg', streetPlaceholder: 'Grand-Rue' },
        sole: 'Indépendant', company: 'SARL / SA', soleDesc: 'Je travaille seul(e)', companyDesc: "J'ai des employés"
    },
    'PL': {
        registration: { type: 'KRS/REGON', label: 'Numer KRS lub REGON', placeholder: '0000123456', help: 'Twój numer KRS (10 cyfr) lub REGON (9/14 cyfr)', maxlength: 14 },
        tax: { type: 'NIP', label: 'Numer NIP', placeholder: 'PL1234567890', help: 'Format: PL + 10 cyfr', maxlength: 12 },
        address: { street: 'Ulica', house: 'Nr', postal: 'Kod pocztowy', city: 'Miasto', postalPlaceholder: '00-001', cityPlaceholder: 'Warszawa', streetPlaceholder: 'ul. Marszałkowska' },
        sole: 'Jednoosobowa działalność', company: 'Spółka z o.o.', soleDesc: 'Pracuję sam/sama', companyDesc: 'Mam pracowników'
    },
    'US': {
        registration: { type: 'EIN', label: 'EIN (Employer Identification Number)', placeholder: '12-3456789', help: 'Your IRS Employer Identification Number (XX-XXXXXXX)', maxlength: 10 },
        tax: { type: 'Sales Tax', label: 'State Sales Tax Permit', placeholder: 'State-specific', help: 'Your state sales tax permit number (if applicable)', maxlength: 20 },
        address: { street: 'Street address', house: 'Suite/Apt', postal: 'ZIP Code', city: 'City', postalPlaceholder: '10001', cityPlaceholder: 'New York', streetPlaceholder: '123 Main Street' },
        sole: 'Sole Proprietor', company: 'LLC / Corporation', soleDesc: 'I work alone', companyDesc: 'I have employees'
    },
    'CA': {
        registration: { type: 'BN', label: 'Business Number (BN)', placeholder: '123456789', help: 'Your 9-digit CRA Business Number', maxlength: 9 },
        tax: { type: 'GST/HST', label: 'GST/HST Registration Number', placeholder: '123456789RT0001', help: 'Format: BN + RT + 4 digits', maxlength: 15 },
        address: { street: 'Street address', house: 'Unit', postal: 'Postal Code', city: 'City', postalPlaceholder: 'M5V 2T6', cityPlaceholder: 'Toronto', streetPlaceholder: '123 Queen Street' },
        sole: 'Sole Proprietor', company: 'Corporation / Inc.', soleDesc: 'I work alone', companyDesc: 'I have employees'
    },
    'AU': {
        registration: { type: 'ABN', label: 'ABN (Australian Business Number)', placeholder: '12 345 678 901', help: 'Your 11-digit Australian Business Number', maxlength: 14 },
        tax: { type: 'GST', label: 'GST Registration', placeholder: '12345678901', help: 'Same as ABN if GST registered', maxlength: 11 },
        address: { street: 'Street address', house: 'Unit', postal: 'Postcode', city: 'Suburb/City', postalPlaceholder: '2000', cityPlaceholder: 'Sydney', streetPlaceholder: '123 George Street' },
        sole: 'Sole Trader', company: 'Pty Ltd / Company', soleDesc: 'I work alone', companyDesc: 'I have employees'
    },
    'TR': {
        registration: { type: 'VKN', label: 'Vergi Kimlik Numarası (VKN)', placeholder: '1234567890', help: '10 haneli Vergi Kimlik Numaranız', maxlength: 10 },
        tax: { type: 'KDV', label: 'KDV Numarası', placeholder: 'TR1234567890', help: 'Format: TR + VKN', maxlength: 12 },
        address: { street: 'Adres', house: 'No', postal: 'Posta kodu', city: 'İl / İlçe', postalPlaceholder: '34000', cityPlaceholder: 'İstanbul', streetPlaceholder: 'İstiklal Caddesi' },
        sole: 'Şahıs şirketi', company: 'Limited / Anonim Şirket', soleDesc: 'Tek başıma çalışıyorum', companyDesc: 'Çalışanlarım var'
    },
    'MA': {
        registration: { type: 'RC', label: 'السجل التجاري / Registre de Commerce (RC)', placeholder: '123456', help: 'Numéro du Registre de Commerce / رقم السجل التجاري', maxlength: 15 },
        tax: { type: 'IF', label: 'المعرف الضريبي / Identifiant Fiscal (IF)', placeholder: '12345678', help: 'Identifiant Fiscal / رقم التعريف الضريبي', maxlength: 15 },
        address: { street: 'العنوان / Adresse', house: 'الرقم / N°', postal: 'الرمز البريدي / Code postal', city: 'المدينة / Ville', postalPlaceholder: '20000', cityPlaceholder: 'Casablanca', streetPlaceholder: 'Boulevard Mohammed V' },
        sole: 'Auto-entrepreneur / مقاول ذاتي', company: 'SARL / شركة', soleDesc: 'أعمل وحدي / Je travaille seul(e)', companyDesc: 'لدي موظفون / J\'ai des employés'
    },
    'AE': {
        registration: { type: 'License', label: 'الرخصة التجارية / Trade License Number', placeholder: '123456', help: 'Your Trade License Number / رقم الرخصة التجارية', maxlength: 20 },
        tax: { type: 'TRN', label: 'رقم التسجيل الضريبي / Tax Registration Number (TRN)', placeholder: '100000000000003', help: '15-digit Tax Registration Number', maxlength: 15 },
        address: { street: 'العنوان / Address', house: 'الوحدة / Unit', postal: 'الرمز البريدي / P.O. Box', city: 'الإمارة / Emirate', postalPlaceholder: '00000', cityPlaceholder: 'Dubai', streetPlaceholder: 'Sheikh Zayed Road' },
        sole: 'مؤسسة فردية / Sole Establishment', company: 'شركة ذ.م.م / LLC', soleDesc: 'أعمل وحدي / I work alone', companyDesc: 'لدي موظفون / I have employees'
    },
    'OTHER': {
        registration: { type: 'Business ID', label: 'Business Registration Number', placeholder: '', help: 'Your official business registration number', maxlength: 30 },
        tax: { type: 'Tax ID', label: 'Tax / VAT Number', placeholder: '', help: 'Your tax or VAT registration number', maxlength: 20 },
        address: { street: 'Street address', house: 'Number', postal: 'Postal code', city: 'City', postalPlaceholder: '', cityPlaceholder: '', streetPlaceholder: '' },
        sole: 'Sole proprietor', company: 'Company', soleDesc: 'I work alone', companyDesc: 'I have employees'
    }
};

function updateBusinessFields() {
    const country = document.getElementById('country-select').value;
    const config = countryConfig[country] || countryConfig['OTHER'];

    // Update registration field
    document.getElementById('business-reg-label-text').textContent = config.registration.label;
    document.getElementById('business-reg-input').placeholder = config.registration.placeholder;
    document.getElementById('business-reg-input').maxLength = Math.max(config.registration.maxlength || 30, 30);
    document.getElementById('business-reg-help').textContent = config.registration.help;
    document.getElementById('business-reg-type').value = config.registration.type;

    // Update tax field
    document.getElementById('tax-label-text').textContent = config.tax.label;
    document.getElementById('tax-input').placeholder = config.tax.placeholder;
    document.getElementById('tax-input').maxLength = Math.max(config.tax.maxlength || 30, 30);
    document.getElementById('tax-help').textContent = config.tax.help;

    // Update address labels per country
    if (config.address) {
        const streetLabel = document.getElementById('street-label');
        const houseLabel = document.getElementById('house-label');
        const postalLabel = document.getElementById('postal-label');
        const cityLabel = document.getElementById('city-label');
        const streetInput = document.querySelector('input[name="address"]');
        const postalInput = document.querySelector('input[name="postal_code"]');
        const cityInput = document.querySelector('input[name="city"]');

        if (streetLabel) streetLabel.textContent = config.address.street + ' *';
        if (houseLabel) houseLabel.textContent = config.address.house;
        if (postalLabel) postalLabel.textContent = config.address.postal + ' *';
        if (cityLabel) cityLabel.textContent = config.address.city + ' *';
        if (streetInput && config.address.streetPlaceholder) streetInput.placeholder = config.address.streetPlaceholder;
        if (postalInput && config.address.postalPlaceholder) postalInput.placeholder = config.address.postalPlaceholder;
        if (cityInput && config.address.cityPlaceholder) cityInput.placeholder = config.address.cityPlaceholder;
    }

    // Update business type labels per country
    if (config.sole) {
        const soleTitle = document.querySelector('#type-eenmanszaak .type-title');
        const soleDesc = document.querySelector('#type-eenmanszaak .type-desc');
        const companyTitle = document.querySelector('#type-bv .type-title');
        const companyDesc = document.querySelector('#type-bv .type-desc');
        if (soleTitle) soleTitle.textContent = config.sole;
        if (soleDesc) soleDesc.textContent = config.soleDesc;
        if (companyTitle) companyTitle.textContent = config.company;
        if (companyDesc) companyDesc.textContent = config.companyDesc;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateBusinessType();
    updateBusinessFields();
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

<?php ob_start(); ?>

<style>
    /* =============================================
       AUTH PAGES - Theme Support
       ============================================= */

    /* Dark Theme (Default) */
    .auth-wrapper,
    [data-theme="dark"] .auth-wrapper {
        --auth-bg: #000000;
        --auth-surface: #111111;
        --auth-border: #333333;
        --auth-text: #ffffff;
        --auth-text-muted: rgba(255, 255, 255, 0.7);
        --auth-text-placeholder: rgba(255, 255, 255, 0.5);
        --auth-input-border: rgba(255, 255, 255, 0.4);
        --auth-input-focus: #ffffff;
        --auth-btn-bg: #ffffff;
        --auth-btn-text: #000000;
        --auth-link: #ffffff;
        --auth-shadow: rgba(0, 0, 0, 0.3);
        --auth-tab-hover: rgba(255, 255, 255, 0.1);
        --auth-alert-bg: rgba(255, 255, 255, 0.1);
        --auth-alert-border: rgba(255, 255, 255, 0.3);
    }

    /* Light Theme */
    [data-theme="light"] .auth-wrapper {
        --auth-bg: #ffffff;
        --auth-surface: #f5f5f5;
        --auth-border: #e0e0e0;
        --auth-text: #000000;
        --auth-text-muted: rgba(0, 0, 0, 0.6);
        --auth-text-placeholder: rgba(0, 0, 0, 0.5);
        --auth-input-border: rgba(0, 0, 0, 0.3);
        --auth-input-focus: #000000;
        --auth-btn-bg: #000000;
        --auth-btn-text: #ffffff;
        --auth-link: #000000;
        --auth-shadow: rgba(0, 0, 0, 0.1);
        --auth-tab-hover: rgba(0, 0, 0, 0.05);
        --auth-alert-bg: rgba(0, 0, 0, 0.05);
        --auth-alert-border: rgba(0, 0, 0, 0.2);
    }

    .register-container {
        max-width: 720px;
        margin: 1rem auto;
        padding: 0 1rem;
    }
    @media (max-width: 768px) {
        .register-container {
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
        .register-card {
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
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
        .password-wrapper {
            width: 100%;
        }
        .benefits-list {
            grid-template-columns: 1fr !important;
        }
    }
    .register-card {
        background: var(--auth-bg);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px var(--auth-shadow);
        border: 2px solid var(--auth-border);
        transition: background 0.3s, border-color 0.3s;
    }
    .register-header {
        background: var(--auth-bg);
        color: var(--auth-text);
        padding: 3rem 2rem;
        text-align: center;
        border-bottom: 2px solid var(--auth-border);
        border-radius: 0 0 30px 30px;
    }
    .register-header i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: var(--auth-text);
    }
    .register-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--auth-text);
    }
    .register-header p {
        margin: 0.5rem 0 0 0;
        font-size: 0.95rem;
        color: var(--auth-text-muted);
    }

    /* Account Type Tabs */
    .account-tabs {
        display: flex;
        background: var(--auth-bg);
        border-bottom: 2px solid var(--auth-input-border);
    }
    .account-tab {
        flex: 1;
        padding: 1.25rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        background: transparent;
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--auth-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    .account-tab:hover {
        background: var(--auth-tab-hover);
        color: var(--auth-text);
    }
    .account-tab.active {
        background: var(--auth-tab-hover);
        color: var(--auth-text);
        border-bottom: 3px solid var(--auth-text);
        margin-bottom: -2px;
    }
    .account-tab i {
        font-size: 1.25rem;
    }
    .account-tab-label {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    .account-tab-label small {
        font-size: 0.7rem;
        font-weight: 400;
        opacity: 0.8;
    }

    .register-body {
        padding: 2rem;
        background: var(--auth-bg);
    }

    /* Grid Layout */
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 0;
        border-bottom: 2px solid var(--auth-input-border);
        margin-bottom: 1.5rem;
    }
    .section-header i {
        width: 40px;
        height: 40px;
        background: var(--auth-btn-bg);
        color: var(--auth-btn-text);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .section-header h4 {
        margin: 0;
        color: var(--auth-text);
        font-size: 1.1rem;
    }

    /* Form Group */
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--auth-text);
    }
    .form-group label i {
        color: var(--auth-text);
        font-size: 0.9rem;
    }
    .form-control {
        width: 100%;
        padding: 0.9rem 0;
        background: transparent;
        border: none;
        border-bottom: 2px solid var(--auth-input-border);
        border-radius: 0;
        font-size: 1rem;
        color: var(--auth-text);
        transition: all 0.3s ease;
    }
    .form-control:focus {
        outline: none;
        border-bottom-color: var(--auth-input-focus);
        box-shadow: none;
    }
    .form-control:hover {
        border-bottom-color: var(--auth-text-muted);
    }
    .form-control::placeholder {
        color: var(--auth-text-placeholder);
    }
    select.form-control {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0 center;
        background-size: 1rem;
        padding-right: 1.5rem;
        cursor: pointer;
    }
    select.form-control option {
        background: var(--auth-bg);
        color: var(--auth-text);
    }
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .btn-register {
        width: 100%;
        padding: 1.1rem;
        background: var(--auth-btn-bg);
        color: var(--auth-btn-text);
        border: none;
        border-radius: 50px;
        font-size: 1.05rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }
    .btn-register:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px var(--auth-shadow);
    }

    .register-footer {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid var(--auth-input-border);
        margin-top: 1.5rem;
    }
    .register-footer p {
        margin: 0.5rem 0;
        color: var(--auth-text-muted);
    }
    .register-footer a {
        color: var(--auth-link);
        text-decoration: none;
        font-weight: 500;
    }
    .register-footer a:hover {
        text-decoration: underline;
    }

    .alert-danger {
        background: var(--auth-alert-bg);
        border: 1px solid var(--auth-alert-border);
        color: var(--auth-text);
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.25rem;
    }

    .account-info {
        background: var(--auth-alert-bg);
        border: 2px solid var(--auth-alert-border);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: var(--auth-text);
    }
    .account-info i {
        font-size: 1.25rem;
        color: var(--auth-text);
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
        color: var(--auth-text-muted);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .password-toggle:hover {
        color: var(--auth-text);
    }
    .password-wrapper .form-control {
        padding-right: 2rem;
    }

    /* Terms checkbox */
    .terms-box {
        background: var(--auth-alert-bg);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-top: 1.5rem;
    }
    .terms-box label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
        margin: 0;
    }
    .terms-box span {
        color: var(--auth-text);
        font-size: 0.9rem;
    }
    .terms-box a {
        color: var(--auth-link);
        font-weight: 500;
    }
    .terms-box input[type="checkbox"] {
        width: 22px;
        height: 22px;
        accent-color: var(--auth-text);
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* Pricing Card */
    .pricing-card {
        background: var(--auth-alert-bg);
        border: 2px solid var(--auth-alert-border);
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
        background: var(--auth-btn-bg);
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
        color: var(--auth-text);
    }
    .price-period {
        color: var(--auth-text-muted);
        font-size: 0.9rem;
    }
    .price-note {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--auth-text-muted);
        font-size: 0.9rem;
    }
    .price-note i {
        color: var(--auth-text);
    }
    .benefits-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--auth-input-border);
    }
    .benefit-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--auth-text);
    }
    .benefit-item i {
        color: var(--auth-text);
    }

    /* QR Warning */
    .qr-warning {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        background: var(--auth-alert-bg);
        border: 2px solid var(--auth-btn-bg);
        border-radius: 12px;
        padding: 1.25rem;
        margin: 1.5rem 0;
    }
    .qr-warning-icon {
        width: 50px;
        height: 50px;
        background: var(--auth-btn-bg);
        color: var(--auth-btn-text);
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
        color: var(--auth-text);
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
        color: var(--auth-text);
        line-height: 1.5;
    }
    .qr-warning-content strong {
        color: var(--auth-text);
    }

    /* Tab content visibility */
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>

<div class="auth-wrapper">
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h1><?= $translations['create_account'] ?? 'Create Account' ?></h1>
            <p><?= $translations['register_free_subtitle'] ?? 'Register for free as customer or business' ?></p>
        </div>

        <!-- Account Type Tabs -->
        <div class="account-tabs">
            <button type="button" class="account-tab active" onclick="switchTab('personal')" id="tabPersonal">
                <i class="fas fa-user"></i>
                <span class="account-tab-label">
                    <?= $translations['personal'] ?? 'Personal' ?>
                    <small><?= $translations['personal_account'] ?? 'Customer account' ?></small>
                </span>
            </button>
            <button type="button" class="account-tab" onclick="switchTab('business')" id="tabBusiness">
                <i class="fas fa-store"></i>
                <span class="account-tab-label">
                    <?= $translations['business_tab'] ?? 'Business' ?>
                    <small><?= $translations['salon_business'] ?? 'Salon / Business' ?></small>
                </span>
            </button>
        </div>

        <div class="register-body">
            <?php if (isset($error)): ?>
                <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Personal Registration -->
            <div class="tab-content active" id="contentPersonal">
                <div class="account-info">
                    <i class="fas fa-info-circle"></i>
                    <span><?= $translations['create_personal_account'] ?? 'Create a personal account to book appointments and save your favorites.' ?></span>
                </div>

                <form method="POST" action="/register" id="formPersonal">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                    <input type="hidden" name="account_type" value="personal">

                    <div class="grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> <?= $translations['first_name'] ?? 'First name' ?> *</label>
                            <input type="text" name="first_name" class="form-control" placeholder="<?= $translations['first_name_placeholder'] ?? 'Your first name' ?>" value="<?= htmlspecialchars($data['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> <?= $translations['last_name'] ?? 'Last name' ?> *</label>
                            <input type="text" name="last_name" class="form-control" placeholder="<?= $translations['last_name_placeholder'] ?? 'Your last name' ?>" value="<?= htmlspecialchars($data['last_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> <?= $translations['email'] ?? 'Email address' ?> *</label>
                        <input type="email" name="email" class="form-control" placeholder="<?= $translations['email_placeholder'] ?? 'your@email.com' ?>" value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> <?= $translations['phone'] ?? 'Phone' ?> <span style="font-weight:400;color:rgba(255,255,255,0.5)">(<?= $translations['optional'] ?? 'optional' ?>)</span></label>
                        <input type="tel" name="phone" class="form-control" placeholder="<?= $translations['phone_placeholder'] ?? '06-12345678' ?>" value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> <?= $translations['password'] ?? 'Password' ?> *</label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="passwordPersonal" class="form-control" placeholder="<?= $translations['min_8_chars'] ?? 'Minimum 8 characters' ?>" minlength="8" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('passwordPersonal', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> <?= $translations['confirm_password'] ?? 'Confirm password' ?> *</label>
                            <div class="password-wrapper">
                                <input type="password" name="password_confirm" id="passwordConfirmPersonal" class="form-control" placeholder="<?= $translations['repeat_password'] ?? 'Repeat password' ?>" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('passwordConfirmPersonal', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="terms-box">
                        <label>
                            <input type="checkbox" name="accept_terms" required>
                            <span><?= $translations['agree_terms'] ?? 'I agree to the' ?> <a href="/terms" target="_blank"><?= $translations['terms_conditions'] ?? 'Terms and Conditions' ?></a> <?= $translations['and_the'] ?? 'and the' ?> <a href="/privacy" target="_blank"><?= $translations['privacy_policy'] ?? 'Privacy Policy' ?></a> *</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus"></i> <?= $translations['create_account'] ?? 'Create Account' ?>
                    </button>
                </form>

                <div class="register-footer">
                    <p><?= $translations['already_account'] ?? 'Already have an account?' ?> <a href="/login"><?= $translations['login'] ?? 'Login' ?></a></p>
                </div>
            </div>

            <!-- Business Registration -->
            <div class="tab-content" id="contentBusiness">
                <div class="account-info">
                    <i class="fas fa-store"></i>
                    <span><?= $translations['register_business_info'] ?? 'Register your salon or business and receive bookings from customers.' ?></span>
                </div>

                <form method="POST" action="/business/register" id="formBusiness">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">

                    <!-- Bedrijfsgegevens -->
                    <div class="section-header">
                        <i class="fas fa-building"></i>
                        <h4><?= $translations['business_data'] ?? 'Business details' ?></h4>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-store"></i> <?= $translations['business_name'] ?? 'Business name' ?> *</label>
                        <input type="text" name="name" class="form-control" placeholder="<?= $translations['business_name_placeholder'] ?? 'Name of your salon' ?>" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> <?= $translations['email'] ?? 'Email address' ?> *</label>
                            <input type="email" name="email" class="form-control" placeholder="<?= $translations['business_email_placeholder'] ?? 'info@yourbusiness.com' ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> <?= $translations['phone'] ?? 'Phone' ?></label>
                            <input type="tel" name="phone" class="form-control" placeholder="<?= $translations['phone_placeholder'] ?? '06-12345678' ?>">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> <?= $translations['password'] ?? 'Password' ?> * <span style="font-weight:400;color:rgba(255,255,255,0.5)">(<?= $translations['min_8_chars_short'] ?? 'min. 8 chars' ?>)</span></label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="passwordBusiness" class="form-control" placeholder="<?= $translations['choose_strong_password'] ?? 'Choose a strong password' ?>" minlength="8" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('passwordBusiness', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> <?= $translations['confirm_password'] ?? 'Confirm password' ?> *</label>
                            <div class="password-wrapper">
                                <input type="password" name="password_confirm" id="passwordConfirmBusiness" class="form-control" placeholder="<?= $translations['repeat_password'] ?? 'Repeat password' ?>" minlength="8" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('passwordConfirmBusiness', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-tags"></i> <?= $translations['category'] ?? 'Category' ?></label>
                        <select name="category_id" class="form-control">
                            <option value=""><?= $translations['select_category'] ?? 'Select a category' ?></option>
                            <?php if (isset($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['translated_name'] ?? $cat['name'] ?? '') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Locatie -->
                    <div class="section-header" style="margin-top:2rem">
                        <i class="fas fa-map-marker-alt"></i>
                        <h4><?= $translations['location'] ?? 'Location' ?></h4>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-road"></i> <?= $translations['address'] ?? 'Address' ?> *</label>
                        <input type="text" name="address" class="form-control" placeholder="<?= $translations['address_placeholder'] ?? 'Street name 123' ?>" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-mail-bulk"></i> <?= $translations['postal_code'] ?? 'Postal code' ?> *</label>
                            <input type="text" name="postal_code" class="form-control" placeholder="<?= $translations['postal_code_placeholder'] ?? '1234 AB' ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-city"></i> <?= $translations['city'] ?? 'City' ?> *</label>
                            <input type="text" name="city" class="form-control" placeholder="<?= $translations['city_placeholder'] ?? 'Amsterdam' ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-file-contract"></i> <?= $translations['kvk_number'] ?? 'Chamber of Commerce number' ?> <span style="font-weight:400;color:rgba(255,255,255,0.5)">(<?= $translations['optional'] ?? 'optional' ?>)</span></label>
                        <input type="text" name="kvk_number" class="form-control" placeholder="12345678">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> <?= $translations['description'] ?? 'Description' ?> <span style="font-weight:400;color:rgba(255,255,255,0.5)">(<?= $translations['optional'] ?? 'optional' ?>)</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="<?= $translations['description_placeholder'] ?? 'Tell something about your salon...' ?>"></textarea>
                    </div>

                    <!-- Pricing -->
                    <div class="pricing-card">
                        <div class="early-bird-badge" style="background:#fbbf24;color:#000;padding:0.5rem 1rem;border-radius:50px;display:inline-flex;align-items:center;gap:0.5rem;font-size:0.85rem;font-weight:600;margin-bottom:1rem">
                            <i class="fas fa-star"></i> <?= $translations['early_bird'] ?? 'Early Bird - First 100 businesses' ?>
                        </div>
                        <div class="price-display">
                            <span class="price-amount"><?= $promo['local_price'] ?? '€0,99' ?></span>
                            <span class="price-period"><?= $translations['one_time'] ?? 'one-time' ?></span>
                            <?php if ($showDualCurrency ?? false): ?>
                            <span class="price-eur" style="font-size:0.8rem;color:rgba(255,255,255,0.6)">(<?= $promo['eur_price'] ?? '€0,99' ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="price-note" style="margin-bottom:0.5rem">
                            <i class="fas fa-clock"></i>
                            <span><?= $translations['after_free_trial'] ?? 'After 14 days free trial' ?></span>
                        </div>
                        <div class="price-note" style="margin-bottom:1rem;padding:0.75rem;background:rgba(255,255,255,0.05);border-radius:8px">
                            <i class="fas fa-info-circle"></i>
                            <span><?= str_replace('&euro;99.99', $promo['local_original'] ?? '€99,99', $translations['after_price_new_business'] ?? 'Then &euro;99.99 one-time for new businesses') ?></span>
                        </div>
                        <div class="price-note">
                            <i class="fas fa-receipt"></i>
                            <span><?= str_replace(':amount', $promo['fee_display'] ?? '€1,75', $translations['per_booking_fee'] ?? ':amount per successful booking') ?></span>
                        </div>

                        <div class="benefits-list">
                            <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $translations['benefit_free_trial'] ?? '14 days free trial' ?></div>
                            <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $translations['benefit_unlimited_bookings'] ?? 'Unlimited bookings' ?></div>
                            <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $translations['benefit_profile_page'] ?? 'Own profile page' ?></div>
                            <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $translations['benefit_email_notifications'] ?? 'Email notifications' ?></div>
                            <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $translations['benefit_dashboard'] ?? 'Dashboard & statistics' ?></div>
                            <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $translations['benefit_customer_management'] ?? 'Customer management' ?></div>
                            <div class="benefit-item"><i class="fas fa-check-circle"></i> <?= $translations['benefit_online_payments'] ?? 'Online payments' ?></div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="terms-box">
                        <label>
                            <input type="checkbox" name="terms" required>
                            <span><?= $translations['agree_terms'] ?? 'I agree to the' ?> <a href="/terms" target="_blank"><?= $translations['terms_conditions'] ?? 'Terms and Conditions' ?></a> <?= $translations['and_the'] ?? 'and the' ?> <a href="/privacy" target="_blank"><?= $translations['privacy_policy'] ?? 'Privacy Policy' ?></a> *</span>
                        </label>
                    </div>

                    <!-- QR Code Warning -->
                    <div class="qr-warning">
                        <div class="qr-warning-icon">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <div class="qr-warning-content">
                            <h4><i class="fas fa-exclamation-triangle"></i> <?= $translations['important'] ?? 'Important' ?>: <?= $translations['qr_scan_required'] ?? 'QR Code Scan Required' ?></h4>
                            <p><?= $translations['qr_scan_booking_desc'] ?? 'For each booking, the customer must scan the QR code upon arrival.' ?></p>
                        </div>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-rocket"></i> <?= $translations['register_my_salon'] ?? 'Register My Salon' ?>
                    </button>
                </form>

                <div class="register-footer">
                    <p><?= $translations['already_account'] ?? 'Already have an account?' ?> <a href="/login"><?= $translations['login'] ?? 'Login' ?></a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</div><!-- End auth-wrapper -->

<script>
function switchTab(type) {
    // Update tabs
    document.getElementById('tabPersonal').classList.remove('active');
    document.getElementById('tabBusiness').classList.remove('active');
    document.getElementById('tab' + type.charAt(0).toUpperCase() + type.slice(1)).classList.add('active');

    // Update content
    document.getElementById('contentPersonal').classList.remove('active');
    document.getElementById('contentBusiness').classList.remove('active');
    document.getElementById('content' + type.charAt(0).toUpperCase() + type.slice(1)).classList.add('active');

    // Store preference
    sessionStorage.setItem('registerTab', type);
}

// Restore tab preference or check URL parameter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('type');

    if (tabParam === 'business') {
        switchTab('business');
    } else {
        const savedTab = sessionStorage.getItem('registerTab');
        if (savedTab) {
            switchTab(savedTab);
        }
    }
});

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
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

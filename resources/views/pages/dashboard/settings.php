<?php ob_start(); ?>

<style>
.settings-container {
    max-width: 600px;
    margin: 0 auto;
}
.settings-section {
    background: #000000;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    border: 1px solid #333333;
    color: #ffffff;
}
.settings-section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}
.settings-section-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}
.settings-section-header h3 {
    margin: 0;
    font-size: 1.1rem;
}
.settings-section-header p {
    margin: 0.25rem 0 0;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.7);
}
.settings-section-header h3 {
    color: #ffffff;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
@media (max-width: 500px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
.form-group {
    margin-bottom: 1rem;
}
.form-group:last-child {
    margin-bottom: 0;
}
.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.9);
}
.form-control {
    width: 100%;
    padding: 0.9rem 0;
    border: none;
    border-bottom: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 0;
    font-size: 1rem;
    background: transparent;
    color: #ffffff;
    transition: all 0.3s ease;
}
.form-control:focus {
    border-bottom-color: #ffffff;
    outline: none;
    box-shadow: none;
}
.form-control:hover {
    border-bottom-color: rgba(255, 255, 255, 0.7);
}
.form-control::placeholder {
    color: rgba(255, 255, 255, 0.5);
}
.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0 center;
    padding-right: 1.5rem;
    cursor: pointer;
}
.form-select option {
    background: #000000;
    color: #ffffff;
}
[data-theme="dark"] .form-control {
    border-bottom-color: rgba(255, 255, 255, 0.3);
    color: var(--text);
}
[data-theme="dark"] .form-control:focus {
    border-bottom-color: var(--primary);
}
[data-theme="dark"] .form-control:hover {
    border-bottom-color: rgba(255, 255, 255, 0.5);
}
[data-theme="dark"] .form-control::placeholder {
    color: rgba(255, 255, 255, 0.4);
}
.btn-block {
    width: 100%;
    padding: 0.875rem;
    font-size: 1rem;
    background: #ffffff;
    color: #000000;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}
.btn-block:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255,255,255,0.2);
}
.settings-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    text-decoration: none;
    color: #ffffff;
    transition: all 0.2s;
    margin-bottom: 0.75rem;
}
.settings-link:last-child {
    margin-bottom: 0;
}
.settings-link:hover {
    background: rgba(255,255,255,0.15);
    transform: translateX(4px);
}
.settings-link-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.settings-link-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}
.settings-link h4 {
    margin: 0;
    font-size: 0.95rem;
    color: #ffffff;
}
.settings-link p {
    margin: 0.25rem 0 0;
    font-size: 0.8rem;
    color: rgba(255,255,255,0.7);
}
.settings-link i.fa-chevron-right {
    color: rgba(255,255,255,0.7);
}
.danger-zone {
    border: 2px solid #450a0a;
    background: #1c0a0a;
}
.danger-zone .settings-section-header {
    border-bottom-color: #450a0a;
}
.btn-danger-outline {
    background: transparent;
    color: #ffffff;
    border: 2px solid #ffffff;
}
.btn-danger-outline:hover {
    background: #ffffff;
    color: #000000;
}
.delete-confirm-box {
    display: none;
    margin-top: 1rem;
    padding: 1rem;
    background: #000000;
    border-radius: 12px;
    border: 2px solid #dc2626;
}
.delete-confirm-box.show {
    display: block;
}
.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.info-badge-success {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
}
.info-badge-warning {
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7);
}

/* iOS Toggle Switch */
.toggle-switch {
    position: relative;
    width: 51px;
    height: 31px;
    flex-shrink: 0;
}
.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #e5e5ea;
    transition: .3s;
    border-radius: 31px;
}
.toggle-slider:before {
    position: absolute;
    content: "";
    height: 27px;
    width: 27px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.toggle-switch input:checked + .toggle-slider {
    background: #ffffff;
}
.toggle-switch input:checked + .toggle-slider:before {
    background-color: #000000;
    transform: translateX(20px);
}
.toggle-switch input:disabled + .toggle-slider {
    opacity: 0.5;
    cursor: not-allowed;
}
.settings-toggle-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    margin-bottom: 0.75rem;
}
.settings-toggle-item:last-child {
    margin-bottom: 0;
}
.settings-toggle-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.settings-toggle-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}
.settings-toggle-item h4 {
    margin: 0;
    font-size: 0.95rem;
    color: #ffffff;
}
.settings-toggle-item p {
    margin: 0.25rem 0 0;
    font-size: 0.8rem;
    color: rgba(255,255,255,0.7);
}
[data-theme="dark"] .toggle-slider {
    background-color: #3a3a3c;
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
[data-theme="dark"] .password-toggle {
    color: rgba(255, 255, 255, 0.5);
}
[data-theme="dark"] .password-toggle:hover {
    color: var(--primary);
}
</style>

<div class="container settings-container">
    <h2 style="margin:0 0 1.5rem;display:flex;align-items:center;gap:0.75rem">
        <i class="fas fa-cog" style="color:var(--primary)"></i> <?= $translations['settings'] ?? 'Settings' ?>
    </h2>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>" style="margin-bottom:1.5rem">
            <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Profile Section -->
    <div class="settings-section">
        <div class="settings-section-header">
            <div class="settings-section-icon" style="background:linear-gradient(135deg,#404040,#1d4ed8)">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h3><?= $translations['settings_profile'] ?? 'Profile' ?></h3>
                <p><?= $translations['settings_personal_info'] ?? 'Your personal information' ?></p>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="action" value="update_profile">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><?= $translations['first_name'] ?? 'First name' ?> *</label>
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= $translations['last_name'] ?? 'Last name' ?></label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $translations['email'] ?? 'Email address' ?></label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled style="opacity:0.7">
                <small style="color:var(--text-light);font-size:0.8rem"><?= $translations['settings_email_cannot_change'] ?? 'Email address cannot be changed' ?></small>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $translations['settings_phone'] ?? 'Phone number' ?></label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+31 6 12345678">
            </div>

            <div class="form-group">
                <label class="form-label"><?= $translations['language'] ?? 'Language' ?></label>
                <select name="language" class="form-control form-select">
                    <option value="nl" <?= ($user['language'] ?? 'nl') === 'nl' ? 'selected' : '' ?>>Nederlands</option>
                    <option value="en" <?= ($user['language'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                    <option value="de" <?= ($user['language'] ?? '') === 'de' ? 'selected' : '' ?>>Deutsch</option>
                    <option value="fr" <?= ($user['language'] ?? '') === 'fr' ? 'selected' : '' ?>>Français</option>
                </select>
            </div>

            <button type="submit" class="btn btn-block">
                <i class="fas fa-save"></i> <?= $translations['save'] ?? 'Save' ?>
            </button>
        </form>
    </div>

    <!-- Quick Links -->
    <div class="settings-section">
        <div class="settings-section-header">
            <div class="settings-section-icon" style="background:linear-gradient(135deg,#000000,#262626)">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <h3><?= $translations['settings_security'] ?? 'Security' ?></h3>
                <p><?= $translations['settings_protect_account'] ?? 'Protect your account' ?></p>
            </div>
        </div>

        <a href="/dashboard/security" class="settings-link">
            <div class="settings-link-content">
                <div class="settings-link-icon" style="background:linear-gradient(135deg,#000000,#262626)">
                    <i class="fas fa-lock"></i>
                </div>
                <div>
                    <h4><?= $translations['settings_pin_code'] ?? 'PIN Code' ?></h4>
                    <p><?= $translations['settings_pin_desc'] ?? 'Secure app access with 6-digit code' ?></p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:0.75rem">
                <?php if (!empty($user['pin_enabled'])): ?>
                    <span class="info-badge info-badge-success"><i class="fas fa-check"></i> <?= $translations['settings_active'] ?? 'Active' ?></span>
                <?php else: ?>
                    <span class="info-badge info-badge-warning"><i class="fas fa-times"></i> <?= $translations['settings_off'] ?? 'Off' ?></span>
                <?php endif; ?>
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
    </div>

    <!-- Notifications Section -->
    <div class="settings-section">
        <div class="settings-section-header">
            <div class="settings-section-icon" style="background:linear-gradient(135deg,#000000,#000000)">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h3><?= $translations['settings_notifications'] ?? 'Notifications' ?></h3>
                <p><?= $translations['settings_manage_notifications'] ?? 'Manage your notification preferences' ?></p>
            </div>
        </div>

        <div class="settings-toggle-item">
            <div class="settings-toggle-content">
                <div class="settings-toggle-icon" style="background:linear-gradient(135deg,#000000,#000000)">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div>
                    <h4><?= $translations['settings_push_notifications'] ?? 'Push Notifications' ?></h4>
                    <p id="pushStatusText"><?= $translations['settings_push_receive_reminders'] ?? 'Receive reminders for appointments' ?></p>
                </div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="pushToggle" onchange="togglePushNotifications(this)">
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div id="pushNotSupported" style="display:none;padding:1rem;background:rgba(239,68,68,0.1);border-radius:12px;margin-top:0.75rem">
            <p style="margin:0;font-size:0.85rem;color:#333333">
                <i class="fas fa-exclamation-triangle"></i>
                <?= $translations['settings_push_not_supported'] ?? 'Push notifications are not supported on this device or in this browser.' ?>
            </p>
        </div>

        <div id="pushBlocked" style="display:none;padding:1rem;background:rgba(245,158,11,0.1);border-radius:12px;margin-top:0.75rem">
            <p style="margin:0;font-size:0.85rem;color:#404040">
                <i class="fas fa-ban"></i>
                <?= $translations['settings_push_blocked'] ?? 'Push notifications are blocked. Go to your browser/device settings to change this.' ?>
            </p>
        </div>
    </div>

    <!-- Password Change Section -->
    <div class="settings-section">
        <div class="settings-section-header">
            <div class="settings-section-icon" style="background:linear-gradient(135deg,#000000,#404040)">
                <i class="fas fa-key"></i>
            </div>
            <div>
                <h3><?= $translations['settings_change_password'] ?? 'Change password' ?></h3>
                <p><?= $translations['settings_choose_strong_password'] ?? 'Choose a strong password' ?></p>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="action" value="change_password">

            <div class="form-group">
                <label class="form-label"><?= $translations['settings_current_password'] ?? 'Current password' ?></label>
                <div class="password-wrapper">
                    <input type="password" name="current_password" id="current_password" class="form-control" placeholder="<?= $translations['settings_current_password_placeholder'] ?? 'Your current password' ?>" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('current_password', this)" aria-label="<?= $translations['settings_show_password'] ?? 'Show password' ?>">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $translations['new_password'] ?? 'New password' ?></label>
                <div class="password-wrapper">
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="<?= $translations['settings_min_8_chars'] ?? 'Minimum 8 characters' ?>" minlength="8" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('new_password', this)" aria-label="<?= $translations['settings_show_password'] ?? 'Show password' ?>">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $translations['settings_confirm_new_password'] ?? 'Confirm new password' ?></label>
                <div class="password-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="<?= $translations['settings_repeat_new_password'] ?? 'Repeat new password' ?>" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)" aria-label="<?= $translations['settings_show_password'] ?? 'Show password' ?>">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-block" style="background:linear-gradient(135deg,#000000,#404040)">
                <i class="fas fa-key"></i> <?= $translations['settings_change_password'] ?? 'Change password' ?>
            </button>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="settings-section danger-zone">
        <div class="settings-section-header">
            <div class="settings-section-icon" style="background:linear-gradient(135deg,#333333,#dc2626)">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h3><?= $translations['settings_delete_account'] ?? 'Delete account' ?></h3>
                <p><?= $translations['settings_cannot_be_undone'] ?? 'This cannot be undone' ?></p>
            </div>
        </div>

        <p style="color:var(--text-light);font-size:0.9rem;margin-bottom:1rem;line-height:1.6">
            <?= $translations['settings_delete_warning'] ?? 'If you delete your account, all your data will be permanently removed. Pending bookings will be cancelled. This action cannot be undone.' ?>
        </p>

        <button type="button" class="btn btn-danger-outline btn-block" onclick="toggleDeleteConfirm()">
            <i class="fas fa-trash-alt"></i> <?= $translations['settings_delete_account'] ?? 'Delete account' ?>
        </button>

        <div class="delete-confirm-box" id="deleteConfirmBox">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="delete_account">

                <p style="color:#dc2626;font-weight:600;margin-bottom:1rem">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $translations['settings_confirm_delete'] ?? 'Are you sure you want to delete your account?' ?>
                </p>

                <div class="form-group">
                    <label class="form-label"><?= $translations['settings_your_password'] ?? 'Your password' ?></label>
                    <div class="password-wrapper">
                        <input type="password" name="delete_password" id="delete_password" class="form-control" placeholder="<?= $translations['settings_enter_password'] ?? 'Enter your password' ?>" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('delete_password', this)" aria-label="<?= $translations['settings_show_password'] ?? 'Show password' ?>">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $translations['settings_type_delete_confirm'] ?? 'Type "DELETE" to confirm' ?></label>
                    <input type="text" name="delete_confirmation" class="form-control" placeholder="<?= $translations['settings_delete_keyword'] ?? 'DELETE' ?>" required>
                </div>

                <div style="display:flex;gap:0.75rem">
                    <button type="button" class="btn btn-secondary" style="flex:1" onclick="toggleDeleteConfirm()">
                        <?= $translations['cancel'] ?? 'Cancel' ?>
                    </button>
                    <button type="submit" class="btn btn-danger" style="flex:1">
                        <i class="fas fa-trash-alt"></i> <?= $translations['settings_permanently_delete'] ?? 'Permanently delete' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Info -->
    <div class="settings-section" style="background:var(--secondary)">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem">
            <div style="width:50px;height:50px;background:linear-gradient(135deg,var(--primary),#000000);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:1.25rem;font-weight:700">
                <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <h4 style="margin:0"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></h4>
                <p style="margin:0.25rem 0 0;color:var(--text-light);font-size:0.85rem"><?= htmlspecialchars($user['email'] ?? '') ?></p>
            </div>
        </div>
        <div style="font-size:0.8rem;color:var(--text-light)">
            <p style="margin:0"><i class="fas fa-calendar-alt"></i> <?= $translations['settings_member_since'] ?? 'Member since' ?> <?= date('d-m-Y', strtotime($user['created_at'] ?? 'now')) ?></p>
            <?php if (!empty($user['email_verified'])): ?>
                <p style="margin:0.25rem 0 0;color:#333333"><i class="fas fa-check-circle"></i> <?= $translations['settings_email_verified'] ?? 'Email verified' ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const jsTranslations = {
    pushNotSupported: '<?= addslashes($translations['settings_push_not_supported_short'] ?? 'Not supported') ?>',
    pushBlocked: '<?= addslashes($translations['settings_push_blocked_short'] ?? 'Blocked') ?>',
    pushEnabled: '<?= addslashes($translations['settings_push_enabled'] ?? 'Notifications are enabled') ?>',
    pushReceiveReminders: '<?= addslashes($translations['settings_push_receive_reminders'] ?? 'Receive reminders for appointments') ?>',
    pushLoadError: '<?= addslashes($translations['settings_push_load_error'] ?? 'Error loading: ') ?>',
    pushProcessing: '<?= addslashes($translations['settings_push_processing'] ?? 'Processing...') ?>',
    pushRequestingPermission: '<?= addslashes($translations['settings_push_requesting_permission'] ?? 'Requesting permission...') ?>',
    pushLoadingWorker: '<?= addslashes($translations['settings_push_loading_worker'] ?? 'Loading service worker...') ?>',
    pushFetchingKey: '<?= addslashes($translations['settings_push_fetching_key'] ?? 'Fetching key...') ?>',
    pushNoVapidKey: '<?= addslashes($translations['settings_push_no_vapid_key'] ?? 'No VAPID key') ?>',
    pushSubscribing: '<?= addslashes($translations['settings_push_subscribing'] ?? 'Subscribing...') ?>',
    pushSaving: '<?= addslashes($translations['settings_push_saving'] ?? 'Saving...') ?>',
    pushSaveFailed: '<?= addslashes($translations['settings_push_save_failed'] ?? 'Save failed') ?>',
    pushEnabledToast: '<?= addslashes($translations['settings_push_enabled_toast'] ?? 'Push notifications enabled!') ?>',
    pushBlockedByBrowser: '<?= addslashes($translations['settings_push_blocked_browser'] ?? 'Blocked by browser') ?>',
    pushPermissionDenied: '<?= addslashes($translations['settings_push_permission_denied'] ?? 'Permission denied') ?>',
    pushError: '<?= addslashes($translations['settings_push_error'] ?? 'Error: ') ?>',
    pushDisabledToast: '<?= addslashes($translations['settings_push_disabled_toast'] ?? 'Push notifications disabled') ?>',
    pushSomethingWrong: '<?= addslashes($translations['settings_push_something_wrong'] ?? 'Something went wrong') ?>'
};

function toggleDeleteConfirm() {
    const box = document.getElementById('deleteConfirmBox');
    box.classList.toggle('show');
    if (box.classList.contains('show')) {
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

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

// Push Notification Toggle
document.addEventListener('DOMContentLoaded', async function() {
    const toggle = document.getElementById('pushToggle');
    const statusText = document.getElementById('pushStatusText');
    const notSupported = document.getElementById('pushNotSupported');
    const blocked = document.getElementById('pushBlocked');

    // Check if push notifications are supported
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        toggle.disabled = true;
        notSupported.style.display = 'block';
        statusText.textContent = jsTranslations.pushNotSupported;
        return;
    }

    // Check permission status
    if (Notification.permission === 'denied') {
        toggle.disabled = true;
        blocked.style.display = 'block';
        statusText.textContent = jsTranslations.pushBlocked;
        return;
    }

    // Register service worker first
    try {
        await navigator.serviceWorker.register('/sw.js');
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();

        if (subscription) {
            toggle.checked = true;
            statusText.textContent = jsTranslations.pushEnabled;
        } else {
            toggle.checked = false;
            statusText.textContent = jsTranslations.pushReceiveReminders;
        }
    } catch (error) {
        console.error('Error checking subscription:', error);
        statusText.textContent = jsTranslations.pushLoadError + error.message;
    }
});

async function togglePushNotifications(checkbox) {
    const statusText = document.getElementById('pushStatusText');
    const blocked = document.getElementById('pushBlocked');

    checkbox.disabled = true;
    statusText.textContent = jsTranslations.pushProcessing;

    if (checkbox.checked) {
        // Enable push notifications
        try {
            // Step 1: Request permission
            statusText.textContent = 'Toestemming vragen...';
            const permission = await Notification.requestPermission();

            if (permission === 'granted') {
                // Step 2: Get service worker
                statusText.textContent = 'Service worker laden...';
                await navigator.serviceWorker.register('/sw.js');
                const registration = await navigator.serviceWorker.ready;

                // Step 3: Get VAPID key
                statusText.textContent = 'Sleutel ophalen...';
                const response = await fetch('/api/push/vapid-key');
                const { publicKey } = await response.json();

                if (!publicKey) {
                    throw new Error('Geen VAPID sleutel');
                }

                // Step 4: Subscribe to push
                statusText.textContent = 'Abonneren...';
                const applicationServerKey = urlBase64ToUint8Array(publicKey);
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });

                // Step 5: Save to server
                statusText.textContent = 'Opslaan...';
                const saveResponse = await fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(subscription.toJSON())
                });

                const saveResult = await saveResponse.json();
                if (!saveResult.success) {
                    throw new Error('Opslaan mislukt');
                }

                statusText.textContent = 'Meldingen zijn ingeschakeld';
                showToast('Push meldingen ingeschakeld!', 'success');
            } else if (permission === 'denied') {
                checkbox.checked = false;
                checkbox.disabled = true;
                blocked.style.display = 'block';
                statusText.textContent = 'Geblokkeerd door browser';
            } else {
                checkbox.checked = false;
                statusText.textContent = 'Toestemming geweigerd';
            }
        } catch (error) {
            console.error('Error enabling push:', error);
            checkbox.checked = false;
            statusText.textContent = 'Fout: ' + error.message;
            showToast('Fout: ' + error.message, 'error');
        }
    } else {
        // Disable push notifications
        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();

            if (subscription) {
                await fetch('/api/push/unsubscribe', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ endpoint: subscription.endpoint })
                });

                await subscription.unsubscribe();
            }

            statusText.textContent = 'Ontvang herinneringen voor afspraken';
            showToast('Push meldingen uitgeschakeld', 'info');
        } catch (error) {
            console.error('Error disabling push:', error);
            checkbox.checked = true;
            showToast('Er ging iets mis', 'error');
        }
    }

    checkbox.disabled = false;
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function showToast(message, type = 'info') {
    // Use global showToast if available, otherwise create simple one
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
        return;
    }
    const toast = document.createElement('div');
    toast.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);padding:12px 24px;border-radius:8px;color:white;font-weight:500;z-index:10000;animation:fadeIn 0.3s';
    toast.style.background = type === 'success' ? '#333333' : type === 'error' ? '#333333' : '#6b7280';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

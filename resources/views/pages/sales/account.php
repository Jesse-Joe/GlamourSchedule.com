<?php ob_start(); ?>

<div class="grid-2" style="gap:1.5rem">
    <!-- Account Details -->
    <div class="card">
        <h3><i class="fas fa-user"></i> Account Gegevens</h3>

        <form method="POST" action="/sales/account">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Voornaam *</label>
                    <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'error' : '' ?>" value="<?= htmlspecialchars($data['first_name'] ?? $salesUser['first_name'] ?? '') ?>" required>
                    <?php if (isset($errors['first_name'])): ?>
                        <span class="form-error"><?= $errors['first_name'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Achternaam *</label>
                    <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'error' : '' ?>" value="<?= htmlspecialchars($data['last_name'] ?? $salesUser['last_name'] ?? '') ?>" required>
                    <?php if (isset($errors['last_name'])): ?>
                        <span class="form-error"><?= $errors['last_name'] ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">E-mailadres *</label>
                <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'error' : '' ?>" value="<?= htmlspecialchars($data['email'] ?? $salesUser['email'] ?? '') ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="form-error"><?= $errors['email'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Telefoonnummer</label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($data['phone'] ?? $salesUser['phone'] ?? '') ?>" placeholder="06-12345678">
            </div>

            <div class="form-group">
                <label class="form-label">IBAN (voor uitbetalingen)</label>
                <input type="text" name="iban" class="form-control <?= isset($errors['iban']) ? 'error' : '' ?>" value="<?= htmlspecialchars($data['iban'] ?? $salesUser['iban'] ?? '') ?>" placeholder="NL00BANK0123456789">
                <?php if (isset($errors['iban'])): ?>
                    <span class="form-error"><?= $errors['iban'] ?></span>
                <?php endif; ?>
                <small style="color:#666;display:block;margin-top:0.5rem">Je IBAN wordt gebruikt voor commissie uitbetalingen</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%">
                <i class="fas fa-save"></i> Gegevens Opslaan
            </button>
        </form>
    </div>

    <!-- Password Change -->
    <div class="card">
        <h3><i class="fas fa-lock"></i> Wachtwoord Wijzigen</h3>

        <form method="POST" action="/sales/account/password">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">

            <div class="form-group">
                <label class="form-label">Huidig Wachtwoord *</label>
                <div class="password-wrapper">
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('current_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nieuw Wachtwoord *</label>
                <div class="password-wrapper">
                    <input type="password" name="new_password" id="new_password" class="form-control" minlength="8" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('new_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small style="color:#666;display:block;margin-top:0.5rem">Minimaal 8 karakters</small>
            </div>

            <div class="form-group">
                <label class="form-label">Bevestig Nieuw Wachtwoord *</label>
                <div class="password-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" minlength="8" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%">
                <i class="fas fa-key"></i> Wachtwoord Wijzigen
            </button>
        </form>
    </div>
</div>

<!-- Account Info -->
<div class="card" style="margin-top:1.5rem">
    <h3><i class="fas fa-info-circle"></i> Account Informatie</h3>

    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Referral Code</span>
            <span class="info-value" style="font-family:monospace;font-size:1.1rem;letter-spacing:2px"><?= htmlspecialchars($salesUser['referral_code'] ?? '-') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Account Status</span>
            <span class="info-value">
                <?php if (($salesUser['status'] ?? '') === 'active'): ?>
                    <span style="color:#22c55e"><i class="fas fa-check-circle"></i> Actief</span>
                <?php else: ?>
                    <span style="color:#ef4444"><i class="fas fa-times-circle"></i> <?= ucfirst($salesUser['status'] ?? 'Onbekend') ?></span>
                <?php endif; ?>
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Account Aangemaakt</span>
            <span class="info-value"><?= isset($salesUser['created_at']) ? date('d-m-Y H:i', strtotime($salesUser['created_at'])) : '-' ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Laatst Bijgewerkt</span>
            <span class="info-value"><?= isset($salesUser['updated_at']) ? date('d-m-Y H:i', strtotime($salesUser['updated_at'])) : '-' ?></span>
        </div>
    </div>
</div>

<!-- Account Verwijderen -->
<div class="card danger-zone" style="margin-top:1.5rem">
    <h3><i class="fas fa-exclamation-triangle"></i> Account Opzeggen</h3>

    <p style="color:#a1a1a1;margin-bottom:1.5rem">
        Als je je sales partner account wilt opzeggen, kun je hieronder je account verwijderen.
        Let op: dit kan niet ongedaan worden gemaakt en je verliest toegang tot je commissies en referrals.
    </p>

    <div class="danger-info">
        <div class="danger-item">
            <i class="fas fa-coins"></i>
            <div>
                <strong>Openstaande commissies</strong>
                <span>Niet-uitbetaalde commissies worden geannuleerd</span>
            </div>
        </div>
        <div class="danger-item">
            <i class="fas fa-users"></i>
            <div>
                <strong>Referrals</strong>
                <span>Je referral code wordt ongeldig</span>
            </div>
        </div>
        <div class="danger-item">
            <i class="fas fa-chart-line"></i>
            <div>
                <strong>Statistieken</strong>
                <span>Al je gegevens worden permanent verwijderd</span>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-danger" onclick="showDeleteModal()" style="width:100%;margin-top:1.5rem">
        <i class="fas fa-trash-alt"></i> Account Verwijderen
    </button>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" style="display:none">
    <div class="modal-content">
        <div class="modal-header">
            <i class="fas fa-exclamation-triangle" style="color:#ef4444;font-size:2rem"></i>
            <h3>Account Permanent Verwijderen?</h3>
        </div>
        <p>Deze actie kan niet ongedaan worden gemaakt. Al je gegevens, commissies en referrals worden permanent verwijderd.</p>
        <p style="margin-top:1rem"><strong>Typ "VERWIJDER" om te bevestigen:</strong></p>
        <form method="POST" action="/sales/account/delete" id="deleteForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
            <input type="text" name="confirm_text" id="confirmText" class="form-control" placeholder="Typ VERWIJDER" autocomplete="off" style="margin-top:0.75rem">
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" onclick="hideDeleteModal()">Annuleren</button>
                <button type="submit" class="btn btn-danger" id="deleteBtn" disabled>
                    <i class="fas fa-trash-alt"></i> Definitief Verwijderen
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #a1a1a1;
    }
    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #333;
        border-radius: 10px;
        font-size: 1rem;
        transition: border-color 0.2s;
        box-sizing: border-box;
        background: #0a0a0a;
        color: #fff;
    }
    .form-control::placeholder {
        color: #555;
    }
    .form-control:focus {
        outline: none;
        border-color: #fff;
    }
    .form-control.error {
        border-color: #ef4444;
    }
    .form-error {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: block;
    }
    .password-wrapper {
        position: relative;
    }
    .password-wrapper .form-control {
        padding-right: 44px;
    }
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 4px;
    }
    .password-toggle:hover {
        color: #fff;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .info-label {
        font-size: 0.85rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-size: 1rem;
        color: #fff;
        font-weight: 500;
    }

    /* Danger Zone */
    .danger-zone {
        border-color: #dc2626 !important;
        background: #1a0a0a !important;
    }
    .danger-zone h3 {
        color: #ef4444;
    }
    .danger-info {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .danger-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        background: rgba(239, 68, 68, 0.1);
        border-radius: 10px;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .danger-item i {
        color: #ef4444;
        font-size: 1.25rem;
        margin-top: 0.125rem;
    }
    .danger-item strong {
        display: block;
        color: #fff;
        margin-bottom: 0.25rem;
    }
    .danger-item span {
        color: #a1a1a1;
        font-size: 0.9rem;
    }
    .btn-danger {
        background: #dc2626;
        color: #fff;
        border: none;
        padding: 0.875rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-danger:hover {
        background: #b91c1c;
    }
    .btn-danger:disabled {
        background: #333;
        color: #666;
        cursor: not-allowed;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        padding: 1rem;
    }
    .modal-content {
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 16px;
        padding: 2rem;
        max-width: 450px;
        width: 100%;
    }
    .modal-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .modal-header h3 {
        color: #fff;
        margin: 1rem 0 0 0;
    }
    .modal-content > p {
        color: #a1a1a1;
        line-height: 1.6;
    }
    .modal-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .modal-buttons .btn {
        flex: 1;
    }
    .btn-secondary {
        background: #333;
        color: #fff;
        border: none;
        padding: 0.875rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-secondary:hover {
        background: #444;
    }
</style>

<script>
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

function showDeleteModal() {
    document.getElementById('deleteModal').style.display = 'flex';
    document.getElementById('confirmText').focus();
}

function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.getElementById('confirmText').value = '';
    document.getElementById('deleteBtn').disabled = true;
}

document.getElementById('confirmText').addEventListener('input', function() {
    document.getElementById('deleteBtn').disabled = this.value !== 'VERWIJDER';
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') hideDeleteModal();
});
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/resources/views/layouts/sales.php';
?>

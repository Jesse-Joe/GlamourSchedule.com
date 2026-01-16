<?php ob_start(); ?>

<style>
/* Dark Dashboard Theme */
.dashboard-page {
    background: #000000;
    min-height: 100vh;
    padding-bottom: 2rem;
}
.dashboard-page .container {
    padding-top: 1rem;
}

.security-container {
    max-width: 500px;
    margin: 0 auto;
}
.dashboard-page .card {
    background: #111111;
    border: 1px solid #333333;
    color: #ffffff;
}
.dashboard-page h2, .dashboard-page h4 {
    color: #ffffff;
}
.dashboard-page p {
    color: rgba(255,255,255,0.7);
}
.pin-input-group {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin: 1.5rem 0;
}
.pin-digit {
    width: 50px;
    height: 60px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    border: 2px solid #333333;
    border-radius: 12px;
    background: #1a1a1a;
    color: #ffffff;
    transition: all 0.2s;
}
.pin-digit:focus {
    border-color: #ffffff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.1);
}
.pin-digit.filled {
    border-color: #ffffff;
    background: #333333;
}
@media (max-width: 400px) {
    .pin-digit {
        width: 42px;
        height: 52px;
        font-size: 1.25rem;
    }
}
.security-feature {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: #1a1a1a;
    border-radius: 16px;
    margin-bottom: 1rem;
}
.security-feature-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
    color: white;
}
.security-feature-content {
    flex: 1;
}
.security-feature-content h4 {
    margin: 0 0 0.25rem;
    font-size: 1rem;
    color: #ffffff;
}
.security-feature-content p {
    margin: 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
}
.status-active {
    background: rgba(34,197,94,0.2);
    color: #22c55e;
}
.status-inactive {
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.6);
}

/* Password Toggle */
.password-wrapper {
    position: relative;
}
.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: rgba(255,255,255,0.5);
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
    padding-right: 44px;
}
.dashboard-page .form-control {
    background: transparent;
    border: none;
    border-bottom: 2px solid rgba(255,255,255,0.3);
    border-radius: 0;
    color: #ffffff;
}
.dashboard-page .form-control:focus {
    border-bottom-color: #ffffff;
}
.dashboard-page .form-label {
    color: rgba(255,255,255,0.9);
}
.dashboard-page .btn {
    background: #ffffff;
    color: #000000;
}
.dashboard-page .btn-danger {
    background: #dc2626;
    color: #ffffff;
}
</style>

<div class="dashboard-page">
<div class="container security-container">
    <div class="card">
        <h2 style="margin:0 0 0.5rem;display:flex;align-items:center;gap:0.75rem">
            <i class="fas fa-shield-alt" style="color:var(--primary)"></i> Beveiliging
        </h2>
        <p style="color:var(--text-light);margin:0 0 1.5rem">Beveilig je account met extra bescherming</p>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>" style="margin-bottom:1.5rem">
                <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- PIN Code Feature -->
        <div class="security-feature">
            <div class="security-feature-icon" style="background:linear-gradient(135deg,#000000,#262626)">
                <i class="fas fa-lock"></i>
            </div>
            <div class="security-feature-content">
                <h4>PIN Code</h4>
                <p>Vraag om een 6-cijferige PIN code wanneer de app wordt geopend</p>
                <?php if ($pinEnabled): ?>
                    <span class="status-badge status-active"><i class="fas fa-check"></i> Actief</span>
                <?php else: ?>
                    <span class="status-badge status-inactive"><i class="fas fa-times"></i> Niet ingesteld</span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($pinEnabled): ?>
            <!-- Remove PIN Form -->
            <div class="card" style="background:var(--secondary);margin-top:1rem">
                <h4 style="margin:0 0 1rem"><i class="fas fa-trash-alt"></i> PIN verwijderen</h4>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="action" value="remove_pin">

                    <div class="form-group">
                        <label class="form-label">Bevestig met je wachtwoord</label>
                        <div class="password-wrapper">
                            <input type="password" name="current_password" id="current_password_remove" class="form-control" placeholder="Je huidige wachtwoord" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('current_password_remove', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger" style="width:100%">
                        <i class="fas fa-trash-alt"></i> PIN verwijderen
                    </button>
                </form>
            </div>
        <?php else: ?>
            <!-- Set PIN Form -->
            <form method="POST" id="pinForm">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="set_pin">
                <input type="hidden" name="pin" id="pinValue">
                <input type="hidden" name="pin_confirm" id="pinConfirmValue">

                <div style="margin-top:1.5rem">
                    <h4 style="margin:0 0 0.5rem">Stel je PIN in</h4>
                    <p style="color:var(--text-light);font-size:0.9rem;margin:0 0 1rem">Kies een 6-cijferige code die je kunt onthouden</p>

                    <label style="font-weight:600;margin-bottom:0.5rem;display:block">PIN code</label>
                    <div class="pin-input-group" id="pinInputs">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="0" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="2" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="3" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="4" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="5" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                    </div>

                    <label style="font-weight:600;margin:1.5rem 0 0.5rem;display:block">Bevestig PIN code</label>
                    <div class="pin-input-group" id="pinConfirmInputs">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="0" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="2" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="3" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="4" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="tel" maxlength="1" class="pin-digit" data-index="5" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                    </div>

                    <div class="form-group" style="margin-top:1.5rem">
                        <label class="form-label">Bevestig met je wachtwoord</label>
                        <div class="password-wrapper">
                            <input type="password" name="current_password" id="current_password_set" class="form-control" placeholder="Je huidige wachtwoord" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('current_password_set', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn" style="width:100%;padding:1rem">
                        <i class="fas fa-lock"></i> PIN code instellen
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Security Tips -->
    <div class="card" style="background:linear-gradient(135deg,#ffffff,#f5f5f5);color:#000000">
        <h4 style="margin:0 0 0.75rem;color:#000000"><i class="fas fa-lightbulb"></i> Beveiligingstips</h4>
        <ul style="margin:0;padding-left:1.25rem;font-size:0.9rem;opacity:0.8;line-height:1.8;color:#000000">
            <li>Gebruik geen voor de hand liggende codes zoals 123456</li>
            <li>Deel je PIN nooit met anderen</li>
            <li>Gebruik een unieke PIN die je niet elders gebruikt</li>
        </ul>
    </div>
</div>
</div>

<script>
// PIN Input Handler
function setupPinInputs(containerId, hiddenInputId) {
    const container = document.getElementById(containerId);
    const inputs = container.querySelectorAll('.pin-digit');
    const hiddenInput = document.getElementById(hiddenInputId);

    inputs.forEach((input, index) => {
        // Only allow numbers
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length === 1) {
                this.classList.add('filled');
                // Move to next input
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            } else {
                this.classList.remove('filled');
            }

            updateHiddenValue();
        });

        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                inputs[index - 1].focus();
                inputs[index - 1].value = '';
                inputs[index - 1].classList.remove('filled');
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/[^0-9]/g, '').slice(0, 6);

            digits.split('').forEach((digit, i) => {
                if (inputs[i]) {
                    inputs[i].value = digit;
                    inputs[i].classList.add('filled');
                }
            });

            if (digits.length > 0) {
                const focusIndex = Math.min(digits.length, inputs.length - 1);
                inputs[focusIndex].focus();
            }

            updateHiddenValue();
        });
    });

    function updateHiddenValue() {
        let value = '';
        inputs.forEach(input => value += input.value);
        hiddenInput.value = value;
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    setupPinInputs('pinInputs', 'pinValue');
    setupPinInputs('pinConfirmInputs', 'pinConfirmValue');
});

// Password toggle function
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

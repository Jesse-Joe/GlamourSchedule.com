<?php ob_start(); ?>

<style>
    /* Dark Mode for verify-email */
    [data-theme="dark"] .verify-email-card {
        background: var(--bg-card) !important;
    }
    [data-theme="dark"] .verify-email-card .code-input {
        background: var(--bg-secondary) !important;
        border-color: var(--border) !important;
        color: var(--primary) !important;
    }
    [data-theme="dark"] .verify-email-card .code-input:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2) !important;
    }
    [data-theme="dark"] .verify-email-card .alert-danger {
        background: rgba(0, 0, 0, 0.1) !important;
        border-color: rgba(0, 0, 0, 0.1) !important;
        color: #d4d4d4 !important;
    }
    [data-theme="dark"] .verify-email-card .alert-success {
        background: rgba(16, 185, 129, 0.15) !important;
        border-color: rgba(16, 185, 129, 0.3) !important;
        color: #6ee7b7 !important;
    }
</style>

<div class="container" style="max-width:500px;margin-top:3rem">
    <div class="card verify-email-card" style="text-align:center">
        <div style="margin-bottom:2rem">
            <div style="width:80px;height:80px;background:linear-gradient(135deg,#000000,#000000);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-envelope" style="font-size:2rem;color:white"></i>
            </div>
            <h2 style="margin-bottom:0.5rem"><?= $translations['verify_email'] ?? 'Verify your email' ?></h2>
            <p style="color:var(--text-light)">
                <?= $translations['code_sent_to'] ?? 'We sent a 6-digit code to' ?><br>
                <strong><?= htmlspecialchars($maskedEmail) ?></strong>
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="text-align:left;margin-bottom:1.5rem">
                <?php
                $errorMessages = [
                    'csrf' => $translations['error_csrf'] ?? 'Security error. Please try again.',
                    'invalid_code' => $translations['error_invalid_code'] ?? 'Enter a valid 6-digit code.',
                    'wrong_code' => $translations['error_wrong_code'] ?? 'Incorrect code. Check your email and try again.',
                    'expired' => $translations['error_expired'] ?? 'This code has expired. Request a new code.',
                    'max_attempts' => $translations['error_max_attempts'] ?? 'Too many attempts. Request a new code.',
                    'rate_limit' => $translations['error_rate_limit'] ?? 'Please wait before requesting a new code.'
                ];
                echo $errorMessages[$error] ?? ($translations['error_generic'] ?? 'An error occurred.');
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success" style="text-align:left;margin-bottom:1.5rem">
                <?php if ($success === 'resent'): ?>
                    <i class="fas fa-check"></i> <?= $translations['new_code_sent'] ?? 'A new code has been sent to your email.' ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/verify-email" id="verifyForm">
            <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">

            <div class="form-group">
                <label style="font-weight:600;margin-bottom:1rem;display:block"><?= $translations['enter_verification_code'] ?? 'Enter your verification code' ?></label>
                <div style="display:flex;justify-content:center;gap:8px" id="codeInputs">
                    <?php for ($i = 0; $i < 6; $i++): ?>
                        <input type="text"
                               class="code-input"
                               maxlength="1"
                               pattern="[0-9]"
                               inputmode="numeric"
                               autocomplete="off"
                               style="width:50px;height:60px;text-align:center;font-size:1.5rem;font-weight:bold;border:2px solid var(--border);border-radius:10px"
                               data-index="<?= $i ?>">
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="code" id="codeHidden">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:1.5rem;padding:1rem">
                <i class="fas fa-check"></i> <?= $translations['verify_btn'] ?? 'Verify Account' ?>
            </button>
        </form>

        <hr style="margin:2rem 0;border:none;border-top:1px solid var(--border)">

        <p style="color:var(--text-light);margin-bottom:1rem"><?= $translations['no_code_received'] ?? 'Didn\'t receive a code?' ?></p>

        <form method="POST" action="/verify-email/resend" style="display:inline">
            <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
            <button type="submit" class="btn" style="background:transparent;color:var(--primary);border:1px solid var(--primary)">
                <i class="fas fa-redo"></i> <?= $translations['resend_code'] ?? 'Send new code' ?>
            </button>
        </form>

        <p class="timer" style="margin-top:2rem;font-size:0.9rem;color:var(--text-light)">
            <i class="fas fa-clock"></i> <span id="countdown">10:00</span>
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.code-input');
    const hiddenInput = document.getElementById('codeHidden');
    const form = document.getElementById('verifyForm');

    // Focus first input
    inputs[0].focus();

    inputs.forEach((input, index) => {
        // Handle input
        input.addEventListener('input', function(e) {
            const value = e.target.value;

            // Only allow digits
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            // Move to next input
            if (value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            updateHiddenInput();

            // Auto submit when all filled
            if (index === inputs.length - 1 && value) {
                const code = Array.from(inputs).map(i => i.value).join('');
                if (code.length === 6) {
                    form.submit();
                }
            }
        });

        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/\D/g, '').split('');

            digits.forEach((digit, i) => {
                if (inputs[index + i]) {
                    inputs[index + i].value = digit;
                }
            });

            updateHiddenInput();

            // Focus last filled input or next empty
            const lastIndex = Math.min(index + digits.length - 1, inputs.length - 1);
            inputs[lastIndex].focus();

            // Auto submit if complete
            const code = Array.from(inputs).map(i => i.value).join('');
            if (code.length === 6) {
                setTimeout(() => form.submit(), 100);
            }
        });
    });

    function updateHiddenInput() {
        hiddenInput.value = Array.from(inputs).map(i => i.value).join('');
    }

    // Countdown timer
    let timeLeft = 10 * 60;
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        const countdownInterval = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                countdownEl.textContent = '<?= addslashes($translations['error_expired'] ?? 'Expired') ?>';
                countdownEl.style.color = 'var(--danger, #ef4444)';
            }
        }, 1000);
    }

    // Form submit validation
    form.addEventListener('submit', function(e) {
        updateHiddenInput();
        if (hiddenInput.value.length !== 6) {
            e.preventDefault();
            alert('<?= addslashes($translations['enter_all_digits'] ?? 'Enter all 6 digits') ?>');
        }
    });
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

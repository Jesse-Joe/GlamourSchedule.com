<?php ob_start(); ?>

<style>
    .verify-page {
        background: #000000;
        min-height: 100vh;
        padding: 2rem 0;
    }
    .verify-container {
        max-width: 450px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    .verify-card {
        background: #111111;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        overflow: hidden;
        border: 1px solid #333333;
    }
    .verify-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }
    .verify-header i {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }
    .verify-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: #ffffff;
    }
    .verify-body {
        padding: 2rem;
    }
    .verify-info {
        text-align: center;
        margin-bottom: 2rem;
    }
    .verify-info p {
        color: rgba(255,255,255,0.7);
        margin: 0;
    }
    .verify-info .email {
        font-weight: 600;
        color: #ffffff;
        display: block;
        margin-top: 0.5rem;
    }
    .code-inputs {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .code-input {
        width: 50px;
        height: 60px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        border: 2px solid #333333;
        border-radius: 12px;
        transition: all 0.3s ease;
        color: #ffffff;
        background: #1a1a1a;
    }
    .code-input:focus {
        outline: none;
        border-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
    .code-input.filled {
        border-color: #ffffff;
        background: #333333;
    }
    .timer {
        text-align: center;
        color: rgba(255,255,255,0.7);
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }
    .timer span {
        font-weight: 600;
        color: #ffffff;
    }
    .resend-btn {
        background: none;
        border: none;
        color: #ffffff;
        cursor: pointer;
        font-weight: 600;
        text-decoration: underline;
        padding: 0;
    }
    .resend-btn:disabled {
        color: rgba(255,255,255,0.5);
        cursor: not-allowed;
        text-decoration: none;
    }
    .btn-verify {
        width: 100%;
        padding: 1rem;
        background: #ffffff;
        color: #000000;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-verify:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
    }
    .btn-verify:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .back-link {
        display: block;
        text-align: center;
        margin-top: 1.5rem;
        color: rgba(255,255,255,0.7);
    }
    .back-link a {
        color: #ffffff;
    }
    .verify-page .alert-danger {
        background: rgba(239,68,68,0.2);
        border: 1px solid rgba(239,68,68,0.3);
        color: #f87171;
    }
</style>

<div class="verify-page">
<div class="verify-container">
    <div class="verify-card">
        <div class="verify-header">
            <i class="fas fa-envelope-open-text"></i>
            <h2><?= $__('verify_email') ?></h2>
        </div>

        <div class="verify-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" style="margin-bottom:1.5rem"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="verify-info">
                <p><?= $__('code_sent_to') ?></p>
                <span class="email"><?= htmlspecialchars($email) ?></span>
            </div>

            <form method="POST" action="<?= $type === 'registration' ? '/verify-registration' : '/verify-login' ?>" id="verifyForm">
                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                <input type="hidden" name="code" id="fullCode">

                <div class="code-inputs">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autofocus>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric">
                </div>

                <div class="timer">
                    <?= $__('code_valid_for') ?>: <span id="countdown">10:00</span>
                </div>

                <button type="submit" class="btn-verify" id="submitBtn" disabled>
                    <i class="fas fa-check"></i> <?= $__('verify_btn') ?>
                </button>
            </form>

            <div style="text-align:center;margin-top:1.5rem">
                <p style="color:var(--text-muted);margin-bottom:0.5rem"><?= $__('resend_code') ?>?</p>
                <button type="button" class="resend-btn" id="resendBtn" disabled onclick="resendCode()">
                    <?= $__('resend_code') ?>
                </button>
                <span id="resendTimer" style="color:var(--text-muted);font-size:0.85rem"></span>
            </div>

            <p class="back-link">
                <a href="<?= $type === 'registration' ? '/register' : '/login' ?>">
                    <i class="fas fa-arrow-left"></i> <?= $__('back') ?>
                </a>
            </p>
        </div>
    </div>
</div>
</div>

<script>
// Translation strings for JavaScript
const jsTranslations = {
    expired: '<?= addslashes($__('error_expired')) ?>',
    codeSent: '<?= addslashes($__('email_sent')) ?>',
    errorOccurred: '<?= addslashes($__('error_generic')) ?>',
    resendCode: '<?= addslashes($__('resend_code')) ?>'
};

document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.code-input');
    const fullCodeInput = document.getElementById('fullCode');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('verifyForm');

    // Handle input
    inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;

            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            // Add filled class
            if (value) {
                e.target.classList.add('filled');
            } else {
                e.target.classList.remove('filled');
            }

            // Move to next input
            if (value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            updateFullCode();
        });

        input.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
                inputs[index - 1].value = '';
                inputs[index - 1].classList.remove('filled');
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);

            pasteData.split('').forEach((char, i) => {
                if (inputs[i]) {
                    inputs[i].value = char;
                    inputs[i].classList.add('filled');
                }
            });

            if (pasteData.length > 0) {
                inputs[Math.min(pasteData.length, inputs.length) - 1].focus();
            }

            updateFullCode();
        });
    });

    function updateFullCode() {
        let code = '';
        inputs.forEach(input => code += input.value);
        fullCodeInput.value = code;
        submitBtn.disabled = code.length !== 6;
    }

    // Countdown timer
    let timeLeft = 10 * 60; // 10 minutes
    const countdownEl = document.getElementById('countdown');

    const countdownInterval = setInterval(() => {
        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            countdownEl.textContent = jsTranslations.expired;
            countdownEl.style.color = 'var(--danger)';
        }
    }, 1000);

    // Resend timer
    let resendTimeLeft = 60;
    const resendBtn = document.getElementById('resendBtn');
    const resendTimer = document.getElementById('resendTimer');

    const resendInterval = setInterval(() => {
        resendTimeLeft--;
        resendTimer.textContent = ` (${resendTimeLeft}s)`;

        if (resendTimeLeft <= 0) {
            clearInterval(resendInterval);
            resendBtn.disabled = false;
            resendTimer.textContent = '';
        }
    }, 1000);
});

function resendCode() {
    const resendBtn = document.getElementById('resendBtn');
    const type = '<?= $type ?>';
    const endpoint = type === 'registration' ? '/api/resend-registration-code' : '/api/resend-login-code';

    resendBtn.disabled = true;
    resendBtn.textContent = '...';

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resendBtn.textContent = jsTranslations.codeSent;
            setTimeout(() => {
                resendBtn.textContent = jsTranslations.resendCode;
                // Reset resend timer
                let countdown = 60;
                const timer = document.getElementById('resendTimer');
                const interval = setInterval(() => {
                    countdown--;
                    timer.textContent = ` (${countdown}s)`;
                    if (countdown <= 0) {
                        clearInterval(interval);
                        resendBtn.disabled = false;
                        timer.textContent = '';
                    }
                }, 1000);
            }, 2000);
        } else {
            resendBtn.textContent = jsTranslations.errorOccurred;
            setTimeout(() => {
                resendBtn.textContent = jsTranslations.resendCode;
                resendBtn.disabled = false;
            }, 2000);
        }
    })
    .catch(() => {
        resendBtn.textContent = jsTranslations.errorOccurred;
        setTimeout(() => {
            resendBtn.textContent = jsTranslations.resendCode;
            resendBtn.disabled = false;
        }, 2000);
    });
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

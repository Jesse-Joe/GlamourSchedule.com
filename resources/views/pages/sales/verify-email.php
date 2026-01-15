<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifieer E-mail - GlamourSchedule Sales</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000000;
            padding: 2rem;
        }
        .verify-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            text-align: center;
        }
        .verify-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #333333, #000000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .verify-icon i {
            font-size: 2rem;
            color: white;
        }
        h1 {
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
            color: #1f2937;
        }
        .subtitle {
            color: #6b7280;
            margin: 0 0 2rem;
        }
        .email-display {
            background: #f5f5f5;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: #374151;
            font-weight: 500;
            margin-bottom: 1.5rem;
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
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            color: #333333;
            transition: all 0.2s;
        }
        .code-input:focus {
            outline: none;
            border-color: #333333;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }
        .btn-verify {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #333333, #000000);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .btn-verify:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .resend-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        .resend-text {
            color: #6b7280;
            font-size: 0.9rem;
            margin: 0 0 0.75rem;
        }
        .btn-resend {
            background: transparent;
            border: 2px solid #333333;
            color: #333333;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-resend:hover {
            background: #333333;
            color: white;
        }
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .alert-danger {
            background: #f5f5f5;
            border: 1px solid #e5e5e5;
            color: #000000;
        }
        .alert-success {
            background: #ffffff;
            border: 1px solid #86efac;
            color: #166534;
        }
        .timer {
            color: #6b7280;
            font-size: 0.85rem;
            margin-top: 1rem;
        }
        .timer span {
            color: #333333;
            font-weight: 600;
        }
        .steps-indicator {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e5e7eb;
        }
        .step-dot.active {
            background: #333333;
        }
        .step-dot.completed {
            background: #333333;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="steps-indicator">
            <div class="step-dot completed"></div>
            <div class="step-dot active"></div>
            <div class="step-dot"></div>
        </div>

        <div class="verify-icon">
            <i class="fas fa-envelope"></i>
        </div>

        <h1>Verifieer je E-mail</h1>
        <p class="subtitle">We hebben een 6-cijferige code gestuurd naar:</p>
        <div class="email-display"><?= htmlspecialchars($maskedEmail ?? '') ?></div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php
                $errors = [
                    'csrf' => 'Beveiligingsfout. Probeer het opnieuw.',
                    'invalid_code' => 'Voer een geldige 6-cijferige code in.',
                    'wrong_code' => 'Onjuiste code. Controleer je e-mail.',
                    'expired' => 'Code verlopen. Vraag een nieuwe aan.'
                ];
                echo $errors[$error] ?? 'Er is een fout opgetreden.';
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="POST" action="/sales/verify-email" id="verifyForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="code" id="codeHidden">

            <div class="code-inputs">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="<?= $i ?>">
                <?php endfor; ?>
            </div>

            <button type="submit" class="btn-verify" id="submitBtn" disabled>
                <i class="fas fa-check"></i> Verifieer Code
            </button>
        </form>

        <p class="timer"><i class="fas fa-clock"></i> Code geldig voor: <span id="countdown">30:00</span></p>

        <div class="resend-section">
            <p class="resend-text">Geen code ontvangen?</p>
            <form method="POST" action="/sales/verify-email/resend" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="btn-resend">
                    <i class="fas fa-redo"></i> Stuur nieuwe code
                </button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.code-input');
        const hiddenInput = document.getElementById('codeHidden');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('verifyForm');

        inputs[0].focus();

        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                if (!/^\d*$/.test(e.target.value)) {
                    e.target.value = '';
                    return;
                }
                if (e.target.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateCode();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                paste.split('').forEach((char, i) => {
                    if (inputs[i]) inputs[i].value = char;
                });
                updateCode();
                if (paste.length === 6) form.submit();
            });
        });

        function updateCode() {
            const code = Array.from(inputs).map(i => i.value).join('');
            hiddenInput.value = code;
            submitBtn.disabled = code.length !== 6;
        }

        // Countdown
        let time = 30 * 60;
        const countdown = document.getElementById('countdown');
        setInterval(() => {
            if (time > 0) {
                time--;
                const m = Math.floor(time / 60);
                const s = time % 60;
                countdown.textContent = m + ':' + s.toString().padStart(2, '0');
            } else {
                countdown.textContent = 'Verlopen';
                countdown.style.color = '#333333';
            }
        }, 1000);
    });
    </script>
</body>
</html>

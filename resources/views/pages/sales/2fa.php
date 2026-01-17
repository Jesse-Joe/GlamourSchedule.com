<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificatie - GlamourSchedule Sales</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0a0a0a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .container {
            width: 100%;
            max-width: 420px;
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo i {
            font-size: 2.5rem;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }
        .logo h1 {
            font-size: 1.5rem;
            color: #ffffff;
            font-weight: 600;
        }
        .logo span {
            color: #666;
            font-size: 0.9rem;
        }
        .card {
            background: #1a1a1a;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #333;
        }
        .card h2 {
            color: #fff;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .card p {
            color: #a1a1a1;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .card p strong {
            color: #fff;
        }
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            color: #a1a1a1;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .code-input-wrapper {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        .code-input {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            font-family: monospace;
            background: #0a0a0a;
            border: 2px solid #333;
            border-radius: 10px;
            color: #fff;
            transition: all 0.2s;
        }
        .code-input:focus {
            outline: none;
            border-color: #fff;
        }
        .code-input.filled {
            border-color: #22c55e;
            background: rgba(34, 197, 94, 0.1);
        }
        .hidden-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .btn {
            width: 100%;
            padding: 0.875rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: #fff;
            color: #000;
        }
        .btn-primary:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
        }
        .btn-primary:disabled {
            background: #333;
            color: #666;
            cursor: not-allowed;
            transform: none;
        }
        .resend-section {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #333;
        }
        .resend-section p {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
        }
        .resend-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #0a0a0a;
            border: 1px solid #333;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .resend-link:hover {
            border-color: #fff;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #fff;
        }
        .icon-shield {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #333, #1a1a1a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            border: 2px solid #333;
        }
        .icon-shield i {
            font-size: 1.5rem;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-chart-line"></i>
            <h1>Sales Portal</h1>
            <span>GlamourSchedule</span>
        </div>

        <div class="card">
            <div class="icon-shield">
                <i class="fas fa-shield-alt"></i>
            </div>

            <h2>Verificatiecode invoeren</h2>
            <p>We hebben een 6-cijferige code gestuurd naar<br><strong><?= htmlspecialchars($maskedEmail ?? '') ?></strong></p>

            <?php if (!empty($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] === 'success' ? 'success' : 'error' ?>">
                    <i class="fas fa-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/sales/2fa/verify" id="2faForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <input type="hidden" name="code" id="codeInput" value="">

                <div class="form-group">
                    <div class="code-input-wrapper" onclick="document.getElementById('codeInput').focus()">
                        <input type="text" class="code-input" maxlength="1" data-index="0" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" class="code-input" maxlength="1" data-index="1" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" class="code-input" maxlength="1" data-index="2" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" class="code-input" maxlength="1" data-index="3" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" class="code-input" maxlength="1" data-index="4" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" class="code-input" maxlength="1" data-index="5" inputmode="numeric" pattern="[0-9]*">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-check"></i>
                    Verifieer
                </button>
            </form>

            <div class="resend-section">
                <p>Geen code ontvangen?</p>
                <a href="/sales/2fa/resend" class="resend-link">
                    <i class="fas fa-redo"></i>
                    Nieuwe code versturen
                </a>
            </div>
        </div>

        <a href="/sales/login" class="back-link">
            <i class="fas fa-arrow-left"></i> Terug naar inloggen
        </a>
    </div>

    <script>
        const inputs = document.querySelectorAll('.code-input');
        const hiddenInput = document.getElementById('codeInput');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('2faForm');

        function updateHiddenInput() {
            let code = '';
            inputs.forEach(input => {
                code += input.value;
            });
            hiddenInput.value = code;
            submitBtn.disabled = code.length !== 6;
        }

        function updateFilledState() {
            inputs.forEach(input => {
                if (input.value) {
                    input.classList.add('filled');
                } else {
                    input.classList.remove('filled');
                }
            });
        }

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = value.slice(-1);

                if (value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

                updateHiddenInput();
                updateFilledState();

                // Auto-submit when complete
                if (hiddenInput.value.length === 6) {
                    setTimeout(() => form.submit(), 100);
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const digits = paste.replace(/[^0-9]/g, '').slice(0, 6);

                digits.split('').forEach((digit, i) => {
                    if (inputs[i]) {
                        inputs[i].value = digit;
                    }
                });

                updateHiddenInput();
                updateFilledState();

                if (digits.length === 6) {
                    inputs[5].focus();
                    setTimeout(() => form.submit(), 100);
                } else if (inputs[digits.length]) {
                    inputs[digits.length].focus();
                }
            });

            input.addEventListener('focus', () => {
                input.select();
            });
        });

        // Focus first input on load
        inputs[0].focus();
    </script>
</body>
</html>

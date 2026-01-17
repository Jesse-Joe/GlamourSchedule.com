<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registratie Voltooien - GlamourSchedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: #ffffff;
            padding: 1rem;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem 0;
        }
        .register-card {
            background: #f5f5f5;
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-header .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #333333, #000000);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .register-header .icon i {
            font-size: 2.5rem;
            color: #ffffff;
        }
        .register-header h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #333333;
        }
        .register-header p {
            margin: 0.5rem 0 0;
            color: #999999;
        }
        .business-name {
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-top: 1rem;
            color: #000000;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .section-title {
            color: #333333;
            font-size: 1rem;
            font-weight: 600;
            margin: 1.5rem 0 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333333;
            font-size: 0.95rem;
        }
        .form-label i {
            color: #333333;
        }
        .form-label .required {
            color: #333333;
        }
        .form-label .optional {
            color: #666666;
            font-size: 0.8rem;
            font-weight: 400;
        }
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            background: #ffffff;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            font-size: 1rem;
            color: #333333;
            transition: all 0.2s;
        }
        .form-control::placeholder {
            color: #666666;
        }
        .form-control:focus {
            outline: none;
            border-color: #333333;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }
        .form-control.error {
            border-color: #333333;
        }
        .error-text {
            color: #d4d4d4;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .grid-3-1 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 480px) {
            .grid-2, .grid-3-1 { grid-template-columns: 1fr; }
        }
        .alert-error {
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid #333333;
            color: #d4d4d4;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, #333333, #000000);
            color: #ffffff !important;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 2rem;
        }
        .btn-primary i {
            color: #ffffff !important;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .password-strength {
            height: 4px;
            background: rgba(0,0,0,0.1);
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        .password-strength .bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
        }
        .password-hint {
            font-size: 0.8rem;
            color: #666666;
            margin-top: 0.25rem;
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
            color: #666666;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-toggle:hover {
            color: #333333;
        }
        .password-wrapper .form-control {
            padding-right: 44px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-card">
            <div class="register-header">
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
                <h1>Registratie Voltooien</h1>
                <p>Vul je gegevens aan om je account te activeren</p>

                <div class="business-name">
                    <i class="fas fa-building"></i>
                    <?= htmlspecialchars($business['company_name'] ?? '') ?>
                </div>
            </div>

            <?php if (!empty($errors['general'])): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['general']) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/partner/complete/<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <!-- Password Section -->
                <div class="section-title">
                    <i class="fas fa-lock"></i> Wachtwoord Instellen
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-key"></i> Wachtwoord <span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password"
                                   class="form-control <?= isset($errors['password']) ? 'error' : '' ?>"
                                   placeholder="Min. 8 tekens" required minlength="8">
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength"><div class="bar" id="strengthBar"></div></div>
                        <div class="password-hint">Minimaal 8 tekens</div>
                        <?php if (isset($errors['password'])): ?>
                            <p class="error-text"><?= htmlspecialchars($errors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-key"></i> Bevestig Wachtwoord <span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirm" id="password_confirm"
                                   class="form-control <?= isset($errors['password_confirm']) ? 'error' : '' ?>"
                                   placeholder="Herhaal wachtwoord" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirm', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['password_confirm'])): ?>
                            <p class="error-text"><?= htmlspecialchars($errors['password_confirm']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Address Section -->
                <div class="section-title">
                    <i class="fas fa-map-marker-alt"></i> Bedrijfsadres
                </div>

                <div class="grid-3-1">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-road"></i> Straat <span class="required">*</span>
                        </label>
                        <input type="text" name="street"
                               class="form-control <?= isset($errors['street']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($data['street'] ?? '') ?>"
                               placeholder="Straatnaam" required>
                        <?php if (isset($errors['street'])): ?>
                            <p class="error-text"><?= htmlspecialchars($errors['street']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-hashtag"></i> Nr <span class="required">*</span>
                        </label>
                        <input type="text" name="house_number"
                               class="form-control <?= isset($errors['house_number']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($data['house_number'] ?? '') ?>"
                               placeholder="12A" required>
                        <?php if (isset($errors['house_number'])): ?>
                            <p class="error-text"><?= htmlspecialchars($errors['house_number']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-mail-bulk"></i> Postcode <span class="required">*</span>
                        </label>
                        <input type="text" name="postal_code"
                               class="form-control <?= isset($errors['postal_code']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($data['postal_code'] ?? '') ?>"
                               placeholder="1234 AB" required>
                        <?php if (isset($errors['postal_code'])): ?>
                            <p class="error-text"><?= htmlspecialchars($errors['postal_code']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-city"></i> Plaats <span class="required">*</span>
                        </label>
                        <input type="text" name="city"
                               class="form-control <?= isset($errors['city']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($data['city'] ?? '') ?>"
                               placeholder="Amsterdam" required>
                        <?php if (isset($errors['city'])): ?>
                            <p class="error-text"><?= htmlspecialchars($errors['city']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Optional Info Section -->
                <div class="section-title">
                    <i class="fas fa-info-circle"></i> Aanvullende Gegevens <span class="optional">(optioneel)</span>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i> Telefoonnummer <span class="optional">(optioneel)</span>
                    </label>
                    <input type="tel" name="phone" class="form-control"
                           value="<?= htmlspecialchars($data['phone'] ?? '') ?>"
                           placeholder="06 12345678">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-building"></i> KvK-nummer <span class="optional">(optioneel)</span>
                        </label>
                        <input type="text" name="kvk_number" class="form-control"
                               value="<?= htmlspecialchars($data['kvk_number'] ?? '') ?>"
                               placeholder="12345678">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-receipt"></i> BTW-nummer <span class="optional">(optioneel)</span>
                        </label>
                        <input type="text" name="btw_number" class="form-control"
                               value="<?= htmlspecialchars($data['btw_number'] ?? '') ?>"
                               placeholder="NL123456789B01">
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-check-circle"></i> Account Activeren
                </button>
            </form>
        </div>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function(e) {
            const bar = document.getElementById('strengthBar');
            const len = e.target.value.length;
            let width = 0;
            let color = '#333333';

            if (len >= 8) { width = 33; color = '#000000'; }
            if (len >= 10) { width = 66; color = '#eab308'; }
            if (len >= 12 && /[A-Z]/.test(e.target.value) && /[0-9]/.test(e.target.value)) {
                width = 100; color = '#333333';
            }

            bar.style.width = width + '%';
            bar.style.background = color;
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
</body>
</html>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuw Wachtwoord - Sales Portal</title>
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
        .card {
            background: #ffffff;
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        }
        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .card-header i {
            font-size: 3rem;
            color: #333333;
            margin-bottom: 1rem;
        }
        .card-header h1 {
            margin: 0;
            font-size: 1.75rem;
            color: #1f2937;
        }
        .card-header p {
            margin: 0.5rem 0 0 0;
            color: #6b7280;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #333333;
        }
        .btn-primary {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #333333, #000000);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        .back-link a {
            color: #333333;
            text-decoration: none;
            font-weight: 500;
        }
        .code-input {
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            font-family: monospace;
        }
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
            color: #9ca3af;
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
    <div class="card">
        <div class="card-header">
            <i class="fas fa-lock"></i>
            <h1>Nieuw Wachtwoord</h1>
            <p>Voer de code in en kies een nieuw wachtwoord</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/sales/reset-password">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

            <div class="form-group">
                <label class="form-label">Reset Code</label>
                <input type="text" name="code" class="form-control code-input" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Nieuw Wachtwoord</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Minimaal 8 karakters" minlength="8" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Wachtwoord tonen">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Bevestig Wachtwoord</label>
                <div class="password-wrapper">
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Herhaal wachtwoord" minlength="8" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirm', this)" aria-label="Wachtwoord tonen">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Wachtwoord Opslaan
            </button>
        </form>

        <div class="back-link">
            <a href="/sales/login"><i class="fas fa-arrow-left"></i> Terug naar inloggen</a>
        </div>
    </div>

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
    </script>
</body>
</html>

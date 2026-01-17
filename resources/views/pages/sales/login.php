<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Login - GlamourSchedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0a0a;
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
        .login-card {
            background: #1a1a1a;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #333;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #a1a1a1;
            font-size: 0.875rem;
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
        .btn-primary {
            width: 100%;
            padding: 0.875rem 1rem;
            background: #fff;
            color: #000;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-primary:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
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
            color: #666;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-toggle:hover {
            color: #fff;
        }
        .password-wrapper .form-control {
            padding-right: 44px;
        }
        .forgot-password-link {
            text-align: center;
            margin-top: 1.25rem;
        }
        .forgot-password-link a {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .forgot-password-link a:hover {
            color: #fff;
        }
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #333;
        }
        .register-link p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .register-link a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #0a0a0a;
            border: 1px solid #333;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .register-link a:hover {
            border-color: #fff;
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

        <div class="login-card">
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] === 'success' ? 'success' : 'error' ?>">
                    <i class="fas fa-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <form method="POST" action="/sales/login">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">

                <div class="form-group">
                    <label class="form-label">E-mailadres</label>
                    <input type="email" name="email" class="form-control" placeholder="jouw@email.nl" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Wachtwoord</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Je wachtwoord" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Wachtwoord tonen">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Inloggen
                </button>
            </form>

            <div class="forgot-password-link">
                <a href="/sales/forgot-password"><i class="fas fa-key"></i> Wachtwoord vergeten?</a>
            </div>

            <div class="register-link">
                <p>Nog geen account?</p>
                <a href="/sales/register"><i class="fas fa-user-plus"></i> Word Sales Partner</a>
            </div>
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

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Login - GlamourSchedule</title>
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
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header i {
            font-size: 3rem;
            color: #333333;
            margin-bottom: 1rem;
        }
        .login-header h1 {
            margin: 0;
            font-size: 1.75rem;
            color: #1f2937;
        }
        .login-header p {
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
            background: #f5f5f5;
            color: #000000;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        .register-link a {
            color: #333333;
            text-decoration: none;
            font-weight: 500;
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
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-chart-line"></i>
            <h1>Sales Portal</h1>
            <p>Log in op je sales dashboard</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash'])): ?>
            <div style="background:#ffffff;color:#000000;padding:1rem;border-radius:10px;margin-bottom:1.5rem">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash']['message']) ?>
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

        <div class="register-link">
            <p style="margin:0;color:#6b7280">Nog geen account?</p>
            <a href="/sales/register">Word Sales Partner</a>
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

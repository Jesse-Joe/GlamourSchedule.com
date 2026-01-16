<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord Vergeten - Sales Portal</title>
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
        .alert-success {
            background: #f0fdf4;
            color: #166534;
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
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-key"></i>
            <h1>Wachtwoord Vergeten</h1>
            <p>Ontvang een reset code via e-mail</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
            <div style="text-align:center;margin-bottom:1.5rem">
                <a href="/sales/reset-password" class="btn-primary" style="display:inline-block;padding:1rem 2rem;text-decoration:none;border-radius:10px">
                    <i class="fas fa-arrow-right"></i> Ga naar reset pagina
                </a>
            </div>
        <?php endif; ?>

        <form method="POST" action="/sales/forgot-password">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">

            <div class="form-group">
                <label class="form-label">E-mailadres</label>
                <input type="email" name="email" class="form-control" placeholder="jouw@email.nl" required autofocus value="<?= htmlspecialchars($email ?? '') ?>">
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane"></i> Verstuur Reset Code
            </button>
        </form>

        <div class="back-link">
            <a href="/sales/login"><i class="fas fa-arrow-left"></i> Terug naar inloggen</a>
        </div>
    </div>
</body>
</html>

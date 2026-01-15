<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registratie Voltooid - GlamourSchedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 1rem;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .success-card {
            background: #f5f5f5;
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: rgba(0, 0, 0, 0.1);
            border: 2px solid #333333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: celebrate 0.5s ease-out;
        }
        @keyframes celebrate {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .success-icon i {
            font-size: 3rem;
            color: #333333;
        }
        h1 {
            color: #000000;
            margin: 0 0 1rem;
            font-size: 1.75rem;
        }
        p {
            color: #999999;
            line-height: 1.6;
            margin: 0 0 1.5rem;
        }
        .business-name {
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin: 1.5rem 0;
            color: #000000;
            font-weight: 600;
        }
        .trial-box {
            background: #000000;
            border: 2px solid #333333;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .trial-box h3 {
            color: #000000;
            margin: 0 0 0.5rem;
            font-size: 1.1rem;
        }
        .trial-box p {
            color: #333333;
            margin: 0;
            font-size: 0.95rem;
        }
        .next-steps {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 1.25rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .next-steps h4 {
            color: #333333;
            margin: 0 0 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .step {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            color: #999999;
        }
        .step:last-child {
            margin-bottom: 0;
        }
        .step-num {
            width: 24px;
            height: 24px;
            background: #333333;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #333333, #000000);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            top: -10px;
            animation: fall 3s linear forwards;
        }
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1>Gefeliciteerd!</h1>
        <p>Je account is succesvol geactiveerd. Je kunt nu inloggen en aan de slag!</p>

        <div class="business-name">
            <i class="fas fa-store"></i> <?= htmlspecialchars($business['company_name'] ?? '') ?>
        </div>

        <div class="trial-box">
            <h3><i class="fas fa-gift"></i> 14 Dagen Proefperiode</h3>
            <p>Je proefperiode is gestart. Ontdek alle mogelijkheden van GlamourSchedule!</p>
        </div>

        <div class="next-steps">
            <h4><i class="fas fa-rocket"></i> Aan de slag</h4>
            <div class="step">
                <span class="step-num">1</span>
                <span>Voeg je diensten en prijzen toe</span>
            </div>
            <div class="step">
                <span class="step-num">2</span>
                <span>Stel je openingstijden in</span>
            </div>
            <div class="step">
                <span class="step-num">3</span>
                <span>Personaliseer je salonpagina</span>
            </div>
            <div class="step">
                <span class="step-num">4</span>
                <span>Deel je boekingslink met klanten</span>
            </div>
        </div>

        <a href="/login" class="btn-primary">
            <i class="fas fa-sign-in-alt"></i> Nu Inloggen
        </a>
    </div>

    <script>
        // Confetti effect
        const colors = ['#333333', '#000000', '#000000', '#333333', '#737373'];
        for (let i = 0; i < 50; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 3000);
            }, i * 50);
        }
    </script>
</body>
</html>

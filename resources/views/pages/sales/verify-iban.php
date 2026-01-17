<?php ob_start(); ?>

<div class="container" style="max-width:600px">
    <div class="card text-center">
        <div style="width:80px;height:80px;background:linear-gradient(135deg,#fafafa,#f5f5f5);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;border:2px solid #000000">
            <i class="fas fa-university" style="font-size:2rem;color:#000000"></i>
        </div>
        <h2>Bankrekening Verificatie</h2>
        <p style="color:var(--text-light);margin-bottom:1.5rem">
            Verifieer je bankrekening via een eenmalige betaling van <strong>€0,01</strong>.
            Je IBAN wordt automatisch gekoppeld aan je account.
        </p>
    </div>

    <div class="card">
        <h3><i class="fas fa-info-circle"></i> Hoe werkt het?</h3>
        <ol style="color:var(--text-light);line-height:1.8;padding-left:1.25rem">
            <li>Klik op "Start Verificatie"</li>
            <li>Betaal €0,01 via iDEAL</li>
            <li>Je IBAN wordt automatisch gekoppeld</li>
            <li>Uitbetalingen gaan naar dit rekeningnummer</li>
        </ol>

        <div style="background:#fafafa;border-left:4px solid #000000;padding:1rem;border-radius:0 8px 8px 0;margin:1.5rem 0">
            <p style="margin:0;color:#333;font-size:0.9rem">
                <strong>Let op:</strong> De €0,01 wordt niet teruggestort. Dit is nodig om je bankgegevens veilig te verifiëren.
            </p>
        </div>

        <form method="POST" action="/sales/verify-iban">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <button type="submit" class="btn" style="width:100%">
                <i class="fas fa-lock"></i> Start Verificatie - €0,01
            </button>
        </form>

        <p style="text-align:center;margin-top:1rem">
            <a href="/sales/account" style="color:var(--text-light);text-decoration:none">
                <i class="fas fa-arrow-left"></i> Terug naar account
            </a>
        </p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/sales.php'; ?>

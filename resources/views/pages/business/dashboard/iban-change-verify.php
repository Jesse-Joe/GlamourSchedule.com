<?php ob_start(); ?>

<div style="max-width:500px;margin:0 auto">
    <div class="card">
        <div class="card-header" style="text-align:center">
            <h3 class="card-title"><i class="fas fa-shield-alt"></i> IBAN Wijzigen</h3>
        </div>

        <div style="background:#1a1a1a;border:1px solid #f59e0b;border-radius:10px;padding:1rem;margin-bottom:1.5rem;text-align:center">
            <i class="fas fa-exclamation-triangle" style="font-size:2rem;color:#f59e0b;margin-bottom:0.5rem"></i>
            <p style="margin:0;color:#ffffff">
                Voor je veiligheid hebben we een verificatiecode gestuurd naar:<br>
                <strong><?= htmlspecialchars($email) ?></strong>
            </p>
        </div>

        <form method="POST" action="/business/iban/verify-change">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="form-group">
                <label class="form-label">Verificatiecode</label>
                <input type="text" name="code" class="form-control"
                       placeholder="000000"
                       maxlength="6" minlength="6"
                       pattern="[0-9]{6}"
                       style="font-size:2rem;text-align:center;letter-spacing:0.5rem;font-family:monospace"
                       autocomplete="off" required autofocus>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error" style="margin-bottom:1rem">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:1rem">
                <i class="fas fa-check"></i> Verifieer & Wijzig IBAN
            </button>
        </form>

        <div style="margin-top:1.5rem;text-align:center">
            <p class="text-muted" style="font-size:0.85rem">Geen code ontvangen?</p>
            <form method="POST" action="/business/iban/resend-change-code" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fas fa-redo"></i> Code Opnieuw Versturen
                </button>
            </form>
        </div>

        <hr style="margin:1.5rem 0">

        <a href="/business/profile" class="btn btn-secondary" style="width:100%">
            <i class="fas fa-arrow-left"></i> Annuleren
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

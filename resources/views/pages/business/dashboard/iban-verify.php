<?php ob_start(); ?>

<div style="max-width:500px;margin:0 auto">
    <div class="card">
        <div class="card-header" style="text-align:center">
            <h3 class="card-title"><i class="fas fa-shield-alt"></i> IBAN Verificatie</h3>
        </div>

        <!-- Progress Steps -->
        <div style="display:flex;gap:0.5rem;margin-bottom:1.5rem">
            <div style="flex:1;text-align:center;padding:0.75rem;background:var(--b-bg-card);border-radius:8px">
                <i class="fas fa-check-circle" style="color:var(--b-text-muted)"></i>
                <p style="margin:0.25rem 0 0 0;font-size:0.75rem;color:var(--b-text)">IBAN</p>
            </div>
            <div style="flex:1;text-align:center;padding:0.75rem;background:linear-gradient(135deg,#333333,#111111);border-radius:8px;color:white">
                <i class="fas fa-envelope"></i>
                <p style="margin:0.25rem 0 0 0;font-size:0.75rem">2FA Code</p>
            </div>
            <div style="flex:1;text-align:center;padding:0.75rem;background:var(--b-bg-surface);border-radius:8px">
                <i class="fas fa-credit-card" style="color:var(--b-text-muted)"></i>
                <p style="margin:0.25rem 0 0 0;font-size:0.75rem;color:var(--b-text-muted)">Betaling</p>
            </div>
        </div>

        <div style="background:var(--b-bg-card);border-radius:10px;padding:1rem;margin-bottom:1.5rem;text-align:center">
            <i class="fas fa-envelope" style="font-size:2rem;color:var(--b-text);margin-bottom:0.5rem"></i>
            <p style="margin:0;color:var(--b-text)">
                We hebben een 6-cijferige code gestuurd naar:<br>
                <strong><?= htmlspecialchars($email) ?></strong>
            </p>
        </div>

        <form method="POST" action="/business/iban/verify">
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
                <i class="fas fa-arrow-right"></i> Doorgaan naar Betaling
            </button>
        </form>

        <div style="margin-top:1.5rem;text-align:center">
            <p class="text-muted" style="font-size:0.85rem">Geen code ontvangen?</p>
            <form method="POST" action="/business/iban/resend" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fas fa-redo"></i> Code Opnieuw Versturen
                </button>
            </form>
        </div>

        <hr style="margin:1.5rem 0">

        <div style="background:var(--b-bg-surface);border-radius:10px;padding:1rem">
            <p style="margin:0;font-size:0.85rem;color:var(--b-text-muted)">
                <strong>IBAN:</strong> <?= htmlspecialchars($iban) ?><br>
                <strong>Naam:</strong> <?= htmlspecialchars($accountHolder) ?>
            </p>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

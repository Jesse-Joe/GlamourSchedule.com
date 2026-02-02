<?php ob_start(); ?>

<div style="max-width:500px;margin:0 auto">
    <div class="card">
        <div class="card-header" style="text-align:center">
            <h3 class="card-title"><i class="fas fa-university"></i> IBAN Invoeren</h3>
        </div>

        <div style="background:var(--success);background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-radius:10px;padding:1rem;margin-bottom:1.5rem;text-align:center">
            <i class="fas fa-check-circle" style="font-size:2rem;color:#22c55e;margin-bottom:0.5rem"></i>
            <p style="margin:0;color:#22c55e">
                Verificatie gelukt! Voer nu je IBAN in.
            </p>
        </div>

        <form method="POST" action="/business/iban/save">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="form-group">
                <label class="form-label">Naam rekeninghouder *</label>
                <input type="text" name="account_holder" class="form-control"
                       placeholder="Naam zoals op je bankrekening"
                       value="<?= htmlspecialchars($account_holder ?? $business['company_name'] ?? '') ?>"
                       required>
                <small class="text-muted">Dit moet exact overeenkomen met je bankgegevens</small>
            </div>

            <div class="form-group">
                <label class="form-label">IBAN *</label>
                <input type="text" name="iban" class="form-control"
                       placeholder="NL00 BANK 0000 0000 00"
                       value="<?= htmlspecialchars($iban ?? '') ?>"
                       style="font-family:monospace;font-size:1.1rem;text-transform:uppercase"
                       maxlength="34"
                       autocomplete="off" required>
                <small class="text-muted">Bijv: NL91 ABNA 0417 1643 00</small>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error" style="margin-bottom:1rem">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div style="background:var(--secondary);border-radius:10px;padding:1rem;margin-bottom:1.5rem">
                <p style="margin:0;font-size:0.9rem;color:var(--text-muted)">
                    <i class="fas fa-info-circle"></i>
                    <strong>Let op:</strong> Je IBAN kan maximaal 1x per 30 dagen worden gewijzigd.
                    Controleer je gegevens zorgvuldig.
                </p>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:1rem">
                <i class="fas fa-save"></i> IBAN Opslaan
            </button>
        </form>

        <hr style="margin:1.5rem 0">

        <a href="/business/payouts" class="btn btn-secondary" style="width:100%">
            <i class="fas fa-arrow-left"></i> Annuleren
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

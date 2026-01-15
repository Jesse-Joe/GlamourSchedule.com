<?php ob_start(); ?>

<!-- Pending Balance -->
<div style="background:linear-gradient(135deg,#000000,#404040);border-radius:16px;padding:2rem;color:white;margin-bottom:2rem;display:flex;justify-content:space-between;align-items:center">
    <div>
        <h3 style="margin:0 0 0.5rem 0"><i class="fas fa-wallet"></i> Uitbetaling in afwachting</h3>
        <p style="margin:0;opacity:0.9">Dit bedrag wordt uitbetaald bij de volgende uitbetalingsronde</p>
    </div>
    <div style="text-align:right">
        <p style="margin:0;font-size:2.5rem;font-weight:bold">&euro;<?= number_format($pendingAmount, 2, ',', '.') ?></p>
    </div>
</div>

<!-- Payout Info -->
<div class="card" style="background:#ffffff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:2rem">
    <h3 style="margin:0 0 1rem 0"><i class="fas fa-info-circle"></i> Uitbetalingsinformatie</h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem">
        <div style="background:#ffffff;padding:1rem;border-radius:10px">
            <h4 style="margin:0;color:#000000">Minimum uitbetaling</h4>
            <p style="margin:0.5rem 0 0 0;font-size:1.25rem;font-weight:bold;color:#333333">&euro;10,00</p>
        </div>
        <div style="background:#ffffff;padding:1rem;border-radius:10px">
            <h4 style="margin:0;color:#000000">Uitbetalingsdag</h4>
            <p style="margin:0.5rem 0 0 0;font-size:1.25rem;font-weight:bold;color:#404040">15e van de maand</p>
        </div>
        <div style="background:#ffffff;padding:1rem;border-radius:10px">
            <h4 style="margin:0;color:#000000">Betaalmethode</h4>
            <p style="margin:0.5rem 0 0 0;font-size:1.25rem;font-weight:bold;color:#000000">Bankoverschrijving</p>
        </div>
    </div>
</div>

<!-- Payout History -->
<div class="card" style="background:#ffffff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
    <h3 style="margin:0 0 1.5rem 0"><i class="fas fa-history"></i> Uitbetalingsgeschiedenis</h3>

    <?php if (empty($payouts)): ?>
        <p style="color:#6b7280;text-align:center;padding:3rem 0">
            <i class="fas fa-euro-sign" style="font-size:3rem;opacity:0.3;display:block;margin-bottom:1rem"></i>
            Nog geen uitbetalingen.<br>Bereik het minimum van &euro;10,00 om je eerste uitbetaling te ontvangen.
        </p>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="border-bottom:2px solid #e5e7eb">
                    <th style="text-align:left;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Datum</th>
                    <th style="text-align:left;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Referentie</th>
                    <th style="text-align:center;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Status</th>
                    <th style="text-align:right;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Bedrag</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payouts as $payout): ?>
                    <tr style="border-bottom:1px solid #e5e7eb">
                        <td style="padding:1rem 0.5rem">
                            <?= date('d-m-Y', strtotime($payout['created_at'])) ?>
                        </td>
                        <td style="padding:1rem 0.5rem;color:#6b7280;font-family:monospace">
                            <?= htmlspecialchars($payout['reference'] ?? '-') ?>
                        </td>
                        <td style="padding:1rem 0.5rem;text-align:center">
                            <span style="background:#33333320;color:#333333;padding:0.35rem 1rem;border-radius:20px;font-size:0.85rem">
                                Uitbetaald
                            </span>
                        </td>
                        <td style="padding:1rem 0.5rem;text-align:right;font-weight:600;font-size:1.1rem;color:#333333">
                            &euro;<?= number_format($payout['amount'], 2, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/sales.php'; ?>

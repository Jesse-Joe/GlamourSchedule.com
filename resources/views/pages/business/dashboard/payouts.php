<?php ob_start(); ?>

<div class="grid grid-2">
    <!-- Pending Balance -->
    <div class="card" style="background:linear-gradient(135deg,var(--success),#000000);color:white">
        <div style="display:flex;align-items:center;gap:1rem">
            <div style="width:60px;height:60px;background:rgba(255,255,255,0.2);border-radius:15px;display:flex;align-items:center;justify-content:center">
                <i class="fas fa-wallet" style="font-size:1.5rem"></i>
            </div>
            <div>
                <p style="opacity:0.9;margin:0">In afwachting van uitbetaling</p>
                <h2 style="margin:0.25rem 0 0 0;font-size:2rem">&euro;<?= number_format($pendingAmount ?? 0, 2, ',', '.') ?></h2>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card">
        <div style="display:flex;align-items:center;gap:1rem">
            <div style="width:60px;height:60px;background:var(--secondary);border-radius:15px;display:flex;align-items:center;justify-content:center">
                <i class="fas fa-info-circle" style="font-size:1.5rem;color:var(--primary)"></i>
            </div>
            <div>
                <h4 style="margin:0">Uitbetalingen</h4>
                <p class="text-muted" style="margin:0.25rem 0 0 0;font-size:0.9rem">Uitbetalingen worden elke 14 dagen verwerkt naar je IBAN</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <!-- Payout History -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history"></i> Uitbetalingsgeschiedenis</h3>
        </div>

        <?php if (empty($payouts)): ?>
            <div class="text-center" style="padding:3rem">
                <i class="fas fa-money-bill-wave" style="font-size:4rem;color:var(--border);margin-bottom:1rem"></i>
                <h4>Nog geen uitbetalingen</h4>
                <p class="text-muted">Je uitbetalingsgeschiedenis verschijnt hier.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border)">
                            <th style="text-align:left;padding:0.75rem 0;font-weight:600">Datum</th>
                            <th style="text-align:left;padding:0.75rem 0;font-weight:600">Bedrag</th>
                            <th style="text-align:left;padding:0.75rem 0;font-weight:600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payouts as $payout): ?>
                            <tr style="border-bottom:1px solid var(--border)">
                                <td style="padding:0.75rem 0">
                                    <?= !empty($payout['created_at']) ? date('d-m-Y', strtotime($payout['created_at'])) : '-' ?>
                                </td>
                                <td style="padding:0.75rem 0;font-weight:600">
                                    &euro;<?= number_format($payout['amount'] ?? 0, 2, ',', '.') ?>
                                </td>
                                <td style="padding:0.75rem 0">
                                    <?php
                                    $status = $payout['status'] ?? 'pending';
                                    $statusColors = ['pending' => 'var(--warning)', 'completed' => 'var(--success)', 'failed' => 'var(--danger)'];
                                    $statusLabels = ['pending' => 'In verwerking', 'completed' => 'Voltooid', 'failed' => 'Mislukt'];
                                    ?>
                                    <span style="background:<?= $statusColors[$status] ?? 'var(--warning)' ?>;color:white;padding:0.25rem 0.75rem;border-radius:15px;font-size:0.75rem">
                                        <?= $statusLabels[$status] ?? 'Onbekend' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Payout Info -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-university"></i> Bankgegevens</h3>
            </div>

            <div style="padding:1rem;background:var(--secondary);border-radius:10px">
                <p class="text-muted" style="font-size:0.85rem;margin:0">IBAN</p>
                <p style="font-family:monospace;font-size:1.1rem;margin:0.25rem 0 0 0">
                    <?= htmlspecialchars($business['iban'] ?? 'Niet opgegeven') ?>
                </p>
            </div>

            <?php if (empty($business['iban'])): ?>
                <div class="alert alert-warning" style="margin-top:1rem">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Voeg je IBAN toe in je profiel om uitbetalingen te ontvangen.</span>
                </div>
                <a href="/business/profile" class="btn btn-primary" style="margin-top:0.5rem">
                    <i class="fas fa-edit"></i> IBAN Toevoegen
                </a>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calculator"></i> Hoe worden uitbetalingen berekend?</h3>
            </div>

            <ul style="padding-left:1.25rem;color:var(--text-light);line-height:2;font-size:0.9rem">
                <li>Boekingsbedrag - &euro;1,75 administratiekosten = jouw verdiensten</li>
                <li>Uitbetalingen worden elke 14 dagen verwerkt</li>
                <li>Minimaal uitbetalingsbedrag: &euro;25,00</li>
                <li>Uitbetaling binnen 2-3 werkdagen na verwerking</li>
            </ul>
        </div>

        <div class="card" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white">
            <h4 style="margin-bottom:0.5rem"><i class="fas fa-question-circle"></i> Vragen?</h4>
            <p style="font-size:0.9rem;opacity:0.9">Neem contact op met onze klantenservice als je vragen hebt over uitbetalingen.</p>
            <a href="/contact" class="btn" style="background:#ffffff;color:var(--primary);margin-top:0.5rem">
                <i class="fas fa-envelope"></i> Contact
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

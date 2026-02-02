<?php ob_start(); ?>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success" style="margin-bottom:1.5rem;">
    <i class="fas fa-check-circle"></i> Uitbetaling is gemarkeerd als voltooid.
</div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card warning">
        <div class="stat-label">Te Betalen (Bedrijven)</div>
        <div class="stat-value">&euro;<?= number_format($totalBusinessAmount, 2, ',', '.') ?></div>
        <div class="stat-sub"><?= count($businessPayouts) ?> bedrijf/bedrijven</div>
    </div>
    <div class="stat-card info">
        <div class="stat-label">Te Betalen (Sales)</div>
        <div class="stat-value">&euro;<?= number_format($totalSalesAmount, 2, ',', '.') ?></div>
        <div class="stat-sub"><?= count($salesPayouts) ?> partner(s)</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-label">Totaal Handmatig</div>
        <div class="stat-value">&euro;<?= number_format($totalAmount, 2, ',', '.') ?></div>
        <div class="stat-sub">Over te maken</div>
    </div>
    <div class="stat-card primary">
        <div class="stat-label">Wise Saldo</div>
        <div class="stat-value" id="wise-balance">Laden...</div>
        <div class="stat-sub">EUR balans</div>
    </div>
</div>

<!-- Business Payouts -->
<div class="card" style="margin-top:1.5rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-store"></i> Bedrijfsuitbetalingen</h3>
        <span class="badge badge-warning"><?= count($businessPayouts) ?> openstaand</span>
    </div>

    <?php if (empty($businessPayouts)): ?>
    <div style="padding:3rem;text-align:center;color:var(--text-light);">
        <i class="fas fa-check-circle" style="font-size:3rem;color:var(--success);margin-bottom:1rem;"></i>
        <p>Geen openstaande bedrijfsuitbetalingen</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Bedrijf</th>
                    <th>IBAN</th>
                    <th>Boekingen</th>
                    <th style="text-align:right;">Bedrag</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($businessPayouts as $payout): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($payout['company_name']) ?></strong>
                        <br><small style="color:var(--text-light);"><?= htmlspecialchars($payout['email']) ?></small>
                    </td>
                    <td>
                        <code style="background:var(--bg);padding:0.25rem 0.5rem;border-radius:4px;font-size:0.85rem;">
                            <?= htmlspecialchars($payout['iban'] ?? 'Geen IBAN') ?>
                        </code>
                        <?php if ($payout['iban']): ?>
                        <button onclick="copyToClipboard('<?= htmlspecialchars($payout['iban']) ?>')" class="btn btn-sm" style="padding:0.2rem 0.4rem;margin-left:0.5rem;" title="Kopieer IBAN">
                            <i class="fas fa-copy"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-secondary"><?= count($payout['bookings']) ?></span>
                        <button onclick="toggleDetails('biz-<?= $payout['business_id'] ?>')" class="btn btn-sm" style="padding:0.2rem 0.4rem;margin-left:0.25rem;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                    <td style="text-align:right;">
                        <strong style="color:var(--warning);font-size:1.1rem;">
                            &euro;<?= number_format($payout['total_amount'], 2, ',', '.') ?>
                        </strong>
                        <button onclick="copyToClipboard('<?= number_format($payout['total_amount'], 2, '.', '') ?>')" class="btn btn-sm" style="padding:0.2rem 0.4rem;margin-left:0.5rem;" title="Kopieer bedrag">
                            <i class="fas fa-copy"></i>
                        </button>
                    </td>
                    <td>
                        <form method="POST" action="/admin/payouts/mark/business/<?= $payout['business_id'] ?>" style="display:inline;" onsubmit="return confirm('Weet je zeker dat je deze uitbetaling als voltooid wilt markeren?');">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> Voltooid
                            </button>
                        </form>
                    </td>
                </tr>
                <tr id="biz-<?= $payout['business_id'] ?>" style="display:none;">
                    <td colspan="5" style="background:var(--bg);padding:1rem;">
                        <table style="width:100%;font-size:0.9rem;">
                            <thead>
                                <tr>
                                    <th>Boeking</th>
                                    <th>Service</th>
                                    <th>Klant</th>
                                    <th>Check-in</th>
                                    <th style="text-align:right;">Bedrag</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payout['bookings'] as $booking): ?>
                                <tr>
                                    <td><?= htmlspecialchars($booking['booking_number']) ?></td>
                                    <td><?= htmlspecialchars($booking['service_name']) ?></td>
                                    <td><?= htmlspecialchars($booking['customer_name'] ?? 'Gast') ?></td>
                                    <td><?= $booking['checked_in_at'] ? date('d-m-Y H:i', strtotime($booking['checked_in_at'])) : '-' ?></td>
                                    <td style="text-align:right;">&euro;<?= number_format($booking['business_payout'] ?? ($booking['service_price'] - 1.75), 2, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Copy all for bank transfer -->
    <div style="padding:1rem;background:var(--bg);border-top:1px solid var(--border);">
        <h4 style="margin-bottom:1rem;"><i class="fas fa-clipboard-list"></i> Snel Kopieren voor Bankoverboeking</h4>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:1rem;">
            <?php foreach ($businessPayouts as $payout): ?>
            <?php if ($payout['iban']): ?>
            <div style="background:var(--card-bg);padding:1rem;border-radius:8px;border:1px solid var(--border);">
                <div style="font-weight:600;margin-bottom:0.5rem;"><?= htmlspecialchars($payout['company_name']) ?></div>
                <div style="font-family:monospace;font-size:0.85rem;color:var(--text-light);margin-bottom:0.5rem;"><?= htmlspecialchars($payout['iban']) ?></div>
                <div style="font-size:1.2rem;font-weight:700;color:var(--warning);">&euro;<?= number_format($payout['total_amount'], 2, ',', '.') ?></div>
                <div style="font-size:0.8rem;color:var(--text-light);margin-top:0.5rem;">
                    Omschrijving: GlamourSchedule uitbetaling <?= date('d-m-Y') ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Sales Partner Payouts -->
<div class="card" style="margin-top:1.5rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-handshake"></i> Sales Partner Commissies</h3>
        <span class="badge badge-info"><?= count($salesPayouts) ?> openstaand</span>
    </div>

    <?php if (empty($salesPayouts)): ?>
    <div style="padding:3rem;text-align:center;color:var(--text-light);">
        <i class="fas fa-check-circle" style="font-size:3rem;color:var(--success);margin-bottom:1rem;"></i>
        <p>Geen openstaande sales partner uitbetalingen</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Partner</th>
                    <th>IBAN</th>
                    <th>Referrals</th>
                    <th style="text-align:right;">Commissie</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salesPayouts as $partner): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($partner['sales_name']) ?></strong>
                        <br><small style="color:var(--text-light);"><?= htmlspecialchars($partner['sales_email']) ?></small>
                    </td>
                    <td>
                        <code style="background:var(--bg);padding:0.25rem 0.5rem;border-radius:4px;font-size:0.85rem;">
                            <?= htmlspecialchars($partner['sales_iban'] ?? 'Geen IBAN') ?>
                        </code>
                        <?php if ($partner['sales_iban']): ?>
                        <button onclick="copyToClipboard('<?= htmlspecialchars($partner['sales_iban']) ?>')" class="btn btn-sm" style="padding:0.2rem 0.4rem;margin-left:0.5rem;">
                            <i class="fas fa-copy"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-secondary"><?= $partner['referral_count'] ?> salons</span></td>
                    <td style="text-align:right;">
                        <strong style="color:var(--info);font-size:1.1rem;">
                            &euro;<?= number_format($partner['total_commission'], 2, ',', '.') ?>
                        </strong>
                    </td>
                    <td>
                        <form method="POST" action="/admin/payouts/mark/sales/<?= $partner['sales_user_id'] ?>" style="display:inline;" onsubmit="return confirm('Weet je zeker dat je deze commissie als uitbetaald wilt markeren?');">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> Voltooid
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Payout Records -->
<?php if (!empty($payoutRecords)): ?>
<div class="card" style="margin-top:1.5rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history"></i> Recente Uitbetalingsrecords</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bedrijf</th>
                    <th>Bedrag</th>
                    <th>Status</th>
                    <th>Datum</th>
                    <th>Notities</th>
                    <th>Actie</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payoutRecords as $record): ?>
                <tr>
                    <td>#<?= $record['id'] ?></td>
                    <td><?= htmlspecialchars($record['company_name']) ?></td>
                    <td>&euro;<?= number_format($record['amount'], 2, ',', '.') ?></td>
                    <td>
                        <?php if ($record['status'] === 'failed'): ?>
                            <span class="badge badge-danger">Mislukt</span>
                        <?php elseif ($record['status'] === 'pending'): ?>
                            <span class="badge badge-warning">Wachtend</span>
                        <?php elseif ($record['status'] === 'processing'): ?>
                            <span class="badge badge-info">Verwerking</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d-m-Y', strtotime($record['created_at'])) ?></td>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($record['notes'] ?? '') ?>">
                        <?= htmlspecialchars($record['notes'] ?? '-') ?>
                    </td>
                    <td>
                        <form method="POST" action="/admin/payouts/mark/record/<?= $record['id'] ?>" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show brief notification
        const toast = document.createElement('div');
        toast.innerHTML = '<i class="fas fa-check"></i> Gekopieerd!';
        toast.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#22c55e;color:#fff;padding:0.75rem 1.5rem;border-radius:8px;z-index:9999;animation:fadeIn 0.3s;';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}

function toggleDetails(id) {
    const row = document.getElementById(id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}

// Load Wise balance
fetch('/api/wise/balance')
    .then(r => r.json())
    .then(data => {
        document.getElementById('wise-balance').innerHTML = data.balance
            ? '&euro;' + parseFloat(data.balance).toFixed(2).replace('.', ',')
            : 'N/A';
    })
    .catch(() => {
        document.getElementById('wise-balance').innerHTML = 'N/A';
    });
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/admin.php'; ?>

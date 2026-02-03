<?php ob_start(); ?>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success" style="margin-bottom:1.5rem;">
    <i class="fas fa-check-circle"></i> Uitbetaling is gemarkeerd als voltooid.
</div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card warning">
        <div class="stat-label">Wise Wachtend</div>
        <div class="stat-value">&euro;<?= number_format($totalWiseAmount ?? 0, 2, ',', '.') ?></div>
        <div class="stat-sub"><?= count($wiseTransfers ?? []) ?> transfer(s) te keuren</div>
    </div>
    <div class="stat-card info">
        <div class="stat-label">Te Betalen (Bedrijven)</div>
        <div class="stat-value">&euro;<?= number_format($totalBusinessAmount, 2, ',', '.') ?></div>
        <div class="stat-sub"><?= count($businessPayouts) ?> bedrijf/bedrijven</div>
    </div>
    <div class="stat-card secondary">
        <div class="stat-label">Te Betalen (Sales)</div>
        <div class="stat-value">&euro;<?= number_format($totalSalesAmount, 2, ',', '.') ?></div>
        <div class="stat-sub"><?= count($salesPayouts) ?> partner(s)</div>
    </div>
    <div class="stat-card primary">
        <div class="stat-label">Wise Saldo</div>
        <div class="stat-value">&euro;<?= $wiseBalance !== null ? number_format($wiseBalance, 2, ',', '.') : 'N/A' ?></div>
        <div class="stat-sub">EUR balans</div>
    </div>
</div>

<!-- Wise Fee Information -->
<div class="card" style="margin-top:1.5rem;border:1px solid var(--info);">
    <div class="card-header" style="cursor:pointer;" onclick="document.getElementById('fee-info').style.display = document.getElementById('fee-info').style.display === 'none' ? 'block' : 'none';">
        <h3 class="card-title" style="margin:0;"><i class="fas fa-info-circle"></i> Wise Transactiekosten per Land</h3>
        <span class="badge badge-info"><i class="fas fa-chevron-down"></i> Klik om te tonen</span>
    </div>
    <div id="fee-info" style="display:none;padding:1rem;">
        <p style="color:var(--text-light);margin-bottom:1rem;">
            <i class="fas fa-calculator"></i> Wise rekent een <strong>vaste fee + percentage</strong> per transactie.
            Houd hier rekening mee bij uitbetalingen.
        </p>
        <div class="table-responsive">
            <table style="font-size:0.9rem;">
                <thead>
                    <tr>
                        <th>Land</th>
                        <th>IBAN Prefix</th>
                        <th>Vaste Fee</th>
                        <th>Variabel</th>
                        <th>Voorbeeld €100</th>
                        <th>Voorbeeld €500</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background:rgba(34,197,94,0.1);">
                        <td><strong>Nederland</strong></td>
                        <td><code>NL</code></td>
                        <td>€0.54</td>
                        <td>0.41%</td>
                        <td>€0.95</td>
                        <td>€2.59</td>
                    </tr>
                    <tr style="background:rgba(34,197,94,0.1);">
                        <td><strong>Belgie</strong></td>
                        <td><code>BE</code></td>
                        <td>€0.54</td>
                        <td>0.41%</td>
                        <td>€0.95</td>
                        <td>€2.59</td>
                    </tr>
                    <tr style="background:rgba(34,197,94,0.1);">
                        <td><strong>Duitsland</strong></td>
                        <td><code>DE</code></td>
                        <td>€0.54</td>
                        <td>0.41%</td>
                        <td>€0.95</td>
                        <td>€2.59</td>
                    </tr>
                    <tr style="background:rgba(34,197,94,0.1);">
                        <td><strong>Frankrijk</strong></td>
                        <td><code>FR</code></td>
                        <td>€0.54</td>
                        <td>0.41%</td>
                        <td>€0.95</td>
                        <td>€2.59</td>
                    </tr>
                    <tr style="background:rgba(34,197,94,0.1);">
                        <td><strong>Spanje</strong></td>
                        <td><code>ES</code></td>
                        <td>€0.54</td>
                        <td>0.41%</td>
                        <td>€0.95</td>
                        <td>€2.59</td>
                    </tr>
                    <tr style="background:rgba(34,197,94,0.1);">
                        <td><strong>Italie</strong></td>
                        <td><code>IT</code></td>
                        <td>€0.54</td>
                        <td>0.41%</td>
                        <td>€0.95</td>
                        <td>€2.59</td>
                    </tr>
                    <tr style="background:rgba(34,197,94,0.1);">
                        <td><strong>Polen</strong></td>
                        <td><code>PL</code></td>
                        <td>€0.54</td>
                        <td>0.41%</td>
                        <td>€0.95</td>
                        <td>€2.59</td>
                    </tr>
                    <tr style="background:rgba(251,191,36,0.15);">
                        <td><strong>Verenigd Koninkrijk</strong></td>
                        <td><code>GB</code></td>
                        <td>€0.71</td>
                        <td>0.56%</td>
                        <td>€1.27</td>
                        <td>€3.51</td>
                    </tr>
                    <tr style="background:rgba(251,191,36,0.15);">
                        <td><strong>Zwitserland</strong></td>
                        <td><code>CH</code></td>
                        <td>€0.89</td>
                        <td>0.41%</td>
                        <td>€1.30</td>
                        <td>€2.94</td>
                    </tr>
                    <tr style="background:rgba(239,68,68,0.1);">
                        <td><strong>Turkije</strong></td>
                        <td><code>TR</code></td>
                        <td>€1.05</td>
                        <td>1.41%</td>
                        <td>€2.46</td>
                        <td>€8.10</td>
                    </tr>
                    <tr style="background:rgba(239,68,68,0.1);">
                        <td><strong>Marokko</strong></td>
                        <td><code>MA</code></td>
                        <td>€1.66</td>
                        <td>1.85%</td>
                        <td>€3.51</td>
                        <td>€10.91</td>
                    </tr>
                    <tr style="background:rgba(239,68,68,0.1);">
                        <td><strong>Verenigde Staten</strong></td>
                        <td><code>US</code></td>
                        <td>€0.94</td>
                        <td>0.56%</td>
                        <td>€1.50</td>
                        <td>€3.74</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;padding:0.75rem;background:var(--bg);border-radius:8px;font-size:0.85rem;">
            <p style="margin:0 0 0.5rem 0;"><strong>Legenda:</strong></p>
            <span style="display:inline-block;width:12px;height:12px;background:rgba(34,197,94,0.3);margin-right:0.5rem;border-radius:2px;"></span> SEPA-landen (goedkoop)
            <span style="display:inline-block;width:12px;height:12px;background:rgba(251,191,36,0.3);margin-left:1rem;margin-right:0.5rem;border-radius:2px;"></span> Europa non-SEPA
            <span style="display:inline-block;width:12px;height:12px;background:rgba(239,68,68,0.2);margin-left:1rem;margin-right:0.5rem;border-radius:2px;"></span> Buiten Europa (duurder)
            <p style="margin:0.75rem 0 0 0;color:var(--text-light);">
                <i class="fas fa-external-link-alt"></i>
                <a href="https://wise.com/pricing" target="_blank" style="color:var(--info);">Actuele tarieven bekijken op Wise.com</a>
            </p>
        </div>
    </div>
</div>

<!-- Wise Pending Transfers -->
<?php if (!empty($wiseTransfers)): ?>
<div class="card" style="margin-top:1.5rem;border:2px solid var(--warning);">
    <div class="card-header" style="background:var(--warning);color:#000;">
        <h3 class="card-title" style="color:#000;"><i class="fas fa-exclamation-triangle"></i> Wise Transfers - Goedkeuring Nodig</h3>
        <a href="https://wise.com/transactions" target="_blank" class="btn btn-sm" style="background:#000;color:#fff;">
            <i class="fas fa-external-link-alt"></i> Open Wise
        </a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Transfer ID</th>
                    <th>Ontvanger</th>
                    <th>Omschrijving</th>
                    <th>Aangemaakt</th>
                    <th style="text-align:right;">Bedrag</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($wiseTransfers as $transfer): ?>
                <tr>
                    <td><code><?= $transfer['transfer_id'] ?></code></td>
                    <td><?= htmlspecialchars($transfer['recipient_name']) ?></td>
                    <td><?= htmlspecialchars($transfer['reference']) ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($transfer['created'])) ?></td>
                    <td style="text-align:right;">
                        <strong>&euro;<?= number_format($transfer['source_amount'], 2, ',', '.') ?></strong>
                    </td>
                    <td><span class="badge badge-warning">Wacht op funding</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="padding:1rem;background:var(--bg);border-top:1px solid var(--border);">
        <p style="margin:0;color:var(--text-light);">
            <i class="fas fa-info-circle"></i>
            Deze transfers zijn aangemaakt maar wachten op goedkeuring in je Wise account.
            <a href="https://wise.com/transactions" target="_blank" style="color:var(--warning);">Klik hier om ze goed te keuren →</a>
        </p>
    </div>
</div>
<?php endif; ?>

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
                        <form method="POST" action="/admin/payouts/mark/business/<?= $payout['business_id'] ?>" style="display:inline;" onsubmit="return confirm('<?= $translations['confirm_mark_completed'] ?? 'Are you sure you want to mark this payout as completed?' ?>');">
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
                        <form method="POST" action="/admin/payouts/mark/sales/<?= $partner['sales_user_id'] ?>" style="display:inline;" onsubmit="return confirm('<?= $translations['confirm_mark_paid'] ?? 'Are you sure you want to mark this commission as paid?' ?>');">
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

</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/admin.php'; ?>

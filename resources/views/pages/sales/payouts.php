<?php ob_start(); ?>

<!-- Pending Balance -->
<div style="background:linear-gradient(135deg,#000000,#404040);border-radius:16px;padding:2rem;color:white;margin-bottom:2rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
    <div>
        <h3 style="margin:0 0 0.5rem 0"><i class="fas fa-wallet"></i> Uitbetaling in afwachting</h3>
        <p style="margin:0;opacity:0.9">Dit bedrag wordt uitbetaald bij de volgende uitbetalingsronde</p>
    </div>
    <div style="text-align:right">
        <p style="margin:0;font-size:2.5rem;font-weight:bold">&euro;<?= number_format($pendingAmount, 2, ',', '.') ?></p>
    </div>
</div>

<!-- How Payouts Work -->
<div class="payout-info-card">
    <h3><i class="fas fa-info-circle"></i> Hoe commissies werken</h3>

    <div class="commission-highlight">
        <div class="commission-icon">
            <i class="fas fa-euro-sign"></i>
        </div>
        <div class="commission-text">
            <strong>Je verdient &euro;49,99 per geregistreerde salon</strong>
            <p>Commissie wordt uitgekeerd wanneer een salon via jouw referral code registreert, de 14 dagen proeftijd afrondt en de registratiefee betaalt.</p>
        </div>
    </div>

    <div class="info-grid-3">
        <div class="info-box">
            <div class="info-icon"><i class="fas fa-coins"></i></div>
            <h4>Commissie per salon</h4>
            <p class="info-value">&euro;49,99</p>
            <span class="info-note">Per betalende salon</span>
        </div>
        <div class="info-box">
            <div class="info-icon"><i class="fas fa-calendar-check"></i></div>
            <h4>Uitbetalingsdag</h4>
            <p class="info-value">Elke woensdag</p>
            <span class="info-note">Wekelijkse automatische verwerking</span>
        </div>
        <div class="info-box">
            <div class="info-icon"><i class="fas fa-university"></i></div>
            <h4>Minimum uitbetaling</h4>
            <p class="info-value">&euro;49,99</p>
            <span class="info-note">Minimaal 1 geconverteerde salon</span>
        </div>
    </div>
</div>

<!-- Timeline -->
<div class="timeline-card">
    <h3><i class="fas fa-clock"></i> Wanneer ontvang je commissie?</h3>

    <div class="timeline-visual">
        <div class="timeline-step">
            <div class="step-marker">1</div>
            <div class="step-content">
                <strong>Salon registreert</strong>
                <span>Een salon registreert zich via jouw referral link</span>
            </div>
        </div>
        <div class="timeline-connector"></div>
        <div class="timeline-step">
            <div class="step-marker">2</div>
            <div class="step-content">
                <strong>14 dagen proeftijd</strong>
                <span>De salon test GlamourSchedule gratis uit</span>
            </div>
        </div>
        <div class="timeline-connector"></div>
        <div class="timeline-step">
            <div class="step-marker">3</div>
            <div class="step-content">
                <strong>Salon betaalt registratiefee</strong>
                <span>Na de proeftijd betaalt de salon om door te gaan</span>
            </div>
        </div>
        <div class="timeline-connector"></div>
        <div class="timeline-step highlight">
            <div class="step-marker success"><i class="fas fa-check"></i></div>
            <div class="step-content">
                <strong>Commissie vrijgegeven</strong>
                <span>Jouw &euro;49,99 commissie wordt toegevoegd aan je saldo</span>
            </div>
        </div>
        <div class="timeline-connector"></div>
        <div class="timeline-step">
            <div class="step-marker final"><i class="fas fa-euro-sign"></i></div>
            <div class="step-content">
                <strong>Woensdag: Uitbetaling</strong>
                <span>Automatische overboeking naar je IBAN</span>
            </div>
        </div>
    </div>

    <div class="timeline-example">
        <i class="fas fa-lightbulb"></i>
        <div>
            <strong>Voorbeeld:</strong> Salon "Beauty Studio" registreert op 1 januari via jouw link.
            Na de proeftijd betaalt ze op 15 januari de registratiefee.
            Jouw &euro;49,99 commissie wordt de eerstvolgende woensdag (22 januari) uitbetaald.
        </div>
    </div>
</div>

<!-- Important Notes -->
<div class="notes-card">
    <h3><i class="fas fa-exclamation-triangle"></i> Belangrijk om te weten</h3>
    <ul>
        <li><i class="fas fa-hourglass-half"></i> Commissie wordt pas vrijgegeven <strong>na betaling</strong> van de registratiefee (na proeftijd)</li>
        <li><i class="fas fa-ban"></i> Geen commissie voor <strong>Early Bird</strong> registraties (deze zijn gratis)</li>
        <li><i class="fas fa-times-circle"></i> Als een salon de proeftijd niet omzet naar betaling, ontvang je geen commissie</li>
        <li><i class="fas fa-piggy-bank"></i> Commissies blijven staan tot de eerstvolgende uitbetalingsronde</li>
        <li><i class="fas fa-edit"></i> Zorg dat je <a href="/sales/account">IBAN correct is ingevuld</a> in je account</li>
    </ul>
</div>

<!-- Payout History -->
<div class="card" style="background:#ffffff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
    <h3 style="margin:0 0 1.5rem 0"><i class="fas fa-history"></i> Uitbetalingsgeschiedenis</h3>

    <?php if (empty($payouts)): ?>
        <p style="color:#6b7280;text-align:center;padding:3rem 0">
            <i class="fas fa-euro-sign" style="font-size:3rem;opacity:0.3;display:block;margin-bottom:1rem"></i>
            Nog geen uitbetalingen.<br>Zodra een salon via jouw link registreert en betaalt, ontvang je commissie.
        </p>
    <?php else: ?>
        <div class="table-responsive">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="border-bottom:2px solid #e5e7eb">
                        <th style="text-align:left;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Datum</th>
                        <th style="text-align:left;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Referentie</th>
                        <th style="text-align:center;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Salons</th>
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
                                <?= isset($payout['referral_count']) ? $payout['referral_count'] : (isset($payout['booking_count']) ? $payout['booking_count'] : '-') ?>
                            </td>
                            <td style="padding:1rem 0.5rem;text-align:center">
                                <?php
                                $status = $payout['status'] ?? 'completed';
                                $statusColors = [
                                    'completed' => ['bg' => '#22c55e20', 'text' => '#22c55e'],
                                    'pending' => ['bg' => '#f59e0b20', 'text' => '#f59e0b'],
                                    'failed' => ['bg' => '#ef444420', 'text' => '#ef4444']
                                ];
                                $colors = $statusColors[$status] ?? $statusColors['completed'];
                                $statusText = ['completed' => 'Uitbetaald', 'pending' => 'In verwerking', 'failed' => 'Mislukt'];
                                ?>
                                <span style="background:<?= $colors['bg'] ?>;color:<?= $colors['text'] ?>;padding:0.35rem 1rem;border-radius:20px;font-size:0.85rem">
                                    <?= $statusText[$status] ?? 'Uitbetaald' ?>
                                </span>
                            </td>
                            <td style="padding:1rem 0.5rem;text-align:right;font-weight:600;font-size:1.1rem;color:#333333">
                                &euro;<?= number_format($payout['amount'], 2, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.payout-info-card,
.timeline-card,
.notes-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.payout-info-card h3,
.timeline-card h3,
.notes-card h3 {
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.commission-highlight {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border: 2px solid #22c55e;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.commission-icon {
    width: 50px;
    height: 50px;
    background: #22c55e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.commission-text strong {
    display: block;
    font-size: 1.1rem;
    color: #166534;
    margin-bottom: 0.25rem;
}

.commission-text p {
    margin: 0;
    color: #166534;
    font-size: 0.9rem;
}

.info-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.info-box {
    background: #f9fafb;
    border-radius: 10px;
    padding: 1.25rem;
    text-align: center;
}

.info-icon {
    width: 40px;
    height: 40px;
    background: #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin: 0 auto 0.75rem;
}

.info-box h4 {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    color: #666;
}

.info-box .info-value {
    margin: 0;
    font-size: 1.25rem;
    font-weight: bold;
    color: #000;
}

.info-box .info-note {
    display: block;
    font-size: 0.75rem;
    color: #999;
    margin-top: 0.25rem;
}

/* Timeline Visual */
.timeline-visual {
    position: relative;
    padding: 1rem 0;
}

.timeline-step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.5rem 0;
}

.timeline-step.highlight .step-content {
    background: #f0fdf4;
    border: 1px solid #22c55e;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin: -0.5rem 0;
}

.step-marker {
    width: 36px;
    height: 36px;
    background: #e5e7eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #374151;
    flex-shrink: 0;
    font-size: 0.9rem;
}

.step-marker.success {
    background: #22c55e;
    color: white;
}

.step-marker.final {
    background: #000;
    color: white;
}

.step-content {
    flex: 1;
    padding-top: 0.25rem;
}

.step-content strong {
    display: block;
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
}

.step-content span {
    font-size: 0.85rem;
    color: #666;
}

.timeline-connector {
    width: 2px;
    height: 20px;
    background: #d1d5db;
    margin-left: 17px;
}

.timeline-example {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    background: #eff6ff;
    border: 1px solid #3b82f6;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1.5rem;
    font-size: 0.85rem;
    color: #1e40af;
}

.timeline-example i {
    color: #3b82f6;
    font-size: 1.1rem;
    margin-top: 0.1rem;
}

.timeline-example strong {
    color: #1e3a8a;
}

/* Notes Card */
.notes-card {
    background: #fffbeb;
    border: 1px solid #f59e0b;
}

.notes-card h3 {
    color: #92400e;
}

.notes-card ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.notes-card li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #fde68a;
    color: #78350f;
}

.notes-card li:last-child {
    border-bottom: none;
}

.notes-card li i {
    color: #f59e0b;
    margin-top: 0.15rem;
}

.notes-card li a {
    color: #d97706;
    font-weight: 600;
}

.table-responsive {
    overflow-x: auto;
}

@media (max-width: 768px) {
    .info-grid-3 {
        grid-template-columns: 1fr;
    }

    .commission-highlight {
        flex-direction: column;
        text-align: center;
    }

    .timeline-step {
        gap: 0.75rem;
    }

    .step-marker {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }

    .timeline-connector {
        margin-left: 15px;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/sales.php'; ?>

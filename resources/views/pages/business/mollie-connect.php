<?php
$pageTitle = 'Mollie Koppelen - Automatische Uitbetalingen';
$isConnected = $isConnected ?? false;
$onboardingStatus = $onboardingStatus ?? 'pending';
ob_start();
?>

<div class="mollie-connect-page">
    <div class="connect-header">
        <h1>Automatische Uitbetalingen</h1>
        <p>Koppel je Mollie account om automatisch uitbetalingen te ontvangen na voltooide boekingen.</p>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if ($isConnected): ?>
        <!-- Connected State -->
        <div class="connect-card connected">
            <div class="status-badge success">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Verbonden
            </div>

            <div class="mollie-logo">
                <img src="https://www.mollie.com/external/icons/mollie-logo.svg" alt="Mollie" onerror="this.style.display='none'">
            </div>

            <h2>Mollie Account Gekoppeld</h2>
            <p class="connected-info">
                Je ontvangt automatisch uitbetalingen voor voltooide boekingen.<br>
                Account ID: <code><?= htmlspecialchars($business['mollie_account_id'] ?? 'N/A') ?></code>
            </p>

            <div class="payout-info">
                <div class="info-item">
                    <span class="label">Platformkosten per boeking</span>
                    <span class="value">&euro;1,75</span>
                </div>
                <div class="info-item">
                    <span class="label">Uitbetaling na QR scan</span>
                    <span class="value">24 uur</span>
                </div>
                <div class="info-item">
                    <span class="label">Verbonden sinds</span>
                    <span class="value"><?= $business['mollie_connected_at'] ? date('d-m-Y', strtotime($business['mollie_connected_at'])) : 'N/A' ?></span>
                </div>
            </div>

            <div class="actions">
                <a href="/business/payouts" class="btn btn-primary">Bekijk Uitbetalingen</a>
                <form action="/business/mollie/disconnect" method="POST" class="disconnect-form" onsubmit="return confirm('<?= $translations['confirm_disconnect_mollie'] ?? 'Are you sure you want to disconnect? You will no longer receive automatic payouts.' ?>');">
                    <button type="submit" class="btn btn-outline-danger">Koppeling Verbreken</button>
                </form>
            </div>
        </div>

    <?php else: ?>
        <!-- Not Connected State -->
        <div class="connect-card">
            <div class="mollie-logo">
                <img src="https://www.mollie.com/external/icons/mollie-logo.svg" alt="Mollie" onerror="this.style.display='none'">
            </div>

            <h2>Koppel je Mollie Account</h2>
            <p>Met Mollie Connect ontvang je automatisch uitbetalingen direct op je bankrekening.</p>

            <div class="benefits">
                <div class="benefit">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="benefit-text">
                        <strong>Automatisch</strong>
                        <span>24 uur na QR scan</span>
                    </div>
                </div>
                <div class="benefit">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <div class="benefit-text">
                        <strong>Veilig</strong>
                        <span>Mollie verified</span>
                    </div>
                </div>
                <div class="benefit">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <div class="benefit-text">
                        <strong>Laag tarief</strong>
                        <span>&euro;1,75 per boeking</span>
                    </div>
                </div>
            </div>

            <div class="recommendation-banner">
                <div class="rec-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <div class="rec-content">
                    <strong>Aanbevolen: Koppel Mollie voor snellere uitbetalingen!</strong>
                    <p>Met Mollie Connect ontvang je automatisch je geld <strong>24 uur na de QR-code scan</strong>. Zonder Mollie worden uitbetalingen elke <strong>zondag</strong> handmatig verwerkt naar je bankrekening.</p>
                </div>
            </div>

            <div class="how-it-works">
                <h3>Hoe het werkt</h3>
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <strong>Klant boekt online</strong>
                            <span>Betaling via iDEAL, creditcard, etc.</span>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <strong>Scan QR-code</strong>
                            <span>Check de klant in bij aankomst</span>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <strong>Automatische uitbetaling</strong>
                            <span>24 uur later op je rekening</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="comparison-box">
                <h3>Vergelijking uitbetalingen</h3>
                <div class="comparison-grid">
                    <div class="comparison-item recommended">
                        <div class="comp-header">
                            <span class="comp-badge">Aanbevolen</span>
                            <strong>Met Mollie Connect</strong>
                        </div>
                        <ul>
                            <li><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Automatisch binnen 24 uur</li>
                            <li><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Direct na QR-code scan</li>
                            <li><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Geen handmatige actie nodig</li>
                        </ul>
                    </div>
                    <div class="comparison-item">
                        <div class="comp-header">
                            <strong>Zonder Mollie Connect</strong>
                        </div>
                        <ul>
                            <li><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#f59e0b" stroke-width="3"><circle cx="12" cy="12" r="10"></circle></svg> Wekelijks op woensdag</li>
                            <li><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#f59e0b" stroke-width="3"><circle cx="12" cy="12" r="10"></circle></svg> Automatisch via bankoverschrijving</li>
                            <li><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#f59e0b" stroke-width="3"><circle cx="12" cy="12" r="10"></circle></svg> Tot 10 dagen wachttijd</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="timeline-box">
                <h3>Verwerkingsschema</h3>

                <div class="timeline-section">
                    <div class="timeline-header mollie">
                        <span class="timeline-badge">Mollie Connect</span>
                        <span class="timeline-speed">Snelste optie</span>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-row">
                            <div class="timeline-day">Dag 1</div>
                            <div class="timeline-event">Klant betaalt &amp; QR-code gescand</div>
                        </div>
                        <div class="timeline-arrow"></div>
                        <div class="timeline-row">
                            <div class="timeline-day">Dag 2</div>
                            <div class="timeline-event success">Geld op je rekening</div>
                        </div>
                    </div>
                </div>

                <div class="timeline-divider">of</div>

                <div class="timeline-section">
                    <div class="timeline-header bunq">
                        <span class="timeline-badge alt">Zonder Mollie Connect</span>
                        <span class="timeline-speed">Wekelijks</span>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-row">
                            <div class="timeline-day">Ma - Zo</div>
                            <div class="timeline-event">Boekingen van de week (QR gescand)</div>
                        </div>
                        <div class="timeline-arrow"></div>
                        <div class="timeline-row">
                            <div class="timeline-day">Maandag</div>
                            <div class="timeline-event">Mollie verwerkt betalingen</div>
                        </div>
                        <div class="timeline-arrow"></div>
                        <div class="timeline-row">
                            <div class="timeline-day">Dinsdag</div>
                            <div class="timeline-event">Mollie keert uit naar GlamourSchedule</div>
                        </div>
                        <div class="timeline-arrow"></div>
                        <div class="timeline-row">
                            <div class="timeline-day">Woensdag</div>
                            <div class="timeline-event success">Automatische uitbetaling naar jouw IBAN</div>
                        </div>
                        <div class="timeline-arrow"></div>
                        <div class="timeline-row">
                            <div class="timeline-day">Do - Vr</div>
                            <div class="timeline-event success">Geld op je rekening (1-2 werkdagen)</div>
                        </div>
                    </div>
                </div>

                <div class="timeline-note">
                    <strong>Voorbeeld:</strong> Een boeking op vrijdag 10 januari wordt uiterlijk vrijdag 17 januari uitbetaald zonder Mollie Connect, of zaterdag 11 januari met Mollie Connect.
                </div>
            </div>

            <div class="pricing-example">
                <h3>Rekenvoorbeeld</h3>
                <table>
                    <tr>
                        <td>Behandeling</td>
                        <td class="amount">&euro;50,00</td>
                    </tr>
                    <tr>
                        <td>Platformkosten</td>
                        <td class="amount negative">-&euro;1,75</td>
                    </tr>
                    <tr class="total">
                        <td>Jouw uitbetaling</td>
                        <td class="amount positive">&euro;48,25</td>
                    </tr>
                </table>
            </div>

            <a href="/business/mollie/authorize" class="btn btn-mollie">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                </svg>
                Koppel Mollie Account
            </a>

            <p class="note">
                Je wordt doorgestuurd naar Mollie om in te loggen en toestemming te geven.
                Heb je nog geen Mollie account? Je kunt er gratis een aanmaken.
            </p>
        </div>
    <?php endif; ?>
</div>

<style>
.mollie-connect-page {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.connect-header {
    text-align: center;
    margin-bottom: 30px;
}

.connect-header h1 {
    font-size: 1.75rem;
    margin-bottom: 10px;
}

.connect-header p {
    color: #666;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.alert-success {
    background: #f0fdf4;
    border: 1px solid #22c55e;
    color: #166534;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #ef4444;
    color: #991b1b;
}

.connect-card {
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.connect-card.connected {
    border: 2px solid #22c55e;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.status-badge.success {
    background: #f0fdf4;
    color: #22c55e;
}

.mollie-logo {
    text-align: center;
    margin-bottom: 20px;
}

.mollie-logo img {
    height: 40px;
}

.connect-card h2 {
    text-align: center;
    margin-bottom: 10px;
}

.connect-card > p {
    text-align: center;
    color: #666;
    margin-bottom: 25px;
}

.connected-info {
    font-size: 0.9rem;
}

.connected-info code {
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.8rem;
}

.payout-info {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    margin: 25px 0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item .label {
    color: #666;
}

.info-item .value {
    font-weight: 600;
}

.actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.actions .btn {
    flex: 1;
}

.disconnect-form {
    flex: 1;
}

.disconnect-form .btn {
    width: 100%;
}

.benefits {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 30px;
}

.benefit {
    text-align: center;
    padding: 15px 10px;
    background: #f9fafb;
    border-radius: 12px;
}

.benefit-icon {
    width: 48px;
    height: 48px;
    background: #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    color: #fff;
}

.benefit-text strong {
    display: block;
    font-size: 0.9rem;
}

.benefit-text span {
    font-size: 0.8rem;
    color: #666;
}

.how-it-works {
    margin-bottom: 30px;
}

.how-it-works h3 {
    font-size: 1rem;
    margin-bottom: 15px;
}

.steps {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.step {
    display: flex;
    align-items: center;
    gap: 15px;
}

.step-number {
    width: 32px;
    height: 32px;
    background: #000;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
}

.step-content strong {
    display: block;
}

.step-content span {
    font-size: 0.85rem;
    color: #666;
}

.pricing-example {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
}

.pricing-example h3 {
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.pricing-example table {
    width: 100%;
}

.pricing-example td {
    padding: 8px 0;
}

.pricing-example .amount {
    text-align: right;
    font-family: monospace;
}

.pricing-example .negative {
    color: #dc2626;
}

.pricing-example .positive {
    color: #22c55e;
}

.pricing-example .total {
    border-top: 2px solid #ddd;
    font-weight: bold;
}

.pricing-example .total td {
    padding-top: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 24px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #000;
    color: #fff;
}

.btn-primary:hover {
    background: #333;
}

.btn-mollie {
    width: 100%;
    background: #000;
    color: #fff;
    font-size: 1rem;
}

.btn-mollie:hover {
    background: #333;
}

.btn-outline-danger {
    background: transparent;
    border: 2px solid #dc2626;
    color: #dc2626;
}

.btn-outline-danger:hover {
    background: #dc2626;
    color: #fff;
}

.note {
    text-align: center;
    font-size: 0.8rem;
    color: #999;
    margin-top: 15px;
}

@media (max-width: 480px) {
    .benefits {
        grid-template-columns: 1fr;
    }

    .actions {
        flex-direction: column;
    }

    .comparison-grid {
        grid-template-columns: 1fr;
    }
}

.recommendation-banner {
    display: flex;
    gap: 15px;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border: 2px solid #22c55e;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
}

.rec-icon {
    width: 48px;
    height: 48px;
    background: #22c55e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: white;
}

.rec-content strong {
    display: block;
    color: #166534;
    margin-bottom: 5px;
}

.rec-content p {
    margin: 0;
    color: #166534;
    font-size: 0.9rem;
    line-height: 1.5;
}

.comparison-box {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
}

.comparison-box h3 {
    font-size: 1rem;
    margin-bottom: 15px;
    text-align: center;
}

.comparison-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.comparison-item {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    border: 2px solid #e5e7eb;
}

.comparison-item.recommended {
    border-color: #22c55e;
    background: linear-gradient(135deg, #f0fdf4, #fff);
}

.comp-header {
    margin-bottom: 12px;
}

.comp-badge {
    display: inline-block;
    background: #22c55e;
    color: white;
    font-size: 0.7rem;
    padding: 3px 8px;
    border-radius: 10px;
    margin-bottom: 5px;
    font-weight: 600;
}

.comp-header strong {
    display: block;
    font-size: 0.95rem;
}

.comparison-item ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.comparison-item li {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
    font-size: 0.85rem;
    color: #374151;
}

/* Timeline Box */
.timeline-box {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
}

.timeline-box h3 {
    font-size: 1rem;
    margin-bottom: 20px;
    text-align: center;
}

.timeline-section {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    border: 2px solid #e5e7eb;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e7eb;
}

.timeline-header.mollie {
    border-bottom-color: #22c55e;
}

.timeline-header.bunq {
    border-bottom-color: #f59e0b;
}

.timeline-badge {
    background: #22c55e;
    color: white;
    font-size: 0.75rem;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 600;
}

.timeline-badge.alt {
    background: #f59e0b;
}

.timeline-speed {
    font-size: 0.8rem;
    color: #666;
    font-weight: 500;
}

.timeline-content {
    padding: 0 10px;
}

.timeline-row {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 8px 0;
}

.timeline-day {
    min-width: 80px;
    font-weight: 600;
    font-size: 0.85rem;
    color: #374151;
}

.timeline-event {
    flex: 1;
    font-size: 0.85rem;
    color: #666;
    padding: 8px 12px;
    background: #f5f5f5;
    border-radius: 6px;
}

.timeline-event.success {
    background: #f0fdf4;
    color: #166534;
    font-weight: 600;
    border: 1px solid #22c55e;
}

.timeline-arrow {
    width: 2px;
    height: 15px;
    background: #d1d5db;
    margin-left: 39px;
}

.timeline-divider {
    text-align: center;
    color: #999;
    font-size: 0.85rem;
    margin: 10px 0;
    position: relative;
}

.timeline-divider::before,
.timeline-divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 40%;
    height: 1px;
    background: #e5e7eb;
}

.timeline-divider::before {
    left: 0;
}

.timeline-divider::after {
    right: 0;
}

.timeline-note {
    background: #eff6ff;
    border: 1px solid #3b82f6;
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 0.8rem;
    color: #1e40af;
    margin-top: 15px;
}

@media (max-width: 480px) {
    .timeline-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .timeline-day {
        min-width: auto;
    }

    .timeline-arrow {
        margin-left: 10px;
        height: 10px;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

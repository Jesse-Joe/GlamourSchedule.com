<?php
$pageTitle = 'Stripe Connect - Automatische Uitbetalingen';
$isConnected = $isConnected ?? false;
ob_start();
?>

<style>
.stripe-connect-page {
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.connect-header {
    text-align: center;
    margin-bottom: 2rem;
}

.connect-header h1 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.connect-header p {
    color: var(--text-secondary);
}

.connect-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
}

.connect-card.connected {
    border-color: #10b981;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.status-badge.success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.stripe-logo {
    margin-bottom: 1.5rem;
}

.stripe-logo svg {
    height: 40px;
    width: auto;
}

.connect-card h2 {
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
    color: var(--text-primary);
}

.connect-card p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.connected-info code {
    background: var(--input-bg);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
}

.payout-info {
    background: var(--input-bg);
    border-radius: 12px;
    padding: 1.25rem;
    margin: 1.5rem 0;
    text-align: left;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--card-border);
}

.info-item:last-child {
    border-bottom: none;
}

.info-item .label {
    color: var(--text-secondary);
}

.info-item .value {
    font-weight: 600;
    color: var(--text-primary);
}

.benefits {
    display: grid;
    gap: 1rem;
    margin: 1.5rem 0;
    text-align: left;
}

.benefit {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--input-bg);
    border-radius: 12px;
}

.benefit-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #635bff 0%, #8b5cf6 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.benefit-text strong {
    display: block;
    color: var(--text-primary);
}

.benefit-text span {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1.5rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    text-decoration: none;
}

.btn-stripe {
    background: linear-gradient(135deg, #635bff 0%, #8b5cf6 100%);
    color: white;
}

.btn-stripe:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 91, 255, 0.4);
}

.btn-primary {
    background: var(--business-primary, #635bff);
    color: white;
}

.btn-outline {
    background: transparent;
    border: 2px solid var(--card-border);
    color: var(--text-primary);
}

.btn-outline:hover {
    border-color: var(--text-primary);
}

.btn-outline-danger {
    background: transparent;
    border: 2px solid #ef4444;
    color: #ef4444;
}

.btn-outline-danger:hover {
    background: #ef4444;
    color: white;
}

.disconnect-form {
    display: inline;
}

.alert {
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.alert-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.alert-info {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.provider-choice {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 2rem;
}

.provider-option {
    border: 2px solid var(--card-border);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.provider-option:hover {
    border-color: var(--text-secondary);
}

.provider-option.active {
    border-color: #635bff;
    background: rgba(99, 91, 255, 0.05);
}

.provider-option img {
    height: 30px;
    margin-bottom: 0.75rem;
}

.provider-option span {
    display: block;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

@media (max-width: 600px) {
    .provider-choice {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="stripe-connect-page">
    <div class="connect-header">
        <h1><i class="fas fa-link"></i> Stripe Connect</h1>
        <p>Koppel je Stripe account voor automatische uitbetalingen bij internationale betalingen.</p>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['warning'])): ?>
        <div class="alert alert-warning">
            <?= htmlspecialchars($_SESSION['warning']) ?>
        </div>
        <?php unset($_SESSION['warning']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['info'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($_SESSION['info']) ?>
        </div>
        <?php unset($_SESSION['info']); ?>
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

            <div class="stripe-logo">
                <svg viewBox="0 0 60 25" xmlns="http://www.w3.org/2000/svg" width="120" height="50">
                    <path fill="#635bff" d="M59.64 14.28h-8.06c.19 1.93 1.6 2.55 3.2 2.55 1.64 0 2.96-.37 4.05-.95v3.32a8.33 8.33 0 0 1-4.56 1.1c-4.01 0-6.83-2.5-6.83-7.48 0-4.19 2.39-7.52 6.3-7.52 3.92 0 5.96 3.28 5.96 7.5 0 .4-.02 1.04-.06 1.48zm-6.3-5.63c-1.03 0-1.93.76-2.12 2.39h4.25c-.1-1.46-.78-2.39-2.13-2.39zM37.6 19.52h-4.14V7.24l4.14-.89v13.17zM37.6 6.07h-4.14V2.3l4.14-.86V6.07zM31.12 15.64V5.3h4.14v9.49c0 1.19.58 1.54 1.4 1.54.28 0 .6-.04.95-.14v3.36c-.61.2-1.43.3-2.36.3-2.69 0-4.13-1.48-4.13-4.21zM22.68 5.3h3.18v1.38a4.05 4.05 0 0 1 3.33-1.63c2.75 0 4.08 1.94 4.08 4.88v9.59h-4.14v-8.42c0-1.67-.64-2.3-1.84-2.3-1.3 0-2.47.86-2.47 2.79v7.93h-4.14V5.3zM13.1 5.3h3.54v2.12h-3.54v5.61c0 1.72.75 2.2 1.8 2.2.52 0 .96-.07 1.4-.2v3.46c-.72.26-1.5.36-2.35.36-2.77 0-5-.81-5-5.32V7.42H6.7V5.3h2.25V2.4l4.14-.89v3.78zM5.76 9.96c-1.52-.56-2.4-.86-2.4-1.63 0-.57.48-.87 1.32-.87 1.2 0 2.62.46 3.96 1.24V4.95a10.05 10.05 0 0 0-3.96-.75c-3.24 0-5.48 1.67-5.48 4.16 0 2.76 2.2 3.7 4.36 4.4 1.59.52 2.52.84 2.52 1.68 0 .66-.57 1.04-1.63 1.04-1.35 0-3.3-.6-4.68-1.56v4.05c1.38.64 3.24 1.03 4.68 1.03 3.44 0 5.79-1.62 5.79-4.22 0-2.88-2.27-3.77-4.48-4.82z"/>
                </svg>
            </div>

            <h2>Stripe Account Gekoppeld</h2>
            <p class="connected-info">
                Je ontvangt automatisch uitbetalingen voor internationale betalingen.<br>
                Account ID: <code><?= htmlspecialchars($business['stripe_account_id'] ?? 'N/A') ?></code>
            </p>

            <div class="payout-info">
                <div class="info-item">
                    <span class="label">Platformkosten per boeking</span>
                    <span class="value">&euro;1,75</span>
                </div>
                <div class="info-item">
                    <span class="label">Uitbetaling</span>
                    <span class="value">Automatisch via Stripe</span>
                </div>
                <div class="info-item">
                    <span class="label">Verbonden sinds</span>
                    <span class="value"><?= $business['stripe_connected_at'] ? date('d-m-Y', strtotime($business['stripe_connected_at'])) : 'N/A' ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Status</span>
                    <span class="value" style="color: #10b981;">
                        <i class="fas fa-check-circle"></i>
                        <?= $business['stripe_charges_enabled'] ? 'Betalingen actief' : 'In behandeling' ?>
                    </span>
                </div>
            </div>

            <div class="actions">
                <a href="/business/stripe-connect/dashboard" class="btn btn-stripe">
                    <i class="fas fa-external-link-alt"></i> Open Stripe Dashboard
                </a>
                <a href="/business/payouts" class="btn btn-outline">Bekijk Uitbetalingen</a>
                <form action="/business/stripe-connect/disconnect" method="POST" class="disconnect-form" onsubmit="return confirm('<?= $translations['confirm_disconnect_stripe'] ?? 'Are you sure you want to disconnect the connection?' ?>');">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="submit" class="btn btn-outline-danger" style="width:100%;">Koppeling Verbreken</button>
                </form>
            </div>
        </div>

    <?php elseif (!empty($business['stripe_account_id']) && $business['stripe_onboarding_status'] !== 'completed'): ?>
        <!-- Onboarding in progress -->
        <div class="connect-card">
            <div class="status-badge pending">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Onboarding niet voltooid
            </div>

            <div class="stripe-logo">
                <svg viewBox="0 0 60 25" xmlns="http://www.w3.org/2000/svg" width="120" height="50">
                    <path fill="#635bff" d="M59.64 14.28h-8.06c.19 1.93 1.6 2.55 3.2 2.55 1.64 0 2.96-.37 4.05-.95v3.32a8.33 8.33 0 0 1-4.56 1.1c-4.01 0-6.83-2.5-6.83-7.48 0-4.19 2.39-7.52 6.3-7.52 3.92 0 5.96 3.28 5.96 7.5 0 .4-.02 1.04-.06 1.48zm-6.3-5.63c-1.03 0-1.93.76-2.12 2.39h4.25c-.1-1.46-.78-2.39-2.13-2.39zM37.6 19.52h-4.14V7.24l4.14-.89v13.17zM37.6 6.07h-4.14V2.3l4.14-.86V6.07zM31.12 15.64V5.3h4.14v9.49c0 1.19.58 1.54 1.4 1.54.28 0 .6-.04.95-.14v3.36c-.61.2-1.43.3-2.36.3-2.69 0-4.13-1.48-4.13-4.21zM22.68 5.3h3.18v1.38a4.05 4.05 0 0 1 3.33-1.63c2.75 0 4.08 1.94 4.08 4.88v9.59h-4.14v-8.42c0-1.67-.64-2.3-1.84-2.3-1.3 0-2.47.86-2.47 2.79v7.93h-4.14V5.3zM13.1 5.3h3.54v2.12h-3.54v5.61c0 1.72.75 2.2 1.8 2.2.52 0 .96-.07 1.4-.2v3.46c-.72.26-1.5.36-2.35.36-2.77 0-5-.81-5-5.32V7.42H6.7V5.3h2.25V2.4l4.14-.89v3.78zM5.76 9.96c-1.52-.56-2.4-.86-2.4-1.63 0-.57.48-.87 1.32-.87 1.2 0 2.62.46 3.96 1.24V4.95a10.05 10.05 0 0 0-3.96-.75c-3.24 0-5.48 1.67-5.48 4.16 0 2.76 2.2 3.7 4.36 4.4 1.59.52 2.52.84 2.52 1.68 0 .66-.57 1.04-1.63 1.04-1.35 0-3.3-.6-4.68-1.56v4.05c1.38.64 3.24 1.03 4.68 1.03 3.44 0 5.79-1.62 5.79-4.22 0-2.88-2.27-3.77-4.48-4.82z"/>
                </svg>
            </div>

            <h2>Voltooi je Stripe Onboarding</h2>
            <p>Je Stripe account is aangemaakt, maar de verificatie is nog niet voltooid. Klik hieronder om verder te gaan.</p>

            <div class="actions">
                <a href="/business/stripe-connect/connect" class="btn btn-stripe">
                    <i class="fas fa-arrow-right"></i> Verder met Onboarding
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- Not Connected State -->
        <div class="connect-card">
            <div class="stripe-logo">
                <svg viewBox="0 0 60 25" xmlns="http://www.w3.org/2000/svg" width="120" height="50">
                    <path fill="#635bff" d="M59.64 14.28h-8.06c.19 1.93 1.6 2.55 3.2 2.55 1.64 0 2.96-.37 4.05-.95v3.32a8.33 8.33 0 0 1-4.56 1.1c-4.01 0-6.83-2.5-6.83-7.48 0-4.19 2.39-7.52 6.3-7.52 3.92 0 5.96 3.28 5.96 7.5 0 .4-.02 1.04-.06 1.48zm-6.3-5.63c-1.03 0-1.93.76-2.12 2.39h4.25c-.1-1.46-.78-2.39-2.13-2.39zM37.6 19.52h-4.14V7.24l4.14-.89v13.17zM37.6 6.07h-4.14V2.3l4.14-.86V6.07zM31.12 15.64V5.3h4.14v9.49c0 1.19.58 1.54 1.4 1.54.28 0 .6-.04.95-.14v3.36c-.61.2-1.43.3-2.36.3-2.69 0-4.13-1.48-4.13-4.21zM22.68 5.3h3.18v1.38a4.05 4.05 0 0 1 3.33-1.63c2.75 0 4.08 1.94 4.08 4.88v9.59h-4.14v-8.42c0-1.67-.64-2.3-1.84-2.3-1.3 0-2.47.86-2.47 2.79v7.93h-4.14V5.3zM13.1 5.3h3.54v2.12h-3.54v5.61c0 1.72.75 2.2 1.8 2.2.52 0 .96-.07 1.4-.2v3.46c-.72.26-1.5.36-2.35.36-2.77 0-5-.81-5-5.32V7.42H6.7V5.3h2.25V2.4l4.14-.89v3.78zM5.76 9.96c-1.52-.56-2.4-.86-2.4-1.63 0-.57.48-.87 1.32-.87 1.2 0 2.62.46 3.96 1.24V4.95a10.05 10.05 0 0 0-3.96-.75c-3.24 0-5.48 1.67-5.48 4.16 0 2.76 2.2 3.7 4.36 4.4 1.59.52 2.52.84 2.52 1.68 0 .66-.57 1.04-1.63 1.04-1.35 0-3.3-.6-4.68-1.56v4.05c1.38.64 3.24 1.03 4.68 1.03 3.44 0 5.79-1.62 5.79-4.22 0-2.88-2.27-3.77-4.48-4.82z"/>
                </svg>
            </div>

            <h2>Koppel je Stripe Account</h2>
            <p>Met Stripe Connect ontvang je automatisch uitbetalingen voor internationale betalingen (creditcard, Apple Pay, Google Pay).</p>

            <div class="benefits">
                <div class="benefit">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <div class="benefit-text">
                        <strong>Automatische Splits</strong>
                        <span>Betalingen worden automatisch gesplitst</span>
                    </div>
                </div>
                <div class="benefit">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </div>
                    <div class="benefit-text">
                        <strong>Internationaal</strong>
                        <span>Creditcard, Apple Pay, Google Pay</span>
                    </div>
                </div>
                <div class="benefit">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <div class="benefit-text">
                        <strong>Veilig & Betrouwbaar</strong>
                        <span>Stripe verwerkt miljarden per jaar</span>
                    </div>
                </div>
                <div class="benefit">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="benefit-text">
                        <strong>Snelle Uitbetaling</strong>
                        <span>Dagelijks of wekelijks naar je rekening</span>
                    </div>
                </div>
            </div>

            <div class="payout-info">
                <div class="info-item">
                    <span class="label">Platformkosten per boeking</span>
                    <span class="value">&euro;1,75</span>
                </div>
                <div class="info-item">
                    <span class="label">Stripe transactiekosten</span>
                    <span class="value">1,5% + €0,25</span>
                </div>
            </div>

            <div class="actions">
                <a href="/business/stripe-connect/connect" class="btn btn-stripe">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    Stripe Account Koppelen
                </a>
                <a href="/business/mollie-connect" class="btn btn-outline">
                    <i class="fas fa-exchange-alt"></i> Liever Mollie Connect?
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Info box about both options -->
    <div class="connect-card" style="margin-top:1.5rem; text-align:left;">
        <h3 style="margin-bottom:1rem;"><i class="fas fa-info-circle"></i> Welke kiezen?</h3>
        <p style="margin-bottom:1rem;">Je kunt zowel Mollie Connect als Stripe Connect gebruiken. Het systeem kiest automatisch de juiste provider:</p>
        <ul style="margin-left:1.5rem; color:var(--text-secondary);">
            <li><strong>Mollie</strong>: iDEAL, Bancontact, SOFORT, Giropay (NL, BE, DE)</li>
            <li><strong>Stripe</strong>: Creditcard, Apple Pay, Google Pay (Internationaal)</li>
        </ul>
        <p style="margin-top:1rem; font-size:0.9rem; color:var(--text-muted);">
            Tip: Koppel beide voor maximale flexibiliteit en automatische uitbetalingen.
        </p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

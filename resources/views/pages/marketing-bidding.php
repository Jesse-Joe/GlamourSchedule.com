<?php ob_start(); ?>

<style>
/* Marketing Bidding Page */
.bidding-page {
    padding-top: 0;
    background: #0a0a0a;
    min-height: 100vh;
}

/* Hero */
.bidding-hero {
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
    padding: 4rem 1.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border-bottom: 1px solid #222;
}
.bidding-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.03) 0%, transparent 50%);
    pointer-events: none;
}
.bidding-hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}
.bidding-hero h1 {
    font-size: 2.5rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 1rem;
    line-height: 1.2;
}
.bidding-hero p {
    font-size: 1.15rem;
    color: rgba(255,255,255,0.7);
    margin: 0 0 1.5rem;
    line-height: 1.6;
}
.bidding-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    color: #ffffff;
    font-size: 0.85rem;
    margin-bottom: 1.5rem;
}
.bidding-hero-badge i {
    color: #fbbf24;
}

/* Currency Info Banner */
.currency-banner {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
}
.currency-banner i {
    color: #22c55e;
}

/* Stats Bar */
.bidding-stats-bar {
    display: flex;
    justify-content: center;
    gap: 3rem;
    padding: 1.5rem;
    background: #111111;
    border-bottom: 1px solid #222;
}
.bidding-stat {
    text-align: center;
}
.bidding-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
}
.bidding-stat-label {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

/* Main Content */
.bidding-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1.5rem 4rem;
}

/* Section Header */
.bidding-section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem;
}
.bidding-section-subtitle {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.5);
    margin: 0 0 2rem;
}

/* Bid Cards Grid */
.bid-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.5rem;
}

/* Individual Bid Card */
.bid-card {
    background: #141414;
    border: 1px solid #2a2a2a;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.bid-card:hover {
    border-color: #444;
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.4);
}

/* Card Header */
.bid-card-header {
    padding: 1.5rem 1.5rem 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}
.bid-card-icon {
    width: 48px;
    height: 48px;
    background: #ffffff;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bid-card-icon i {
    font-size: 1.2rem;
    color: #000000;
}
.bid-card-title {
    flex: 1;
}
.bid-card-title h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.25rem;
}
.bid-card-title p {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
    margin: 0;
}

/* Reservation Stats */
.bid-card-stats {
    padding: 0 1.5rem;
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}
.bid-stat-box {
    flex: 1;
    background: #1e1e1e;
    border-radius: 12px;
    padding: 0.75rem;
    text-align: center;
}
.bid-stat-box-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: #ffffff;
}
.bid-stat-box-label {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.15rem;
}
.trend-up {
    color: #22c55e !important;
}
.trend-down {
    color: #ef4444 !important;
}
.trend-neutral {
    color: #fbbf24 !important;
}

/* Current Bid Display */
.bid-card-current {
    padding: 0.75rem 1.5rem;
    background: #1a1a1a;
    border-top: 1px solid #2a2a2a;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.bid-current-label {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
}
.bid-current-amount {
    font-size: 1.1rem;
    font-weight: 700;
    color: #ffffff;
}
.bid-current-amount small {
    font-size: 0.75rem;
    font-weight: 400;
    color: rgba(255,255,255,0.4);
    margin-left: 0.25rem;
}

/* Bid Number Active Bids */
.bid-active-count {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
    padding: 0.25rem 0.5rem;
    background: rgba(255,255,255,0.05);
    border-radius: 6px;
}
.bid-active-count i {
    font-size: 0.7rem;
    color: #fbbf24;
}

/* Bid Action Area */
.bid-card-action {
    padding: 1rem 1.5rem 1.5rem;
}
.bid-min-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}
.bid-min-label {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
}
.bid-min-amount {
    font-size: 0.9rem;
    font-weight: 600;
    color: #22c55e;
}

/* Bid Input Group */
.bid-input-group {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.bid-input-wrapper {
    flex: 1;
    position: relative;
}
.bid-input-wrapper .currency-prefix {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.4);
    font-size: 0.9rem;
    font-weight: 600;
}
.bid-input {
    width: 100%;
    padding: 0.75rem 0.75rem 0.75rem 2.5rem;
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 12px;
    color: #ffffff;
    font-size: 1rem;
    font-weight: 600;
    outline: none;
    transition: border-color 0.2s;
}
.bid-input:focus {
    border-color: #ffffff;
}
.bid-input::placeholder {
    color: rgba(255,255,255,0.3);
}
.bid-btn {
    padding: 0.75rem 1.5rem;
    background: #ffffff;
    color: #000000;
    border: none;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.bid-btn:hover {
    background: #e0e0e0;
    transform: translateY(-1px);
}
.bid-btn:disabled {
    background: #333;
    color: #666;
    cursor: not-allowed;
    transform: none;
}

/* Quick Bid Buttons */
.quick-bids {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.quick-bid-btn {
    padding: 0.4rem 0.75rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 8px;
    color: rgba(255,255,255,0.7);
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
}
.quick-bid-btn:hover {
    background: rgba(255,255,255,0.12);
    border-color: rgba(255,255,255,0.25);
    color: #ffffff;
}

/* Calculation Box */
.bid-calculation {
    background: #111111;
    border: 1px solid #222;
    border-radius: 20px;
    padding: 2rem;
    margin-top: 3rem;
}
.bid-calculation h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.bid-calculation h3 i {
    color: #fbbf24;
}
.bid-calculation > p {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.5);
    margin: 0 0 1.5rem;
}

/* Calculation Table */
.calc-table {
    width: 100%;
    border-collapse: collapse;
}
.calc-table th {
    text-align: left;
    padding: 0.75rem 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #2a2a2a;
}
.calc-table td {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    color: #ffffff;
    border-bottom: 1px solid #1a1a1a;
}
.calc-table tr:last-child td {
    border-bottom: none;
}
.calc-table .eur-equiv {
    color: rgba(255,255,255,0.4);
    font-size: 0.8rem;
}
.calc-highlight {
    background: rgba(255,255,255,0.03);
    border-radius: 8px;
}

/* Increment Explanation */
.increment-explain {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #222;
}
.increment-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: rgba(255,255,255,0.03);
    border-radius: 12px;
}
.increment-item i {
    color: #22c55e;
    margin-top: 0.15rem;
}
.increment-item strong {
    display: block;
    font-size: 0.85rem;
    color: #ffffff;
    margin-bottom: 0.2rem;
}
.increment-item span {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
}

/* Login CTA */
.bid-login-cta {
    text-align: center;
    padding: 2rem;
    background: #1a1a1a;
    border-radius: 16px;
    margin-top: 2rem;
}
.bid-login-cta p {
    color: rgba(255,255,255,0.7);
    margin: 0 0 1rem;
}
.bid-login-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 2rem;
    background: #ffffff;
    color: #000000;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}
.bid-login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255,255,255,0.15);
}

/* My Bids Section */
.my-bids-section {
    margin-top: 3rem;
}
.my-bids-list {
    display: grid;
    gap: 0.75rem;
}
.my-bid-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    background: #141414;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
}
.my-bid-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.my-bid-info i {
    color: rgba(255,255,255,0.5);
}
.my-bid-info span {
    font-size: 0.9rem;
    color: #ffffff;
}
.my-bid-amount {
    font-weight: 700;
    color: #ffffff;
}
.my-bid-status {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-weight: 600;
}
.my-bid-status.active {
    background: rgba(34,197,94,0.15);
    color: #22c55e;
}
.my-bid-status.outbid {
    background: rgba(239,68,68,0.15);
    color: #ef4444;
}
.my-bid-status.won {
    background: rgba(251,191,36,0.15);
    color: #fbbf24;
}

/* Responsive */
@media (max-width: 768px) {
    .bidding-hero { padding: 3rem 1rem; }
    .bidding-hero h1 { font-size: 1.75rem; }
    .bidding-stats-bar { gap: 1.5rem; flex-wrap: wrap; }
    .bid-cards-grid { grid-template-columns: 1fr; }
    .bid-input-group { flex-direction: column; }
    .bid-btn { width: 100%; text-align: center; }
    .increment-explain { grid-template-columns: 1fr; }
}
</style>

<div class="bidding-page">
    <!-- Hero -->
    <section class="bidding-hero">
        <div class="bidding-hero-content">
            <div class="bidding-hero-badge">
                <i class="fas fa-gavel"></i>
                Marketing Bidding
            </div>
            <h1>Bied op premium marketingplekken</h1>
            <p>Bekijk hoeveel reserveringen er per sectie zijn en bied om jouw salon op de beste plekken te tonen. Meer reserveringen = meer waarde.</p>

            <?php if ($showDualCurrency ?? false): ?>
            <div class="currency-banner">
                <i class="fas fa-globe"></i>
                Prijzen getoond in <?= htmlspecialchars($currency ?? 'EUR') ?> &middot; Minimale verhoging: <?= htmlspecialchars($increment['increment_formatted'] ?? '€25,00') ?>
                <?php if ($increment['show_dual'] ?? false): ?>
                    (€<?= number_format($increment['increment_eur'] ?? 25, 2, ',', '.') ?>)
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="currency-banner">
                <i class="fas fa-info-circle"></i>
                Minimale verhoging per bod: <?= htmlspecialchars($increment['increment_formatted'] ?? '€25,00') ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Stats Bar -->
    <div class="bidding-stats-bar">
        <?php
        $totalReservations = 0;
        $totalBids = 0;
        foreach ($sections ?? [] as $s) {
            $totalReservations += $s['reservation_count'] ?? 0;
            $totalBids += $s['active_bids'] ?? 0;
        }
        ?>
        <div class="bidding-stat">
            <div class="bidding-stat-value"><?= number_format($totalReservations) ?></div>
            <div class="bidding-stat-label">Reserveringen (30d)</div>
        </div>
        <div class="bidding-stat">
            <div class="bidding-stat-value"><?= count($sections ?? []) ?></div>
            <div class="bidding-stat-label">Beschikbare secties</div>
        </div>
        <div class="bidding-stat">
            <div class="bidding-stat-value"><?= number_format($totalBids) ?></div>
            <div class="bidding-stat-label">Actieve biedingen</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bidding-content">
        <h2 class="bidding-section-title">Beschikbare secties</h2>
        <p class="bidding-section-subtitle">Elke sectie toont live reserveringsdata van de afgelopen 30 dagen</p>

        <!-- Bid Cards -->
        <div class="bid-cards-grid">
            <?php foreach ($sections ?? [] as $section):
                $calc = $calculations[$section['id']] ?? [];
                $minBid = $calc['minimum_bid'] ?? [];
                $highestBid = $section['highest_bid_eur'] ?? null;
                $trend = $section['reservation_trend'] ?? 0;
                $trendClass = $trend > 0 ? 'trend-up' : ($trend < 0 ? 'trend-down' : 'trend-neutral');
                $trendIcon = $trend > 0 ? 'fa-arrow-up' : ($trend < 0 ? 'fa-arrow-down' : 'fa-minus');
                // Section name from translation key
            ?>
            <div class="bid-card" data-section-id="<?= $section['id'] ?>">
                <!-- Header -->
                <div class="bid-card-header">
                    <div class="bid-card-icon">
                        <i class="<?= htmlspecialchars($section['icon'] ?? 'fas fa-tag') ?>"></i>
                    </div>
                    <div class="bid-card-title">
                        <h3><?= htmlspecialchars($section['name_key'] ?? $section['slug']) ?></h3>
                        <p><?= htmlspecialchars($section['description_key'] ?? '') ?></p>
                    </div>
                </div>

                <!-- Reservation Stats -->
                <div class="bid-card-stats">
                    <div class="bid-stat-box">
                        <div class="bid-stat-box-value"><?= number_format($section['reservation_count'] ?? 0) ?></div>
                        <div class="bid-stat-box-label">Reserveringen</div>
                    </div>
                    <div class="bid-stat-box">
                        <div class="bid-stat-box-value <?= $trendClass ?>">
                            <i class="fas <?= $trendIcon ?>" style="font-size:0.8rem;"></i>
                            <?= abs($trend) ?>%
                        </div>
                        <div class="bid-stat-box-label">Trend (30d)</div>
                    </div>
                    <div class="bid-stat-box">
                        <div class="bid-stat-box-value"><?= $section['active_bids'] ?? 0 ?></div>
                        <div class="bid-stat-box-label">Biedingen</div>
                    </div>
                </div>

                <!-- Current Highest Bid -->
                <div class="bid-card-current">
                    <span class="bid-current-label">Huidige hoogste bod</span>
                    <?php if ($highestBid): ?>
                        <span class="bid-current-amount">
                            <?php if ($showDualCurrency ?? false): ?>
                                <?= htmlspecialchars($calc['current_highest_local'] ?? '') ?>
                                <small>(€<?= number_format($highestBid, 2, ',', '.') ?>)</small>
                            <?php else: ?>
                                €<?= number_format($highestBid, 2, ',', '.') ?>
                            <?php endif; ?>
                        </span>
                    <?php else: ?>
                        <span class="bid-current-amount" style="color: rgba(255,255,255,0.3);">Geen biedingen</span>
                    <?php endif; ?>
                </div>

                <!-- Bid Action -->
                <div class="bid-card-action">
                    <div class="bid-min-info">
                        <span class="bid-min-label">Minimaal bod</span>
                        <span class="bid-min-amount">
                            <?= htmlspecialchars($minBid['local_formatted'] ?? '€50,00') ?>
                            <?php if ($minBid['show_dual'] ?? false): ?>
                                <small style="color: rgba(255,255,255,0.4)">(<?= htmlspecialchars($minBid['eur_formatted'] ?? '') ?>)</small>
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if ($businessId ?? null): ?>
                    <form class="bid-form" data-section="<?= $section['id'] ?>" data-min-eur="<?= $minBid['eur_amount'] ?? 50 ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                        <input type="hidden" name="section_id" value="<?= $section['id'] ?>">
                        <div class="bid-input-group">
                            <div class="bid-input-wrapper">
                                <span class="currency-prefix"><?= htmlspecialchars($currencySymbol ?? '€') ?></span>
                                <input type="number" class="bid-input" name="bid_amount_local"
                                       min="<?= $minBid['local_amount'] ?? 50 ?>"
                                       step="<?= $increment['rounding_step'] ?? 5 ?>"
                                       placeholder="<?= $minBid['local_amount'] ?? 50 ?>"
                                       data-rate="<?= $minBid['eur_amount'] > 0 && $minBid['local_amount'] > 0 ? ($minBid['local_amount'] / $minBid['eur_amount']) : 1 ?>">
                                <input type="hidden" name="bid_amount_eur" value="">
                            </div>
                            <button type="submit" class="bid-btn">
                                <i class="fas fa-gavel"></i> Bied
                            </button>
                        </div>
                        <!-- Quick Bid Buttons -->
                        <div class="quick-bids">
                            <?php
                            $steps = $calc['steps'] ?? [];
                            foreach (array_slice($steps, 0, 4) as $step):
                            ?>
                            <button type="button" class="quick-bid-btn"
                                    data-eur="<?= $step['eur_amount'] ?>"
                                    data-local="<?= $step['local_amount'] ?>">
                                <?= htmlspecialchars($step['local_formatted']) ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </form>
                    <?php else: ?>
                    <a href="/business/login" class="bid-btn" style="display:block;text-align:center;text-decoration:none;">
                        <i class="fas fa-sign-in-alt"></i> Log in om te bieden
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Bid Calculation Explanation -->
        <div class="bid-calculation">
            <h3><i class="fas fa-calculator"></i> Hoe werkt de biedberekening?</h3>
            <p>Biedingen zijn gebaseerd op EUR met automatische omrekening naar jouw lokale valuta</p>

            <table class="calc-table">
                <thead>
                    <tr>
                        <th>Valuta</th>
                        <th>Minimale verhoging</th>
                        <th>Afronding</th>
                        <th>Voorbeeld startbod</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($calcExamples ?? [] as $ex):
                        $incr = $ex['increment'] ?? [];
                        $startBid = $ex['start_bid'] ?? [];
                        $roundStep = $incr['rounding_step'] ?? 5;
                    ?>
                    <tr class="<?= ($ex['is_current'] ?? false) ? 'calc-highlight' : '' ?>">
                        <td>
                            <strong><?= htmlspecialchars($ex['code']) ?></strong>
                            <span style="color:rgba(255,255,255,0.4);font-size:0.8rem;margin-left:0.25rem;"><?= htmlspecialchars($ex['name']) ?></span>
                            <?php if ($ex['is_current'] ?? false): ?>
                                <span style="color:#22c55e;font-size:0.7rem;margin-left:0.5rem;">&#9679; Jouw valuta</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($incr['increment_formatted'] ?? '') ?>
                            <?php if ($ex['code'] !== 'EUR'): ?>
                                <span class="eur-equiv">(€25,00)</span>
                            <?php endif; ?>
                        </td>
                        <td style="color:rgba(255,255,255,0.6);">
                            <?= htmlspecialchars($incr['local_symbol'] ?? $ex['code']) ?><?= number_format($roundStep, 0, ',', '.') ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($startBid['local_formatted'] ?? '') ?>
                            <?php if ($ex['code'] !== 'EUR'): ?>
                                <span class="eur-equiv">(€50,00)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Increment Explanation -->
            <div class="increment-explain">
                <div class="increment-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Basis: €25 verhoging</strong>
                        <span>Elk nieuw bod moet minimaal €25 (of equivalent) hoger zijn dan het huidige hoogste bod</span>
                    </div>
                </div>
                <div class="increment-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Slim afgerond</strong>
                        <span>Bedragen worden afgerond naar mooie ronde getallen in jouw valuta (bijv. SEK 50, PLN 25, JPY 500)</span>
                    </div>
                </div>
                <div class="increment-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>30 dagen campagne</strong>
                        <span>Elk bod geldt voor een periode van 30 dagen. Het hoogste bod krijgt de beste plek.</span>
                    </div>
                </div>
                <div class="increment-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Live wisselkoersen</strong>
                        <span>Wisselkoersen worden dagelijks bijgewerkt voor eerlijke prijzen wereldwijd</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Active Bids (if logged in) -->
        <?php if (!empty($businessBids)): ?>
        <div class="my-bids-section">
            <h2 class="bidding-section-title">Mijn biedingen</h2>
            <p class="bidding-section-subtitle">Overzicht van jouw actieve en afgelopen biedingen</p>

            <div class="my-bids-list">
                <?php foreach ($businessBids as $bid): ?>
                <div class="my-bid-item">
                    <div class="my-bid-info">
                        <i class="<?= htmlspecialchars($bid['icon'] ?? 'fas fa-tag') ?>"></i>
                        <span><?= htmlspecialchars($bid['name_key'] ?? $bid['section_slug'] ?? '') ?></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:1rem;">
                        <span class="my-bid-amount">
                            €<?= number_format($bid['bid_amount_eur'], 2, ',', '.') ?>
                            <?php if ($bid['bid_currency'] !== 'EUR'): ?>
                                <small style="color:rgba(255,255,255,0.4);">(<?= $bid['bid_currency'] ?> <?= number_format($bid['bid_amount_local'], 2, ',', '.') ?>)</small>
                            <?php endif; ?>
                        </span>
                        <span class="my-bid-status <?= htmlspecialchars($bid['status']) ?>">
                            <?= ucfirst(htmlspecialchars($bid['status'])) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Login CTA (if not logged in) -->
        <?php if (!($businessId ?? null)): ?>
        <div class="bid-login-cta">
            <p>Log in met je bedrijfsaccount om te bieden op marketingplekken</p>
            <a href="/business/login" class="bid-login-btn">
                <i class="fas fa-sign-in-alt"></i> Inloggen als bedrijf
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle bid form submissions
    document.querySelectorAll('.bid-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var localInput = form.querySelector('input[name="bid_amount_local"]');
            var eurInput = form.querySelector('input[name="bid_amount_eur"]');
            var rate = parseFloat(localInput.dataset.rate) || 1;
            var localAmount = parseFloat(localInput.value);

            if (!localAmount || localAmount <= 0) {
                alert('Voer een geldig bedrag in');
                return;
            }

            // Convert local to EUR
            var eurAmount = Math.round((localAmount / rate) * 100) / 100;
            eurInput.value = eurAmount;

            var minEur = parseFloat(form.dataset.minEur) || 0;
            if (eurAmount < minEur) {
                alert('Het bod is te laag. Minimaal: ' + minEur.toFixed(2) + ' EUR');
                return;
            }

            var btn = form.querySelector('.bid-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Bezig...';

            var formData = new FormData();
            formData.append('csrf_token', form.querySelector('input[name="csrf_token"]').value);
            formData.append('section_id', form.querySelector('input[name="section_id"]').value);
            formData.append('bid_amount_eur', eurAmount);

            fetch('/marketing/bidding', {
                method: 'POST',
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    // Update UI
                    var card = form.closest('.bid-card');
                    var currentAmount = card.querySelector('.bid-current-amount');
                    if (currentAmount) {
                        currentAmount.textContent = '€' + eurAmount.toFixed(2).replace('.', ',');
                    }
                    btn.innerHTML = '<i class="fas fa-check"></i> Bod geplaatst!';
                    btn.style.background = '#22c55e';
                    btn.style.color = '#ffffff';

                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert(data.error || 'Er is iets misgegaan');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-gavel"></i> Bied';
                }
            })
            .catch(function() {
                alert('Netwerkfout. Probeer het opnieuw.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-gavel"></i> Bied';
            });
        });
    });

    // Quick bid buttons
    document.querySelectorAll('.quick-bid-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var form = btn.closest('.bid-form');
            var input = form.querySelector('input[name="bid_amount_local"]');
            input.value = btn.dataset.local;
        });
    });
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

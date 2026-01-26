<?php ob_start(); ?>

<style>
.loyalty-hero {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark, #333));
    color: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    text-align: center;
}
.loyalty-hero h1 {
    margin: 0 0 0.5rem;
    font-size: 2rem;
}
.loyalty-hero .points-total {
    font-size: 3rem;
    font-weight: 700;
    margin: 1rem 0;
}
.loyalty-hero .points-label {
    opacity: 0.8;
    font-size: 1rem;
}

.loyalty-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.loyalty-info-card {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    border: 1px solid var(--border);
}
.loyalty-info-card i {
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 0.75rem;
}
.loyalty-info-card h4 {
    margin: 0 0 0.5rem;
    font-size: 1rem;
}
.loyalty-info-card p {
    margin: 0;
    color: var(--text-light);
    font-size: 0.9rem;
}
.loyalty-info-card .value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
}

.salon-points-card {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    border: 1px solid var(--border);
}
.salon-points-card .salon-logo {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    background: var(--secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.salon-points-card .salon-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.salon-points-card .salon-logo i {
    font-size: 1.25rem;
    color: var(--text-light);
}
.salon-points-card .salon-info {
    flex: 1;
}
.salon-points-card .salon-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}
.salon-points-card .salon-points {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
}
.salon-points-card .book-btn {
    padding: 0.5rem 1rem;
    background: var(--primary);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
}
.salon-points-card .book-btn:hover {
    opacity: 0.9;
}

.transaction-list {
    background: var(--bg-card);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--border);
}
.transaction-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid var(--border);
}
.transaction-item:last-child {
    border-bottom: none;
}
.transaction-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.transaction-icon.earn {
    background: #dcfce7;
    color: #16a34a;
}
.transaction-icon.redeem {
    background: #fee2e2;
    color: #dc2626;
}
.transaction-info {
    flex: 1;
}
.transaction-description {
    font-weight: 500;
    margin-bottom: 0.25rem;
}
.transaction-meta {
    font-size: 0.85rem;
    color: var(--text-light);
}
.transaction-points {
    font-weight: 700;
    font-size: 1.1rem;
}
.transaction-points.positive {
    color: #16a34a;
}
.transaction-points.negative {
    color: #dc2626;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: var(--text-light);
}
.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .loyalty-hero {
        padding: 1.5rem;
    }
    .loyalty-hero h1 {
        font-size: 1.5rem;
    }
    .loyalty-hero .points-total {
        font-size: 2.5rem;
    }
    .salon-points-card {
        flex-wrap: wrap;
    }
    .salon-points-card .book-btn {
        width: 100%;
        text-align: center;
        margin-top: 0.5rem;
    }
}
</style>

<div class="loyalty-hero">
    <h1><i class="fas fa-star"></i> <?= $translations['loyalty_points'] ?? 'Loyaliteitspunten' ?></h1>
    <p class="points-label"><?= $translations['total_points'] ?? 'Totaal punten' ?></p>
    <div class="points-total"><?= number_format($totalPoints) ?></div>
</div>

<!-- How it works -->
<div class="loyalty-info-grid">
    <div class="loyalty-info-card">
        <i class="fas fa-calendar-check"></i>
        <h4><?= $translations['earn_booking'] ?? 'Boek een afspraak' ?></h4>
        <div class="value">+<?= $pointsPerBooking ?></div>
        <p><?= $translations['points_per_booking'] ?? 'punten per voltooide boeking' ?></p>
    </div>
    <div class="loyalty-info-card">
        <i class="fas fa-comment"></i>
        <h4><?= $translations['leave_review'] ?? 'Schrijf een review' ?></h4>
        <div class="value">+<?= $pointsPerReview ?></div>
        <p><?= $translations['points_per_review'] ?? 'punten per review' ?></p>
    </div>
    <div class="loyalty-info-card">
        <i class="fas fa-percent"></i>
        <h4><?= $translations['get_discount'] ?? 'Krijg korting' ?></h4>
        <div class="value"><?= $pointsPerPercent ?> = 1%</div>
        <p><?= $translations['points_equals_percent'] ?? 'punten = procent korting' ?></p>
    </div>
</div>

<!-- Points per salon -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-store"></i> <?= $translations['points_per_salon'] ?? 'Punten per salon' ?></h3>
    </div>

    <?php if (empty($balances)): ?>
    <div class="empty-state">
        <i class="fas fa-star"></i>
        <p><?= $translations['no_points_yet'] ?? 'Je hebt nog geen punten verdiend. Boek een afspraak om te beginnen!' ?></p>
        <a href="/search" class="btn btn-primary" style="margin-top:1rem">
            <i class="fas fa-search"></i> <?= $translations['search_salons'] ?? 'Zoek salons' ?>
        </a>
    </div>
    <?php else: ?>
    <?php foreach ($balances as $balance): ?>
    <div class="salon-points-card">
        <div class="salon-logo">
            <?php if (!empty($balance['logo'])): ?>
                <img src="<?= htmlspecialchars($balance['logo']) ?>" alt="">
            <?php else: ?>
                <i class="fas fa-store"></i>
            <?php endif; ?>
        </div>
        <div class="salon-info">
            <div class="salon-name"><?= htmlspecialchars($balance['company_name']) ?></div>
            <div class="salon-points"><?= number_format($balance['total_points']) ?> <?= $translations['points'] ?? 'punten' ?></div>
        </div>
        <a href="/business/<?= htmlspecialchars($balance['slug']) ?>" class="book-btn">
            <i class="fas fa-calendar-plus"></i> <?= $translations['book'] ?? 'Boek' ?>
        </a>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Transaction history -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history"></i> <?= $translations['transaction_history'] ?? 'Transactiegeschiedenis' ?></h3>
    </div>

    <?php if (empty($transactions)): ?>
    <div class="empty-state">
        <i class="fas fa-receipt"></i>
        <p><?= $translations['no_transactions'] ?? 'Nog geen transacties' ?></p>
    </div>
    <?php else: ?>
    <div class="transaction-list">
        <?php foreach ($transactions as $tx): ?>
        <div class="transaction-item">
            <div class="transaction-icon <?= $tx['points'] > 0 ? 'earn' : 'redeem' ?>">
                <?php if ($tx['transaction_type'] === 'earn_booking'): ?>
                    <i class="fas fa-calendar-check"></i>
                <?php elseif ($tx['transaction_type'] === 'earn_review'): ?>
                    <i class="fas fa-comment"></i>
                <?php elseif ($tx['transaction_type'] === 'redeem'): ?>
                    <i class="fas fa-gift"></i>
                <?php else: ?>
                    <i class="fas fa-exchange-alt"></i>
                <?php endif; ?>
            </div>
            <div class="transaction-info">
                <div class="transaction-description">
                    <?php
                    $typeLabels = [
                        'earn_booking' => $translations['earned_from_booking'] ?? 'Verdiend door boeking',
                        'earn_review' => $translations['earned_from_review'] ?? 'Verdiend door review',
                        'redeem' => $translations['redeemed_discount'] ?? 'Ingewisseld voor korting',
                        'expire' => $translations['points_expired'] ?? 'Punten verlopen',
                        'adjustment' => $translations['adjustment'] ?? 'Aanpassing'
                    ];
                    echo htmlspecialchars($typeLabels[$tx['transaction_type'] ?? ''] ?? ($translations['unknown'] ?? 'Onbekend'));
                    ?>
                </div>
                <div class="transaction-meta">
                    <?= htmlspecialchars($tx['company_name'] ?? '') ?>
                    <?php if (!empty($tx['booking_number'])): ?>
                        &middot; #<?= htmlspecialchars($tx['booking_number']) ?>
                    <?php endif; ?>
                    &middot; <?= !empty($tx['created_at']) ? date('d-m-Y H:i', strtotime($tx['created_at'])) : '-' ?>
                </div>
            </div>
            <div class="transaction-points <?= ($tx['points'] ?? 0) > 0 ? 'positive' : 'negative' ?>">
                <?= $tx['points'] > 0 ? '+' : '' ?><?= number_format($tx['points']) ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Info box -->
<div class="card" style="background:linear-gradient(135deg,#fef3c7,#fde68a);border-color:#f59e0b;">
    <h4 style="margin:0 0 0.75rem;color:#92400e;"><i class="fas fa-info-circle"></i> <?= $translations['how_points_work'] ?? 'Hoe werken punten?' ?></h4>
    <ul style="margin:0;padding-left:1.25rem;color:#92400e;">
        <li><?= $translations['points_info_1'] ?? 'Punten worden per salon bijgehouden' ?></li>
        <li><?= $translations['points_info_2'] ?? 'Wissel punten in bij het afrekenen voor korting' ?></li>
        <li><?= $translations['points_info_3'] ?? 'Salons bepalen zelf het max. aantal inwisselbare punten' ?></li>
        <li><?= $translations['points_info_4'] ?? 'Platformkosten (€1,75) blijven altijd van toepassing' ?></li>
    </ul>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

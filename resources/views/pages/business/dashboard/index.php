<?php ob_start(); ?>

<?php
// Calculate trial info
$trialEndsAt = !empty($business['trial_ends_at']) ? strtotime($business['trial_ends_at']) : 0;
$daysRemaining = max(0, ceil(($trialEndsAt - time()) / 86400));
$isOnTrial = $business['subscription_status'] === 'trial' && $daysRemaining > 0;
$trialExpired = $business['subscription_status'] === 'trial' && $daysRemaining <= 0;
$subscriptionPrice = (float)($business['subscription_price'] ?? 99.99);
$welcomeDiscount = (float)($business['welcome_discount'] ?? 0);
$finalPrice = max(0, $subscriptionPrice - $welcomeDiscount);
?>

<style>
/* Mobile-first dashboard styles */
.dash-banner {
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    color: white;
}
.dash-banner-trial {
    background: linear-gradient(135deg, #404040, #1d4ed8);
}
.dash-banner-expired {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
}
.dash-banner-active {
    background: linear-gradient(135deg, #333333, #000000);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
}
.banner-content {
    flex: 1;
}
.banner-content h3 {
    margin: 0;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.banner-content p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
    font-size: 0.9rem;
    line-height: 1.5;
}
.banner-countdown {
    text-align: center;
    background: rgba(255,255,255,0.15);
    padding: 0.75rem 1rem;
    border-radius: 12px;
    margin-top: 1rem;
}
.banner-countdown .number {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
}
.banner-countdown .label {
    font-size: 0.8rem;
    opacity: 0.9;
}
.discount-tag {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}
.banner-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #ffffff;
    color: #dc2626;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    margin-top: 1rem;
}

/* Stats Grid - 2x2 on mobile, 4 columns on desktop */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
}
.stat-card {
    background: var(--white);
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.stat-card i {
    font-size: 1.5rem;
}
.stat-card h3 {
    margin: 0.35rem 0;
    font-size: 1.5rem;
}
.stat-card p {
    margin: 0;
    font-size: 0.8rem;
    color: var(--text-light);
}

/* Quick Actions - Stack on mobile */
.actions-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1rem;
}
.action-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--white);
    border-radius: 12px;
    padding: 1rem;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: transform 0.2s;
}
.action-card:active {
    transform: scale(0.98);
}
.action-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.action-icon i {
    color: white;
    font-size: 1.1rem;
}
.action-text h4 {
    margin: 0;
    font-size: 1rem;
}
.action-text p {
    margin: 0;
    font-size: 0.8rem;
    color: var(--text-light);
}

/* Bookings Grid - Stack on mobile */
.bookings-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.dash-card {
    background: var(--white);
    border-radius: 16px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.dash-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.dash-card-title {
    font-size: 1rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.dash-card-title i {
    color: var(--primary);
}
.btn-small {
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    background: var(--secondary);
    color: var(--text);
    border-radius: 8px;
    text-decoration: none;
}
.booking-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border);
}
.booking-item:last-child {
    border-bottom: none;
}
.booking-info strong {
    font-size: 0.95rem;
}
.booking-info small {
    display: block;
    color: var(--text-light);
    font-size: 0.8rem;
}
.booking-status {
    padding: 0.25rem 0.6rem;
    border-radius: 15px;
    font-size: 0.7rem;
    color: white;
    white-space: nowrap;
}
.status-confirmed {
    background: var(--success);
}
.status-pending {
    background: var(--warning);
}
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--text-light);
}
.empty-state i {
    font-size: 2rem;
    opacity: 0.5;
    margin-bottom: 0.5rem;
}
.empty-state p {
    margin: 0;
}

/* Desktop enhancements */
@media (min-width: 768px) {
    .dash-banner {
        padding: 1.5rem;
    }
    .banner-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .banner-countdown {
        margin-top: 0;
        padding: 1rem 1.5rem;
    }
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    .stat-card {
        padding: 1.25rem;
    }
    .stat-card i {
        font-size: 2rem;
    }
    .stat-card h3 {
        font-size: 2rem;
    }
    .actions-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    .action-card {
        padding: 1.25rem;
    }
    .action-icon {
        width: 50px;
        height: 50px;
    }
    .bookings-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    .dash-card {
        padding: 1.25rem;
    }
}
</style>

<!-- Trial/Subscription Banner -->
<?php if ($isOnTrial): ?>
    <div class="dash-banner dash-banner-trial">
        <div class="banner-flex">
            <div class="banner-content">
                <h3><i class="fas fa-gift"></i> Proefperiode Actief</h3>
                <p>
                    Nog <strong><?= $daysRemaining ?> dag<?= $daysRemaining !== 1 ? 'en' : '' ?></strong> gratis.
                    <?php if ($welcomeDiscount > 0): ?>
                        Daarna eenmalig &euro;<?= number_format($finalPrice, 2, ',', '.') ?>
                        <span style="text-decoration:line-through;opacity:0.7">&euro;<?= number_format($subscriptionPrice, 2, ',', '.') ?></span>
                        <br><span class="discount-tag">-&euro;<?= number_format($welcomeDiscount, 2, ',', '.') ?> welkomstkorting</span>
                    <?php else: ?>
                        Daarna eenmalig &euro;<?= number_format($subscriptionPrice, 2, ',', '.') ?>
                    <?php endif; ?>
                </p>
            </div>
            <div class="banner-countdown">
                <p class="number"><?= $daysRemaining ?></p>
                <p class="label">dagen over</p>
            </div>
        </div>
    </div>
<?php elseif ($trialExpired): ?>
    <div class="dash-banner dash-banner-expired">
        <h3 style="margin:0;display:flex;align-items:center;gap:0.5rem">
            <i class="fas fa-exclamation-triangle"></i> Proefperiode Verlopen
        </h3>
        <p style="margin:0.5rem 0 0 0">
            Je proefperiode is verlopen. Activeer je abonnement om door te gaan.
            <?php if ($welcomeDiscount > 0): ?>
                Eenmalig <strong>&euro;<?= number_format($finalPrice, 2, ',', '.') ?></strong>
            <?php else: ?>
                Eenmalig <strong>&euro;<?= number_format($subscriptionPrice, 2, ',', '.') ?></strong>
            <?php endif; ?>
        </p>
        <a href="/business/subscription" class="banner-btn">
            <i class="fas fa-credit-card"></i> Abonnement Activeren
        </a>
    </div>
<?php elseif ($business['subscription_status'] === 'active'): ?>
    <div class="dash-banner dash-banner-active">
        <i class="fas fa-check-circle" style="font-size:1.5rem"></i>
        <span>Abonnement actief</span>
    </div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-calendar-check" style="color:var(--primary)"></i>
        <h3><?= $stats['todayBookings'] ?></h3>
        <p>Vandaag</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-calendar-alt" style="color:var(--success)"></i>
        <h3><?= $stats['totalBookings'] ?></h3>
        <p>Totaal Boekingen</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-euro-sign" style="color:#fd7e14"></i>
        <h3>&euro;<?= number_format($stats['totalRevenue'], 0, ',', '.') ?></h3>
        <p>Omzet</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-star" style="color:#f5c518"></i>
        <h3><?= number_format($stats['avgRating'], 1) ?></h3>
        <p>Beoordeling</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="actions-grid">
    <a href="/business/bookings" class="action-card">
        <div class="action-icon" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark))">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="action-text">
            <h4>Boekingen</h4>
            <p>Beheer afspraken</p>
        </div>
    </a>
    <a href="/business/services" class="action-card">
        <div class="action-icon" style="background:linear-gradient(135deg,var(--success),#000000)">
            <i class="fas fa-cut"></i>
        </div>
        <div class="action-text">
            <h4>Diensten</h4>
            <p>Beheer diensten & prijzen</p>
        </div>
    </a>
    <a href="/business/website" class="action-card">
        <div class="action-icon" style="background:linear-gradient(135deg,#000000,#262626)">
            <i class="fas fa-globe"></i>
        </div>
        <div class="action-text">
            <h4>Webpagina</h4>
            <p>Bewerk je pagina</p>
        </div>
    </a>
</div>

<!-- Bookings -->
<div class="bookings-grid">
    <!-- Today's Bookings -->
    <div class="dash-card">
        <div class="dash-card-header">
            <h3 class="dash-card-title"><i class="fas fa-calendar-day"></i> Vandaag</h3>
            <a href="/business/calendar" class="btn-small">Bekijk Agenda</a>
        </div>
        <?php if (empty($todayBookings)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-check"></i>
                <p>Geen afspraken voor vandaag</p>
            </div>
        <?php else: ?>
            <div style="max-height:280px;overflow-y:auto">
                <?php foreach ($todayBookings as $booking): ?>
                    <div class="booking-item">
                        <div class="booking-info">
                            <strong><?= date('H:i', strtotime($booking['appointment_time'])) ?></strong>
                            <span style="color:var(--text-light)"> - <?= htmlspecialchars($booking['first_name'] ?? $booking['guest_name'] ?? 'Gast') ?></span>
                            <small><?= htmlspecialchars($booking['service_name']) ?></small>
                        </div>
                        <span class="booking-status <?= $booking['status'] === 'confirmed' ? 'status-confirmed' : 'status-pending' ?>">
                            <?= $booking['status'] === 'confirmed' ? 'Bevestigd' : 'Wacht' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Bookings -->
    <div class="dash-card">
        <div class="dash-card-header">
            <h3 class="dash-card-title"><i class="fas fa-history"></i> Recente Boekingen</h3>
            <a href="/business/bookings" class="btn-small">Bekijk Alle</a>
        </div>
        <?php if (empty($recentBookings)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>Nog geen boekingen</p>
            </div>
        <?php else: ?>
            <div style="max-height:280px;overflow-y:auto">
                <?php foreach (array_slice($recentBookings, 0, 5) as $booking): ?>
                    <div class="booking-item">
                        <div class="booking-info">
                            <strong><?= htmlspecialchars($booking['first_name'] ?? $booking['guest_name'] ?? 'Gast') ?></strong>
                            <small><?= htmlspecialchars($booking['service_name']) ?></small>
                        </div>
                        <small style="color:var(--text-light)"><?= date('d-m-Y', strtotime($booking['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

<?php ob_start(); ?>

<?php
// Calculate trial info
$trialEndsAt = !empty($business['trial_ends_at']) ? strtotime($business['trial_ends_at']) : 0;
$daysRemaining = max(0, ceil(($trialEndsAt - time()) / 86400));
$isOnTrial = $business['subscription_status'] === 'trial' && $daysRemaining > 0;
$trialExpired = $business['subscription_status'] === 'trial' && $daysRemaining <= 0;
$isEarlyAdopter = !empty($business['is_early_adopter']);
$subscriptionPrice = (float)($business['subscription_price'] ?? 99.99);
// Early adopters don't get welcome discount applied - they pay the early bird price
$welcomeDiscount = $isEarlyAdopter ? 0 : (float)($business['welcome_discount'] ?? 0);
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

<?php
// Check if business needs verification (no KVK and not yet verified)
$needsVerification = empty($business['kvk_number']) && empty($business['is_verified']);
?>
<?php if ($needsVerification): ?>
    <div class="dash-banner dash-banner-pending" style="background:linear-gradient(135deg,#f59e0b,#d97706);margin-bottom:1rem">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:50px;height:50px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fas fa-lock" style="font-size:1.5rem"></i>
            </div>
            <div style="flex:1">
                <h3 style="margin:0;display:flex;align-items:center;gap:0.5rem;font-size:1.1rem">
                    <i class="fas fa-clock"></i> Verificatie in behandeling
                </h3>
                <p style="margin:0.5rem 0 0 0;opacity:0.95;font-size:0.9rem;line-height:1.5">
                    Je bedrijf wordt binnen <strong>24 uur</strong> geverifieerd door ons team.
                    Tot die tijd kun je geen boekingen ontvangen.
                </p>
                <p style="margin:0.75rem 0 0 0;opacity:0.85;font-size:0.85rem">
                    <i class="fas fa-info-circle"></i> Tip: Voeg je KVK-nummer toe voor directe verificatie
                </p>
            </div>
        </div>
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

<!-- Glamori AI Manager Widget -->
<?php if (!empty($aiManager)): ?>
<div class="ai-manager-widget" style="margin-top:1.5rem">
    <div class="dash-card" style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 100%);color:white;overflow:hidden;position:relative">
        <div class="ai-glow" style="position:absolute;top:-50px;right:-50px;width:150px;height:150px;background:radial-gradient(circle,rgba(139,92,246,0.3) 0%,transparent 70%);pointer-events:none"></div>
        <div class="dash-card-header" style="position:relative">
            <h3 class="dash-card-title" style="color:white">
                <i class="fas fa-robot" style="color:#a78bfa"></i>
                Glamori Manager
            </h3>
            <span style="background:rgba(255,255,255,0.15);padding:0.25rem 0.5rem;border-radius:6px;font-size:0.7rem;color:#c4b5fd">
                <i class="fas fa-sparkles"></i> AI
            </span>
        </div>

        <p style="margin:0 0 1rem 0;font-size:0.95rem;opacity:0.95"><?= htmlspecialchars($aiManager['greeting'] ?? 'Welkom!') ?></p>

        <!-- Stats Row -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;margin-bottom:1rem">
            <div style="background:rgba(255,255,255,0.1);padding:0.75rem;border-radius:10px;text-align:center">
                <div style="font-size:1.25rem;font-weight:bold"><?= $aiManager['today']['total_bookings'] ?? 0 ?></div>
                <div style="font-size:0.7rem;opacity:0.8">Vandaag</div>
            </div>
            <div style="background:rgba(255,255,255,0.1);padding:0.75rem;border-radius:10px;text-align:center">
                <div style="font-size:1.25rem;font-weight:bold">&euro;<?= number_format($aiManager['week']['revenue'] ?? 0, 0, ',', '.') ?></div>
                <div style="font-size:0.7rem;opacity:0.8">Deze week</div>
            </div>
            <div style="background:rgba(255,255,255,0.1);padding:0.75rem;border-radius:10px;text-align:center">
                <div style="font-size:1.25rem;font-weight:bold">
                    <?php
                    $change = $aiManager['week']['revenue_change_percent'] ?? 0;
                    $arrow = $change >= 0 ? '<i class="fas fa-arrow-up" style="color:#4ade80"></i>' : '<i class="fas fa-arrow-down" style="color:#f87171"></i>';
                    echo $arrow . ' ' . abs($change) . '%';
                    ?>
                </div>
                <div style="font-size:0.7rem;opacity:0.8">vs vorige week</div>
            </div>
        </div>

        <!-- Tips -->
        <?php if (!empty($aiManager['tips'])): ?>
        <div style="margin-bottom:1rem">
            <?php foreach (array_slice($aiManager['tips'], 0, 2) as $tip): ?>
            <div style="display:flex;align-items:flex-start;gap:0.5rem;background:rgba(255,255,255,0.08);padding:0.6rem 0.75rem;border-radius:8px;margin-bottom:0.5rem;font-size:0.85rem">
                <?php
                $iconColor = '#fbbf24';
                if ($tip['priority'] === 'high') $iconColor = '#f87171';
                elseif ($tip['priority'] === 'low') $iconColor = '#4ade80';
                ?>
                <i class="fas fa-lightbulb" style="color:<?= $iconColor ?>;flex-shrink:0;margin-top:2px"></i>
                <span style="opacity:0.95"><?= htmlspecialchars($tip['message']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;font-size:0.8rem;opacity:0.9">
            <?php if ($aiManager['unanswered_reviews'] ?? 0 > 0): ?>
            <a href="/business/reviews" style="display:inline-flex;align-items:center;gap:0.35rem;background:rgba(251,191,36,0.2);color:#fcd34d;padding:0.35rem 0.6rem;border-radius:6px;text-decoration:none">
                <i class="fas fa-star"></i>
                <?= $aiManager['unanswered_reviews'] ?> onbeantwoorde review<?= $aiManager['unanswered_reviews'] > 1 ? 's' : '' ?>
            </a>
            <?php endif; ?>
            <?php if (!empty($aiManager['popular_service'])): ?>
            <span style="display:inline-flex;align-items:center;gap:0.35rem;background:rgba(74,222,128,0.2);color:#86efac;padding:0.35rem 0.6rem;border-radius:6px">
                <i class="fas fa-fire"></i>
                Populair: <?= htmlspecialchars($aiManager['popular_service']) ?>
            </span>
            <?php endif; ?>
            <?php if (count($aiManager['notifications'] ?? []) > 0): ?>
            <span style="display:inline-flex;align-items:center;gap:0.35rem;background:rgba(139,92,246,0.3);color:#c4b5fd;padding:0.35rem 0.6rem;border-radius:6px">
                <i class="fas fa-bell"></i>
                <?= count($aiManager['notifications']) ?> nieuwe melding<?= count($aiManager['notifications']) > 1 ? 'en' : '' ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- Expand Link -->
        <div style="margin-top:1rem;padding-top:0.75rem;border-top:1px solid rgba(255,255,255,0.1)">
            <a href="/business/insights" style="display:flex;justify-content:space-between;align-items:center;color:#c4b5fd;text-decoration:none;font-size:0.85rem">
                <span>Bekijk alle inzichten</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- New Registration Welcome Popup -->
<?php if (!empty($isNewRegistration)): ?>
<div class="welcome-popup-overlay" id="welcomePopup">
    <div class="welcome-popup">
        <div class="welcome-popup-header">
            <div class="welcome-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Welkom bij GlamourSchedule!</h2>
            <p>Je bedrijf is succesvol geregistreerd</p>
        </div>

        <div class="welcome-popup-body">
            <h3>Voltooi je profiel</h3>
            <p class="subtitle">Zorg dat klanten je kunnen vinden door je profiel compleet te maken</p>

            <div class="completion-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $profileCompletion['percentage'] ?>%"></div>
                </div>
                <span class="progress-text"><?= $profileCompletion['completed'] ?>/<?= $profileCompletion['total'] ?> voltooid</span>
            </div>

            <div class="completion-checklist">
                <?php foreach ($profileCompletion['items'] as $key => $item): ?>
                    <a href="<?= $item['url'] ?>" class="checklist-item <?= $item['done'] ? 'done' : '' ?>">
                        <div class="checklist-icon">
                            <i class="fas <?= $item['done'] ? 'fa-check-circle' : 'fa-circle' ?>"></i>
                        </div>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="welcome-popup-footer">
            <button type="button" class="btn-start" onclick="closeWelcomePopup()">
                <i class="fas fa-rocket"></i> Aan de slag!
            </button>
            <p class="skip-text">
                <a href="#" onclick="closeWelcomePopup(); return false;">Later voltooien</a>
            </p>
        </div>
    </div>
</div>

<style>
.welcome-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 1rem;
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.welcome-popup {
    background: var(--white);
    border-radius: 24px;
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideUp 0.4s ease;
}
@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.welcome-popup-header {
    background: linear-gradient(135deg, #000000, #333333);
    color: white;
    padding: 2rem;
    text-align: center;
    border-radius: 24px 24px 0 0;
}
.welcome-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}
.welcome-icon i {
    font-size: 2.5rem;
    color: #4ade80;
}
.welcome-popup-header h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}
.welcome-popup-header p {
    margin: 0;
    opacity: 0.9;
}
.welcome-popup-body {
    padding: 1.5rem;
}
.welcome-popup-body h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.2rem;
    color: var(--text);
}
.welcome-popup-body .subtitle {
    margin: 0 0 1.25rem 0;
    color: var(--text-light);
    font-size: 0.9rem;
}
.completion-progress {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.progress-bar {
    flex: 1;
    height: 8px;
    background: var(--secondary);
    border-radius: 4px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #000000, #333333);
    border-radius: 4px;
    transition: width 0.5s ease;
}
.progress-text {
    font-size: 0.85rem;
    color: var(--text-light);
    white-space: nowrap;
}
.completion-checklist {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.checklist-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    background: var(--secondary);
    border-radius: 12px;
    text-decoration: none;
    color: var(--text);
    transition: all 0.2s;
}
.checklist-item:hover {
    background: #e5e5e5;
    transform: translateX(4px);
}
.checklist-item.done {
    background: #f0fdf4;
}
.checklist-item.done .checklist-icon i {
    color: #22c55e;
}
.checklist-item .checklist-icon {
    width: 24px;
    text-align: center;
}
.checklist-item .checklist-icon i {
    font-size: 1.1rem;
    color: #d1d5db;
}
.checklist-item span {
    flex: 1;
    font-size: 0.95rem;
}
.checklist-item .fa-chevron-right {
    color: var(--text-light);
    font-size: 0.8rem;
}
.welcome-popup-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border);
    text-align: center;
}
.btn-start {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #000000, #333333);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: transform 0.2s;
}
.btn-start:hover {
    transform: translateY(-2px);
}
.skip-text {
    margin: 1rem 0 0 0;
    font-size: 0.9rem;
}
.skip-text a {
    color: var(--text-light);
    text-decoration: none;
}
.skip-text a:hover {
    color: var(--text);
}
</style>

<script>
function closeWelcomePopup() {
    const popup = document.getElementById('welcomePopup');
    popup.style.animation = 'fadeOut 0.3s ease forwards';
    setTimeout(() => popup.remove(), 300);
}

// Add fadeOut animation
const style = document.createElement('style');
style.textContent = '@keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }';
document.head.appendChild(style);

// Close on overlay click
document.getElementById('welcomePopup')?.addEventListener('click', function(e) {
    if (e.target === this) closeWelcomePopup();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeWelcomePopup();
});
</script>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

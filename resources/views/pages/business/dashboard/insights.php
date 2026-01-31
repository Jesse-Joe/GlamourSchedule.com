<?php ob_start(); ?>

<style>
    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .insight-card {
        background: var(--white);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .insight-card-dark {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
    }

    .insight-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .insight-title {
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .insight-title i {
        opacity: 0.7;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .stat-item {
        text-align: center;
        padding: 1rem;
        background: rgba(0,0,0,0.03);
        border-radius: 12px;
    }

    .insight-card-dark .stat-item {
        background: rgba(255,255,255,0.1);
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.8rem;
        opacity: 0.7;
        margin-top: 0.25rem;
    }

    .stat-change {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .stat-change.positive {
        color: #10b981;
    }

    .stat-change.negative {
        color: #ef4444;
    }

    .insight-card-dark .stat-change.positive {
        color: #4ade80;
    }

    .insight-card-dark .stat-change.negative {
        color: #f87171;
    }

    .tips-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .tip-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--secondary);
        border-radius: 12px;
        border-left: 4px solid;
    }

    .tip-item.high {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .tip-item.medium {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .tip-item.low {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .tip-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .tip-item.high .tip-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .tip-item.medium .tip-icon {
        background: #fef3c7;
        color: #d97706;
    }

    .tip-item.low .tip-icon {
        background: #dcfce7;
        color: #16a34a;
    }

    .tip-content {
        flex: 1;
    }

    .tip-message {
        font-size: 0.9rem;
        color: var(--text);
    }

    .tip-action {
        font-size: 0.8rem;
        color: var(--primary);
        text-decoration: none;
        margin-top: 0.5rem;
        display: inline-block;
    }

    .notification-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        max-height: 400px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--secondary);
        border-radius: 8px;
    }

    .notification-item.unread {
        background: #eff6ff;
        border-left: 3px solid var(--primary);
    }

    .notification-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-message {
        font-size: 0.85rem;
        color: var(--text);
    }

    .notification-time {
        font-size: 0.75rem;
        color: var(--text-light);
        margin-top: 0.25rem;
    }

    .services-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .service-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: var(--secondary);
        border-radius: 8px;
    }

    .service-name {
        font-weight: 500;
        font-size: 0.9rem;
    }

    .service-stats {
        text-align: right;
        font-size: 0.8rem;
    }

    .service-count {
        font-weight: 600;
        color: var(--primary);
    }

    .service-revenue {
        color: var(--text-light);
    }

    .customers-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        text-align: center;
    }

    .customer-stat {
        padding: 1rem;
        background: var(--secondary);
        border-radius: 12px;
    }

    .customer-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .customer-label {
        font-size: 0.75rem;
        color: var(--text-light);
        margin-top: 0.25rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--text-light);
    }

    .empty-state i {
        font-size: 2rem;
        opacity: 0.3;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
        .stat-grid {
            grid-template-columns: 1fr 1fr;
        }
        .customers-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header" style="margin-bottom:1.5rem">
    <h1 style="font-size:1.5rem;margin:0;display:flex;align-items:center;gap:0.5rem">
        <i class="fas fa-chart-line" style="color:var(--primary)"></i>
        Inzichten & Statistieken
    </h1>
    <p style="color:var(--text-light);margin:0.5rem 0 0 0">Bekijk hoe je salon presteert</p>
</div>

<!-- Overzicht Stats -->
<div class="insights-grid">
    <!-- Vandaag -->
    <div class="insight-card insight-card-dark">
        <div class="insight-header">
            <h3 class="insight-title"><i class="fas fa-calendar-day"></i> Vandaag</h3>
        </div>
        <div class="stat-grid">
            <div class="stat-item">
                <div class="stat-value"><?= $stats['today']['total_bookings'] ?? 0 ?></div>
                <div class="stat-label">Afspraken</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">&euro;<?= number_format($stats['today']['revenue'] ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label">Omzet</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $stats['today']['completed'] ?? 0 ?></div>
                <div class="stat-label">Voltooid</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $stats['today']['upcoming'] ?? 0 ?></div>
                <div class="stat-label">Nog te gaan</div>
            </div>
        </div>
    </div>

    <!-- Deze Week -->
    <div class="insight-card">
        <div class="insight-header">
            <h3 class="insight-title"><i class="fas fa-calendar-week"></i> Deze Week</h3>
        </div>
        <div class="stat-grid">
            <div class="stat-item">
                <div class="stat-value"><?= $stats['week']['total_bookings'] ?? 0 ?></div>
                <div class="stat-label">Boekingen</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">&euro;<?= number_format($stats['week']['revenue'] ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label">Omzet</div>
                <?php $change = $stats['week']['revenue_change_percent'] ?? 0; ?>
                <div class="stat-change <?= $change >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $change >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($change) ?>% vs vorige week
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $stats['week']['completed'] ?? 0 ?></div>
                <div class="stat-label">Voltooid</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $stats['week']['cancelled'] ?? 0 ?></div>
                <div class="stat-label">Geannuleerd</div>
            </div>
        </div>
    </div>

    <!-- Deze Maand -->
    <div class="insight-card">
        <div class="insight-header">
            <h3 class="insight-title"><i class="fas fa-calendar-alt"></i> Deze Maand</h3>
        </div>
        <div class="stat-grid">
            <div class="stat-item">
                <div class="stat-value"><?= $stats['month']['total_bookings'] ?? 0 ?></div>
                <div class="stat-label">Boekingen</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">&euro;<?= number_format($stats['month']['revenue'] ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label">Omzet</div>
                <?php $mchange = $stats['month']['revenue_change_percent'] ?? 0; ?>
                <div class="stat-change <?= $mchange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $mchange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($mchange) ?>% vs vorige maand
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-value">&euro;<?= number_format($stats['month']['net_revenue'] ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label">Netto Omzet</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $stats['month']['no_shows'] ?? 0 ?></div>
                <div class="stat-label">No-shows</div>
            </div>
        </div>
    </div>
</div>

<div class="insights-grid">
    <!-- Tips -->
    <div class="insight-card">
        <div class="insight-header">
            <h3 class="insight-title"><i class="fas fa-lightbulb"></i> Tips voor jou</h3>
        </div>
        <?php if (empty($tips)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>Alles ziet er goed uit!</p>
            </div>
        <?php else: ?>
            <div class="tips-list">
                <?php foreach ($tips as $tip): ?>
                    <div class="tip-item <?= $tip['priority'] ?>">
                        <div class="tip-icon">
                            <i class="fas fa-<?= $tip['icon'] ?? 'lightbulb' ?>"></i>
                        </div>
                        <div class="tip-content">
                            <div class="tip-message"><?= htmlspecialchars($tip['message']) ?></div>
                            <?php if (!empty($tip['action'])): ?>
                                <?php
                                $actionLinks = [
                                    'respond_reviews' => '/business/reviews',
                                    'complete_profile' => '/business/profile',
                                    'add_photos' => '/business/photos',
                                    'add_services' => '/business/services',
                                    'upgrade_subscription' => '/business/subscription',
                                    'update_profile' => '/business/website',
                                    'create_promotion' => '/business/boost'
                                ];
                                $link = $actionLinks[$tip['action']] ?? '#';
                                ?>
                                <a href="<?= $link ?>" class="tip-action">Actie ondernemen <i class="fas fa-arrow-right"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Klanten -->
    <div class="insight-card">
        <div class="insight-header">
            <h3 class="insight-title"><i class="fas fa-users"></i> Klanten deze maand</h3>
        </div>
        <div class="customers-grid">
            <div class="customer-stat">
                <div class="customer-value"><?= $stats['customers']['unique_this_month'] ?? 0 ?></div>
                <div class="customer-label">Unieke klanten</div>
            </div>
            <div class="customer-stat">
                <div class="customer-value"><?= $stats['customers']['new_customers'] ?? 0 ?></div>
                <div class="customer-label">Nieuwe klanten</div>
            </div>
            <div class="customer-stat">
                <div class="customer-value"><?= $stats['customers']['returning_customers'] ?? 0 ?></div>
                <div class="customer-label">Terugkerend</div>
            </div>
        </div>
        <?php if (($stats['customers']['retention_rate'] ?? 0) > 0): ?>
            <div style="text-align:center;margin-top:1rem;padding:0.75rem;background:var(--secondary);border-radius:8px">
                <span style="font-size:0.85rem;color:var(--text-light)">Retentie:</span>
                <span style="font-weight:600;color:var(--primary)"><?= $stats['customers']['retention_rate'] ?>%</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Populaire Diensten -->
    <div class="insight-card">
        <div class="insight-header">
            <h3 class="insight-title"><i class="fas fa-fire"></i> Populaire Diensten</h3>
        </div>
        <?php if (empty($stats['services']['popular_services'])): ?>
            <div class="empty-state">
                <i class="fas fa-cut"></i>
                <p>Nog geen data beschikbaar</p>
            </div>
        <?php else: ?>
            <div class="services-list">
                <?php foreach ($stats['services']['popular_services'] as $service): ?>
                    <div class="service-item">
                        <span class="service-name"><?= htmlspecialchars($service['name']) ?></span>
                        <div class="service-stats">
                            <div class="service-count"><?= $service['booking_count'] ?>x</div>
                            <div class="service-revenue">&euro;<?= number_format($service['revenue'], 0, ',', '.') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (($stats['services']['average_booking_value'] ?? 0) > 0): ?>
                <div style="text-align:center;margin-top:1rem;padding:0.75rem;background:var(--secondary);border-radius:8px">
                    <span style="font-size:0.85rem;color:var(--text-light)">Gem. boeking:</span>
                    <span style="font-weight:600;color:var(--primary)">&euro;<?= number_format($stats['services']['average_booking_value'], 2, ',', '.') ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Meldingen -->
<div class="insight-card" style="margin-top:1.5rem">
    <div class="insight-header">
        <h3 class="insight-title"><i class="fas fa-bell"></i> Recente Meldingen</h3>
    </div>
    <?php if (empty($notifications)): ?>
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <p>Geen meldingen</p>
        </div>
    <?php else: ?>
        <div class="notification-list">
            <?php foreach ($notifications as $notif): ?>
                <?php
                $icons = [
                    'new_booking' => 'fa-calendar-plus',
                    'cancellation' => 'fa-calendar-times',
                    'new_review' => 'fa-star',
                    'booking_reminder' => 'fa-clock',
                    'milestone' => 'fa-trophy',
                    'test' => 'fa-flask'
                ];
                $icon = $icons[$notif['type']] ?? 'fa-bell';
                ?>
                <div class="notification-item <?= empty($notif['read_at']) ? 'unread' : '' ?>">
                    <div class="notification-icon">
                        <i class="fas <?= $icon ?>"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-message"><?= htmlspecialchars($notif['message']) ?></div>
                        <div class="notification-time"><?= date('d-m-Y H:i', strtotime($notif['created_at'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Reviews Stats -->
<div class="insight-card" style="margin-top:1.5rem">
    <div class="insight-header">
        <h3 class="insight-title"><i class="fas fa-star"></i> Reviews</h3>
        <a href="/business/reviews" style="font-size:0.85rem;color:var(--primary);text-decoration:none">Bekijk alle <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="stat-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-item">
            <div class="stat-value"><?= $stats['reviews']['total_reviews'] ?? 0 ?></div>
            <div class="stat-label">Totaal</div>
        </div>
        <div class="stat-item">
            <div class="stat-value"><?= number_format($stats['reviews']['average_rating'] ?? 0, 1, ',', '.') ?></div>
            <div class="stat-label">Gem. Rating</div>
        </div>
        <div class="stat-item">
            <div class="stat-value"><?= $stats['reviews']['reviews_this_month'] ?? 0 ?></div>
            <div class="stat-label">Deze maand</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color:<?= ($stats['reviews']['unanswered_reviews'] ?? 0) > 0 ? '#f59e0b' : 'inherit' ?>">
                <?= $stats['reviews']['unanswered_reviews'] ?? 0 ?>
            </div>
            <div class="stat-label">Onbeantwoord</div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

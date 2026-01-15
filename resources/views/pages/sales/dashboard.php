<?php ob_start(); ?>

<!-- Stats Grid -->
<div class="grid-4" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <h4><i class="fas fa-users"></i> Totaal Referrals</h4>
        <p class="value"><?= $stats['totalReferrals'] ?></p>
    </div>
    <div class="stat-card">
        <h4><i class="fas fa-check-circle"></i> Geconverteerd</h4>
        <p class="value"><?= $stats['convertedReferrals'] ?></p>
    </div>
    <div class="stat-card">
        <h4><i class="fas fa-wallet"></i> Totaal Verdiend</h4>
        <p class="value"><?= number_format($stats['totalEarnings'], 2, ',', '.') ?></p>
    </div>
    <div class="stat-card">
        <h4><i class="fas fa-clock"></i> In Afwachting</h4>
        <p class="value" style="color:#737373"><?= number_format($stats['pendingEarnings'], 2, ',', '.') ?></p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid-2" style="margin-bottom:1.5rem">
    <div class="card" style="border:2px solid #333333">
        <h3><i class="fas fa-link"></i> Deel je Link</h3>
        <div style="background:#ffffff;padding:0.75rem;border-radius:8px;word-break:break-all;font-family:monospace;font-size:0.85rem;color:#000000;margin-bottom:1rem;border:1px solid rgba(0,0,0,0.1)">
            glamourschedule.nl/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?>
        </div>
        <button onclick="copyLink()" class="btn btn-primary" style="width:100%">
            <i class="fas fa-copy"></i> Kopieer Link
        </button>
    </div>

    <div class="card" style="background:linear-gradient(135deg,#000000,#000000);border:1px solid #333333">
        <h3 style="color:#000000"><i class="fas fa-euro-sign"></i> Verdien 49,99</h3>
        <p style="margin:0;color:#333333;line-height:1.6">
            Voor elk bedrijf dat zich registreert met jouw code ontvang je 49,99 commissie!
        </p>
        <a href="/sales/materials" style="display:inline-block;margin-top:1rem;color:#000000;text-decoration:none;font-weight:600">
            <i class="fas fa-arrow-right"></i> Bekijk promotiemateriaal
        </a>
    </div>
</div>

<!-- Recent Referrals -->
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem">
        <h3 style="margin:0"><i class="fas fa-users"></i> Recente Referrals</h3>
        <a href="/sales/referrals" style="color:#333333;text-decoration:none;font-size:0.9rem">
            Bekijk alle <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <?php if (empty($recentReferrals)): ?>
        <div style="text-align:center;padding:2rem 0">
            <i class="fas fa-user-plus" style="font-size:3rem;color:rgba(0,0,0,0.1);margin-bottom:1rem;display:block"></i>
            <p style="color:#666666;margin:0">Nog geen referrals. Deel je code om te beginnen!</p>
            <a href="/sales/materials" class="btn btn-primary" style="margin-top:1rem;display:inline-flex">
                <i class="fas fa-bullhorn"></i> Promotiemateriaal
            </a>
        </div>
    <?php else: ?>
        <!-- Desktop table -->
        <div class="referral-table-desktop" style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>Bedrijf</th>
                        <th>Status</th>
                        <th style="text-align:right">Commissie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentReferrals as $ref): ?>
                        <tr>
                            <td>
                                <strong style="color:#333333"><?= htmlspecialchars($ref['company_name']) ?></strong><br>
                                <small style="color:#666666"><?= date('d-m-Y', strtotime($ref['created_at'])) ?></small>
                            </td>
                            <td>
                                <?php
                                $statusColors = ['pending' => '#737373', 'converted' => '#333333', 'paid' => '#404040'];
                                $statusLabels = ['pending' => 'In afwachting', 'converted' => 'Geconverteerd', 'paid' => 'Uitbetaald'];
                                $status = $ref['status'] ?? 'pending';
                                ?>
                                <span style="background:<?= $statusColors[$status] ?>20;color:<?= $statusColors[$status] ?>;padding:0.25rem 0.75rem;border-radius:15px;font-size:0.8rem;white-space:nowrap">
                                    <?= $statusLabels[$status] ?? $status ?>
                                </span>
                            </td>
                            <td style="text-align:right;font-weight:600;color:#333333">
                                <?= number_format($ref['commission'], 2, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile cards -->
        <div class="referral-cards-mobile">
            <?php foreach ($recentReferrals as $ref): ?>
                <?php
                $statusColors = ['pending' => '#737373', 'converted' => '#333333', 'paid' => '#404040'];
                $statusLabels = ['pending' => 'In afwachting', 'converted' => 'Geconverteerd', 'paid' => 'Uitbetaald'];
                $status = $ref['status'] ?? 'pending';
                ?>
                <div style="background:#ffffff;border-radius:12px;padding:1rem;margin-bottom:0.75rem;border:1px solid rgba(0,0,0,0.1)">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem">
                        <strong style="color:#333333"><?= htmlspecialchars($ref['company_name']) ?></strong>
                        <span style="background:<?= $statusColors[$status] ?>20;color:<?= $statusColors[$status] ?>;padding:0.25rem 0.5rem;border-radius:10px;font-size:0.75rem">
                            <?= $statusLabels[$status] ?? $status ?>
                        </span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <small style="color:#666666"><?= date('d-m-Y', strtotime($ref['created_at'])) ?></small>
                        <span style="font-weight:700;color:#333333"><?= number_format($ref['commission'], 2, ',', '.') ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .referral-table-desktop { display: block; }
    .referral-cards-mobile { display: none; }

    @media (max-width: 768px) {
        .referral-table-desktop { display: none; }
        .referral-cards-mobile { display: block; }
    }
</style>

<script>
    function copyLink() {
        const link = 'https://glamourschedule.nl/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?>';
        navigator.clipboard.writeText(link).then(() => {
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#333333;color:#ffffff;padding:0.75rem 1.5rem;border-radius:10px;font-weight:600;z-index:9999';
            toast.innerHTML = '<i class="fas fa-check"></i> Link gekopieerd!';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        });
    }
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/sales.php'; ?>

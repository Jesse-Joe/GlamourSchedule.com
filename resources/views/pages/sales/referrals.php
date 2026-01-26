<?php ob_start(); ?>

<div class="card" style="background:#ffffff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
    <h3 style="margin:0 0 1.5rem 0"><i class="fas fa-users"></i> Alle Referrals</h3>

    <?php if (empty($referrals)): ?>
        <p style="color:#6b7280;text-align:center;padding:3rem 0">
            <i class="fas fa-user-plus" style="font-size:3rem;opacity:0.3;display:block;margin-bottom:1rem"></i>
            Nog geen referrals.<br>Deel je code om te beginnen met verdienen!
        </p>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="border-bottom:2px solid #e5e7eb">
                    <th style="text-align:left;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Bedrijf</th>
                    <th style="text-align:left;padding:1rem 0.5rem;color:#6b7280;font-weight:600">E-mail</th>
                    <th style="text-align:left;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Geregistreerd</th>
                    <th style="text-align:center;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Status</th>
                    <th style="text-align:right;padding:1rem 0.5rem;color:#6b7280;font-weight:600">Commissie</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($referrals as $ref): ?>
                    <?php
                    $statusColors = ['pending' => '#000000', 'converted' => '#333333', 'paid' => '#404040', 'cancelled' => '#dc2626', 'failed' => '#dc2626', 'expired' => '#6b7280'];
                    $statusLabels = ['pending' => 'In afwachting', 'converted' => 'Geconverteerd', 'paid' => 'Uitbetaald', 'cancelled' => 'Geannuleerd', 'failed' => 'Mislukt', 'expired' => 'Verlopen'];
                    $status = $ref['status'] ?? 'pending';
                    ?>
                    <tr style="border-bottom:1px solid #e5e7eb">
                        <td style="padding:1rem 0.5rem">
                            <strong><?= htmlspecialchars($ref['company_name'] ?? '') ?></strong>
                        </td>
                        <td style="padding:1rem 0.5rem;color:#6b7280">
                            <?= htmlspecialchars($ref['email'] ?? '') ?>
                        </td>
                        <td style="padding:1rem 0.5rem;color:#6b7280">
                            <?= !empty($ref['business_created']) ? date('d-m-Y', strtotime($ref['business_created'])) : '-' ?>
                        </td>
                        <td style="padding:1rem 0.5rem;text-align:center">
                            <span style="background:<?= $statusColors[$status] ?? '#6b7280' ?>20;color:<?= $statusColors[$status] ?? '#6b7280' ?>;padding:0.35rem 1rem;border-radius:20px;font-size:0.85rem;font-weight:500">
                                <?= $statusLabels[$status] ?? ucfirst($status) ?>
                            </span>
                        </td>
                        <td style="padding:1rem 0.5rem;text-align:right;font-weight:600;font-size:1.1rem">
                            &euro;<?= number_format($ref['commission'], 2, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/sales.php'; ?>

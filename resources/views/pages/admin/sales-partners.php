<?php ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-label"><?= $__('total_partners') ?></div>
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
    </div>
    <div class="stat-card success">
        <div class="stat-label"><?= $__('active_partners') ?></div>
        <div class="stat-value"><?= number_format($stats['active']) ?></div>
    </div>
    <div class="stat-card warning">
        <div class="stat-label"><?= $__('total_referrals') ?></div>
        <div class="stat-value"><?= number_format($stats['totalReferrals']) ?></div>
    </div>
    <div class="stat-card info">
        <div class="stat-label"><?= $__('total_commission') ?></div>
        <div class="stat-value">&euro;<?= number_format($stats['totalCommission'], 2, ',', '.') ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-handshake"></i> <?= $__('sales_partners') ?> (<?= number_format($totalPartners) ?>)</h3>
        <form method="GET" class="search-box">
            <input type="text" name="search" class="form-control" placeholder="<?= $__('search_name_or_email') ?>" value="<?= htmlspecialchars($search) ?>">
            <select name="status" class="form-control" style="width:auto;">
                <option value=""><?= $__('all') ?></option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>><?= $__('active') ?></option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>><?= $__('pending') ?></option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>><?= $__('inactive') ?></option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?= $__('name') ?></th>
                    <th><?= $__('email') ?></th>
                    <th><?= $__('phone') ?></th>
                    <th><?= $__('referral_code') ?></th>
                    <th><?= $__('referrals') ?></th>
                    <th><?= $__('commission') ?></th>
                    <th><?= $__('status') ?></th>
                    <th><?= $__('created') ?></th>
                    <th><?= $__('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($partners)): ?>
                <tr>
                    <td colspan="10" style="text-align:center;color:var(--text-light);padding:2rem;">
                        <?= $__('no_sales_partners_found') ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($partners as $partner): ?>
                <tr>
                    <td><?= $partner['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($partner['first_name'] . ' ' . ($partner['last_name'] ?? '')) ?></strong>
                        <?php if (!empty($partner['company_name'])): ?>
                            <div style="font-size:0.8rem;color:var(--text-light);">
                                <?= htmlspecialchars($partner['company_name']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($partner['email']) ?></td>
                    <td><?= htmlspecialchars($partner['phone'] ?? '-') ?></td>
                    <td>
                        <code style="background:var(--bg);padding:0.25rem 0.5rem;border-radius:4px;font-size:0.85rem;">
                            <?= htmlspecialchars($partner['referral_code']) ?>
                        </code>
                    </td>
                    <td>
                        <strong><?= number_format($partner['referral_count'] ?? 0) ?></strong>
                    </td>
                    <td>
                        &euro;<?= number_format($partner['total_commission'] ?? 0, 2, ',', '.') ?>
                        <?php if (($partner['pending_commission'] ?? 0) > 0): ?>
                            <div style="font-size:0.75rem;color:var(--warning);">
                                &euro;<?= number_format($partner['pending_commission'], 2, ',', '.') ?> <?= $__('pending') ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($partner['status'] === 'active'): ?>
                            <span class="badge badge-success"><?= $__('active') ?></span>
                        <?php elseif ($partner['status'] === 'pending'): ?>
                            <span class="badge badge-warning"><?= $__('pending') ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= $__($partner['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= $formatDate($partner['created_at']) ?></td>
                    <td>
                        <div class="actions">
                            <?php if ($partner['status'] !== 'active'): ?>
                            <form method="POST" action="/admin/sales-partner/<?= $partner['id'] ?>/update" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-sm btn-success" title="<?= $__('activate') ?>">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <form method="POST" action="/admin/sales-partner/<?= $partner['id'] ?>/update" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                                <input type="hidden" name="status" value="inactive">
                                <button type="submit" class="btn btn-sm btn-secondary" title="<?= $__('deactivate') ?>">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <a href="/sales/<?= htmlspecialchars($partner['referral_code']) ?>" target="_blank" class="btn btn-sm btn-secondary" title="<?= $__('referral_link') ?>">
                                <i class="fas fa-link"></i>
                            </a>
                            <form method="POST" action="/admin/sales-partner/<?= $partner['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('<?= $__('confirm_delete_partner') ?>');">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="<?= $__('delete') ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>

        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
            <?php if ($i === $currentPage): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/admin.php'; ?>

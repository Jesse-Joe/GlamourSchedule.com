<?php ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-store"></i> <?= $__('businesses') ?> (<?= number_format($totalBusinesses) ?>)</h3>
        <form method="GET" class="search-box" style="max-width:500px;">
            <input type="text" name="search" class="form-control" placeholder="<?= $__('search') ?>..." value="<?= htmlspecialchars($search) ?>">
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
                    <th><?= $__('business') ?></th>
                    <th><?= $__('email') ?></th>
                    <th><?= $__('status') ?></th>
                    <th><?= $__('subscription') ?></th>
                    <th><?= $__('trial_ends') ?></th>
                    <th><?= $__('created') ?></th>
                    <th><?= $__('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($businesses)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--text-light);padding:2rem;">
                        <?= $__('no_businesses_found') ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($businesses as $biz): ?>
                <tr>
                    <td><?= $biz['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($biz['company_name']) ?></strong>
                        <?php if ($biz['is_early_adopter']): ?>
                            <span class="badge badge-info" style="margin-left:0.5rem;">Early Adopter</span>
                        <?php endif; ?>
                        <div style="font-size:0.8rem;color:var(--text-light);">
                            <?= htmlspecialchars($biz['city'] ?? '') ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($biz['email']) ?></td>
                    <td>
                        <?php if ($biz['status'] === 'active'): ?>
                            <span class="badge badge-success"><?= $__('active') ?></span>
                        <?php elseif ($biz['status'] === 'pending'): ?>
                            <span class="badge badge-warning"><?= $__('pending') ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= $__($biz['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($biz['subscription_status'] === 'active'): ?>
                            <span class="badge badge-success"><?= $__('active') ?></span>
                        <?php elseif ($biz['subscription_status'] === 'trial'): ?>
                            <span class="badge badge-info"><?= $__('trial') ?></span>
                        <?php elseif ($biz['subscription_status'] === 'expired'): ?>
                            <span class="badge badge-danger"><?= $__('expired') ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= $__($biz['subscription_status'] ?? 'pending') ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($biz['trial_ends_at']): ?>
                            <?= $formatDate($biz['trial_ends_at']) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= $formatDate($biz['created_at']) ?></td>
                    <td>
                        <div class="actions">
                            <?php if ($biz['status'] !== 'active'): ?>
                            <form method="POST" action="/admin/business/<?= $biz['id'] ?>/activate" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                                <button type="submit" class="btn btn-sm btn-success" title="<?= $__('activate') ?>">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <form method="POST" action="/admin/business/<?= $biz['id'] ?>/update" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                                <input type="hidden" name="status" value="inactive">
                                <input type="hidden" name="subscription_status" value="<?= $biz['subscription_status'] ?>">
                                <button type="submit" class="btn btn-sm btn-secondary" title="<?= $__('deactivate') ?>">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <a href="/salon/<?= htmlspecialchars($biz['slug']) ?>" target="_blank" class="btn btn-sm btn-secondary" title="<?= $__('view') ?>">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="POST" action="/admin/business/<?= $biz['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('<?= $__('confirm_delete_business') ?>');">
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

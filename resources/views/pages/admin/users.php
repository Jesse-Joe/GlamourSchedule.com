<?php ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users"></i> <?= $__('users') ?> (<?= number_format($totalUsers) ?>)</h3>
        <form method="GET" class="search-box">
            <input type="text" name="search" class="form-control" placeholder="<?= $__('search_name_or_email') ?>" value="<?= htmlspecialchars($search) ?>">
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
                    <th><?= $__('status') ?></th>
                    <th><?= $__('created') ?></th>
                    <th><?= $__('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;color:var(--text-light);padding:2rem;">
                        <?= $__('no_users_found') ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($user['first_name'] . ' ' . ($user['last_name'] ?? '')) ?></strong>
                    </td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                    <td>
                        <?php if ($user['status'] === 'active'): ?>
                            <span class="badge badge-success"><?= $__('active') ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= $__($user['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= $formatDate($user['created_at']) ?></td>
                    <td>
                        <div class="actions">
                            <form method="POST" action="/admin/user/<?= $user['id'] ?>/update" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                                <?php if ($user['status'] === 'active'): ?>
                                    <input type="hidden" name="status" value="inactive">
                                    <button type="submit" class="btn btn-sm btn-secondary" title="<?= $__('deactivate') ?>">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                <?php else: ?>
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-sm btn-success" title="<?= $__('activate') ?>">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php endif; ?>
                            </form>
                            <form method="POST" action="/admin/user/<?= $user['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('<?= $__('confirm_delete_user') ?>');">
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
            <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>

        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
            <?php if ($i === $currentPage): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/admin.php'; ?>

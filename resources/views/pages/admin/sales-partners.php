<?php ob_start(); ?>

<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-label">Totaal Partners</div>
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
    </div>
    <div class="stat-card success">
        <div class="stat-label">Actieve Partners</div>
        <div class="stat-value"><?= number_format($stats['active']) ?></div>
    </div>
    <div class="stat-card warning">
        <div class="stat-label">Totaal Referrals</div>
        <div class="stat-value"><?= number_format($stats['totalReferrals']) ?></div>
    </div>
    <div class="stat-card info">
        <div class="stat-label">Totale Commissie</div>
        <div class="stat-value">&euro;<?= number_format($stats['totalCommission'], 2, ',', '.') ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-handshake"></i> Sales Partners (<?= number_format($totalPartners) ?>)</h3>
        <form method="GET" class="search-box">
            <input type="text" name="search" class="form-control" placeholder="Zoeken op naam of email..." value="<?= htmlspecialchars($search) ?>">
            <select name="status" class="form-control" style="width:auto;">
                <option value="">Alle</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Actief</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactief</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Naam</th>
                    <th>Email</th>
                    <th>Telefoon</th>
                    <th>Referral Code</th>
                    <th>Referrals</th>
                    <th>Commissie</th>
                    <th>Status</th>
                    <th>Aangemaakt</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($partners)): ?>
                <tr>
                    <td colspan="10" style="text-align:center;color:var(--text-light);padding:2rem;">
                        Geen sales partners gevonden
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
                                &euro;<?= number_format($partner['pending_commission'], 2, ',', '.') ?> pending
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($partner['status'] === 'active'): ?>
                            <span class="badge badge-success">Actief</span>
                        <?php elseif ($partner['status'] === 'pending'): ?>
                            <span class="badge badge-warning">Pending</span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= ucfirst($partner['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d-m-Y', strtotime($partner['created_at'])) ?></td>
                    <td>
                        <div class="actions">
                            <?php if ($partner['status'] !== 'active'): ?>
                            <form method="POST" action="/admin/sales-partner/<?= $partner['id'] ?>/update" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-sm btn-success" title="Activeren">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <form method="POST" action="/admin/sales-partner/<?= $partner['id'] ?>/update" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                                <input type="hidden" name="status" value="inactive">
                                <button type="submit" class="btn btn-sm btn-secondary" title="Deactiveren">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <a href="/sales/<?= htmlspecialchars($partner['referral_code']) ?>" target="_blank" class="btn btn-sm btn-secondary" title="Referral Link">
                                <i class="fas fa-link"></i>
                            </a>
                            <form method="POST" action="/admin/sales-partner/<?= $partner['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('<?= $translations['confirm_delete_partner'] ?? 'Are you sure you want to delete this sales partner?' ?>');">
                                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Verwijderen">
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

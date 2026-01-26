<?php ob_start(); ?>

<div class="page-header">
    <div>
        <h1>Early Birds</h1>
        <p style="color:#a1a1a1;margin-top:0.25rem">Registreer nieuwe salons en verdien €9,99 per conversie</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="showRegisterModal()">
        <i class="fas fa-plus"></i> Nieuwe Early Bird
    </button>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <h4>Totaal Geregistreerd</h4>
        <div class="value"><?= number_format($stats['total'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <h4>In Trial</h4>
        <div class="value" style="color:#f59e0b"><?= number_format($stats['in_trial'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <h4>Geconverteerd</h4>
        <div class="value" style="color:#22c55e"><?= number_format($stats['converted'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <h4>Te Verdienen</h4>
        <div class="value" style="color:#22c55e">€<?= number_format(($stats['converted'] ?? 0) * 9.99, 2, ',', '.') ?></div>
    </div>
</div>

<!-- Info Banner -->
<div class="info-banner">
    <div class="info-banner-icon">
        <i class="fas fa-gift"></i>
    </div>
    <div class="info-banner-content">
        <h4>Hoe werkt Early Bird?</h4>
        <ul>
            <li><strong>Stap 1:</strong> Registreer een salon hieronder met naam en contactgegevens</li>
            <li><strong>Stap 2:</strong> De salon ontvangt een uitnodiging voor de Early Bird actie (€0,99)</li>
            <li><strong>Stap 3:</strong> Wanneer de salon de trial voltooit en betaalt, ontvang jij <strong>€9,99</strong></li>
        </ul>
    </div>
</div>

<!-- Early Birds List -->
<div class="card">
    <h3><i class="fas fa-list"></i> Mijn Early Birds</h3>

    <?php if (empty($earlyBirds)): ?>
        <div class="empty-state">
            <i class="fas fa-seedling"></i>
            <h4>Nog geen Early Birds</h4>
            <p>Begin met het registreren van salons om commissie te verdienen!</p>
            <button type="button" class="btn btn-primary" onclick="showRegisterModal()">
                <i class="fas fa-plus"></i> Eerste Early Bird Registreren
            </button>
        </div>
    <?php else: ?>
        <div class="early-birds-list">
            <?php foreach ($earlyBirds as $bird): ?>
                <div class="early-bird-card">
                    <div class="early-bird-header">
                        <div class="early-bird-info">
                            <h4><?= htmlspecialchars($bird['business_name']) ?></h4>
                            <p><?= htmlspecialchars($bird['contact_name']) ?></p>
                        </div>
                        <?php
                        $statusLabels = [
                            'pending' => 'Uitgenodigd',
                            'registered' => 'Geregistreerd',
                            'trial' => 'In Trial',
                            'converted' => 'Geconverteerd',
                            'expired' => 'Verlopen',
                            'cancelled' => 'Geannuleerd'
                        ];
                        $birdStatus = $bird['status'] ?? 'pending';
                        $safeStatus = in_array($birdStatus, array_keys($statusLabels)) ? $birdStatus : 'pending';
                        ?>
                        <span class="status-badge status-<?= $safeStatus ?>">
                            <?= $statusLabels[$birdStatus] ?? 'Onbekend' ?>
                        </span>
                    </div>
                    <div class="early-bird-details">
                        <div class="detail">
                            <i class="fas fa-envelope"></i>
                            <span><?= htmlspecialchars($bird['contact_email']) ?></span>
                        </div>
                        <?php if (!empty($bird['contact_phone'])): ?>
                            <div class="detail">
                                <i class="fas fa-phone"></i>
                                <span><?= htmlspecialchars($bird['contact_phone']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="detail">
                            <i class="fas fa-calendar"></i>
                            <span><?= !empty($bird['created_at']) ? date('d-m-Y', strtotime($bird['created_at'])) : '-' ?></span>
                        </div>
                    </div>
                    <div class="early-bird-footer">
                        <?php if ($bird['status'] === 'pending'): ?>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="copyInviteLink('<?= $bird['invite_code'] ?>')">
                                <i class="fas fa-copy"></i> Kopieer Link
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="resendInvite(<?= $bird['id'] ?>)">
                                <i class="fas fa-paper-plane"></i> Opnieuw Versturen
                            </button>
                        <?php elseif ($bird['status'] === 'converted'): ?>
                            <div class="commission-earned">
                                <i class="fas fa-check-circle"></i>
                                <span>€<?= number_format($bird['commission'], 2, ',', '.') ?> verdiend</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Register Modal -->
<div id="registerModal" class="modal-overlay" style="display:none">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-seedling"></i> Nieuwe Early Bird Registreren</h3>
            <button type="button" class="modal-close" onclick="hideRegisterModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="/sales/early-birds/register" id="registerForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">

            <div class="form-group">
                <label class="form-label">Salonnaam *</label>
                <input type="text" name="business_name" class="form-control" placeholder="Bijv. Beauty Salon Anna" required>
            </div>

            <div class="form-group">
                <label class="form-label">Contactpersoon *</label>
                <input type="text" name="contact_name" class="form-control" placeholder="Volledige naam" required>
            </div>

            <div class="form-group">
                <label class="form-label">E-mailadres *</label>
                <input type="email" name="contact_email" class="form-control" placeholder="email@salon.nl" required>
            </div>

            <div class="form-group">
                <label class="form-label">Telefoonnummer</label>
                <input type="tel" name="contact_phone" class="form-control" placeholder="06-12345678">
            </div>

            <div class="form-group">
                <label class="form-label">Notities (optioneel)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Extra informatie over de salon..."></textarea>
            </div>

            <div class="commission-preview">
                <i class="fas fa-coins"></i>
                <div>
                    <strong>Jouw Commissie</strong>
                    <span>€9,99 bij conversie</span>
                </div>
            </div>

            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" onclick="hideRegisterModal()">Annuleren</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Registreren & Uitnodigen
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .page-header h1 {
        margin: 0;
        color: #fff;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 900px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 500px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .info-banner {
        display: flex;
        gap: 1.25rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        border: 1px solid #333;
        border-radius: 16px;
        margin-bottom: 1.5rem;
    }
    .info-banner-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        flex-shrink: 0;
    }
    .info-banner-content h4 {
        margin: 0 0 0.75rem;
        color: #fff;
    }
    .info-banner-content ul {
        margin: 0;
        padding-left: 1.25rem;
        color: #a1a1a1;
        font-size: 0.9rem;
        line-height: 1.8;
    }
    .info-banner-content ul strong {
        color: #fff;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #a1a1a1;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #333;
    }
    .empty-state h4 {
        color: #fff;
        margin: 0 0 0.5rem;
    }
    .empty-state p {
        margin: 0 0 1.5rem;
    }

    .early-birds-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .early-bird-card {
        background: #0a0a0a;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 1.25rem;
    }
    .early-bird-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    .early-bird-info h4 {
        margin: 0;
        color: #fff;
        font-size: 1.1rem;
    }
    .early-bird-info p {
        margin: 0.25rem 0 0;
        color: #a1a1a1;
        font-size: 0.9rem;
    }
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-pending {
        background: rgba(107, 114, 128, 0.2);
        color: #9ca3af;
    }
    .status-registered {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }
    .status-trial {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }
    .status-converted {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
    }
    .status-expired, .status-cancelled {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    .early-bird-details {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #333;
    }
    .early-bird-details .detail {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #a1a1a1;
        font-size: 0.85rem;
    }
    .early-bird-details .detail i {
        color: #666;
    }

    .early-bird-footer {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .commission-earned {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #22c55e;
        font-weight: 600;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        padding: 1rem;
    }
    .modal-content {
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #333;
    }
    .modal-header h3 {
        margin: 0;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .modal-header h3 i {
        color: #f59e0b;
    }
    .modal-close {
        background: none;
        border: none;
        color: #666;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.5rem;
    }
    .modal-close:hover {
        color: #fff;
    }
    .modal-content form {
        padding: 1.5rem;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-label {
        display: block;
        color: #a1a1a1;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        background: #0a0a0a;
        border: 2px solid #333;
        border-radius: 10px;
        color: #fff;
        font-size: 1rem;
        box-sizing: border-box;
    }
    .form-control::placeholder {
        color: #555;
    }
    .form-control:focus {
        outline: none;
        border-color: #fff;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .commission-preview {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 10px;
        margin-bottom: 1.5rem;
    }
    .commission-preview i {
        font-size: 1.5rem;
        color: #22c55e;
    }
    .commission-preview strong {
        display: block;
        color: #fff;
        margin-bottom: 0.125rem;
    }
    .commission-preview span {
        color: #22c55e;
        font-size: 0.9rem;
    }

    .modal-buttons {
        display: flex;
        gap: 1rem;
    }
    .modal-buttons .btn {
        flex: 1;
    }
    .btn {
        padding: 0.875rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn-primary {
        background: #fff;
        color: #000;
    }
    .btn-primary:hover {
        background: #f0f0f0;
    }
    .btn-secondary {
        background: #333;
        color: #fff;
    }
    .btn-secondary:hover {
        background: #444;
    }
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
</style>

<script>
function showRegisterModal() {
    document.getElementById('registerModal').style.display = 'flex';
}

function hideRegisterModal() {
    document.getElementById('registerModal').style.display = 'none';
    document.getElementById('registerForm').reset();
}

function copyInviteLink(code) {
    const link = 'https://glamourschedule.nl/early-bird/' + code;
    navigator.clipboard.writeText(link).then(() => {
        alert('Uitnodigingslink gekopieerd!');
    });
}

function resendInvite(id) {
    if (confirm('Wil je de uitnodiging opnieuw versturen?')) {
        window.location.href = '/sales/early-birds/resend/' + id;
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') hideRegisterModal();
});
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/resources/views/layouts/sales.php';
?>

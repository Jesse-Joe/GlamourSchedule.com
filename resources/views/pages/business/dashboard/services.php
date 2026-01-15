<?php ob_start(); ?>

<style>
    .service-card {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.25rem;
        background: var(--white);
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    .service-card:hover {
        border-color: var(--primary);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .service-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .service-info {
        flex: 1;
    }
    .service-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
    }
    .service-actions {
        display: flex;
        gap: 0.5rem;
    }
</style>

<div class="grid grid-2">
    <!-- Add New Service -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle"></i> Nieuwe Dienst Toevoegen</h3>
            </div>

            <form method="POST" action="/business/services">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label class="form-label">Naam van de dienst *</label>
                    <input type="text" name="name" class="form-control" placeholder="Bijv: Dames Knippen" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Beschrijving</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Korte beschrijving van de dienst..."></textarea>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Prijs (&euro;) *</label>
                        <input type="number" name="price" class="form-control" placeholder="25.00" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Duur (minuten) *</label>
                        <input type="number" name="duration" class="form-control" placeholder="30" min="5" step="5" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%">
                    <i class="fas fa-plus"></i> Dienst Toevoegen
                </button>
            </form>
        </div>

        <div class="card" style="background:linear-gradient(135deg,#000000,#262626);color:white">
            <h4 style="margin-bottom:0.5rem"><i class="fas fa-lightbulb"></i> Tips</h4>
            <ul style="padding-left:1.25rem;opacity:0.9;font-size:0.9rem;line-height:1.8">
                <li>Gebruik duidelijke namen voor je diensten</li>
                <li>Voeg een beschrijving toe zodat klanten weten wat ze kunnen verwachten</li>
                <li>Stel realistische tijden in zodat je agenda klopt</li>
            </ul>
        </div>
    </div>

    <!-- Services List -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cut"></i> Mijn Diensten (<?= count($services) ?>)</h3>
            </div>

            <?php if (empty($services)): ?>
                <div class="text-center" style="padding:3rem">
                    <i class="fas fa-cut" style="font-size:4rem;color:var(--border);margin-bottom:1rem"></i>
                    <h4>Nog geen diensten</h4>
                    <p class="text-muted">Voeg je eerste dienst toe om boekingen te ontvangen.</p>
                </div>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-cut"></i>
                        </div>
                        <div class="service-info">
                            <h4 style="margin:0 0 0.25rem 0"><?= htmlspecialchars($service['name']) ?></h4>
                            <?php if (!empty($service['description'])): ?>
                                <p class="text-muted" style="margin:0;font-size:0.85rem"><?= htmlspecialchars($service['description']) ?></p>
                            <?php endif; ?>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> <?= $service['duration_minutes'] ?> min
                                <?php if (!$service['is_active']): ?>
                                    <span style="color:var(--warning);margin-left:0.5rem"><i class="fas fa-eye-slash"></i> Inactief</span>
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="service-price">
                            &euro;<?= number_format($service['price'], 2, ',', '.') ?>
                        </div>
                        <div class="service-actions">
                            <form method="POST" action="/business/services" onsubmit="return confirm('Weet je zeker dat je deze dienst wilt verwijderen?')">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

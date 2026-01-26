<?php ob_start(); ?>

<style>
    .inventory-card {
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
    .inventory-card:hover {
        border-color: var(--primary);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .inventory-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .inventory-icon.low-stock {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .inventory-icon.out-of-stock {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    .inventory-info {
        flex: 1;
    }
    .inventory-quantity {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--success);
        min-width: 80px;
        text-align: center;
    }
    .inventory-quantity.low-stock {
        color: var(--warning);
    }
    .inventory-quantity.out-of-stock {
        color: var(--danger);
    }
    .inventory-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .linked-services {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        margin-top: 0.5rem;
    }
    .service-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background: rgba(0,0,0,0.1);
        border-radius: 6px;
        font-size: 0.75rem;
    }
    .low-stock-alert {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 1px solid #f59e0b;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .low-stock-alert h4 {
        color: #92400e;
        margin: 0 0 0.5rem 0;
    }
    .low-stock-alert ul {
        margin: 0;
        padding-left: 1.25rem;
        color: #78350f;
    }
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal.active {
        display: flex;
    }
    .modal-content {
        background: var(--white);
        border-radius: 16px;
        padding: 1.5rem;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .modal-header h3 {
        margin: 0;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text);
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr !important;
        }
        .inventory-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
        }
        .inventory-icon {
            width: 44px;
            height: 44px;
        }
        .inventory-info {
            width: 100%;
        }
        .inventory-quantity {
            font-size: 1.1rem;
        }
        .inventory-actions {
            width: 100%;
            justify-content: flex-end;
        }
        .form-control {
            font-size: 16px;
        }
        .btn {
            padding: 0.75rem 1rem;
        }
    }
    @media (max-width: 480px) {
        .card {
            padding: 1rem;
        }
        .inventory-actions .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
    }
</style>

<?php if (!empty($lowStockItems)): ?>
<div class="low-stock-alert">
    <h4><i class="fas fa-exclamation-triangle"></i> Lage voorraad waarschuwing</h4>
    <ul>
        <?php foreach ($lowStockItems as $item): ?>
            <li><strong><?= htmlspecialchars($item['name']) ?></strong>: <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?> (minimum: <?= $item['min_quantity'] ?>)</li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="grid grid-2">
    <!-- Add New Inventory Item -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle"></i> Nieuw Product Toevoegen</h3>
            </div>

            <form method="POST" action="/business/inventory">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label class="form-label">Productnaam *</label>
                    <input type="text" name="name" class="form-control" placeholder="Bijv: Haarverf Bruin" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Beschrijving</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Optionele beschrijving..."></textarea>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">SKU / Artikelnummer</label>
                        <input type="text" name="sku" class="form-control" placeholder="Bijv: HV-001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Eenheid</label>
                        <select name="unit" class="form-control">
                            <option value="stuks">Stuks</option>
                            <option value="ml">Milliliter (ml)</option>
                            <option value="gram">Gram (g)</option>
                            <option value="liter">Liter (L)</option>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="set">Set</option>
                            <option value="doos">Doos</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Huidige voorraad *</label>
                        <input type="number" name="quantity" class="form-control" placeholder="0" min="0" value="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Minimum voorraad</label>
                        <input type="number" name="min_quantity" class="form-control" placeholder="5" min="0" value="0">
                        <small class="text-muted">Waarschuwing bij lagere voorraad</small>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Inkoopprijs (&euro;)</label>
                        <input type="number" name="purchase_price" class="form-control" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Verkoopprijs (&euro;)</label>
                        <input type="number" name="sell_price" class="form-control" placeholder="0.00" step="0.01" min="0">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%">
                    <i class="fas fa-plus"></i> Product Toevoegen
                </button>
            </form>
        </div>

        <div class="card" style="background:linear-gradient(135deg,#000000,#262626);color:white">
            <h4 style="margin-bottom:0.5rem"><i class="fas fa-lightbulb"></i> Tips</h4>
            <ul style="padding-left:1.25rem;opacity:0.9;font-size:0.9rem;line-height:1.8">
                <li>Stel een minimum voorraad in voor automatische waarschuwingen</li>
                <li>Koppel producten aan diensten om verbruik bij te houden</li>
                <li>Gebruik SKU-nummers voor snelle identificatie</li>
            </ul>
        </div>
    </div>

    <!-- Inventory List -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-boxes"></i> Mijn Voorraad (<?= count($inventory) ?>)</h3>
            </div>

            <?php if (empty($inventory)): ?>
                <div class="text-center" style="padding:3rem">
                    <i class="fas fa-boxes" style="font-size:4rem;color:var(--border);margin-bottom:1rem"></i>
                    <h4>Nog geen voorraad</h4>
                    <p class="text-muted">Voeg je eerste product toe om je voorraad bij te houden.</p>
                </div>
            <?php else: ?>
                <?php foreach ($inventory as $item):
                    $isLowStock = $item['min_quantity'] > 0 && $item['quantity'] <= $item['min_quantity'];
                    $isOutOfStock = $item['quantity'] <= 0;
                    $stockClass = $isOutOfStock ? 'out-of-stock' : ($isLowStock ? 'low-stock' : '');
                ?>
                    <div class="inventory-card">
                        <div class="inventory-icon <?= $stockClass ?>">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="inventory-info">
                            <h4 style="margin:0 0 0.25rem 0"><?= htmlspecialchars($item['name']) ?></h4>
                            <?php if (!empty($item['sku'])): ?>
                                <small class="text-muted">SKU: <?= htmlspecialchars($item['sku']) ?></small>
                            <?php endif; ?>
                            <?php if (!empty($item['description'])): ?>
                                <p class="text-muted" style="margin:0.25rem 0;font-size:0.85rem"><?= htmlspecialchars($item['description']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($item['linked_services'])): ?>
                                <div class="linked-services">
                                    <small class="text-muted"><i class="fas fa-link"></i></small>
                                    <?php foreach (explode(', ', $item['linked_services']) as $svc): ?>
                                        <span class="service-badge"><?= htmlspecialchars($svc) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="inventory-quantity <?= $stockClass ?>">
                            <?= $item['quantity'] ?><br>
                            <small style="font-weight:400;font-size:0.75rem"><?= htmlspecialchars($item['unit']) ?></small>
                        </div>
                        <div class="inventory-actions">
                            <button type="button" class="btn btn-sm btn-primary" onclick="openAdjustModal(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>', <?= $item['quantity'] ?>)">
                                <i class="fas fa-plus-minus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="openLinkModal(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($item['service_links'] ?? '', ENT_QUOTES) ?>')">
                                <i class="fas fa-link"></i>
                            </button>
                            <form method="POST" action="/business/inventory" style="display:inline" onsubmit="return confirm('Weet je zeker dat je dit product wilt verwijderen?')">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="inventory_id" value="<?= $item['id'] ?>">
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

<!-- Adjust Quantity Modal -->
<div id="adjustModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-minus"></i> Voorraad Aanpassen</h3>
            <button type="button" class="modal-close" onclick="closeModal('adjustModal')">&times;</button>
        </div>
        <form method="POST" action="/business/inventory/adjust">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="inventory_id" id="adjust_inventory_id">

            <p style="margin-bottom:1rem"><strong id="adjust_product_name"></strong></p>
            <p class="text-muted" style="margin-bottom:1rem">Huidige voorraad: <strong id="adjust_current_qty"></strong></p>

            <div class="form-group">
                <label class="form-label">Actie</label>
                <div style="display:flex;gap:1rem">
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer">
                        <input type="radio" name="adjustment_type" value="add" checked> Toevoegen
                    </label>
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer">
                        <input type="radio" name="adjustment_type" value="subtract"> Aftrekken
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Aantal</label>
                <input type="number" name="quantity" class="form-control" min="1" value="1" required>
            </div>

            <div class="form-group">
                <label class="form-label">Notitie (optioneel)</label>
                <input type="text" name="notes" class="form-control" placeholder="Bijv: Nieuwe levering ontvangen">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%">
                <i class="fas fa-check"></i> Voorraad Aanpassen
            </button>
        </form>
    </div>
</div>

<!-- Link to Service Modal -->
<div id="linkModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-link"></i> Koppelen aan Dienst</h3>
            <button type="button" class="modal-close" onclick="closeModal('linkModal')">&times;</button>
        </div>

        <p style="margin-bottom:1rem"><strong id="link_product_name"></strong></p>

        <form method="POST" action="/business/inventory/link">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="inventory_id" id="link_inventory_id">

            <div class="form-group">
                <label class="form-label">Dienst selecteren</label>
                <select name="service_id" class="form-control" required>
                    <option value="">-- Selecteer een dienst --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['name']) ?> - &euro;<?= number_format($service['price'], 2, ',', '.') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Verbruik per behandeling</label>
                <input type="number" name="quantity_used" class="form-control" min="0.01" step="0.01" value="1" required>
                <small class="text-muted">Hoeveel van dit product wordt verbruikt per behandeling</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%">
                <i class="fas fa-link"></i> Koppelen
            </button>
        </form>

        <div id="current_links" style="margin-top:1.5rem;display:none">
            <h4 style="font-size:0.9rem;margin-bottom:0.5rem">Huidige koppelingen:</h4>
            <div id="links_list"></div>
        </div>
    </div>
</div>

<script>
const services = <?= json_encode($services) ?>;

function openAdjustModal(id, name, qty) {
    document.getElementById('adjust_inventory_id').value = id;
    document.getElementById('adjust_product_name').textContent = name;
    document.getElementById('adjust_current_qty').textContent = qty;
    document.getElementById('adjustModal').classList.add('active');
}

function openLinkModal(id, name, serviceLinks) {
    document.getElementById('link_inventory_id').value = id;
    document.getElementById('link_product_name').textContent = name;

    const linksDiv = document.getElementById('current_links');
    const linksList = document.getElementById('links_list');
    linksList.innerHTML = '';

    if (serviceLinks) {
        const links = serviceLinks.split(',');
        links.forEach(link => {
            const [serviceId, qty] = link.split(':');
            const service = services.find(s => s.id == serviceId);
            if (service) {
                const div = document.createElement('div');
                div.style.cssText = 'display:flex;justify-content:space-between;align-items:center;padding:0.5rem;background:rgba(0,0,0,0.05);border-radius:6px;margin-bottom:0.5rem';
                div.innerHTML = `
                    <span>${service.name} (${qty}x)</span>
                    <form method="POST" action="/business/inventory/unlink" style="margin:0">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="inventory_id" value="${id}">
                        <input type="hidden" name="service_id" value="${serviceId}">
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-unlink"></i></button>
                    </form>
                `;
                linksList.appendChild(div);
            }
        });
        linksDiv.style.display = 'block';
    } else {
        linksDiv.style.display = 'none';
    }

    document.getElementById('linkModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Close modal when clicking outside
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

<?php ob_start(); ?>

<style>
    .employee-card {
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
    .employee-card:hover {
        border-color: var(--primary);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .employee-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
        flex-shrink: 0;
        overflow: hidden;
    }
    .employee-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .employee-info {
        flex: 1;
    }
    .employee-info h4 {
        margin: 0 0 0.25rem 0;
    }
    .employee-status {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .employee-status.active {
        background: #d1fae5;
        color: #065f46;
    }
    .employee-status.inactive {
        background: #fee2e2;
        color: #991b1b;
    }
    .employee-actions {
        display: flex;
        gap: 0.5rem;
    }
    .color-preview {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-block;
        vertical-align: middle;
        margin-left: 0.5rem;
        border: 2px solid var(--border);
    }
    .service-tag {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: var(--secondary);
        border-radius: 4px;
        font-size: 0.75rem;
        margin: 0.125rem;
    }
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-content {
        background: var(--white);
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 2rem;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-light);
    }
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.5rem;
    }
    .service-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: var(--secondary);
        border-radius: 8px;
        cursor: pointer;
    }
    .service-checkbox:hover {
        background: var(--border);
    }
    .service-checkbox input {
        width: 18px;
        height: 18px;
    }

    /* Mobile-first responsive styles */
    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr !important;
        }
        .employee-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
        }
        .employee-avatar {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
        .employee-info {
            width: 100%;
        }
        .employee-actions {
            width: 100%;
        }
        .employee-actions .btn {
            flex: 1;
        }
        .form-control {
            font-size: 16px;
        }
    }
    @media (max-width: 480px) {
        .card {
            padding: 1rem;
        }
        .service-checkbox {
            padding: 0.5rem;
            font-size: 0.85rem;
        }
    }
</style>

<div class="grid grid-2">
    <!-- Add New Employee -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-plus"></i> Nieuwe Medewerker Toevoegen</h3>
            </div>

            <form method="POST" action="/business/employees" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label class="form-label">Naam *</label>
                    <input type="text" name="name" class="form-control" placeholder="Bijv: Lisa de Vries" required>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">E-mailadres</label>
                        <input type="email" name="email" class="form-control" placeholder="medewerker@email.nl">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefoonnummer</label>
                        <input type="tel" name="phone" class="form-control" placeholder="06-12345678">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Bio / Beschrijving</label>
                    <textarea name="bio" class="form-control" rows="3" placeholder="Korte beschrijving van de medewerker..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Agenda Kleur</label>
                    <input type="color" name="color" value="#000000" class="form-control" style="height:50px;padding:5px">
                    <p class="form-hint">Deze kleur wordt gebruikt in de agenda om afspraken van deze medewerker te onderscheiden.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%">
                    <i class="fas fa-plus"></i> Medewerker Toevoegen
                </button>
            </form>
        </div>

        <div class="card" style="background:linear-gradient(135deg,#000000,#262626);color:white">
            <h4 style="margin-bottom:0.5rem"><i class="fas fa-info-circle"></i> Werknemersbeheer</h4>
            <ul style="padding-left:1.25rem;opacity:0.9;font-size:0.9rem;line-height:1.8">
                <li>Elke medewerker krijgt een eigen agenda</li>
                <li>Klanten kunnen bij het boeken een medewerker kiezen</li>
                <li>Meerdere afspraken tegelijk mogelijk op hetzelfde tijdstip</li>
                <li>Per extra medewerker: &euro;4,99 eenmalig</li>
            </ul>
        </div>
    </div>

    <!-- Employees List -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users"></i> Medewerkers (<?= count($employees) ?>)</h3>
            </div>

            <?php if (empty($employees)): ?>
                <div class="text-center" style="padding:3rem">
                    <i class="fas fa-users" style="font-size:4rem;color:var(--border);margin-bottom:1rem"></i>
                    <h4>Nog geen medewerkers</h4>
                    <p class="text-muted">Voeg je eerste medewerker toe om afspraken per persoon te beheren.</p>
                </div>
            <?php else: ?>
                <?php foreach ($employees as $employee): ?>
                    <div class="employee-card">
                        <div class="employee-avatar" style="<?= !empty($employee['color']) ? 'background:' . htmlspecialchars($employee['color']) : '' ?>">
                            <?php if (!empty($employee['photo'])): ?>
                                <img src="<?= htmlspecialchars($employee['photo']) ?>" alt="<?= htmlspecialchars($employee['name']) ?>">
                            <?php else: ?>
                                <?= strtoupper(substr($employee['name'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="employee-info">
                            <h4>
                                <?= htmlspecialchars($employee['name']) ?>
                                <span class="color-preview" style="background:<?= htmlspecialchars($employee['color'] ?? '#000000') ?>"></span>
                            </h4>
                            <?php if (!empty($employee['email'])): ?>
                                <p class="text-muted" style="margin:0;font-size:0.85rem">
                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($employee['email']) ?>
                                </p>
                            <?php endif; ?>
                            <div style="margin-top:0.5rem">
                                <span class="employee-status <?= $employee['is_active'] ? 'active' : 'inactive' ?>">
                                    <i class="fas fa-<?= $employee['is_active'] ? 'check-circle' : 'times-circle' ?>"></i>
                                    <?= $employee['is_active'] ? 'Actief' : 'Inactief' ?>
                                </span>
                            </div>
                            <?php if (!empty($employee['services'])): ?>
                                <div style="margin-top:0.5rem">
                                    <?php foreach ($employee['services'] as $service): ?>
                                        <span class="service-tag"><?= htmlspecialchars($service['name']) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="employee-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="editEmployee(<?= $employee['id'] ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="manageServices(<?= $employee['id'] ?>, '<?= htmlspecialchars($employee['name'], ENT_QUOTES) ?>')">
                                <i class="fas fa-cut"></i>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="manageHours(<?= $employee['id'] ?>, '<?= htmlspecialchars($employee['name'], ENT_QUOTES) ?>')">
                                <i class="fas fa-clock"></i>
                            </button>
                            <form method="POST" action="/business/employees" style="display:inline" onsubmit="return confirm('Weet je zeker dat je deze medewerker wilt verwijderen?')">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
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

<!-- Edit Employee Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit"></i> Medewerker Bewerken</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST" action="/business/employees" enctype="multipart/form-data" id="editForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="employee_id" id="edit_employee_id">

            <div class="form-group">
                <label class="form-label">Naam *</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">E-mailadres</label>
                    <input type="email" name="email" id="edit_email" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Telefoonnummer</label>
                    <input type="tel" name="phone" id="edit_phone" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Bio / Beschrijving</label>
                <textarea name="bio" id="edit_bio" class="form-control" rows="3"></textarea>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Agenda Kleur</label>
                    <input type="color" name="color" id="edit_color" class="form-control" style="height:50px;padding:5px">
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="is_active" id="edit_is_active" class="form-control">
                        <option value="1">Actief</option>
                        <option value="0">Inactief</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nieuwe Foto</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%">
                <i class="fas fa-save"></i> Opslaan
            </button>
        </form>
    </div>
</div>

<!-- Services Modal -->
<div class="modal-overlay" id="servicesModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-cut"></i> Diensten voor <span id="services_employee_name"></span></h3>
            <button class="modal-close" onclick="closeModal('servicesModal')">&times;</button>
        </div>
        <form method="POST" action="/business/employees">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="action" value="update_services">
            <input type="hidden" name="employee_id" id="services_employee_id">

            <p class="text-muted" style="margin-bottom:1rem">Selecteer welke diensten deze medewerker kan uitvoeren:</p>

            <div class="services-grid">
                <?php foreach ($allServices as $service): ?>
                    <label class="service-checkbox">
                        <input type="checkbox" name="services[]" value="<?= $service['id'] ?>" class="service-check" data-service-id="<?= $service['id'] ?>">
                        <span><?= htmlspecialchars($service['name']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <?php if (empty($allServices)): ?>
                <p class="text-muted text-center" style="padding:2rem">Voeg eerst diensten toe voordat je ze aan medewerkers kunt toewijzen.</p>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:1.5rem">
                <i class="fas fa-save"></i> Diensten Opslaan
            </button>
        </form>
    </div>
</div>

<!-- Hours Modal -->
<div class="modal-overlay" id="hoursModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-clock"></i> Werktijden voor <span id="hours_employee_name"></span></h3>
            <button class="modal-close" onclick="closeModal('hoursModal')">&times;</button>
        </div>
        <form method="POST" action="/business/employees">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="action" value="update_hours">
            <input type="hidden" name="employee_id" id="hours_employee_id">

            <p class="text-muted" style="margin-bottom:1rem">Stel de werktijden van deze medewerker in:</p>

            <?php
            $days = ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'];
            foreach ($days as $index => $day):
                $dayIndex = ($index + 1) % 7; // Convert to 0=Sunday format
            ?>
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.75rem;padding:0.75rem;background:var(--secondary);border-radius:8px" class="hours-row" data-day="<?= $dayIndex ?>">
                    <label style="width:100px;font-weight:500"><?= $day ?></label>
                    <input type="time" name="hours[<?= $dayIndex ?>][open]" class="form-control hour-open" style="flex:1" value="09:00">
                    <span>-</span>
                    <input type="time" name="hours[<?= $dayIndex ?>][close]" class="form-control hour-close" style="flex:1" value="18:00">
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer">
                        <input type="checkbox" name="hours[<?= $dayIndex ?>][closed]" class="hour-closed" onchange="toggleDay(this)">
                        Dicht
                    </label>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:1.5rem">
                <i class="fas fa-save"></i> Werktijden Opslaan
            </button>
        </form>
    </div>
</div>

<script>
const employees = <?= json_encode($employees) ?>;
const employeeServices = <?= json_encode($employeeServices ?? []) ?>;
const employeeHours = <?= json_encode($employeeHours ?? []) ?>;

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

function editEmployee(id) {
    const emp = employees.find(e => e.id == id);
    if (!emp) return;

    document.getElementById('edit_employee_id').value = emp.id;
    document.getElementById('edit_name').value = emp.name || '';
    document.getElementById('edit_email').value = emp.email || '';
    document.getElementById('edit_phone').value = emp.phone || '';
    document.getElementById('edit_bio').value = emp.bio || '';
    document.getElementById('edit_color').value = emp.color || '#000000';
    document.getElementById('edit_is_active').value = emp.is_active ? '1' : '0';

    document.getElementById('editModal').classList.add('active');
}

function manageServices(id, name) {
    document.getElementById('services_employee_id').value = id;
    document.getElementById('services_employee_name').textContent = name;

    // Reset all checkboxes
    document.querySelectorAll('.service-check').forEach(cb => cb.checked = false);

    // Check the services this employee has
    const services = employeeServices[id] || [];
    services.forEach(sid => {
        const cb = document.querySelector(`.service-check[data-service-id="${sid}"]`);
        if (cb) cb.checked = true;
    });

    document.getElementById('servicesModal').classList.add('active');
}

function manageHours(id, name) {
    document.getElementById('hours_employee_id').value = id;
    document.getElementById('hours_employee_name').textContent = name;

    // Reset to defaults
    document.querySelectorAll('.hours-row').forEach(row => {
        row.querySelector('.hour-open').value = '09:00';
        row.querySelector('.hour-close').value = '18:00';
        row.querySelector('.hour-closed').checked = false;
        row.querySelector('.hour-open').disabled = false;
        row.querySelector('.hour-close').disabled = false;
    });

    // Load employee's hours
    const hours = employeeHours[id] || [];
    hours.forEach(h => {
        const row = document.querySelector(`.hours-row[data-day="${h.day_of_week}"]`);
        if (row) {
            if (h.open_time) row.querySelector('.hour-open').value = h.open_time.substring(0, 5);
            if (h.close_time) row.querySelector('.hour-close').value = h.close_time.substring(0, 5);
            if (h.is_closed) {
                row.querySelector('.hour-closed').checked = true;
                row.querySelector('.hour-open').disabled = true;
                row.querySelector('.hour-close').disabled = true;
            }
        }
    });

    document.getElementById('hoursModal').classList.add('active');
}

function toggleDay(checkbox) {
    const row = checkbox.closest('.hours-row');
    row.querySelector('.hour-open').disabled = checkbox.checked;
    row.querySelector('.hour-close').disabled = checkbox.checked;
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

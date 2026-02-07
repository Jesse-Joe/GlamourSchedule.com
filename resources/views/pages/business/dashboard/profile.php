<?php ob_start(); ?>

<style>
/* Mobile-first responsive styles */
@media (max-width: 768px) {
    .grid-2 {
        grid-template-columns: 1fr !important;
    }
    .card {
        padding: 1rem;
        border-radius: 12px;
    }
    .form-control {
        font-size: 16px; /* Prevents zoom on iOS */
    }
    .btn {
        width: 100%;
        padding: 0.875rem 1rem;
    }
}
@media (max-width: 480px) {
    h1, .page-title {
        font-size: 1.5rem;
    }
    .card-title {
        font-size: 1rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
}
</style>

<?php
$days = [
    $__('monday'),
    $__('tuesday'),
    $__('wednesday'),
    $__('thursday'),
    $__('friday'),
    $__('saturday'),
    $__('sunday')
];
?>

<form method="POST" action="/business/profile">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="grid grid-2">
        <!-- Business Info -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building"></i> <?= $__('business_info') ?></h3>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $__('business_name_label') ?> *</label>
                    <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($business['company_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $__('email_label') ?> *</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($business['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $__('phone_label') ?></label>
                    <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($business['phone'] ?? '') ?>" placeholder="06-12345678">
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $__('website_label') ?></label>
                    <input type="url" name="website" class="form-control" value="<?= htmlspecialchars($business['website'] ?? '') ?>" placeholder="https://www.example.com">
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> <?= $__('address_info') ?></h3>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label"><?= $__('street_label') ?></label>
                        <input type="text" name="street" class="form-control" value="<?= htmlspecialchars($business['street'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('house_number_label') ?></label>
                        <input type="text" name="house_number" class="form-control" value="<?= htmlspecialchars($business['house_number'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label"><?= $__('postal_code') ?></label>
                        <input type="text" name="postal_code" class="form-control" value="<?= htmlspecialchars($business['postal_code'] ?? '') ?>" placeholder="1234 AB">
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('city') ?></label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($business['city'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- IBAN / Bankgegevens -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-university"></i> <?= $__('bank_details_payouts') ?></h3>
                </div>

                <?php if (!empty($business['iban']) && $business['iban_verified']): ?>
                    <?php
                    // Calculate 72-hour delay
                    $ibanChangedAt = !empty($business['iban_changed_at']) ? strtotime($business['iban_changed_at']) : 0;
                    $payoutAvailableAt = $ibanChangedAt + (72 * 3600);
                    $hoursRemaining = max(0, ceil(($payoutAvailableAt - time()) / 3600));
                    $isInCoolingPeriod = $hoursRemaining > 0;
                    ?>
                    <!-- IBAN Geverifieerd -->
                    <div style="padding:1rem;background:#1a1a1a;border-radius:10px;border:1px solid #22c55e;margin-bottom:1rem">
                        <div style="display:flex;align-items:center;gap:0.75rem">
                            <i class="fas fa-check-circle" style="color:#22c55e;font-size:1.5rem"></i>
                            <div>
                                <p style="margin:0;font-weight:600;color:#ffffff"><?= $__('iban_verified') ?></p>
                                <p style="margin:0.25rem 0 0 0;font-family:monospace;color:#ffffff"><?= htmlspecialchars($business['iban']) ?></p>
                                <p style="margin:0.25rem 0 0 0;font-size:0.85rem;color:#999999"><?= htmlspecialchars($business['account_holder'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($isInCoolingPeriod): ?>
                        <!-- 72-hour security delay warning -->
                        <div style="padding:1rem;background:#1a1a1a;border-radius:10px;border:1px solid #f59e0b;margin-bottom:1rem">
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <i class="fas fa-shield-alt" style="color:#f59e0b;font-size:1.25rem"></i>
                                <div>
                                    <p style="margin:0;font-weight:600;color:#ffffff"><?= $__('security_period_active') ?></p>
                                    <p style="margin:0.25rem 0 0 0;font-size:0.85rem;color:#999999">
                                        <?= $__('payouts_available_in', ['hours' => $hoursRemaining]) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted" style="font-size:0.85rem">
                            <i class="fas fa-info-circle"></i> <?= $__('profile_iban_verified_desc') ?>
                        </p>
                    <?php endif; ?>

                    <a href="/business/change-iban" class="btn btn-secondary" style="margin-top:1rem">
                        <i class="fas fa-edit"></i> <?= $__('iban_change') ?>
                    </a>

                <?php else: ?>
                    <!-- IBAN Toevoegen -->
                    <div style="background:linear-gradient(135deg,#1a1a1a,#0a0a0a);border:1px solid #333333;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem">
                        <h4 style="margin:0 0 0.5rem 0;color:#ffffff"><i class="fas fa-shield-alt"></i> <?= $__('secure_iban_verification') ?></h4>
                        <p style="margin:0;color:#999999;font-size:0.9rem">
                            <?= $__('iban_verification_desc') ?>
                        </p>
                    </div>

                    <!-- Use formaction to override the parent form action -->
                    <button type="submit" formaction="/business/iban/add" class="btn btn-primary" style="width:100%;padding:1rem;font-size:1.1rem">
                        <i class="fas fa-university"></i> <?= $__('iban_add_via_ideal') ?>
                    </button>
                    <p class="text-muted text-center" style="margin-top:1rem;font-size:0.85rem">
                        <i class="fas fa-lock"></i> <?= $__('iban_verification_note') ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Cash Betaling Optie -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> <?= $__('cash_payment') ?></h3>
                </div>

                <div style="padding:1rem;background:#1a1a1a;border:1px solid #333333;border-radius:12px;margin-bottom:1rem">
                    <div style="display:flex;align-items:flex-start;gap:1rem">
                        <label class="switch" style="flex-shrink:0;margin-top:0.25rem">
                            <input type="checkbox" name="cash_payment_enabled" value="1" <?= !empty($business['cash_payment_enabled']) ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                        <div>
                            <p style="margin:0;font-weight:600;color:#ffffff"><?= $__('cash_payment_enabled') ?></p>
                            <p style="margin:0.5rem 0 0 0;font-size:0.9rem;color:#999999">
                                <?= $__('cash_payment_desc') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div style="padding:1rem;background:#1a1a1a;border-radius:12px;border:1px solid #333333">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem">
                        <i class="fas fa-info-circle" style="color:#ffffff;font-size:1.25rem;margin-top:0.125rem"></i>
                        <div>
                            <p style="margin:0;font-weight:600;color:#ffffff"><?= $__('platform_fee_info') ?></p>
                            <p style="margin:0.5rem 0 0 0;font-size:0.9rem;color:#999999">
                                <?= $__('profile_cash_fee_explanation', ['fee' => $feeData['fee_display'] ?? '€1,75']) ?>
                            </p>
                            <p style="margin:0.75rem 0 0 0;font-size:0.85rem;color:#999999">
                                <i class="fas fa-calculator"></i> <?= $__('profile_cash_fee_example', ['fee' => $feeData['fee_display'] ?? '€1,75', 'cash' => number_format(50 - ($feeData['fee_amount'] ?? 1.75), 2, ',', '.')]) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cash Betaling Disclaimer -->
                <div style="padding:1rem;background:#fef2f2;border-radius:12px;border:1px solid #ef4444;margin-top:1rem">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem">
                        <i class="fas fa-exclamation-triangle" style="color:#dc2626;font-size:1.25rem;margin-top:0.125rem"></i>
                        <div>
                            <p style="margin:0;font-weight:600;color:#dc2626"><?= $__('cash_payment_disclaimer') ?></p>
                            <ul style="margin:0.75rem 0 0 0;padding-left:1.25rem;font-size:0.9rem;color:#991b1b;line-height:1.6">
                                <li><?= $__('no_show_warning') ?></li>
                                <li><?= $__('your_costs') ?></li>
                                <li><?= $__('cancellation_policy_note') ?></li>
                            </ul>
                            <p style="margin:0.75rem 0 0 0;font-size:0.85rem;color:#991b1b">
                                <i class="fas fa-lightbulb"></i> <?= $__('tip_online_payments') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> <?= $__('registration_info') ?></h3>
                </div>

                <div class="grid grid-2">
                    <div>
                        <p class="text-muted" style="font-size:0.85rem"><?= $__('kvk_number_label') ?></p>
                        <p style="font-weight:500"><?= htmlspecialchars($business['kvk_number'] ?? $__('not_specified')) ?></p>
                    </div>
                    <div>
                        <p class="text-muted" style="font-size:0.85rem"><?= $__('vat_number_label') ?></p>
                        <p style="font-weight:500"><?= htmlspecialchars($business['btw_number'] ?? $__('not_specified')) ?></p>
                    </div>
                </div>

                <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
                    <p class="text-muted" style="font-size:0.85rem"><?= $__('account_status') ?></p>
                    <span style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 1rem;background:<?= $business['status'] === 'active' ? '#ffffff' : '#ffffff' ?>;color:<?= $business['status'] === 'active' ? '#000000' : '#000000' ?>;border-radius:20px;font-size:0.85rem;font-weight:500">
                        <i class="fas fa-<?= $business['status'] === 'active' ? 'check-circle' : 'clock' ?>"></i>
                        <?= $business['status'] === 'active' ? $__('status_active') : $__('status_awaiting_verification') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Opening Hours -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> <?= $__('opening_hours') ?></h3>
                </div>

                <?php for ($i = 0; $i < 7; $i++):
                    $dayHours = $hours[$i] ?? ['open_time' => '09:00', 'close_time' => '18:00', 'is_closed' => ($i >= 6)];
                    // Convert HH:MM:SS to HH:MM for time inputs
                    $openTime = substr($dayHours['open_time'] ?? '09:00', 0, 5);
                    $closeTime = substr($dayHours['close_time'] ?? '18:00', 0, 5);
                ?>
                    <div style="display:flex;align-items:center;gap:1rem;padding:0.75rem 0;border-bottom:1px solid var(--border)">
                        <div style="width:100px;font-weight:500"><?= $days[$i] ?></div>
                        <div style="flex:1;display:flex;align-items:center;gap:0.5rem">
                            <input type="text" name="hours[<?= $i ?>][open]" class="form-control time-input"
                                   value="<?= htmlspecialchars($openTime) ?>"
                                   placeholder="09:00" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                   style="width:80px;text-align:center;font-family:monospace">
                            <span>-</span>
                            <input type="text" name="hours[<?= $i ?>][close]" class="form-control time-input"
                                   value="<?= htmlspecialchars($closeTime) ?>"
                                   placeholder="18:00" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                   style="width:80px;text-align:center;font-family:monospace">
                        </div>
                        <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;white-space:nowrap">
                            <input type="checkbox" name="hours[<?= $i ?>][closed]" <?= !empty($dayHours['is_closed']) ? 'checked' : '' ?> style="width:18px;height:18px;accent-color:var(--danger)">
                            <span class="text-muted" style="font-size:0.85rem"><?= $__('closed') ?></span>
                        </label>
                    </div>
                <?php endfor; ?>
                <script>
                // Auto-format time input (add : after 2 digits)
                document.querySelectorAll('.time-input').forEach(input => {
                    input.addEventListener('input', function(e) {
                        let value = this.value.replace(/[^0-9]/g, '');
                        if (value.length >= 2) {
                            value = value.substring(0,2) + ':' + value.substring(2,4);
                        }
                        this.value = value.substring(0,5);
                    });
                });
                </script>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-link"></i> <?= $__('your_public_page') ?></h3>
                </div>

                <p class="text-muted" style="margin-bottom:1rem"><?= $__('public_page_desc') ?></p>

                <div style="display:flex;align-items:center;gap:0.5rem;padding:1rem;background:var(--secondary);border-radius:10px">
                    <i class="fas fa-globe" style="color:var(--primary)"></i>
                    <code style="flex:1;word-break:break-all">https://glamourschedule.nl/business/<?= htmlspecialchars($business['slug'] ?? '') ?></code>
                    <button type="button" onclick="copyLink()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <a href="/business/<?= htmlspecialchars($business['slug'] ?? '') ?>" target="_blank" class="btn btn-primary" style="width:100%;margin-top:1rem">
                    <i class="fas fa-external-link-alt"></i> <?= $__('view_page') ?>
                </a>
            </div>

            <div class="card stats-card" style="background:linear-gradient(135deg,#ffffff,#f0f0f0);border:1px solid #333333;transition:all 0.3s ease;cursor:default">
                <h4 class="stats-title" style="margin-bottom:0.5rem;color:#000000;transition:color 0.3s ease"><i class="fas fa-chart-line"></i> <?= $__('statistics') ?></h4>
                <div class="grid grid-2" style="margin-top:1rem">
                    <div>
                        <p class="stats-number" style="font-size:2rem;font-weight:700;margin:0;color:#000000;transition:color 0.3s ease"><?= number_format($business['total_reviews'] ?? 0) ?></p>
                        <p class="stats-label" style="color:#333333;font-size:0.85rem;transition:color 0.3s ease"><?= $__('reviews') ?></p>
                    </div>
                    <div>
                        <p class="stats-number" style="font-size:2rem;font-weight:700;margin:0;color:#000000;transition:color 0.3s ease"><?= number_format($business['rating'] ?? 0, 1) ?></p>
                        <p class="stats-label" style="color:#333333;font-size:0.85rem;transition:color 0.3s ease"><?= $__('average_rating') ?></p>
                    </div>
                </div>
            </div>
            <style>
                .stats-card:hover {
                    background: linear-gradient(135deg,#000000,#1a1a1a) !important;
                    border-color: #ffffff !important;
                }
                .stats-card:hover .stats-title,
                .stats-card:hover .stats-number {
                    color: #ffffff !important;
                }
                .stats-card:hover .stats-label {
                    color: #cccccc !important;
                }
                .stats-card:hover i {
                    color: #ffffff !important;
                }
            </style>

            <!-- Bedrijf Opzeggen -->
            <div class="card" style="border-color:#dc2626;background:rgba(220,38,38,0.1)">
                <h4 style="color:#ef4444;margin-bottom:0.75rem"><i class="fas fa-exclamation-triangle"></i> <?= $__('cancel_business') ?></h4>
                <p style="color:var(--text-light);font-size:0.9rem;margin-bottom:1rem">
                    <?= $__('delete_business_warning') ?>
                </p>
                <button type="button" onclick="showBusinessDeleteModal()" class="btn" style="width:100%;background:#dc2626;color:white;border:none;padding:0.75rem;border-radius:8px;font-weight:600;cursor:pointer">
                    <i class="fas fa-trash-alt"></i> <?= $__('delete_business') ?>
                </button>
            </div>
        </div>
    </div>

    <div style="position:sticky;bottom:0;background:var(--secondary);padding:1rem 0;margin-top:2rem;border-top:1px solid var(--border)">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?= $__('save_profile') ?>
        </button>
    </div>
</form>

<!-- Delete Business Modal -->
<div id="businessDeleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.8);z-index:10000;display:none;align-items:center;justify-content:center;padding:1rem">
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:450px;width:100%">
        <div style="text-align:center;margin-bottom:1.5rem">
            <i class="fas fa-exclamation-triangle" style="color:#ef4444;font-size:2.5rem"></i>
            <h3 style="color:var(--text);margin:1rem 0 0 0"><?= $__('delete_business_confirm') ?></h3>
        </div>
        <p style="color:var(--text-light);line-height:1.6;margin-bottom:1rem">
            <?= $__('confirm_delete_business') ?>
        </p>
        <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);border-radius:10px;padding:1rem;margin-bottom:1.5rem">
            <p style="color:#ef4444;font-size:0.9rem;margin:0"><strong><?= $__('delete_business_warning') ?></strong></p>
            <ul style="color:#ef4444;font-size:0.85rem;margin:0.5rem 0 0 1.25rem;padding:0">
                <li><?= $__('delete_all_bookings') ?></li>
                <li><?= $__('delete_all_services') ?></li>
                <li><?= $__('delete_all_reviews') ?></li>
                <li><?= $__('delete_public_page') ?></li>
            </ul>
        </div>
        <p style="color:var(--text);margin-bottom:0.75rem"><strong><?= $__('type_delete_confirm') ?></strong></p>
        <form method="POST" action="/business/delete" id="businessDeleteForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
            <input type="text" name="confirm_text" id="businessConfirmText" placeholder="<?= $__('type_delete') ?>" autocomplete="off" style="width:100%;padding:0.875rem 1rem;border:2px solid var(--border);border-radius:10px;font-size:1rem;background:var(--secondary);color:var(--text);box-sizing:border-box">
            <div style="display:flex;gap:1rem;margin-top:1.5rem">
                <button type="button" onclick="hideBusinessDeleteModal()" style="flex:1;padding:0.875rem;background:var(--secondary);color:var(--text);border:1px solid var(--border);border-radius:10px;font-weight:600;cursor:pointer"><?= $__('cancel') ?></button>
                <button type="submit" id="businessDeleteBtn" disabled style="flex:1;padding:0.875rem;background:#dc2626;color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;opacity:0.5">
                    <i class="fas fa-trash-alt"></i> <?= $__('delete') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function copyLink() {
        const link = 'https://glamourschedule.nl/business/<?= htmlspecialchars($business['slug'] ?? '') ?>';
        navigator.clipboard.writeText(link).then(() => {
            alert(<?= json_encode($__('link_copied')) ?>);
        });
    }

    function showBusinessDeleteModal() {
        document.getElementById('businessDeleteModal').style.display = 'flex';
        document.getElementById('businessConfirmText').focus();
    }

    function hideBusinessDeleteModal() {
        document.getElementById('businessDeleteModal').style.display = 'none';
        document.getElementById('businessConfirmText').value = '';
        document.getElementById('businessDeleteBtn').disabled = true;
        document.getElementById('businessDeleteBtn').style.opacity = '0.5';
    }

    document.getElementById('businessConfirmText').addEventListener('input', function() {
        const isValid = this.value === 'VERWIJDER';
        document.getElementById('businessDeleteBtn').disabled = !isValid;
        document.getElementById('businessDeleteBtn').style.opacity = isValid ? '1' : '0.5';
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideBusinessDeleteModal();
    });
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

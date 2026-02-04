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
    $translations['monday'] ?? 'Monday',
    $translations['tuesday'] ?? 'Tuesday',
    $translations['wednesday'] ?? 'Wednesday',
    $translations['thursday'] ?? 'Thursday',
    $translations['friday'] ?? 'Friday',
    $translations['saturday'] ?? 'Saturday',
    $translations['sunday'] ?? 'Sunday'
];
?>

<form method="POST" action="/business/profile">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="grid grid-2">
        <!-- Business Info -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building"></i> <?= $translations['business_info'] ?? 'Business Details' ?></h3>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $translations['business_name_label'] ?? 'Business name' ?> *</label>
                    <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($business['company_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $translations['email_label'] ?? 'Email address' ?> *</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($business['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $translations['phone_label'] ?? 'Phone number' ?></label>
                    <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($business['phone'] ?? '') ?>" placeholder="06-12345678">
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $translations['website_label'] ?? 'Website' ?></label>
                    <input type="url" name="website" class="form-control" value="<?= htmlspecialchars($business['website'] ?? '') ?>" placeholder="https://www.example.com">
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> <?= $translations['address_info'] ?? 'Address Details' ?></h3>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label"><?= $translations['street_label'] ?? 'Street' ?></label>
                        <input type="text" name="street" class="form-control" value="<?= htmlspecialchars($business['street'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $translations['house_number_label'] ?? 'House number' ?></label>
                        <input type="text" name="house_number" class="form-control" value="<?= htmlspecialchars($business['house_number'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label"><?= $translations['postal_code'] ?? 'Postal code' ?></label>
                        <input type="text" name="postal_code" class="form-control" value="<?= htmlspecialchars($business['postal_code'] ?? '') ?>" placeholder="1234 AB">
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $translations['city'] ?? 'City' ?></label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($business['city'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- IBAN / Bankgegevens -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-university"></i> <?= $translations['bank_details_payouts'] ?? 'Bank Details (Payouts)' ?></h3>
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
                                <p style="margin:0;font-weight:600;color:#ffffff"><?= $translations['iban_verified'] ?? 'IBAN Verified' ?></p>
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
                                    <p style="margin:0;font-weight:600;color:#ffffff"><?= $translations['security_period_active'] ?? 'Security period active' ?></p>
                                    <p style="margin:0.25rem 0 0 0;font-size:0.85rem;color:#999999">
                                        <?= str_replace(':hours', $hoursRemaining, $translations['payouts_available_in'] ?? 'Payouts available in :hours hours (72 hours after IBAN change)') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted" style="font-size:0.85rem">
                            <i class="fas fa-info-circle"></i> Je IBAN is geverifieerd. Uitbetalingen worden naar dit rekeningnummer overgemaakt.
                        </p>
                    <?php endif; ?>

                    <a href="/business/change-iban" class="btn btn-secondary" style="margin-top:1rem">
                        <i class="fas fa-edit"></i> <?= $translations['iban_change'] ?? 'Change IBAN' ?>
                    </a>

                <?php else: ?>
                    <!-- IBAN Toevoegen -->
                    <div style="background:linear-gradient(135deg,#1a1a1a,#0a0a0a);border:1px solid #333333;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem">
                        <h4 style="margin:0 0 0.5rem 0;color:#ffffff"><i class="fas fa-shield-alt"></i> <?= $translations['secure_iban_verification'] ?? 'Secure IBAN Verification' ?></h4>
                        <p style="margin:0;color:#999999;font-size:0.9rem">
                            <?= $translations['iban_verification_desc'] ?? 'Link your bank account via a €0.01 iDEAL payment. Your IBAN will be automatically retrieved.' ?>
                        </p>
                    </div>

                    <!-- Use formaction to override the parent form action -->
                    <button type="submit" formaction="/business/iban/add" class="btn btn-primary" style="width:100%;padding:1rem;font-size:1.1rem">
                        <i class="fas fa-university"></i> <?= $translations['iban_add_via_ideal'] ?? 'Add IBAN via iDEAL' ?>
                    </button>
                    <p class="text-muted text-center" style="margin-top:1rem;font-size:0.85rem">
                        <i class="fas fa-lock"></i> <?= $translations['iban_verification_note'] ?? '€0.01 verification payment - your IBAN will be automatically linked' ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Cash Betaling Optie -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> <?= $translations['cash_payment'] ?? 'Cash Payment' ?></h3>
                </div>

                <div style="padding:1rem;background:#1a1a1a;border:1px solid #333333;border-radius:12px;margin-bottom:1rem">
                    <div style="display:flex;align-items:flex-start;gap:1rem">
                        <label class="switch" style="flex-shrink:0;margin-top:0.25rem">
                            <input type="checkbox" name="cash_payment_enabled" value="1" <?= !empty($business['cash_payment_enabled']) ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                        <div>
                            <p style="margin:0;font-weight:600;color:#ffffff"><?= $translations['cash_payment_enabled'] ?? 'Accept cash payments' ?></p>
                            <p style="margin:0.5rem 0 0 0;font-size:0.9rem;color:#999999">
                                <?= $translations['cash_payment_desc'] ?? 'Allow customers to pay cash on arrival at your salon.' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div style="padding:1rem;background:#1a1a1a;border-radius:12px;border:1px solid #333333">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem">
                        <i class="fas fa-info-circle" style="color:#ffffff;font-size:1.25rem;margin-top:0.125rem"></i>
                        <div>
                            <p style="margin:0;font-weight:600;color:#ffffff"><?= $translations['platform_fee_info'] ?? 'Platform Fee for Cash Payment' ?></p>
                            <p style="margin:0.5rem 0 0 0;font-size:0.9rem;color:#999999">
                                Bij cash betalingen betaalt de klant <strong style="color:#ffffff"><?= $feeData['fee_display'] ?? '€1,75' ?> platform fee</strong> online tijdens het boeken.
                                Dit bedrag wordt afgetrokken van uw openstaande cash saldo.
                            </p>
                            <p style="margin:0.75rem 0 0 0;font-size:0.85rem;color:#999999">
                                <i class="fas fa-calculator"></i> Voorbeeld: Dienst €50 → Klant betaalt <?= $feeData['fee_display'] ?? '€1,75' ?> online + €<?= number_format(50 - ($feeData['fee_amount'] ?? 1.75), 2, ',', '.') ?> cash bij aankomst
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cash Betaling Disclaimer -->
                <div style="padding:1rem;background:#fef2f2;border-radius:12px;border:1px solid #ef4444;margin-top:1rem">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem">
                        <i class="fas fa-exclamation-triangle" style="color:#dc2626;font-size:1.25rem;margin-top:0.125rem"></i>
                        <div>
                            <p style="margin:0;font-weight:600;color:#dc2626"><?= $translations['cash_payment_disclaimer'] ?? 'Important information about cash payments' ?></p>
                            <ul style="margin:0.75rem 0 0 0;padding-left:1.25rem;font-size:0.9rem;color:#991b1b;line-height:1.6">
                                <li><?= $translations['no_show_warning'] ?? 'No-shows & refusals: GlamourSchedule takes no responsibility for customers who refuse to pay or do not show up for cash bookings.' ?></li>
                                <li><?= $translations['your_costs'] ?? 'Your costs: You only pay the platform fee per booking, regardless of whether the customer shows up.' ?></li>
                                <li><?= $translations['cancellation_policy_note'] ?? 'Cancellation policy: The standard 50% cancellation policy for cancellations within 24 hours still applies. For no-shows, you will not receive this amount automatically.' ?></li>
                            </ul>
                            <p style="margin:0.75rem 0 0 0;font-size:0.85rem;color:#991b1b">
                                <i class="fas fa-lightbulb"></i> <?= $translations['tip_online_payments'] ?? 'Tip: Consider accepting only online payments for maximum security.' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> <?= $translations['registration_info'] ?? 'Registration Info' ?></h3>
                </div>

                <div class="grid grid-2">
                    <div>
                        <p class="text-muted" style="font-size:0.85rem"><?= $translations['kvk_number_label'] ?? 'Chamber of Commerce' ?></p>
                        <p style="font-weight:500"><?= htmlspecialchars($business['kvk_number'] ?? ($translations['not_specified'] ?? 'Not specified')) ?></p>
                    </div>
                    <div>
                        <p class="text-muted" style="font-size:0.85rem"><?= $translations['vat_number_label'] ?? 'VAT Number' ?></p>
                        <p style="font-weight:500"><?= htmlspecialchars($business['btw_number'] ?? ($translations['not_specified'] ?? 'Not specified')) ?></p>
                    </div>
                </div>

                <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
                    <p class="text-muted" style="font-size:0.85rem"><?= $translations['account_status'] ?? 'Account Status' ?></p>
                    <span style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 1rem;background:<?= $business['status'] === 'active' ? '#ffffff' : '#ffffff' ?>;color:<?= $business['status'] === 'active' ? '#000000' : '#000000' ?>;border-radius:20px;font-size:0.85rem;font-weight:500">
                        <i class="fas fa-<?= $business['status'] === 'active' ? 'check-circle' : 'clock' ?>"></i>
                        <?= $business['status'] === 'active' ? ($translations['status_active'] ?? 'Active') : ($translations['status_awaiting_verification'] ?? 'Awaiting verification') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Opening Hours -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> <?= $translations['opening_hours'] ?? 'Opening Hours' ?></h3>
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
                            <span class="text-muted" style="font-size:0.85rem"><?= $translations['closed'] ?? 'Closed' ?></span>
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
                    <h3 class="card-title"><i class="fas fa-link"></i> <?= $translations['your_public_page'] ?? 'Your Public Page' ?></h3>
                </div>

                <p class="text-muted" style="margin-bottom:1rem"><?= $translations['public_page_desc'] ?? 'This is the link to your public business page:' ?></p>

                <div style="display:flex;align-items:center;gap:0.5rem;padding:1rem;background:var(--secondary);border-radius:10px">
                    <i class="fas fa-globe" style="color:var(--primary)"></i>
                    <code style="flex:1;word-break:break-all">https://glamourschedule.nl/business/<?= htmlspecialchars($business['slug'] ?? '') ?></code>
                    <button type="button" onclick="copyLink()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <a href="/business/<?= htmlspecialchars($business['slug'] ?? '') ?>" target="_blank" class="btn btn-primary" style="width:100%;margin-top:1rem">
                    <i class="fas fa-external-link-alt"></i> <?= $translations['view_page'] ?? 'View Page' ?>
                </a>
            </div>

            <div class="card stats-card" style="background:linear-gradient(135deg,#ffffff,#f0f0f0);border:1px solid #333333;transition:all 0.3s ease;cursor:default">
                <h4 class="stats-title" style="margin-bottom:0.5rem;color:#000000;transition:color 0.3s ease"><i class="fas fa-chart-line"></i> <?= $translations['statistics'] ?? 'Statistics' ?></h4>
                <div class="grid grid-2" style="margin-top:1rem">
                    <div>
                        <p class="stats-number" style="font-size:2rem;font-weight:700;margin:0;color:#000000;transition:color 0.3s ease"><?= number_format($business['total_reviews'] ?? 0) ?></p>
                        <p class="stats-label" style="color:#333333;font-size:0.85rem;transition:color 0.3s ease"><?= $translations['reviews'] ?? 'Reviews' ?></p>
                    </div>
                    <div>
                        <p class="stats-number" style="font-size:2rem;font-weight:700;margin:0;color:#000000;transition:color 0.3s ease"><?= number_format($business['rating'] ?? 0, 1) ?></p>
                        <p class="stats-label" style="color:#333333;font-size:0.85rem;transition:color 0.3s ease"><?= $translations['average_rating'] ?? 'Average rating' ?></p>
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
                <h4 style="color:#ef4444;margin-bottom:0.75rem"><i class="fas fa-exclamation-triangle"></i> <?= $translations['cancel_business'] ?? 'Cancel Business' ?></h4>
                <p style="color:var(--text-light);font-size:0.9rem;margin-bottom:1rem">
                    <?= $translations['delete_business_warning'] ?? 'Note: This permanently deletes:' ?>
                </p>
                <button type="button" onclick="showBusinessDeleteModal()" class="btn" style="width:100%;background:#dc2626;color:white;border:none;padding:0.75rem;border-radius:8px;font-weight:600;cursor:pointer">
                    <i class="fas fa-trash-alt"></i> <?= $translations['delete_business'] ?? 'Delete Business' ?>
                </button>
            </div>
        </div>
    </div>

    <div style="position:sticky;bottom:0;background:var(--secondary);padding:1rem 0;margin-top:2rem;border-top:1px solid var(--border)">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?= $translations['save_profile'] ?? 'Save Profile' ?>
        </button>
    </div>
</form>

<!-- Delete Business Modal -->
<div id="businessDeleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.8);z-index:10000;display:none;align-items:center;justify-content:center;padding:1rem">
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:450px;width:100%">
        <div style="text-align:center;margin-bottom:1.5rem">
            <i class="fas fa-exclamation-triangle" style="color:#ef4444;font-size:2.5rem"></i>
            <h3 style="color:var(--text);margin:1rem 0 0 0"><?= $translations['delete_business_confirm'] ?? 'Delete Business Permanently?' ?></h3>
        </div>
        <p style="color:var(--text-light);line-height:1.6;margin-bottom:1rem">
            <?= $translations['confirm_delete_business'] ?? 'Are you sure you want to delete this business? This will also delete all related data.' ?>
        </p>
        <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);border-radius:10px;padding:1rem;margin-bottom:1.5rem">
            <p style="color:#ef4444;font-size:0.9rem;margin:0"><strong><?= $translations['delete_business_warning'] ?? 'Note: This permanently deletes:' ?></strong></p>
            <ul style="color:#ef4444;font-size:0.85rem;margin:0.5rem 0 0 1.25rem;padding:0">
                <li><?= $translations['delete_all_bookings'] ?? 'All bookings and calendar' ?></li>
                <li><?= $translations['delete_all_services'] ?? 'All services and prices' ?></li>
                <li><?= $translations['delete_all_reviews'] ?? 'All reviews and photos' ?></li>
                <li><?= $translations['delete_public_page'] ?? 'Your public business page' ?></li>
            </ul>
        </div>
        <p style="color:var(--text);margin-bottom:0.75rem"><strong><?= $translations['type_delete_confirm'] ?? 'Type "DELETE" to confirm:' ?></strong></p>
        <form method="POST" action="/business/delete" id="businessDeleteForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
            <input type="text" name="confirm_text" id="businessConfirmText" placeholder="<?= $translations['type_delete'] ?? 'Type DELETE' ?>" autocomplete="off" style="width:100%;padding:0.875rem 1rem;border:2px solid var(--border);border-radius:10px;font-size:1rem;background:var(--secondary);color:var(--text);box-sizing:border-box">
            <div style="display:flex;gap:1rem;margin-top:1.5rem">
                <button type="button" onclick="hideBusinessDeleteModal()" style="flex:1;padding:0.875rem;background:var(--secondary);color:var(--text);border:1px solid var(--border);border-radius:10px;font-weight:600;cursor:pointer"><?= $translations['cancel'] ?? 'Cancel' ?></button>
                <button type="submit" id="businessDeleteBtn" disabled style="flex:1;padding:0.875rem;background:#dc2626;color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;opacity:0.5">
                    <i class="fas fa-trash-alt"></i> <?= $translations['delete'] ?? 'Delete' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function copyLink() {
        const link = 'https://glamourschedule.nl/business/<?= htmlspecialchars($business['slug'] ?? '') ?>';
        navigator.clipboard.writeText(link).then(() => {
            alert(<?= json_encode($translations['link_copied'] ?? 'Link copied!') ?>);
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

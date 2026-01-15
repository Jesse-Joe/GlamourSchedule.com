<?php ob_start(); ?>

<?php
$days = ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'];
?>

<form method="POST" action="/business/profile">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="grid grid-2">
        <!-- Business Info -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building"></i> Bedrijfsgegevens</h3>
                </div>

                <div class="form-group">
                    <label class="form-label">Bedrijfsnaam *</label>
                    <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($business['company_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">E-mailadres *</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($business['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Telefoonnummer</label>
                    <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($business['phone'] ?? '') ?>" placeholder="06-12345678">
                </div>

                <div class="form-group">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control" value="<?= htmlspecialchars($business['website'] ?? '') ?>" placeholder="https://www.example.com">
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Adresgegevens</h3>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Straat</label>
                        <input type="text" name="street" class="form-control" value="<?= htmlspecialchars($business['street'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Huisnummer</label>
                        <input type="text" name="house_number" class="form-control" value="<?= htmlspecialchars($business['house_number'] ?? '') ?>">
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Postcode</label>
                        <input type="text" name="postal_code" class="form-control" value="<?= htmlspecialchars($business['postal_code'] ?? '') ?>" placeholder="1234 AB">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stad</label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($business['city'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- IBAN / Bankgegevens -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-university"></i> Bankgegevens (Uitbetalingen)</h3>
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
                    <div style="padding:1rem;background:#ffffff;border-radius:10px;border:1px solid #333333;margin-bottom:1rem">
                        <div style="display:flex;align-items:center;gap:0.75rem">
                            <i class="fas fa-check-circle" style="color:#333333;font-size:1.5rem"></i>
                            <div>
                                <p style="margin:0;font-weight:600;color:#000000">IBAN Geverifieerd</p>
                                <p style="margin:0.25rem 0 0 0;font-family:monospace;color:#000000"><?= htmlspecialchars($business['iban']) ?></p>
                                <p style="margin:0.25rem 0 0 0;font-size:0.85rem;color:#000000"><?= htmlspecialchars($business['account_holder'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($isInCoolingPeriod): ?>
                        <!-- 72-hour security delay warning -->
                        <div style="padding:1rem;background:#ffffff;border-radius:10px;border:1px solid #000000;margin-bottom:1rem">
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <i class="fas fa-shield-alt" style="color:#404040;font-size:1.25rem"></i>
                                <div>
                                    <p style="margin:0;font-weight:600;color:#000000">Beveiligingsperiode actief</p>
                                    <p style="margin:0.25rem 0 0 0;font-size:0.85rem;color:#000000">
                                        Uitbetalingen beschikbaar over <?= $hoursRemaining ?> uur (72 uur na IBAN wijziging)
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
                        <i class="fas fa-edit"></i> IBAN Wijzigen
                    </a>

                <?php else: ?>
                    <!-- IBAN Toevoegen -->
                    <div style="background:linear-gradient(135deg,#ffffff,#ffffff);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem">
                        <h4 style="margin:0 0 0.5rem 0;color:#000000"><i class="fas fa-shield-alt"></i> Veilige IBAN Verificatie</h4>
                        <p style="margin:0;color:#000000;font-size:0.9rem">
                            Koppel je bankrekening via een €0,01 iDEAL betaling. Je IBAN wordt automatisch opgehaald.
                        </p>
                    </div>

                    <!-- Use formaction to override the parent form action -->
                    <button type="submit" formaction="/business/iban/add" class="btn btn-primary" style="width:100%;padding:1rem;font-size:1.1rem">
                        <i class="fas fa-university"></i> IBAN Toevoegen via iDEAL
                    </button>
                    <p class="text-muted text-center" style="margin-top:1rem;font-size:0.85rem">
                        <i class="fas fa-lock"></i> €0,01 verificatiebetaling - je IBAN wordt automatisch gekoppeld
                    </p>
                <?php endif; ?>
            </div>

            <!-- Cash Betaling Optie -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> Cash Betaling</h3>
                </div>

                <div style="padding:1rem;background:#f8fafc;border-radius:12px;margin-bottom:1rem">
                    <div style="display:flex;align-items:flex-start;gap:1rem">
                        <label class="switch" style="flex-shrink:0;margin-top:0.25rem">
                            <input type="checkbox" name="cash_payment_enabled" value="1" <?= !empty($business['cash_payment_enabled']) ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                        <div>
                            <p style="margin:0;font-weight:600;color:#000000">Cash betalingen accepteren</p>
                            <p style="margin:0.5rem 0 0 0;font-size:0.9rem;color:#4b5563">
                                Sta klanten toe om contant te betalen bij aankomst in uw salon.
                            </p>
                        </div>
                    </div>
                </div>

                <div style="padding:1rem;background:#ffffff;border-radius:12px;border:1px solid #000000">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem">
                        <i class="fas fa-info-circle" style="color:#000000;font-size:1.25rem;margin-top:0.125rem"></i>
                        <div>
                            <p style="margin:0;font-weight:600;color:#000000">Platform Fee bij Cash Betaling</p>
                            <p style="margin:0.5rem 0 0 0;font-size:0.9rem;color:#000000">
                                Bij cash betalingen betaalt de klant <strong>€1,75 platform fee</strong> online tijdens het boeken.
                                Dit bedrag wordt afgetrokken van uw openstaande cash saldo.
                            </p>
                            <p style="margin:0.75rem 0 0 0;font-size:0.85rem;color:#000000">
                                <i class="fas fa-calculator"></i> Voorbeeld: Dienst €50 → Klant betaalt €1,75 online + €48,25 cash bij aankomst
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Registratie Info</h3>
                </div>

                <div class="grid grid-2">
                    <div>
                        <p class="text-muted" style="font-size:0.85rem">KvK Nummer</p>
                        <p style="font-weight:500"><?= htmlspecialchars($business['kvk_number'] ?? 'Niet opgegeven') ?></p>
                    </div>
                    <div>
                        <p class="text-muted" style="font-size:0.85rem">BTW Nummer</p>
                        <p style="font-weight:500"><?= htmlspecialchars($business['btw_number'] ?? 'Niet opgegeven') ?></p>
                    </div>
                </div>

                <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
                    <p class="text-muted" style="font-size:0.85rem">Account Status</p>
                    <span style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 1rem;background:<?= $business['status'] === 'active' ? '#ffffff' : '#ffffff' ?>;color:<?= $business['status'] === 'active' ? '#000000' : '#000000' ?>;border-radius:20px;font-size:0.85rem;font-weight:500">
                        <i class="fas fa-<?= $business['status'] === 'active' ? 'check-circle' : 'clock' ?>"></i>
                        <?= $business['status'] === 'active' ? 'Actief' : 'In afwachting van verificatie' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Opening Hours -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Openingstijden</h3>
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
                            <span class="text-muted" style="font-size:0.85rem">Gesloten</span>
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
                    <h3 class="card-title"><i class="fas fa-link"></i> Je Publieke Pagina</h3>
                </div>

                <p class="text-muted" style="margin-bottom:1rem">Dit is de link naar je publieke bedrijfspagina:</p>

                <div style="display:flex;align-items:center;gap:0.5rem;padding:1rem;background:var(--secondary);border-radius:10px">
                    <i class="fas fa-globe" style="color:var(--primary)"></i>
                    <code style="flex:1;word-break:break-all">https://glamourschedule.nl/business/<?= htmlspecialchars($business['slug'] ?? '') ?></code>
                    <button type="button" onclick="copyLink()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <a href="/business/<?= htmlspecialchars($business['slug'] ?? '') ?>" target="_blank" class="btn btn-primary" style="width:100%;margin-top:1rem">
                    <i class="fas fa-external-link-alt"></i> Bekijk Pagina
                </a>
            </div>

            <div class="card" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white">
                <h4 style="margin-bottom:0.5rem"><i class="fas fa-chart-line"></i> Statistieken</h4>
                <div class="grid grid-2" style="margin-top:1rem">
                    <div>
                        <p style="font-size:2rem;font-weight:700;margin:0"><?= number_format($business['total_reviews'] ?? 0) ?></p>
                        <p style="opacity:0.9;font-size:0.85rem">Reviews</p>
                    </div>
                    <div>
                        <p style="font-size:2rem;font-weight:700;margin:0"><?= number_format($business['rating'] ?? 0, 1) ?></p>
                        <p style="opacity:0.9;font-size:0.85rem">Gemiddelde score</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="position:sticky;bottom:0;background:var(--secondary);padding:1rem 0;margin-top:2rem;border-top:1px solid var(--border)">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Profiel Opslaan
        </button>
    </div>
</form>

<script>
    function copyLink() {
        const link = 'https://glamourschedule.nl/business/<?= htmlspecialchars($business['slug'] ?? '') ?>';
        navigator.clipboard.writeText(link).then(() => {
            alert('Link gekopieerd!');
        });
    }
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

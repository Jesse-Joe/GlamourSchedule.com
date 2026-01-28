<?php ob_start(); ?>

<form method="POST" action="/business/website">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="grid grid-2">
        <!-- Main Content -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-heading"></i> Teksten</h3>
                </div>

                <div class="form-group">
                    <label class="form-label">Tagline / Slogan</label>
                    <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($settings['tagline'] ?? '') ?>" placeholder="Bijv: De beste kapper in Amsterdam">
                    <p class="form-hint">Een korte zin die onder je bedrijfsnaam wordt weergegeven</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Bedrijfsomschrijving</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Vertel over je bedrijf..."><?= htmlspecialchars($business['description'] ?? '') ?></textarea>
                    <p class="form-hint">Dit wordt getoond op je publieke pagina</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Welkomstbericht</label>
                    <textarea name="welcome_message" class="form-control" rows="3" placeholder="Welkom bij..."><?= htmlspecialchars($settings['welcome_message'] ?? '') ?></textarea>
                    <p class="form-hint">Optioneel welkomstbericht voor bezoekers</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Over Ons Sectie</h3>
                </div>

                <div class="form-group">
                    <label class="form-label">Titel "Over Ons"</label>
                    <input type="text" name="about_title" class="form-control" value="<?= htmlspecialchars($settings['about_title'] ?? '') ?>" placeholder="Bijv: Over ons salon">
                </div>

                <div class="form-group">
                    <label class="form-label">Over Ons Tekst</label>
                    <textarea name="about_text" class="form-control" rows="5" placeholder="Vertel je verhaal..."><?= htmlspecialchars($settings['about_text'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-share-alt"></i> Social Media</h3>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fab fa-facebook" style="color:#1877f2"></i> Facebook</label>
                    <input type="url" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/...">
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fab fa-instagram" style="color:#e4405f"></i> Instagram</label>
                    <input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/...">
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fab fa-twitter" style="color:#1da1f2"></i> Twitter/X</label>
                    <input type="url" name="twitter_url" class="form-control" value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>" placeholder="https://twitter.com/...">
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fab fa-tiktok"></i> TikTok</label>
                    <input type="url" name="tiktok_url" class="form-control" value="<?= htmlspecialchars($settings['tiktok_url'] ?? '') ?>" placeholder="https://tiktok.com/@...">
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-eye"></i> Weergave Opties</h3>
                </div>

                <div style="display:flex;flex-direction:column;gap:1rem">
                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_reviews" <?= ($settings['show_reviews'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--b-accent)">
                        <span>Toon reviews op pagina</span>
                    </label>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_prices" <?= ($settings['show_prices'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--b-accent)">
                        <span>Toon prijzen bij diensten</span>
                    </label>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_duration" <?= ($settings['show_duration'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--b-accent)">
                        <span>Toon duur bij diensten</span>
                    </label>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_availability" <?= ($settings['show_availability'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--b-accent)">
                        <span>Toon beschikbaarheid kalender</span>
                    </label>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star"></i> <?= $translations['loyalty_points'] ?? 'Loyaliteitspunten' ?></h3>
                </div>

                <div style="display:flex;flex-direction:column;gap:1rem">
                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="loyalty_enabled" id="loyalty_enabled" <?= ($settings['loyalty_enabled'] ?? 0) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--b-accent)">
                        <span><?= $translations['loyalty_enable'] ?? 'Loyaliteitspunten inschakelen' ?></span>
                    </label>

                    <div id="loyalty_settings" style="display:<?= ($settings['loyalty_enabled'] ?? 0) ? 'block' : 'none' ?>;margin-top:0.5rem;padding:1rem;background:var(--b-bg-surface);border-radius:8px;">
                        <div class="form-group" style="margin-bottom:1rem">
                            <label class="form-label"><?= $translations['loyalty_max_redeem'] ?? 'Max. punten per boeking' ?></label>
                            <select name="loyalty_max_redeem_points" class="form-control">
                                <option value="1000" <?= ($settings['loyalty_max_redeem_points'] ?? 2000) == 1000 ? 'selected' : '' ?>>1000 <?= $translations['points'] ?? 'punten' ?> (10% max)</option>
                                <option value="2000" <?= ($settings['loyalty_max_redeem_points'] ?? 2000) == 2000 ? 'selected' : '' ?>>2000 <?= $translations['points'] ?? 'punten' ?> (20% max)</option>
                                <option value="5000" <?= ($settings['loyalty_max_redeem_points'] ?? 2000) == 5000 ? 'selected' : '' ?>>5000 <?= $translations['points'] ?? 'punten' ?> (50% max)</option>
                            </select>
                        </div>

                        <div style="background:var(--b-bg-card);border-radius:8px;padding:1rem;font-size:0.85rem;">
                            <p style="margin:0 0 0.5rem;color:var(--b-text-muted)"><strong><?= $translations['how_it_works'] ?? 'Hoe werkt het?' ?></strong></p>
                            <ul style="margin:0;padding-left:1.25rem;color:var(--b-text-muted)">
                                <li><?= $translations['loyalty_earn_booking'] ?? 'Klanten verdienen 100 punten per voltooide boeking' ?></li>
                                <li><?= $translations['loyalty_earn_review'] ?? 'Klanten verdienen 35 punten per review' ?></li>
                                <li><?= $translations['loyalty_redeem_info'] ?? '100 punten = 1% korting op de dienstprijs' ?></li>
                                <li><?= $translations['loyalty_platform_fee'] ?? 'Platformkosten blijven altijd van toepassing' ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            document.getElementById('loyalty_enabled').addEventListener('change', function() {
                document.getElementById('loyalty_settings').style.display = this.checked ? 'block' : 'none';
            });
            </script>

            <div class="card" style="background:linear-gradient(135deg,#333333,#111111);color:white">
                <h4 style="margin-bottom:0.5rem"><i class="fas fa-lightbulb"></i> Tip</h4>
                <p style="font-size:0.9rem;opacity:0.9">Voeg goede foto's en een duidelijke beschrijving toe om meer klanten aan te trekken!</p>
                <a href="/business/photos" class="btn" style="background:var(--b-bg-card);color:var(--b-accent);margin-top:1rem">
                    <i class="fas fa-images"></i> Foto's Beheren
                </a>
            </div>
        </div>
    </div>

    <div style="position:sticky;bottom:0;background:var(--b-bg-surface);padding:1rem 0;margin-top:2rem;border-top:1px solid var(--b-border)">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Opslaan
        </button>
        <a href="/business/<?= htmlspecialchars($business['slug']) ?>" target="_blank" class="btn btn-secondary" style="margin-left:0.5rem">
            <i class="fas fa-external-link-alt"></i> Bekijk Pagina
        </a>
    </div>
</form>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

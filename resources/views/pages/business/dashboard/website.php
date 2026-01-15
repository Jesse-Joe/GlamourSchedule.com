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
                        <input type="checkbox" name="show_reviews" <?= ($settings['show_reviews'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--primary)">
                        <span>Toon reviews op pagina</span>
                    </label>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_prices" <?= ($settings['show_prices'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--primary)">
                        <span>Toon prijzen bij diensten</span>
                    </label>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_duration" <?= ($settings['show_duration'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--primary)">
                        <span>Toon duur bij diensten</span>
                    </label>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_availability" <?= ($settings['show_availability'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--primary)">
                        <span>Toon beschikbaarheid kalender</span>
                    </label>
                </div>
            </div>

            <div class="card" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white">
                <h4 style="margin-bottom:0.5rem"><i class="fas fa-lightbulb"></i> Tip</h4>
                <p style="font-size:0.9rem;opacity:0.9">Voeg goede foto's en een duidelijke beschrijving toe om meer klanten aan te trekken!</p>
                <a href="/business/photos" class="btn" style="background:#ffffff;color:var(--primary);margin-top:1rem">
                    <i class="fas fa-images"></i> Foto's Beheren
                </a>
            </div>
        </div>
    </div>

    <div style="position:sticky;bottom:0;background:var(--secondary);padding:1rem 0;margin-top:2rem;border-top:1px solid var(--border)">
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

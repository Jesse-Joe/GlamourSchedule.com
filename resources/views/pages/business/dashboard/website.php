<?php ob_start(); ?>

<form method="POST" action="/business/website">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="grid grid-2">
        <!-- Main Content -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-heading"></i> <?= $__('texts') ?></h3>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $__('tagline_slogan') ?></label>
                    <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($settings['tagline'] ?? '') ?>" placeholder="<?= $__('tagline_example') ?>">
                    <p class="form-hint"><?= $__('tagline_description') ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $__('business_description') ?></label>
                    <textarea name="description" class="form-control" rows="4" placeholder="<?= $__('tell_about_business') ?>"><?= htmlspecialchars($business['description'] ?? '') ?></textarea>
                    <p class="form-hint"><?= $__('shown_on_public_page') ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $__('welcome_message') ?></label>
                    <textarea name="welcome_message" class="form-control" rows="3" placeholder="<?= $__('welcome_message_example') ?>"><?= htmlspecialchars($settings['welcome_message'] ?? '') ?></textarea>
                    <p class="form-hint"><?= $__('welcome_message_description') ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> <?= $__('about_us_section') ?></h3>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $__('about_us_title') ?></label>
                    <input type="text" name="about_title" class="form-control" value="<?= htmlspecialchars($settings['about_title'] ?? '') ?>" placeholder="<?= $__('about_us_title_example') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label"><?= $__('about_us_text') ?></label>
                    <textarea name="about_text" class="form-control" rows="5" placeholder="<?= $__('tell_your_story') ?>"><?= htmlspecialchars($settings['about_text'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-share-alt"></i> <?= $__('social_media') ?></h3>
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
                    <h3 class="card-title"><i class="fas fa-eye"></i> <?= $__('display_options') ?></h3>
                </div>

                <div style="display:flex;flex-direction:column;gap:1rem">
                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_reviews" <?= ($settings['show_reviews'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--primary)">
                        <span><?= $__('show_reviews') ?></span>
                    </label>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_prices" <?= ($settings['show_prices'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--primary)">
                        <span><?= $__('show_prices') ?></span>
                    </label>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_duration" <?= ($settings['show_duration'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--primary)">
                        <span><?= $__('show_duration') ?></span>
                    </label>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="show_availability" <?= ($settings['show_availability'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--primary)">
                        <span><?= $__('show_availability') ?></span>
                    </label>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star"></i> <?= $__('loyalty_points') ?></h3>
                </div>

                <div style="display:flex;flex-direction:column;gap:1rem">
                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer">
                        <input type="checkbox" name="loyalty_enabled" id="loyalty_enabled" <?= ($settings['loyalty_enabled'] ?? 0) ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:var(--primary)">
                        <span><?= $__('loyalty_enable') ?></span>
                    </label>

                    <div id="loyalty_settings" style="display:<?= ($settings['loyalty_enabled'] ?? 0) ? 'block' : 'none' ?>;margin-top:0.5rem;padding:1rem;background:var(--secondary);border-radius:8px;">
                        <div class="form-group" style="margin-bottom:1rem">
                            <label class="form-label"><?= $__('loyalty_max_redeem') ?></label>
                            <select name="loyalty_max_redeem_points" class="form-control">
                                <option value="1000" <?= ($settings['loyalty_max_redeem_points'] ?? 2000) == 1000 ? 'selected' : '' ?>>1000 <?= $__('points') ?> (10% max)</option>
                                <option value="2000" <?= ($settings['loyalty_max_redeem_points'] ?? 2000) == 2000 ? 'selected' : '' ?>>2000 <?= $__('points') ?> (20% max)</option>
                                <option value="5000" <?= ($settings['loyalty_max_redeem_points'] ?? 2000) == 5000 ? 'selected' : '' ?>>5000 <?= $__('points') ?> (50% max)</option>
                            </select>
                        </div>

                        <div style="background:var(--bg-card);border-radius:8px;padding:1rem;font-size:0.85rem;">
                            <p style="margin:0 0 0.5rem;color:var(--text-light)"><strong><?= $__('how_it_works') ?></strong></p>
                            <ul style="margin:0;padding-left:1.25rem;color:var(--text-light)">
                                <li><?= $__('loyalty_earn_booking') ?></li>
                                <li><?= $__('loyalty_earn_review') ?></li>
                                <li><?= $__('loyalty_redeem_info') ?></li>
                                <li><?= $__('loyalty_platform_fee') ?></li>
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

            <div class="card" style="background:linear-gradient(135deg,#ffffff,#f0f0f0);border:1px solid #333333">
                <h4 style="margin-bottom:0.5rem;color:#000000"><i class="fas fa-lightbulb" style="color:#f5c518"></i> <?= $__('tip') ?></h4>
                <p style="font-size:0.9rem;color:#333333"><?= $__('website_tip_photos') ?></p>
                <a href="/business/photos" class="btn" style="background:#000000;color:#ffffff;margin-top:1rem">
                    <i class="fas fa-images"></i> <?= $__('manage_photos') ?>
                </a>
            </div>
        </div>
    </div>

    <div style="position:sticky;bottom:0;background:var(--secondary);padding:1rem 0;margin-top:2rem;border-top:1px solid var(--border)">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?= $__('save') ?>
        </button>
        <a href="/business/<?= htmlspecialchars($business['slug']) ?>" target="_blank" class="btn btn-secondary" style="margin-left:0.5rem">
            <i class="fas fa-external-link-alt"></i> <?= $__('view_page') ?>
        </a>
    </div>
</form>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

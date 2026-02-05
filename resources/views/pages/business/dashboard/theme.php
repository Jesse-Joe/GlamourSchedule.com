<?php ob_start(); ?>

<style>
    .color-presets {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
    }
    .color-preset {
        border: 3px solid transparent;
        border-radius: 12px;
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--white);
    }
    .color-preset:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .color-preset.active {
        border-color: var(--primary);
    }
    .color-preset-preview {
        display: flex;
        gap: 0.25rem;
        margin-bottom: 0.5rem;
    }
    .color-preset-preview span {
        flex: 1;
        height: 25px;
        border-radius: 6px;
    }
    .color-preset-name {
        font-size: 0.75rem;
        font-weight: 500;
        text-align: center;
    }
    .color-picker-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .color-picker-wrapper input[type="color"] {
        width: 50px;
        height: 50px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        padding: 0;
    }
    .color-picker-wrapper input[type="text"] {
        flex: 1;
    }
    .preview-card {
        background: var(--white);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .preview-header {
        padding: 2rem;
        color: white;
        text-align: center;
    }
    .preview-content {
        padding: 1.5rem;
    }
    .preview-btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        color: white;
        text-decoration: none;
        font-weight: 600;
    }

    /* Font Preview Styles */
    .font-options {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }
    .font-option {
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }
    .font-option:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
    }
    .font-option.active {
        border-color: var(--primary);
        background: rgba(0,0,0,0.02);
    }
    .font-option-preview {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        min-height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .font-option-name {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* Layout Templates */
    .layout-options {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    .layout-option {
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .layout-option:hover {
        border-color: var(--primary);
    }
    .layout-option.active {
        border-color: var(--primary);
        background: rgba(0,0,0,0.02);
    }
    .layout-preview {
        background: #f5f5f5;
        border-radius: 8px;
        padding: 0.5rem;
        margin-bottom: 0.75rem;
        min-height: 100px;
    }
    .layout-preview-header {
        background: #333;
        height: 25px;
        border-radius: 4px;
        margin-bottom: 0.5rem;
    }
    .layout-preview-content {
        display: flex;
        gap: 0.25rem;
    }
    .layout-preview-sidebar {
        width: 30%;
        background: #ddd;
        border-radius: 4px;
        min-height: 50px;
    }
    .layout-preview-main {
        flex: 1;
        background: #e5e5e5;
        border-radius: 4px;
        min-height: 50px;
    }
    .layout-option-name {
        font-weight: 600;
        text-align: center;
        font-size: 0.9rem;
    }
    .layout-option-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-align: center;
        margin-top: 0.25rem;
    }

    /* Button Style Options */
    .button-style-options {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .button-style-option {
        flex: 1;
        min-width: 120px;
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }
    .button-style-option:hover,
    .button-style-option.active {
        border-color: var(--primary);
    }
    .button-style-preview {
        margin-bottom: 0.5rem;
    }
    .btn-preview {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: #000;
        color: #fff;
        font-size: 0.8rem;
    }
    .btn-preview.rounded { border-radius: 25px; }
    .btn-preview.square { border-radius: 4px; }
    .btn-preview.pill { border-radius: 50px; padding: 0.5rem 1.5rem; }
    .btn-preview.sharp { border-radius: 0; }

    /* Custom CSS Editor */
    .css-editor {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.85rem;
        line-height: 1.5;
        min-height: 200px;
        background: #1e1e1e;
        color: #d4d4d4;
        border-radius: 8px;
        padding: 1rem;
        resize: vertical;
    }
    .css-editor:focus {
        outline: none;
        box-shadow: 0 0 0 2px var(--primary);
    }

    /* Tabs */
    .theme-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #333333;
        padding-bottom: 0.5rem;
        overflow-x: auto;
    }
    .theme-tab {
        padding: 0.75rem 1.25rem;
        border: none;
        background: none;
        cursor: pointer;
        font-weight: 500;
        color: #ffffff;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .theme-tab:hover {
        color: #ffffff;
        background: rgba(255,255,255,0.1);
    }
    .theme-tab.active {
        color: #ffffff;
        background: rgba(255,255,255,0.15);
        border-bottom: 2px solid #ffffff;
        margin-bottom: -2px;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    /* Mobile Theme Dropdown */
    .theme-tabs-mobile {
        display: none;
        margin-bottom: 1.5rem;
    }
    .theme-tabs-mobile select {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        font-weight: 500;
        border: 2px solid #333333;
        border-radius: 8px;
        background: #1a1a1a;
        color: #ffffff;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffffff' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
    }
    .theme-tabs-mobile select:focus {
        outline: none;
        border-color: #ffffff;
    }

    @media (max-width: 768px) {
        .theme-tabs {
            display: none;
        }
        .theme-tabs-mobile {
            display: block;
        }
    }
</style>

<form method="POST" action="/business/theme" id="themeForm">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <!-- Theme Tabs (Desktop) -->
    <div class="theme-tabs">
        <button type="button" class="theme-tab active" data-tab="colors"><i class="fas fa-palette"></i> <?= $__('colors') ?></button>
        <button type="button" class="theme-tab" data-tab="fonts"><i class="fas fa-font"></i> <?= $__('fonts') ?></button>
        <button type="button" class="theme-tab" data-tab="layout"><i class="fas fa-th-large"></i> <?= $__('layout') ?></button>
        <button type="button" class="theme-tab" data-tab="styling"><i class="fas fa-paint-brush"></i> <?= $__('styling') ?></button>
        <button type="button" class="theme-tab" data-tab="advanced"><i class="fas fa-code"></i> <?= $__('advanced') ?></button>
    </div>

    <!-- Theme Tabs (Mobile Dropdown) -->
    <div class="theme-tabs-mobile">
        <select id="themeTabsMobile">
            <option value="colors"><?= $__('colors') ?></option>
            <option value="fonts"><?= $__('fonts') ?></option>
            <option value="layout"><?= $__('layout') ?></option>
            <option value="styling"><?= $__('styling') ?></option>
            <option value="advanced"><?= $__('advanced') ?></option>
        </select>
    </div>

    <div class="grid grid-2">
        <div>
            <!-- TAB: Colors -->
            <div class="tab-content active" id="tab-colors">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-swatchbook"></i> <?= $__('color_presets') ?></h3>
                    </div>
                    <p class="text-muted" style="margin-bottom:1rem"><?= $__('choose_color_scheme') ?></p>

                    <div class="color-presets">
                        <?php foreach ($colorPresets as $preset): ?>
                            <div class="color-preset" onclick="selectPreset('<?= $preset['primary'] ?>', '<?= $preset['secondary'] ?>', '<?= $preset['accent'] ?>')">
                                <div class="color-preset-preview">
                                    <span style="background:<?= $preset['primary'] ?>"></span>
                                    <span style="background:<?= $preset['secondary'] ?>"></span>
                                    <span style="background:<?= $preset['accent'] ?>"></span>
                                </div>
                                <div class="color-preset-name"><?= htmlspecialchars($preset['name']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-sliders-h"></i> <?= $__('custom_colors') ?></h3>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('primary_color') ?></label>
                        <div class="color-picker-wrapper">
                            <input type="color" id="primaryColorPicker" value="<?= htmlspecialchars($settings['primary_color'] ?? '#000000') ?>" onchange="updateColor('primary', this.value)">
                            <input type="text" name="primary_color" id="primaryColor" class="form-control" value="<?= htmlspecialchars($settings['primary_color'] ?? '#000000') ?>" pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <p class="form-hint"><?= $__('primary_color_description') ?></p>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('secondary_color') ?></label>
                        <div class="color-picker-wrapper">
                            <input type="color" id="secondaryColorPicker" value="<?= htmlspecialchars($settings['secondary_color'] ?? '#333333') ?>" onchange="updateColor('secondary', this.value)">
                            <input type="text" name="secondary_color" id="secondaryColor" class="form-control" value="<?= htmlspecialchars($settings['secondary_color'] ?? '#333333') ?>" pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <p class="form-hint"><?= $__('secondary_color_description') ?></p>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('accent_color') ?></label>
                        <div class="color-picker-wrapper">
                            <input type="color" id="accentColorPicker" value="<?= htmlspecialchars($settings['accent_color'] ?? '#fbbf24') ?>" onchange="updateColor('accent', this.value)">
                            <input type="text" name="accent_color" id="accentColor" class="form-control" value="<?= htmlspecialchars($settings['accent_color'] ?? '#fbbf24') ?>" pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <p class="form-hint"><?= $__('accent_color_description') ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-adjust"></i> <?= $__('theme_mode') ?></h3>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('page_theme') ?></label>
                        <select name="theme" class="form-control" id="themeMode">
                            <option value="light" <?= ($business['theme'] ?? 'light') === 'light' ? 'selected' : '' ?>><?= $__('light') ?></option>
                            <option value="dark" <?= ($business['theme'] ?? '') === 'dark' ? 'selected' : '' ?>><?= $__('dark') ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('style_preference') ?></label>
                        <select name="gender_theme" class="form-control">
                            <option value="neutral" <?= ($business['gender_theme'] ?? 'neutral') === 'neutral' ? 'selected' : '' ?>><?= $__('neutral_default') ?></option>
                            <option value="feminine" <?= ($business['gender_theme'] ?? '') === 'feminine' ? 'selected' : '' ?>><?= $__('feminine_soft') ?></option>
                            <option value="masculine" <?= ($business['gender_theme'] ?? '') === 'masculine' ? 'selected' : '' ?>><?= $__('masculine_modern') ?></option>
                        </select>
                        <p class="form-hint"><?= $__('style_preference_hint') ?></p>
                    </div>
                </div>
            </div>

            <!-- TAB: Fonts -->
            <div class="tab-content" id="tab-fonts">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-heading"></i> <?= $__('heading_font') ?></h3>
                    </div>
                    <p class="text-muted" style="margin-bottom:1rem"><?= $__('choose_font_titles') ?></p>

                    <div class="font-options">
                        <div class="font-option <?= ($settings['font_family'] ?? 'playfair') === 'playfair' ? 'active' : '' ?>" data-font="playfair">
                            <div class="font-option-preview" style="font-family: 'Playfair Display', serif; font-style: italic;">
                                Glamour
                            </div>
                            <div class="font-option-name">Playfair Display</div>
                        </div>
                        <div class="font-option <?= ($settings['font_family'] ?? '') === 'cormorant' ? 'active' : '' ?>" data-font="cormorant">
                            <div class="font-option-preview" style="font-family: 'Cormorant Garamond', serif;">
                                Glamour
                            </div>
                            <div class="font-option-name">Cormorant Garamond</div>
                        </div>
                        <div class="font-option <?= ($settings['font_family'] ?? '') === 'lora' ? 'active' : '' ?>" data-font="lora">
                            <div class="font-option-preview" style="font-family: 'Lora', serif;">
                                Glamour
                            </div>
                            <div class="font-option-name">Lora</div>
                        </div>
                        <div class="font-option <?= ($settings['font_family'] ?? '') === 'montserrat' ? 'active' : '' ?>" data-font="montserrat">
                            <div class="font-option-preview" style="font-family: 'Montserrat', sans-serif; font-weight: 600;">
                                Glamour
                            </div>
                            <div class="font-option-name">Montserrat</div>
                        </div>
                        <div class="font-option <?= ($settings['font_family'] ?? '') === 'poppins' ? 'active' : '' ?>" data-font="poppins">
                            <div class="font-option-preview" style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                                Glamour
                            </div>
                            <div class="font-option-name">Poppins</div>
                        </div>
                        <div class="font-option <?= ($settings['font_family'] ?? '') === 'dancing' ? 'active' : '' ?>" data-font="dancing">
                            <div class="font-option-preview" style="font-family: 'Dancing Script', cursive;">
                                Glamour
                            </div>
                            <div class="font-option-name">Dancing Script</div>
                        </div>
                        <div class="font-option <?= ($settings['font_family'] ?? '') === 'great-vibes' ? 'active' : '' ?>" data-font="great-vibes">
                            <div class="font-option-preview" style="font-family: 'Great Vibes', cursive;">
                                Glamour
                            </div>
                            <div class="font-option-name">Great Vibes</div>
                        </div>
                        <div class="font-option <?= ($settings['font_family'] ?? '') === 'raleway' ? 'active' : '' ?>" data-font="raleway">
                            <div class="font-option-preview" style="font-family: 'Raleway', sans-serif; font-weight: 300;">
                                Glamour
                            </div>
                            <div class="font-option-name">Raleway</div>
                        </div>
                    </div>
                    <input type="hidden" name="font_family" id="fontFamily" value="<?= htmlspecialchars($settings['font_family'] ?? 'playfair') ?>">
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-italic"></i> <?= $__('text_style') ?></h3>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('font_style') ?></label>
                        <select name="font_style" class="form-control" id="fontStyle">
                            <option value="elegant" <?= ($settings['font_style'] ?? 'elegant') === 'elegant' ? 'selected' : '' ?>><?= $__('elegant_italic') ?></option>
                            <option value="modern" <?= ($settings['font_style'] ?? '') === 'modern' ? 'selected' : '' ?>><?= $__('modern_straight') ?></option>
                            <option value="bold" <?= ($settings['font_style'] ?? '') === 'bold' ? 'selected' : '' ?>><?= $__('bold_style') ?></option>
                            <option value="light" <?= ($settings['font_style'] ?? '') === 'light' ? 'selected' : '' ?>><?= $__('light_thin') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TAB: Layout -->
            <div class="tab-content" id="tab-layout">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-columns"></i> <?= $__('page_layout') ?></h3>
                    </div>
                    <p class="text-muted" style="margin-bottom:1rem"><?= $__('choose_layout') ?></p>

                    <div class="layout-options">
                        <div class="layout-option <?= ($settings['layout_template'] ?? 'classic') === 'classic' ? 'active' : '' ?>" data-layout="classic">
                            <div class="layout-preview">
                                <div class="layout-preview-header"></div>
                                <div class="layout-preview-content">
                                    <div class="layout-preview-main"></div>
                                </div>
                            </div>
                            <div class="layout-option-name">Classic</div>
                            <div class="layout-option-desc"><?= $__('layout_classic_desc') ?></div>
                        </div>

                        <div class="layout-option <?= ($settings['layout_template'] ?? '') === 'sidebar' ? 'active' : '' ?>" data-layout="sidebar">
                            <div class="layout-preview">
                                <div class="layout-preview-header"></div>
                                <div class="layout-preview-content">
                                    <div class="layout-preview-sidebar"></div>
                                    <div class="layout-preview-main"></div>
                                </div>
                            </div>
                            <div class="layout-option-name"><?= $__('layout_sidebar') ?></div>
                            <div class="layout-option-desc"><?= $__('layout_sidebar_desc') ?></div>
                        </div>

                        <div class="layout-option <?= ($settings['layout_template'] ?? '') === 'hero' ? 'active' : '' ?>" data-layout="hero">
                            <div class="layout-preview">
                                <div class="layout-preview-header" style="height:50px;"></div>
                                <div class="layout-preview-content">
                                    <div class="layout-preview-main"></div>
                                </div>
                            </div>
                            <div class="layout-option-name">Hero Banner</div>
                            <div class="layout-option-desc"><?= $__('layout_hero_desc') ?></div>
                        </div>

                        <div class="layout-option <?= ($settings['layout_template'] ?? '') === 'minimal' ? 'active' : '' ?>" data-layout="minimal">
                            <div class="layout-preview">
                                <div class="layout-preview-content" style="padding-top:0.5rem;">
                                    <div class="layout-preview-main" style="min-height:70px;"></div>
                                </div>
                            </div>
                            <div class="layout-option-name"><?= $__('layout_minimal') ?></div>
                            <div class="layout-option-desc"><?= $__('layout_minimal_desc') ?></div>
                        </div>

                        <div class="layout-option <?= ($settings['layout_template'] ?? '') === 'cards' ? 'active' : '' ?>" data-layout="cards">
                            <div class="layout-preview">
                                <div class="layout-preview-header" style="height:15px;"></div>
                                <div class="layout-preview-content" style="gap:0.15rem;">
                                    <div class="layout-preview-main" style="min-height:30px;"></div>
                                    <div class="layout-preview-main" style="min-height:30px;"></div>
                                </div>
                            </div>
                            <div class="layout-option-name"><?= $__('layout_cards') ?></div>
                            <div class="layout-option-desc"><?= $__('layout_cards_desc') ?></div>
                        </div>

                        <div class="layout-option <?= ($settings['layout_template'] ?? '') === 'magazine' ? 'active' : '' ?>" data-layout="magazine">
                            <div class="layout-preview">
                                <div class="layout-preview-content" style="display:grid;grid-template-columns:1fr 1fr;gap:0.15rem;">
                                    <div class="layout-preview-main" style="min-height:35px;"></div>
                                    <div class="layout-preview-main" style="min-height:35px;"></div>
                                    <div class="layout-preview-main" style="min-height:35px;grid-column:span 2;"></div>
                                </div>
                            </div>
                            <div class="layout-option-name">Magazine</div>
                            <div class="layout-option-desc"><?= $__('layout_magazine_desc') ?></div>
                        </div>
                    </div>
                    <input type="hidden" name="layout_template" id="layoutTemplate" value="<?= htmlspecialchars($settings['layout_template'] ?? 'classic') ?>">
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-images"></i> <?= $__('gallery_style') ?></h3>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('photo_gallery_display') ?></label>
                        <select name="gallery_style" class="form-control">
                            <option value="grid" <?= ($settings['gallery_style'] ?? 'grid') === 'grid' ? 'selected' : '' ?>><?= $__('gallery_grid') ?></option>
                            <option value="carousel" <?= ($settings['gallery_style'] ?? '') === 'carousel' ? 'selected' : '' ?>><?= $__('gallery_carousel') ?></option>
                            <option value="masonry" <?= ($settings['gallery_style'] ?? '') === 'masonry' ? 'selected' : '' ?>><?= $__('gallery_masonry') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TAB: Styling -->
            <div class="tab-content" id="tab-styling">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-square"></i> <?= $__('button_style') ?></h3>
                    </div>
                    <p class="text-muted" style="margin-bottom:1rem"><?= $__('button_style_question') ?></p>

                    <div class="button-style-options">
                        <div class="button-style-option <?= ($settings['button_style'] ?? 'rounded') === 'rounded' ? 'active' : '' ?>" data-style="rounded">
                            <div class="button-style-preview">
                                <span class="btn-preview rounded"><?= $__('book_now') ?></span>
                            </div>
                            <div class="font-option-name"><?= $__('rounded') ?></div>
                        </div>
                        <div class="button-style-option <?= ($settings['button_style'] ?? '') === 'square' ? 'active' : '' ?>" data-style="square">
                            <div class="button-style-preview">
                                <span class="btn-preview square"><?= $__('book_now') ?></span>
                            </div>
                            <div class="font-option-name"><?= $__('square') ?></div>
                        </div>
                        <div class="button-style-option <?= ($settings['button_style'] ?? '') === 'pill' ? 'active' : '' ?>" data-style="pill">
                            <div class="button-style-preview">
                                <span class="btn-preview pill"><?= $__('book_now') ?></span>
                            </div>
                            <div class="font-option-name">Pill</div>
                        </div>
                        <div class="button-style-option <?= ($settings['button_style'] ?? '') === 'sharp' ? 'active' : '' ?>" data-style="sharp">
                            <div class="button-style-preview">
                                <span class="btn-preview sharp"><?= $__('book_now') ?></span>
                            </div>
                            <div class="font-option-name"><?= $__('sharp') ?></div>
                        </div>
                    </div>
                    <input type="hidden" name="button_style" id="buttonStyle" value="<?= htmlspecialchars($settings['button_style'] ?? 'rounded') ?>">
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-layer-group"></i> <?= $__('header_style') ?></h3>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $__('header_background') ?></label>
                        <select name="header_style" class="form-control" id="headerStyle">
                            <option value="gradient" <?= ($settings['header_style'] ?? 'gradient') === 'gradient' ? 'selected' : '' ?>><?= $__('header_gradient') ?></option>
                            <option value="solid" <?= ($settings['header_style'] ?? '') === 'solid' ? 'selected' : '' ?>><?= $__('header_solid') ?></option>
                            <option value="image" <?= ($settings['header_style'] ?? '') === 'image' ? 'selected' : '' ?>><?= $__('header_image') ?></option>
                            <option value="transparent" <?= ($settings['header_style'] ?? '') === 'transparent' ? 'selected' : '' ?>><?= $__('header_transparent') ?></option>
                        </select>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye"></i> <?= $__('display_options') ?></h3>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;">
                            <input type="checkbox" name="show_reviews" value="1" <?= ($settings['show_reviews'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;">
                            <span><?= $__('show_reviews') ?></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;">
                            <input type="checkbox" name="show_prices" value="1" <?= ($settings['show_prices'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;">
                            <span><?= $__('show_prices') ?></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;">
                            <input type="checkbox" name="show_duration" value="1" <?= ($settings['show_duration'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;">
                            <span><?= $__('show_duration') ?></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;">
                            <input type="checkbox" name="show_availability" value="1" <?= ($settings['show_availability'] ?? 1) ? 'checked' : '' ?> style="width:20px;height:20px;">
                            <span><?= $__('show_availability_indicator') ?></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- TAB: Advanced -->
            <div class="tab-content" id="tab-advanced">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-code"></i> <?= $__('custom_css') ?></h3>
                    </div>
                    <p class="text-muted" style="margin-bottom:1rem"><?= $__('add_custom_css') ?></p>

                    <div class="form-group">
                        <textarea name="custom_css" class="css-editor" id="customCss" placeholder="/* <?= $__('your_custom_css') ?> */
.business-page .header {
    /* <?= $__('example') ?> */
}"><?= htmlspecialchars($settings['custom_css'] ?? '') ?></textarea>
                        <p class="form-hint"><?= $__('invalid_css_warning') ?></p>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?= $__('custom_css_advanced_users') ?></span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> <?= $__('css_variables') ?></h3>
                    </div>
                    <p class="text-muted" style="margin-bottom:1rem"><?= $__('available_css_variables') ?></p>

                    <div style="background:#f5f5f5;border-radius:8px;padding:1rem;font-family:monospace;font-size:0.85rem;">
                        <code>--business-primary</code> - <?= $__('primary_color') ?><br>
                        <code>--business-secondary</code> - <?= $__('secondary_color') ?><br>
                        <code>--business-accent</code> - <?= $__('accent_color') ?><br>
                        <code>--business-font</code> - <?= $__('chosen_font') ?><br>
                        <code>--business-radius</code> - Border radius<br>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Column -->
        <div>
            <div class="card" style="position:sticky;top:1rem;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-eye"></i> <?= $__('live_preview') ?></h3>
                </div>

                <div class="preview-card" id="previewCard">
                    <div class="preview-header" id="previewHeader" style="background:linear-gradient(135deg, <?= htmlspecialchars($settings['primary_color'] ?? '#000000') ?>, <?= htmlspecialchars($settings['accent_color'] ?? '#fbbf24') ?>)">
                        <h2 style="margin:0;font-family:'Playfair Display',serif;" id="previewTitle"><?= htmlspecialchars($business['company_name'] ?? $__('my_salon')) ?></h2>
                        <p style="opacity:0.9;margin-top:0.5rem"><?= htmlspecialchars($settings['tagline'] ?? $__('your_beauty_expert')) ?></p>
                    </div>
                    <div class="preview-content">
                        <h4 style="margin-bottom:0.5rem" id="previewSubtitle"><?= $__('our_services') ?></h4>
                        <p class="text-muted" style="font-size:0.9rem"><?= $__('preview_services_text') ?></p>
                        <div style="margin-top:1rem">
                            <a href="#" class="preview-btn" id="previewBtn" style="background:linear-gradient(135deg, <?= htmlspecialchars($settings['primary_color'] ?? '#000000') ?>, <?= htmlspecialchars($settings['accent_color'] ?? '#fbbf24') ?>)">
                                <i class="fas fa-calendar-plus"></i> <?= $__('book_now') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div style="margin-top:1rem;padding:1rem;background:#f9f9f9;border-radius:10px;">
                    <h5 style="margin:0 0 0.5rem;font-size:0.85rem;color:#666;"><?= $__('preview_options') ?></h5>
                    <div style="display:flex;gap:0.5rem;">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="togglePreviewMode('desktop')">
                            <i class="fas fa-desktop"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="togglePreviewMode('mobile')">
                            <i class="fas fa-mobile-alt"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="openFullPreview()">
                            <i class="fas fa-external-link-alt"></i> <?= $__('view_page') ?>
                        </button>
                    </div>
                </div>

                <div class="alert alert-info" style="margin-top:1rem">
                    <i class="fas fa-lightbulb"></i>
                    <span><?= $__('preview_changes_hint') ?></span>
                </div>
            </div>
        </div>
    </div>

    <div style="position:sticky;bottom:0;background:var(--secondary);padding:1rem 0;margin-top:2rem;border-top:1px solid var(--border)">
        <div style="display:flex;gap:1rem;align-items:center;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $__('save_theme') ?>
            </button>
            <button type="button" class="btn btn-secondary" onclick="resetToDefaults()">
                <i class="fas fa-undo"></i> <?= $__('reset_to_default') ?>
            </button>
        </div>
    </div>
</form>

<!-- Load extra fonts for preview -->
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&family=Great+Vibes&family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script>
    // Tab switching function
    function switchToTab(tabId) {
        document.querySelectorAll('.theme-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        const tabButton = document.querySelector('.theme-tab[data-tab="' + tabId + '"]');
        if (tabButton) tabButton.classList.add('active');
        document.getElementById('tab-' + tabId).classList.add('active');

        // Sync mobile dropdown
        document.getElementById('themeTabsMobile').value = tabId;
    }

    // Desktop tab clicking
    document.querySelectorAll('.theme-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            switchToTab(this.dataset.tab);
        });
    });

    // Mobile dropdown change
    document.getElementById('themeTabsMobile').addEventListener('change', function() {
        switchToTab(this.value);
    });

    // Color preset selection
    function selectPreset(primary, secondary, accent) {
        document.getElementById('primaryColor').value = primary;
        document.getElementById('primaryColorPicker').value = primary;
        document.getElementById('secondaryColor').value = secondary;
        document.getElementById('secondaryColorPicker').value = secondary;
        document.getElementById('accentColor').value = accent;
        document.getElementById('accentColorPicker').value = accent;
        updatePreview();

        document.querySelectorAll('.color-preset').forEach(el => el.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }

    // Color picker update
    function updateColor(type, value) {
        document.getElementById(type + 'Color').value = value;
        document.getElementById(type + 'ColorPicker').value = value;
        updatePreview();
    }

    // Font selection
    document.querySelectorAll('.font-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.font-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('fontFamily').value = this.dataset.font;
            updatePreview();
        });
    });

    // Layout selection
    document.querySelectorAll('.layout-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.layout-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('layoutTemplate').value = this.dataset.layout;
        });
    });

    // Button style selection
    document.querySelectorAll('.button-style-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.button-style-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('buttonStyle').value = this.dataset.style;
            updatePreview();
        });
    });

    // Main preview update function
    function updatePreview() {
        const primary = document.getElementById('primaryColor').value;
        const accent = document.getElementById('accentColor').value;
        const headerStyle = document.getElementById('headerStyle').value;
        const buttonStyle = document.getElementById('buttonStyle').value;
        const fontFamily = document.getElementById('fontFamily').value;

        // Update header background
        let headerBg;
        if (headerStyle === 'gradient') {
            headerBg = `linear-gradient(135deg, ${primary}, ${accent})`;
        } else if (headerStyle === 'solid') {
            headerBg = primary;
        } else {
            headerBg = primary;
        }

        document.getElementById('previewHeader').style.background = headerBg;
        document.getElementById('previewBtn').style.background = headerBg;

        // Update button border radius
        const btnRadiusMap = {
            'rounded': '25px',
            'square': '4px',
            'pill': '50px',
            'sharp': '0'
        };
        document.getElementById('previewBtn').style.borderRadius = btnRadiusMap[buttonStyle] || '25px';

        // Update font
        const fontMap = {
            'playfair': "'Playfair Display', serif",
            'cormorant': "'Cormorant Garamond', serif",
            'lora': "'Lora', serif",
            'montserrat': "'Montserrat', sans-serif",
            'poppins': "'Poppins', sans-serif",
            'dancing': "'Dancing Script', cursive",
            'great-vibes': "'Great Vibes', cursive",
            'raleway': "'Raleway', sans-serif"
        };
        const selectedFont = fontMap[fontFamily] || fontMap['playfair'];
        document.getElementById('previewTitle').style.fontFamily = selectedFont;
        document.getElementById('previewSubtitle').style.fontFamily = selectedFont;
    }

    // Sync text input with color picker
    document.querySelectorAll('input[type="text"][id$="Color"]').forEach(input => {
        input.addEventListener('input', function() {
            const type = this.id.replace('Color', '');
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                document.getElementById(type + 'ColorPicker').value = this.value;
                updatePreview();
            }
        });
    });

    // Header style change
    document.getElementById('headerStyle').addEventListener('change', updatePreview);

    // Preview mode toggle
    function togglePreviewMode(mode) {
        const preview = document.getElementById('previewCard');
        if (mode === 'mobile') {
            preview.style.maxWidth = '320px';
            preview.style.margin = '0 auto';
        } else {
            preview.style.maxWidth = 'none';
            preview.style.margin = '0';
        }
    }

    // Open full preview
    function openFullPreview() {
        window.open('/business/<?= $business['slug'] ?? $business['id'] ?>', '_blank');
    }

    // Reset to defaults
    function resetToDefaults() {
        if (confirm('<?= $__('confirm_reset_theme') ?>')) {
            document.getElementById('primaryColor').value = '#000000';
            document.getElementById('primaryColorPicker').value = '#000000';
            document.getElementById('secondaryColor').value = '#333333';
            document.getElementById('secondaryColorPicker').value = '#333333';
            document.getElementById('accentColor').value = '#fbbf24';
            document.getElementById('accentColorPicker').value = '#fbbf24';
            document.getElementById('fontFamily').value = 'playfair';
            document.getElementById('layoutTemplate').value = 'classic';
            document.getElementById('buttonStyle').value = 'rounded';
            document.getElementById('headerStyle').value = 'gradient';

            document.querySelectorAll('.font-option').forEach(o => o.classList.remove('active'));
            document.querySelector('.font-option[data-font="playfair"]').classList.add('active');

            document.querySelectorAll('.layout-option').forEach(o => o.classList.remove('active'));
            document.querySelector('.layout-option[data-layout="classic"]').classList.add('active');

            document.querySelectorAll('.button-style-option').forEach(o => o.classList.remove('active'));
            document.querySelector('.button-style-option[data-style="rounded"]').classList.add('active');

            updatePreview();
        }
    }

    // Initialize preview on load
    updatePreview();
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

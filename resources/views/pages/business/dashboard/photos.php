<?php ob_start(); ?>

<style>
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    .photo-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        background: var(--secondary);
    }
    .photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-item-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        opacity: 0;
        transition: opacity 0.3s;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1rem;
    }
    .photo-item:hover .photo-item-overlay {
        opacity: 1;
    }
    .photo-item-type {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        background: var(--primary);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 5px;
        font-size: 0.7rem;
        text-transform: uppercase;
    }
    .photo-item-actions {
        display: flex;
        gap: 0.5rem;
    }
    .photo-item-actions button {
        flex: 1;
        padding: 0.5rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .photo-item-actions .btn-delete {
        background: var(--danger);
        color: white;
    }
    .upload-zone {
        border: 3px dashed var(--border);
        border-radius: 15px;
        padding: 3rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: var(--primary);
        background: rgba(255, 215, 0, 0.05);
    }
    .upload-zone i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 1rem;
    }

    /* Salon Banner Section */
    .banner-section {
        margin-bottom: 2rem;
    }
    .banner-preview {
        position: relative;
        width: 100%;
        aspect-ratio: 3 / 1;
        max-height: 300px;
        border-radius: 16px;
        overflow: hidden;
        background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
        margin-bottom: 1rem;
        border: 2px solid #333;
    }
    .banner-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .banner-preview-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #666;
        background: linear-gradient(135deg, #1f1f1f 0%, #0d0d0d 100%);
    }
    .banner-preview-placeholder i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        color: #444;
    }
    .banner-preview-placeholder span {
        color: #666;
        font-size: 0.9rem;
    }
    .banner-upload-zone {
        border: 3px dashed #444;
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: rgba(255,255,255,0.02);
        margin-bottom: 1rem;
    }
    .banner-upload-zone:hover,
    .banner-upload-zone.dragover {
        border-color: #fff;
        background: rgba(255,255,255,0.05);
    }
    .banner-upload-zone i {
        font-size: 2.5rem;
        color: #666;
        margin-bottom: 0.75rem;
    }
    .banner-upload-zone h4 {
        margin: 0 0 0.5rem;
        color: #fff;
    }
    .banner-upload-zone p {
        margin: 0;
        color: #888;
        font-size: 0.85rem;
    }
    .banner-upload-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: #ffffff;
        color: #000;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
    }
    .banner-upload-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(255,255,255,0.2);
    }
    .banner-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .banner-delete-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: transparent;
        color: #dc2626;
        border: 2px solid #dc2626;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
    }
    .banner-delete-btn:hover {
        background: #dc2626;
        color: white;
    }
    .banner-position-select {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: auto;
    }
    .banner-position-select label {
        color: #888;
        font-size: 0.85rem;
    }
    .banner-position-select select {
        padding: 0.5rem 1rem;
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 8px;
        color: #fff;
        font-size: 0.85rem;
        cursor: pointer;
    }
    .banner-position-select select:focus {
        outline: none;
        border-color: #fff;
    }

    /* Mobile-first responsive styles */
    @media (max-width: 768px) {
        .photo-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        .upload-zone {
            padding: 2rem 1rem;
        }
        .upload-zone i {
            font-size: 2rem;
        }
        .photo-item-overlay {
            opacity: 1;
            background: linear-gradient(to top, rgba(0,0,0,0.6), transparent 50%);
        }
    }
    @media (max-width: 480px) {
        .card {
            padding: 1rem;
        }
        .photo-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .photo-item-actions button {
            padding: 0.35rem;
            font-size: 0.75rem;
        }
    }
</style>

<!-- Salon Banner Section -->
<div class="card banner-section">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-panorama"></i> Salon Banner / Cover</h3>
    </div>
    <p class="text-muted" style="margin-bottom:1rem">Je salon banner wordt prominent getoond op zoekresultaten en je bedrijfspagina. Een professionele banner trekt meer klanten aan!</p>

    <?php
    $hasBanner = !empty($business['banner_image']);
    $bannerPosition = $business['banner_position'] ?? 'center';
    ?>

    <!-- Current Banner Preview -->
    <div class="banner-preview">
        <?php if ($hasBanner): ?>
            <img src="<?= htmlspecialchars($business['banner_image']) ?>"
                 alt="<?= htmlspecialchars($business['company_name'] ?? 'Salon') ?> Banner"
                 style="object-position: <?= htmlspecialchars($bannerPosition) ?>">
        <?php else: ?>
            <div class="banner-preview-placeholder">
                <i class="fas fa-image"></i>
                <span>Nog geen banner geüpload</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Upload Zone (drag & drop) -->
    <div class="banner-upload-zone" id="bannerUploadZone" onclick="document.getElementById('bannerFileInput').click()">
        <i class="fas fa-cloud-upload-alt"></i>
        <h4><?= $hasBanner ? 'Nieuwe banner uploaden' : 'Banner uploaden' ?></h4>
        <p>Sleep een afbeelding hierheen of klik om te selecteren</p>
        <p style="color:#666;font-size:0.75rem;margin-top:0.5rem">JPG, PNG of WebP • Max 5MB • Aanbevolen: 1200x400 px</p>
    </div>

    <form method="POST" action="/business/banner/upload" enctype="multipart/form-data" id="bannerUploadForm" style="display:none">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="banner_position" id="bannerPositionInput" value="<?= htmlspecialchars($bannerPosition) ?>">
        <input type="file" name="banner" id="bannerFileInput" accept="image/jpeg,image/png,image/webp">
    </form>

    <div class="banner-actions">
        <?php if ($hasBanner): ?>
            <!-- Position Control -->
            <form method="POST" action="/business/banner/position" class="banner-position-select">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <label for="positionSelect"><i class="fas fa-crop-alt"></i> Positie:</label>
                <select name="banner_position" id="positionSelect" onchange="this.form.submit()">
                    <option value="top" <?= $bannerPosition === 'top' ? 'selected' : '' ?>>Boven</option>
                    <option value="center" <?= $bannerPosition === 'center' ? 'selected' : '' ?>>Midden</option>
                    <option value="bottom" <?= $bannerPosition === 'bottom' ? 'selected' : '' ?>>Onder</option>
                </select>
            </form>

            <!-- Delete Button -->
            <form method="POST" action="/business/banner/delete" style="display:inline" onsubmit="return confirm('Weet je zeker dat je de banner wilt verwijderen?')">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="banner-delete-btn">
                    <i class="fas fa-trash-alt"></i> Verwijderen
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-2">
    <!-- Upload Section -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-upload"></i> Nieuwe Foto Uploaden</h3>
        </div>

        <form method="POST" action="/business/photos" enctype="multipart/form-data" id="uploadForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="upload-zone" id="uploadZone" onclick="document.getElementById('photoInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <h4>Sleep foto's hierheen</h4>
                <p class="text-muted">of klik om te selecteren</p>
                <p class="form-hint">JPG, PNG, WebP of GIF - Max 5MB</p>
            </div>

            <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none" onchange="previewImage(this)">

            <div id="previewContainer" style="display:none;margin-top:1rem">
                <img id="imagePreview" src="" alt="Preview" style="max-width:100%;border-radius:10px">
            </div>

            <div class="form-group" style="margin-top:1rem">
                <label class="form-label">Type Afbeelding</label>
                <select name="image_type" class="form-control">
                    <option value="gallery">Galerij</option>
                    <option value="logo">Logo</option>
                    <option value="cover">Cover/Banner</option>
                    <option value="team">Team</option>
                    <option value="work">Werk/Portfolio</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Onderschrift (optioneel)</label>
                <input type="text" name="caption" class="form-control" placeholder="Beschrijving van de foto">
            </div>

            <div class="form-group">
                <label class="form-label">Alt Tekst (optioneel)</label>
                <input type="text" name="alt_text" class="form-control" placeholder="Beschrijving voor toegankelijkheid">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%">
                <i class="fas fa-upload"></i> Uploaden
            </button>
        </form>
    </div>

    <!-- Tips -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lightbulb"></i> Tips voor goede foto's</h3>
            </div>
            <ul style="padding-left:1.25rem;color:var(--text-light);line-height:1.8">
                <li>Gebruik goed belichte foto's</li>
                <li>Toon je beste werk en je salon</li>
                <li>Upload een professionele logo</li>
                <li>Maak een aantrekkelijke cover foto</li>
                <li>Voeg foto's van je team toe</li>
                <li>Toon voor/na resultaten</li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Afbeelding Types</h3>
            </div>
            <div style="display:flex;flex-direction:column;gap:0.75rem">
                <div style="display:flex;align-items:start;gap:0.75rem">
                    <span style="background:#ffffff;color:#000000;padding:0.25rem 0.5rem;border-radius:5px;font-size:0.7rem;border:1px solid #333333">LOGO</span>
                    <span class="text-muted" style="font-size:0.85rem">Je bedrijfslogo, wordt klein weergegeven</span>
                </div>
                <div style="display:flex;align-items:start;gap:0.75rem">
                    <span style="background:var(--success);color:white;padding:0.25rem 0.5rem;border-radius:5px;font-size:0.7rem">COVER</span>
                    <span class="text-muted" style="font-size:0.85rem">Grote banner bovenaan je pagina</span>
                </div>
                <div style="display:flex;align-items:start;gap:0.75rem">
                    <span style="background:#000000;color:white;padding:0.25rem 0.5rem;border-radius:5px;font-size:0.7rem">GALERIJ</span>
                    <span class="text-muted" style="font-size:0.85rem">Algemene foto's in de galerij</span>
                </div>
                <div style="display:flex;align-items:start;gap:0.75rem">
                    <span style="background:#000000;color:white;padding:0.25rem 0.5rem;border-radius:5px;font-size:0.7rem">WERK</span>
                    <span class="text-muted" style="font-size:0.85rem">Portfolio van je werk</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Gallery -->
<div class="card" style="margin-top:1.5rem">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-images"></i> Mijn Foto's (<?= count($images) ?>)</h3>
    </div>

    <?php if (empty($images)): ?>
        <div class="text-center" style="padding:3rem">
            <i class="fas fa-images" style="font-size:4rem;color:var(--border);margin-bottom:1rem"></i>
            <h4>Nog geen foto's</h4>
            <p class="text-muted">Upload je eerste foto om je pagina aantrekkelijker te maken</p>
        </div>
    <?php else: ?>
        <div class="photo-grid" id="photoGrid">
            <?php foreach ($images as $image): ?>
                <div class="photo-item" data-id="<?= $image['id'] ?>">
                    <?php
                    $typeColors = [
                        'logo' => 'var(--primary)',
                        'cover' => 'var(--success)',
                        'gallery' => '#000000',
                        'team' => '#000000',
                        'work' => '#000000'
                    ];
                    $typeColor = $typeColors[$image['image_type'] ?? 'gallery'] ?? '#000000';
                    ?>
                    <span class="photo-item-type" style="background:<?= $typeColor ?>"><?= htmlspecialchars($image['image_type'] ?? 'gallery') ?></span>
                    <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="<?= htmlspecialchars($image['alt_text'] ?? '') ?>">
                    <div class="photo-item-overlay">
                        <?php if (!empty($image['caption'])): ?>
                            <p style="color:white;font-size:0.85rem;margin-bottom:0.5rem"><?= htmlspecialchars($image['caption']) ?></p>
                        <?php endif; ?>
                        <div class="photo-item-actions">
                            <form method="POST" action="/business/photos/delete" style="flex:1" onsubmit="return confirm('Weet je zeker dat je deze foto wilt verwijderen?')">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                                <button type="submit" class="btn-delete" style="width:100%">
                                    <i class="fas fa-trash"></i> Verwijderen
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // ==========================================
    // BANNER UPLOAD - Drag & Drop
    // ==========================================
    const bannerUploadZone = document.getElementById('bannerUploadZone');
    const bannerFileInput = document.getElementById('bannerFileInput');
    const bannerUploadForm = document.getElementById('bannerUploadForm');

    if (bannerUploadZone && bannerFileInput) {
        // Drag over
        bannerUploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            bannerUploadZone.classList.add('dragover');
        });

        // Drag leave
        bannerUploadZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            bannerUploadZone.classList.remove('dragover');
        });

        // Drop
        bannerUploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            bannerUploadZone.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type.startsWith('image/')) {
                bannerFileInput.files = files;
                submitBannerForm();
            }
        });

        // File input change
        bannerFileInput.addEventListener('change', () => {
            if (bannerFileInput.files.length > 0) {
                submitBannerForm();
            }
        });

        function submitBannerForm() {
            // Show loading state
            bannerUploadZone.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size:2rem;color:#fff;margin-bottom:0.5rem"></i><h4 style="color:#fff">Banner uploaden...</h4>';
            bannerUploadForm.submit();
        }
    }

    // ==========================================
    // PHOTO UPLOAD - Drag & Drop
    // ==========================================
    const uploadZone = document.getElementById('uploadZone');
    const photoInput = document.getElementById('photoInput');

    if (uploadZone && photoInput) {
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                photoInput.files = files;
                previewImage(photoInput);
            }
        });
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('previewContainer').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

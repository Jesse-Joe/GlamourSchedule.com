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
        background: var(--b-bg-surface);
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
        background: var(--b-accent);
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
        border: 3px dashed var(--b-border);
        border-radius: 15px;
        padding: 3rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: var(--b-accent);
        background: rgba(255, 255, 255, 0.05);
    }
    .upload-zone i {
        font-size: 3rem;
        color: var(--b-accent);
        margin-bottom: 1rem;
    }

    /* Salon Banner Section */
    .banner-section {
        margin-bottom: 2rem;
    }
    .banner-preview {
        position: relative;
        width: 100%;
        height: 200px;
        border-radius: 16px;
        overflow: hidden;
        background: var(--b-bg-surface);
        margin-bottom: 1rem;
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
    }
    .banner-preview-placeholder i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        color: #999;
    }
    .banner-upload-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: #000000;
        color: white;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
    }
    .banner-upload-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    }
    .banner-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .banner-delete-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--b-bg-card);
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
        <h3 class="card-title"><i class="fas fa-image"></i> Salon Banner</h3>
    </div>
    <p class="text-muted" style="margin-bottom:1rem">Je salon banner wordt bovenaan je bedrijfspagina getoond. Een goede banner trekt klanten aan!</p>

    <div class="banner-preview">
        <?php
        $currentBanner = null;
        foreach ($images as $img) {
            if (($img['image_type'] ?? '') === 'cover') {
                $currentBanner = $img;
                break;
            }
        }
        ?>
        <?php if ($currentBanner): ?>
            <img src="<?= htmlspecialchars($currentBanner['image_path']) ?>" alt="Salon Banner">
        <?php else: ?>
            <div class="banner-preview-placeholder">
                <i class="fas fa-image"></i>
                <span>Nog geen banner ingesteld</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="banner-actions">
        <form method="POST" action="/business/photos" enctype="multipart/form-data" style="display:inline">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="image_type" value="cover">
            <input type="file" name="photo" id="bannerInput" accept="image/*" style="display:none" onchange="this.form.submit()">
            <button type="button" class="banner-upload-btn" onclick="document.getElementById('bannerInput').click()">
                <i class="fas fa-upload"></i> <?= $currentBanner ? 'Banner Wijzigen' : 'Banner Uploaden' ?>
            </button>
        </form>
        <?php if ($currentBanner): ?>
            <form method="POST" action="/business/photos/delete" style="display:inline" onsubmit="return confirm('Weet je zeker dat je de banner wilt verwijderen?')">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="image_id" value="<?= $currentBanner['id'] ?>">
                <button type="submit" class="banner-delete-btn">
                    <i class="fas fa-trash"></i> Banner Verwijderen
                </button>
            </form>
        <?php endif; ?>
    </div>
    <p class="form-hint" style="margin-top:1rem">Aanbevolen: 1200x400 pixels, JPG of PNG</p>
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
            <ul style="padding-left:1.25rem;color:var(--b-text-muted);line-height:1.8">
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
                    <span style="background:var(--b-accent);color:white;padding:0.25rem 0.5rem;border-radius:5px;font-size:0.7rem">LOGO</span>
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
            <i class="fas fa-images" style="font-size:4rem;color:var(--b-border);margin-bottom:1rem"></i>
            <h4>Nog geen foto's</h4>
            <p class="text-muted">Upload je eerste foto om je pagina aantrekkelijker te maken</p>
        </div>
    <?php else: ?>
        <div class="photo-grid" id="photoGrid">
            <?php foreach ($images as $image): ?>
                <div class="photo-item" data-id="<?= $image['id'] ?>">
                    <?php
                    $typeColors = [
                        'logo' => 'var(--b-accent)',
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
    // Drag and drop
    const uploadZone = document.getElementById('uploadZone');
    const photoInput = document.getElementById('photoInput');

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

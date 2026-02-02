<?php ob_start(); ?>

<style>
    .reviews-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
    }
    @media (max-width: 900px) {
        .reviews-layout {
            grid-template-columns: 1fr;
        }
        .reviews-sidebar {
            order: -1;
        }
    }
    .rating-bars {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .rating-bar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .rating-bar-label {
        width: 30px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .rating-bar-track {
        flex: 1;
        height: 8px;
        background: var(--secondary);
        border-radius: 4px;
        overflow: hidden;
    }
    .rating-bar-fill {
        height: 100%;
        background: #f5c518;
        border-radius: 4px;
    }
    .rating-bar-count {
        width: 30px;
        font-size: 0.85rem;
        color: var(--text-light);
        text-align: right;
    }
    .review-item {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border);
    }
    .review-item:last-child {
        border-bottom: none;
    }
    @media (max-width: 480px) {
        .review-item {
            padding: 1rem;
        }
    }
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
        gap: 0.75rem;
    }
    @media (max-width: 480px) {
        .review-header {
            flex-direction: column;
            gap: 0.5rem;
        }
        .review-meta {
            text-align: left !important;
        }
    }
    .review-author {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .review-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        flex-shrink: 0;
    }
    @media (max-width: 480px) {
        .review-avatar {
            width: 40px;
            height: 40px;
            font-size: 0.9rem;
        }
    }
    .review-stars {
        color: #f5c518;
        font-size: 0.9rem;
    }
    .review-response {
        margin-top: 1rem;
        padding: 1rem;
        background: var(--secondary);
        border-radius: 10px;
        border-left: 3px solid var(--primary);
    }
    .response-form {
        margin-top: 1rem;
        display: none;
    }
    .response-form.active {
        display: block;
    }
    .response-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    @media (max-width: 480px) {
        .response-buttons .btn {
            flex: 1;
            text-align: center;
        }
    }
    .stats-card-big {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .stats-card-big .rating-number {
        font-size: 3rem;
        font-weight: 700;
        color: #f5c518;
    }
    @media (max-width: 480px) {
        .stats-card-big .rating-number {
            font-size: 2.5rem;
        }
    }
</style>

<div class="reviews-layout">
    <!-- Reviews List -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-comments"></i> Alle Reviews (<?= count($reviews) ?>)</h3>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="text-center" style="padding:3rem">
                    <i class="fas fa-star" style="font-size:4rem;color:var(--border);margin-bottom:1rem"></i>
                    <h4>Nog geen reviews</h4>
                    <p class="text-muted">Wanneer klanten reviews achterlaten, verschijnen ze hier.</p>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-author">
                                <div class="review-avatar">
                                    <?= strtoupper(substr($review['first_name'] ?? 'G', 0, 1)) ?>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars(($review['first_name'] ?? 'Gast') . ' ' . substr($review['last_name'] ?? '', 0, 1) . '.') ?></strong>
                                    <div class="review-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= ($review['rating'] ?? 0) ? '' : '-o' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="review-meta" style="text-align:right">
                                <small class="text-muted"><?= !empty($review['created_at']) ? date('d-m-Y', strtotime($review['created_at'])) : '-' ?></small>
                                <?php if (!empty($review['service_name'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($review['service_name']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($review['comment'])): ?>
                            <p style="margin:0.75rem 0;line-height:1.6"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($review['business_response'])): ?>
                            <div class="review-response">
                                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem">
                                    <i class="fas fa-reply" style="color:var(--primary)"></i>
                                    <strong style="font-size:0.85rem">Jouw reactie</strong>
                                    <small class="text-muted"><?= !empty($review['responded_at']) ? date('d-m-Y', strtotime($review['responded_at'])) : '' ?></small>
                                </div>
                                <p style="margin:0;font-size:0.9rem"><?= nl2br(htmlspecialchars($review['business_response'])) ?></p>
                            </div>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-sm" style="margin-top:0.5rem" onclick="showResponseForm(<?= $review['id'] ?>)">
                                <i class="fas fa-reply"></i> Reageren
                            </button>

                            <div class="response-form" id="responseForm<?= $review['id'] ?>">
                                <form method="POST" action="/business/reviews/respond">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                    <div class="form-group" style="margin-bottom:0.75rem">
                                        <textarea name="response" class="form-control" rows="3" placeholder="Schrijf je reactie..." required></textarea>
                                    </div>
                                    <div class="response-buttons">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-paper-plane"></i> Versturen
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="hideResponseForm(<?= $review['id'] ?>)">
                                            Annuleren
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats Sidebar -->
    <div class="reviews-sidebar">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Statistieken</h3>
            </div>

            <div class="stats-card-big">
                <div class="rating-number"><?= number_format($stats['average'] ?? 0, 1) ?></div>
                <div class="review-stars" style="font-size:1.5rem">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star<?= $i <= round($stats['average'] ?? 0) ? '' : '-o' ?>"></i>
                    <?php endfor; ?>
                </div>
                <p class="text-muted" style="margin-top:0.5rem"><?= $stats['total'] ?? 0 ?> reviews</p>
            </div>

            <div class="rating-bars">
                <?php
                $total = max(1, $stats['total'] ?? 1);
                for ($star = 5; $star >= 1; $star--):
                    $count = $stats[strtolower(['', 'one', 'two', 'three', 'four', 'five'][$star]) . '_star'] ?? 0;
                    $percentage = ($count / $total) * 100;
                ?>
                    <div class="rating-bar">
                        <div class="rating-bar-label">
                            <?= $star ?> <i class="fas fa-star" style="color:#f5c518;font-size:0.7rem"></i>
                        </div>
                        <div class="rating-bar-track">
                            <div class="rating-bar-fill" style="width:<?= $percentage ?>%"></div>
                        </div>
                        <div class="rating-bar-count"><?= $count ?></div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lightbulb"></i> Tips</h3>
            </div>
            <ul style="padding-left:1.25rem;color:var(--text-light);line-height:1.8;font-size:0.9rem">
                <li>Reageer snel op reviews om betrokkenheid te tonen</li>
                <li>Bedank klanten voor positieve feedback</li>
                <li>Bij negatieve reviews: blijf professioneel en bied oplossingen</li>
                <li>Vraag tevreden klanten om een review achter te laten</li>
            </ul>
        </div>

        <div class="card" style="background:linear-gradient(135deg,#ffffff,#f0f0f0);border:1px solid #333333">
            <h4 style="margin-bottom:0.5rem;color:#000000"><i class="fas fa-star" style="color:#f5c518"></i> Waarom Reviews Belangrijk Zijn</h4>
            <p style="font-size:0.85rem;color:#333333">Bedrijven met veel positieve reviews krijgen tot 70% meer boekingen!</p>
        </div>
    </div>
</div>

<script>
    function showResponseForm(id) {
        document.getElementById('responseForm' + id).classList.add('active');
    }

    function hideResponseForm(id) {
        document.getElementById('responseForm' + id).classList.remove('active');
    }
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

<?php ob_start(); ?>

<style>
    .review-container {
        max-width: 500px;
        margin: 0 auto;
    }
    .star-rating {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin: 1.5rem 0;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        font-size: 2.5rem;
        color: #d1d5db;
        cursor: pointer;
        transition: all 0.2s;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #f5c518;
    }
    .star-rating:hover label {
        color: #d1d5db;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #f5c518;
    }
    /* Reverse order for proper hover effect */
    .star-rating {
        flex-direction: row-reverse;
        justify-content: center;
    }
    .star-rating input:checked + label,
    .star-rating input:checked + label ~ label {
        color: #f5c518;
    }
    @media (max-width: 480px) {
        .star-rating label {
            font-size: 2rem;
        }
    }
    .booking-summary {
        background: var(--secondary);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .booking-summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }
    .booking-summary-label {
        color: var(--text-light);
    }
    .success-animation {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #333333, #000000);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        animation: pop 0.3s ease-out;
    }
    @keyframes pop {
        0% { transform: scale(0); }
        80% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    .success-animation i {
        font-size: 2rem;
        color: white;
    }
</style>

<div class="container review-container">
    <?php if (isset($_GET['success'])): ?>
        <!-- Success State -->
        <div class="card text-center">
            <div class="success-animation">
                <i class="fas fa-check"></i>
            </div>
            <h2 style="margin:0 0 0.5rem"><?= $translations['review_thanks'] ?? 'Thank you for your review!' ?></h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem">
                <?= $translations['review_helps'] ?? 'Your feedback helps' ?> <?= htmlspecialchars($booking['business_name']) ?> <?= $translations['review_and_others'] ?? 'and other customers.' ?>
            </p>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
                <a href="/business/<?= htmlspecialchars($booking['business_slug']) ?>" class="btn" style="flex:1">
                    <i class="fas fa-store"></i> <?= $translations['view_salon'] ?? 'View salon' ?>
                </a>
                <a href="/search" class="btn btn-secondary" style="flex:1">
                    <i class="fas fa-search"></i> <?= $translations['search_salons'] ?? 'Search salons' ?>
                </a>
            </div>
        </div>

    <?php elseif ($alreadyReviewed): ?>
        <!-- Already Reviewed State -->
        <div class="card text-center">
            <div style="width:80px;height:80px;background:#ffffff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-star" style="font-size:2rem;color:#000000"></i>
            </div>
            <h2 style="margin:0 0 0.5rem"><?= $translations['already_reviewed'] ?? 'You have already left a review' ?></h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem">
                <?= $translations['thanks_previous_feedback'] ?? 'Thank you for your previous feedback for' ?> <?= htmlspecialchars($booking['business_name']) ?>!
            </p>
            <a href="/search" class="btn">
                <i class="fas fa-search"></i> <?= $translations['search_other_salons'] ?? 'Search other salons' ?>
            </a>
        </div>

    <?php elseif (isset($_GET['error'])): ?>
        <!-- Error State -->
        <div class="card text-center">
            <div style="width:80px;height:80px;background:#f5f5f5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-exclamation-triangle" style="font-size:2rem;color:#333333"></i>
            </div>
            <h2 style="margin:0 0 0.5rem"><?= $translations['something_went_wrong'] ?? 'Something went wrong' ?></h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem">
                <?php
                $error = $_GET['error'] ?? '';
                if ($error === 'csrf') {
                    echo $translations['error_csrf'] ?? 'Invalid session. Please try again.';
                } elseif ($error === 'invalid_rating') {
                    echo $translations['review_select_rating'] ?? 'Select a rating from 1 to 5 stars.';
                } elseif ($error === 'already_reviewed') {
                    echo $translations['review_already_given'] ?? 'You have already reviewed this booking.';
                } else {
                    echo $translations['try_again'] ?? 'Please try again.';
                }
                ?>
            </p>
            <a href="/review/<?= htmlspecialchars($booking['uuid']) ?>" class="btn">
                <i class="fas fa-redo"></i> <?= $translations['try_again'] ?? 'Try again' ?>
            </a>
        </div>

    <?php else: ?>
        <!-- Review Form -->
        <div class="card">
            <div class="text-center" style="margin-bottom:1.5rem">
                <div style="font-size:3rem;margin-bottom:0.5rem">
                    <i class="fas fa-star" style="color:#f5c518"></i>
                </div>
                <h2 style="margin:0 0 0.5rem"><?= $translations['review_how_was_visit'] ?? 'How was your visit?' ?></h2>
                <p style="color:var(--text-light);margin:0">
                    <?= $translations['review_share_experience'] ?? 'Share your experience at' ?> <?= htmlspecialchars($booking['business_name']) ?>
                </p>
            </div>

            <div class="booking-summary">
                <div class="booking-summary-row">
                    <span class="booking-summary-label"><?= $translations['service'] ?? 'Service' ?></span>
                    <strong><?= htmlspecialchars($booking['service_name']) ?></strong>
                </div>
                <div class="booking-summary-row">
                    <span class="booking-summary-label"><?= $translations['date'] ?? 'Date' ?></span>
                    <strong><?= date('d-m-Y', strtotime($booking['appointment_date'])) ?></strong>
                </div>
            </div>

            <form method="POST" action="/review/<?= htmlspecialchars($booking['uuid']) ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="form-group">
                    <label style="display:block;text-align:center;font-weight:600;margin-bottom:0.5rem">
                        <?= $translations['rating'] ?? 'Rating' ?>
                    </label>
                    <div class="star-rating">
                        <input type="radio" name="rating" value="5" id="star5" required>
                        <label for="star5"><i class="fas fa-star"></i></label>
                        <input type="radio" name="rating" value="4" id="star4">
                        <label for="star4"><i class="fas fa-star"></i></label>
                        <input type="radio" name="rating" value="3" id="star3">
                        <label for="star3"><i class="fas fa-star"></i></label>
                        <input type="radio" name="rating" value="2" id="star2">
                        <label for="star2"><i class="fas fa-star"></i></label>
                        <input type="radio" name="rating" value="1" id="star1">
                        <label for="star1"><i class="fas fa-star"></i></label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comment" class="form-label">
                        <?= $translations['review_tell_experience'] ?? 'Tell about your experience' ?> <span style="color:var(--text-light);font-weight:400">(<?= $translations['optional'] ?? 'optional' ?>)</span>
                    </label>
                    <textarea
                        name="comment"
                        id="comment"
                        class="form-control"
                        rows="4"
                        placeholder="<?= $translations['review_placeholder'] ?? 'What did you like? What could be better? Your review helps others choose.' ?>"
                        maxlength="1000"
                    ></textarea>
                    <small style="color:var(--text-light);display:block;margin-top:0.5rem">
                        <span id="charCount">0</span>/1000 <?= $translations['characters'] ?? 'characters' ?>
                    </small>
                </div>

                <button type="submit" class="btn" style="width:100%;padding:1rem;font-size:1.1rem">
                    <i class="fas fa-paper-plane"></i> <?= $translations['review_submit'] ?? 'Submit review' ?>
                </button>
            </form>
        </div>

        <p class="text-center text-muted" style="margin-top:1rem;font-size:0.85rem">
            <i class="fas fa-shield-alt"></i> <?= $translations['review_privacy_note'] ?? 'Your review will be posted with your first name and last initial' ?>
        </p>
    <?php endif; ?>
</div>

<script>
// Character counter
const textarea = document.getElementById('comment');
const charCount = document.getElementById('charCount');
if (textarea && charCount) {
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

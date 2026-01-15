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
            <h2 style="margin:0 0 0.5rem">Bedankt voor je review!</h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem">
                Je feedback helpt <?= htmlspecialchars($booking['business_name']) ?> en andere klanten.
            </p>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
                <a href="/business/<?= htmlspecialchars($booking['business_slug']) ?>" class="btn" style="flex:1">
                    <i class="fas fa-store"></i> Bekijk salon
                </a>
                <a href="/search" class="btn btn-secondary" style="flex:1">
                    <i class="fas fa-search"></i> Zoek salons
                </a>
            </div>
        </div>

    <?php elseif ($alreadyReviewed): ?>
        <!-- Already Reviewed State -->
        <div class="card text-center">
            <div style="width:80px;height:80px;background:#ffffff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-star" style="font-size:2rem;color:#000000"></i>
            </div>
            <h2 style="margin:0 0 0.5rem">Je hebt al een review gegeven</h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem">
                Bedankt voor je eerdere feedback voor <?= htmlspecialchars($booking['business_name']) ?>!
            </p>
            <a href="/search" class="btn">
                <i class="fas fa-search"></i> Zoek andere salons
            </a>
        </div>

    <?php elseif (isset($_GET['error'])): ?>
        <!-- Error State -->
        <div class="card text-center">
            <div style="width:80px;height:80px;background:#f5f5f5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-exclamation-triangle" style="font-size:2rem;color:#333333"></i>
            </div>
            <h2 style="margin:0 0 0.5rem">Er ging iets mis</h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem">
                <?php
                $error = $_GET['error'] ?? '';
                if ($error === 'csrf') {
                    echo 'Ongeldige sessie. Probeer het opnieuw.';
                } elseif ($error === 'invalid_rating') {
                    echo 'Selecteer een beoordeling van 1 tot 5 sterren.';
                } elseif ($error === 'already_reviewed') {
                    echo 'Je hebt al een review gegeven voor deze boeking.';
                } else {
                    echo 'Probeer het opnieuw.';
                }
                ?>
            </p>
            <a href="/review/<?= htmlspecialchars($booking['uuid']) ?>" class="btn">
                <i class="fas fa-redo"></i> Probeer opnieuw
            </a>
        </div>

    <?php else: ?>
        <!-- Review Form -->
        <div class="card">
            <div class="text-center" style="margin-bottom:1.5rem">
                <div style="font-size:3rem;margin-bottom:0.5rem">
                    <i class="fas fa-star" style="color:#f5c518"></i>
                </div>
                <h2 style="margin:0 0 0.5rem">Hoe was je bezoek?</h2>
                <p style="color:var(--text-light);margin:0">
                    Deel je ervaring bij <?= htmlspecialchars($booking['business_name']) ?>
                </p>
            </div>

            <div class="booking-summary">
                <div class="booking-summary-row">
                    <span class="booking-summary-label">Dienst</span>
                    <strong><?= htmlspecialchars($booking['service_name']) ?></strong>
                </div>
                <div class="booking-summary-row">
                    <span class="booking-summary-label">Datum</span>
                    <strong><?= date('d-m-Y', strtotime($booking['appointment_date'])) ?></strong>
                </div>
            </div>

            <form method="POST" action="/review/<?= htmlspecialchars($booking['uuid']) ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="form-group">
                    <label style="display:block;text-align:center;font-weight:600;margin-bottom:0.5rem">
                        Beoordeling
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
                        Vertel over je ervaring <span style="color:var(--text-light);font-weight:400">(optioneel)</span>
                    </label>
                    <textarea
                        name="comment"
                        id="comment"
                        class="form-control"
                        rows="4"
                        placeholder="Wat vond je goed? Wat kan beter? Je review helpt anderen bij hun keuze."
                        maxlength="1000"
                    ></textarea>
                    <small style="color:var(--text-light);display:block;margin-top:0.5rem">
                        <span id="charCount">0</span>/1000 tekens
                    </small>
                </div>

                <button type="submit" class="btn" style="width:100%;padding:1rem;font-size:1.1rem">
                    <i class="fas fa-paper-plane"></i> Review versturen
                </button>
            </form>
        </div>

        <p class="text-center text-muted" style="margin-top:1rem;font-size:0.85rem">
            <i class="fas fa-shield-alt"></i> Je review wordt geplaatst met je voornaam en eerste letter van je achternaam
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

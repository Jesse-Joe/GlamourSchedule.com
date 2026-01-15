<?php ob_start(); ?>

<div class="container" style="max-width:600px">
    <div class="card text-center">
        <div style="width:100px;height:100px;background:var(--danger);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
            <i class="fas fa-times" style="font-size:3rem;color:white"></i>
        </div>
        <h1 style="color:var(--danger)">Betaling mislukt</h1>
        <p style="color:var(--text-light);font-size:1.1rem;margin-bottom:1rem">
            <?php if ($status === 'canceled'): ?>
                Je hebt de betaling geannuleerd.
            <?php elseif ($status === 'expired'): ?>
                De betaling is verlopen. Probeer het opnieuw.
            <?php else: ?>
                Er is iets misgegaan met de betaling.
            <?php endif; ?>
        </p>
        <p style="color:var(--text-light);margin-bottom:2rem">
            Je boeking is nog niet bevestigd. Je kunt het opnieuw proberen.
        </p>
    </div>

    <div class="card">
        <h3><i class="fas fa-info-circle"></i> Boeking <?= htmlspecialchars($booking['booking_number']) ?></h3>
        <p style="margin-top:1rem;color:var(--text-light)">
            <strong><?= htmlspecialchars($booking['service_name']) ?></strong> bij <?= htmlspecialchars($booking['business_name']) ?><br>
            <?= date('d-m-Y', strtotime($booking['appointment_date'])) ?> om <?= date('H:i', strtotime($booking['appointment_time'])) ?>
        </p>
        <p style="font-size:1.3rem;font-weight:700;color:var(--primary);margin-top:1rem">
            &euro;<?= number_format($booking['total_price'], 2, ',', '.') ?>
        </p>
    </div>

    <div style="text-align:center;margin-top:2rem">
        <a href="/payment/create/<?= $booking['uuid'] ?>" class="btn" style="margin-right:1rem">
            <i class="fas fa-redo"></i> Opnieuw proberen
        </a>
        <a href="/booking/<?= $booking['uuid'] ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Terug naar boeking
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

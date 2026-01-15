<?php ob_start(); ?>

<div class="container" style="max-width:600px">
    <div class="card text-center">
        <div style="width:100px;height:100px;background:var(--warning);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
            <i class="fas fa-clock" style="font-size:3rem;color:white"></i>
        </div>
        <h1 style="color:var(--warning)">Betaling in behandeling</h1>
        <p style="color:var(--text-light);font-size:1.1rem;margin-bottom:2rem">
            We wachten op bevestiging van je betaling. Dit kan enkele minuten duren.
        </p>
    </div>

    <div class="card">
        <h3><i class="fas fa-info-circle"></i> Boeking <?= htmlspecialchars($booking['booking_number']) ?></h3>
        <p style="margin-top:1rem;color:var(--text-light)">
            <strong><?= htmlspecialchars($booking['service_name']) ?></strong> bij <?= htmlspecialchars($booking['business_name']) ?><br>
            <?= date('d-m-Y', strtotime($booking['appointment_date'])) ?> om <?= date('H:i', strtotime($booking['appointment_time'])) ?>
        </p>
    </div>

    <div style="text-align:center;margin-top:2rem">
        <a href="/booking/<?= $booking['uuid'] ?>" class="btn">
            <i class="fas fa-eye"></i> Bekijk boeking status
        </a>
    </div>

    <p style="text-align:center;color:var(--text-light);margin-top:2rem;font-size:0.9rem">
        <i class="fas fa-info-circle"></i> Je ontvangt een e-mail zodra de betaling is bevestigd.
    </p>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

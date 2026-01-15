<?php ob_start(); ?>

<div class="container" style="max-width:600px">
    <div class="card text-center">
        <div style="width:100px;height:100px;background:var(--success);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
            <i class="fas fa-check" style="font-size:3rem;color:white"></i>
        </div>
        <h1 style="color:var(--success)"><?= $__('payment_success_title') ?></h1>
        <p style="color:var(--text-light);font-size:1.1rem;margin-bottom:2rem">
            <?= $__('payment_success_msg') ?>
        </p>
    </div>

    <div class="card">
        <h3><i class="fas fa-calendar-check"></i> <?= $__('booking_details') ?></h3>

        <table style="width:100%;margin-top:1rem">
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('booking_number') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:700"><?= htmlspecialchars($booking['booking_number']) ?></td>
            </tr>
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('salon') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:500"><?= htmlspecialchars($booking['business_name']) ?></td>
            </tr>
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('service') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:500"><?= htmlspecialchars($booking['service_name']) ?></td>
            </tr>
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('date_time') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:500">
                    <?= date('d-m-Y', strtotime($booking['appointment_date'])) ?> <?= $__('at') ?> <?= date('H:i', strtotime($booking['appointment_time'])) ?>
                </td>
            </tr>
            <tr>
                <td style="padding:0.75rem 0;color:var(--text-light)"><?= $__('paid') ?></td>
                <td style="padding:0.75rem 0;text-align:right;font-weight:700;color:var(--success);font-size:1.2rem">&euro;<?= number_format($booking['total_price'], 2, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <div style="text-align:center;margin-top:2rem">
        <a href="/booking/<?= $booking['uuid'] ?>" class="btn" style="margin-right:1rem">
            <i class="fas fa-eye"></i> <?= $__('view_booking_btn') ?>
        </a>
        <a href="/" class="btn btn-secondary">
            <i class="fas fa-home"></i> <?= $__('to_home') ?>
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

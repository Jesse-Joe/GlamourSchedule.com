<?php ob_start(); ?>

<style>
/* Override for payment pages - dark theme */
body, main {
    background: #000 !important;
}
.payment-page {
    min-height: 100vh;
    background: #000;
    color: #fff;
    padding: 2rem 1rem;
}
.payment-page .card {
    background: #111;
    border: 1px solid #222;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.payment-page h1 {
    color: #ef4444;
}
.payment-page h3 {
    color: #fff;
    margin-bottom: 1rem;
}
.payment-page .text-light {
    color: rgba(255,255,255,0.6) !important;
}
.payment-page table td {
    color: #fff;
}
.payment-page .btn {
    background: #fff;
    color: #000;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}
.payment-page .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255,255,255,0.2);
}
.payment-page .btn-secondary {
    background: transparent;
    color: #fff;
    border: 1px solid #333;
}
.payment-page .btn-secondary:hover {
    background: rgba(255,255,255,0.1);
    border-color: #555;
}
.failed-icon {
    width: 100px;
    height: 100px;
    background: #ef4444;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}
.failed-icon i {
    font-size: 3rem;
    color: #fff;
}

/* Mobile responsive */
@media (max-width: 480px) {
    .payment-page {
        padding: 1rem 0.75rem;
    }
    .payment-page .card {
        padding: 1.25rem 1rem;
        border-radius: 12px;
    }
    .payment-page h1 {
        font-size: 1.5rem;
    }
    .payment-page h3 {
        font-size: 1.1rem;
    }
    .payment-page table {
        font-size: 0.9rem;
    }
    .payment-page table td {
        padding: 0.6rem 0 !important;
        display: block;
        text-align: left !important;
        border-bottom: none !important;
    }
    .payment-page table tr {
        display: block;
        border-bottom: 1px solid #333;
        padding: 0.5rem 0;
    }
    .payment-page table tr:last-child {
        border-bottom: none;
    }
    .payment-page table td:first-child {
        font-size: 0.8rem;
        padding-bottom: 0.25rem !important;
    }
    .payment-page table td:last-child {
        font-weight: 600 !important;
    }
    .failed-icon {
        width: 80px;
        height: 80px;
    }
    .failed-icon i {
        font-size: 2.5rem;
    }
    .payment-page .btn {
        width: 100%;
        justify-content: center;
        margin-bottom: 0.75rem;
        margin-right: 0 !important;
    }
    .payment-page .buttons-container {
        display: flex;
        flex-direction: column;
    }
}
</style>

<div class="payment-page">
    <div class="container" style="max-width:600px">
        <div class="card text-center">
            <div class="failed-icon">
                <i class="fas fa-times"></i>
            </div>
            <h1><?= $__('payment_failed_title') ?></h1>
            <p class="text-light" style="font-size:1.1rem;margin-bottom:1rem">
                <?php if ($status === 'canceled'): ?>
                    <?= $__('payment_failed_canceled') ?>
                <?php elseif ($status === 'expired'): ?>
                    <?= $__('payment_failed_expired') ?>
                <?php else: ?>
                    <?= $__('payment_failed_generic') ?>
                <?php endif; ?>
            </p>
            <p class="text-light" style="margin-bottom:2rem">
                <?= $__('booking_not_confirmed') ?>
            </p>
        </div>

        <div class="card">
            <h3><i class="fas fa-info-circle"></i> <?= $__('booking_details') ?></h3>

            <table style="width:100%;margin-top:1rem">
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;color:rgba(255,255,255,0.6)"><?= $__('booking_number') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;text-align:right;font-weight:700;color:#fff"><?= htmlspecialchars($booking['booking_number']) ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;color:rgba(255,255,255,0.6)"><?= $__('customer_name') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;text-align:right;font-weight:500;color:#fff"><?= htmlspecialchars($booking['guest_name'] ?? $booking['customer_name'] ?? '') ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;color:rgba(255,255,255,0.6)"><?= $__('email') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;text-align:right;font-weight:500;color:#fff"><?= htmlspecialchars($booking['guest_email'] ?? $booking['customer_email'] ?? '') ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;color:rgba(255,255,255,0.6)"><?= $__('salon') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;text-align:right;font-weight:500;color:#fff"><?= htmlspecialchars($booking['business_name']) ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;color:rgba(255,255,255,0.6)"><?= $__('service') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;text-align:right;font-weight:500;color:#fff"><?= htmlspecialchars($booking['service_name']) ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;color:rgba(255,255,255,0.6)"><?= $__('date_time') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;text-align:right;font-weight:500;color:#fff">
                        <?= !empty($booking['appointment_date']) ? date('d-m-Y', strtotime($booking['appointment_date'])) : '-' ?> <?= $__('at') ?> <?= !empty($booking['appointment_time']) ? date('H:i', strtotime($booking['appointment_time'])) : '-' ?>
                    </td>
                </tr>
                <?php if (!empty($booking['duration'])): ?>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;color:rgba(255,255,255,0.6)"><?= $__('duration') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid #333;text-align:right;font-weight:500;color:#fff"><?= htmlspecialchars($booking['duration']) ?> <?= $__('minutes') ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding:0.75rem 0;color:rgba(255,255,255,0.6)"><?= $__('total_amount') ?></td>
                    <td style="padding:0.75rem 0;text-align:right;font-weight:700;color:#ef4444;font-size:1.2rem">&euro;<?= number_format($booking['total_price'], 2, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <div class="buttons-container" style="text-align:center;margin-top:2rem">
            <a href="/payment/create/<?= $booking['uuid'] ?>" class="btn" style="margin-right:1rem">
                <i class="fas fa-redo"></i> <?= $__('try_again_btn') ?>
            </a>
            <a href="/booking/<?= $booking['uuid'] ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> <?= $__('back_to_booking') ?>
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

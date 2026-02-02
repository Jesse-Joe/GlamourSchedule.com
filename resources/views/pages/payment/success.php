<?php ob_start(); ?>

<style>
/* Payment page - follows theme */
.payment-page {
    min-height: 100vh;
    background: var(--theme-bg, #000000);
    color: var(--theme-text, #ffffff);
    padding: 2rem 1rem;
}
.payment-page .card {
    background: var(--card-bg, #0a0a0a);
    border: 1px solid var(--card-border, #333);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.payment-page h1 {
    color: var(--success, #22c55e);
}
.payment-page h3 {
    color: var(--theme-text, #ffffff);
    margin-bottom: 1rem;
}
.payment-page .text-light {
    color: var(--theme-text-muted, #999) !important;
}
.payment-page table td {
    color: var(--theme-text, #ffffff);
}
.payment-page .btn {
    background: #ffffff;
    color: #000000;
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
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.payment-page .btn-secondary {
    background: transparent;
    color: var(--theme-text, #ffffff);
    border: 1px solid var(--card-border, #333);
}
.payment-page .btn-secondary:hover {
    background: var(--card-bg, #1a1a1a);
    border-color: var(--card-border, #333);
}
.success-icon {
    width: 100px;
    height: 100px;
    background: #22c55e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}
.success-icon i {
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
    .success-icon {
        width: 80px;
        height: 80px;
    }
    .success-icon i {
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
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1><?= $__('payment_success_title') ?></h1>
            <p class="text-light" style="font-size:1.1rem;margin-bottom:2rem">
                <?= $__('payment_success_msg') ?>
            </p>
        </div>

        <div class="card">
            <h3><i class="fas fa-calendar-check"></i> <?= $__('booking_details') ?></h3>

            <table style="width:100%;margin-top:1rem">
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);color:var(--theme-text-muted, #999)"><?= $__('booking_number') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);text-align:right;font-weight:700;color:var(--theme-text, #fff)"><?= htmlspecialchars($booking['booking_number']) ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);color:var(--theme-text-muted, #999)"><?= $__('customer_name') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);text-align:right;font-weight:500;color:var(--theme-text, #fff)"><?= htmlspecialchars($booking['customer_name'] ?? $booking['guest_name'] ?? '') ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);color:var(--theme-text-muted, #999)"><?= $__('email') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);text-align:right;font-weight:500;color:var(--theme-text, #fff)"><?= htmlspecialchars($booking['customer_email'] ?? $booking['guest_email'] ?? '') ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);color:var(--theme-text-muted, #999)"><?= $__('salon') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);text-align:right;font-weight:500;color:var(--theme-text, #fff)"><?= htmlspecialchars($booking['business_name']) ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);color:var(--theme-text-muted, #999)"><?= $__('service') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);text-align:right;font-weight:500;color:var(--theme-text, #fff)"><?= htmlspecialchars($booking['service_name']) ?></td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);color:var(--theme-text-muted, #999)"><?= $__('date_time') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);text-align:right;font-weight:500;color:var(--theme-text, #fff)">
                        <?= !empty($booking['appointment_date']) ? date('d-m-Y', strtotime($booking['appointment_date'])) : '-' ?> <?= $__('at') ?> <?= !empty($booking['appointment_time']) ? date('H:i', strtotime($booking['appointment_time'])) : '-' ?>
                    </td>
                </tr>
                <?php if (!empty($booking['duration'])): ?>
                <tr>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);color:var(--theme-text-muted, #999)"><?= $__('duration') ?></td>
                    <td style="padding:0.75rem 0;border-bottom:1px solid var(--card-border, #333);text-align:right;font-weight:500;color:var(--theme-text, #fff)"><?= htmlspecialchars($booking['duration']) ?> <?= $__('minutes') ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding:0.75rem 0;color:var(--theme-text-muted, #999)"><?= $__('paid') ?></td>
                    <td style="padding:0.75rem 0;text-align:right;font-weight:700;color:var(--success, #22c55e);font-size:1.2rem">&euro;<?= number_format($booking['total_price'], 2, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <div class="buttons-container" style="text-align:center;margin-top:2rem">
            <a href="/booking/<?= $booking['uuid'] ?>" class="btn" style="margin-right:1rem">
                <i class="fas fa-eye"></i> <?= $__('view_booking_btn') ?>
            </a>
            <a href="/" class="btn btn-secondary">
                <i class="fas fa-home"></i> <?= $__('to_home') ?>
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

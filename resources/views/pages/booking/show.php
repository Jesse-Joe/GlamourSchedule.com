<?php ob_start(); ?>

<div class="container" style="max-width:600px">
    <div class="card text-center">
        <?php if ($booking['status'] === 'cancelled'): ?>
            <div style="width:80px;height:80px;background:var(--danger);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-times" style="font-size:2rem;color:white"></i>
            </div>
            <h2>Boeking Geannuleerd</h2>
            <?php if ($booking['payment_status'] === 'paid' || $booking['payment_status'] === 'refunded'): ?>
                <div style="background:#ffffff;border:2px solid #333333;border-radius:12px;padding:1rem;margin:1rem 0;text-align:center">
                    <p style="margin:0;color:#000000;font-weight:600">
                        <i class="fas fa-undo"></i> Terugbetaling in behandeling
                    </p>
                    <p style="margin:0.5rem 0 0;color:#000000;font-size:0.9rem">
                        Binnen 72 uur wordt het bedrag teruggestort op uw rekening
                    </p>
                </div>
            <?php endif; ?>
        <?php elseif ($booking['status'] === 'pending' && $booking['payment_status'] !== 'paid'): ?>
            <div style="width:80px;height:80px;background:var(--warning);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-credit-card" style="font-size:2rem;color:white"></i>
            </div>
            <h2><?= $__('payment_required') ?></h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem"><?= $__('complete_payment_to_confirm') ?></p>
            <a href="/payment/create/<?= $booking['uuid'] ?>" class="btn" style="font-size:1.1rem;padding:1rem 2rem">
                <i class="fas fa-lock"></i> <?= $__('pay_now') ?> - &euro;<?= number_format($booking['total_price'], 2, ',', '.') ?>
            </a>
        <?php elseif ($booking['status'] === 'confirmed' || $booking['payment_status'] === 'paid'): ?>
            <div style="width:80px;height:80px;background:var(--success);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-check" style="font-size:2rem;color:white"></i>
            </div>
            <h2><?= $__('booking_confirmed') ?></h2>
        <?php endif; ?>

        <p style="color:var(--text-light);margin-bottom:2rem"><?= $__('booking_number') ?>: <?= htmlspecialchars($booking['booking_number']) ?></p>
    </div>

    <div class="card">
        <h3><i class="fas fa-info-circle"></i> <?= $__('booking_details') ?></h3>

        <table style="width:100%;margin-top:1rem">
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('salon') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:500"><?= htmlspecialchars($booking['business_name']) ?></td>
            </tr>
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('service') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:500"><?= htmlspecialchars($booking['service_name']) ?></td>
            </tr>
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('date') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:500"><?= date('d-m-Y', strtotime($booking['appointment_date'])) ?></td>
            </tr>
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('time') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:500"><?= date('H:i', strtotime($booking['appointment_time'])) ?></td>
            </tr>
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('duration') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:500"><?= $booking['duration_minutes'] ?> <?= $__('minutes') ?></td>
            </tr>
            <tr>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);color:var(--text-light)"><?= $__('total') ?></td>
                <td style="padding:0.75rem 0;border-bottom:1px solid var(--border);text-align:right;font-weight:700;color:var(--primary);font-size:1.2rem">&euro;<?= number_format($booking['total_price'], 2, ',', '.') ?></td>
            </tr>
        </table>

        <div style="margin-top:1.5rem;padding:1rem;background:var(--secondary);border-radius:10px">
            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($booking['address']) ?>, <?= htmlspecialchars($booking['city']) ?></p>
            <?php if (!empty($booking['business_phone'])): ?>
                <p style="margin-top:0.5rem"><i class="fas fa-phone"></i> <?= htmlspecialchars($booking['business_phone']) ?></p>
            <?php endif; ?>
        </div>

        <?php if (($booking['status'] === 'confirmed' || $booking['payment_status'] === 'paid') && $booking['status'] !== 'cancelled'): ?>
        <!-- 24-hour Policy Box -->
        <div style="margin-top:1.5rem;padding:1.25rem;background:linear-gradient(135deg,#fef3c7,#fde68a);border:2px solid #f59e0b;border-radius:12px">
            <h4 style="margin:0 0 0.75rem;color:#92400e;display:flex;align-items:center;gap:0.5rem;font-size:1rem">
                <i class="fas fa-exclamation-triangle"></i> 24-uurs annuleringsbeleid
            </h4>
            <p style="margin:0;color:#92400e;font-size:0.9rem;line-height:1.5">
                Je kunt gratis annuleren tot 24 uur voor de afspraak. Bij annulering binnen 24 uur wordt 50% van het bedrag in rekening gebracht. De overige 50% wordt teruggestort.
            </p>
        </div>

        <!-- Confirmation Email Notice -->
        <div style="margin-top:1rem;padding:1rem;background:linear-gradient(135deg,#d1fae5,#a7f3d0);border:2px solid #10b981;border-radius:12px">
            <p style="margin:0;color:#065f46;font-size:0.9rem;display:flex;align-items:center;gap:0.5rem">
                <i class="fas fa-envelope-circle-check"></i>
                <span>Er is een bevestigingsmail verstuurd naar <strong><?= htmlspecialchars($booking['guest_email'] ?? $booking['user_email'] ?? 'je e-mailadres') ?></strong></span>
            </p>
        </div>

        <!-- Reminder Notice -->
        <div style="margin-top:1rem;padding:1rem;background:var(--secondary);border-radius:12px">
            <p style="margin:0;color:var(--text-light);font-size:0.85rem;display:flex;align-items:center;gap:0.5rem">
                <i class="fas fa-bell"></i>
                <span>Je ontvangt een herinnering 24 uur en 1 uur voor je afspraak</span>
            </p>
        </div>
        <?php endif; ?>

        <?php if (($booking['status'] === 'confirmed' || $booking['payment_status'] === 'paid') && $booking['status'] !== 'checked_in'): ?>
            <!-- Check-in QR Code -->
            <div style="margin-top:1.5rem;padding:1.5rem;background:linear-gradient(135deg,#ffffff,#f5f5f5);border:2px dashed #333333;border-radius:12px;text-align:center">
                <h4 style="margin:0 0 1rem 0;color:#000000"><i class="fas fa-qrcode"></i> Check-in Code</h4>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= urlencode('https://glamourschedule.nl/checkin/' . $booking['uuid']) ?>"
                     alt="Check-in QR" style="border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.1)">
                <div style="margin-top:1rem;padding:0.75rem 1.5rem;background:#ffffff;border-radius:8px;display:inline-block">
                    <p style="margin:0;color:#6b7280;font-size:0.8rem">Of noem dit nummer:</p>
                    <p style="margin:0.25rem 0 0 0;font-size:1.5rem;font-weight:700;color:#000000;letter-spacing:2px"><?= htmlspecialchars($booking['booking_number']) ?></p>
                </div>
                <p style="margin:1rem 0 0 0;color:#000000;font-size:0.9rem">
                    <i class="fas fa-info-circle"></i> Toon deze code bij aankomst aan de salon
                </p>
            </div>
        <?php elseif ($booking['status'] === 'checked_in'): ?>
            <!-- Already checked in -->
            <div style="margin-top:1.5rem;padding:1.5rem;background:linear-gradient(135deg,#ffffff,#f5f5f5);border-radius:12px;text-align:center">
                <div style="width:60px;height:60px;background:#333333;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                    <i class="fas fa-check" style="font-size:1.5rem;color:white"></i>
                </div>
                <h4 style="margin:0;color:#000000">Ingecheckt!</h4>
                <p style="margin:0.5rem 0 0 0;color:#000000;font-size:0.9rem">Je aanwezigheid is bevestigd</p>
            </div>
        <?php endif; ?>

        <?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'checked_in'): ?>
            <?php
            // Calculate if within 24 hours of appointment
            $appointmentDateTime = new DateTime($booking['appointment_date'] . ' ' . $booking['appointment_time']);
            $now = new DateTime();
            $hoursUntilAppointment = ($appointmentDateTime->getTimestamp() - $now->getTimestamp()) / 3600;
            $isWithin24Hours = $hoursUntilAppointment <= 24 && $hoursUntilAppointment > 0;
            $isPastAppointment = $hoursUntilAppointment <= 0;
            $halfPrice = number_format($booking['total_price'] / 2, 2, ',', '.');
            ?>
            <div style="margin-top:1.5rem;display:flex;gap:1rem;flex-wrap:wrap">
                <a href="/search" class="btn btn-secondary" style="flex:1;text-align:center;min-width:140px">
                    <i class="fas fa-search"></i> <?= $__('new_booking') ?>
                </a>
                <?php if (!$isPastAppointment): ?>
                    <button type="button" class="btn btn-danger" style="flex:1;min-width:140px" onclick="showCancelModal()">
                        <i class="fas fa-times"></i> <?= $__('cancel_booking') ?>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Cancel Modal -->
            <?php if (!$isPastAppointment): ?>
            <div id="cancelModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.6);z-index:1000;padding:1rem;overflow-y:auto;align-items:center;justify-content:center">
                <div style="max-width:400px;width:100%;background:var(--bg-card);border-radius:16px;overflow:hidden">
                    <div style="background:linear-gradient(135deg,#333333,#dc2626);padding:1.5rem;text-align:center;color:white">
                        <div style="font-size:2.5rem;margin-bottom:0.5rem"><i class="fas fa-exclamation-triangle"></i></div>
                        <h3 style="margin:0">Afspraak annuleren</h3>
                    </div>
                    <div style="padding:1.5rem">
                        <?php if ($isWithin24Hours): ?>
                            <div style="background:#f5f5f5;border:2px solid #333333;border-radius:12px;padding:1rem;margin-bottom:1.5rem;text-align:center">
                                <p style="margin:0;color:#000000;font-weight:600;font-size:0.95rem">
                                    <i class="fas fa-clock"></i> Je annuleert binnen 24 uur voor de afspraak
                                </p>
                                <p style="margin:0.75rem 0 0;color:#dc2626;font-size:1.25rem;font-weight:700">
                                    50% van het bedrag (&euro;<?= $halfPrice ?>) gaat naar het bedrijf
                                </p>
                                <p style="margin:0.5rem 0 0;color:#7f1d1d;font-size:0.85rem">
                                    Je ontvangt &euro;<?= $halfPrice ?> terug
                                </p>
                            </div>
                        <?php else: ?>
                            <div style="background:#ffffff;border:2px solid #333333;border-radius:12px;padding:1rem;margin-bottom:1.5rem;text-align:center">
                                <p style="margin:0;color:#000000;font-weight:600">
                                    <i class="fas fa-check-circle"></i> Gratis annuleren
                                </p>
                                <p style="margin:0.5rem 0 0;color:#000000;font-size:0.9rem">
                                    Je ontvangt het volledige bedrag (&euro;<?= number_format($booking['total_price'], 2, ',', '.') ?>) terug
                                </p>
                            </div>
                        <?php endif; ?>

                        <p style="text-align:center;margin-bottom:1.5rem;color:var(--text-light)">
                            Weet je zeker dat je deze afspraak wilt annuleren?
                        </p>

                        <div style="display:flex;gap:0.75rem">
                            <button type="button" class="btn btn-secondary" style="flex:1" onclick="hideCancelModal()">
                                Terug
                            </button>
                            <button type="button" class="btn btn-danger" style="flex:1" onclick="showFinalConfirm()">
                                Ja, annuleer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Final Confirmation Modal -->
            <div id="finalConfirmModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.6);z-index:1001;padding:1rem;overflow-y:auto;align-items:center;justify-content:center">
                <div style="max-width:400px;width:100%;background:var(--bg-card);border-radius:16px;overflow:hidden">
                    <div style="padding:2rem;text-align:center">
                        <div style="font-size:3rem;margin-bottom:1rem"><i class="fas fa-question-circle" style="color:#000000"></i></div>
                        <h3 style="margin:0 0 1rem">Laatste bevestiging</h3>
                        <?php if ($isWithin24Hours): ?>
                            <p style="color:#dc2626;font-weight:600;margin-bottom:1.5rem">
                                Door te annuleren gaat &euro;<?= $halfPrice ?> naar <?= htmlspecialchars($booking['business_name']) ?>
                            </p>
                        <?php else: ?>
                            <p style="color:var(--text-light);margin-bottom:1.5rem">
                                Je afspraak bij <?= htmlspecialchars($booking['business_name']) ?> wordt geannuleerd
                            </p>
                        <?php endif; ?>

                        <form method="POST" action="/booking/<?= $booking['uuid'] ?>/cancel">
                            <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                            <input type="hidden" name="confirm_cancel" value="1">
                            <?php if ($isWithin24Hours): ?>
                                <input type="hidden" name="late_cancel" value="1">
                            <?php endif; ?>
                            <div style="display:flex;gap:0.75rem">
                                <button type="button" class="btn btn-secondary" style="flex:1" onclick="hideFinalConfirm()">
                                    Nee, behouden
                                </button>
                                <button type="submit" class="btn btn-danger" style="flex:1">
                                    Ja, definitief annuleren
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
            function showCancelModal() {
                document.getElementById('cancelModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            function hideCancelModal() {
                document.getElementById('cancelModal').style.display = 'none';
                document.body.style.overflow = '';
            }
            function showFinalConfirm() {
                document.getElementById('cancelModal').style.display = 'none';
                document.getElementById('finalConfirmModal').style.display = 'flex';
            }
            function hideFinalConfirm() {
                document.getElementById('finalConfirmModal').style.display = 'none';
                document.getElementById('cancelModal').style.display = 'flex';
            }
            // Close modal on outside click
            document.getElementById('cancelModal').addEventListener('click', function(e) {
                if (e.target === this) hideCancelModal();
            });
            document.getElementById('finalConfirmModal').addEventListener('click', function(e) {
                if (e.target === this) hideFinalConfirm();
            });
            </script>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

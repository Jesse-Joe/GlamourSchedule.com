<?php ob_start(); ?>

<div class="container" style="max-width:500px">
    <?php
    $success = isset($_GET['success']);
    $already = isset($_GET['already']);
    $error = $_GET['error'] ?? null;
    $customerName = $booking['guest_name'] ?? trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '')) ?: 'Klant';
    ?>

    <?php if ($success || $already || $booking['status'] === 'checked_in'): ?>
        <!-- Success State -->
        <div class="card text-center">
            <div style="width:100px;height:100px;background:linear-gradient(135deg,#333333,#000000);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 10px 30px rgba(16,185,129,0.3)">
                <i class="fas fa-check" style="font-size:3rem;color:white"></i>
            </div>
            <h2 style="color:#000000;margin-bottom:0.5rem">Ingecheckt!</h2>
            <p style="color:#000000;font-size:1.1rem;margin-bottom:2rem">
                <?= htmlspecialchars($customerName) ?> is succesvol ingecheckt
            </p>

            <div style="background:#ffffff;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem">
                <table style="width:100%;text-align:left">
                    <tr>
                        <td style="padding:0.5rem 0;color:#6b7280">Boeking</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:600">#<?= htmlspecialchars($booking['booking_number']) ?></td>
                    </tr>
                    <?php if (!empty($booking['verification_code'])): ?>
                    <tr>
                        <td style="padding:0.5rem 0;color:#6b7280"><i class="fas fa-shield-alt" style="color:#f59e0b"></i> Verificatie</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:700;font-family:monospace;letter-spacing:1px;color:#000"><?= htmlspecialchars($booking['verification_code']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="padding:0.5rem 0;color:#6b7280">Dienst</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:600"><?= htmlspecialchars($booking['service_name']) ?></td>
                    </tr>
                    <tr>
                        <td style="padding:0.5rem 0;color:#6b7280">Tijd</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:600"><?= date('H:i', strtotime($booking['appointment_time'])) ?></td>
                    </tr>
                    <tr>
                        <td style="padding:0.5rem 0;color:#6b7280">Duur</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:600"><?= $booking['duration_minutes'] ?> min</td>
                    </tr>
                </table>
            </div>

            <p style="color:#333333;font-size:0.9rem">
                <i class="fas fa-euro-sign"></i> Uitbetaling vrijgegeven
            </p>

            <a href="/business/bookings" class="btn" style="margin-top:1rem;width:100%">
                <i class="fas fa-arrow-left"></i> Terug naar boekingen
            </a>
        </div>

    <?php elseif ($error === 'unauthorized'): ?>
        <!-- Not authorized -->
        <div class="card text-center">
            <div style="width:80px;height:80px;background:#ffffff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-exclamation-triangle" style="font-size:2rem;color:#000000"></i>
            </div>
            <h2>Niet geautoriseerd</h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem">
                Log in als bedrijf om klanten in te checken.
            </p>
            <a href="/business/login" class="btn" style="width:100%">
                <i class="fas fa-sign-in-alt"></i> Inloggen als bedrijf
            </a>
        </div>

    <?php elseif ($error === 'not_paid'): ?>
        <!-- Not paid -->
        <div class="card text-center">
            <div style="width:80px;height:80px;background:#f5f5f5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-credit-card" style="font-size:2rem;color:#333333"></i>
            </div>
            <h2>Niet betaald</h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem">
                Deze boeking is nog niet betaald en kan niet worden ingecheckt.
            </p>
            <a href="/business/bookings" class="btn btn-secondary" style="width:100%">
                <i class="fas fa-arrow-left"></i> Terug naar boekingen
            </a>
        </div>

    <?php elseif ($isBusinessOwner): ?>
        <!-- Check-in Form for Business Owner -->
        <div class="card">
            <div style="text-align:center;margin-bottom:2rem">
                <div style="width:80px;height:80px;background:linear-gradient(135deg,#404040,#1d4ed8);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 8px 25px rgba(59,130,246,0.3)">
                    <i class="fas fa-qrcode" style="font-size:2rem;color:white"></i>
                </div>
                <h2 style="margin:0">Klant Check-in</h2>
                <p style="color:var(--text-light);margin:0.5rem 0 0 0">Bevestig de aanwezigheid</p>
            </div>

            <div style="background:var(--secondary);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem">
                <h3 style="margin:0 0 1rem 0;font-size:1rem;color:var(--text-light)">Boekingsdetails</h3>
                <table style="width:100%">
                    <tr>
                        <td style="padding:0.5rem 0;color:var(--text-light)">Klant</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:600"><?= htmlspecialchars($customerName) ?></td>
                    </tr>
                    <tr>
                        <td style="padding:0.5rem 0;color:var(--text-light)">Dienst</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:600"><?= htmlspecialchars($booking['service_name']) ?></td>
                    </tr>
                    <tr>
                        <td style="padding:0.5rem 0;color:var(--text-light)">Datum</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:600"><?= date('d-m-Y', strtotime($booking['appointment_date'])) ?></td>
                    </tr>
                    <tr>
                        <td style="padding:0.5rem 0;color:var(--text-light)">Tijd</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:600"><?= date('H:i', strtotime($booking['appointment_time'])) ?></td>
                    </tr>
                    <tr>
                        <td style="padding:0.5rem 0;color:var(--text-light)">Bedrag</td>
                        <td style="padding:0.5rem 0;text-align:right;font-weight:700;color:var(--primary);font-size:1.1rem">&euro;<?= number_format($booking['total_price'], 2, ',', '.') ?></td>
                    </tr>
                </table>
            </div>

            <?php if ($booking['payment_status'] === 'paid'): ?>
                <form method="POST" action="/checkin/<?= $booking['uuid'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <button type="submit" class="btn" style="width:100%;padding:1rem;font-size:1.1rem;background:linear-gradient(135deg,#333333,#000000)">
                        <i class="fas fa-check-circle"></i> Bevestig Check-in
                    </button>
                </form>
                <p style="text-align:center;color:var(--text-light);font-size:0.85rem;margin-top:1rem">
                    <i class="fas fa-info-circle"></i> Na check-in wordt de uitbetaling vrijgegeven
                </p>
            <?php else: ?>
                <div style="background:#f5f5f5;border-radius:10px;padding:1rem;text-align:center;color:#000000">
                    <i class="fas fa-exclamation-circle"></i> Boeking is nog niet betaald
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- Not logged in as business -->
        <div class="card text-center">
            <div style="width:80px;height:80px;background:linear-gradient(135deg,#000000,#000000);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
                <i class="fas fa-store" style="font-size:2rem;color:white"></i>
            </div>
            <h2>Check-in voor <?= htmlspecialchars($booking['business_name']) ?></h2>
            <p style="color:var(--text-light);margin-bottom:1.5rem">
                Log in als <?= htmlspecialchars($booking['business_name']) ?> om deze klant in te checken.
            </p>

            <div style="background:var(--secondary);border-radius:12px;padding:1rem;margin-bottom:1.5rem;text-align:left">
                <p style="margin:0"><strong>Boeking:</strong> #<?= htmlspecialchars($booking['booking_number']) ?></p>
                <p style="margin:0.5rem 0 0 0"><strong>Dienst:</strong> <?= htmlspecialchars($booking['service_name']) ?></p>
                <p style="margin:0.5rem 0 0 0"><strong>Tijd:</strong> <?= date('d-m-Y', strtotime($booking['appointment_date'])) ?> om <?= date('H:i', strtotime($booking['appointment_time'])) ?></p>
            </div>

            <a href="/business/login?redirect=<?= urlencode('/checkin/' . $booking['uuid']) ?>" class="btn" style="width:100%">
                <i class="fas fa-sign-in-alt"></i> Inloggen als bedrijf
            </a>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

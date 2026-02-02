<?php ob_start(); ?>

<style>
    .scanner-container {
        max-width: 500px;
        margin: 0 auto;
    }
    #reader {
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
    }
    #reader video {
        border-radius: 16px;
    }
    .scan-result {
        display: none;
        margin-top: 1.5rem;
        padding: 1.5rem;
        border-radius: 16px;
        text-align: center;
    }
    .scan-result.success {
        background: linear-gradient(135deg, #0a0a0a, #111111);
        border: 2px solid #22c55e;
        color: #22c55e;
    }
    .scan-result.success h3 {
        color: #22c55e;
    }
    .scan-result.success p {
        color: #ffffff;
    }
    .scan-result.error {
        background: linear-gradient(135deg, #0a0a0a, #111111);
        border: 2px solid #ef4444;
        color: #ef4444;
    }
    .scan-result.error h3 {
        color: #ef4444;
    }
    .scan-result.error p {
        color: #ffffff;
    }
    .scan-result.warning {
        background: linear-gradient(135deg, #0a0a0a, #111111);
        border: 2px solid #f59e0b;
        color: #f59e0b;
    }
    .scan-result.warning h3 {
        color: #f59e0b;
    }
    .scan-result.warning p {
        color: #ffffff;
    }
    .result-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
        color: white;
    }
    .success .result-icon { background: linear-gradient(135deg, #333333, #000000); }
    .error .result-icon { background: linear-gradient(135deg, #333333, #dc2626); }
    .warning .result-icon { background: linear-gradient(135deg, #000000, #404040); }
    .booking-details {
        background: #1a1a1a;
        border: 1px solid #333333;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
        text-align: left;
        color: #ffffff;
    }
    .booking-details table {
        width: 100%;
    }
    .booking-details td {
        padding: 0.5rem 0;
        color: #ffffff;
    }
    .booking-details td:first-child {
        color: #999999;
    }
    .booking-details td:last-child {
        text-align: right;
        font-weight: 600;
    }
    .scanner-instructions {
        background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
        border: 1px solid #333333;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .scanner-instructions h4 {
        margin: 0 0 0.5rem 0;
        color: #ffffff;
    }
    .scanner-instructions p {
        margin: 0;
        color: #999999;
        font-size: 0.9rem;
    }
</style>

<div class="scanner-container">
    <div class="card">
        <h2 style="margin:0 0 1.5rem 0;text-align:center">
            <i class="fas fa-qrcode"></i> QR Scanner
        </h2>

        <div class="scanner-instructions">
            <h4><i class="fas fa-info-circle"></i> Hoe werkt het?</h4>
            <p>Laat de klant de QR code tonen uit de boekingsbevestiging. Scan de code om de aanwezigheid te bevestigen.</p>
        </div>

        <div id="reader"></div>

        <div id="scanResult" class="scan-result">
            <div class="result-icon">
                <i id="resultIcon" class="fas fa-check"></i>
            </div>
            <h3 id="resultTitle">Resultaat</h3>
            <p id="resultMessage"></p>
            <div id="bookingDetails" class="booking-details" style="display:none">
                <table>
                    <tr>
                        <td>Boeking</td>
                        <td id="detailNumber">-</td>
                    </tr>
                    <tr id="verificationRow" style="display:none">
                        <td><i class="fas fa-shield-alt" style="color:#f59e0b"></i> Verificatie</td>
                        <td id="detailVerification" style="font-family:monospace;letter-spacing:1px">-</td>
                    </tr>
                    <tr>
                        <td>Klant</td>
                        <td id="detailCustomer">-</td>
                    </tr>
                    <tr>
                        <td>Dienst</td>
                        <td id="detailService">-</td>
                    </tr>
                    <tr>
                        <td>Tijd</td>
                        <td id="detailTime">-</td>
                    </tr>
                    <tr>
                        <td>Bedrag</td>
                        <td id="detailPrice">-</td>
                    </tr>
                </table>
            </div>
            <button id="scanAgainBtn" class="btn" style="margin-top:1.5rem;width:100%" onclick="startScanner()">
                <i class="fas fa-redo"></i> Opnieuw scannen
            </button>
        </div>

        <div id="scannerError" style="display:none;margin-top:1rem;padding:1rem;background:#1a1a1a;border:1px solid #ef4444;border-radius:10px;color:#ef4444;text-align:center">
            <i class="fas fa-exclamation-circle"></i> <span id="errorMessage"></span>
        </div>
    </div>

    <!-- Manual input fallback -->
    <div class="card" style="margin-top:1rem">
        <h4 style="margin:0 0 1rem 0"><i class="fas fa-keyboard"></i> Handmatige invoer</h4>
        <p style="color:var(--text-light);font-size:0.9rem;margin-bottom:0.5rem">
            Camera werkt niet? Voer een van de volgende codes in:
        </p>
        <ul style="color:var(--text-light);font-size:0.85rem;margin:0 0 1rem 1rem;padding:0">
            <li>Boekingsnummer: <strong>GS12345678</strong></li>
            <li>Verificatiecode (SHA256): <strong>A1B2-C3D4-E5F6</strong></li>
        </ul>
        <form id="manualForm" style="display:flex;gap:0.5rem">
            <input type="text" id="manualInput" class="form-control" placeholder="GS12345678 of A1B2-C3D4-E5F6" style="flex:1;text-transform:uppercase">
            <button type="submit" class="btn"><i class="fas fa-check"></i> Check-in</button>
        </form>
        <p style="margin-top:0.75rem;font-size:0.75rem;color:#666">
            <i class="fas fa-shield-alt"></i> De verificatiecode is gekoppeld aan uw salon via SHA256 encryptie
        </p>
    </div>
</div>

<!-- QR Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
const csrfToken = '<?= $csrfToken ?>';
let html5QrCode = null;

function startScanner() {
    document.getElementById('scanResult').style.display = 'none';
    document.getElementById('scannerError').style.display = 'none';

    if (html5QrCode) {
        html5QrCode.stop().catch(err => console.log(err));
    }

    html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1
        },
        onScanSuccess,
        onScanFailure
    ).catch(err => {
        console.error('Camera error:', err);
        document.getElementById('scannerError').style.display = 'block';
        document.getElementById('errorMessage').textContent = 'Kon camera niet starten. Geef toestemming of gebruik handmatige invoer.';
    });
}

function onScanSuccess(decodedText, decodedResult) {
    // Stop scanner
    if (html5QrCode) {
        html5QrCode.stop().catch(err => console.log(err));
    }

    // Play success sound
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        gainNode.gain.value = 0.3;
        oscillator.start();
        setTimeout(() => oscillator.stop(), 150);
    } catch(e) {}

    // Process the QR code
    processCheckin(decodedText);
}

function onScanFailure(error) {
    // Ignore scan failures (continuous scanning)
}

async function processCheckin(qrData) {
    try {
        const response = await fetch('/business/checkin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                qr_data: qrData,
                csrf_token: csrfToken
            })
        });

        const data = await response.json();
        showResult(data);
    } catch (error) {
        showResult({
            success: false,
            error: 'Netwerkfout. Probeer opnieuw.'
        });
    }
}

function showResult(data) {
    const resultDiv = document.getElementById('scanResult');
    const icon = document.getElementById('resultIcon');
    const title = document.getElementById('resultTitle');
    const message = document.getElementById('resultMessage');
    const details = document.getElementById('bookingDetails');

    resultDiv.className = 'scan-result ' + (data.success ? 'success' : (data.booking ? 'warning' : 'error'));

    if (data.success) {
        icon.className = 'fas fa-check';
        title.textContent = 'Ingecheckt!';
        message.textContent = data.message;
        // Vibrate on success
        if (navigator.vibrate) navigator.vibrate(200);
    } else {
        icon.className = data.booking ? 'fas fa-exclamation' : 'fas fa-times';
        title.textContent = data.booking ? 'Let op' : 'Fout';
        message.textContent = data.error;
    }

    // Show booking details if available
    if (data.booking) {
        details.style.display = 'block';
        document.getElementById('detailNumber').textContent = '#' + data.booking.booking_number;
        document.getElementById('detailCustomer').textContent = data.booking.customer_name;
        document.getElementById('detailService').textContent = data.booking.service_name;
        document.getElementById('detailTime').textContent = data.booking.date + ' ' + data.booking.time;
        document.getElementById('detailPrice').textContent = '€' + data.booking.price;

        // Show verification code if available
        const verificationRow = document.getElementById('verificationRow');
        if (data.booking.verification_code) {
            verificationRow.style.display = '';
            document.getElementById('detailVerification').textContent = data.booking.verification_code;
        } else {
            verificationRow.style.display = 'none';
        }
    } else {
        details.style.display = 'none';
    }

    resultDiv.style.display = 'block';
}

// Manual input form
document.getElementById('manualForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('manualInput').value.trim();
    if (input) {
        processCheckin(input);
    }
});

// Start scanner on page load
document.addEventListener('DOMContentLoaded', startScanner);
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>

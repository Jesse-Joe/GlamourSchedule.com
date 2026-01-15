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
        background: linear-gradient(135deg, #ffffff, #f5f5f5);
        border: 2px solid #333333;
        color: #166534;
    }
    .scan-result.success h3 {
        color: #14532d;
    }
    .scan-result.success p {
        color: #166534;
    }
    .scan-result.error {
        background: linear-gradient(135deg, #f5f5f5, #f5f5f5);
        border: 2px solid #333333;
        color: #000000;
    }
    .scan-result.error h3 {
        color: #7f1d1d;
    }
    .scan-result.error p {
        color: #000000;
    }
    .scan-result.warning {
        background: linear-gradient(135deg, #ffffff, #ffffff);
        border: 2px solid #000000;
        color: #000000;
    }
    .scan-result.warning h3 {
        color: #000000;
    }
    .scan-result.warning p {
        color: #000000;
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
        background: #ffffff;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
        text-align: left;
    }
    .booking-details table {
        width: 100%;
    }
    .booking-details td {
        padding: 0.5rem 0;
    }
    .booking-details td:last-child {
        text-align: right;
        font-weight: 600;
    }
    .scanner-instructions {
        background: linear-gradient(135deg, #eff6ff, #f5f5f5);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .scanner-instructions h4 {
        margin: 0 0 0.5rem 0;
        color: #000000;
    }
    .scanner-instructions p {
        margin: 0;
        color: #404040;
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

        <div id="scannerError" style="display:none;margin-top:1rem;padding:1rem;background:#f5f5f5;border-radius:10px;color:#000000;text-align:center">
            <i class="fas fa-exclamation-circle"></i> <span id="errorMessage"></span>
        </div>
    </div>

    <!-- Manual input fallback -->
    <div class="card" style="margin-top:1rem">
        <h4 style="margin:0 0 1rem 0"><i class="fas fa-keyboard"></i> Handmatige invoer</h4>
        <p style="color:var(--text-light);font-size:0.9rem;margin-bottom:1rem">
            Camera werkt niet? Voer het boekingsnummer in (bijv. <strong>GS-240104-AB12</strong>)
        </p>
        <form id="manualForm" style="display:flex;gap:0.5rem">
            <input type="text" id="manualInput" class="form-control" placeholder="Bijv. GS-240104-AB12" style="flex:1;text-transform:uppercase">
            <button type="submit" class="btn"><i class="fas fa-check"></i> Check-in</button>
        </form>
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

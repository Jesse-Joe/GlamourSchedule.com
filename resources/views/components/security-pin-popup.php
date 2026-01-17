<?php
/**
 * Security PIN Setup Popup
 * Shows when user is logged in but hasn't set up a 6-digit security PIN
 */

// Only show if user is logged in
$showPinSetup = false;
$userId = $_SESSION['user_id'] ?? null;
$businessId = $_SESSION['business_id'] ?? null;

if ($userId || $businessId) {
    // Check if PIN is already set (using a cookie to track if we've shown this)
    $pinSetupDismissed = isset($_COOKIE['pin_setup_dismissed']);
    $pinAlreadySet = isset($_COOKIE['security_pin_set']) || isset($_SESSION['security_pin_set']);

    if (!$pinSetupDismissed && !$pinAlreadySet) {
        $showPinSetup = true;
    }
}
?>

<?php if ($showPinSetup): ?>
<!-- Security PIN Setup Popup -->
<div id="securityPinPopup" class="security-pin-popup" style="display:none">
    <div class="security-pin-overlay" onclick="dismissPinPopup()"></div>
    <div class="security-pin-modal">
        <button class="security-pin-close" onclick="dismissPinPopup()">&times;</button>

        <div class="security-pin-content">
            <div class="security-pin-icon">
                <i class="fas fa-shield-alt"></i>
            </div>

            <h2>Beveilig je account</h2>
            <p>Stel een 6-cijferige PIN code in voor extra beveiliging van je account.</p>

            <form id="securityPinForm" onsubmit="savePinCode(event)">
                <div class="pin-input-container">
                    <input type="tel" maxlength="1" class="pin-input" data-index="0" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                    <input type="tel" maxlength="1" class="pin-input" data-index="1" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                    <input type="tel" maxlength="1" class="pin-input" data-index="2" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                    <input type="tel" maxlength="1" class="pin-input" data-index="3" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                    <input type="tel" maxlength="1" class="pin-input" data-index="4" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                    <input type="tel" maxlength="1" class="pin-input" data-index="5" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                </div>
                <input type="hidden" id="fullPinCode" name="pin_code">

                <div id="pinError" class="pin-error" style="display:none">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Voer alle 6 cijfers in</span>
                </div>

                <div class="pin-benefits">
                    <div class="pin-benefit">
                        <i class="fas fa-lock"></i>
                        <span>Extra beveiligingslaag</span>
                    </div>
                    <div class="pin-benefit">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Snelle toegang in de app</span>
                    </div>
                    <div class="pin-benefit">
                        <i class="fas fa-user-shield"></i>
                        <span>Bescherm je gegevens</span>
                    </div>
                </div>

                <button type="submit" class="pin-submit-btn" id="pinSubmitBtn">
                    <i class="fas fa-check"></i> PIN instellen
                </button>
            </form>

            <button class="pin-later-btn" onclick="dismissPinPopup()">
                Later instellen
            </button>
        </div>
    </div>
</div>

<style>
.security-pin-popup {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10005;
    display: flex;
    align-items: center;
    justify-content: center;
}
.security-pin-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(8px);
}
.security-pin-modal {
    position: relative;
    background: #000;
    border: 2px solid #333;
    border-radius: 24px;
    padding: 2.5rem;
    max-width: 400px;
    width: 90%;
    animation: pinPopupSlide 0.4s ease;
}
@keyframes pinPopupSlide {
    from { opacity: 0; transform: translateY(30px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.security-pin-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    color: #666;
    font-size: 1.75rem;
    cursor: pointer;
    line-height: 1;
    transition: color 0.2s;
}
.security-pin-close:hover {
    color: #fff;
}
.security-pin-content {
    text-align: center;
}
.security-pin-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.security-pin-icon i {
    font-size: 2.5rem;
    color: #fff;
}
.security-pin-content h2 {
    color: #fff;
    font-size: 1.5rem;
    margin: 0 0 0.75rem;
    font-weight: 600;
}
.security-pin-content > p {
    color: rgba(255,255,255,0.7);
    margin: 0 0 1.5rem;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* PIN Input Styling */
.pin-input-container {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.pin-input {
    width: 48px;
    height: 56px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    background: #1a1a1a;
    border: 2px solid #333;
    border-radius: 12px;
    color: #fff;
    transition: all 0.2s;
}
.pin-input:focus {
    outline: none;
    border-color: #22c55e;
    background: #111;
}
.pin-input.filled {
    border-color: #22c55e;
    background: rgba(34,197,94,0.1);
}
.pin-input.error {
    border-color: #ef4444;
    animation: pinShake 0.3s;
}
@keyframes pinShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.pin-error {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: #ef4444;
    font-size: 0.85rem;
    margin-bottom: 1rem;
}

.pin-benefits {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin: 1.5rem 0;
    text-align: left;
}
.pin-benefit {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: rgba(255,255,255,0.05);
    border-radius: 10px;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.8);
}
.pin-benefit i {
    color: #22c55e;
    width: 20px;
    text-align: center;
}

.pin-submit-btn {
    width: 100%;
    padding: 1rem;
    background: #22c55e;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s;
}
.pin-submit-btn:hover {
    background: #16a34a;
    transform: translateY(-2px);
}
.pin-submit-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.pin-later-btn {
    margin-top: 1rem;
    background: none;
    border: none;
    color: rgba(255,255,255,0.5);
    font-size: 0.9rem;
    cursor: pointer;
    transition: color 0.2s;
}
.pin-later-btn:hover {
    color: #fff;
}

@media (max-width: 400px) {
    .pin-input {
        width: 40px;
        height: 48px;
        font-size: 1.25rem;
    }
    .security-pin-modal {
        padding: 1.5rem;
    }
}
</style>

<script>
// PIN Input Logic
document.addEventListener('DOMContentLoaded', function() {
    const pinInputs = document.querySelectorAll('.pin-input');

    pinInputs.forEach((input, index) => {
        // Handle input
        input.addEventListener('input', function(e) {
            const value = e.target.value;

            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            if (value.length === 1) {
                input.classList.add('filled');
                // Move to next input
                if (index < pinInputs.length - 1) {
                    pinInputs[index + 1].focus();
                }
            }

            updateFullPinCode();
            hideError();
        });

        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                pinInputs[index - 1].focus();
                pinInputs[index - 1].value = '';
                pinInputs[index - 1].classList.remove('filled');
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);

            pastedData.split('').forEach((char, i) => {
                if (pinInputs[i]) {
                    pinInputs[i].value = char;
                    pinInputs[i].classList.add('filled');
                }
            });

            if (pastedData.length > 0) {
                const focusIndex = Math.min(pastedData.length, pinInputs.length - 1);
                pinInputs[focusIndex].focus();
            }

            updateFullPinCode();
        });
    });
});

function updateFullPinCode() {
    const pinInputs = document.querySelectorAll('.pin-input');
    let fullCode = '';
    pinInputs.forEach(input => {
        fullCode += input.value;
    });
    document.getElementById('fullPinCode').value = fullCode;
}

function showError(message) {
    const errorDiv = document.getElementById('pinError');
    errorDiv.querySelector('span').textContent = message;
    errorDiv.style.display = 'flex';

    document.querySelectorAll('.pin-input').forEach(input => {
        input.classList.add('error');
        setTimeout(() => input.classList.remove('error'), 300);
    });
}

function hideError() {
    document.getElementById('pinError').style.display = 'none';
}

function savePinCode(event) {
    event.preventDefault();

    const pinCode = document.getElementById('fullPinCode').value;

    if (pinCode.length !== 6) {
        showError('Voer alle 6 cijfers in');
        return;
    }

    // Show loading state
    const submitBtn = document.getElementById('pinSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Opslaan...';
    submitBtn.disabled = true;

    // Save PIN via API
    fetch('/api/save-security-pin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ pin: pinCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Set cookie to remember PIN is set
            document.cookie = 'security_pin_set=1;max-age=' + (365 * 24 * 60 * 60) + ';path=/';

            // Show success and close
            submitBtn.innerHTML = '<i class="fas fa-check"></i> PIN ingesteld!';
            submitBtn.style.background = '#22c55e';

            setTimeout(() => {
                document.getElementById('securityPinPopup').style.display = 'none';
            }, 1500);
        } else {
            showError(data.message || 'Er ging iets mis');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Verbindingsfout, probeer opnieuw');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function dismissPinPopup() {
    document.getElementById('securityPinPopup').style.display = 'none';
    // Remember dismissal for 7 days
    document.cookie = 'pin_setup_dismissed=1;max-age=' + (7 * 24 * 60 * 60) + ';path=/';
}

// Show popup after delay if user is logged in
setTimeout(function() {
    const popup = document.getElementById('securityPinPopup');
    if (popup) {
        popup.style.display = 'flex';
        // Focus first input
        const firstInput = popup.querySelector('.pin-input');
        if (firstInput) firstInput.focus();
    }
}, 8000); // Show after 8 seconds

// Close on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const popup = document.getElementById('securityPinPopup');
        if (popup && popup.style.display !== 'none') {
            dismissPinPopup();
        }
    }
});
</script>
<?php endif; ?>

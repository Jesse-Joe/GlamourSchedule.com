<?php ob_start(); ?>

<div class="grid-2">
    <!-- Referral Link -->
    <div class="card">
        <h3><i class="fas fa-link"></i> Je Referral Link</h3>
        <div style="background:#ffffff;padding:1rem;border-radius:10px;word-break:break-all;font-family:monospace;font-size:0.9rem;margin-bottom:1rem;color:#000000;border:1px solid rgba(0,0,0,0.1)">
            https://glamourschedule.nl/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?>
        </div>
        <button onclick="copyLink()" class="btn btn-primary" style="width:100%">
            <i class="fas fa-copy"></i> Kopieer Link
        </button>
    </div>

    <!-- QR Code -->
    <div class="card">
        <h3><i class="fas fa-qrcode"></i> QR Code</h3>
        <div style="text-align:center;padding:1rem">
            <div style="background:#ffffff;padding:1rem;border-radius:12px;display:inline-block;border:2px solid #333333">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://glamourschedule.nl/partner/register?ref=<?= urlencode($salesUser['referral_code']) ?>"
                     alt="QR Code" style="max-width:150px;display:block">
            </div>
        </div>
        <p style="text-align:center;color:#555555;font-size:0.9rem;margin:0">
            Laat scannen om direct te registreren met jouw partnerkorting
        </p>
    </div>
</div>

<!-- Partner Info -->
<div class="card" style="background:#ffffff;margin-top:1.5rem;border:1px solid #333333">
    <h3 style="color:#000000"><i class="fas fa-info-circle"></i> Jouw Partner Voordelen</h3>
    <div class="grid-3" style="text-align:center">
        <div>
            <div style="font-size:2rem;font-weight:700;color:#000000">€25</div>
            <div style="color:#555555;font-size:0.9rem">Korting voor de klant</div>
            <div style="color:#888888;font-size:0.8rem">op registratiekosten</div>
        </div>
        <div>
            <div style="font-size:2rem;font-weight:700;color:#000000">€99,99</div>
            <div style="color:#555555;font-size:0.9rem">Jouw commissie</div>
            <div style="color:#888888;font-size:0.8rem">per betalende klant</div>
        </div>
        <div>
            <div style="font-size:2rem;font-weight:700;color:#000000"><?= htmlspecialchars($salesUser['referral_code']) ?></div>
            <div style="color:#555555;font-size:0.9rem">Jouw partnercode</div>
        </div>
    </div>
</div>

<!-- Quick Email Send -->
<div class="card" style="margin-top:1.5rem;border:2px solid #333333">
    <h3><i class="fas fa-paper-plane"></i> Stuur een uitnodiging per email</h3>
    <p style="color:#555555;margin-bottom:1.5rem">
        Stuur direct een professionele uitnodiging naar een salon. De email bevat informatie over de 14 dagen gratis proefperiode en jouw partnerkorting. Verstuurd vanuit sales@glamourschedule.com.
    </p>

    <form id="quickEmailForm" onsubmit="sendQuickEmail(event)">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
            <div>
                <label style="display:block;color:#333333;margin-bottom:0.5rem;font-size:0.9rem">
                    <i class="fas fa-store"></i> Salonnaam
                </label>
                <input type="text" name="salon_name" required placeholder="Bijv. Beauty Salon Amsterdam"
                       style="width:100%;padding:0.875rem;background:#ffffff;border:1px solid rgba(0,0,0,0.1);border-radius:10px;color:#333333;font-size:1rem">
            </div>
            <div>
                <label style="display:block;color:#333333;margin-bottom:0.5rem;font-size:0.9rem">
                    <i class="fas fa-envelope"></i> E-mailadres salon
                </label>
                <input type="email" name="salon_email" required placeholder="info@salon.nl"
                       style="width:100%;padding:0.875rem;background:#ffffff;border:1px solid rgba(0,0,0,0.1);border-radius:10px;color:#333333;font-size:1rem">
            </div>
        </div>

        <div style="margin-bottom:1rem">
            <label style="display:block;color:#333333;margin-bottom:0.5rem;font-size:0.9rem">
                <i class="fas fa-comment"></i> Persoonlijke boodschap (optioneel)
            </label>
            <textarea name="personal_message" rows="2" placeholder="Bijv. We ontmoetten elkaar op de beautybeurs..."
                      style="width:100%;padding:0.875rem;background:#ffffff;border:1px solid rgba(0,0,0,0.1);border-radius:10px;color:#333333;font-size:1rem;resize:vertical"></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%" id="sendEmailBtn">
            <i class="fas fa-paper-plane"></i> Verstuur Email met Referral Link
        </button>
    </form>

    <div id="emailResult" style="margin-top:1rem;display:none"></div>
</div>

<!-- Text Templates -->
<div class="card" style="margin-top:1.5rem">
    <h3><i class="fas fa-comment-alt"></i> Tekst Templates</h3>

    <div style="margin-bottom:1.5rem">
        <h4 style="margin:0 0 0.5rem 0;color:#333333">WhatsApp / SMS</h4>
        <div style="background:#ffffff;padding:1rem;border-radius:10px;position:relative;border:1px solid rgba(0,0,0,0.1)">
            <p style="margin:0;font-size:0.95rem;line-height:1.6;color:#333333" id="whatsappText">Hey! Ken je GlamourSchedule al? Het online boekingssysteem voor salons. Je kunt het 14 dagen gratis proberen en via mijn link krijg je 25 euro korting op de eenmalige registratie. Geen maandelijkse kosten, je betaalt alleen per boeking. Kijk hier: https://glamourschedule.nl/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?></p>
            <button onclick="copyText('whatsappText')" style="position:absolute;top:0.5rem;right:0.5rem;background:#333333;color:#ffffff;border:none;padding:0.5rem 0.75rem;border-radius:6px;cursor:pointer;font-size:0.8rem;font-weight:600">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </div>

    <div style="margin-bottom:1.5rem">
        <h4 style="margin:0 0 0.5rem 0;color:#333333">E-mail Template</h4>
        <div style="background:#ffffff;padding:1rem;border-radius:10px;position:relative;border:1px solid rgba(0,0,0,0.1)">
            <p style="margin:0;font-size:0.95rem;line-height:1.6;white-space:pre-line;color:#333333" id="emailText">Onderwerp: 14 dagen gratis proberen + 25 euro korting op GlamourSchedule

Beste ondernemer,

Ben je op zoek naar een modern boekingssysteem voor je salon? Met GlamourSchedule kun je:

- Online boekingen ontvangen, 24/7
- Automatische herinneringen sturen naar klanten
- Betalingen ontvangen via iDEAL
- Je eigen professionele salonpagina krijgen

Het mooie: je kunt het 14 dagen gratis uitproberen. Daarna betaal je eenmalig 74,99 euro registratiekosten (normaal 99,99 euro). Geen maandelijkse kosten - je betaalt alleen 1,75 euro per boeking die je ontvangt.

Geen boekingen? Dan betaal je niets.

Registreer via onderstaande link:
https://glamourschedule.nl/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?>

Met vriendelijke groet</p>
            <button onclick="copyText('emailText')" style="position:absolute;top:0.5rem;right:0.5rem;background:#333333;color:#ffffff;border:none;padding:0.5rem 0.75rem;border-radius:6px;cursor:pointer;font-size:0.8rem;font-weight:600">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </div>

    <div style="margin-bottom:1.5rem">
        <h4 style="margin:0 0 0.5rem 0;color:#333333">Social Media Post</h4>
        <div style="background:#ffffff;padding:1rem;border-radius:10px;position:relative;border:1px solid rgba(0,0,0,0.1)">
            <p style="margin:0;font-size:0.95rem;line-height:1.6;white-space:pre-line;color:#333333" id="socialText">Tip voor saloneigenaren!

GlamourSchedule: het boekingssysteem zonder maandelijkse kosten.

14 dagen gratis proberen
Daarna eenmalig 74,99 euro (25 euro korting via mijn link)
Je betaalt alleen per boeking die je ontvangt

Wat krijg je:
- Online boekingen 24/7
- Automatische herinneringen naar klanten
- iDEAL betalingen
- Je eigen salonpagina

Bekijk het hier:
glamourschedule.nl/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?>

#salon #beauty #ondernemen #boekingssysteem</p>
            <button onclick="copyText('socialText')" style="position:absolute;top:0.5rem;right:0.5rem;background:#333333;color:#ffffff;border:none;padding:0.5rem 0.75rem;border-radius:6px;cursor:pointer;font-size:0.8rem;font-weight:600">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </div>

    <div>
        <h4 style="margin:0 0 0.5rem 0;color:#333333">Korte pitch (face-to-face)</h4>
        <div style="background:#ffffff;padding:1rem;border-radius:10px;position:relative;border:1px solid rgba(0,0,0,0.1)">
            <p style="margin:0;font-size:0.95rem;line-height:1.6;white-space:pre-line;color:#333333" id="pitchText">GlamourSchedule is een online boekingssysteem voor salons.

Klanten kunnen 24/7 online boeken, krijgen automatisch een herinnering, en kunnen direct via iDEAL betalen.

Je kunt het 14 dagen gratis proberen. Daarna kost het eenmalig 74,99 euro - met 25 euro korting via mij. Geen maandelijkse kosten. Je betaalt alleen 1,75 euro per boeking die je ontvangt.

Geen boekingen? Dan betaal je ook niets.

Zal ik je even laten zien hoe het werkt?</p>
            <button onclick="copyText('pitchText')" style="position:absolute;top:0.5rem;right:0.5rem;background:#333333;color:#ffffff;border:none;padding:0.5rem 0.75rem;border-radius:6px;cursor:pointer;font-size:0.8rem;font-weight:600">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </div>
</div>

<!-- Download Section -->
<div class="card" style="margin-top:1.5rem">
    <h3><i class="fas fa-download"></i> Downloads</h3>
    <div class="grid-2">
        <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&format=png&data=https://glamourschedule.nl/partner/register?ref=<?= urlencode($salesUser['referral_code']) ?>"
           download="qr-code-<?= htmlspecialchars($salesUser['referral_code']) ?>.png"
           style="display:flex;align-items:center;gap:0.75rem;padding:1rem;background:#ffffff;border-radius:10px;text-decoration:none;color:#333333;border:1px solid rgba(0,0,0,0.1)">
            <i class="fas fa-qrcode" style="font-size:1.5rem;color:#333333"></i>
            <div>
                <div style="font-weight:600">QR Code (PNG)</div>
                <div style="font-size:0.85rem;color:#666666">Hoge resolutie voor print</div>
            </div>
        </a>
        <a href="/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?>"
           target="_blank"
           style="display:flex;align-items:center;gap:0.75rem;padding:1rem;background:#ffffff;border-radius:10px;text-decoration:none;color:#333333;border:1px solid rgba(0,0,0,0.1)">
            <i class="fas fa-external-link-alt" style="font-size:1.5rem;color:#333333"></i>
            <div>
                <div style="font-weight:600">Bekijk registratiepagina</div>
                <div style="font-size:0.85rem;color:#666666">Zoals klanten het zien</div>
            </div>
        </a>
    </div>
</div>

<script>
    function copyLink() {
        const link = 'https://glamourschedule.nl/partner/register?ref=<?= htmlspecialchars($salesUser['referral_code']) ?>';
        navigator.clipboard.writeText(link).then(() => {
            showToast('Link gekopieerd!');
        });
    }

    function copyText(elementId) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            showToast('Tekst gekopieerd!');
        });
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#333333;color:#ffffff;padding:0.75rem 1.5rem;border-radius:10px;font-weight:600;z-index:9999;animation:fadeInUp 0.3s ease';
        toast.innerHTML = '<i class="fas fa-check"></i> ' + message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }

    async function sendQuickEmail(e) {
        e.preventDefault();
        const btn = document.getElementById('sendEmailBtn');
        const result = document.getElementById('emailResult');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Versturen...';

        const formData = new FormData(e.target);
        formData.append('csrf_token', '<?= $csrfToken ?? '' ?>');

        try {
            const response = await fetch('/sales/send-referral-email', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                result.style.display = 'block';
                result.innerHTML = '<div style="background:rgba(16,185,129,0.15);border:1px solid #333333;color:#000000;padding:1rem;border-radius:10px"><i class="fas fa-check-circle"></i> Email succesvol verstuurd naar ' + formData.get('salon_email') + '!</div>';
                e.target.reset();
            } else {
                result.style.display = 'block';
                result.innerHTML = '<div style="background:rgba(239,68,68,0.15);border:1px solid #333333;color:#d4d4d4;padding:1rem;border-radius:10px"><i class="fas fa-exclamation-circle"></i> ' + (data.error || 'Er ging iets mis') + '</div>';
            }
        } catch (err) {
            result.style.display = 'block';
            result.innerHTML = '<div style="background:rgba(239,68,68,0.15);border:1px solid #333333;color:#d4d4d4;padding:1rem;border-radius:10px"><i class="fas fa-exclamation-circle"></i> Verbindingsfout. Probeer opnieuw.</div>';
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Verstuur Email met Referral Link';
    }
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translate(-50%, 20px); }
        to { opacity: 1; transform: translate(-50%, 0); }
    }
    input:focus, textarea:focus {
        border-color: #333333 !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
    }
    input::placeholder, textarea::placeholder {
        color: #666666;
    }
</style>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/sales.php'; ?>

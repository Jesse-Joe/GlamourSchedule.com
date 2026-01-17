<?php ob_start(); ?>

<style>
.guide-container {
    max-width: 900px;
    margin: 0 auto;
}
.steps-progress {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
}
.steps-progress::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e5e7eb;
    z-index: 0;
}
.step-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 1;
    cursor: pointer;
}
.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 0.5rem;
    transition: all 0.3s;
}
.step-indicator.active .step-number {
    background: linear-gradient(135deg, #333333, #000000);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
.step-indicator.completed .step-number {
    background: #333333;
    color: white;
}
.step-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-align: center;
    max-width: 80px;
}
.step-indicator.active .step-label {
    color: #333333;
    font-weight: 600;
}
.step-content {
    display: none;
    background: #ffffff;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.step-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.step-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f5f5f5;
}
.step-header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #333333, #000000);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.step-header h2 {
    margin: 0;
    font-size: 1.35rem;
}
.step-header p {
    margin: 0.25rem 0 0;
    color: #6b7280;
    font-size: 0.9rem;
}
.script-box {
    background: linear-gradient(135deg, #ffffff, #ffffff);
    border: 2px solid #333333;
    border-radius: 12px;
    padding: 1.25rem;
    margin: 1rem 0;
    position: relative;
}
.script-box::before {
    content: 'SCRIPT';
    position: absolute;
    top: -10px;
    left: 1rem;
    background: #333333;
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
}
.script-box p {
    margin: 0;
    font-style: italic;
    color: #000000;
    line-height: 1.7;
}
.tip-box {
    background: #ffffff;
    border-left: 4px solid #000000;
    border-radius: 0 8px 8px 0;
    padding: 1rem 1.25rem;
    margin: 1rem 0;
}
.tip-box strong {
    color: #000000;
}
.tip-box p {
    margin: 0.5rem 0 0;
    color: #000000;
    font-size: 0.95rem;
}
.objection-card {
    background: #f5f5f5;
    border-radius: 10px;
    padding: 1rem;
    margin: 0.75rem 0;
}
.objection-card .question {
    color: #000000;
    font-weight: 600;
    margin: 0 0 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.objection-card .answer {
    color: #000000;
    background: #ffffff;
    padding: 0.75rem;
    border-radius: 8px;
    margin: 0;
    font-size: 0.95rem;
}
.checklist {
    list-style: none;
    padding: 0;
    margin: 1rem 0;
}
.checklist li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f5f5f5;
}
.checklist li:last-child {
    border-bottom: none;
}
.checklist .check {
    width: 24px;
    height: 24px;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    cursor: pointer;
    transition: all 0.2s;
}
.checklist .check.checked {
    background: #333333;
    border-color: #333333;
    color: white;
}
.btn-nav {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}
.btn-step {
    padding: 0.875rem 1.75rem;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.btn-prev {
    background: #f5f5f5;
    color: #374151;
    border: none;
}
.btn-prev:hover {
    background: #e5e7eb;
}
.btn-next {
    background: linear-gradient(135deg, #333333, #000000);
    color: white;
    border: none;
}
.btn-next:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
.highlight-box {
    background: linear-gradient(135deg, #333333, #000000);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin: 1rem 0;
    text-align: center;
}
.highlight-box h3 {
    margin: 0 0 0.5rem;
    font-size: 1.5rem;
}
.highlight-box p {
    margin: 0;
    opacity: 0.9;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin: 1rem 0;
}
.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #fafafa;
    border-radius: 8px;
}
.feature-item i {
    color: #333333;
    font-size: 1.1rem;
}
</style>

<div class="guide-container">
    <!-- Progress Steps -->
    <div class="steps-progress">
        <div class="step-indicator active" onclick="goToStep(1)">
            <div class="step-number">1</div>
            <span class="step-label">Contact</span>
        </div>
        <div class="step-indicator" onclick="goToStep(2)">
            <div class="step-number">2</div>
            <span class="step-label">Introductie</span>
        </div>
        <div class="step-indicator" onclick="goToStep(3)">
            <div class="step-number">3</div>
            <span class="step-label">Voordelen</span>
        </div>
        <div class="step-indicator" onclick="goToStep(4)">
            <div class="step-number">4</div>
            <span class="step-label">Bezwaren</span>
        </div>
        <div class="step-indicator" onclick="goToStep(5)">
            <div class="step-number">5</div>
            <span class="step-label">Afsluiten</span>
        </div>
    </div>

    <!-- Step 1: Contact -->
    <div class="step-content active" id="step1">
        <div class="step-header">
            <div class="step-header-icon"><i class="fas fa-phone"></i></div>
            <div>
                <h2>Stap 1: Eerste Contact</h2>
                <p>Maak een goede eerste indruk</p>
            </div>
        </div>

        <h4><i class="fas fa-bullseye" style="color:#333333"></i> Doel van deze stap</h4>
        <p>Wek interesse en plan een gesprek of demo in.</p>

        <div class="script-box">
            <p>"Goedemorgen/middag, u spreekt met [NAAM]. Ik bel namens GlamourSchedule. Wij helpen salons zoals die van u om meer klanten te krijgen en minder no-shows te hebben. Heeft u even 2 minuutjes?"</p>
        </div>

        <div class="tip-box">
            <strong><i class="fas fa-lightbulb"></i> Pro Tip</strong>
            <p>Bel tussen 10:00-11:30 of 14:00-16:00. Vermijd maandagochtend en vrijdagmiddag - dan zijn salons het drukst.</p>
        </div>

        <h4><i class="fas fa-tasks" style="color:#333333"></i> Checklist</h4>
        <ul class="checklist">
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span>Onderzoek de salon vooraf (Instagram, website, reviews)</span>
            </li>
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span>Noteer de naam van de eigenaar</span>
            </li>
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span>Bereid een persoonlijk compliment voor</span>
            </li>
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span>Heb je referral link klaarstaan</span>
            </li>
        </ul>

        <div class="btn-nav">
            <div></div>
            <button class="btn-step btn-next" onclick="nextStep()">
                Volgende <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 2: Introduction -->
    <div class="step-content" id="step2">
        <div class="step-header">
            <div class="step-header-icon"><i class="fas fa-handshake"></i></div>
            <div>
                <h2>Stap 2: Introductie & Behoeften</h2>
                <p>Ontdek de pijnpunten van de klant</p>
            </div>
        </div>

        <h4><i class="fas fa-question-circle" style="color:#333333"></i> Stel deze vragen</h4>

        <ul class="checklist">
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span><strong>"Hoe maken klanten nu afspraken bij u?"</strong><br><small style="color:#6b7280">Ontdek of ze telefoon, WhatsApp, of al een systeem gebruiken</small></span>
            </li>
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span><strong>"Hoeveel tijd kwijt bent u dagelijks aan het beheren van afspraken?"</strong><br><small style="color:#6b7280">Creëer bewustzijn van tijdverlies</small></span>
            </li>
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span><strong>"Heeft u last van no-shows?"</strong><br><small style="color:#6b7280">Dit is vaak een groot pijnpunt!</small></span>
            </li>
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span><strong>"Kunnen klanten 's avonds of in het weekend bij u boeken?"</strong><br><small style="color:#6b7280">Benadruk 24/7 beschikbaarheid</small></span>
            </li>
        </ul>

        <div class="script-box">
            <p>"Ik hoor dat u [PIJNPUNT] heeft. Dat is precies waarom veel salons overstappen naar GlamourSchedule. Mag ik u laten zien hoe wij dat oplossen?"</p>
        </div>

        <div class="tip-box">
            <strong><i class="fas fa-lightbulb"></i> Luister actief!</strong>
            <p>Laat de klant praten. Hoe meer ze vertellen over hun problemen, hoe makkelijker je de oplossing kunt presenteren.</p>
        </div>

        <div class="btn-nav">
            <button class="btn-step btn-prev" onclick="prevStep()">
                <i class="fas fa-arrow-left"></i> Vorige
            </button>
            <button class="btn-step btn-next" onclick="nextStep()">
                Volgende <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 3: Benefits -->
    <div class="step-content" id="step3">
        <div class="step-header">
            <div class="step-header-icon"><i class="fas fa-star"></i></div>
            <div>
                <h2>Stap 3: Voordelen Presenteren</h2>
                <p>Laat zien wat GlamourSchedule biedt</p>
            </div>
        </div>

        <div class="highlight-box">
            <h3><i class="fas fa-gift"></i> Exclusieve Aanbieding</h3>
            <p>14 dagen GRATIS + €25 welkomstkorting via jouw link!</p>
        </div>

        <h4><i class="fas fa-check-circle" style="color:#333333"></i> Belangrijkste Voordelen</h4>

        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-clock"></i>
                <span><strong>24/7 Online Boeken</strong><br><small>Klanten boeken wanneer het hen uitkomt</small></span>
            </div>
            <div class="feature-item">
                <i class="fas fa-bell"></i>
                <span><strong>Automatische Herinneringen</strong><br><small>Tot 80% minder no-shows</small></span>
            </div>
            <div class="feature-item">
                <i class="fas fa-credit-card"></i>
                <span><strong>iDEAL Betalingen</strong><br><small>Direct betaald krijgen</small></span>
            </div>
            <div class="feature-item">
                <i class="fas fa-mobile-alt"></i>
                <span><strong>Eigen App-pagina</strong><br><small>Professionele uitstraling</small></span>
            </div>
            <div class="feature-item">
                <i class="fas fa-chart-line"></i>
                <span><strong>Inzichten & Statistieken</strong><br><small>Zie wat werkt</small></span>
            </div>
            <div class="feature-item">
                <i class="fas fa-euro-sign"></i>
                <span><strong>Geen Transactiekosten</strong><br><small>Vast maandbedrag</small></span>
            </div>
        </div>

        <div class="script-box">
            <p>"Stel je voor: het is zondagavond, jij bent lekker thuis, en ondertussen boeken klanten hun afspraken via hun telefoon. Maandagochtend heb je een volle agenda zonder dat je iets hoefde te doen. Dat is GlamourSchedule."</p>
        </div>

        <div class="tip-box">
            <strong><i class="fas fa-lightbulb"></i> Focus op hun pijnpunt</strong>
            <p>Heeft de klant last van no-shows? Benadruk de automatische herinneringen. Zijn ze veel tijd kwijt aan de telefoon? Focus op 24/7 online boeken.</p>
        </div>

        <div class="btn-nav">
            <button class="btn-step btn-prev" onclick="prevStep()">
                <i class="fas fa-arrow-left"></i> Vorige
            </button>
            <button class="btn-step btn-next" onclick="nextStep()">
                Volgende <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 4: Objections -->
    <div class="step-content" id="step4">
        <div class="step-header">
            <div class="step-header-icon"><i class="fas fa-comments"></i></div>
            <div>
                <h2>Stap 4: Bezwaren Weerleggen</h2>
                <p>Veelvoorkomende twijfels en hoe je ze oplost</p>
            </div>
        </div>

        <div class="objection-card">
            <p class="question"><i class="fas fa-user-clock"></i> "Ik heb geen tijd om dit op te zetten"</p>
            <p class="answer">"Begrijp ik helemaal! Daarom duurt het opzetten maar 15 minuten. En daarna bespaar je elke dag tijd doordat klanten zelf boeken. Hoeveel tijd bent u nu dagelijks kwijt aan de telefoon?"</p>
        </div>

        <div class="objection-card">
            <p class="question"><i class="fas fa-euro-sign"></i> "Het is te duur"</p>
            <p class="answer">"Ik snap dat kosten belangrijk zijn. Maar reken eens mee: hoeveel kost één no-show u? Met onze herinneringen voorkom je minstens 2-3 no-shows per maand. Dat verdient zichzelf terug. Plus: de eerste 14 dagen zijn gratis!"</p>
        </div>

        <div class="objection-card">
            <p class="question"><i class="fas fa-laptop"></i> "Ik ben niet zo technisch"</p>
            <p class="answer">"Geen probleem! Als je een smartphone kunt gebruiken, kun je GlamourSchedule gebruiken. Het is ontworpen voor salon-eigenaren, niet voor IT'ers. En onze klantenservice helpt je graag."</p>
        </div>

        <div class="objection-card">
            <p class="question"><i class="fas fa-users"></i> "Mijn klanten boeken liever telefonisch"</p>
            <p class="answer">"Dat denken veel salon-eigenaren! Maar wist je dat 70% van de boekingen buiten werktijden binnenkomt? Klanten willen 's avonds op de bank boeken. Je verliest nu potentiële klanten."</p>
        </div>

        <div class="objection-card">
            <p class="question"><i class="fas fa-calendar"></i> "Ik gebruik al iets anders"</p>
            <p class="answer">"Oh interessant! Wat gebruikt u nu? [LUISTER] En bent u daar helemaal tevreden mee? Veel salons stappen over omdat GlamourSchedule makkelijker is en beter werkt op mobiel. Probeer het 14 dagen gratis naast uw huidige systeem."</p>
        </div>

        <div class="tip-box">
            <strong><i class="fas fa-lightbulb"></i> Gouden Regel</strong>
            <p>Erken altijd eerst het bezwaar ("Ik begrijp het...") voordat je het weerlegt. Dit voorkomt dat de klant zich aangevallen voelt.</p>
        </div>

        <div class="btn-nav">
            <button class="btn-step btn-prev" onclick="prevStep()">
                <i class="fas fa-arrow-left"></i> Vorige
            </button>
            <button class="btn-step btn-next" onclick="nextStep()">
                Volgende <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 5: Close -->
    <div class="step-content" id="step5">
        <div class="step-header">
            <div class="step-header-icon"><i class="fas fa-trophy"></i></div>
            <div>
                <h2>Stap 5: De Sale Afsluiten</h2>
                <p>Sluit de deal en help de klant starten</p>
            </div>
        </div>

        <div class="highlight-box">
            <h3><i class="fas fa-gift"></i> Jouw Commissie</h3>
            <p>€99,99 voor elke succesvolle aanmelding!</p>
        </div>

        <h4><i class="fas fa-handshake" style="color:#333333"></i> Afsluit Technieken</h4>

        <div class="script-box">
            <p><strong>Directe Afsluiting:</strong><br>"Zullen we het gewoon proberen? Ik stuur je nu de link en over 5 minuten ben je klaar."</p>
        </div>

        <div class="script-box">
            <p><strong>Alternatieve Afsluiting:</strong><br>"Wil je vandaag starten of is morgenochtend handiger? Dan stuur ik je de link via WhatsApp."</p>
        </div>

        <div class="script-box">
            <p><strong>Urgentie Afsluiting:</strong><br>"De €25 welkomstkorting is een tijdelijke actie. Als je nu registreert, profiteer je ervan."</p>
        </div>

        <h4><i class="fas fa-paper-plane" style="color:#333333"></i> Na het "Ja"</h4>

        <ul class="checklist">
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span><strong>Stuur direct je referral link</strong><br>
                <code style="background:#f5f5f5;padding:0.25rem 0.5rem;border-radius:4px;font-size:0.85rem">glamourschedule.nl/business/register?ref=<?= htmlspecialchars($salesUser['referral_code'] ?? 'CODE') ?></code></span>
            </li>
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span>Blijf aan de lijn terwijl ze registreren (indien mogelijk)</span>
            </li>
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span>Bevestig dat ze de welkomstmail hebben ontvangen</span>
            </li>
            <li>
                <div class="check" onclick="toggleCheck(this)"></div>
                <span>Plan een follow-up over 3 dagen om te checken hoe het gaat</span>
            </li>
        </ul>

        <div class="tip-box">
            <strong><i class="fas fa-lightbulb"></i> Bij twijfel</strong>
            <p>Als ze nog twijfelen: "Wat houdt je tegen? Het is 14 dagen gratis, je kunt altijd stoppen. Je hebt letterlijk niets te verliezen."</p>
        </div>

        <div style="background:#ffffff;border-radius:12px;padding:1.5rem;margin-top:1.5rem;text-align:center">
            <h3 style="margin:0 0 0.5rem;color:#000000"><i class="fas fa-check-circle"></i> Gefeliciteerd!</h3>
            <p style="margin:0;color:#000000">Je hebt alle stappen doorlopen. Veel succes met je volgende sale!</p>
            <button onclick="copyReferralLink()" style="margin-top:1rem;padding:0.75rem 1.5rem;background:#333333;color:white;border:none;border-radius:8px;font-weight:600;cursor:pointer">
                <i class="fas fa-copy"></i> Kopieer je Referral Link
            </button>
        </div>

        <div class="btn-nav">
            <button class="btn-step btn-prev" onclick="prevStep()">
                <i class="fas fa-arrow-left"></i> Vorige
            </button>
            <a href="/sales/dashboard" class="btn-step btn-next" style="text-decoration:none">
                <i class="fas fa-home"></i> Terug naar Dashboard
            </a>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 5;

function goToStep(step) {
    if (step < 1 || step > totalSteps) return;

    // Update current step
    currentStep = step;

    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        indicator.classList.remove('active', 'completed');
        if (index + 1 < step) {
            indicator.classList.add('completed');
        } else if (index + 1 === step) {
            indicator.classList.add('active');
        }
    });

    // Show correct content
    document.querySelectorAll('.step-content').forEach((content, index) => {
        content.classList.remove('active');
        if (index + 1 === step) {
            content.classList.add('active');
        }
    });

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function nextStep() {
    if (currentStep < totalSteps) {
        goToStep(currentStep + 1);
    }
}

function prevStep() {
    if (currentStep > 1) {
        goToStep(currentStep - 1);
    }
}

function toggleCheck(element) {
    element.classList.toggle('checked');
    if (element.classList.contains('checked')) {
        element.innerHTML = '<i class="fas fa-check"></i>';
    } else {
        element.innerHTML = '';
    }
}

function copyReferralLink() {
    const link = 'https://glamourschedule.nl/business/register?ref=<?= htmlspecialchars($salesUser['referral_code'] ?? '') ?>';
    navigator.clipboard.writeText(link).then(() => {
        alert('Referral link gekopieerd!\n\n' + link);
    });
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/sales.php'; ?>

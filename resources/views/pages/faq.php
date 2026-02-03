<?php ob_start(); ?>

<style>
    .faq-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    .faq-card {
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid var(--card-border);
    }
    .faq-header {
        background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
        color: white;
        padding: 2.5rem 2rem;
        text-align: center;
    }
    .faq-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
    .faq-header p {
        margin-top: 0.5rem;
        opacity: 0.9;
    }
    .faq-body {
        padding: 2rem;
    }
    .faq-section {
        margin-bottom: 2rem;
    }
    .faq-section h2 {
        color: var(--text-primary);
        font-size: 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .faq-section h2 i {
        color: var(--business-primary, #635bff);
    }
    .faq-item {
        background: var(--input-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        margin-bottom: 0.75rem;
        overflow: hidden;
    }
    .faq-question {
        padding: 1rem 1.25rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: var(--text-primary);
        transition: background 0.2s;
    }
    .faq-question:hover {
        background: var(--service-hover);
    }
    .faq-question i {
        transition: transform 0.3s;
        color: var(--text-secondary);
    }
    .faq-item.open .faq-question i {
        transform: rotate(180deg);
    }
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    .faq-item.open .faq-answer {
        max-height: 500px;
    }
    .faq-answer-content {
        padding: 0 1.25rem 1.25rem;
        color: var(--text-secondary);
        line-height: 1.7;
    }
    .faq-contact {
        background: linear-gradient(135deg, #635bff 0%, #8b5cf6 100%);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        color: white;
        margin-top: 2rem;
    }
    .faq-contact h3 {
        margin: 0 0 0.5rem 0;
    }
    .faq-contact p {
        margin: 0 0 1rem 0;
        opacity: 0.9;
    }
    .faq-contact a {
        display: inline-block;
        background: white;
        color: #635bff;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: transform 0.2s;
    }
    .faq-contact a:hover {
        transform: translateY(-2px);
    }
</style>

<div class="faq-container">
    <div class="faq-card">
        <div class="faq-header">
            <h1><i class="fas fa-question-circle"></i> <?= $translations['faq_title'] ?? 'Veelgestelde Vragen' ?></h1>
            <p><?= $translations['faq_subtitle'] ?? 'Vind snel antwoord op je vragen' ?></p>
        </div>

        <div class="faq-body">
            <!-- Voor Klanten -->
            <div class="faq-section">
                <h2><i class="fas fa-user"></i> <?= $translations['faq_for_customers'] ?? 'Voor Klanten' ?></h2>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q1'] ?? 'Hoe boek ik een afspraak?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a1'] ?? 'Zoek een salon via de zoekpagina, kies een dienst en selecteer een beschikbare datum en tijd. Vul je gegevens in en bevestig de boeking. Je ontvangt een bevestigingsmail met alle details.' ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q2'] ?? 'Kan ik mijn afspraak annuleren of wijzigen?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a2'] ?? 'Ja, je kunt je afspraak tot 24 uur van tevoren kosteloos annuleren via de link in je bevestigingsmail. Bij annulering binnen 24 uur kunnen kosten in rekening worden gebracht.' ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q3'] ?? 'Hoe werkt de QR-code check-in?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a3'] ?? 'Bij aankomst in de salon scan je de QR-code om je aanwezigheid te bevestigen. Dit zorgt ervoor dat de salon weet dat je bent gearriveerd en de betaling kan worden vrijgegeven.' ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q4'] ?? 'Welke betaalmethodes worden geaccepteerd?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a4'] ?? 'We accepteren iDEAL, creditcard, Bancontact, PayPal, Apple Pay, Google Pay en meer. De beschikbare methodes kunnen per land verschillen.' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voor Salons -->
            <div class="faq-section">
                <h2><i class="fas fa-store"></i> <?= $translations['faq_for_businesses'] ?? 'Voor Salons' ?></h2>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q5'] ?? 'Hoe registreer ik mijn salon?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a5'] ?? 'Klik op "Registreren" en kies voor een zakelijk account. Vul je salongegevens in, voeg je diensten toe en je bent klaar om boekingen te ontvangen.' ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q6'] ?? 'Wat kost het om GlamourSchedule te gebruiken?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a6'] ?? 'We rekenen €1,75 per voltooide boeking. Er zijn geen maandelijkse kosten of opstartkosten. Je betaalt alleen wanneer je daadwerkelijk klanten ontvangt.' ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q7'] ?? 'Hoe ontvang ik mijn uitbetalingen?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a7'] ?? 'Koppel je Mollie of Stripe account voor automatische uitbetalingen. Het bedrag wordt automatisch gesplitst: jij ontvangt je deel direct, wij houden €1,75 platformkosten in.' ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q8'] ?? 'Kan ik mijn eigen openingstijden instellen?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a8'] ?? 'Ja, in je dashboard kun je per dag je openingstijden instellen. Je kunt ook specifieke dagen blokkeren voor vakanties of andere redenen.' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Betalingen & Veiligheid -->
            <div class="faq-section">
                <h2><i class="fas fa-shield-alt"></i> <?= $translations['faq_payments_security'] ?? 'Betalingen & Veiligheid' ?></h2>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q9'] ?? 'Is mijn betaling veilig?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a9'] ?? 'Ja, alle betalingen worden verwerkt via Mollie en Stripe, gecertificeerde betaalproviders die voldoen aan de hoogste beveiligingsstandaarden (PCI-DSS).' ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_q10'] ?? 'Hoe worden mijn gegevens beschermd?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?= $translations['faq_a10'] ?? 'We gebruiken SSL-encryptie voor alle communicatie. Je gegevens worden opgeslagen volgens de AVG/GDPR-richtlijnen. Lees ons privacybeleid voor meer informatie.' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="faq-contact">
                <h3><i class="fas fa-headset"></i> <?= $translations['faq_still_questions'] ?? 'Nog vragen?' ?></h3>
                <p><?= $translations['faq_contact_text'] ?? 'Ons supportteam helpt je graag verder' ?></p>
                <a href="/contact"><i class="fas fa-envelope"></i> <?= $translations['contact_us'] ?? 'Neem contact op' ?></a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFaq(element) {
    const item = element.parentElement;
    item.classList.toggle('open');
}

// Open first item by default
document.addEventListener('DOMContentLoaded', function() {
    const firstItem = document.querySelector('.faq-item');
    if (firstItem) firstItem.classList.add('open');
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

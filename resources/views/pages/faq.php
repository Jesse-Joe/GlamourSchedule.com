<?php ob_start(); ?>

<style>
    .faq-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    .faq-card {
        background: var(--white);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
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
        padding: 2.5rem;
    }
    .faq-section {
        margin-bottom: 2rem;
    }
    .faq-section-title {
        color: #000000;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f5f5f5;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .faq-section-title i {
        color: #000000;
    }
    .faq-item {
        background: #fafafa;
        border-radius: 12px;
        margin-bottom: 1rem;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    .faq-item:hover {
        border-color: #000000;
    }
    .faq-question {
        padding: 1.25rem 1.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: #374151;
    }
    .faq-question:hover {
        background: #f5f5f5;
    }
    .faq-question i {
        transition: transform 0.3s ease;
        color: #6b7280;
    }
    .faq-item.active .faq-question i {
        transform: rotate(180deg);
    }
    .faq-answer {
        display: none;
        padding: 0 1.5rem 1.25rem;
        color: #4b5563;
        line-height: 1.7;
    }
    .faq-item.active .faq-answer {
        display: block;
    }
    .faq-answer ul {
        margin: 0.5rem 0;
        padding-left: 1.5rem;
    }
    .faq-answer li {
        margin-bottom: 0.5rem;
    }
    .faq-contact {
        background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        margin-top: 2rem;
    }
    .faq-contact h3 {
        color: white;
        margin: 0 0 0.5rem;
        font-size: 1.25rem;
    }
    .faq-contact p {
        color: rgba(255,255,255,0.8);
        margin: 0 0 1.5rem;
    }
    .faq-contact-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        color: #000000;
        padding: 0.875rem 1.75rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .faq-contact-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    @media (max-width: 768px) {
        .faq-header h1 {
            font-size: 1.5rem;
        }
        .faq-body {
            padding: 1.5rem;
        }
        .faq-question {
            padding: 1rem 1.25rem;
            font-size: 0.95rem;
        }
    }
</style>

<div class="faq-container">
    <div class="faq-card">
        <div class="faq-header">
            <h1><?= $lang === 'nl' ? 'Veelgestelde Vragen' : 'Frequently Asked Questions' ?></h1>
            <p><?= $lang === 'nl' ? 'Vind snel antwoorden op je vragen' : 'Find quick answers to your questions' ?></p>
        </div>

        <div class="faq-body">
            <!-- Voor Klanten -->
            <div class="faq-section">
                <h2 class="faq-section-title">
                    <i class="fas fa-user"></i>
                    <?= $lang === 'nl' ? 'Voor Klanten' : 'For Customers' ?>
                </h2>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Hoe boek ik een afspraak?' : 'How do I book an appointment?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>Een afspraak boeken is eenvoudig:</p>
                        <ul>
                            <li>Zoek een salon via onze zoekpagina</li>
                            <li>Kies een dienst en selecteer een beschikbare tijd</li>
                            <li>Vul je gegevens in en bevestig de boeking</li>
                            <li>Je ontvangt een bevestigingsmail met alle details</li>
                        </ul>
                        <?php else: ?>
                        <p>Booking an appointment is easy:</p>
                        <ul>
                            <li>Search for a salon via our search page</li>
                            <li>Choose a service and select an available time</li>
                            <li>Fill in your details and confirm the booking</li>
                            <li>You will receive a confirmation email with all details</li>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Kan ik mijn afspraak annuleren of wijzigen?' : 'Can I cancel or change my appointment?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>Ja, je kunt je afspraak tot 24 uur voor de geplande tijd kosteloos annuleren of wijzigen. Gebruik de link in je bevestigingsmail of log in op je account. Bij annulering binnen 24 uur kunnen annuleringskosten in rekening worden gebracht.</p>
                        <?php else: ?>
                        <p>Yes, you can cancel or change your appointment free of charge up to 24 hours before the scheduled time. Use the link in your confirmation email or log in to your account. Cancellation fees may apply for cancellations within 24 hours.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Welke betaalmethodes worden geaccepteerd?' : 'What payment methods are accepted?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>Wij accepteren diverse betaalmethodes via onze betalingspartner Mollie:</p>
                        <ul>
                            <li>iDEAL</li>
                            <li>Creditcard (Visa, Mastercard, American Express)</li>
                            <li>Apple Pay & Google Pay</li>
                            <li>Bancontact</li>
                            <li>Contant betalen bij de salon (indien aangeboden)</li>
                        </ul>
                        <?php else: ?>
                        <p>We accept various payment methods via our payment partner Mollie:</p>
                        <ul>
                            <li>iDEAL</li>
                            <li>Credit card (Visa, Mastercard, American Express)</li>
                            <li>Apple Pay & Google Pay</li>
                            <li>Bancontact</li>
                            <li>Cash payment at the salon (if offered)</li>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Wat is de QR-code in mijn bevestiging?' : 'What is the QR code in my confirmation?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>De QR-code dient als digitale bevestiging van je boeking. Toon deze bij aankomst in de salon voor een snelle check-in. De medewerker scant de code en ziet direct je afspraakgegevens.</p>
                        <?php else: ?>
                        <p>The QR code serves as digital confirmation of your booking. Show it upon arrival at the salon for quick check-in. The staff will scan the code and immediately see your appointment details.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Voor Salons -->
            <div class="faq-section">
                <h2 class="faq-section-title">
                    <i class="fas fa-store"></i>
                    <?= $lang === 'nl' ? 'Voor Salons' : 'For Salons' ?>
                </h2>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Wat kost het om mijn salon te registreren?' : 'What does it cost to register my salon?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>De registratie kost eenmalig slechts &euro;0,99. Daarna betaal je alleen een klein percentage per boeking. Er zijn geen maandelijkse abonnementskosten.</p>
                        <?php else: ?>
                        <p>Registration costs only &euro;0.99 one-time. After that, you only pay a small percentage per booking. There are no monthly subscription fees.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Hoe ontvang ik mijn betalingen?' : 'How do I receive my payments?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>Betalingen worden wekelijks automatisch naar je bankrekening overgemaakt via Mollie. Je kunt je uitbetalingsoverzicht bekijken in je dashboard onder "Uitbetalingen".</p>
                        <?php else: ?>
                        <p>Payments are automatically transferred to your bank account weekly via Mollie. You can view your payout overview in your dashboard under "Payouts".</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Kan ik meerdere medewerkers toevoegen?' : 'Can I add multiple employees?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>Ja, je kunt onbeperkt medewerkers toevoegen aan je salon. Elke medewerker kan eigen diensten, beschikbaarheid en werktijden hebben. Beheer dit via je dashboard onder "Medewerkers".</p>
                        <?php else: ?>
                        <p>Yes, you can add unlimited employees to your salon. Each employee can have their own services, availability, and working hours. Manage this via your dashboard under "Employees".</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Hoe verhoog ik mijn zichtbaarheid?' : 'How do I increase my visibility?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>Er zijn verschillende manieren om je zichtbaarheid te verhogen:</p>
                        <ul>
                            <li>Voeg professionele foto's toe aan je profiel</li>
                            <li>Vraag klanten om reviews achter te laten</li>
                            <li>Houd je diensten en prijzen up-to-date</li>
                            <li>Gebruik de Boost-functie voor tijdelijke extra zichtbaarheid</li>
                        </ul>
                        <?php else: ?>
                        <p>There are several ways to increase your visibility:</p>
                        <ul>
                            <li>Add professional photos to your profile</li>
                            <li>Ask customers to leave reviews</li>
                            <li>Keep your services and prices up-to-date</li>
                            <li>Use the Boost feature for temporary extra visibility</li>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Algemeen -->
            <div class="faq-section">
                <h2 class="faq-section-title">
                    <i class="fas fa-info-circle"></i>
                    <?= $lang === 'nl' ? 'Algemeen' : 'General' ?>
                </h2>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Is mijn data veilig?' : 'Is my data secure?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>Ja, wij nemen beveiliging zeer serieus. Alle gegevens worden versleuteld opgeslagen en verzonden via SSL. Wij voldoen aan de AVG/GDPR en verkopen nooit je gegevens aan derden. Lees meer in ons <a href="/privacy">privacybeleid</a>.</p>
                        <?php else: ?>
                        <p>Yes, we take security very seriously. All data is stored encrypted and transmitted via SSL. We comply with GDPR and never sell your data to third parties. Read more in our <a href="/privacy">privacy policy</a>.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <?= $lang === 'nl' ? 'Hoe kan ik contact opnemen met support?' : 'How can I contact support?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?php if ($lang === 'nl'): ?>
                        <p>Je kunt ons bereiken via het <a href="/contact">contactformulier</a> of direct via e-mail op support@glamourschedule.com. We streven ernaar binnen 24 uur te reageren.</p>
                        <?php else: ?>
                        <p>You can reach us via the <a href="/contact">contact form</a> or directly via email at support@glamourschedule.com. We aim to respond within 24 hours.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Contact CTA -->
            <div class="faq-contact">
                <h3><?= $lang === 'nl' ? 'Vraag niet beantwoord?' : 'Question not answered?' ?></h3>
                <p><?= $lang === 'nl' ? 'Neem gerust contact met ons op, we helpen je graag!' : 'Feel free to contact us, we are happy to help!' ?></p>
                <a href="/contact" class="faq-contact-btn">
                    <i class="fas fa-envelope"></i>
                    <?= $lang === 'nl' ? 'Contact opnemen' : 'Contact us' ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.faq-question').forEach(function(question) {
    question.addEventListener('click', function() {
        const item = this.closest('.faq-item');
        const wasActive = item.classList.contains('active');

        // Close all items
        document.querySelectorAll('.faq-item').forEach(function(i) {
            i.classList.remove('active');
        });

        // Toggle clicked item
        if (!wasActive) {
            item.classList.add('active');
        }
    });
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

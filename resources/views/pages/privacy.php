<?php ob_start(); ?>

<style>
    .privacy-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    .privacy-card {
        background: var(--white);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .privacy-header {
        background: linear-gradient(135deg, #000000 0%, #000000 100%);
        color: white;
        padding: 2.5rem 2rem;
        text-align: center;
    }
    .privacy-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
    .privacy-header p {
        margin-top: 0.5rem;
        opacity: 0.9;
    }
    .privacy-body {
        padding: 2.5rem;
        line-height: 1.8;
        color: var(--text);
    }
    .privacy-body h2 {
        color: #000000;
        font-size: 1.3rem;
        margin-top: 2rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f5f5f5;
    }
    .privacy-body h2:first-child {
        margin-top: 0;
    }
    .privacy-body p {
        margin-bottom: 1rem;
        color: #4b5563;
    }
    .privacy-body ul {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }
    .privacy-body li {
        margin-bottom: 0.5rem;
        color: #4b5563;
    }
    .privacy-body strong {
        color: #374151;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
    }
    .data-table th, .data-table td {
        padding: 0.75rem;
        text-align: left;
        border: 1px solid #e5e7eb;
    }
    .data-table th {
        background: #fafafa;
        font-weight: 600;
        color: #374151;
    }
    .data-table td {
        color: #4b5563;
    }
    .privacy-info {
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 1.25rem;
        margin: 1.5rem 0;
        border-left: 4px solid #000000;
    }
    .privacy-info p {
        margin: 0;
        color: #262626;
    }
    .contact-box {
        background: #fafafa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }
    .contact-box h3 {
        margin: 0 0 1rem 0;
        color: #374151;
    }
    .last-updated {
        font-size: 0.9rem;
        color: #6b7280;
        text-align: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }
    @media (max-width: 768px) {
        .privacy-header h1 {
            font-size: 1.5rem;
        }
        .privacy-body {
            padding: 1.5rem;
        }
        .data-table {
            font-size: 0.9rem;
        }
    }
</style>

<div class="privacy-container">
    <div class="privacy-card">
        <div class="privacy-header">
            <h1><?= $translations['privacy'] ?? 'Privacybeleid' ?></h1>
            <p>GlamourSchedule B.V.</p>
        </div>

        <div class="privacy-body">
            <?php if ($lang === 'nl'): ?>
            <!-- DUTCH VERSION -->
            <h2>1. Inleiding</h2>
            <p>GlamourSchedule B.V. ("wij", "ons", "onze") respecteert uw privacy en is toegewijd aan het beschermen van uw persoonsgegevens. Dit privacybeleid informeert u over hoe wij omgaan met uw persoonsgegevens wanneer u onze website bezoekt en gebruik maakt van onze diensten.</p>

            <h2>2. Wie zijn wij?</h2>
            <p>GlamourSchedule B.V. is de verwerkingsverantwoordelijke voor uw persoonsgegevens. Wij zijn een online boekingsplatform voor beauty- en wellnessdiensten, gevestigd in Nederland.</p>

            <div class="privacy-info">
                <p><strong>Contact:</strong> Voor vragen over uw privacy kunt u contact opnemen via privacy@glamourschedule.nl</p>
            </div>

            <h2>3. Welke gegevens verzamelen wij?</h2>
            <p>Wij verzamelen en verwerken de volgende categorieën persoonsgegevens:</p>

            <table class="data-table">
                <tr>
                    <th>Categorie</th>
                    <th>Voorbeelden</th>
                    <th>Doel</th>
                </tr>
                <tr>
                    <td>Accountgegevens</td>
                    <td>Naam, e-mail, telefoonnummer</td>
                    <td>Aanmaken en beheren account</td>
                </tr>
                <tr>
                    <td>Boekingsgegevens</td>
                    <td>Afspraken, voorkeuren, notities</td>
                    <td>Uitvoeren boekingen</td>
                </tr>
                <tr>
                    <td>Betalingsgegevens</td>
                    <td>Transactie-informatie</td>
                    <td>Verwerken betalingen</td>
                </tr>
                <tr>
                    <td>Technische gegevens</td>
                    <td>IP-adres, browser, apparaat</td>
                    <td>Website-optimalisatie, beveiliging</td>
                </tr>
            </table>

            <h2>4. Hoe gebruiken wij uw gegevens?</h2>
            <p>Wij gebruiken uw persoonsgegevens voor de volgende doeleinden:</p>
            <ul>
                <li>Het verwerken en beheren van uw boekingen</li>
                <li>Het verzenden van bevestigingen en herinneringen</li>
                <li>Het verbeteren van onze dienstverlening</li>
                <li>Het verstrekken van klantenservice</li>
                <li>Het voldoen aan wettelijke verplichtingen</li>
                <li>Het verzenden van nieuwsbrieven (alleen met uw toestemming)</li>
            </ul>

            <h2>5. Rechtsgronden</h2>
            <p>Wij verwerken uw gegevens op basis van:</p>
            <ul>
                <li><strong>Uitvoering overeenkomst:</strong> noodzakelijk om uw boeking te verwerken</li>
                <li><strong>Wettelijke verplichting:</strong> bijvoorbeeld voor belastingadministratie</li>
                <li><strong>Gerechtvaardigd belang:</strong> verbetering dienstverlening, beveiliging</li>
                <li><strong>Toestemming:</strong> voor marketing en nieuwsbrieven</li>
            </ul>

            <h2>6. Delen van gegevens</h2>
            <p>Wij delen uw gegevens alleen met:</p>
            <ul>
                <li><strong>Dienstverleners (salons):</strong> noodzakelijke gegevens voor uw afspraak</li>
                <li><strong>Betalingsverwerkers:</strong> Mollie B.V. voor betalingsverwerking</li>
                <li><strong>IT-dienstverleners:</strong> hosting en e-maildiensten</li>
                <li><strong>Overheidsinstanties:</strong> indien wettelijk verplicht</li>
            </ul>
            <p>Wij verkopen uw gegevens nooit aan derden.</p>

            <h2>7. Bewaartermijnen</h2>
            <p>Wij bewaren uw gegevens niet langer dan noodzakelijk:</p>
            <ul>
                <li>Accountgegevens: tot 2 jaar na laatste activiteit</li>
                <li>Boekingsgegevens: 7 jaar (wettelijke verplichting)</li>
                <li>Technische logs: maximaal 12 maanden</li>
            </ul>

            <h2>8. Uw rechten</h2>
            <p>Onder de AVG heeft u de volgende rechten:</p>
            <ul>
                <li><strong>Inzage:</strong> opvragen welke gegevens wij van u hebben</li>
                <li><strong>Rectificatie:</strong> corrigeren van onjuiste gegevens</li>
                <li><strong>Verwijdering:</strong> verzoeken om verwijdering van uw gegevens</li>
                <li><strong>Beperking:</strong> beperken van de verwerking</li>
                <li><strong>Dataportabiliteit:</strong> overdracht van uw gegevens</li>
                <li><strong>Bezwaar:</strong> bezwaar maken tegen verwerking</li>
            </ul>
            <p>U kunt uw rechten uitoefenen door contact op te nemen via privacy@glamourschedule.nl.</p>

            <h2>9. Beveiliging</h2>
            <p>Wij nemen passende technische en organisatorische maatregelen om uw gegevens te beschermen, waaronder:</p>
            <ul>
                <li>SSL-encryptie voor alle gegevensoverdracht</li>
                <li>Beveiligde servers in Nederland/EU</li>
                <li>Toegangscontrole en authenticatie</li>
                <li>Regelmatige beveiligingsaudits</li>
            </ul>

            <h2>10. Cookies</h2>
            <p>Onze website gebruikt cookies voor:</p>
            <ul>
                <li>Functionele cookies: noodzakelijk voor werking website</li>
                <li>Analytische cookies: inzicht in websitegebruik</li>
                <li>Voorkeurscookies: onthouden van uw taalinstellingen</li>
            </ul>

            <h2>11. Klachten</h2>
            <p>Als u een klacht heeft over hoe wij met uw gegevens omgaan, kunt u contact opnemen met ons of een klacht indienen bij de Autoriteit Persoonsgegevens (autoriteitpersoonsgegevens.nl).</p>

            <div class="contact-box">
                <h3>Contact</h3>
                <p>
                    <strong>GlamourSchedule B.V.</strong><br>
                    E-mail: privacy@glamourschedule.nl<br>
                    Website: www.glamourschedule.nl
                </p>
            </div>

            <?php else: ?>
            <!-- ENGLISH VERSION -->
            <h2>1. Introduction</h2>
            <p>GlamourSchedule B.V. ("we", "us", "our") respects your privacy and is committed to protecting your personal data. This privacy policy informs you about how we handle your personal data when you visit our website and use our services.</p>

            <h2>2. Who are we?</h2>
            <p>GlamourSchedule B.V. is the data controller for your personal data. We are an online booking platform for beauty and wellness services, based in the Netherlands.</p>

            <div class="privacy-info">
                <p><strong>Contact:</strong> For questions about your privacy, you can contact us at privacy@glamourschedule.nl</p>
            </div>

            <h2>3. What data do we collect?</h2>
            <p>We collect and process the following categories of personal data:</p>

            <table class="data-table">
                <tr>
                    <th>Category</th>
                    <th>Examples</th>
                    <th>Purpose</th>
                </tr>
                <tr>
                    <td>Account data</td>
                    <td>Name, email, phone number</td>
                    <td>Creating and managing account</td>
                </tr>
                <tr>
                    <td>Booking data</td>
                    <td>Appointments, preferences, notes</td>
                    <td>Processing bookings</td>
                </tr>
                <tr>
                    <td>Payment data</td>
                    <td>Transaction information</td>
                    <td>Processing payments</td>
                </tr>
                <tr>
                    <td>Technical data</td>
                    <td>IP address, browser, device</td>
                    <td>Website optimization, security</td>
                </tr>
            </table>

            <h2>4. How do we use your data?</h2>
            <p>We use your personal data for the following purposes:</p>
            <ul>
                <li>Processing and managing your bookings</li>
                <li>Sending confirmations and reminders</li>
                <li>Improving our services</li>
                <li>Providing customer service</li>
                <li>Complying with legal obligations</li>
                <li>Sending newsletters (only with your consent)</li>
            </ul>

            <h2>5. Legal bases</h2>
            <p>We process your data based on:</p>
            <ul>
                <li><strong>Contract performance:</strong> necessary to process your booking</li>
                <li><strong>Legal obligation:</strong> for example for tax administration</li>
                <li><strong>Legitimate interest:</strong> improving services, security</li>
                <li><strong>Consent:</strong> for marketing and newsletters</li>
            </ul>

            <h2>6. Sharing data</h2>
            <p>We only share your data with:</p>
            <ul>
                <li><strong>Service providers (salons):</strong> necessary data for your appointment</li>
                <li><strong>Payment processors:</strong> Mollie B.V. for payment processing</li>
                <li><strong>IT service providers:</strong> hosting and email services</li>
                <li><strong>Government authorities:</strong> if legally required</li>
            </ul>
            <p>We never sell your data to third parties.</p>

            <h2>7. Retention periods</h2>
            <p>We do not keep your data longer than necessary:</p>
            <ul>
                <li>Account data: up to 2 years after last activity</li>
                <li>Booking data: 7 years (legal obligation)</li>
                <li>Technical logs: maximum 12 months</li>
            </ul>

            <h2>8. Your rights</h2>
            <p>Under GDPR, you have the following rights:</p>
            <ul>
                <li><strong>Access:</strong> request what data we have about you</li>
                <li><strong>Rectification:</strong> correct inaccurate data</li>
                <li><strong>Erasure:</strong> request deletion of your data</li>
                <li><strong>Restriction:</strong> restrict processing</li>
                <li><strong>Data portability:</strong> transfer of your data</li>
                <li><strong>Objection:</strong> object to processing</li>
            </ul>
            <p>You can exercise your rights by contacting privacy@glamourschedule.nl.</p>

            <h2>9. Security</h2>
            <p>We take appropriate technical and organizational measures to protect your data, including:</p>
            <ul>
                <li>SSL encryption for all data transfers</li>
                <li>Secure servers in the Netherlands/EU</li>
                <li>Access control and authentication</li>
                <li>Regular security audits</li>
            </ul>

            <h2>10. Cookies</h2>
            <p>Our website uses cookies for:</p>
            <ul>
                <li>Functional cookies: necessary for website operation</li>
                <li>Analytical cookies: insight into website usage</li>
                <li>Preference cookies: remembering your language settings</li>
            </ul>

            <h2>11. Complaints</h2>
            <p>If you have a complaint about how we handle your data, you can contact us or file a complaint with the Dutch Data Protection Authority (autoriteitpersoonsgegevens.nl).</p>

            <div class="contact-box">
                <h3>Contact</h3>
                <p>
                    <strong>GlamourSchedule B.V.</strong><br>
                    Email: privacy@glamourschedule.nl<br>
                    Website: www.glamourschedule.nl
                </p>
            </div>
            <?php endif; ?>

            <p class="last-updated">
                <?= $lang === 'nl' ? 'Laatst bijgewerkt: januari 2025' : 'Last updated: January 2025' ?>
            </p>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

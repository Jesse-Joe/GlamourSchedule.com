<?php ob_start(); ?>

<style>
    .terms-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    .terms-card {
        background: var(--white);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .terms-header {
        background: linear-gradient(135deg, #000000 0%, #000000 30%, #000000 70%, #333333 100%);
        color: white;
        padding: 2.5rem 2rem;
        text-align: center;
    }
    .terms-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
    .terms-header p {
        margin-top: 0.5rem;
        opacity: 0.9;
    }
    .terms-body {
        padding: 2.5rem;
        line-height: 1.8;
        color: var(--text);
    }
    .terms-body h2 {
        color: #000000;
        font-size: 1.3rem;
        margin-top: 2rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f5f5f5;
    }
    .terms-body h2:first-child {
        margin-top: 0;
    }
    .terms-body h3 {
        font-size: 1.1rem;
        color: #374151;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .terms-body p {
        margin-bottom: 1rem;
        color: #4b5563;
    }
    .terms-body ul, .terms-body ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }
    .terms-body li {
        margin-bottom: 0.5rem;
        color: #4b5563;
    }
    .terms-body strong {
        color: #374151;
    }
    .terms-toc {
        background: #fafafa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .terms-toc h3 {
        margin: 0 0 1rem 0;
        font-size: 1rem;
        color: #374151;
    }
    .terms-toc ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .terms-toc li {
        margin-bottom: 0.5rem;
    }
    .terms-toc a {
        color: #000000;
        text-decoration: none;
        font-size: 0.95rem;
    }
    .terms-toc a:hover {
        text-decoration: underline;
    }
    .terms-info {
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 1.25rem;
        margin: 1.5rem 0;
        border-left: 4px solid #000000;
    }
    .terms-info p {
        margin: 0;
        color: #262626;
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
        .terms-header h1 {
            font-size: 1.5rem;
        }
        .terms-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="terms-container">
    <div class="terms-card">
        <div class="terms-header">
            <h1><?= $translations['terms'] ?? 'Algemene Voorwaarden' ?></h1>
            <p>GlamourSchedule B.V.</p>
        </div>

        <div class="terms-body">
            <?php if ($lang === 'nl'): ?>
            <!-- DUTCH VERSION -->
            <div class="terms-toc">
                <h3>Inhoudsopgave</h3>
                <ul>
                    <li><a href="#artikel-1">Artikel 1 - Definities</a></li>
                    <li><a href="#artikel-2">Artikel 2 - Toepasselijkheid</a></li>
                    <li><a href="#artikel-3">Artikel 3 - Dienstverlening</a></li>
                    <li><a href="#artikel-4">Artikel 4 - Boekingen</a></li>
                    <li><a href="#artikel-5">Artikel 5 - Betaling</a></li>
                    <li><a href="#artikel-6">Artikel 6 - Annulering</a></li>
                    <li><a href="#artikel-7">Artikel 7 - Aansprakelijkheid</a></li>
                    <li><a href="#artikel-8">Artikel 8 - Privacy</a></li>
                    <li><a href="#artikel-8a">Artikel 8a - Cookies & Advertenties</a></li>
                    <li><a href="#artikel-9">Artikel 9 - Klachten</a></li>
                    <li><a href="#artikel-10">Artikel 10 - Slotbepalingen</a></li>
                </ul>
            </div>

            <h2 id="artikel-1">Artikel 1 - Definities</h2>
            <p>In deze algemene voorwaarden wordt verstaan onder:</p>
            <ol>
                <li><strong>GlamourSchedule:</strong> GlamourSchedule B.V., gevestigd te Nederland, KVK-nummer 81973667, hierna te noemen "Platform".</li>
                <li><strong>Klant:</strong> De natuurlijke persoon of rechtspersoon die via het Platform een dienst boekt bij een Dienstverlener.</li>
                <li><strong>Dienstverlener:</strong> De ondernemer (salon, beautysalon, kapper, etc.) die zijn of haar diensten aanbiedt via het Platform.</li>
                <li><strong>Boeking:</strong> Een via het Platform gemaakte afspraak tussen Klant en Dienstverlener.</li>
                <li><strong>Dienst:</strong> De beauty- of wellnessbehandeling die door de Dienstverlener wordt uitgevoerd.</li>
            </ol>

            <h2 id="artikel-2">Artikel 2 - Toepasselijkheid</h2>
            <p>2.1 Deze algemene voorwaarden zijn van toepassing op alle diensten aangeboden door GlamourSchedule en op alle boekingen die via het Platform worden gemaakt.</p>
            <p>2.2 Door gebruik te maken van het Platform of door een boeking te plaatsen, accepteert u deze algemene voorwaarden.</p>
            <p>2.3 Afwijkingen van deze voorwaarden zijn alleen geldig indien schriftelijk overeengekomen.</p>

            <h2 id="artikel-3">Artikel 3 - Dienstverlening</h2>
            <p>3.1 GlamourSchedule is een bemiddelingsplatform dat Klanten en Dienstverleners met elkaar in contact brengt.</p>
            <p>3.2 GlamourSchedule is geen partij bij de overeenkomst tussen Klant en Dienstverlener. De Dienstverlener is volledig verantwoordelijk voor de uitvoering van de geboekte dienst.</p>
            <p>3.3 GlamourSchedule spant zich in om de informatie op het Platform zo actueel en accuraat mogelijk te houden, maar garandeert niet de juistheid, volledigheid of actualiteit van de verstrekte informatie.</p>

            <h2 id="artikel-4">Artikel 4 - Boekingen</h2>
            <p>4.1 Een boeking komt tot stand zodra de Klant de boeking via het Platform heeft bevestigd en de betaling is voltooid.</p>
            <p>4.2 Na het voltooien van een boeking ontvangt de Klant een bevestigingsmail met alle relevante gegevens en een QR-code die bij aankomst kan worden getoond.</p>
            <p>4.3 De Klant dient tijdig (minimaal 5 minuten voor aanvang) aanwezig te zijn op de afgesproken locatie.</p>
            <p>4.4 Bij no-show kan de Dienstverlener de volledige prijs in rekening brengen.</p>

            <div class="terms-info">
                <p><strong>Tip:</strong> U ontvangt 24 uur voor uw afspraak automatisch een herinnering per e-mail.</p>
            </div>

            <h2 id="artikel-5">Artikel 5 - Betaling</h2>
            <p>5.1 Betalingen geschieden via het beveiligde betaalsysteem van Mollie. GlamourSchedule accepteert de gangbare Nederlandse betaalmethoden waaronder iDEAL, creditcard en bankoverschrijving.</p>
            <p>5.2 De prijs van de dienst wordt bij het boeken duidelijk vermeld. De Klant betaalt het volledige bedrag van de behandeling zonder extra kosten.</p>
            <p>5.3 Per voltooide boeking wordt een administratieve fee van €1,75 ingehouden op de uitbetaling aan de Dienstverlener. Deze kosten worden niet doorberekend aan de Klant.</p>
            <p>5.4 De Dienstverlener ontvangt de betaling (minus de administratieve fee van €1,75 per boeking) binnen 14 dagen na voltooiing van de dienst.</p>

            <h2 id="artikel-6">Artikel 6 - Annulering</h2>
            <p>6.1 Annulering door de Klant is kosteloos mogelijk tot 24 uur voor aanvang van de afspraak.</p>
            <p>6.2 Bij annulering binnen 24 uur voor aanvang wordt 50% van de totaalprijs in rekening gebracht.</p>
            <p>6.3 De Dienstverlener kan een afspraak annuleren in geval van overmacht. In dit geval ontvangt de Klant volledige restitutie.</p>
            <p>6.4 Restitutie geschiedt binnen 5-10 werkdagen via dezelfde betaalmethode als de originele betaling.</p>

            <h2 id="artikel-7">Artikel 7 - Aansprakelijkheid</h2>
            <p>7.1 GlamourSchedule is niet aansprakelijk voor schade voortvloeiend uit de dienstverlening door de Dienstverlener.</p>
            <p>7.2 GlamourSchedule is niet aansprakelijk voor indirecte schade, gevolgschade, gederfde winst of gemiste besparingen.</p>
            <p>7.3 De aansprakelijkheid van GlamourSchedule is in alle gevallen beperkt tot het bedrag dat de Klant voor de betreffende boeking heeft betaald.</p>
            <p>7.4 Klachten over de uitvoering van de dienst dienen direct bij de Dienstverlener te worden ingediend.</p>

            <h2 id="artikel-8">Artikel 8 - Privacy</h2>
            <p>8.1 GlamourSchedule verwerkt persoonsgegevens in overeenstemming met de Algemene Verordening Gegevensbescherming (AVG).</p>
            <p>8.2 Persoonsgegevens worden uitsluitend gebruikt voor het uitvoeren van boekingen, communicatie en het verbeteren van onze dienstverlening.</p>
            <p>8.3 Voor meer informatie verwijzen wij naar ons <a href="/privacy" style="color:#000000">Privacybeleid</a>.</p>

            <h2 id="artikel-8a">Artikel 8a - Cookies & Advertenties</h2>
            <p>8a.1 GlamourSchedule maakt gebruik van cookies om de gebruikerservaring te verbeteren en relevante content aan te bieden.</p>
            <p>8a.2 Wij gebruiken de volgende soorten cookies:</p>
            <ul>
                <li><strong>Functionele cookies:</strong> Noodzakelijk voor de werking van het Platform (bijv. sessie, taalvoorkeur).</li>
                <li><strong>Analytische cookies:</strong> Om het gebruik van het Platform te analyseren en te verbeteren.</li>
                <li><strong>Marketing/Advertentie cookies:</strong> Om gepersonaliseerde content en aanbevelingen te tonen op basis van uw interesses en browsegedrag.</li>
            </ul>
            <p>8a.3 Advertentie cookies verzamelen informatie over:</p>
            <ul>
                <li>Bekeken diensten en categorieën (bijv. kapper, nagelsalon, massages)</li>
                <li>Bezochte salons en bedrijven</li>
                <li>Zoekopdrachten binnen het Platform</li>
                <li>Interesses afgeleid uit uw activiteit</li>
            </ul>
            <p>8a.4 Deze gegevens worden alleen verzameld nadat u uitdrukkelijk toestemming heeft gegeven via de cookiebanner. U kunt uw toestemming te allen tijde intrekken via de cookie-instellingen.</p>
            <p>8a.5 Advertentie cookies worden opgeslagen voor maximaal 365 dagen. De verzamelde gegevens worden niet gedeeld met derden voor externe advertentiedoeleinden.</p>
            <p>8a.6 Door het accepteren van marketing cookies stemt u in met het tonen van gepersonaliseerde aanbevelingen voor salons en diensten die aansluiten bij uw voorkeuren.</p>

            <div class="terms-info">
                <p><strong>Let op:</strong> U kunt uw cookievoorkeuren op elk moment wijzigen door op "Cookie-instellingen" te klikken onderaan elke pagina.</p>
            </div>

            <h2 id="artikel-9">Artikel 9 - Klachten</h2>
            <p>9.1 Klachten over het Platform of de dienstverlening van GlamourSchedule kunnen worden ingediend via info@glamourschedule.nl.</p>
            <p>9.2 Wij streven ernaar klachten binnen 14 dagen af te handelen.</p>
            <p>9.3 Klachten over de uitvoering van een behandeling door een Dienstverlener dienen direct bij de betreffende Dienstverlener te worden ingediend.</p>

            <h2 id="artikel-10">Artikel 10 - Slotbepalingen</h2>
            <p>10.1 Op deze algemene voorwaarden is Nederlands recht van toepassing.</p>
            <p>10.2 Geschillen worden voorgelegd aan de bevoegde rechter te Amsterdam.</p>
            <p>10.3 GlamourSchedule behoudt zich het recht voor deze algemene voorwaarden te wijzigen. Wijzigingen worden via het Platform bekend gemaakt.</p>
            <p>10.4 Indien een bepaling uit deze voorwaarden nietig of vernietigbaar blijkt, tast dit de geldigheid van de overige bepalingen niet aan.</p>

            <?php else: ?>
            <!-- ENGLISH VERSION -->
            <div class="terms-toc">
                <h3>Table of Contents</h3>
                <ul>
                    <li><a href="#artikel-1">Article 1 - Definitions</a></li>
                    <li><a href="#artikel-2">Article 2 - Applicability</a></li>
                    <li><a href="#artikel-3">Article 3 - Services</a></li>
                    <li><a href="#artikel-4">Article 4 - Bookings</a></li>
                    <li><a href="#artikel-5">Article 5 - Payment</a></li>
                    <li><a href="#artikel-6">Article 6 - Cancellation</a></li>
                    <li><a href="#artikel-7">Article 7 - Liability</a></li>
                    <li><a href="#artikel-8">Article 8 - Privacy</a></li>
                    <li><a href="#artikel-8a">Article 8a - Cookies & Advertising</a></li>
                    <li><a href="#artikel-9">Article 9 - Complaints</a></li>
                    <li><a href="#artikel-10">Article 10 - Final Provisions</a></li>
                </ul>
            </div>

            <h2 id="artikel-1">Article 1 - Definitions</h2>
            <p>In these general terms and conditions, the following terms have the following meanings:</p>
            <ol>
                <li><strong>GlamourSchedule:</strong> GlamourSchedule B.V., registered in the Netherlands, Chamber of Commerce number 81973667, hereinafter referred to as "Platform".</li>
                <li><strong>Customer:</strong> The natural person or legal entity that books a service from a Service Provider via the Platform.</li>
                <li><strong>Service Provider:</strong> The entrepreneur (salon, beauty salon, hairdresser, etc.) who offers his or her services via the Platform.</li>
                <li><strong>Booking:</strong> An appointment made via the Platform between Customer and Service Provider.</li>
                <li><strong>Service:</strong> The beauty or wellness treatment performed by the Service Provider.</li>
            </ol>

            <h2 id="artikel-2">Article 2 - Applicability</h2>
            <p>2.1 These general terms and conditions apply to all services offered by GlamourSchedule and to all bookings made via the Platform.</p>
            <p>2.2 By using the Platform or by placing a booking, you accept these general terms and conditions.</p>
            <p>2.3 Deviations from these conditions are only valid if agreed in writing.</p>

            <h2 id="artikel-3">Article 3 - Services</h2>
            <p>3.1 GlamourSchedule is an intermediary platform that connects Customers and Service Providers.</p>
            <p>3.2 GlamourSchedule is not a party to the agreement between Customer and Service Provider. The Service Provider is fully responsible for the execution of the booked service.</p>
            <p>3.3 GlamourSchedule strives to keep the information on the Platform as current and accurate as possible, but does not guarantee the accuracy, completeness or timeliness of the information provided.</p>

            <h2 id="artikel-4">Article 4 - Bookings</h2>
            <p>4.1 A booking is established as soon as the Customer has confirmed the booking via the Platform and the payment has been completed.</p>
            <p>4.2 After completing a booking, the Customer will receive a confirmation email with all relevant details and a QR code that can be shown upon arrival.</p>
            <p>4.3 The Customer must be present at the agreed location on time (at least 5 minutes before the start).</p>
            <p>4.4 In case of a no-show, the Service Provider may charge the full price.</p>

            <div class="terms-info">
                <p><strong>Tip:</strong> You will automatically receive a reminder email 24 hours before your appointment.</p>
            </div>

            <h2 id="artikel-5">Article 5 - Payment</h2>
            <p>5.1 Payments are made via Mollie's secure payment system. GlamourSchedule accepts common Dutch payment methods including iDEAL, credit card and bank transfer.</p>
            <p>5.2 The price of the service is clearly stated when booking. The Customer pays the full amount of the treatment without any additional fees.</p>
            <p>5.3 An administrative fee of €1.75 per completed booking is deducted from the payout to the Service Provider. This fee is not charged to the Customer.</p>
            <p>5.4 The Service Provider receives the payment (minus the administrative fee of €1.75 per booking) within 14 days after completion of the service.</p>

            <h2 id="artikel-6">Article 6 - Cancellation</h2>
            <p>6.1 Cancellation by the Customer is free of charge up to 24 hours before the start of the appointment.</p>
            <p>6.2 In case of cancellation within 24 hours before the start, 50% of the total price will be charged.</p>
            <p>6.3 The Service Provider may cancel an appointment in case of force majeure. In this case, the Customer will receive a full refund.</p>
            <p>6.4 Refunds are made within 5-10 business days via the same payment method as the original payment.</p>

            <h2 id="artikel-7">Article 7 - Liability</h2>
            <p>7.1 GlamourSchedule is not liable for damage resulting from the services provided by the Service Provider.</p>
            <p>7.2 GlamourSchedule is not liable for indirect damage, consequential damage, lost profits or missed savings.</p>
            <p>7.3 GlamourSchedule's liability is in all cases limited to the amount the Customer paid for the booking in question.</p>
            <p>7.4 Complaints about the execution of the service must be submitted directly to the Service Provider.</p>

            <h2 id="artikel-8">Article 8 - Privacy</h2>
            <p>8.1 GlamourSchedule processes personal data in accordance with the General Data Protection Regulation (GDPR).</p>
            <p>8.2 Personal data is only used to execute bookings, communication and to improve our services.</p>
            <p>8.3 For more information, please refer to our <a href="/privacy" style="color:#000000">Privacy Policy</a>.</p>

            <h2 id="artikel-8a">Article 8a - Cookies & Advertising</h2>
            <p>8a.1 GlamourSchedule uses cookies to improve the user experience and to offer relevant content.</p>
            <p>8a.2 We use the following types of cookies:</p>
            <ul>
                <li><strong>Functional cookies:</strong> Necessary for the operation of the Platform (e.g., session, language preference).</li>
                <li><strong>Analytical cookies:</strong> To analyze and improve the use of the Platform.</li>
                <li><strong>Marketing/Advertising cookies:</strong> To show personalized content and recommendations based on your interests and browsing behavior.</li>
            </ul>
            <p>8a.3 Advertising cookies collect information about:</p>
            <ul>
                <li>Viewed services and categories (e.g., hairdresser, nail salon, massages)</li>
                <li>Visited salons and businesses</li>
                <li>Search queries within the Platform</li>
                <li>Interests derived from your activity</li>
            </ul>
            <p>8a.4 This data is only collected after you have explicitly given consent via the cookie banner. You can withdraw your consent at any time via the cookie settings.</p>
            <p>8a.5 Advertising cookies are stored for a maximum of 365 days. The collected data is not shared with third parties for external advertising purposes.</p>
            <p>8a.6 By accepting marketing cookies, you consent to seeing personalized recommendations for salons and services that match your preferences.</p>

            <div class="terms-info">
                <p><strong>Note:</strong> You can change your cookie preferences at any time by clicking "Cookie Settings" at the bottom of any page.</p>
            </div>

            <h2 id="artikel-9">Article 9 - Complaints</h2>
            <p>9.1 Complaints about the Platform or GlamourSchedule's services can be submitted via info@glamourschedule.nl.</p>
            <p>9.2 We aim to handle complaints within 14 days.</p>
            <p>9.3 Complaints about the execution of a treatment by a Service Provider must be submitted directly to the relevant Service Provider.</p>

            <h2 id="artikel-10">Article 10 - Final Provisions</h2>
            <p>10.1 Dutch law applies to these general terms and conditions.</p>
            <p>10.2 Disputes will be submitted to the competent court in Amsterdam.</p>
            <p>10.3 GlamourSchedule reserves the right to change these general terms and conditions. Changes will be announced via the Platform.</p>
            <p>10.4 If a provision of these conditions proves to be null and void, this does not affect the validity of the remaining provisions.</p>
            <?php endif; ?>

            <p class="last-updated">
                <?= $lang === 'nl' ? 'Laatst bijgewerkt: januari 2026' : 'Last updated: January 2026' ?>
            </p>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>

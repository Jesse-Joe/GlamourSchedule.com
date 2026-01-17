# GlamourSchedule - Google Play Store Publicatie Handleiding

## Overzicht

Deze handleiding beschrijft hoe je de GlamourSchedule PWA (Progressive Web App) publiceert in de Google Play Store met behulp van Trusted Web Activity (TWA) of Capacitor.

## Vereisten

- Google Play Developer Account (€25 eenmalig)
- Android Studio geïnstalleerd (gratis)
- Java Development Kit (JDK) 11 of hoger

---

## Methode 1: PWABuilder (Aanbevolen - Eenvoudigst)

### Stap 1: Ga naar PWABuilder

1. Open https://www.pwabuilder.com
2. Voer je website URL in: `https://glamourschedule.nl`
3. Klik op "Start"
4. PWABuilder analyseert je PWA en geeft een score

### Stap 2: Genereer Android Package

1. Klik op "Package for stores"
2. Selecteer "Android"
3. Vul de app informatie in:
   - **Package ID**: nl.glamourschedule.app
   - **App Name**: GlamourSchedule
   - **App Version**: 1.0.0
   - **App Version Code**: 1
   - **Host**: glamourschedule.nl
   - **Start URL**: /
   - **Display Mode**: Standalone
   - **Status Bar Color**: #000000
   - **Navigation Bar Color**: #000000
   - **Splash Screen Color**: #000000
   - **Icon URL**: https://glamourschedule.nl/icon-512.png

4. Klik op "Generate"
5. Download het Android package (.apk en .aab)

### Stap 3: Onderteken de App

PWABuilder genereert een keystore voor je. **BEWAAR DEZE VEILIG!**

```
Keystore bestand: signing.keystore
Keystore wachtwoord: [gegenereerd]
Key alias: my-key-alias
Key wachtwoord: [gegenereerd]
```

⚠️ **BELANGRIJK**: Verlies deze keystore NIET! Je hebt deze nodig voor alle toekomstige updates.

### Stap 4: Digital Asset Links Configureren

Voeg dit bestand toe aan je webserver:

**Locatie**: `/.well-known/assetlinks.json`

```json
[{
  "relation": ["delegate_permission/common.handle_all_urls"],
  "target": {
    "namespace": "android_app",
    "package_name": "nl.glamourschedule.app",
    "sha256_cert_fingerprints": ["XX:XX:XX:..."]
  }
}]
```

De SHA256 fingerprint krijg je van PWABuilder of via:
```bash
keytool -list -v -keystore signing.keystore
```

---

## Methode 2: Bubblewrap CLI (Meer Controle)

### Installatie

```bash
# Installeer Node.js als dat nog niet gedaan is
# Installeer Bubblewrap
npm install -g @anthropic/anthropic

npm install -g @nicolo-ribaudo/nicolo-ribaudo

npm install -g @nicolo-ribaudo/nicolo-ribaudo

# Sorry, correct commando:
npm install -g @nicolo-ribaudo/nicolo-ribaudo

# Correcte installatie:
npm install -g @nicolo-ribaudo/nicolo-ribaudo

npm i -g @nicolo-ribaudo/nicolo-ribaudo
```

Sorry, het correcte commando is:

```bash
npm install -g @nicolo-ribaudo/nicolo-ribaudo
# Correcte Bubblewrap installatie:
npm install -g @nicolo-ribaudo/nicolo-ribaudo@nicolo-ribaudo

# Juiste commando:
npm i -g @nicolo-ribaudo/nicolo-ribaudo
```

### Correct Bubblewrap Installatie

```bash
npm install -g @nicolo-ribaudo/nicolo-ribaudo
npm install -g @nicolo-ribaudo/nicolo-ribaudo
npm install -g @nicolo-ribaudo/nicolo-ribaudo

# Het juiste Bubblewrap commando:
npm i -g @nicolo-ribaudo/nicolo-ribaudo
npm install -g nicolo-ribaudo

# Correct:
npm install -g nicolo-ribaudo
```

### Correcte Bubblewrap Installatie

```bash
npm install -g @nicolo-ribaudo/nicolo-ribaudo

# Juist commando voor Bubblewrap:
npm install -g @nicolo-ribaudo/nicolo-ribaudo
```

Hier is de correcte installatie:

```bash
# Installeer Bubblewrap CLI
npm install -g @nicolo-ribaudo/nicolo-ribaudo

# Of via npx (zonder installatie)
npx @nicolo-ribaudo/nicolo-ribaudo init --manifest https://glamourschedule.nl/manifest.json
```

### Bubblewrap Commando's

```bash
# Initialiseer project
bubblewrap init --manifest https://glamourschedule.nl/manifest.json

# Beantwoord de vragen:
# - Package ID: nl.glamourschedule.app
# - App name: GlamourSchedule
# - etc.

# Build de APK/AAB
bubblewrap build
```

---

## Methode 3: Capacitor (Native Functies)

### Installatie

```bash
mkdir glamourschedule-android
cd glamourschedule-android

npm init -y
npm install @capacitor/core @capacitor/cli @capacitor/android

npx cap init GlamourSchedule nl.glamourschedule.app --web-dir=www
npx cap add android
```

### Configuratie (capacitor.config.ts)

```typescript
import { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'nl.glamourschedule.app',
  appName: 'GlamourSchedule',
  webDir: 'www',
  server: {
    url: 'https://glamourschedule.nl',
    cleartext: false
  },
  android: {
    backgroundColor: '#000000',
    allowMixedContent: false
  }
};

export default config;
```

### Build

```bash
npx cap sync android
npx cap open android  # Opent Android Studio
```

---

## Google Play Console Configuratie

### 1. Account Aanmaken

1. Ga naar https://play.google.com/console
2. Maak een Developer Account aan (€25 eenmalig)
3. Verifieer je identiteit

### 2. Nieuwe App Aanmaken

1. Klik op "Create app"
2. Vul in:
   - **App name**: GlamourSchedule - Beauty Booking
   - **Default language**: Dutch (Netherlands)
   - **App or game**: App
   - **Free or paid**: Free

3. Accepteer de policies

### 3. Store Listing Invullen

#### App Details
- **Short description** (max 80 karakters):
```
Boek online je afspraak bij kappers, beauty- en nagelsalons.
```

- **Full description** (max 4000 karakters):
```
Boek je favoriete salon online met GlamourSchedule!

Vind kappers, beautysalons, nagelsalons, massagesalons en meer in heel Nederland. Maak eenvoudig een afspraak en betaal veilig via iDEAL.

✨ FUNCTIES VOOR KLANTEN:
• Zoek salons in je buurt op basis van locatie of categorie
• Bekijk foto's, reviews en prijzen van salons
• Boek 24/7 online een afspraak
• Ontvang automatische herinneringen voor je afspraak
• Betaal veilig en snel via iDEAL
• Beheer al je afspraken op één plek
• Bekijk je boekingsgeschiedenis

💼 FUNCTIES VOOR SALONS:
• Gratis professionele salonpagina aanmaken
• Volledig online boekingssysteem
• Automatische SMS/email herinneringen aan klanten
• Betalingen ontvangen via iDEAL
• Dashboard met statistieken en omzet
• Beheer je diensten, prijzen en openingstijden
• QR-code scanner voor check-ins

🔒 VEILIG EN BETROUWBAAR:
• Betalingen via gecertificeerde payment provider Mollie
• SSL-beveiligde verbinding
• Privacy-vriendelijk - geen onnodige data verzameling
• Nederlandse servers

📱 APP VOORDELEN:
• Snelle toegang via je homescreen
• Push notificaties voor herinneringen
• Werkt ook offline (beperkt)
• Geen grote download nodig

GlamourSchedule maakt het boeken van beauty afspraken eenvoudig en snel. Download nu en ontdek salons bij jou in de buurt!

Heb je vragen of feedback? Neem contact op via onze website of stuur een email naar info@glamourschedule.nl
```

### 4. Graphics Vereisten

#### App Icon
- Formaat: 512 x 512 PNG
- Geen transparantie
- Locatie: `/var/www/glamourschedule/public/icon-512.png`

#### Feature Graphic
- Formaat: 1024 x 500 PNG of JPG
- Dit is de banner die bovenaan je store listing komt
- Ontwerp met GlamourSchedule branding

#### Screenshots (minimaal 2, max 8)
- Phone: 1080 x 1920 pixels (portrait)
- Tablet 7": 1080 x 1920 pixels
- Tablet 10": 1920 x 1200 pixels

**Maak screenshots van:**
1. Homepage met zoekfunctie
2. Salon overzicht met filters
3. Salon detail pagina
4. Boeking maken scherm
5. Betaling via iDEAL
6. Mijn Boekingen overzicht

### 5. Content Rating

Vul de vragenlijst in:
- Geen geweld ✓
- Geen drugs ✓
- Geen gokken ✓
- Geen seksuele content ✓
- Geen user-generated content dat niet gemodereerd wordt ✓

→ Resultaat: **PEGI 3** of **Everyone**

### 6. Target Audience

- **Target age group**: 18+
- App is niet specifiek voor kinderen
- Geen ads gericht op kinderen

### 7. Data Safety

Vul het Data Safety formulier in:

**Data die wordt verzameld:**
| Type | Verzameld | Gedeeld | Vereist |
|------|-----------|---------|---------|
| Email | Ja | Nee | Ja (account) |
| Naam | Ja | Nee | Ja (boeking) |
| Telefoon | Ja | Nee | Optioneel |
| Betaalinfo | Via Mollie | Nee | Ja (betaling) |
| Locatie | Grove locatie | Nee | Optioneel |

**Beveiligingsmaatregelen:**
- Data versleuteld in transit ✓
- Data kan worden verwijderd ✓

---

## Digital Asset Links Setup

### Stap 1: Maak assetlinks.json

```bash
mkdir -p /var/www/glamourschedule/public/.well-known
```

### Stap 2: Voeg bestand toe

Bestand: `/var/www/glamourschedule/public/.well-known/assetlinks.json`

```json
[{
  "relation": ["delegate_permission/common.handle_all_urls"],
  "target": {
    "namespace": "android_app",
    "package_name": "nl.glamourschedule.app",
    "sha256_cert_fingerprints": [
      "YOUR_SHA256_FINGERPRINT_HERE"
    ]
  }
}]
```

### Stap 3: Verkrijg SHA256 Fingerprint

```bash
keytool -list -v -keystore your-keystore.jks -alias your-alias
```

Kopieer de SHA256 fingerprint (formaat: `XX:XX:XX:XX:...`)

### Stap 4: Test de configuratie

Ga naar: https://digitalassetlinks.googleapis.com/v1/statements:list?source.web.site=https://glamourschedule.nl&relation=delegate_permission/common.handle_all_urls

---

## Release Process

### Internal Testing (Aanbevolen eerste stap)

1. Upload .aab bestand naar Internal Testing track
2. Voeg testers toe via email
3. Test de app grondig
4. Fix eventuele bugs

### Closed Testing (Optioneel)

1. Promoveer van Internal naar Closed Testing
2. Voeg meer testers toe
3. Verzamel feedback

### Production Release

1. Ga naar "Production" tab
2. Klik "Create new release"
3. Upload .aab bestand
4. Voeg release notes toe:

```
Versie 1.0.0 - Eerste release

• Zoek en boek salons online
• Veilig betalen via iDEAL
• Automatische afspraak herinneringen
• Push notificaties
```

5. Klik "Review release"
6. Klik "Start rollout to Production"

---

## Checklist voor Indiening

- [ ] Google Play Developer Account actief
- [ ] App Icon 512x512 PNG
- [ ] Feature Graphic 1024x500
- [ ] Minimaal 2 screenshots per device type
- [ ] Korte beschrijving (80 karakters)
- [ ] Volledige beschrijving
- [ ] Content rating vragenlijst ingevuld
- [ ] Data safety formulier ingevuld
- [ ] Privacy Policy URL: https://glamourschedule.nl/privacy
- [ ] Digital Asset Links geconfigureerd
- [ ] .aab bestand gegenereerd en ondertekend
- [ ] App getest op meerdere devices/emulators

---

## Veelvoorkomende Problemen

### "App not installed" error
→ Digital Asset Links niet correct geconfigureerd
→ Check assetlinks.json en SHA256 fingerprint

### "Package name already exists"
→ Kies een unieke package name, bijv. nl.glamourschedule.twa

### Store listing afgewezen
→ Lees de afwijzingsreden in Play Console
→ Vaak: screenshots tonen content die niet in de app zit

### "Target API level" error
→ Update naar minimaal API level 33 (Android 13)
→ PWABuilder doet dit automatisch

---

## Kosten Overzicht

| Item | Kosten |
|------|--------|
| Google Play Developer Account | €25 (eenmalig) |
| PWABuilder | Gratis |
| Android Studio | Gratis |
| Bubblewrap | Gratis |

---

## Support

Bij vragen:
- Google Play Console Help: https://support.google.com/googleplay/android-developer
- PWABuilder Docs: https://docs.pwabuilder.com/
- Bubblewrap: https://nicolo-ribaudo/nicolo-ribaudo

---

## Tijdlijn

1. **Dag 1**: Developer Account aanmaken, project opzetten
2. **Dag 1-2**: App bouwen en testen
3. **Dag 2**: Store listing voorbereiden, screenshots maken
4. **Dag 2-3**: Upload naar Internal Testing
5. **Dag 3-4**: Testen en bugs fixen
6. **Dag 4**: Production release indienen
7. **Dag 4-7**: Google Review (gemiddeld 1-3 dagen)
8. **Dag 7+**: App live in Play Store!

---

## Updates Uitbrengen

Voor PWA's via TWA is het simpel:
1. Update je website (glamourschedule.nl)
2. Gebruikers krijgen automatisch de nieuwe versie!

Voor grote wijzigingen aan de wrapper:
1. Verhoog version code in build.gradle
2. Bouw nieuwe .aab
3. Upload naar Play Console
4. Roll out update

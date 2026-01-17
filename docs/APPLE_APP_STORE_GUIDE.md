# GlamourSchedule - Apple App Store Publicatie Handleiding

## Overzicht

Deze handleiding beschrijft hoe je de GlamourSchedule PWA (Progressive Web App) publiceert in de Apple App Store met behulp van een native iOS wrapper.

## Vereisten

- Apple Developer Account (€99/jaar) - ✅ Je hebt dit al
- Mac met Xcode geïnstalleerd (gratis via App Store)
- iOS apparaat voor testen (optioneel, simulator werkt ook)

---

## Methode 1: PWABuilder (Aanbevolen - Eenvoudigst)

### Stap 1: Ga naar PWABuilder

1. Open https://www.pwabuilder.com
2. Voer je website URL in: `https://glamourschedule.nl`
3. Klik op "Start"
4. PWABuilder analyseert je PWA

### Stap 2: Genereer iOS Package

1. Klik op "Package for stores"
2. Selecteer "iOS"
3. Vul de app informatie in:
   - **App Name**: GlamourSchedule
   - **Bundle ID**: nl.glamourschedule.app
   - **Version**: 1.0.0
   - **Display Mode**: Standalone

4. Klik op "Generate"
5. Download het Xcode project (.zip)

### Stap 3: Open in Xcode

1. Pak het gedownloade .zip bestand uit
2. Open het `.xcodeproj` bestand in Xcode
3. Selecteer je Team (Apple Developer Account) bij Signing & Capabilities
4. Pas de Bundle Identifier aan indien nodig

### Stap 4: Test de App

1. Sluit je iPhone aan of gebruik de Simulator
2. Klik op de "Play" knop in Xcode
3. Test alle functionaliteit

### Stap 5: Upload naar App Store Connect

1. In Xcode: Product → Archive
2. Na het archiveren: Distribute App → App Store Connect
3. Upload naar App Store Connect

---

## Methode 2: Capacitor (Meer Controle)

Als je meer controle wilt over de native functies, gebruik Capacitor.

### Installatie

```bash
# Maak een nieuwe map voor het iOS project
mkdir glamourschedule-ios
cd glamourschedule-ios

# Initialiseer npm project
npm init -y

# Installeer Capacitor
npm install @capacitor/core @capacitor/cli @capacitor/ios

# Initialiseer Capacitor
npx cap init GlamourSchedule nl.glamourschedule.app --web-dir=www

# Voeg iOS platform toe
npx cap add ios
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
  ios: {
    contentInset: 'always',
    backgroundColor: '#000000',
    preferredContentMode: 'mobile'
  }
};

export default config;
```

### Build en Deploy

```bash
# Sync met iOS project
npx cap sync ios

# Open in Xcode
npx cap open ios
```

---

## App Store Connect Configuratie

### 1. Maak nieuwe app aan

1. Ga naar https://appstoreconnect.apple.com
2. Klik op "My Apps" → "+" → "New App"
3. Vul in:
   - **Platform**: iOS
   - **Name**: GlamourSchedule - Beauty Booking
   - **Primary Language**: Dutch
   - **Bundle ID**: nl.glamourschedule.app
   - **SKU**: glamourschedule-ios-001

### 2. App Informatie

#### Algemeen
- **Naam**: GlamourSchedule - Beauty Booking
- **Ondertitel**: Boek je favoriete salon online
- **Categorie**: Lifestyle (primair), Business (secundair)

#### Beschrijving (Nederlands)
```
Boek je favoriete salon online met GlamourSchedule!

Vind kappers, beautysalons, nagelsalons, massagesalons en meer in heel Nederland. Maak eenvoudig een afspraak en betaal veilig via iDEAL.

FUNCTIES:
• Zoek salons in je buurt
• Bekijk foto's, reviews en prijzen
• Boek 24/7 online een afspraak
• Ontvang automatische herinneringen
• Betaal veilig via iDEAL
• Beheer al je afspraken op één plek

VOOR SALONS:
• Gratis salonpagina aanmaken
• Online boekingssysteem
• Automatische klant-herinneringen
• Betalingen via iDEAL
• Dashboard met statistieken

GlamourSchedule maakt het boeken van beauty afspraken eenvoudig en snel!
```

#### Keywords
```
salon, kapper, beauty, nagels, massage, boeken, afspraak, nederland, hair, nails, booking
```

### 3. Schermafbeeldingen Vereist

Je hebt screenshots nodig voor:
- iPhone 6.7" (1290 x 2796 pixels) - iPhone 15 Pro Max
- iPhone 6.5" (1284 x 2778 pixels) - iPhone 14 Plus
- iPhone 5.5" (1242 x 2208 pixels) - iPhone 8 Plus
- iPad Pro 12.9" (2048 x 2732 pixels)

**Maak screenshots van:**
1. Homepage / zoekscherm
2. Salon overzicht
3. Boeking maken
4. Betalingsscherm
5. Dashboard (optioneel)

### 4. App Privacy

Voeg toe in App Store Connect onder "App Privacy":

**Data Types die worden verzameld:**
- Contact Info (Email, Phone)
- Identifiers (User ID)
- Usage Data (Product Interaction)
- Financial Info (Payment Info - via Mollie)

**Doeleinden:**
- App Functionality
- Analytics
- Account Management

### 5. App Review Informatie

Geef Apple reviewers toegang:
- Demo account aanmaken
- Review notes toevoegen met instructies

---

## Benodigde Assets

### App Icon (1024x1024)

Maak een 1024x1024 PNG zonder alpha channel:
- Gebruik het GlamourSchedule logo
- Zorg voor goede leesbaarheid op kleine formaten
- Geen transparantie

Bestandslocatie: `/var/www/glamourschedule/public/icon-512.png`
(Schaal op naar 1024x1024)

### Launch Screen

Maak een launch screen (storyboard of afbeelding):
- Zwarte achtergrond (#000000)
- GlamourSchedule logo gecentreerd
- Geen tekst behalve logo

---

## Checklist voor Indiening

- [ ] Apple Developer Account actief
- [ ] App Icon 1024x1024 PNG
- [ ] Screenshots voor alle vereiste formaten
- [ ] App beschrijving in het Nederlands
- [ ] Privacy Policy URL: https://glamourschedule.nl/privacy
- [ ] Support URL: https://glamourschedule.nl/contact
- [ ] Marketing URL: https://glamourschedule.nl
- [ ] Xcode project gebouwd zonder errors
- [ ] App getest op fysiek apparaat
- [ ] App Store Connect app aangemaakt

---

## Veelvoorkomende Problemen

### "App is missing required launch images"
→ Voeg LaunchScreen.storyboard toe of configureer launch images

### "The bundle identifier cannot be used"
→ Kies een unieke bundle ID, bijv. nl.glamourschedule.app

### "Missing required icon"
→ Zorg dat alle icon sizes aanwezig zijn in Assets.xcassets

### App wordt afgewezen
→ Bekijk de feedback van Apple, vaak gaat het om:
- Onvolledige functionaliteit
- Bugs of crashes
- Misleidende metadata
- Privacy policy ontbreekt

---

## Kosten Overzicht

| Item | Kosten |
|------|--------|
| Apple Developer Account | €99/jaar |
| PWABuilder | Gratis |
| Capacitor | Gratis |
| Xcode | Gratis |

---

## Support

Bij vragen over het publicatieproces:
- Apple Developer Support: https://developer.apple.com/support/
- PWABuilder Docs: https://docs.pwabuilder.com/
- Capacitor Docs: https://capacitorjs.com/docs/ios

---

## Tijdlijn

1. **Dag 1-2**: Xcode project opzetten en testen
2. **Dag 2-3**: Screenshots maken en metadata voorbereiden
3. **Dag 3**: Upload naar App Store Connect
4. **Dag 4-7**: Apple Review (gemiddeld 24-48 uur, max 7 dagen)
5. **Dag 7+**: App live in App Store!

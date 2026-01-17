# Screenshots Guide voor App Stores

## Vereiste Screenshots

### Apple App Store

| Device | Afmetingen | Aantal |
|--------|------------|--------|
| iPhone 6.7" (15 Pro Max) | 1290 x 2796 | 3-10 |
| iPhone 6.5" (14 Plus) | 1284 x 2778 | 3-10 |
| iPhone 5.5" (8 Plus) | 1242 x 2208 | 3-10 |
| iPad Pro 12.9" | 2048 x 2732 | 3-10 |

### Google Play Store

| Device | Afmetingen | Aantal |
|--------|------------|--------|
| Phone | 1080 x 1920 | 2-8 |
| Tablet 7" | 1080 x 1920 | 0-8 |
| Tablet 10" | 1920 x 1200 | 0-8 |

---

## Te Maken Screenshots

### 1. Homepage / Zoeken
- **Bestandsnaam**: `home.png`
- **Wat**: Hoofdpagina met zoekbalk en categorieën
- **Focus**: Zoekfunctionaliteit en overzichtelijk design

### 2. Salon Zoekresultaten
- **Bestandsnaam**: `search.png`
- **Wat**: Lijst met salons na een zoekopdracht
- **Focus**: Filters, ratings, locatie

### 3. Salon Detail Pagina
- **Bestandsnaam**: `salon.png`
- **Wat**: Detail van een salon met diensten
- **Focus**: Foto's, reviews, diensten, prijzen

### 4. Boeking Maken
- **Bestandsnaam**: `booking.png`
- **Wat**: Datum/tijd selectie voor boeking
- **Focus**: Kalender, tijdslots

### 5. Betaling
- **Bestandsnaam**: `payment.png`
- **Wat**: Betaalscherm met iDEAL
- **Focus**: Veilige betaling

### 6. Mijn Boekingen
- **Bestandsnaam**: `my-bookings.png`
- **Wat**: Overzicht van gemaakte afspraken
- **Focus**: Overzichtelijke lijst

---

## Screenshots Maken

### Methode 1: Browser Developer Tools

1. Open Chrome DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Selecteer device of stel custom size in
4. Maak screenshot (Ctrl+Shift+P → "Capture screenshot")

### Methode 2: Smartphone

1. Open de website op je telefoon
2. Maak screenshot (iPhone: Side + Volume Up / Android: Power + Volume Down)
3. Bewerk indien nodig om status bar te verwijderen

### Methode 3: Screenshot Tools

- **Figma**: Maak mockups met device frames
- **Screely**: https://www.screely.com
- **MockuPhone**: https://mockuphone.com

---

## Tips voor Goede Screenshots

1. **Gebruik echte data** - Geen "Lorem ipsum"
2. **Volle batterij** - Laat 100% zien in status bar
3. **Consistent tijdstip** - Alle screenshots zelfde tijd (bijv. 9:41)
4. **Geen notificaties** - Schakel Do Not Disturb in
5. **Wis browserbalk** - Gebruik standalone/fullscreen mode
6. **Hoge resolutie** - Minimaal 2x schaling

---

## Bestandslocaties

Plaats screenshots in:
```
/var/www/glamourschedule/public/screenshots/
├── home.png
├── search.png
├── booking.png
├── salon.png
├── payment.png
├── my-bookings.png
├── business-dashboard.png
├── business-calendar.png
├── business-bookings.png
├── sales-dashboard.png
└── sales-referrals.png
```

---

## Feature Graphic (Play Store)

- **Afmetingen**: 1024 x 500 pixels
- **Bestandsnaam**: `feature-graphic.png`
- **Inhoud**:
  - GlamourSchedule logo
  - Tagline: "Boek je favoriete salon online"
  - Achtergrond: Gradient of foto
  - Geen tekst kleiner dan 30px

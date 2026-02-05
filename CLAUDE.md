# GlamourSchedule - Project Context

## Overview
GlamourSchedule is a beauty salon booking platform (SaaS). Customers can search and book appointments at salons, businesses manage their agenda, services, employees, and payouts. Built as a custom PHP MVC application (no framework), running on PHP 7.4+ with MySQL/MariaDB.

**Live URL:** https://glamourschedule.com

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 7.4+ (custom MVC, no framework) |
| Database | MySQL/MariaDB (utf8mb4) |
| Frontend | Vanilla HTML/CSS/JS (no React/Vue) |
| Payments | Mollie (NL/EU), Stripe Connect (international) |
| Email | PHPMailer via SMTP |
| Push | Web Push (minishlink/web-push) |
| PDF | DomPDF |
| Auth | Session-based (cookie), 2FA via email codes |
| Hosting | Ubuntu VPS, Apache |

### Composer Dependencies
- `mollie/mollie-api-php` - Payment processing (Mollie)
- `stripe/stripe-php` - Payment processing (Stripe)
- `phpmailer/phpmailer` - Transactional emails
- `minishlink/web-push` - Push notifications
- `dompdf/dompdf` - PDF invoice generation
- `vlucas/phpdotenv` - Environment config

---

## Project Structure

```
glamourschedule/
├── public/                  # Web root (Apache DocumentRoot)
│   ├── index.php            # Single entry point (front controller)
│   ├── css/                 # Stylesheets
│   │   ├── prestige.css     # Main theme (dark/light mode)
│   │   └── concept*.css     # Landing page design variants
│   ├── js/                  # JavaScript files
│   ├── images/              # Static images
│   └── uploads/             # User uploads (logos, photos)
├── src/                     # Application source code
│   ├── Core/                # Framework core
│   │   ├── Application.php  # Bootstrap + route registration
│   │   ├── Router.php       # URL routing with {param} support
│   │   ├── Controller.php   # Base controller (view, redirect, csrf, auth, db, i18n)
│   │   ├── Database.php     # PDO wrapper
│   │   ├── Mailer.php       # PHPMailer wrapper
│   │   ├── GeoIP.php        # IP-based language detection
│   │   ├── Glamori.php      # AI chatbot integration
│   │   ├── PushNotification.php
│   │   └── SmsService.php   # MessageBird SMS
│   ├── Controllers/         # All route handlers (see Routes section)
│   └── Services/            # Business logic services
│       ├── HybridPaymentService.php    # Mollie + Stripe routing
│       ├── StripeConnectService.php    # Stripe Connect onboarding
│       ├── MollieSplitPaymentService.php
│       ├── LoyaltyService.php          # Points system
│       ├── BookingSignatureService.php  # Anti-fraud signatures
│       ├── CurrencyService.php         # Multi-currency support
│       ├── InvoiceService.php          # PDF invoices
│       └── TrackingService.php         # Analytics
├── resources/
│   ├── views/               # PHP view templates
│   │   ├── layouts/         # Layout templates (main, business, admin, sales, minimal)
│   │   └── pages/           # Page templates organized by section
│   └── lang/                # 88 language translation files
├── config/
│   └── config.php           # App config (reads from .env)
├── database/
│   └── migrations/          # SQL migration files
├── cron/                    # Cron job scripts
├── scripts/                 # Utility scripts
├── storage/
│   └── logs/                # Application logs
└── vendor/                  # Composer dependencies
```

---

## Architecture Pattern

### Request Flow
1. Apache rewrites all requests to `public/index.php`
2. `Application.php` bootstraps: loads config, DB, registers routes
3. `Router.php` matches URL to `Controller@method`
4. Controller extends `Controller.php` base class which provides:
   - `$this->db` - Database access (PDO)
   - `$this->config` - App configuration
   - `$this->view(template, data)` - Render view
   - `$this->redirect(url)` - HTTP redirect
   - `$this->csrf()` - CSRF token generation
   - `$this->user()` / `$this->business()` - Auth helpers
   - `$this->t(key)` - Translation helper
5. Views are plain PHP files rendered with extracted variables

### Authentication
- **Customers:** Email + password, session-based, optional 2FA (email code)
- **Businesses:** Email + password, separate session, security PIN option
- **Admin:** Separate login at `/admin`
- **Sales Partners:** Separate login at `/sales`
- Middleware groups: `auth` (customer), `business` (business dashboard)

---

## Routes / API Endpoints

### Public Pages
| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/` | HomeController@index | Homepage |
| GET | `/search` | SearchController@index | Search salons |
| GET | `/s/{uuid}` | BusinessController@showByUuid | Salon page (short URL) |
| GET | `/business/{slug}` | BusinessController@show | Salon page (legacy) |
| GET | `/pricing` | PagesController@pricing | Pricing page |
| GET | `/terms` | PagesController@terms | Terms & conditions |
| GET | `/privacy` | PagesController@privacy | Privacy policy |
| GET | `/faq` | PagesController@faq | FAQ |
| GET | `/contact` | PagesController@contact | Contact form |

### Authentication
| Method | URL | Description |
|--------|-----|-------------|
| GET/POST | `/login` | Customer login |
| GET/POST | `/register` | Customer registration |
| GET | `/logout` | Logout |
| GET/POST | `/verify-login` | 2FA verification |
| GET/POST | `/forgot-password` | Password reset request |
| GET/POST | `/reset-password/{token}` | Password reset form |
| GET/POST | `/business/login` | Business login |
| GET/POST | `/business/register` | Business registration |

### Booking Flow (Customer)
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/s/{uuid}/book` | Select service + time |
| POST | `/s/{uuid}/book` | Submit booking |
| GET | `/booking/checkout` | Payment checkout |
| POST | `/booking/confirm` | Confirm & pay |
| GET | `/booking/{uuid}` | View booking details |
| POST | `/booking/{uuid}/cancel` | Cancel booking |
| GET | `/review/{uuid}` | Write review |
| GET | `/checkin/{uuid}` | QR check-in page |

### Customer Dashboard (auth required)
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/dashboard` | Overview |
| GET | `/dashboard/bookings` | My bookings |
| GET | `/dashboard/profile` | Profile |
| GET | `/dashboard/settings` | Settings |
| GET | `/dashboard/security` | Security settings |
| GET | `/dashboard/loyalty` | Loyalty points |

### Business Dashboard (business auth required)
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/business/dashboard` | Overview + stats |
| GET | `/business/bookings` | All bookings |
| GET | `/business/calendar` | Calendar view |
| GET/POST | `/business/services` | Manage services |
| GET/POST | `/business/employees` | Manage employees |
| GET | `/business/payouts` | Payout overview |
| GET/POST | `/business/profile` | Business profile |
| GET/POST | `/business/website` | Website settings |
| GET/POST | `/business/photos` | Photo gallery |
| GET/POST | `/business/theme` | Theme customization |
| GET | `/business/reviews` | Customer reviews |
| GET | `/business/insights` | AI analytics |
| GET | `/business/boost` | Marketing boost |
| GET | `/business/pos` | Point of Sale system |
| GET/POST | `/business/inventory` | Inventory management |
| GET | `/business/scanner` | QR scanner |
| GET | `/business/terminals` | PIN terminal management |

### POS (Point of Sale) - Public Payment Links
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/pay/{uuid}` | Show payment page |
| POST | `/pay/{uuid}` | Process payment |
| GET | `/pay/{uuid}/return` | Return from Mollie |
| GET | `/pay/{uuid}/success` | Success page |

### REST API Endpoints
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/api/services/{businessId}` | List services for business |
| GET | `/api/availability/{businessId}` | Available time slots |
| GET | `/api/available-times/{slug}` | Available times for date |
| GET | `/api/salons/map` | Salon map data (GeoJSON) |
| GET | `/api/salon/{id}` | Single salon data |
| GET | `/api/translations/{lang}` | Translation strings |
| GET | `/api/global-search` | Global search |
| GET | `/api/categories` | Service categories |
| GET | `/api/stats` | Platform statistics |
| GET | `/api/payment/methods` | Available payment methods |
| POST | `/api/glamori/chat` | AI chatbot |
| GET | `/api/glamori/welcome` | Chatbot welcome message |
| POST | `/api/push/subscribe` | Push notification subscribe |
| POST | `/api/theme` | Save theme preference |
| POST | `/api/consent` | Cookie consent |

### Webhooks
| Method | URL | Description |
|--------|-----|-------------|
| POST | `/api/webhooks/mollie` | Mollie payment webhook |
| POST | `/api/webhooks/stripe` | Stripe payment webhook |
| POST | `/api/webhooks/mollie-terminal` | Terminal payment webhook |

### Cron Jobs
| URL | Description |
|-----|-------------|
| `/cron/weekly-payouts` | Process weekly payouts (Wednesday) |
| `/cron/process-payouts` | Process pending payouts |
| `/cron/complete-payouts` | Mark completed payouts |
| `/cron/trial-expiry` | Send trial expiry warnings |
| `/cron/deactivate-expired` | Deactivate expired trials |
| `/cron/process-reminders` | Send booking reminders |
| `/cron/waitlist-expire` | Expire old waitlist entries |

---

## Database Schema (Key Tables)

### users
Customer accounts. Fields: `id`, `uuid`, `email`, `password_hash`, `security_pin`, `first_name`, `last_name`, `phone`, `language` (nl/en/de/fr), `theme` (light/dark), `email_verified`, `two_factor_enabled`, `status` (active/inactive/banned).

### businesses
Salon/business accounts. Fields: `id`, `uuid`, `company_name`, `slug`, `email`, `password_hash`, `phone`, `street`, `house_number`, `postal_code`, `city`, `country`, `latitude`, `longitude`, `iban`, `iban_verified`, `logo`, `banner_image`, `status` (pending/active/suspended/inactive), `subscription_status` (pending/trial/active/expired/cancelled), `trial_ends_at`, `rating`, `total_reviews`, `language`, `theme`, `cash_payment_enabled`, `online_booking_enabled`, `mollie_account_id`, `stripe_account_id`, `preferred_payment_provider`.

### services
Services offered by businesses. Fields: `id`, `uuid`, `business_id`, `name`, `description`, `duration_minutes`, `price`, `sale_price`, `is_active`, `sort_order`.

### bookings
Online bookings made by customers. Fields: `id`, `uuid`, `booking_number`, `user_id`, `business_id`, `employee_id`, `service_id`, `guest_email`, `guest_name`, `appointment_date`, `appointment_time`, `duration_minutes`, `total_price`, `admin_fee` (platform fee, default 1.75), `status` (pending/confirmed/in_progress/checked_in/completed/cancelled/no_show), `payment_status` (pending/paid/refunded), `payment_method` (online/cash), `payout_status`, `mollie_payment_id`, `qr_code`, `language`.

### pos_bookings
Bookings created via POS (in-salon). Fields: `id`, `uuid`, `business_id`, `customer_id`, `service_id`, `employee_id`, `customer_name`, `customer_email`, `customer_phone`, `appointment_date`, `appointment_time`, `duration_minutes`, `total_price`, `service_fee`, `payment_method` (online/cash), `payment_status`, `booking_status`, `confirmation_email_sent`, `payment_link`, `notes`.

### pos_customers
Walk-in customer database per business. Fields: `id`, `business_id`, `name`, `email`, `phone`, `total_appointments`.

### employees
Staff members. Fields: `id`, `business_id`, `name`, `email`, `phone`, `photo`, `specialties`, `is_active`, `color`.

### business_hours
Opening hours. Fields: `id`, `business_id`, `day_of_week` (0-6), `open_time`, `close_time`, `is_closed`.

### reviews
Customer reviews. Fields: `id`, `booking_id`, `user_id`, `business_id`, `rating` (1-5), `comment`, `business_response`, `is_visible`.

### favorites
User favorited businesses. Composite key: `user_id`, `business_id`.

### loyalty_points
Per-user per-business loyalty points. Fields: `user_id`, `business_id`, `total_points`, `lifetime_points`.

### notifications
In-app notifications. Fields: `id`, `uuid`, `user_id`, `business_id`, `type`, `title`, `message`, `is_read`.

### push_subscriptions
Web Push subscriptions. Fields: `id`, `user_id`, `endpoint`, `p256dh_key`, `auth_key`.

### business_payouts
Payout records. Payouts are processed every Wednesday. Mollie Connect and Stripe Connect disburse 24h after booking approval.

---

## Payment Flow

### Online Booking Payment
1. Customer selects service + time slot
2. Creates booking with `status=pending`, `payment_status=pending`
3. Redirects to Mollie/Stripe for payment
4. Webhook confirms payment: sets `payment_status=paid`, `status=confirmed`
5. Confirmation email sent to customer
6. Platform fee (admin_fee = 1.75) deducted, rest queued for business payout

### POS Payment
1. Business creates booking via POS dashboard
2. Payment link generated and sent via email/SMS
3. Customer pays via link (Mollie)
4. Webhook confirms: sets `payment_status=paid`, `booking_status=confirmed`
5. Confirmation email sent (with idempotency check)

### Cash Payment
1. Booking created with `payment_method=cash`
2. Service fee (1.75) paid online
3. Remaining amount paid in cash at appointment

### Payout Schedule
- Payouts processed every **Wednesday**
- Mollie Connect: automatic split payments, 24h after confirmed booking
- Stripe Connect: automatic split payments, 24h after confirmed booking
- Manual IBAN: weekly batch payout via platform

---

## Internationalization (i18n)

- 88 languages supported
- Translation files in `resources/lang/{code}/translations.php`
- Auto-detection via GeoIP (ip-api.com) and browser Accept-Language
- RTL support for: Arabic, Hebrew, Farsi, Urdu, Pashto, Kurdish
- Default: English, Fallback: Dutch
- Controller helper: `$this->t('key')` returns translated string

---

## Key Business Rules

- **Admin fee:** Fixed at 1.75 per booking (platform revenue)
- **Cancellation:** Free within 24h, 50% fee after
- **Trial:** Businesses get a trial period, then must subscribe (99.99 registration)
- **Early adopters:** First 100 businesses pay only 0.99
- **QR Check-in:** Each booking gets a QR code, business scans for check-in
- **Boost:** Businesses can pay for featured placement
- **Sales partners:** Commission of 25.00 per referred business signup

---

## Building a Mobile App

When building a mobile app for this platform, keep in mind:

### API Endpoints to Use
All `/api/*` endpoints return JSON and can be used directly. Key ones for a mobile app:
- `/api/global-search` - Search salons
- `/api/salons/map` - Map view data
- `/api/salon/{id}` - Salon detail
- `/api/services/{businessId}` - Services list
- `/api/availability/{businessId}` - Available time slots
- `/api/available-times/{slug}` - Time slots for date
- `/api/categories` - Browse by category
- `/api/translations/{lang}` - Get translations
- `/api/payment/methods` - Payment options
- `/api/push/subscribe` - Push notifications
- `/api/glamori/chat` - AI chatbot

### Authentication for Mobile
The current auth is session/cookie based. For a mobile app you will need to either:
1. Add JWT/token-based auth endpoints (recommended)
2. Or use cookie-based sessions with a WebView approach

### Missing API Endpoints (Need to Build)
For a full mobile app, you'll likely need to create additional REST endpoints for:
- `POST /api/auth/login` - Token-based login
- `POST /api/auth/register` - Registration
- `GET /api/user/profile` - User profile
- `GET /api/user/bookings` - User's bookings
- `POST /api/bookings/create` - Create booking
- `POST /api/bookings/{uuid}/cancel` - Cancel booking
- `GET /api/user/favorites` - Favorite salons
- `POST /api/user/favorites` - Add/remove favorite
- `GET /api/user/loyalty` - Loyalty points
- `GET /api/user/notifications` - Notifications
- `POST /api/reviews` - Submit review

### Payment Integration
- Mollie and Stripe both support mobile SDKs
- Payment links can be opened in in-app browser
- Webhook flow remains the same (server-side)

### Push Notifications
- Current: Web Push (VAPID)
- Mobile: Will need Firebase Cloud Messaging (FCM) for Android + APNs for iOS
- Consider adding `device_tokens` table and FCM/APNs integration

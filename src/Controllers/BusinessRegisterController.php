<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;
use GlamourSchedule\Core\PushNotification;
use GlamourSchedule\Core\GeoIP;

class BusinessRegisterController extends Controller
{
    // Pricing constants
    private const REGISTRATION_FEE = 99.99;
    private const SALES_PARTNER_DISCOUNT = 25.00;
    private const SALES_PARTNER_COMMISSION = 49.99;      // Commission for normal sales (€74.99 payment)
    private const EARLY_BIRD_COMMISSION = 9.99;          // Commission for early bird sales (€0.99 payment)
    private const COMMISSION_PAYOUT_DAYS = 14;           // Bedenktijd

    private GeoIP $geoIP;

    public function __construct()
    {
        parent::__construct();
        $this->geoIP = new GeoIP($this->db);
    }

    public function show(): string
    {
        if (isset($_SESSION['business_id'])) {
            return $this->redirect('/business/dashboard');
        }

        // Get user location based on IP
        $location = $this->geoIP->lookup();
        $countryCode = $location['country_code'];
        $promoInfo = $this->geoIP->getPromotionPrice($countryCode);

        // Log the visit
        $this->geoIP->logLocation($location, null, null, '/business/register');

        $categories = $this->getCategories();

        // Check for referral code in URL
        $referralCode = $_GET['ref'] ?? '';
        $hasValidReferral = false;
        $discountedFee = self::REGISTRATION_FEE;

        if (!empty($referralCode)) {
            $salesStmt = $this->db->query(
                "SELECT id FROM sales_users WHERE referral_code = ? AND status = 'active'",
                [$referralCode]
            );
            if ($salesStmt->fetch()) {
                $hasValidReferral = true;
                $discountedFee = self::REGISTRATION_FEE - self::SALES_PARTNER_DISCOUNT;
            }
        }

        // Determine final price: promo price takes priority over referral discount
        $regFee = $promoInfo['is_promo'] ? $promoInfo['price'] : ($hasValidReferral ? $discountedFee : self::REGISTRATION_FEE);

        return $this->view('pages/business/register', [
            'pageTitle' => 'Bedrijf Registreren',
            'categories' => $categories,
            'isEarlyAdopter' => $promoInfo['is_promo'],
            'earlyAdopterCount' => 100 - $promoInfo['spots_left'],
            'earlyAdopterSpots' => $promoInfo['spots_left'],
            'regFee' => $regFee,
            'standardFee' => self::REGISTRATION_FEE,
            'discountedFee' => $discountedFee,
            'hasValidReferral' => $hasValidReferral,
            'referralCode' => $referralCode,
            'salesPartnerDiscount' => self::SALES_PARTNER_DISCOUNT,
            // New geo-based data
            'countryCode' => $countryCode,
            'countryName' => $location['country_name'],
            'promoPrice' => $promoInfo['price'],
            'spotsLeft' => $promoInfo['spots_left'],
            'isPromo' => $promoInfo['is_promo'],
            'detectedLanguage' => $location['language']
        ]);
    }

    public function register(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/business/register?error=csrf');
        }

        $data = [
            'company_name' => trim($_POST['name'] ?? $_POST['company_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'street' => trim($_POST['address'] ?? $_POST['street'] ?? ''),
            'house_number' => trim($_POST['house_number'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'kvk_number' => trim($_POST['kvk_number'] ?? ''),
            'terms_accepted' => isset($_POST['terms'])
        ];

        $errors = $this->validateRegistration($data);

        // Check if email already exists (1 email = 1 account, either customer or business)
        $stmt = $this->db->query("SELECT id FROM businesses WHERE email = ?", [$data['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Dit e-mailadres is al in gebruik';
        }
        $stmt = $this->db->query("SELECT id FROM users WHERE email = ?", [$data['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Dit e-mailadres is al in gebruik';
        }

        if (!empty($errors)) {
            // Get user location for country-based pricing
            $location = $this->geoIP->lookup();
            $countryCode = $location['country_code'];
            $promoInfo = $this->geoIP->getPromotionPrice($countryCode);

            $referralCode = trim($_POST['referral_code'] ?? '');
            $hasValidReferral = false;

            if (!empty($referralCode)) {
                $salesStmt = $this->db->query(
                    "SELECT id FROM sales_users WHERE referral_code = ? AND status = 'active'",
                    [$referralCode]
                );
                $hasValidReferral = (bool)$salesStmt->fetch();
            }

            $regFee = $promoInfo['is_promo'] ? $promoInfo['price'] : ($hasValidReferral ? self::REGISTRATION_FEE - self::SALES_PARTNER_DISCOUNT : self::REGISTRATION_FEE);

            return $this->view('pages/business/register', [
                'pageTitle' => 'Bedrijf Registreren',
                'categories' => $this->getCategories(),
                'errors' => $errors,
                'data' => $data,
                'isEarlyAdopter' => $promoInfo['is_promo'],
                'earlyAdopterSpots' => $promoInfo['spots_left'],
                'regFee' => $regFee,
                'standardFee' => self::REGISTRATION_FEE,
                'discountedFee' => self::REGISTRATION_FEE - self::SALES_PARTNER_DISCOUNT,
                'hasValidReferral' => $hasValidReferral,
                'referralCode' => $referralCode,
                'salesPartnerDiscount' => self::SALES_PARTNER_DISCOUNT,
                'countryCode' => $countryCode,
                'countryName' => $location['country_name'],
                'promoPrice' => $promoInfo['price'],
                'spotsLeft' => $promoInfo['spots_left'],
                'isPromo' => $promoInfo['is_promo'],
                'detectedLanguage' => $location['language']
            ]);
        }

        try {
            $this->db->beginTransaction();

            // Generate UUIDs
            $userUuid = $this->generateUuid();
            $businessUuid = $this->generateUuid();
            $slug = $this->generateSlug($data['company_name']);
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

            // Get user location for country-based pricing and language detection
            $location = $this->geoIP->lookup();
            $countryCode = $location['country_code'];
            $detectedLanguage = $location['language'] ?? 'nl';
            $promoInfo = $this->geoIP->getPromotionPrice($countryCode);
            $isPromo = $promoInfo['is_promo'];

            // Validate language
            $validLangs = ['nl', 'en', 'de', 'fr', 'es', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar', 'tr', 'pl', 'sv', 'no', 'da', 'fi', 'el', 'cs', 'hu', 'ro', 'bg', 'hr', 'sk', 'sl', 'et', 'lv', 'lt', 'uk', 'hi', 'th', 'vi', 'id', 'ms', 'tl', 'he', 'fa', 'sw', 'af'];
            if (!in_array($detectedLanguage, $validLangs)) {
                $detectedLanguage = 'nl';
            }

            // Business accounts are separate from customer accounts - no user record needed

            // Log the registration attempt
            $this->geoIP->logLocation($location, null, null, '/business/register [POST]');

            // Check for referral code and calculate pricing
            $referralCode = trim($_POST['referral_code'] ?? '');
            $welcomeDiscount = 0.00;
            $referredBy = null;
            $hasSalesPartnerReferral = false;

            if (!empty($referralCode)) {
                // Check if referral code exists in sales_users
                $salesStmt = $this->db->query(
                    "SELECT id, commission_rate FROM sales_users WHERE referral_code = ? AND status = 'active'",
                    [$referralCode]
                );
                $salesUser = $salesStmt->fetch(\PDO::FETCH_ASSOC);
                if ($salesUser) {
                    $referredBy = $salesUser['id'];
                    $welcomeDiscount = self::SALES_PARTNER_DISCOUNT;
                    $hasSalesPartnerReferral = true;
                }
            }

            // Determine registration fee and trial period
            // - Country promo (first 100 per country): €0.99 with trial
            // - Sales partner referral: discounted price (NO trial - direct payment)
            // - Normal: €99.99 with trial
            if ($isPromo) {
                $regFee = $promoInfo['price']; // €0.99
                $trialEndsAt = date('Y-m-d', strtotime('+14 days'));
                $subscriptionStatus = 'trial';
            } elseif ($hasSalesPartnerReferral) {
                // Sales partner referral: NO trial, direct payment required
                $regFee = self::REGISTRATION_FEE - self::SALES_PARTNER_DISCOUNT;
                $trialEndsAt = null; // No trial
                $subscriptionStatus = 'pending'; // Waiting for payment
            } else {
                $regFee = self::REGISTRATION_FEE;
                $trialEndsAt = date('Y-m-d', strtotime('+14 days'));
                $subscriptionStatus = 'trial';
            }

            // Create business (pending status until verified) with detected language
            // Business has its own password_hash - separate from customer accounts
            $this->db->query(
                "INSERT INTO businesses (
                    uuid, company_name, slug, email, password_hash, phone,
                    street, house_number, postal_code, city, language,
                    description, kvk_number,
                    is_early_adopter, registration_fee_paid, status,
                    trial_ends_at, subscription_status, subscription_price, welcome_discount,
                    referral_code, referred_by_sales_partner,
                    registration_country, registration_ip, promo_applied
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $businessUuid, $data['company_name'], $slug, $data['email'], $passwordHash, $data['phone'],
                    $data['street'], $data['house_number'], $data['postal_code'], $data['city'], $detectedLanguage,
                    $data['description'], $data['kvk_number'],
                    $isPromo ? 1 : 0,
                    $trialEndsAt, $subscriptionStatus, $regFee, $welcomeDiscount, $referralCode ?: null, $referredBy,
                    $countryCode, $location['ip'], $isPromo ? 1 : 0
                ]
            );
            $businessId = $this->db->lastInsertId();

            // Increment country registration count if promo was applied
            if ($isPromo) {
                $this->geoIP->incrementRegistrationCount($countryCode);
            }

            // If referred by sales partner, create referral record with 14-day payout delay
            if ($referredBy) {
                // Commission eligible after 14 days from payment
                // Early bird sales get €9.99 commission, normal sales get €49.99
                $commission = $isPromo ? self::EARLY_BIRD_COMMISSION : self::SALES_PARTNER_COMMISSION;
                $this->db->query(
                    "INSERT INTO sales_referrals (sales_user_id, business_id, status, commission)
                     VALUES (?, ?, 'pending', ?)",
                    [$referredBy, $businessId, $commission]
                );

                // Notify sales partner of new registration
                $this->notifySalesPartnerNewRegistration($referredBy, $businessId, $commission);
            }

            // Add to category
            if ($data['category_id'] > 0) {
                $this->db->query(
                    "INSERT INTO business_categories (business_id, category_id) VALUES (?, ?)",
                    [$businessId, $data['category_id']]
                );
            }

            // Record early adopter (country-based promo)
            if ($isPromo) {
                $this->db->query(
                    "INSERT INTO early_adopters (business_id, position, country_code) VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE position = VALUES(position)",
                    [$businessId, 100 - $promoInfo['spots_left'] + 1, $countryCode]
                );
            }

            // Create default business hours
            $defaultHours = [
                ['day' => 0, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 1, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 2, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 3, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 4, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 5, 'open' => '10:00:00', 'close' => '17:00:00', 'closed' => 0],
                ['day' => 6, 'open' => '00:00:00', 'close' => '00:00:00', 'closed' => 1],
            ];

            foreach ($defaultHours as $hours) {
                $this->db->query(
                    "INSERT INTO business_hours (business_id, day_of_week, open_time, close_time, is_closed)
                     VALUES (?, ?, ?, ?, ?)",
                    [$businessId, $hours['day'], $hours['open'], $hours['close'], $hours['closed']]
                );
            }

            // Generate 6-digit verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Store verification code (user_id is NULL for business registrations)
            $this->db->query(
                "INSERT INTO email_verifications (user_id, business_id, email, verification_code, expires_at)
                 VALUES (NULL, ?, ?, ?, ?)",
                [$businessId, $data['email'], $verificationCode, $expiresAt]
            );

            $this->db->commit();

            // Send push notification to admins about new business
            try {
                $push = new PushNotification();
                $push->notifyNewBusiness([
                    'company_name' => $data['company_name'],
                    'city' => $data['city']
                ]);
            } catch (\Exception $e) {
                error_log('Push notification failed: ' . $e->getMessage());
            }

            // Send verification email
            $this->sendVerificationEmail($data['email'], $data['company_name'], $verificationCode);

            // Store email in session for verification page
            $_SESSION['pending_verification_email'] = $data['email'];
            $_SESSION['pending_business_id'] = $businessId;

            // Redirect to verification page
            return $this->redirect('/verify-email');

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Business registration error: " . $e->getMessage());

            $earlyAdopterCount = $this->getEarlyAdopterCount();
            $isEarlyAdopter = $earlyAdopterCount < 100;
            $referralCode = trim($_POST['referral_code'] ?? '');
            $hasValidReferral = false;

            if (!empty($referralCode)) {
                $salesStmt = $this->db->query(
                    "SELECT id FROM sales_users WHERE referral_code = ? AND status = 'active'",
                    [$referralCode]
                );
                $hasValidReferral = (bool)$salesStmt->fetch();
            }

            return $this->view('pages/business/register', [
                'pageTitle' => 'Bedrijf Registreren',
                'categories' => $this->getCategories(),
                'errors' => ['general' => 'Er is een fout opgetreden bij de registratie. Probeer het opnieuw.'],
                'data' => $data,
                'isEarlyAdopter' => $isEarlyAdopter,
                'earlyAdopterSpots' => 100 - $earlyAdopterCount,
                'regFee' => $isEarlyAdopter ? 0.99 : ($hasValidReferral ? self::REGISTRATION_FEE - self::SALES_PARTNER_DISCOUNT : self::REGISTRATION_FEE),
                'standardFee' => self::REGISTRATION_FEE,
                'discountedFee' => self::REGISTRATION_FEE - self::SALES_PARTNER_DISCOUNT,
                'hasValidReferral' => $hasValidReferral,
                'referralCode' => $referralCode,
                'salesPartnerDiscount' => self::SALES_PARTNER_DISCOUNT
            ]);
        }
    }

    private function sendVerificationEmail(string $email, string $companyName, string $code): void
    {
        $subject = "Bevestig je GlamourSchedule account - Code: {$code}";

        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:#0a0a0a;padding:20px;'>
                <tr>
                    <td align='center'>
                        <table width='500' cellpadding='0' cellspacing='0' style='background:#1a1a1a;border-radius:10px;overflow:hidden;'>
                            <tr>
                                <td style='background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:30px;text-align:center;'>
                                    <h1 style='margin:0;font-size:24px;'>Bevestig je account</h1>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:40px;text-align:center;'>
                                    <p style='font-size:16px;color:#ffffff;margin-bottom:10px;'>Beste <strong>{$companyName}</strong>,</p>
                                    <p style='font-size:16px;color:#ffffff;margin-bottom:30px;'>Gebruik onderstaande code om je account te bevestigen:</p>

                                    <div style='background:#000000;border:2px solid #333333;border-radius:10px;padding:30px;margin:20px 0;'>
                                        <span style='font-size:42px;font-weight:bold;letter-spacing:8px;color:#ffffff;font-family:monospace;'>{$code}</span>
                                    </div>

                                    <p style='font-size:14px;color:#cccccc;margin-top:30px;'>Deze code is 10 minuten geldig.</p>
                                    <p style='font-size:14px;color:#cccccc;'>Heb je deze code niet aangevraagd? Negeer dan deze email.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style='background:#0a0a0a;padding:20px;text-align:center;color:#cccccc;font-size:12px;'>
                                    <p style='margin:0;'>&copy; " . date('Y') . " GlamourSchedule</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $textBody = "
Bevestig je GlamourSchedule account

Beste {$companyName},

Gebruik onderstaande code om je account te bevestigen:

{$code}

Deze code is 10 minuten geldig.

Heb je deze code niet aangevraagd? Negeer dan deze email.

GlamourSchedule
        ";

        try {
            $mailer = new Mailer();
            $mailer->send($email, $subject, $htmlBody, $textBody);
        } catch (\Exception $e) {
            error_log("Failed to send verification email: " . $e->getMessage());
        }
    }

    /**
     * Send verification email with link and temporary password (for free trial registration)
     */
    private function sendVerificationEmailWithLink(string $email, string $firstName, string $companyName, string $verifyUrl, string $tempPassword): void
    {
        $subject = "Voltooi je GlamourSchedule registratie - 14 dagen gratis!";

        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background:#0a0a0a;padding:20px;'>
                <tr>
                    <td align='center'>
                        <table width='500' cellpadding='0' cellspacing='0' style='background:#1a1a1a;border-radius:10px;overflow:hidden;'>
                            <tr>
                                <td style='background:linear-gradient(135deg,#000000,#333333);color:#ffffff;padding:30px;text-align:center;'>
                                    <h1 style='margin:0;font-size:24px;'>Welkom bij GlamourSchedule!</h1>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:40px;'>
                                    <p style='font-size:16px;color:#ffffff;margin-bottom:20px;'>Beste <strong>{$firstName}</strong>,</p>
                                    <p style='font-size:16px;color:#ffffff;margin-bottom:20px;'>Bedankt voor je registratie van <strong>{$companyName}</strong>!</p>

                                    <div style='background:#ecfdf5;border:2px solid #000000;border-radius:10px;padding:20px;margin:20px 0;text-align:center;'>
                                        <p style='margin:0;color:#047857;font-weight:600;font-size:18px;'>🎉 14 dagen GRATIS proberen!</p>
                                        <p style='margin:10px 0 0;color:#ffffff;font-size:14px;'>Geen betaling vooraf nodig</p>
                                    </div>

                                    <p style='font-size:16px;color:#ffffff;margin-bottom:20px;'>Klik op de knop hieronder om je registratie te voltooien:</p>

                                    <div style='text-align:center;margin:30px 0;'>
                                        <a href='{$verifyUrl}' style='display:inline-block;background:linear-gradient(135deg,#000000,#333333);color:#ffffff;text-decoration:none;padding:15px 40px;border-radius:10px;font-weight:600;font-size:16px;'>Registratie Voltooien</a>
                                    </div>

                                    <div style='background:#f8f9fa;border-radius:10px;padding:20px;margin:20px 0;'>
                                        <p style='margin:0 0 10px;color:#ffffff;font-weight:600;'>Je tijdelijke inloggegevens:</p>
                                        <p style='margin:5px 0;color:#cccccc;'>E-mail: <strong>{$email}</strong></p>
                                        <p style='margin:5px 0;color:#cccccc;'>Wachtwoord: <strong style='font-family:monospace;background:#e5e7eb;padding:2px 8px;border-radius:4px;'>{$tempPassword}</strong></p>
                                        <p style='margin:10px 0 0;color:#999;font-size:13px;'>Je kunt je wachtwoord later wijzigen in je dashboard.</p>
                                    </div>

                                    <p style='font-size:14px;color:#cccccc;margin-top:20px;'>Of kopieer deze link in je browser:<br><a href='{$verifyUrl}' style='color:#ffffff;word-break:break-all;'>{$verifyUrl}</a></p>
                                </td>
                            </tr>
                            <tr>
                                <td style='background:#0a0a0a;padding:20px;text-align:center;color:#cccccc;font-size:12px;'>
                                    <p style='margin:0;'>&copy; " . date('Y') . " GlamourSchedule | KVK: 81973667</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $textBody = "
Welkom bij GlamourSchedule!

Beste {$firstName},

Bedankt voor je registratie van {$companyName}!

🎉 14 dagen GRATIS proberen! Geen betaling vooraf nodig.

Klik op de link hieronder om je registratie te voltooien:
{$verifyUrl}

Je tijdelijke inloggegevens:
E-mail: {$email}
Wachtwoord: {$tempPassword}

Je kunt je wachtwoord later wijzigen in je dashboard.

Met vriendelijke groet,
GlamourSchedule
        ";

        try {
            $mailer = new Mailer();
            $mailer->send($email, $subject, $htmlBody, $textBody);
        } catch (\Exception $e) {
            error_log("Failed to send verification email with link: " . $e->getMessage());
        }
    }

    private function validateRegistration(array $data): array
    {
        $errors = [];

        if (empty($data['company_name']) || strlen($data['company_name']) < 2) {
            $errors['name'] = 'Bedrijfsnaam is verplicht (minimaal 2 karakters)';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Voer een geldig e-mailadres in';
        }

        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors['password'] = 'Wachtwoord moet minimaal 8 karakters zijn';
        }

        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Wachtwoorden komen niet overeen';
        }

        if (empty($data['street'])) {
            $errors['address'] = 'Adres is verplicht';
        }

        if (empty($data['city'])) {
            $errors['city'] = 'Plaats is verplicht';
        }

        if (empty($data['postal_code'])) {
            $errors['postal_code'] = 'Postcode is verplicht';
        }

        if (!$data['terms_accepted']) {
            $errors['terms'] = 'Je moet akkoord gaan met de algemene voorwaarden';
        }

        if (empty($data['category_id']) || $data['category_id'] < 1) {
            $errors['category_id'] = 'Selecteer een categorie';
        }

        // KVK is optional but if provided, must be 8 digits
        if (!empty($data['kvk_number']) && !preg_match('/^\d{8}$/', $data['kvk_number'])) {
            $errors['kvk_number'] = 'KVK-nummer moet 8 cijfers bevatten';
        }

        return $errors;
    }

    private function getCategories(): array
    {
        $stmt = $this->db->query(
            "SELECT c.*, ct.name as translated_name
             FROM categories c
             LEFT JOIN category_translations ct ON c.id = ct.category_id AND ct.language = ?
             WHERE c.is_active = 1
             ORDER BY c.sort_order",
            [$this->lang]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getEarlyAdopterCount(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM early_adopters");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)$result['cnt'];
    }

    private function getSalesEarlyAdopterCount(): int
    {
        // Count businesses registered through sales partners (with is_early_adopter = 1 and referred_by_sales_partner IS NOT NULL)
        $stmt = $this->db->query(
            "SELECT COUNT(*) as cnt FROM businesses WHERE referred_by_sales_partner IS NOT NULL AND is_early_adopter = 1"
        );
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)$result['cnt'];
    }

    private function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $original = $slug;
        $i = 1;
        while ($this->slugExists($slug)) {
            $slug = $original . '-' . $i;
            $i++;
        }
        return $slug;
    }

    private function slugExists(string $slug): bool
    {
        $stmt = $this->db->query("SELECT id FROM businesses WHERE slug = ?", [$slug]);
        return (bool)$stmt->fetch();
    }

    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Show partner registration form (with €25 discount or €0.99 early adopter)
     */
    public function showPartnerRegister(): string
    {
        if (isset($_SESSION['business_id'])) {
            return $this->redirect('/business/dashboard');
        }

        $referralCode = $_GET['ref'] ?? '';
        $salesPartner = null;

        if (!empty($referralCode)) {
            $stmt = $this->db->query(
                "SELECT id, name FROM sales_users WHERE referral_code = ? AND status = 'active'",
                [$referralCode]
            );
            $salesPartner = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        // Check sales early adopter status (first 100 via sales get €0.99)
        $salesEarlyAdopterCount = $this->getSalesEarlyAdopterCount();
        $isSalesEarlyAdopter = $salesEarlyAdopterCount < 100;
        $regFee = $isSalesEarlyAdopter ? 0.99 : 74.99;

        return $this->view('pages/business/register-sales', [
            'pageTitle' => 'Registreer met Korting - GlamourSchedule',
            'categories' => $this->getCategories(),
            'referralCode' => $referralCode,
            'salesPartner' => $salesPartner,
            'isSalesEarlyAdopter' => $isSalesEarlyAdopter,
            'salesEarlyAdopterCount' => $salesEarlyAdopterCount,
            'salesEarlyAdopterSpots' => 100 - $salesEarlyAdopterCount,
            'regFee' => $regFee,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Process partner registration (simplified: salon name, first/last name, email, then payment)
     */
    public function partnerRegister(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/partner/register?error=csrf');
        }

        $referralCode = trim($_POST['referral_code'] ?? '');

        $data = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'terms' => isset($_POST['terms'])
        ];

        $errors = [];

        if (empty($data['company_name'])) {
            $errors['company_name'] = 'Salonnaam is verplicht';
        }
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'Voornaam is verplicht';
        }
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Achternaam is verplicht';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Geldig e-mailadres is verplicht';
        }
        if (!$data['terms']) {
            $errors['terms'] = 'Je moet akkoord gaan met de algemene voorwaarden';
        }

        // Check if email exists (1 email = 1 account, either customer or business)
        $stmt = $this->db->query("SELECT id FROM businesses WHERE email = ?", [$data['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Dit e-mailadres is al in gebruik';
        }
        $stmt = $this->db->query("SELECT id FROM users WHERE email = ?", [$data['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Dit e-mailadres is al in gebruik';
        }

        // Get sales partner info
        $salesPartner = null;
        if (!empty($referralCode)) {
            $stmt = $this->db->query(
                "SELECT id, name FROM sales_users WHERE referral_code = ? AND status = 'active'",
                [$referralCode]
            );
            $salesPartner = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        // Check sales early adopter status
        $salesEarlyAdopterCount = $this->getSalesEarlyAdopterCount();
        $isSalesEarlyAdopter = $salesEarlyAdopterCount < 100;

        if (!empty($errors)) {
            return $this->view('pages/business/register-sales', [
                'pageTitle' => 'Registreer met Korting - GlamourSchedule',
                'errors' => $errors,
                'data' => $data,
                'referralCode' => $referralCode,
                'salesPartner' => $salesPartner,
                'isSalesEarlyAdopter' => $isSalesEarlyAdopter,
                'salesEarlyAdopterCount' => $salesEarlyAdopterCount,
                'salesEarlyAdopterSpots' => 100 - $salesEarlyAdopterCount,
                'regFee' => $isSalesEarlyAdopter ? 0.99 : 74.99,
                'csrfToken' => $this->csrf()
            ]);
        }

        try {
            $this->db->beginTransaction();

            // Generate UUIDs and temp password
            $userUuid = $this->generateUuid();
            $businessUuid = $this->generateUuid();
            $slug = $this->generateSlug($data['company_name']);
            $tempPassword = bin2hex(random_bytes(8));
            $passwordHash = password_hash($tempPassword, PASSWORD_BCRYPT);
            $fullName = $data['first_name'] . ' ' . $data['last_name'];

            // Detect language from IP
            $location = $this->geoIP->lookup();
            $detectedLanguage = $location['language'] ?? 'nl';
            $validLangs = ['nl', 'en', 'de', 'fr', 'es', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar', 'tr', 'pl', 'sv', 'no', 'da', 'fi', 'el', 'cs', 'hu', 'ro', 'bg', 'hr', 'sk', 'sl', 'et', 'lv', 'lt', 'uk', 'hi', 'th', 'vi', 'id', 'ms', 'tl', 'he', 'fa', 'sw', 'af'];
            if (!in_array($detectedLanguage, $validLangs)) {
                $detectedLanguage = 'nl';
            }

            // Business accounts are separate from customer accounts - no user record needed

            // Get sales partner ID
            $referredBy = $salesPartner ? $salesPartner['id'] : null;

            // Determine pricing - First 100 via sales get €0.99, rest get €74.99
            $regFee = $isSalesEarlyAdopter ? 0.99 : 74.99;
            $welcomeDiscount = $isSalesEarlyAdopter ? 99.00 : 25.00; // €99 korting for early adopters

            // Generate verification token for email verification
            $verificationToken = bin2hex(random_bytes(32));
            $trialEndsAt = date('Y-m-d', strtotime('+14 days'));

            // Business has its own password_hash - separate from customer accounts
            $this->db->query(
                "INSERT INTO businesses (
                    uuid, company_name, slug, email, password_hash, language,
                    is_early_adopter, registration_fee_paid, status,
                    trial_ends_at, subscription_status, subscription_price, welcome_discount,
                    referral_code, referred_by_sales_partner, verification_token
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 'pending', ?, 'trial', ?, ?, ?, ?, ?)",
                [
                    $businessUuid, $data['company_name'], $slug, $data['email'], $passwordHash, $detectedLanguage,
                    $isSalesEarlyAdopter ? 1 : 0,
                    $trialEndsAt, $regFee, $welcomeDiscount, $referralCode ?: null, $referredBy, $verificationToken
                ]
            );
            $businessId = $this->db->lastInsertId();

            // Create referral record
            if ($referredBy) {
                // Early bird sales get €9.99 commission, normal sales get €49.99
                $commission = $isSalesEarlyAdopter ? self::EARLY_BIRD_COMMISSION : self::SALES_PARTNER_COMMISSION;
                $this->db->query(
                    "INSERT INTO sales_referrals (sales_user_id, business_id, status, commission)
                     VALUES (?, ?, 'pending', ?)",
                    [$referredBy, $businessId, $commission]
                );

                // Notify sales partner of new registration
                $this->notifySalesPartnerNewRegistration($referredBy, $businessId, $commission);
            }

            // Create default hours
            $defaultHours = [
                ['day' => 0, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 1, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 2, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 3, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 4, 'open' => '09:00:00', 'close' => '18:00:00', 'closed' => 0],
                ['day' => 5, 'open' => '10:00:00', 'close' => '17:00:00', 'closed' => 0],
                ['day' => 6, 'open' => '00:00:00', 'close' => '00:00:00', 'closed' => 1],
            ];

            foreach ($defaultHours as $hours) {
                $this->db->query(
                    "INSERT INTO business_hours (business_id, day_of_week, open_time, close_time, is_closed)
                     VALUES (?, ?, ?, ?, ?)",
                    [$businessId, $hours['day'], $hours['open'], $hours['close'], $hours['closed']]
                );
            }

            $this->db->commit();

            // Store session data (business accounts are separate - no user_id needed)
            $_SESSION['partner_register_business_id'] = $businessId;
            $_SESSION['partner_register_temp_password'] = $tempPassword;
            $_SESSION['partner_register_email'] = $data['email'];
            $_SESSION['partner_register_name'] = $data['first_name'];

            // Send verification email (no payment required)
            try {
                $verifyUrl = 'https://glamourschedule.nl/partner/complete/' . $verificationToken;
                $this->sendVerificationEmailWithLink($data['email'], $data['first_name'], $data['company_name'], $verifyUrl, $tempPassword);

                return $this->view('pages/business/register-success', [
                    'pageTitle' => 'Registratie Gelukt - GlamourSchedule',
                    'email' => $data['email'],
                    'companyName' => $data['company_name']
                ]);
            } catch (\Exception $e) {
                error_log('Email send error: ' . $e->getMessage());
                // Still redirect to success, they can request a new email
                return $this->view('pages/business/register-success', [
                    'pageTitle' => 'Registratie Gelukt - GlamourSchedule',
                    'email' => $data['email'],
                    'companyName' => $data['company_name']
                ]);
            }

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Partner registration error: " . $e->getMessage());

            return $this->view('pages/business/register-sales', [
                'pageTitle' => 'Registreer met Korting - GlamourSchedule',
                'errors' => ['general' => 'Er is een fout opgetreden. Probeer het opnieuw.'],
                'data' => $data,
                'referralCode' => $referralCode,
                'salesPartner' => $salesPartner,
                'csrfToken' => $this->csrf()
            ]);
        }
    }

    /**
     * Handle partner payment completion (after Mollie redirect)
     */
    public function partnerPaymentComplete(): string
    {
        $businessId = $_SESSION['partner_register_business_id'] ?? null;
        $email = $_SESSION['partner_register_email'] ?? null;
        $firstName = $_SESSION['partner_register_name'] ?? null;

        if (!$businessId) {
            return $this->redirect('/partner/register?error=session');
        }

        // Get business and check payment
        $stmt = $this->db->query("SELECT * FROM businesses WHERE id = ?", [$businessId]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business || !$business['payment_id']) {
            return $this->redirect('/partner/register?error=notfound');
        }

        // If session data is missing, get from database
        if (empty($email)) {
            $email = $business['email'];
        }
        if (empty($firstName)) {
            $firstName = $business['company_name'] ?? '';
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($_ENV['MOLLIE_API_KEY']);

            $payment = $mollie->payments->get($business['payment_id']);

            if ($payment->isPaid()) {
                // Generate completion token
                $completionToken = bin2hex(random_bytes(32));

                // Get actual payment amount from Mollie
                $paidAmount = (float) $payment->amount->value;

                // Set 14-day trial starting now
                $trialEndsAt = date('Y-m-d', strtotime('+14 days'));

                // Mark as paid, set trial and completion token
                $this->db->query(
                    "UPDATE businesses SET registration_fee_paid = ?, verification_token = ?, trial_ends_at = ?, subscription_status = 'trial' WHERE id = ?",
                    [$paidAmount, $completionToken, $trialEndsAt, $businessId]
                );

                // Update referral status if applicable
                if ($business['referred_by_sales_partner']) {
                    $this->db->query(
                        "UPDATE sales_referrals SET status = 'converted' WHERE business_id = ?",
                        [$businessId]
                    );
                    // Notify sales partner of conversion
                    $this->notifySalesPartnerConversion($businessId);
                }

                // Send completion email
                $this->sendCompletionEmail($email, $firstName, $business['company_name'], $completionToken);

                // Clear session
                unset($_SESSION['partner_register_business_id']);
                unset($_SESSION['partner_register_user_id']);
                unset($_SESSION['partner_register_temp_password']);
                unset($_SESSION['partner_register_email']);
                unset($_SESSION['partner_register_name']);

                return $this->view('pages/business/payment-success', [
                    'pageTitle' => 'Betaling Geslaagd',
                    'email' => $email,
                    'firstName' => $firstName,
                    'companyName' => $business['company_name']
                ]);
            } else {
                return $this->view('pages/business/payment-failed', [
                    'pageTitle' => 'Betaling Mislukt'
                ]);
            }
        } catch (\Exception $e) {
            error_log('Payment check error: ' . $e->getMessage());
            return $this->redirect('/partner/register?error=payment');
        }
    }

    /**
     * Webhook for partner payment (called by Mollie)
     */
    public function partnerPaymentWebhook(): string
    {
        $paymentId = $_POST['id'] ?? null;
        if (!$paymentId) {
            http_response_code(400);
            return '';
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($_ENV['MOLLIE_API_KEY']);

            $payment = $mollie->payments->get($paymentId);
            $businessId = $payment->metadata->business_id ?? null;

            if ($payment->isPaid() && $businessId) {
                // Check if not already processed
                $stmt = $this->db->query(
                    "SELECT registration_fee_paid, verification_token, email, company_name FROM businesses WHERE id = ?",
                    [$businessId]
                );
                $business = $stmt->fetch(\PDO::FETCH_ASSOC);

                // Only process if not yet paid (registration_fee_paid = 0)
                if ($business && $business['registration_fee_paid'] == 0) {
                    // Generate completion token if not exists
                    $completionToken = $business['verification_token'] ?: bin2hex(random_bytes(32));

                    // Get actual payment amount from Mollie
                    $paidAmount = (float) $payment->amount->value;

                    // Set 14-day trial starting now
                    $trialEndsAt = date('Y-m-d', strtotime('+14 days'));

                    $this->db->query(
                        "UPDATE businesses SET registration_fee_paid = ?, verification_token = ?, trial_ends_at = ?, subscription_status = 'trial' WHERE id = ?",
                        [$paidAmount, $completionToken, $trialEndsAt, $businessId]
                    );

                    // Update referral status and notify sales partner
                    $this->db->query(
                        "UPDATE sales_referrals SET status = 'converted' WHERE business_id = ?",
                        [$businessId]
                    );
                    $this->notifySalesPartnerConversion($businessId);

                    // Get business info for email (businesses are separate from customers)
                    $firstName = $business['company_name'] ?? 'Klant';

                    // Send completion email
                    $this->sendCompletionEmail($business['email'], $firstName, $business['company_name'], $completionToken);
                }
            }

            http_response_code(200);
            return '';
        } catch (\Exception $e) {
            error_log('Partner payment webhook error: ' . $e->getMessage());
            http_response_code(500);
            return '';
        }
    }

    /**
     * Send completion email with link to finish registration
     */
    private function sendCompletionEmail(string $email, string $firstName, string $companyName, string $token): void
    {
        $completionUrl = "https://glamourschedule.nl/partner/complete/{$token}";

        $html = "
        <div style='font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;max-width:600px;margin:0 auto'>
            <div style='background:linear-gradient(135deg,#000000,#1a1a1a);padding:2rem;text-align:center;border-radius:12px 12px 0 0'>
                <h1 style='color:#ffffff;margin:0;font-size:1.5rem'>Welkom bij GlamourSchedule!</h1>
            </div>
            <div style='background:#0a0a0a;padding:2rem;border:1px solid #333;border-top:none;border-radius:0 0 12px 12px'>
                <p style='color:#374151;font-size:1.1rem;margin-top:0'>Beste {$firstName},</p>
                <p style='color:#374151;line-height:1.6'>
                    Welkom! Je registratie voor <strong>{$companyName}</strong> is bijna compleet.
                </p>

                <div style='background:#fef3c7;border:2px solid #f59e0b;border-radius:12px;padding:1.5rem;margin:1.5rem 0'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:1rem'>
                        Je proefperiode van 14 dagen start nu!
                    </p>
                    <p style='margin:0.75rem 0 0 0;color:#92400e;font-size:0.9rem'>
                        Probeer GlamourSchedule 14 dagen gratis. Je hoeft pas te betalen na de proefperiode.
                    </p>
                </div>

                <div style='background:#ecfdf5;border:2px solid #333333;border-radius:12px;padding:1.5rem;margin:1.5rem 0'>
                    <p style='margin:0 0 1rem 0;color:#ffffff;font-weight:600'>Voltooi nu je registratie:</p>
                    <p style='margin:0;color:#374151;font-size:0.95rem'>
                        Klik op de knop hieronder om je wachtwoord in te stellen en je bedrijfsgegevens aan te vullen.
                    </p>
                </div>

                <div style='text-align:center;margin:2rem 0'>
                    <a href='{$completionUrl}' style='display:inline-block;background:linear-gradient(135deg,#333333,#000000);color:white;text-decoration:none;padding:1rem 2rem;border-radius:10px;font-weight:600;font-size:1.1rem'>
                        Registratie Afronden
                    </a>
                </div>

                <p style='color:#6b7280;font-size:0.9rem'>
                    Of kopieer deze link: <span style='color:#ffffff'>{$completionUrl}</span>
                </p>

                <div style='border-top:1px solid #e5e7eb;margin-top:1.5rem;padding-top:1.5rem'>
                    <p style='color:#6b7280;font-size:0.85rem;margin:0'>
                        <strong>Wat je nodig hebt:</strong><br>
                        - Een nieuw wachtwoord<br>
                        - Je bedrijfsadres<br>
                        - Je telefoonnummer (optioneel)<br>
                        - Je KvK-nummer (optioneel)
                    </p>
                </div>
            </div>
        </div>
        ";

        try {
            $mailer = new Mailer();
            $mailer->send($email, 'Voltooi je registratie - GlamourSchedule', $html);
        } catch (\Exception $e) {
            error_log('Completion email failed: ' . $e->getMessage());
        }
    }

    /**
     * Show completion form to set password and add business details
     */
    public function showCompleteRegistration(string $token): string
    {
        // Find business by token (allow early adopters or paid registrations)
        // Businesses are separate from customers - no JOIN with users needed
        $stmt = $this->db->query(
            "SELECT * FROM businesses
             WHERE verification_token = ? AND (registration_fee_paid > 0 OR is_early_adopter = 1)",
            [$token]
        );
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            return $this->view('pages/business/complete-invalid', [
                'pageTitle' => 'Ongeldige Link'
            ]);
        }

        // Check if already completed (has password set via this form)
        if ($business['status'] === 'active') {
            return $this->redirect('/login?message=already_registered');
        }

        return $this->view('pages/business/complete-registration', [
            'pageTitle' => 'Registratie Voltooien',
            'business' => $business,
            'token' => $token,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Process registration completion form
     */
    public function completeRegistration(string $token): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect("/partner/complete/{$token}?error=csrf");
        }

        // Find business by token (allow early adopters or paid registrations)
        // Businesses are separate from customers - no JOIN with users needed
        $stmt = $this->db->query(
            "SELECT * FROM businesses
             WHERE verification_token = ? AND (registration_fee_paid > 0 OR is_early_adopter = 1)",
            [$token]
        );
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            return $this->view('pages/business/complete-invalid', [
                'pageTitle' => 'Ongeldige Link'
            ]);
        }

        $data = [
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'phone' => trim($_POST['phone'] ?? ''),
            'street' => trim($_POST['street'] ?? ''),
            'house_number' => trim($_POST['house_number'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'kvk_number' => trim($_POST['kvk_number'] ?? ''),
            'btw_number' => trim($_POST['btw_number'] ?? '')
        ];

        $errors = [];

        // Validate password
        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors['password'] = 'Wachtwoord moet minimaal 8 tekens zijn';
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Wachtwoorden komen niet overeen';
        }

        // Validate required address fields
        if (empty($data['street'])) {
            $errors['street'] = 'Straat is verplicht';
        }
        if (empty($data['house_number'])) {
            $errors['house_number'] = 'Huisnummer is verplicht';
        }
        if (empty($data['postal_code'])) {
            $errors['postal_code'] = 'Postcode is verplicht';
        }
        if (empty($data['city'])) {
            $errors['city'] = 'Plaats is verplicht';
        }

        if (!empty($errors)) {
            return $this->view('pages/business/complete-registration', [
                'pageTitle' => 'Registratie Voltooien',
                'business' => $business,
                'token' => $token,
                'errors' => $errors,
                'data' => $data,
                'csrfToken' => $this->csrf()
            ]);
        }

        try {
            $this->db->beginTransaction();

            // Update business details including password (businesses are separate from customers)
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            $this->db->query(
                "UPDATE businesses SET
                    password_hash = ?,
                    phone = ?,
                    street = ?,
                    house_number = ?,
                    postal_code = ?,
                    city = ?,
                    kvk_number = ?,
                    btw_number = ?,
                    status = 'active',
                    verification_token = NULL,
                    trial_ends_at = DATE_ADD(NOW(), INTERVAL 14 DAY)
                WHERE id = ?",
                [
                    $passwordHash,
                    $data['phone'],
                    $data['street'],
                    $data['house_number'],
                    $data['postal_code'],
                    $data['city'],
                    $data['kvk_number'],
                    $data['btw_number'],
                    $business['id']
                ]
            );

            $this->db->commit();

            // Send welcome email
            $this->sendWelcomeEmail($business['email'], $business['company_name']);

            return $this->view('pages/business/complete-success', [
                'pageTitle' => 'Registratie Voltooid',
                'business' => $business
            ]);

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Registration completion error: ' . $e->getMessage());

            return $this->view('pages/business/complete-registration', [
                'pageTitle' => 'Registratie Voltooien',
                'business' => $business,
                'token' => $token,
                'errors' => ['general' => 'Er is een fout opgetreden. Probeer het opnieuw.'],
                'data' => $data,
                'csrfToken' => $this->csrf()
            ]);
        }
    }

    /**
     * Send welcome email after registration is complete
     */
    private function sendWelcomeEmail(string $email, string $companyName): void
    {
        $loginUrl = 'https://glamourschedule.nl/login';
        $dashboardUrl = 'https://glamourschedule.nl/business/dashboard';

        $html = "
        <div style='font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;max-width:600px;margin:0 auto'>
            <div style='background:linear-gradient(135deg,#000000,#000000);padding:2rem;text-align:center;border-radius:12px 12px 0 0'>
                <h1 style='color:#ffffff;margin:0;font-size:1.5rem'>Welkom bij GlamourSchedule!</h1>
            </div>
            <div style='background:#0a0a0a;padding:2rem;border:1px solid #333;border-top:none;border-radius:0 0 12px 12px'>
                <p style='color:#374151;font-size:1.1rem;margin-top:0'>
                    Gefeliciteerd! Je registratie voor <strong>{$companyName}</strong> is nu compleet.
                </p>

                <div style='background:#ecfdf5;border:2px solid #333333;border-radius:12px;padding:1.5rem;margin:1.5rem 0'>
                    <p style='margin:0;color:#ffffff;font-weight:600'>Je proefperiode van 14 dagen is gestart!</p>
                </div>

                <p style='color:#374151;line-height:1.6'>
                    Je kunt nu inloggen en aan de slag met:
                </p>
                <ul style='color:#374151;line-height:1.8'>
                    <li>Diensten toevoegen</li>
                    <li>Openingstijden instellen</li>
                    <li>Je salonpagina personaliseren</li>
                    <li>Online boekingen ontvangen</li>
                </ul>

                <div style='text-align:center;margin:2rem 0'>
                    <a href='{$loginUrl}' style='display:inline-block;background:linear-gradient(135deg,#333333,#000000);color:white;text-decoration:none;padding:1rem 2rem;border-radius:10px;font-weight:600;font-size:1.1rem'>
                        Nu Inloggen
                    </a>
                </div>
            </div>
        </div>
        ";

        try {
            $mailer = new Mailer();
            $mailer->send($email, 'Welkom bij GlamourSchedule!', $html);
        } catch (\Exception $e) {
            error_log('Welcome email failed: ' . $e->getMessage());
        }
    }

    /**
     * Send notification email to sales partner when a referred business pays
     */
    private function notifySalesPartnerConversion(int $businessId): void
    {
        // Get referral and sales partner info
        $stmt = $this->db->query(
            "SELECT sr.commission, su.email, su.name, su.first_name, b.company_name
             FROM sales_referrals sr
             JOIN sales_users su ON sr.sales_user_id = su.id
             JOIN businesses b ON sr.business_id = b.id
             WHERE sr.business_id = ?",
            [$businessId]
        );
        $referral = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$referral) {
            return;
        }

        $partnerName = $referral['first_name'] ?: $referral['name'];
        $businessName = $referral['company_name'];
        $commission = number_format($referral['commission'], 2, ',', '.');

        $html = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#22c55e;padding:30px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:24px;'>Gefeliciteerd!</h1>
            </div>
            <div style='padding:30px;background:#1a1a1a;border:1px solid #333;'>
                <p style='font-size:16px;color:#ffffff;'>Hallo {$partnerName},</p>

                <p style='color:#374151;line-height:1.6'>
                    Goed nieuws! Een salon die jij hebt aangebracht heeft zojuist de registratiefee betaald.
                </p>

                <div style='background:#f0fdf4;border:2px solid #22c55e;border-radius:12px;padding:20px;margin:25px 0;text-align:center;'>
                    <p style='margin:0 0 10px;color:#166534;font-size:14px;'>Jouw commissie</p>
                    <p style='margin:0;font-size:36px;font-weight:bold;color:#22c55e;'>EUR {$commission}</p>
                </div>

                <div style='background:#f9fafb;border-radius:8px;padding:15px;margin:20px 0;'>
                    <table style='width:100%;font-size:14px;'>
                        <tr>
                            <td style='padding:8px 0;color:#cccccc;'>Salon:</td>
                            <td style='padding:8px 0;text-align:right;font-weight:600;color:#ffffff;'>{$businessName}</td>
                        </tr>
                        <tr>
                            <td style='padding:8px 0;color:#cccccc;'>Commissie:</td>
                            <td style='padding:8px 0;text-align:right;font-weight:600;color:#22c55e;'>EUR {$commission}</td>
                        </tr>
                        <tr>
                            <td style='padding:8px 0;color:#cccccc;'>Status:</td>
                            <td style='padding:8px 0;text-align:right;'>
                                <span style='background:#3b82f620;color:#3b82f6;padding:4px 12px;border-radius:20px;font-size:12px;'>Wacht op uitbetaling</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <p style='color:#374151;line-height:1.6'>
                    <strong>Wanneer ontvang je je commissie?</strong><br>
                    Je commissie wordt elke woensdag automatisch uitbetaald naar je IBAN.
                </p>

                <div style='text-align:center;margin:25px 0;'>
                    <a href='https://glamourschedule.nl/sales/dashboard' style='display:inline-block;background:#000;color:#fff;text-decoration:none;padding:14px 28px;border-radius:8px;font-weight:600;'>
                        Bekijk je dashboard
                    </a>
                </div>

                <p style='color:#6b7280;font-size:13px;margin-top:20px;'>
                    Blijf salons aanbrengen en verdien EUR 49,99 per betalende salon!
                </p>
            </div>
            <div style='background:#0a0a0a;padding:15px;text-align:center;border:1px solid #333;border-top:none;'>
                <p style='margin:0;color:#999;font-size:12px;'>GlamourSchedule Sales Partner</p>
            </div>
        </div>";

        try {
            $mailer = new Mailer();
            $mailer->send($referral['email'], 'Je hebt EUR ' . $commission . ' commissie verdiend!', $html);
        } catch (\Exception $e) {
            error_log('Sales partner notification email failed: ' . $e->getMessage());
        }
    }

    /**
     * Notify sales partner when a new business registers via their referral code
     */
    private function notifySalesPartnerNewRegistration(int $salesUserId, int $businessId, float $commission): void
    {
        try {
            // Get sales user data
            $stmt = $this->db->query(
                "SELECT id, name, email FROM sales_users WHERE id = ?",
                [$salesUserId]
            );
            $salesUser = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$salesUser) {
                return;
            }

            // Get business data
            $stmt = $this->db->query(
                "SELECT company_name, city, is_early_adopter FROM businesses WHERE id = ?",
                [$businessId]
            );
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$business) {
                return;
            }

            // Send notification using SalesController static method
            SalesController::notifySalesPartnerNewRegistration($salesUser, $business, $commission);

        } catch (\Exception $e) {
            error_log("Failed to notify sales partner of new registration: " . $e->getMessage());
        }
    }
}

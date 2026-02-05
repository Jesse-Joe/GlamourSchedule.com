<?php
namespace GlamourSchedule\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Mailer Class
 * Handles sending emails via PHPMailer with SMTP or fallback to PHP mail()
 */
class Mailer
{
    private array $config;
    private string $fromEmail;
    private string $fromName;
    private bool $useSMTP;
    private string $baseUrl;
    private string $lang = 'en';
    private array $translations = [];

    public function __construct(string $lang = 'en')
    {
        $this->config = require BASE_PATH . '/config/config.php';
        $this->fromEmail = $this->config['mail']['from_address'] ?? 'noreply@glamourschedule.com';
        $this->fromName = $this->config['mail']['from_name'] ?? 'GlamourSchedule';
        $this->baseUrl = rtrim($this->config['app']['url'] ?? 'https://glamourschedule.com', '/');

        // Get available languages from config
        $availableLangs = $this->config['languages']['available'] ?? ['nl', 'en', 'de', 'fr'];
        $this->lang = in_array($lang, $availableLangs) ? $lang : 'en';

        // Check if SMTP credentials are configured
        $this->useSMTP = !empty($this->config['mail']['username']) && !empty($this->config['mail']['password']);

        // Load translations from central language files
        $this->loadTranslations();
    }

    /**
     * Load translations from central language files
     */
    private function loadTranslations(): void
    {
        $langFile = BASE_PATH . '/resources/lang/' . $this->lang . '/messages.php';
        $fallbackFile = BASE_PATH . '/resources/lang/en/messages.php';

        // Load requested language
        if (file_exists($langFile)) {
            $this->translations = require $langFile;
        }

        // Load English as fallback and merge (so missing keys fall back to English)
        if ($this->lang !== 'en' && file_exists($fallbackFile)) {
            $englishTranslations = require $fallbackFile;
            $this->translations = array_merge($englishTranslations, $this->translations);
        }
    }

    /**
     * Get translation for email content
     * Uses central translation files from /resources/lang/{lang}/messages.php
     * Maps email-specific keys to the central translation keys
     */
    private function t(string $key): string
    {
        // Map old email keys to new central translation keys
        $keyMap = [
            'booking_confirmed' => 'email_booking_confirmed_title',
            'dear' => 'email_dear',
            'booking_success_msg' => 'email_booking_success_msg',
            'salon' => 'salon',
            'treatment' => 'treatment',
            'date_time' => 'date_time',
            'price' => 'price',
            'checkin_qr' => 'email_checkin_qr',
            'show_code' => 'email_show_code',
            'salon_scans' => 'email_salon_scans',
            'view_booking' => 'email_view_your_booking',
            'reminder_24h' => 'email_reminder_24h',
            'new_booking' => 'email_new_booking',
            'customer_details' => 'email_customer_details',
            'phone' => 'phone',
            'duration' => 'duration',
            'customer_notes' => 'email_customer_notes',
            'go_to_dashboard' => 'email_go_to_dashboard',
            'reminder' => 'email_reminder',
            'appointment_tomorrow' => 'email_appointment_tomorrow',
            'appointment_1hour' => 'email_appointment_1hour',
            'almost_time' => 'email_almost_time',
            'hi' => 'email_hi',
            'friendly_reminder' => 'email_friendly_reminder',
            'where' => 'email_where',
            'when' => 'email_when',
            'view_booking_btn' => 'email_view_your_booking',
            'cant_make_it' => 'email_cant_make_it',
            'welcome' => 'email_welcome_heading',
            'registration_success' => 'email_registration_success',
            'thanks_registration' => 'email_thanks_registration',
            'next_steps' => 'email_next_steps',
            'step1' => 'email_step1',
            'step2' => 'email_step2',
            'step3' => 'email_step3',
            'step4' => 'email_step4',
            'go_to_dashboard_btn' => 'email_go_to_dashboard',
            'your_page' => 'email_your_page',
            'questions' => 'email_questions',
            'trial_ending' => 'email_trial_ending',
            'activate_now' => 'email_activate_now',
            'trial_ended_msg' => 'email_trial_ended_msg',
            'hours' => 'hours',
            'important' => 'email_important',
            'no_payment_warning' => 'email_no_payment_warning',
            'monthly_subscription' => 'email_monthly_subscription',
            'per_month' => 'email_per_month',
            'activate_now_btn' => 'email_activate_now_btn',
            'account_deactivated' => 'email_account_deactivated',
            'no_payment_received' => 'email_no_payment_received',
            'this_means' => 'email_this_means',
            'page_not_visible' => 'email_page_not_visible',
            'no_new_bookings' => 'email_no_new_bookings',
            'data_preserved' => 'email_data_preserved',
            'want_to_continue' => 'email_want_to_continue',
            'reactivate_msg' => 'email_reactivate_msg',
            'reactivate_btn' => 'email_reactivate_btn',
            'hope_to_see' => 'email_hope_to_see',
            'setup_page' => 'email_setup_page',
            'account_verified' => 'email_account_verified',
            'what_to_setup' => 'email_what_to_setup',
            'profile_cover' => 'email_profile_cover',
            'salon_description' => 'email_salon_description',
            'services_prices' => 'email_services_prices',
            'opening_hours' => 'opening_hours',
            'photos_work' => 'email_photos_work',
            'setup_page_btn' => 'email_setup_page_btn',
            'complete_profile' => 'email_complete_profile',
            'all_rights' => 'email_all_rights_reserved',
            'translate_email' => 'email_translate_email',
            'no_notes' => 'email_no_notes',
            'payout' => 'email_payout',
            'email_subject_booking' => 'email_subject_booking',
            'email_subject_reminder' => 'email_subject_reminder',
            'email_subject_reminder_1h' => 'email_subject_reminder_1h',
        ];

        // Get the mapped key or use the original
        $translationKey = $keyMap[$key] ?? $key;

        // Return translation from central files, fallback to key itself
        return $this->translations[$translationKey] ?? $this->translations[$key] ?? $key;
    }

    // Legacy translations array removed - now using central /resources/lang/{lang}/messages.php files
    // This ensures all translations are managed in one place and the language selector works everywhere
    private function legacyTranslationsRemoved(): void {}

    /**
     * Get content in the user's selected language only
     * Falls back to English, then Dutch if language not available
     */
    private function getSingleLangContent(array $texts): string
    {
        // Get content in user's language, fallback to en, then nl
        $content = $texts[$this->lang] ?? $texts['en'] ?? $texts['nl'] ?? '';

        return <<<HTML
        <div style="color:#ffffff;font-size:14px;line-height:1.6;">
            {$content}
        </div>
HTML;
    }

    /**
     * Get multi-language section for emails
     * Now uses single language based on user preference
     */
    private function getMultiLangSection(array $texts): string
    {
        return $this->getSingleLangContent($texts);
    }

    /**
     * Sanitize user data for HTML output to prevent XSS
     */
    private function sanitize(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sanitize all string values in an array
     */
    private function sanitizeData(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->sanitize($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeData($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    /**
     * Send an email using PHPMailer
     */
    public function send(string $to, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        // Try primary (local Postfix)
        $mail = new PHPMailer(true);

        try {
            if ($this->useSMTP) {
                $mail->isSMTP();
                $mail->Host = $this->config['mail']['host'] ?? 'localhost';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config['mail']['username'];
                $mail->Password = $this->config['mail']['password'];
                $encryption = $this->config['mail']['encryption'] ?? '';
                if ($encryption === 'tls') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }
                $mail->Port = $this->config['mail']['port'] ?? 587;
            } else {
                // Use local sendmail (Postfix)
                $mail->isSendmail();
                $mail->Sendmail = '/usr/sbin/sendmail -t -i';
            }

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->fromEmail, $this->fromName);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);

            $this->logEmail($to, $subject, 'attempt');
            $mail->send();
            $this->logEmail($to, $subject, 'success');
            return true;

        } catch (Exception $e) {
            $primaryError = $mail->ErrorInfo ?? $e->getMessage();
            error_log("Mailer primary failed for {$to}: {$primaryError}");
            $this->logEmail($to, $subject, 'primary failed: ' . $primaryError);

            // Try fallback (Gmail SMTP)
            return $this->sendViaFallback($to, $subject, $htmlBody, $textBody);
        }
    }

    /**
     * Send email via fallback SMTP (Gmail)
     */
    private function sendViaFallback(string $to, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        $fallback = $this->config['mail']['fallback'] ?? [];
        if (empty($fallback['host']) || empty($fallback['username']) || empty($fallback['password'])) {
            $this->logEmail($to, $subject, 'failed: no fallback configured');
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $fallback['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $fallback['username'];
            $mail->Password = $fallback['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $fallback['port'] ?? 587;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->fromEmail, $this->fromName);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);

            $this->logEmail($to, $subject, 'fallback attempt');
            $mail->send();
            $this->logEmail($to, $subject, 'fallback success');
            return true;

        } catch (Exception $e) {
            $errorMsg = $mail->ErrorInfo ?? $e->getMessage();
            error_log("Mailer fallback also failed for {$to}: {$errorMsg}");
            $this->logEmail($to, $subject, 'fallback failed: ' . $errorMsg);
            return false;
        }
    }

    /**
     * Send email with attachment
     */
    public function sendWithAttachment(string $to, string $subject, string $htmlBody, string $attachmentPath, string $attachmentName = ''): bool
    {
        $mail = new PHPMailer(true);

        try {
            if ($this->useSMTP) {
                $mail->isSMTP();
                $mail->Host = $this->config['mail']['host'] ?? 'localhost';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config['mail']['username'];
                $mail->Password = $this->config['mail']['password'];
                $encryption = $this->config['mail']['encryption'] ?? '';
                if ($encryption === 'tls') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }
                $mail->Port = $this->config['mail']['port'] ?? 587;
            } else {
                $mail->isSendmail();
                $mail->Sendmail = '/usr/sbin/sendmail -t -i';
            }

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->fromEmail, $this->fromName);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            if (file_exists($attachmentPath)) {
                $name = $attachmentName ?: basename($attachmentPath);
                $mail->addAttachment($attachmentPath, $name);
            }

            $this->logEmail($to, $subject, 'attempt');
            $mail->send();
            $this->logEmail($to, $subject, 'success');
            return true;

        } catch (Exception $e) {
            $primaryError = $mail->ErrorInfo ?? $e->getMessage();
            error_log("Mailer primary failed for {$to}: {$primaryError}");
            $this->logEmail($to, $subject, 'primary failed: ' . $primaryError);

            // Try fallback (Gmail SMTP) with attachment
            $fallback = $this->config['mail']['fallback'] ?? [];
            if (empty($fallback['host']) || empty($fallback['username'])) {
                $this->logEmail($to, $subject, 'failed: no fallback configured');
                return false;
            }

            $mail2 = new PHPMailer(true);
            try {
                $mail2->isSMTP();
                $mail2->Host = $fallback['host'];
                $mail2->SMTPAuth = true;
                $mail2->Username = $fallback['username'];
                $mail2->Password = $fallback['password'];
                $mail2->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail2->Port = $fallback['port'] ?? 587;

                $mail2->setFrom($this->fromEmail, $this->fromName);
                $mail2->addAddress($to);
                $mail2->addReplyTo($this->fromEmail, $this->fromName);
                $mail2->isHTML(true);
                $mail2->CharSet = 'UTF-8';
                $mail2->Subject = $subject;
                $mail2->Body = $htmlBody;
                $mail2->AltBody = strip_tags($htmlBody);

                if (file_exists($attachmentPath)) {
                    $name = $attachmentName ?: basename($attachmentPath);
                    $mail2->addAttachment($attachmentPath, $name);
                }

                $this->logEmail($to, $subject, 'fallback attempt');
                $mail2->send();
                $this->logEmail($to, $subject, 'fallback success');
                return true;

            } catch (Exception $e2) {
                $errorMsg = $mail2->ErrorInfo ?? $e2->getMessage();
                error_log("Mailer fallback also failed for {$to}: {$errorMsg}");
                $this->logEmail($to, $subject, 'fallback failed: ' . $errorMsg);
                return false;
            }
        }
    }

    /**
     * Send a template email
     */
    public function sendTemplate(string $to, string $templateName, array $data = []): bool
    {
        $template = $this->loadTemplate($templateName);

        if (!$template) {
            error_log("Email template not found: {$templateName}");
            return false;
        }

        // Replace placeholders
        $subject = $this->replacePlaceholders($template['subject'], $data);
        $htmlBody = $this->replacePlaceholders($template['body_html'], $data);
        $textBody = $this->replacePlaceholders($template['body_text'] ?? '', $data);

        return $this->send($to, $subject, $htmlBody, $textBody);
    }

    /**
     * Load email template from database
     */
    private function loadTemplate(string $name, string $lang = 'nl'): ?array
    {
        try {
            $db = new Database($this->config['database']);
            $stmt = $db->query(
                "SELECT * FROM email_templates WHERE slug = ? AND language = ? AND is_active = 1",
                [$name, $lang]
            );
            $template = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Fallback to Dutch if not found
            if (!$template && $lang !== 'nl') {
                $stmt = $db->query(
                    "SELECT * FROM email_templates WHERE slug = ? AND language = 'nl' AND is_active = 1",
                    [$name]
                );
                $template = $stmt->fetch(\PDO::FETCH_ASSOC);
            }

            return $template ?: null;
        } catch (\Exception $e) {
            error_log("Error loading email template: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Replace placeholders in template
     */
    private function replacePlaceholders(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return $content;
    }

    /**
     * Log email for debugging/auditing
     */
    private function logEmail(string $to, string $subject, string $status = 'sent'): void
    {
        $logFile = BASE_PATH . '/storage/logs/email.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logEntry = sprintf(
            "[%s] [%s] To: %s | Subject: %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($status),
            $to,
            $subject
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Send booking confirmation to customer
     */
    public function sendBookingConfirmation(array $booking, array $businessSettings = []): bool
    {
        $to = $booking['customer_email'];
        $subject = $this->t('email_subject_booking') . " #{$booking['booking_number']} - GlamourSchedule";

        $dateFormatted = date('d-m-Y', strtotime($booking['date']));
        $priceFormatted = number_format($booking['price'], 2, ',', '.');
        $bookingUrl = "{$this->baseUrl}/booking/{$booking['uuid']}";
        $checkinUrl = "{$this->baseUrl}/checkin/{$booking['uuid']}";
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($checkinUrl);

        $htmlBody = $this->getBookingConfirmationTemplate($this->sanitizeData([
            'customer_name' => $booking['customer_name'],
            'booking_number' => $booking['booking_number'],
            'business_name' => $booking['business_name'],
            'service_name' => $booking['service_name'],
            'date' => $dateFormatted,
            'time' => $booking['time'],
            'price' => $priceFormatted,
            'booking_url' => $bookingUrl,
            'qr_code_url' => $qrCodeUrl
        ]), $businessSettings);

        return $this->send($to, $subject, $htmlBody);
    }

    /**
     * Send booking notification to business
     */
    public function sendBookingNotificationToBusiness(array $booking, array $businessSettings = []): bool
    {
        $to = $booking['business_email'];
        $customerName = $this->sanitize($booking['customer_name']);
        $newBookingText = $this->t('new_booking');
        $subject = "{$newBookingText} #{$booking['booking_number']} - {$customerName}";

        $dateFormatted = date('d-m-Y', strtotime($booking['date']));
        $priceFormatted = number_format($booking['price'], 2, ',', '.');

        $htmlBody = $this->getBusinessNotificationTemplate($this->sanitizeData([
            'business_name' => $booking['business_name'],
            'customer_name' => $booking['customer_name'],
            'customer_email' => $booking['customer_email'],
            'customer_phone' => $booking['customer_phone'] ?? '-',
            'booking_number' => $booking['booking_number'],
            'service_name' => $booking['service_name'],
            'date' => $dateFormatted,
            'time' => $booking['time'],
            'duration' => $booking['duration'] ?? '60',
            'price' => $priceFormatted,
            'notes' => $booking['notes'] ?? $this->t('no_notes'),
            'dashboard_url' => "{$this->baseUrl}/business/dashboard"
        ]), $businessSettings);

        return $this->send($to, $subject, $htmlBody);
    }

    /**
     * Send 24-hour reminder to customer
     */
    public function sendBookingReminder(array $booking, array $businessSettings = []): bool
    {
        $to = $booking['customer_email'];
        $businessName = $this->sanitize($booking['business_name']);
        $subject = $this->t('email_subject_reminder') . " {$businessName}";

        $dateFormatted = date('d-m-Y', strtotime($booking['date']));
        $bookingUrl = "{$this->baseUrl}/booking/{$booking['uuid']}";

        $htmlBody = $this->getReminderTemplate($this->sanitizeData([
            'customer_name' => $booking['customer_name'],
            'business_name' => $booking['business_name'],
            'service_name' => $booking['service_name'],
            'date' => $dateFormatted,
            'time' => $booking['time'],
            'address' => $booking['address'] ?? '',
            'city' => $booking['city'] ?? '',
            'booking_url' => $bookingUrl
        ]), $businessSettings);

        return $this->send($to, $subject, $htmlBody);
    }

    /**
     * Send 1-hour reminder to customer
     */
    public function sendBookingReminder1Hour(array $booking, array $businessSettings = []): bool
    {
        $to = $booking['customer_email'];
        $businessName = $this->sanitize($booking['business_name']);
        $subject = $this->t('email_subject_reminder_1h') . " {$businessName}";

        $timeFormatted = date('H:i', strtotime($booking['time']));
        $bookingUrl = "{$this->baseUrl}/booking/{$booking['uuid']}";

        $htmlBody = $this->getReminder1HourTemplate($this->sanitizeData([
            'customer_name' => $booking['customer_name'],
            'business_name' => $booking['business_name'],
            'service_name' => $booking['service_name'],
            'time' => $timeFormatted,
            'address' => $booking['address'] ?? '',
            'city' => $booking['city'] ?? '',
            'booking_url' => $bookingUrl
        ]), $businessSettings);

        return $this->send($to, $subject, $htmlBody);
    }

    /**
     * Send welcome email to new business
     */
    public function sendBusinessWelcome(array $business, string $setupUrl): bool
    {
        $subject = $this->t('welcome') . " - " . $this->t('registration_success');
        $htmlBody = $this->getBusinessWelcomeTemplate($business, $setupUrl);
        $textBody = $this->getBusinessWelcomeTextTemplate($business, $setupUrl);

        return $this->send($business['email'], $subject, $htmlBody, $textBody);
    }

    /**
     * Send setup completion email to business
     */
    public function sendBusinessSetupEmail(array $business): bool
    {
        $subject = $this->t('setup_page') . " - GlamourSchedule";
        $dashboardUrl = "{$this->baseUrl}/business/dashboard";
        $businessUrl = "{$this->baseUrl}/business/" . ($business['slug'] ?? '');

        $htmlBody = $this->getBusinessSetupTemplate($this->sanitizeData([
            'company_name' => $business['company_name'] ?? $business['name'],
            'dashboard_url' => $dashboardUrl,
            'business_url' => $businessUrl
        ]));

        return $this->send($business['email'], $subject, $htmlBody);
    }

    // ========== EMAIL TEMPLATES ==========

    private function getBookingConfirmationTemplate(array $data, array $settings = []): string
    {
        $primaryColor = $settings['primary_color'] ?? '#000000';
        $headerTitle = $this->t('booking_confirmed');
        $viewBookingBtn = $this->t('view_booking');
        $qrTitle = $this->t('checkin_qr');

        $content = $this->getSingleLangContent([
            'nl' => "
                <p><strong>Beste {$data['customer_name']},</strong></p>
                <p>Je afspraak is succesvol geboekt! Hieronder vind je alle details.</p>
                <p><strong>Salon:</strong> {$data['business_name']}<br>
                <strong>Behandeling:</strong> {$data['service_name']}<br>
                <strong>Datum & Tijd:</strong> {$data['date']} om {$data['time']}<br>
                <strong>Prijs:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>24-uurs annuleringsbeleid</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Gratis annuleren tot 24 uur voor de afspraak. Binnen 24 uur: 50% annuleringskosten.</p>
                </div>
                <p>Toon de QR code hieronder bij aankomst. De salon scant deze om je aanwezigheid te bevestigen.</p>
                <p>Je ontvangt een herinnering 24 uur en 1 uur voor je afspraak.</p>
            ",
            'en' => "
                <p><strong>Dear {$data['customer_name']},</strong></p>
                <p>Your appointment has been successfully booked! Below you will find all the details.</p>
                <p><strong>Salon:</strong> {$data['business_name']}<br>
                <strong>Treatment:</strong> {$data['service_name']}<br>
                <strong>Date & Time:</strong> {$data['date']} at {$data['time']}<br>
                <strong>Price:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>24-hour Cancellation Policy</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Free cancellation up to 24 hours before. Within 24 hours: 50% cancellation fee.</p>
                </div>
                <p>Show the QR code below upon arrival. The salon will scan it to confirm your presence.</p>
                <p>You will receive a reminder 24 hours and 1 hour before your appointment.</p>
            ",
            'de' => "
                <p><strong>Liebe/r {$data['customer_name']},</strong></p>
                <p>Ihr Termin wurde erfolgreich gebucht! Unten finden Sie alle Details.</p>
                <p><strong>Salon:</strong> {$data['business_name']}<br>
                <strong>Behandlung:</strong> {$data['service_name']}<br>
                <strong>Datum & Uhrzeit:</strong> {$data['date']} um {$data['time']}<br>
                <strong>Preis:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>24-Stunden Stornierungsrichtlinie</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Kostenlose Stornierung bis 24 Stunden vorher. Innerhalb von 24 Stunden: 50% Stornogebühr.</p>
                </div>
                <p>Zeigen Sie den QR-Code unten bei Ihrer Ankunft. Der Salon scannt ihn, um Ihre Anwesenheit zu bestätigen.</p>
                <p>Sie erhalten eine Erinnerung 24 Stunden und 1 Stunde vor Ihrem Termin.</p>
            ",
            'fr' => "
                <p><strong>Cher/Chère {$data['customer_name']},</strong></p>
                <p>Votre rendez-vous a été réservé avec succès! Vous trouverez tous les détails ci-dessous.</p>
                <p><strong>Salon:</strong> {$data['business_name']}<br>
                <strong>Traitement:</strong> {$data['service_name']}<br>
                <strong>Date et Heure:</strong> {$data['date']} à {$data['time']}<br>
                <strong>Prix:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>Politique d'annulation 24 heures</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Annulation gratuite jusqu'à 24 heures avant. Moins de 24 heures: 50% de frais.</p>
                </div>
                <p>Montrez le code QR ci-dessous à votre arrivée. Le salon le scannera pour confirmer votre présence.</p>
                <p>Vous recevrez un rappel 24 heures et 1 heure avant votre rendez-vous.</p>
            ",
            'bg' => "
                <p><strong>Уважаеми/а {$data['customer_name']},</strong></p>
                <p>Вашият час е успешно резервиран! По-долу ще намерите всички детайли.</p>
                <p><strong>Салон:</strong> {$data['business_name']}<br>
                <strong>Процедура:</strong> {$data['service_name']}<br>
                <strong>Дата и час:</strong> {$data['date']} в {$data['time']}<br>
                <strong>Цена:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>Политика за анулиране 24 часа</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Безплатно анулиране до 24 часа преди часа. В рамките на 24 часа: 50% такса за анулиране.</p>
                </div>
                <p>Покажете QR кода по-долу при пристигане. Салонът ще го сканира, за да потвърди присъствието ви.</p>
                <p>Ще получите напомняне 24 часа и 1 час преди вашия час.</p>
            ",
            'es' => "
                <p><strong>Estimado/a {$data['customer_name']},</strong></p>
                <p>¡Tu cita ha sido reservada con éxito! A continuación encontrarás todos los detalles.</p>
                <p><strong>Salón:</strong> {$data['business_name']}<br>
                <strong>Tratamiento:</strong> {$data['service_name']}<br>
                <strong>Fecha y Hora:</strong> {$data['date']} a las {$data['time']}<br>
                <strong>Precio:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>Política de cancelación 24 horas</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Cancelación gratuita hasta 24 horas antes. Dentro de las 24 horas: 50% de cargo por cancelación.</p>
                </div>
                <p>Muestra el código QR a tu llegada. El salón lo escaneará para confirmar tu presencia.</p>
                <p>Recibirás un recordatorio 24 horas y 1 hora antes de tu cita.</p>
            ",
            'it' => "
                <p><strong>Gentile {$data['customer_name']},</strong></p>
                <p>Il tuo appuntamento è stato prenotato con successo! Di seguito troverai tutti i dettagli.</p>
                <p><strong>Salone:</strong> {$data['business_name']}<br>
                <strong>Trattamento:</strong> {$data['service_name']}<br>
                <strong>Data e Ora:</strong> {$data['date']} alle {$data['time']}<br>
                <strong>Prezzo:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>Politica di cancellazione 24 ore</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Cancellazione gratuita fino a 24 ore prima. Entro 24 ore: 50% di penale.</p>
                </div>
                <p>Mostra il codice QR al tuo arrivo. Il salone lo scannerizzerà per confermare la tua presenza.</p>
                <p>Riceverai un promemoria 24 ore e 1 ora prima del tuo appuntamento.</p>
            ",
            'pt' => "
                <p><strong>Prezado/a {$data['customer_name']},</strong></p>
                <p>Sua consulta foi reservada com sucesso! Abaixo você encontrará todos os detalhes.</p>
                <p><strong>Salão:</strong> {$data['business_name']}<br>
                <strong>Tratamento:</strong> {$data['service_name']}<br>
                <strong>Data e Hora:</strong> {$data['date']} às {$data['time']}<br>
                <strong>Preço:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>Política de cancelamento 24 horas</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Cancelamento gratuito até 24 horas antes. Dentro de 24 horas: 50% de taxa de cancelamento.</p>
                </div>
                <p>Mostre o código QR na chegada. O salão irá escaneá-lo para confirmar sua presença.</p>
                <p>Você receberá um lembrete 24 horas e 1 hora antes da sua consulta.</p>
            ",
            'pl' => "
                <p><strong>Szanowny/a {$data['customer_name']},</strong></p>
                <p>Twoja wizyta została pomyślnie zarezerwowana! Poniżej znajdziesz wszystkie szczegóły.</p>
                <p><strong>Salon:</strong> {$data['business_name']}<br>
                <strong>Zabieg:</strong> {$data['service_name']}<br>
                <strong>Data i Godzina:</strong> {$data['date']} o {$data['time']}<br>
                <strong>Cena:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>Polityka anulowania 24 godziny</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Bezpłatne anulowanie do 24 godzin przed wizytą. W ciągu 24 godzin: 50% opłaty za anulowanie.</p>
                </div>
                <p>Pokaż kod QR po przybyciu. Salon zeskanuje go, aby potwierdzić Twoją obecność.</p>
                <p>Otrzymasz przypomnienie 24 godziny i 1 godzinę przed wizytą.</p>
            ",
            'ru' => "
                <p><strong>Уважаемый/ая {$data['customer_name']},</strong></p>
                <p>Ваша запись успешно подтверждена! Ниже вы найдете все детали.</p>
                <p><strong>Салон:</strong> {$data['business_name']}<br>
                <strong>Процедура:</strong> {$data['service_name']}<br>
                <strong>Дата и Время:</strong> {$data['date']} в {$data['time']}<br>
                <strong>Цена:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>Политика отмены 24 часа</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Бесплатная отмена за 24 часа до записи. В течение 24 часов: 50% штраф за отмену.</p>
                </div>
                <p>Покажите QR-код при прибытии. Салон отсканирует его для подтверждения вашего присутствия.</p>
                <p>Вы получите напоминание за 24 часа и за 1 час до записи.</p>
            ",
            'tr' => "
                <p><strong>Sayın {$data['customer_name']},</strong></p>
                <p>Randevunuz başarıyla oluşturuldu! Aşağıda tüm detayları bulabilirsiniz.</p>
                <p><strong>Salon:</strong> {$data['business_name']}<br>
                <strong>İşlem:</strong> {$data['service_name']}<br>
                <strong>Tarih ve Saat:</strong> {$data['date']} saat {$data['time']}<br>
                <strong>Fiyat:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>24 Saat İptal Politikası</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>24 saat öncesine kadar ücretsiz iptal. 24 saat içinde: %50 iptal ücreti.</p>
                </div>
                <p>Varışınızda QR kodunu gösterin. Salon varlığınızı onaylamak için tarayacaktır.</p>
                <p>Randevunuzdan 24 saat ve 1 saat önce hatırlatma alacaksınız.</p>
            ",
            'sv' => "
                <p><strong>Kära {$data['customer_name']},</strong></p>
                <p>Din bokning har bekräftats! Nedan hittar du alla detaljer.</p>
                <p><strong>Salong:</strong> {$data['business_name']}<br>
                <strong>Behandling:</strong> {$data['service_name']}<br>
                <strong>Datum och Tid:</strong> {$data['date']} kl {$data['time']}<br>
                <strong>Pris:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>24-timmars avbokningspolicy</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>Gratis avbokning upp till 24 timmar innan. Inom 24 timmar: 50% avbokningsavgift.</p>
                </div>
                <p>Visa QR-koden vid ankomst. Salongen skannar den för att bekräfta din närvaro.</p>
                <p>Du får en påminnelse 24 timmar och 1 timme före din bokning.</p>
            ",
            'ar' => "
                <p><strong>عزيزي/عزيزتي {$data['customer_name']},</strong></p>
                <p>تم حجز موعدك بنجاح! ستجد جميع التفاصيل أدناه.</p>
                <p><strong>الصالون:</strong> {$data['business_name']}<br>
                <strong>العلاج:</strong> {$data['service_name']}<br>
                <strong>التاريخ والوقت:</strong> {$data['date']} الساعة {$data['time']}<br>
                <strong>السعر:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>سياسة الإلغاء 24 ساعة</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>إلغاء مجاني حتى 24 ساعة قبل الموعد. خلال 24 ساعة: رسوم إلغاء 50%.</p>
                </div>
                <p>أظهر رمز QR عند الوصول. سيقوم الصالون بمسحه لتأكيد حضورك.</p>
                <p>ستتلقى تذكيراً قبل 24 ساعة وساعة واحدة من موعدك.</p>
            ",
            'ja' => "
                <p><strong>{$data['customer_name']} 様,</strong></p>
                <p>ご予約が正常に完了しました！以下に詳細をご確認ください。</p>
                <p><strong>サロン:</strong> {$data['business_name']}<br>
                <strong>施術:</strong> {$data['service_name']}<br>
                <strong>日時:</strong> {$data['date']} {$data['time']}<br>
                <strong>料金:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>24時間キャンセルポリシー</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>24時間前まで無料キャンセル可能。24時間以内：50%のキャンセル料。</p>
                </div>
                <p>到着時にQRコードをご提示ください。サロンがスキャンして出席を確認します。</p>
                <p>予約の24時間前と1時間前にリマインダーが届きます。</p>
            ",
            'ko' => "
                <p><strong>{$data['customer_name']} 고객님,</strong></p>
                <p>예약이 성공적으로 완료되었습니다! 아래에서 모든 세부 정보를 확인하세요.</p>
                <p><strong>살롱:</strong> {$data['business_name']}<br>
                <strong>시술:</strong> {$data['service_name']}<br>
                <strong>날짜 및 시간:</strong> {$data['date']} {$data['time']}<br>
                <strong>가격:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>24시간 취소 정책</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>24시간 전까지 무료 취소. 24시간 이내: 50% 취소 수수료.</p>
                </div>
                <p>도착 시 QR 코드를 보여주세요. 살롱에서 스캔하여 출석을 확인합니다.</p>
                <p>예약 24시간 전과 1시간 전에 알림을 받으실 수 있습니다.</p>
            ",
            'zh' => "
                <p><strong>亲爱的 {$data['customer_name']},</strong></p>
                <p>您的预约已成功！以下是所有详情。</p>
                <p><strong>沙龙:</strong> {$data['business_name']}<br>
                <strong>服务:</strong> {$data['service_name']}<br>
                <strong>日期和时间:</strong> {$data['date']} {$data['time']}<br>
                <strong>价格:</strong> EUR {$data['price']}</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:12px;margin:15px 0;'>
                    <p style='margin:0;color:#92400e;font-weight:600;font-size:14px;'>24小时取消政策</p>
                    <p style='margin:5px 0 0;color:#92400e;font-size:13px;'>24小时前免费取消。24小时内：50%取消费用。</p>
                </div>
                <p>到达时出示二维码。沙龙将扫描以确认您的出席。</p>
                <p>您将在预约前24小时和1小时收到提醒。</p>
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                    <!-- Header -->
                    <tr>
                        <td style="background:{$primaryColor};color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">{$headerTitle}</h1>
                            <p style="margin:10px 0 0;opacity:0.9;font-size:16px;">#{$data['booking_number']}</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:30px;color:#ffffff;">
                            {$content}

                            <!-- QR Code -->
                            <div style="text-align:center;padding:25px;background:#0a0a0a;border-radius:12px;margin:20px 0;border:1px solid #333;">
                                <p style="margin:0 0 15px;color:#ffffff;font-weight:bold;">{$qrTitle}</p>
                                <img src="{$data['qr_code_url']}" alt="QR Code" style="width:150px;height:150px;">
                            </div>

                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$data['booking_url']}" style="display:inline-block;background:{$primaryColor};color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    {$viewBookingBtn}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#888;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                            <p style="margin:5px 0 0;color:#cccccc;font-size:11px;">glamourschedule.com</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function getBusinessNotificationTemplate(array $data, array $settings = []): string
    {
        $primaryColor = $settings['primary_color'] ?? '#000000';

        $multiLangContent = $this->getMultiLangSection([
            'nl' => "
                <p><strong>Beste {$data['business_name']},</strong></p>
                <p>Je hebt een nieuwe boeking ontvangen!</p>
                <p><strong>Klant:</strong> {$data['customer_name']}<br>
                <strong>Email:</strong> {$data['customer_email']}<br>
                <strong>Tel:</strong> {$data['customer_phone']}</p>
                <p><strong>Behandeling:</strong> {$data['service_name']}<br>
                <strong>Datum:</strong> {$data['date']}<br>
                <strong>Tijd:</strong> {$data['time']}<br>
                <strong>Duur:</strong> {$data['duration']} min<br>
                <strong>Prijs:</strong> EUR {$data['price']}</p>
                <p><strong>Opmerkingen:</strong> {$data['notes']}</p>
            ",
            'en' => "
                <p><strong>Dear {$data['business_name']},</strong></p>
                <p>You have received a new booking!</p>
                <p><strong>Customer:</strong> {$data['customer_name']}<br>
                <strong>Email:</strong> {$data['customer_email']}<br>
                <strong>Phone:</strong> {$data['customer_phone']}</p>
                <p><strong>Treatment:</strong> {$data['service_name']}<br>
                <strong>Date:</strong> {$data['date']}<br>
                <strong>Time:</strong> {$data['time']}<br>
                <strong>Duration:</strong> {$data['duration']} min<br>
                <strong>Price:</strong> EUR {$data['price']}</p>
                <p><strong>Notes:</strong> {$data['notes']}</p>
            ",
            'de' => "
                <p><strong>Liebe/r {$data['business_name']},</strong></p>
                <p>Sie haben eine neue Buchung erhalten!</p>
                <p><strong>Kunde:</strong> {$data['customer_name']}<br>
                <strong>Email:</strong> {$data['customer_email']}<br>
                <strong>Tel:</strong> {$data['customer_phone']}</p>
                <p><strong>Behandlung:</strong> {$data['service_name']}<br>
                <strong>Datum:</strong> {$data['date']}<br>
                <strong>Zeit:</strong> {$data['time']}<br>
                <strong>Dauer:</strong> {$data['duration']} min<br>
                <strong>Preis:</strong> EUR {$data['price']}</p>
                <p><strong>Anmerkungen:</strong> {$data['notes']}</p>
            ",
            'fr' => "
                <p><strong>Cher/Chere {$data['business_name']},</strong></p>
                <p>Vous avez recu une nouvelle reservation!</p>
                <p><strong>Client:</strong> {$data['customer_name']}<br>
                <strong>Email:</strong> {$data['customer_email']}<br>
                <strong>Tel:</strong> {$data['customer_phone']}</p>
                <p><strong>Traitement:</strong> {$data['service_name']}<br>
                <strong>Date:</strong> {$data['date']}<br>
                <strong>Heure:</strong> {$data['time']}<br>
                <strong>Duree:</strong> {$data['duration']} min<br>
                <strong>Prix:</strong> EUR {$data['price']}</p>
                <p><strong>Notes:</strong> {$data['notes']}</p>
            ",
            'bg' => "
                <p><strong>Уважаеми/а {$data['business_name']},</strong></p>
                <p>Получихте нова резервация!</p>
                <p><strong>Клиент:</strong> {$data['customer_name']}<br>
                <strong>Имейл:</strong> {$data['customer_email']}<br>
                <strong>Тел:</strong> {$data['customer_phone']}</p>
                <p><strong>Процедура:</strong> {$data['service_name']}<br>
                <strong>Дата:</strong> {$data['date']}<br>
                <strong>Час:</strong> {$data['time']}<br>
                <strong>Продължителност:</strong> {$data['duration']} мин<br>
                <strong>Цена:</strong> EUR {$data['price']}</p>
                <p><strong>Бележки:</strong> {$data['notes']}</p>
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                    <!-- Header -->
                    <tr>
                        <td style="background:{$primaryColor};color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">{$this->t('new_booking')}</h1>
                            <p style="margin:10px 0 0;opacity:0.9;">#{$data['booking_number']}</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:30px;color:#ffffff;">
                            {$multiLangContent}

                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$data['dashboard_url']}" style="display:inline-block;background:{$primaryColor};color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Dashboard
                                </a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#888;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function getReminderTemplate(array $data, array $settings = []): string
    {
        $primaryColor = $settings['primary_color'] ?? '#000000';
        $headerTitle = $this->t('reminder');
        $subTitle = $this->t('appointment_tomorrow');
        $viewBookingBtn = $this->t('view_booking_btn');

        $content = $this->getSingleLangContent([
            'nl' => "
                <p><strong>Hoi {$data['customer_name']}!</strong></p>
                <p>Even een vriendelijke herinnering dat je morgen een afspraak hebt.</p>
                <p><strong>Waar:</strong> {$data['business_name']}<br>
                <strong>Adres:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Wanneer:</strong> {$data['date']} om {$data['time']}<br>
                <strong>Behandeling:</strong> {$data['service_name']}</p>
                <p>Kun je niet komen? Annuleer of wijzig je afspraak via de link hieronder.</p>
            ",
            'en' => "
                <p><strong>Hi {$data['customer_name']}!</strong></p>
                <p>Just a friendly reminder that you have an appointment tomorrow.</p>
                <p><strong>Where:</strong> {$data['business_name']}<br>
                <strong>Address:</strong> {$data['address']}, {$data['city']}<br>
                <strong>When:</strong> {$data['date']} at {$data['time']}<br>
                <strong>Treatment:</strong> {$data['service_name']}</p>
                <p>Can't make it? Cancel or modify your appointment via the link below.</p>
            ",
            'de' => "
                <p><strong>Hallo {$data['customer_name']}!</strong></p>
                <p>Eine freundliche Erinnerung, dass Sie morgen einen Termin haben.</p>
                <p><strong>Wo:</strong> {$data['business_name']}<br>
                <strong>Adresse:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Wann:</strong> {$data['date']} um {$data['time']}<br>
                <strong>Behandlung:</strong> {$data['service_name']}</p>
                <p>Können Sie nicht kommen? Stornieren oder ändern Sie Ihren Termin über den Link unten.</p>
            ",
            'fr' => "
                <p><strong>Bonjour {$data['customer_name']}!</strong></p>
                <p>Un petit rappel que vous avez un rendez-vous demain.</p>
                <p><strong>Où:</strong> {$data['business_name']}<br>
                <strong>Adresse:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Quand:</strong> {$data['date']} à {$data['time']}<br>
                <strong>Traitement:</strong> {$data['service_name']}</p>
                <p>Vous ne pouvez pas venir? Annulez ou modifiez votre rendez-vous via le lien ci-dessous.</p>
            ",
            'bg' => "
                <p><strong>Здравейте {$data['customer_name']}!</strong></p>
                <p>Просто приятелско напомняне, че имате час утре.</p>
                <p><strong>Къде:</strong> {$data['business_name']}<br>
                <strong>Адрес:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Кога:</strong> {$data['date']} в {$data['time']}<br>
                <strong>Процедура:</strong> {$data['service_name']}</p>
                <p>Не можете да дойдете? Отменете или променете часа си чрез линка по-долу.</p>
            ",
            'es' => "
                <p><strong>¡Hola {$data['customer_name']}!</strong></p>
                <p>Solo un recordatorio amistoso de que tienes una cita mañana.</p>
                <p><strong>Dónde:</strong> {$data['business_name']}<br>
                <strong>Dirección:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Cuándo:</strong> {$data['date']} a las {$data['time']}<br>
                <strong>Tratamiento:</strong> {$data['service_name']}</p>
                <p>¿No puedes asistir? Cancela o modifica tu cita a través del enlace de abajo.</p>
            ",
            'it' => "
                <p><strong>Ciao {$data['customer_name']}!</strong></p>
                <p>Solo un promemoria che hai un appuntamento domani.</p>
                <p><strong>Dove:</strong> {$data['business_name']}<br>
                <strong>Indirizzo:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Quando:</strong> {$data['date']} alle {$data['time']}<br>
                <strong>Trattamento:</strong> {$data['service_name']}</p>
                <p>Non puoi venire? Cancella o modifica il tuo appuntamento tramite il link qui sotto.</p>
            ",
            'pt' => "
                <p><strong>Olá {$data['customer_name']}!</strong></p>
                <p>Apenas um lembrete amigável de que você tem uma consulta amanhã.</p>
                <p><strong>Onde:</strong> {$data['business_name']}<br>
                <strong>Endereço:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Quando:</strong> {$data['date']} às {$data['time']}<br>
                <strong>Tratamento:</strong> {$data['service_name']}</p>
                <p>Não pode comparecer? Cancele ou modifique sua consulta pelo link abaixo.</p>
            ",
            'pl' => "
                <p><strong>Cześć {$data['customer_name']}!</strong></p>
                <p>Przyjazne przypomnienie, że masz jutro wizytę.</p>
                <p><strong>Gdzie:</strong> {$data['business_name']}<br>
                <strong>Adres:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Kiedy:</strong> {$data['date']} o {$data['time']}<br>
                <strong>Zabieg:</strong> {$data['service_name']}</p>
                <p>Nie możesz przyjść? Anuluj lub zmień wizytę za pomocą poniższego linku.</p>
            ",
            'ru' => "
                <p><strong>Привет {$data['customer_name']}!</strong></p>
                <p>Дружеское напоминание о том, что у вас завтра запись.</p>
                <p><strong>Где:</strong> {$data['business_name']}<br>
                <strong>Адрес:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Когда:</strong> {$data['date']} в {$data['time']}<br>
                <strong>Процедура:</strong> {$data['service_name']}</p>
                <p>Не можете прийти? Отмените или измените запись по ссылке ниже.</p>
            ",
            'tr' => "
                <p><strong>Merhaba {$data['customer_name']}!</strong></p>
                <p>Yarın randevunuz olduğunu hatırlatmak istedik.</p>
                <p><strong>Nerede:</strong> {$data['business_name']}<br>
                <strong>Adres:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Ne zaman:</strong> {$data['date']} saat {$data['time']}<br>
                <strong>İşlem:</strong> {$data['service_name']}</p>
                <p>Gelemeyecek misiniz? Aşağıdaki link üzerinden randevunuzu iptal edin veya değiştirin.</p>
            ",
            'sv' => "
                <p><strong>Hej {$data['customer_name']}!</strong></p>
                <p>En vänlig påminnelse om att du har en bokning imorgon.</p>
                <p><strong>Var:</strong> {$data['business_name']}<br>
                <strong>Adress:</strong> {$data['address']}, {$data['city']}<br>
                <strong>När:</strong> {$data['date']} kl {$data['time']}<br>
                <strong>Behandling:</strong> {$data['service_name']}</p>
                <p>Kan du inte komma? Avboka eller ändra din bokning via länken nedan.</p>
            ",
            'ar' => "
                <p><strong>مرحباً {$data['customer_name']}!</strong></p>
                <p>مجرد تذكير ودي بأن لديك موعداً غداً.</p>
                <p><strong>أين:</strong> {$data['business_name']}<br>
                <strong>العنوان:</strong> {$data['address']}, {$data['city']}<br>
                <strong>متى:</strong> {$data['date']} الساعة {$data['time']}<br>
                <strong>العلاج:</strong> {$data['service_name']}</p>
                <p>لا تستطيع الحضور؟ ألغِ أو عدّل موعدك عبر الرابط أدناه.</p>
            ",
            'ja' => "
                <p><strong>{$data['customer_name']} 様</strong></p>
                <p>明日のご予約のリマインダーです。</p>
                <p><strong>場所:</strong> {$data['business_name']}<br>
                <strong>住所:</strong> {$data['address']}, {$data['city']}<br>
                <strong>日時:</strong> {$data['date']} {$data['time']}<br>
                <strong>施術:</strong> {$data['service_name']}</p>
                <p>ご都合が悪い場合は、下のリンクからキャンセルまたは変更してください。</p>
            ",
            'ko' => "
                <p><strong>{$data['customer_name']} 고객님</strong></p>
                <p>내일 예약이 있음을 알려드립니다.</p>
                <p><strong>장소:</strong> {$data['business_name']}<br>
                <strong>주소:</strong> {$data['address']}, {$data['city']}<br>
                <strong>시간:</strong> {$data['date']} {$data['time']}<br>
                <strong>시술:</strong> {$data['service_name']}</p>
                <p>참석이 어려우신가요? 아래 링크를 통해 취소하거나 변경하세요.</p>
            ",
            'zh' => "
                <p><strong>您好 {$data['customer_name']}！</strong></p>
                <p>温馨提醒您明天有预约。</p>
                <p><strong>地点:</strong> {$data['business_name']}<br>
                <strong>地址:</strong> {$data['address']}, {$data['city']}<br>
                <strong>时间:</strong> {$data['date']} {$data['time']}<br>
                <strong>服务:</strong> {$data['service_name']}</p>
                <p>无法前往？请通过下方链接取消或修改预约。</p>
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                    <tr>
                        <td style="background:{$primaryColor};color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">{$headerTitle}</h1>
                            <p style="margin:10px 0 0;opacity:0.9;">{$subTitle}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;color:#ffffff;">
                            {$content}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$data['booking_url']}" style="display:inline-block;background:{$primaryColor};color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    {$viewBookingBtn}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#888;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function getReminder1HourTemplate(array $data, array $settings = []): string
    {
        $primaryColor = $settings['primary_color'] ?? '#000000';
        $headerTitle = $this->t('appointment_1hour');
        $subTitle = $this->t('almost_time');
        $viewBookingBtn = $this->t('view_booking_btn');

        $content = $this->getSingleLangContent([
            'nl' => "
                <p><strong>Hoi {$data['customer_name']}!</strong></p>
                <p>Over 1 uur begint je afspraak. Vergeet niet op tijd te komen!</p>
                <p><strong>Waar:</strong> {$data['business_name']}<br>
                <strong>Adres:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Tijd:</strong> {$data['time']}<br>
                <strong>Behandeling:</strong> {$data['service_name']}</p>
                <p>Zorg dat je je QR-code bij de hand hebt voor de check-in!</p>
            ",
            'en' => "
                <p><strong>Hi {$data['customer_name']}!</strong></p>
                <p>Your appointment starts in 1 hour. Don't forget to arrive on time!</p>
                <p><strong>Where:</strong> {$data['business_name']}<br>
                <strong>Address:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Time:</strong> {$data['time']}<br>
                <strong>Treatment:</strong> {$data['service_name']}</p>
                <p>Make sure to have your QR code ready for check-in!</p>
            ",
            'de' => "
                <p><strong>Hallo {$data['customer_name']}!</strong></p>
                <p>Ihr Termin beginnt in 1 Stunde. Vergessen Sie nicht, rechtzeitig zu erscheinen!</p>
                <p><strong>Wo:</strong> {$data['business_name']}<br>
                <strong>Adresse:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Zeit:</strong> {$data['time']}<br>
                <strong>Behandlung:</strong> {$data['service_name']}</p>
                <p>Halten Sie Ihren QR-Code für den Check-in bereit!</p>
            ",
            'fr' => "
                <p><strong>Bonjour {$data['customer_name']}!</strong></p>
                <p>Votre rendez-vous commence dans 1 heure. N'oubliez pas d'arriver à l'heure!</p>
                <p><strong>Où:</strong> {$data['business_name']}<br>
                <strong>Adresse:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Heure:</strong> {$data['time']}<br>
                <strong>Traitement:</strong> {$data['service_name']}</p>
                <p>Assurez-vous d'avoir votre code QR prêt pour l'enregistrement!</p>
            ",
            'bg' => "
                <p><strong>Здравейте {$data['customer_name']}!</strong></p>
                <p>Вашият час започва след 1 час. Не забравяйте да дойдете навреме!</p>
                <p><strong>Къде:</strong> {$data['business_name']}<br>
                <strong>Адрес:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Час:</strong> {$data['time']}<br>
                <strong>Процедура:</strong> {$data['service_name']}</p>
                <p>Уверете се, че имате QR кода си готов за регистрация!</p>
            ",
            'es' => "
                <p><strong>¡Hola {$data['customer_name']}!</strong></p>
                <p>Tu cita comienza en 1 hora. ¡No olvides llegar a tiempo!</p>
                <p><strong>Dónde:</strong> {$data['business_name']}<br>
                <strong>Dirección:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Hora:</strong> {$data['time']}<br>
                <strong>Tratamiento:</strong> {$data['service_name']}</p>
                <p>¡Asegúrate de tener tu código QR listo para el check-in!</p>
            ",
            'it' => "
                <p><strong>Ciao {$data['customer_name']}!</strong></p>
                <p>Il tuo appuntamento inizia tra 1 ora. Non dimenticare di arrivare in tempo!</p>
                <p><strong>Dove:</strong> {$data['business_name']}<br>
                <strong>Indirizzo:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Ora:</strong> {$data['time']}<br>
                <strong>Trattamento:</strong> {$data['service_name']}</p>
                <p>Assicurati di avere il tuo codice QR pronto per il check-in!</p>
            ",
            'pt' => "
                <p><strong>Olá {$data['customer_name']}!</strong></p>
                <p>Sua consulta começa em 1 hora. Não esqueça de chegar na hora!</p>
                <p><strong>Onde:</strong> {$data['business_name']}<br>
                <strong>Endereço:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Hora:</strong> {$data['time']}<br>
                <strong>Tratamento:</strong> {$data['service_name']}</p>
                <p>Certifique-se de ter seu código QR pronto para o check-in!</p>
            ",
            'pl' => "
                <p><strong>Cześć {$data['customer_name']}!</strong></p>
                <p>Twoja wizyta zaczyna się za 1 godzinę. Nie zapomnij przyjść na czas!</p>
                <p><strong>Gdzie:</strong> {$data['business_name']}<br>
                <strong>Adres:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Godzina:</strong> {$data['time']}<br>
                <strong>Zabieg:</strong> {$data['service_name']}</p>
                <p>Upewnij się, że masz kod QR gotowy do zameldowania!</p>
            ",
            'ru' => "
                <p><strong>Привет {$data['customer_name']}!</strong></p>
                <p>Ваша запись начинается через 1 час. Не забудьте прийти вовремя!</p>
                <p><strong>Где:</strong> {$data['business_name']}<br>
                <strong>Адрес:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Время:</strong> {$data['time']}<br>
                <strong>Процедура:</strong> {$data['service_name']}</p>
                <p>Убедитесь, что QR-код готов для регистрации!</p>
            ",
            'tr' => "
                <p><strong>Merhaba {$data['customer_name']}!</strong></p>
                <p>Randevunuz 1 saat içinde başlıyor. Zamanında gelmeyi unutmayın!</p>
                <p><strong>Nerede:</strong> {$data['business_name']}<br>
                <strong>Adres:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Saat:</strong> {$data['time']}<br>
                <strong>İşlem:</strong> {$data['service_name']}</p>
                <p>Giriş için QR kodunuzun hazır olduğundan emin olun!</p>
            ",
            'sv' => "
                <p><strong>Hej {$data['customer_name']}!</strong></p>
                <p>Din bokning börjar om 1 timme. Glöm inte att komma i tid!</p>
                <p><strong>Var:</strong> {$data['business_name']}<br>
                <strong>Adress:</strong> {$data['address']}, {$data['city']}<br>
                <strong>Tid:</strong> {$data['time']}<br>
                <strong>Behandling:</strong> {$data['service_name']}</p>
                <p>Se till att ha din QR-kod redo för incheckning!</p>
            ",
            'ar' => "
                <p><strong>مرحباً {$data['customer_name']}!</strong></p>
                <p>موعدك يبدأ خلال ساعة واحدة. لا تنسَ الحضور في الوقت المحدد!</p>
                <p><strong>أين:</strong> {$data['business_name']}<br>
                <strong>العنوان:</strong> {$data['address']}, {$data['city']}<br>
                <strong>الوقت:</strong> {$data['time']}<br>
                <strong>العلاج:</strong> {$data['service_name']}</p>
                <p>تأكد من أن رمز QR جاهز للتسجيل!</p>
            ",
            'ja' => "
                <p><strong>{$data['customer_name']} 様</strong></p>
                <p>ご予約は1時間後に始まります。時間通りにお越しください！</p>
                <p><strong>場所:</strong> {$data['business_name']}<br>
                <strong>住所:</strong> {$data['address']}, {$data['city']}<br>
                <strong>時間:</strong> {$data['time']}<br>
                <strong>施術:</strong> {$data['service_name']}</p>
                <p>チェックイン用のQRコードをご準備ください！</p>
            ",
            'ko' => "
                <p><strong>{$data['customer_name']} 고객님</strong></p>
                <p>예약이 1시간 후 시작됩니다. 시간에 맞춰 오시기 바랍니다!</p>
                <p><strong>장소:</strong> {$data['business_name']}<br>
                <strong>주소:</strong> {$data['address']}, {$data['city']}<br>
                <strong>시간:</strong> {$data['time']}<br>
                <strong>시술:</strong> {$data['service_name']}</p>
                <p>체크인을 위해 QR 코드를 준비해 주세요!</p>
            ",
            'zh' => "
                <p><strong>您好 {$data['customer_name']}！</strong></p>
                <p>您的预约将在1小时后开始。请准时到达！</p>
                <p><strong>地点:</strong> {$data['business_name']}<br>
                <strong>地址:</strong> {$data['address']}, {$data['city']}<br>
                <strong>时间:</strong> {$data['time']}<br>
                <strong>服务:</strong> {$data['service_name']}</p>
                <p>请准备好二维码以便签到！</p>
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">{$headerTitle}</h1>
                            <p style="margin:10px 0 0;opacity:0.9;">{$subTitle}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;color:#ffffff;">
                            {$content}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$data['booking_url']}" style="display:inline-block;background:{$primaryColor};color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    {$viewBookingBtn}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#888;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function getBusinessWelcomeTemplate(array $business, string $setupUrl): string
    {
        $companyName = $this->sanitize($business['company_name'] ?? $business['name'] ?? 'Ondernemer');
        $businessUrl = "{$this->baseUrl}/s/" . ($business['uuid'] ?? '');
        $dashboardUrl = "{$this->baseUrl}/business/dashboard";

        $multiLangContent = $this->getMultiLangSection([
            'nl' => "
                <p><strong>Beste {$companyName},</strong></p>
                <p>Bedankt voor je registratie bij GlamourSchedule! We zijn blij dat je hebt gekozen voor ons platform.</p>
                <p><strong>Volgende stappen:</strong><br>
                1. Upload je logo en cover foto<br>
                2. Voeg je diensten toe met prijzen<br>
                3. Stel je openingstijden in<br>
                4. Betaal de registratievergoeding</p>
                <p>Je bedrijfspagina: {$businessUrl}</p>
                <p>Vragen? Mail naar support@glamourschedule.com</p>
            ",
            'en' => "
                <p><strong>Dear {$companyName},</strong></p>
                <p>Thank you for registering with GlamourSchedule! We're happy you've chosen our platform.</p>
                <p><strong>Next steps:</strong><br>
                1. Upload your logo and cover photo<br>
                2. Add your services with prices<br>
                3. Set your opening hours<br>
                4. Pay the registration fee</p>
                <p>Your business page: {$businessUrl}</p>
                <p>Questions? Email support@glamourschedule.com</p>
            ",
            'de' => "
                <p><strong>Liebe/r {$companyName},</strong></p>
                <p>Vielen Dank fur Ihre Registrierung bei GlamourSchedule! Wir freuen uns, dass Sie sich fur unsere Plattform entschieden haben.</p>
                <p><strong>Nachste Schritte:</strong><br>
                1. Laden Sie Ihr Logo und Titelbild hoch<br>
                2. Fugen Sie Ihre Dienstleistungen mit Preisen hinzu<br>
                3. Legen Sie Ihre Offnungszeiten fest<br>
                4. Bezahlen Sie die Registrierungsgebuhr</p>
                <p>Ihre Geschaftsseite: {$businessUrl}</p>
                <p>Fragen? E-Mail an support@glamourschedule.com</p>
            ",
            'fr' => "
                <p><strong>Cher/Chere {$companyName},</strong></p>
                <p>Merci pour votre inscription sur GlamourSchedule! Nous sommes heureux que vous ayez choisi notre plateforme.</p>
                <p><strong>Prochaines etapes:</strong><br>
                1. Telechargez votre logo et photo de couverture<br>
                2. Ajoutez vos services avec les prix<br>
                3. Definissez vos heures d'ouverture<br>
                4. Payez les frais d'inscription</p>
                <p>Votre page entreprise: {$businessUrl}</p>
                <p>Questions? Email support@glamourschedule.com</p>
            ",
            'bg' => "
                <p><strong>Уважаеми/а {$companyName},</strong></p>
                <p>Благодарим ви за регистрацията в GlamourSchedule! Радваме се, че избрахте нашата платформа.</p>
                <p><strong>Следващи стъпки:</strong><br>
                1. Качете вашето лого и снимка за корица<br>
                2. Добавете услугите си с цени<br>
                3. Задайте работното си време<br>
                4. Платете регистрационната такса</p>
                <p>Вашата бизнес страница: {$businessUrl}</p>
                <p>Въпроси? Пишете на support@glamourschedule.com</p>
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Welcome / Welkom</h1>
                            <p style="margin:10px 0 0;opacity:0.9;">GlamourSchedule</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;color:#ffffff;">
                            {$multiLangContent}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$dashboardUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Dashboard
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#888;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function getBusinessWelcomeTextTemplate(array $business, string $setupUrl): string
    {
        $companyName = $business['company_name'] ?? $business['name'] ?? 'Ondernemer';
        $businessUrl = "{$this->baseUrl}/business/" . ($business['slug'] ?? '');
        $dashboardUrl = "{$this->baseUrl}/business/dashboard";

        return <<<TEXT
Welkom bij GlamourSchedule!

Beste {$companyName},

Bedankt voor je registratie bij GlamourSchedule!

Volgende stappen om live te gaan:
1. Upload je logo en cover foto
2. Voeg je diensten toe met prijzen
3. Stel je openingstijden in
4. Betaal de registratievergoeding

Ga naar je dashboard: {$dashboardUrl}

Je bedrijfspagina: {$businessUrl}

Vragen? Mail naar support@glamourschedule.com

Met vriendelijke groet,
Het GlamourSchedule Team
TEXT;
    }

    /**
     * Send trial expiry warning email (48h before deactivation)
     */
    public function sendTrialExpiryWarning(array $business): bool
    {
        $subject = "⚠️ Je proefperiode eindigt vandaag - GlamourSchedule";
        $htmlBody = $this->getTrialExpiryWarningTemplate($business);

        return $this->send($business['email'], $subject, $htmlBody);
    }

    /**
     * Send account deactivation email
     */
    public function sendAccountDeactivated(array $business): bool
    {
        $subject = "Je account is gedeactiveerd - GlamourSchedule";
        $htmlBody = $this->getAccountDeactivatedTemplate($business);

        return $this->send($business['email'], $subject, $htmlBody);
    }

    private function getTrialExpiryWarningTemplate(array $business): string
    {
        $companyName = $this->sanitize($business['company_name'] ?? 'Ondernemer');
        $dashboardUrl = "{$this->baseUrl}/business/dashboard";
        $price = number_format($business['subscription_price'] ?? 99.99, 2, ',', '.');

        $multiLangContent = $this->getMultiLangSection([
            'nl' => "
                <p><strong>Beste {$companyName},</strong></p>
                <p>Je 14-daagse gratis proefperiode bij GlamourSchedule is vandaag ten einde. Om je salonpagina online te houden, dien je binnen 48 uur je abonnement te activeren.</p>
                <p><strong>Belangrijk:</strong> Als je niet binnen 48 uur betaalt, wordt je account automatisch gedeactiveerd.</p>
                <p><strong>Maandelijks abonnement:</strong> EUR {$price} per maand (excl. BTW)</p>
            ",
            'en' => "
                <p><strong>Dear {$companyName},</strong></p>
                <p>Your 14-day free trial at GlamourSchedule has ended today. To keep your salon page online, you need to activate your subscription within 48 hours.</p>
                <p><strong>Important:</strong> If you don't pay within 48 hours, your account will be automatically deactivated.</p>
                <p><strong>Monthly subscription:</strong> EUR {$price} per month (excl. VAT)</p>
            ",
            'de' => "
                <p><strong>Liebe/r {$companyName},</strong></p>
                <p>Ihre 14-tagige kostenlose Testphase bei GlamourSchedule ist heute zu Ende. Um Ihre Salonseite online zu halten, mussen Sie Ihr Abonnement innerhalb von 48 Stunden aktivieren.</p>
                <p><strong>Wichtig:</strong> Wenn Sie nicht innerhalb von 48 Stunden bezahlen, wird Ihr Konto automatisch deaktiviert.</p>
                <p><strong>Monatliches Abonnement:</strong> EUR {$price} pro Monat (exkl. MwSt.)</p>
            ",
            'fr' => "
                <p><strong>Cher/Chere {$companyName},</strong></p>
                <p>Votre essai gratuit de 14 jours sur GlamourSchedule s'est termine aujourd'hui. Pour garder votre page salon en ligne, vous devez activer votre abonnement dans les 48 heures.</p>
                <p><strong>Important:</strong> Si vous ne payez pas dans les 48 heures, votre compte sera automatiquement desactive.</p>
                <p><strong>Abonnement mensuel:</strong> EUR {$price} par mois (hors TVA)</p>
            ",
            'bg' => "
                <p><strong>Уважаеми/а {$companyName},</strong></p>
                <p>Вашият 14-дневен безплатен пробен период в GlamourSchedule приключи днес. За да запазите страницата на салона си онлайн, трябва да активирате абонамента си в рамките на 48 часа.</p>
                <p><strong>Важно:</strong> Ако не платите в рамките на 48 часа, акаунтът ви ще бъде автоматично деактивиран.</p>
                <p><strong>Месечен абонамент:</strong> EUR {$price} на месец (без ДДС)</p>
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Trial Ending / Proefperiode Eindigt</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;color:#ffffff;">
                            {$multiLangContent}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$dashboardUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Activate Now / Activeer Nu
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#888;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function getAccountDeactivatedTemplate(array $business): string
    {
        $companyName = $this->sanitize($business['company_name'] ?? 'Ondernemer');
        $dashboardUrl = "{$this->baseUrl}/business/dashboard";

        $multiLangContent = $this->getMultiLangSection([
            'nl' => "
                <p><strong>Beste {$companyName},</strong></p>
                <p>Helaas hebben we geen betaling ontvangen binnen 48 uur na het eindigen van je proefperiode. Daarom is je account nu gedeactiveerd.</p>
                <p><strong>Dit betekent:</strong><br>
                - Je salonpagina is niet meer zichtbaar<br>
                - Je kunt geen nieuwe boekingen ontvangen<br>
                - Je bestaande gegevens blijven bewaard</p>
                <p>Je kunt je account op elk moment heractiveren door in te loggen en je abonnement te betalen.</p>
            ",
            'en' => "
                <p><strong>Dear {$companyName},</strong></p>
                <p>Unfortunately, we did not receive payment within 48 hours after your trial period ended. Therefore, your account is now deactivated.</p>
                <p><strong>This means:</strong><br>
                - Your salon page is no longer visible<br>
                - You cannot receive new bookings<br>
                - Your existing data remains preserved</p>
                <p>You can reactivate your account at any time by logging in and paying for your subscription.</p>
            ",
            'de' => "
                <p><strong>Liebe/r {$companyName},</strong></p>
                <p>Leider haben wir keine Zahlung innerhalb von 48 Stunden nach Ablauf Ihrer Testphase erhalten. Daher ist Ihr Konto jetzt deaktiviert.</p>
                <p><strong>Das bedeutet:</strong><br>
                - Ihre Salonseite ist nicht mehr sichtbar<br>
                - Sie konnen keine neuen Buchungen erhalten<br>
                - Ihre vorhandenen Daten bleiben erhalten</p>
                <p>Sie konnen Ihr Konto jederzeit reaktivieren, indem Sie sich anmelden und Ihr Abonnement bezahlen.</p>
            ",
            'fr' => "
                <p><strong>Cher/Chere {$companyName},</strong></p>
                <p>Malheureusement, nous n'avons pas recu de paiement dans les 48 heures suivant la fin de votre periode d'essai. Votre compte est donc desormais desactive.</p>
                <p><strong>Cela signifie:</strong><br>
                - Votre page salon n'est plus visible<br>
                - Vous ne pouvez plus recevoir de nouvelles reservations<br>
                - Vos donnees existantes sont conservees</p>
                <p>Vous pouvez reactiver votre compte a tout moment en vous connectant et en payant votre abonnement.</p>
            ",
            'bg' => "
                <p><strong>Уважаеми/а {$companyName},</strong></p>
                <p>За съжаление не получихме плащане в рамките на 48 часа след края на пробния ви период. Поради това акаунтът ви е деактивиран.</p>
                <p><strong>Това означава:</strong><br>
                - Страницата на салона ви вече не е видима<br>
                - Не можете да получавате нови резервации<br>
                - Съществуващите ви данни са запазени</p>
                <p>Можете да реактивирате акаунта си по всяко време, като влезете и платите абонамента си.</p>
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Account Deactivated / Account Gedeactiveerd</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;color:#ffffff;">
                            {$multiLangContent}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$dashboardUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Reactivate / Heractiveren
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#888;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function getBusinessSetupTemplate(array $data): string
    {
        $multiLangContent = $this->getMultiLangSection([
            'nl' => "
                <p><strong>Beste {$data['company_name']},</strong></p>
                <p>Je account is geverifieerd en betaald. Nu is het tijd om je bedrijfspagina aantrekkelijk te maken voor klanten!</p>
                <p><strong>Wat kun je instellen?</strong><br>
                - Profielfoto en cover afbeelding<br>
                - Beschrijving van je salon<br>
                - Je diensten met prijzen en duur<br>
                - Openingstijden per dag<br>
                - Foto's van je werk</p>
                <p>Neem de tijd om je pagina compleet te maken. Hoe completer je profiel, hoe meer boekingen je ontvangt!</p>
            ",
            'en' => "
                <p><strong>Dear {$data['company_name']},</strong></p>
                <p>Your account is verified and paid. Now it's time to make your business page attractive for customers!</p>
                <p><strong>What can you set up?</strong><br>
                - Profile photo and cover image<br>
                - Description of your salon<br>
                - Your services with prices and duration<br>
                - Opening hours per day<br>
                - Photos of your work</p>
                <p>Take your time to complete your page. The more complete your profile, the more bookings you'll receive!</p>
            ",
            'de' => "
                <p><strong>Liebe/r {$data['company_name']},</strong></p>
                <p>Ihr Konto ist verifiziert und bezahlt. Jetzt ist es Zeit, Ihre Geschaftsseite fur Kunden attraktiv zu gestalten!</p>
                <p><strong>Was konnen Sie einrichten?</strong><br>
                - Profilfoto und Titelbild<br>
                - Beschreibung Ihres Salons<br>
                - Ihre Dienstleistungen mit Preisen und Dauer<br>
                - Offnungszeiten pro Tag<br>
                - Fotos Ihrer Arbeit</p>
                <p>Nehmen Sie sich Zeit, Ihre Seite zu vervollstandigen. Je vollstandiger Ihr Profil, desto mehr Buchungen erhalten Sie!</p>
            ",
            'fr' => "
                <p><strong>Cher/Chere {$data['company_name']},</strong></p>
                <p>Votre compte est verifie et paye. Il est maintenant temps de rendre votre page entreprise attrayante pour les clients!</p>
                <p><strong>Que pouvez-vous configurer?</strong><br>
                - Photo de profil et image de couverture<br>
                - Description de votre salon<br>
                - Vos services avec prix et duree<br>
                - Heures d'ouverture par jour<br>
                - Photos de votre travail</p>
                <p>Prenez votre temps pour completer votre page. Plus votre profil est complet, plus vous recevrez de reservations!</p>
            ",
            'bg' => "
                <p><strong>Уважаеми/а {$data['company_name']},</strong></p>
                <p>Акаунтът ви е верифициран и платен. Сега е време да направите бизнес страницата си привлекателна за клиенти!</p>
                <p><strong>Какво можете да настроите?</strong><br>
                - Профилна снимка и корица<br>
                - Описание на салона ви<br>
                - Вашите услуги с цени и продължителност<br>
                - Работно време за всеки ден<br>
                - Снимки на вашата работа</p>
                <p>Отделете време да попълните страницата си. Колкото по-пълен е профилът ви, толкова повече резервации ще получите!</p>
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Set Up Your Page / Stel Je Pagina In</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;color:#ffffff;">
                            {$multiLangContent}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$data['dashboard_url']}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Dashboard
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#888;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Send payout/refund confirmation email
     */
    public function sendPayoutConfirmation(array $data): bool
    {
        $subject = "Uitbetaling in verwerking - €" . number_format($data['amount'], 2, ',', '.');

        $multiLangContent = $this->getMultiLangSection([
            'nl' => "
                <p><strong>Beste {$data['name']},</strong></p>
                <p>Goed nieuws! Er is een uitbetaling naar jou in verwerking.</p>
                <div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;margin:15px 0;'>
                    <p style='margin:0;font-size:24px;font-weight:bold;color:#22c55e;text-align:center;'>€" . number_format($data['amount'], 2, ',', '.') . "</p>
                </div>
                <p><strong>Bankrekening:</strong> {$data['iban']}<br>
                <strong>Reden:</strong> {$data['reason']}</p>
                <p><strong>Verwachte verwerkingstijd:</strong> 1-3 werkdagen</p>
                <p style='color:#cccccc;font-size:13px;'>Het bedrag wordt overgemaakt naar je geregistreerde bankrekening. De daadwerkelijke verwerkingstijd hangt af van je bank.</p>
            ",
            'en' => "
                <p><strong>Dear {$data['name']},</strong></p>
                <p>Good news! A payout is being processed for you.</p>
                <div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;margin:15px 0;'>
                    <p style='margin:0;font-size:24px;font-weight:bold;color:#22c55e;text-align:center;'>€" . number_format($data['amount'], 2, ',', '.') . "</p>
                </div>
                <p><strong>Bank account:</strong> {$data['iban']}<br>
                <strong>Reason:</strong> {$data['reason']}</p>
                <p><strong>Expected processing time:</strong> 1-3 business days</p>
                <p style='color:#cccccc;font-size:13px;'>The amount will be transferred to your registered bank account. Actual processing time depends on your bank.</p>
            ",
            'de' => "
                <p><strong>Liebe/r {$data['name']},</strong></p>
                <p>Gute Nachrichten! Eine Auszahlung wird fur Sie bearbeitet.</p>
                <div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;margin:15px 0;'>
                    <p style='margin:0;font-size:24px;font-weight:bold;color:#22c55e;text-align:center;'>€" . number_format($data['amount'], 2, ',', '.') . "</p>
                </div>
                <p><strong>Bankkonto:</strong> {$data['iban']}<br>
                <strong>Grund:</strong> {$data['reason']}</p>
                <p><strong>Erwartete Bearbeitungszeit:</strong> 1-3 Werktage</p>
                <p style='color:#cccccc;font-size:13px;'>Der Betrag wird auf Ihr registriertes Bankkonto uberwiesen. Die tatsachliche Bearbeitungszeit hangt von Ihrer Bank ab.</p>
            ",
            'fr' => "
                <p><strong>Cher/Chere {$data['name']},</strong></p>
                <p>Bonne nouvelle! Un paiement est en cours de traitement pour vous.</p>
                <div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;margin:15px 0;'>
                    <p style='margin:0;font-size:24px;font-weight:bold;color:#22c55e;text-align:center;'>€" . number_format($data['amount'], 2, ',', '.') . "</p>
                </div>
                <p><strong>Compte bancaire:</strong> {$data['iban']}<br>
                <strong>Raison:</strong> {$data['reason']}</p>
                <p><strong>Delai de traitement prevu:</strong> 1-3 jours ouvrables</p>
                <p style='color:#cccccc;font-size:13px;'>Le montant sera transfere sur votre compte bancaire enregistre. Le delai reel depend de votre banque.</p>
            ",
            'bg' => "
                <p><strong>Уважаеми/а {$data['name']},</strong></p>
                <p>Добри новини! Изплащане се обработва за вас.</p>
                <div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:15px;margin:15px 0;'>
                    <p style='margin:0;font-size:24px;font-weight:bold;color:#22c55e;text-align:center;'>€" . number_format($data['amount'], 2, ',', '.') . "</p>
                </div>
                <p><strong>Банкова сметка:</strong> {$data['iban']}<br>
                <strong>Причина:</strong> {$data['reason']}</p>
                <p><strong>Очаквано време за обработка:</strong> 1-3 работни дни</p>
                <p style='color:#cccccc;font-size:13px;'>Сумата ще бъде преведена по регистрираната ви банкова сметка. Реалното време за обработка зависи от вашата банка.</p>
            "
        ]);

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Payout / Uitbetaling</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;color:#ffffff;">
                            {$multiLangContent}
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#888;font-size:12px;">&copy; 2026 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        return $this->send($data['email'], $subject, $html);
    }
}

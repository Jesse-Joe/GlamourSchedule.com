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

    public function __construct(string $lang = 'en')
    {
        $this->config = require BASE_PATH . '/config/config.php';
        $this->fromEmail = $this->config['mail']['from_address'] ?? 'noreply@glamourschedule.com';
        $this->fromName = $this->config['mail']['from_name'] ?? 'GlamourSchedule';
        $this->baseUrl = rtrim($this->config['app']['url'] ?? 'https://glamourschedule.com', '/');
        $this->lang = in_array($lang, ['nl', 'en', 'de', 'fr']) ? $lang : 'en';

        // Check if SMTP credentials are configured
        $this->useSMTP = !empty($this->config['mail']['username']) && !empty($this->config['mail']['password']);
    }

    /**
     * Get translation for email content
     */
    private function t(string $key): string
    {
        $translations = [
            'en' => [
                'booking_confirmed' => 'Booking Confirmed!',
                'dear' => 'Dear',
                'booking_success_msg' => 'Your appointment has been successfully booked! Below you will find all the details.',
                'salon' => 'Salon',
                'treatment' => 'Treatment',
                'date_time' => 'Date & Time',
                'price' => 'Price',
                'checkin_qr' => 'Check-in QR Code',
                'show_code' => 'Show this code upon arrival.',
                'salon_scans' => 'The salon will scan this to confirm your presence.',
                'view_booking' => 'View Booking',
                'reminder_24h' => 'You will receive a reminder 24 hours before your appointment.',
                'new_booking' => 'New Booking!',
                'customer_details' => 'Customer Details',
                'phone' => 'Phone',
                'duration' => 'Duration',
                'customer_notes' => 'Customer notes',
                'go_to_dashboard' => 'Go to Dashboard',
                'reminder' => 'Reminder',
                'appointment_tomorrow' => 'Your appointment is tomorrow!',
                'appointment_1hour' => 'Your appointment starts in 1 hour!',
                'almost_time' => 'Almost Time!',
                'hi' => 'Hi',
                'friendly_reminder' => 'Just a friendly reminder that you have an appointment tomorrow.',
                'where' => 'Where',
                'when' => 'When',
                'view_booking_btn' => 'View Booking',
                'cant_make_it' => 'Can\'t make it? Cancel or modify your appointment via the link above.',
                'welcome' => 'Welcome to GlamourSchedule!',
                'registration_success' => 'Your registration is successful',
                'thanks_registration' => 'Thank you for registering with GlamourSchedule! We\'re happy you\'ve chosen our platform to grow your salon online.',
                'next_steps' => 'Next steps to go live:',
                'step1' => 'Upload your logo and cover photo',
                'step2' => 'Add your services with prices',
                'step3' => 'Set your opening hours',
                'step4' => 'Pay the registration fee',
                'go_to_dashboard_btn' => 'Go to your Dashboard',
                'your_page' => 'Your business page',
                'questions' => 'Questions? Email us at',
                'trial_ending' => 'Your trial period ends today!',
                'activate_now' => 'Activate now to keep your salon online',
                'trial_ended_msg' => 'Your 14-day free trial at GlamourSchedule has ended today. To keep your salon page online and continue receiving bookings, you need to activate your subscription within',
                'hours' => 'hours',
                'important' => 'Important',
                'no_payment_warning' => 'If you don\'t pay within 48 hours, your account will be automatically deactivated. Your salon page will no longer be visible to customers and you won\'t be able to receive bookings.',
                'monthly_subscription' => 'Monthly subscription',
                'per_month' => 'per month, excl. VAT',
                'activate_now_btn' => 'Activate Now',
                'account_deactivated' => 'Your account has been deactivated',
                'no_payment_received' => 'Unfortunately, we did not receive payment within 48 hours after your trial period ended. Therefore, your account is now deactivated.',
                'this_means' => 'This means:',
                'page_not_visible' => 'Your salon page is no longer visible to customers',
                'no_new_bookings' => 'You cannot receive new bookings',
                'data_preserved' => 'Your existing data remains preserved',
                'want_to_continue' => 'Want to continue anyway?',
                'reactivate_msg' => 'You can reactivate your account at any time by logging in and paying for your subscription. All your data, services, and settings will be preserved!',
                'reactivate_btn' => 'Reactivate my account',
                'hope_to_see' => 'We hope to see you back soon!',
                'setup_page' => 'Time to set up your page!',
                'account_verified' => 'Your account is verified and paid. Now it\'s time to make your business page attractive for customers!',
                'what_to_setup' => 'What can you set up?',
                'profile_cover' => 'Profile photo and cover image',
                'salon_description' => 'Description of your salon',
                'services_prices' => 'Your services with prices and duration',
                'opening_hours' => 'Opening hours per day',
                'photos_work' => 'Photos of your work',
                'setup_page_btn' => 'Set up your page',
                'complete_profile' => 'Take your time to complete your page. The more complete your profile, the more bookings you\'ll receive!',
                'all_rights' => 'All rights reserved.',
                'translate_email' => 'Translate this email',
                'no_notes' => 'No notes',
                'payout' => 'Payout',
                'email_subject_booking' => 'Booking Confirmation',
                'email_subject_reminder' => 'Reminder: Appointment tomorrow at',
                'email_subject_reminder_1h' => 'Reminder: Appointment in 1 hour at',
            ],
            'nl' => [
                'booking_confirmed' => 'Boeking Bevestigd!',
                'dear' => 'Beste',
                'booking_success_msg' => 'Je afspraak is succesvol geboekt! Hieronder vind je alle details.',
                'salon' => 'Salon',
                'treatment' => 'Behandeling',
                'date_time' => 'Datum & Tijd',
                'price' => 'Prijs',
                'checkin_qr' => 'Check-in QR Code',
                'show_code' => 'Toon deze code bij aankomst.',
                'salon_scans' => 'De salon scant deze om je aanwezigheid te bevestigen.',
                'view_booking' => 'Bekijk Boeking',
                'reminder_24h' => 'Je ontvangt 24 uur voor je afspraak nog een herinnering.',
                'new_booking' => 'Nieuwe Boeking!',
                'customer_details' => 'Klantgegevens',
                'phone' => 'Tel',
                'duration' => 'Duur',
                'customer_notes' => 'Opmerkingen klant',
                'go_to_dashboard' => 'Ga naar Dashboard',
                'reminder' => 'Herinnering',
                'appointment_tomorrow' => 'Je afspraak is morgen!',
                'appointment_1hour' => 'Je afspraak begint over 1 uur!',
                'almost_time' => 'Bijna Tijd!',
                'hi' => 'Hoi',
                'friendly_reminder' => 'Even een vriendelijke herinnering dat je morgen een afspraak hebt.',
                'where' => 'Waar',
                'when' => 'Wanneer',
                'view_booking_btn' => 'Bekijk Boeking',
                'cant_make_it' => 'Kun je niet komen? Annuleer of wijzig je afspraak via bovenstaande link.',
                'welcome' => 'Welkom bij GlamourSchedule!',
                'registration_success' => 'Je registratie is succesvol',
                'thanks_registration' => 'Bedankt voor je registratie bij GlamourSchedule! We zijn blij dat je hebt gekozen voor ons platform om je salon online te laten groeien.',
                'next_steps' => 'Volgende stappen om live te gaan:',
                'step1' => 'Upload je logo en cover foto',
                'step2' => 'Voeg je diensten toe met prijzen',
                'step3' => 'Stel je openingstijden in',
                'step4' => 'Betaal de registratievergoeding',
                'go_to_dashboard_btn' => 'Ga naar je Dashboard',
                'your_page' => 'Je bedrijfspagina',
                'questions' => 'Vragen? Mail naar',
                'trial_ending' => 'Je proefperiode eindigt vandaag!',
                'activate_now' => 'Activeer nu om je salon online te houden',
                'trial_ended_msg' => 'Je 14-daagse gratis proefperiode bij GlamourSchedule is vandaag ten einde. Om je salonpagina online te houden en boekingen te blijven ontvangen, dien je binnen',
                'hours' => 'uur',
                'important' => 'Belangrijk',
                'no_payment_warning' => 'Als je niet binnen 48 uur betaalt, wordt je account automatisch gedeactiveerd. Je salonpagina zal dan niet meer zichtbaar zijn voor klanten en je kunt geen boekingen meer ontvangen.',
                'monthly_subscription' => 'Maandelijks abonnement',
                'per_month' => 'per maand, excl. BTW',
                'activate_now_btn' => 'Activeer Nu',
                'account_deactivated' => 'Je account is gedeactiveerd',
                'no_payment_received' => 'Helaas hebben we geen betaling ontvangen binnen 48 uur na het eindigen van je proefperiode. Daarom is je account nu gedeactiveerd.',
                'this_means' => 'Dit betekent:',
                'page_not_visible' => 'Je salonpagina is niet meer zichtbaar voor klanten',
                'no_new_bookings' => 'Je kunt geen nieuwe boekingen ontvangen',
                'data_preserved' => 'Je bestaande gegevens blijven bewaard',
                'want_to_continue' => 'Wil je toch doorgaan?',
                'reactivate_msg' => 'Je kunt je account op elk moment heractiveren door in te loggen en je abonnement te betalen. Al je gegevens, diensten en instellingen blijven bewaard!',
                'reactivate_btn' => 'Heractiveer mijn account',
                'hope_to_see' => 'We hopen je snel weer terug te zien!',
                'setup_page' => 'Tijd om je pagina in te stellen!',
                'account_verified' => 'Je account is geverifieerd en betaald. Nu is het tijd om je bedrijfspagina aantrekkelijk te maken voor klanten!',
                'what_to_setup' => 'Wat kun je instellen?',
                'profile_cover' => 'Profielfoto en cover afbeelding',
                'salon_description' => 'Beschrijving van je salon',
                'services_prices' => 'Je diensten met prijzen en duur',
                'opening_hours' => 'Openingstijden per dag',
                'photos_work' => 'Foto\'s van je werk',
                'setup_page_btn' => 'Stel je pagina in',
                'complete_profile' => 'Neem de tijd om je pagina compleet te maken. Hoe completer je profiel, hoe meer boekingen je ontvangt!',
                'all_rights' => 'Alle rechten voorbehouden.',
                'translate_email' => 'Vertaal deze email',
                'no_notes' => 'Geen opmerkingen',
                'payout' => 'Uitbetaling',
                'email_subject_booking' => 'Boekingsbevestiging',
                'email_subject_reminder' => 'Herinnering: Morgen afspraak bij',
                'email_subject_reminder_1h' => 'Herinnering: Over 1 uur afspraak bij',
            ],
            'de' => [
                'booking_confirmed' => 'Buchung Bestätigt!',
                'dear' => 'Liebe/r',
                'booking_success_msg' => 'Ihr Termin wurde erfolgreich gebucht! Unten finden Sie alle Details.',
                'salon' => 'Salon',
                'treatment' => 'Behandlung',
                'date_time' => 'Datum & Uhrzeit',
                'price' => 'Preis',
                'checkin_qr' => 'Check-in QR-Code',
                'show_code' => 'Zeigen Sie diesen Code bei der Ankunft.',
                'salon_scans' => 'Der Salon scannt ihn, um Ihre Anwesenheit zu bestätigen.',
                'view_booking' => 'Buchung ansehen',
                'reminder_24h' => 'Sie erhalten 24 Stunden vor Ihrem Termin eine Erinnerung.',
                'new_booking' => 'Neue Buchung!',
                'reminder' => 'Erinnerung',
                'appointment_tomorrow' => 'Ihr Termin ist morgen!',
                'appointment_1hour' => 'Ihr Termin beginnt in 1 Stunde!',
                'almost_time' => 'Fast Zeit!',
                'hi' => 'Hallo',
                'view_booking_btn' => 'Buchung ansehen',
                'welcome' => 'Willkommen bei GlamourSchedule!',
                'trial_ending' => 'Ihre Testphase endet heute!',
                'activate_now_btn' => 'Jetzt Aktivieren',
                'account_deactivated' => 'Ihr Konto wurde deaktiviert',
                'reactivate_btn' => 'Konto reaktivieren',
                'setup_page' => 'Zeit, Ihre Seite einzurichten!',
                'translate_email' => 'Diese E-Mail übersetzen',
                'no_notes' => 'Keine Anmerkungen',
                'payout' => 'Auszahlung',
                'email_subject_booking' => 'Buchungsbestätigung',
                'email_subject_reminder' => 'Erinnerung: Morgen Termin bei',
                'email_subject_reminder_1h' => 'Erinnerung: In 1 Stunde Termin bei',
            ],
            'fr' => [
                'booking_confirmed' => 'Réservation Confirmée!',
                'dear' => 'Cher/Chère',
                'booking_success_msg' => 'Votre rendez-vous a été réservé avec succès! Vous trouverez tous les détails ci-dessous.',
                'salon' => 'Salon',
                'treatment' => 'Traitement',
                'date_time' => 'Date & Heure',
                'price' => 'Prix',
                'checkin_qr' => 'QR Code de Check-in',
                'show_code' => 'Montrez ce code à votre arrivée.',
                'salon_scans' => 'Le salon le scannera pour confirmer votre présence.',
                'view_booking' => 'Voir la réservation',
                'reminder_24h' => 'Vous recevrez un rappel 24 heures avant votre rendez-vous.',
                'new_booking' => 'Nouvelle Réservation!',
                'reminder' => 'Rappel',
                'appointment_tomorrow' => 'Votre rendez-vous est demain!',
                'appointment_1hour' => 'Votre rendez-vous commence dans 1 heure!',
                'almost_time' => 'Presque l\'heure!',
                'hi' => 'Bonjour',
                'view_booking_btn' => 'Voir la réservation',
                'welcome' => 'Bienvenue sur GlamourSchedule!',
                'trial_ending' => 'Votre période d\'essai se termine aujourd\'hui!',
                'activate_now_btn' => 'Activer Maintenant',
                'account_deactivated' => 'Votre compte a été désactivé',
                'reactivate_btn' => 'Réactiver mon compte',
                'setup_page' => 'Il est temps de configurer votre page!',
                'translate_email' => 'Traduire cet e-mail',
                'no_notes' => 'Pas de notes',
                'payout' => 'Paiement',
                'email_subject_booking' => 'Confirmation de réservation',
                'email_subject_reminder' => 'Rappel: Rendez-vous demain chez',
                'email_subject_reminder_1h' => 'Rappel: Rendez-vous dans 1 heure chez',
            ],
        ];

        return $translations[$this->lang][$key] ?? $translations['en'][$key] ?? $key;
    }

    /**
     * Get content in the user's selected language only
     * Falls back to English, then Dutch if language not available
     */
    private function getSingleLangContent(array $texts): string
    {
        // Get content in user's language, fallback to en, then nl
        $content = $texts[$this->lang] ?? $texts['en'] ?? $texts['nl'] ?? '';

        return <<<HTML
        <div style="color:#333;font-size:14px;line-height:1.6;">
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
        $mail = new PHPMailer(true);

        try {
            // Server settings
            if ($this->useSMTP) {
                $mail->isSMTP();
                $mail->Host = $this->config['mail']['host'] ?? 'smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config['mail']['username'];
                $mail->Password = $this->config['mail']['password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $this->config['mail']['port'] ?? 587;
            } else {
                // Use local sendmail (Postfix)
                $mail->isSendmail();
                $mail->Sendmail = '/usr/sbin/sendmail -t -i';
            }

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->fromEmail, $this->fromName);

            // Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);

            // Log email attempt
            $this->logEmail($to, $subject, 'attempt');

            $result = $mail->send();

            $this->logEmail($to, $subject, 'success');
            return true;

        } catch (Exception $e) {
            $errorMsg = $mail->ErrorInfo ?? $e->getMessage();
            error_log("Mailer Error for {$to}: {$errorMsg}");
            $this->logEmail($to, $subject, 'failed: ' . $errorMsg);
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
            // Server settings
            if ($this->useSMTP) {
                $mail->isSMTP();
                $mail->Host = $this->config['mail']['host'] ?? 'smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Username = $this->config['mail']['username'];
                $mail->Password = $this->config['mail']['password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $this->config['mail']['port'] ?? 587;
            } else {
                // Use local sendmail (Postfix)
                $mail->isSendmail();
                $mail->Sendmail = '/usr/sbin/sendmail -t -i';
            }

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->fromEmail, $this->fromName);

            // Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            // Add attachment
            if (file_exists($attachmentPath)) {
                $name = $attachmentName ?: basename($attachmentPath);
                $mail->addAttachment($attachmentPath, $name);
            }

            // Log email attempt
            $this->logEmail($to, $subject, 'attempt');

            $result = $mail->send();

            $this->logEmail($to, $subject, 'success');
            return true;

        } catch (Exception $e) {
            $errorMsg = $mail->ErrorInfo ?? $e->getMessage();
            error_log("Mailer Error for {$to}: {$errorMsg}");
            $this->logEmail($to, $subject, 'failed: ' . $errorMsg);
            return false;
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
        $subject = "Nieuwe boeking #{$booking['booking_number']} - {$customerName}";

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
            'notes' => $booking['notes'] ?? 'Geen opmerkingen',
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
        $subject = "Welkom bij GlamourSchedule - Voltooi je registratie";
        $htmlBody = $this->getBusinessWelcomeTemplate($business, $setupUrl);
        $textBody = $this->getBusinessWelcomeTextTemplate($business, $setupUrl);

        return $this->send($business['email'], $subject, $htmlBody, $textBody);
    }

    /**
     * Send setup completion email to business
     */
    public function sendBusinessSetupEmail(array $business): bool
    {
        $subject = "Stel je bedrijfspagina in - GlamourSchedule";
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
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background:{$primaryColor};color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">{$headerTitle}</h1>
                            <p style="margin:10px 0 0;opacity:0.9;font-size:16px;">#{$data['booking_number']}</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:30px;">
                            {$content}

                            <!-- QR Code -->
                            <div style="text-align:center;padding:25px;background:#f9f9f9;border-radius:12px;margin:20px 0;">
                                <p style="margin:0 0 15px;color:#333;font-weight:bold;">{$qrTitle}</p>
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
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
                            <p style="margin:5px 0 0;color:#999;font-size:11px;">glamourschedule.com</p>
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
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background:{$primaryColor};color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">New Booking / Nieuwe Boeking</h1>
                            <p style="margin:10px 0 0;opacity:0.9;">#{$data['booking_number']}</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:30px;">
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
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:{$primaryColor};color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">{$headerTitle}</h1>
                            <p style="margin:10px 0 0;opacity:0.9;">{$subTitle}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            {$content}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$data['booking_url']}" style="display:inline-block;background:{$primaryColor};color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    {$viewBookingBtn}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">{$headerTitle}</h1>
                            <p style="margin:10px 0 0;opacity:0.9;">{$subTitle}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            {$content}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$data['booking_url']}" style="display:inline-block;background:{$primaryColor};color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    {$viewBookingBtn}
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Welcome / Welkom</h1>
                            <p style="margin:10px 0 0;opacity:0.9;">GlamourSchedule</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            {$multiLangContent}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$dashboardUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Dashboard
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Trial Ending / Proefperiode Eindigt</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            {$multiLangContent}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$dashboardUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Activate Now / Activeer Nu
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Account Deactivated / Account Gedeactiveerd</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            {$multiLangContent}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$dashboardUrl}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Reactivate / Heractiveren
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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
            "
        ]);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Set Up Your Page / Stel Je Pagina In</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            {$multiLangContent}
                            <p style="text-align:center;margin:25px 0;">
                                <a href="{$data['dashboard_url']}" style="display:inline-block;background:#000000;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:8px;font-weight:bold;">
                                    Dashboard
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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
                <p style='color:#666;font-size:13px;'>Het bedrag wordt overgemaakt naar je geregistreerde bankrekening. De daadwerkelijke verwerkingstijd hangt af van je bank.</p>
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
                <p style='color:#666;font-size:13px;'>The amount will be transferred to your registered bank account. Actual processing time depends on your bank.</p>
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
                <p style='color:#666;font-size:13px;'>Der Betrag wird auf Ihr registriertes Bankkonto uberwiesen. Die tatsachliche Bearbeitungszeit hangt von Ihrer Bank ab.</p>
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
                <p style='color:#666;font-size:13px;'>Le montant sera transfere sur votre compte bancaire enregistre. Le delai reel depend de votre banque.</p>
            "
        ]);

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:#000000;color:#ffffff;padding:30px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Payout / Uitbetaling</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            {$multiLangContent}
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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

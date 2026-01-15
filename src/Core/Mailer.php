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

    public function __construct()
    {
        $this->config = require BASE_PATH . '/config/config.php';
        $this->fromEmail = $this->config['mail']['from_address'] ?? 'noreply@glamourschedule.nl';
        $this->fromName = $this->config['mail']['from_name'] ?? 'GlamourSchedule';
        $this->baseUrl = rtrim($this->config['app']['url'] ?? 'https://glamourschedule.nl', '/');

        // Check if SMTP credentials are configured
        $this->useSMTP = !empty($this->config['mail']['username']) && !empty($this->config['mail']['password']);
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
    public function sendBookingConfirmation(array $booking): bool
    {
        $to = $booking['customer_email'];
        $subject = "Boekingsbevestiging #{$booking['booking_number']} - GlamourSchedule";

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
        ]));

        return $this->send($to, $subject, $htmlBody);
    }

    /**
     * Send booking notification to business
     */
    public function sendBookingNotificationToBusiness(array $booking): bool
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
        ]));

        return $this->send($to, $subject, $htmlBody);
    }

    /**
     * Send 24-hour reminder to customer
     */
    public function sendBookingReminder(array $booking): bool
    {
        $to = $booking['customer_email'];
        $businessName = $this->sanitize($booking['business_name']);
        $subject = "Herinnering: Morgen afspraak bij {$businessName}";

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
        ]));

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

    private function getBookingConfirmationTemplate(array $data): string
    {
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
                        <td style="background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:40px;text-align:center;">
                            <div style="font-size:48px;margin-bottom:10px;">✓</div>
                            <h1 style="margin:0;font-size:28px;font-weight:700;">Boeking Bevestigd!</h1>
                            <p style="margin:10px 0 0;opacity:0.9;font-size:16px;">#{$data['booking_number']}</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;margin:0 0 25px;">Beste <strong>{$data['customer_name']}</strong>,</p>
                            <p style="font-size:16px;color:#555;line-height:1.6;margin:0 0 30px;">
                                Je afspraak is succesvol geboekt! Hieronder vind je alle details.
                            </p>

                            <!-- Booking Details Card -->
                            <div style="background:linear-gradient(135deg,#fffbeb,#faf5ff);border-radius:12px;padding:25px;margin-bottom:30px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid #rgba(0,0,0,0.2);">
                                            <span style="color:#666;font-size:14px;">Salon</span><br>
                                            <strong style="color:#333;font-size:16px;">{$data['business_name']}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid #rgba(0,0,0,0.2);">
                                            <span style="color:#666;font-size:14px;">Behandeling</span><br>
                                            <strong style="color:#333;font-size:16px;">{$data['service_name']}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid #rgba(0,0,0,0.2);">
                                            <span style="color:#666;font-size:14px;">Datum & Tijd</span><br>
                                            <strong style="color:#000000;font-size:18px;">{$data['date']} om {$data['time']}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;">
                                            <span style="color:#666;font-size:14px;">Prijs</span><br>
                                            <strong style="color:#333;font-size:18px;">&euro;{$data['price']}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- QR Code Check-in -->
                            <div style="background:#f0fdf4;border:2px dashed #333333;border-radius:12px;padding:25px;margin-bottom:30px;text-align:center;">
                                <p style="margin:0 0 15px;color:#000000;font-weight:bold;font-size:16px;">
                                    📱 Check-in QR Code
                                </p>
                                <img src="{$data['qr_code_url']}" alt="Check-in QR Code" style="width:150px;height:150px;border-radius:8px;">
                                <p style="margin:15px 0 0;color:#000000;font-size:13px;line-height:1.5;">
                                    Toon deze code bij aankomst.<br>
                                    De salon scant deze om je aanwezigheid te bevestigen.
                                </p>
                            </div>

                            <p style="text-align:center;margin:30px 0;">
                                <a href="{$data['booking_url']}" style="display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;box-shadow:0 4px 15px rgba(0,0,0,0.3);">
                                    Bekijk je boeking
                                </a>
                            </p>

                            <p style="font-size:14px;color:#888;text-align:center;margin:25px 0 0;">
                                Je ontvangt 24 uur voor je afspraak nog een herinnering.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#fafafa;padding:25px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">&copy; 2025 GlamourSchedule - Beauty & Wellness Platform</p>
                            <p style="margin:8px 0 0;color:#999;font-size:12px;">
                                <a href="{$this->baseUrl}" style="color:#000000;text-decoration:none;">glamourschedule.nl</a>
                            </p>
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

    private function getBusinessNotificationTemplate(array $data): string
    {
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
                        <td style="background:linear-gradient(135deg,#333333,#000000);color:#ffffff;padding:35px;text-align:center;">
                            <div style="font-size:42px;margin-bottom:8px;">📅</div>
                            <h1 style="margin:0;font-size:24px;font-weight:700;">Nieuwe Boeking!</h1>
                            <p style="margin:8px 0 0;opacity:0.9;">#{$data['booking_number']}</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:35px;">
                            <p style="font-size:16px;color:#333;margin:0 0 20px;">
                                Beste <strong>{$data['business_name']}</strong>,
                            </p>
                            <p style="font-size:16px;color:#555;line-height:1.6;margin:0 0 25px;">
                                Je hebt een nieuwe boeking ontvangen! Hieronder de details:
                            </p>

                            <!-- Customer Info -->
                            <div style="background:#f0fdf4;border-left:4px solid #333333;padding:20px;margin-bottom:20px;border-radius:0 8px 8px 0;">
                                <h3 style="margin:0 0 12px;color:#333333;font-size:14px;text-transform:uppercase;">Klantgegevens</h3>
                                <p style="margin:0;color:#333;">
                                    <strong>{$data['customer_name']}</strong><br>
                                    <span style="color:#666;">{$data['customer_email']}</span><br>
                                    <span style="color:#666;">Tel: {$data['customer_phone']}</span>
                                </p>
                            </div>

                            <!-- Booking Details -->
                            <div style="background:#fafafa;border-radius:10px;padding:20px;margin-bottom:20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:8px 0;">
                                            <span style="color:#666;">Behandeling:</span>
                                            <strong style="float:right;color:#333;">{$data['service_name']}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;border-top:1px solid #eee;">
                                            <span style="color:#666;">Datum:</span>
                                            <strong style="float:right;color:#333;">{$data['date']}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;border-top:1px solid #eee;">
                                            <span style="color:#666;">Tijd:</span>
                                            <strong style="float:right;color:#333;">{$data['time']}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;border-top:1px solid #eee;">
                                            <span style="color:#666;">Duur:</span>
                                            <strong style="float:right;color:#333;">{$data['duration']} min</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:8px 0;border-top:1px solid #eee;">
                                            <span style="color:#666;">Prijs:</span>
                                            <strong style="float:right;color:#333333;font-size:18px;">&euro;{$data['price']}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Notes -->
                            <div style="background:#fffbeb;border-left:4px solid #000000;padding:15px 20px;border-radius:0 8px 8px 0;">
                                <p style="margin:0;color:#000000;font-size:14px;">
                                    <strong>Opmerkingen klant:</strong><br>
                                    {$data['notes']}
                                </p>
                            </div>

                            <p style="text-align:center;margin:30px 0 0;">
                                <a href="{$data['dashboard_url']}" style="display:inline-block;background:#333333;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:15px;">
                                    Ga naar Dashboard
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

    private function getReminderTemplate(array $data): string
    {
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
                        <td style="background:linear-gradient(135deg,#000000,#404040);color:#ffffff;padding:40px;text-align:center;">
                            <div style="font-size:48px;margin-bottom:10px;">⏰</div>
                            <h1 style="margin:0;font-size:26px;font-weight:700;">Herinnering</h1>
                            <p style="margin:10px 0 0;opacity:0.95;font-size:16px;">Je afspraak is morgen!</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;margin:0 0 25px;">
                                Hoi <strong>{$data['customer_name']}</strong>!
                            </p>
                            <p style="font-size:16px;color:#555;line-height:1.6;margin:0 0 30px;">
                                Even een vriendelijke herinnering dat je morgen een afspraak hebt.
                            </p>

                            <!-- Appointment Card -->
                            <div style="background:linear-gradient(135deg,#fffbeb,#f5f5f5);border-radius:12px;padding:25px;margin-bottom:25px;border:2px solid #e5e5e5;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:10px 0;">
                                            <span style="color:#000000;font-size:14px;">Waar</span><br>
                                            <strong style="color:#333;font-size:18px;">{$data['business_name']}</strong>
                                            <br><span style="color:#666;font-size:14px;">{$data['address']}, {$data['city']}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:15px 0;border-top:1px dashed #e5e5e5;">
                                            <span style="color:#000000;font-size:14px;">Wanneer</span><br>
                                            <strong style="color:#404040;font-size:22px;">{$data['date']} om {$data['time']}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px 0;border-top:1px dashed #e5e5e5;">
                                            <span style="color:#000000;font-size:14px;">Behandeling</span><br>
                                            <strong style="color:#333;font-size:16px;">{$data['service_name']}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <p style="text-align:center;margin:30px 0;">
                                <a href="{$data['booking_url']}" style="display:inline-block;background:linear-gradient(135deg,#000000,#404040);color:#ffffff;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;box-shadow:0 4px 15px rgba(245,158,11,0.4);">
                                    Bekijk boeking
                                </a>
                            </p>

                            <p style="font-size:14px;color:#888;text-align:center;margin:20px 0 0;">
                                Kun je niet komen? Annuleer of wijzig je afspraak via bovenstaande link.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#fafafa;padding:25px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">&copy; 2025 GlamourSchedule</p>
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
        $businessUrl = "{$this->baseUrl}/business/" . ($business['slug'] ?? '');
        $dashboardUrl = "{$this->baseUrl}/business/dashboard";

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
                        <td style="background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:45px;text-align:center;">
                            <div style="font-size:48px;margin-bottom:12px;">🎉</div>
                            <h1 style="margin:0;font-size:28px;font-weight:700;">Welkom bij GlamourSchedule!</h1>
                            <p style="margin:12px 0 0;opacity:0.9;font-size:16px;">Je registratie is succesvol</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;margin:0 0 20px;">Beste <strong>{$companyName}</strong>,</p>

                            <p style="font-size:16px;color:#555;line-height:1.7;margin:0 0 30px;">
                                Bedankt voor je registratie bij GlamourSchedule! We zijn blij dat je hebt gekozen voor ons platform om je salon online te laten groeien.
                            </p>

                            <!-- Steps Card -->
                            <div style="background:linear-gradient(135deg,#fffbeb,#faf5ff);border-radius:12px;padding:25px;margin:25px 0;">
                                <h3 style="margin:0 0 20px;color:#000000;font-size:16px;">Volgende stappen om live te gaan:</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid rgba(0,0,0,0.1);">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width:36px;">
                                                        <div style="width:28px;height:28px;background:#000000;border-radius:50%;color:white;text-align:center;line-height:28px;font-weight:bold;font-size:14px;">1</div>
                                                    </td>
                                                    <td style="color:#333;font-size:15px;">Upload je logo en cover foto</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid rgba(0,0,0,0.1);">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width:36px;">
                                                        <div style="width:28px;height:28px;background:#000000;border-radius:50%;color:white;text-align:center;line-height:28px;font-weight:bold;font-size:14px;">2</div>
                                                    </td>
                                                    <td style="color:#333;font-size:15px;">Voeg je diensten toe met prijzen</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid rgba(0,0,0,0.1);">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width:36px;">
                                                        <div style="width:28px;height:28px;background:#000000;border-radius:50%;color:white;text-align:center;line-height:28px;font-weight:bold;font-size:14px;">3</div>
                                                    </td>
                                                    <td style="color:#333;font-size:15px;">Stel je openingstijden in</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px 0;">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="width:36px;">
                                                        <div style="width:28px;height:28px;background:#000000;border-radius:50%;color:white;text-align:center;line-height:28px;font-weight:bold;font-size:14px;">4</div>
                                                    </td>
                                                    <td style="color:#333;font-size:15px;">Betaal de registratievergoeding</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <p style="text-align:center;margin:35px 0;">
                                <a href="{$dashboardUrl}" style="display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:18px 50px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:17px;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                                    Ga naar je Dashboard
                                </a>
                            </p>

                            <p style="font-size:14px;color:#666;text-align:center;margin:25px 0 0;">
                                Je bedrijfspagina: <a href="{$businessUrl}" style="color:#000000;font-weight:500;">{$businessUrl}</a>
                            </p>

                            <hr style="border:none;border-top:1px solid #eee;margin:35px 0;">

                            <p style="font-size:14px;color:#666;text-align:center;">
                                Vragen? Mail naar <a href="mailto:support@glamourschedule.nl" style="color:#000000;">support@glamourschedule.nl</a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#fafafa;padding:25px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">&copy; 2025 GlamourSchedule. Alle rechten voorbehouden.</p>
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

Vragen? Mail naar support@glamourschedule.nl

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
        $price = number_format($business['subscription_price'] ?? 29.99, 2, ',', '.');

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
                        <td style="background:linear-gradient(135deg,#000000,#404040);color:#ffffff;padding:40px;text-align:center;">
                            <div style="font-size:48px;margin-bottom:10px;">⏰</div>
                            <h1 style="margin:0;font-size:26px;font-weight:700;">Je proefperiode eindigt vandaag!</h1>
                            <p style="margin:10px 0 0;opacity:0.95;font-size:16px;">Activeer nu om je salon online te houden</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;margin:0 0 20px;">Beste <strong>{$companyName}</strong>,</p>

                            <p style="font-size:16px;color:#555;line-height:1.7;margin:0 0 25px;">
                                Je 14-daagse gratis proefperiode bij GlamourSchedule is vandaag ten einde.
                                Om je salonpagina online te houden en boekingen te blijven ontvangen,
                                dien je binnen <strong style="color:#404040;">48 uur</strong> je abonnement te activeren.
                            </p>

                            <!-- Warning Box -->
                            <div style="background:#f5f5f5;border:2px solid #e5e5e5;border-radius:12px;padding:25px;margin:25px 0;">
                                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                                    <span style="font-size:24px;">⚠️</span>
                                    <strong style="color:#000000;font-size:16px;">Belangrijk</strong>
                                </div>
                                <p style="margin:0;color:#000000;font-size:15px;line-height:1.6;">
                                    Als je niet binnen 48 uur betaalt, wordt je account automatisch gedeactiveerd.
                                    Je salonpagina zal dan niet meer zichtbaar zijn voor klanten en je kunt geen boekingen meer ontvangen.
                                </p>
                            </div>

                            <!-- Pricing Card -->
                            <div style="background:linear-gradient(135deg,#fffbeb,#faf5ff);border-radius:12px;padding:25px;margin:25px 0;text-align:center;">
                                <p style="margin:0 0 10px;color:#666;font-size:14px;">Maandelijks abonnement</p>
                                <div style="font-size:36px;font-weight:700;color:#000000;">€{$price}</div>
                                <p style="margin:10px 0 0;color:#888;font-size:13px;">per maand, excl. BTW</p>
                            </div>

                            <p style="text-align:center;margin:35px 0;">
                                <a href="{$dashboardUrl}" style="display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:18px 50px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:17px;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                                    Activeer nu
                                </a>
                            </p>

                            <p style="font-size:14px;color:#888;text-align:center;margin:25px 0 0;">
                                Heb je vragen? Neem contact op via <a href="mailto:support@glamourschedule.nl" style="color:#000000;">support@glamourschedule.nl</a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#fafafa;padding:25px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">&copy; 2025 GlamourSchedule. Alle rechten voorbehouden.</p>
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
                        <td style="background:linear-gradient(135deg,#333333,#dc2626);color:#ffffff;padding:40px;text-align:center;">
                            <div style="font-size:48px;margin-bottom:10px;">😢</div>
                            <h1 style="margin:0;font-size:26px;font-weight:700;">Je account is gedeactiveerd</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;margin:0 0 20px;">Beste <strong>{$companyName}</strong>,</p>

                            <p style="font-size:16px;color:#555;line-height:1.7;margin:0 0 25px;">
                                Helaas hebben we geen betaling ontvangen binnen 48 uur na het eindigen van je proefperiode.
                                Daarom is je account nu gedeactiveerd.
                            </p>

                            <!-- What this means -->
                            <div style="background:#f5f5f5;border-left:4px solid #333333;padding:20px;margin-bottom:25px;border-radius:0 8px 8px 0;">
                                <h3 style="margin:0 0 12px;color:#dc2626;font-size:15px;">Dit betekent:</h3>
                                <ul style="margin:0;padding-left:20px;color:#7f1d1d;line-height:1.8;">
                                    <li>Je salonpagina is niet meer zichtbaar voor klanten</li>
                                    <li>Je kunt geen nieuwe boekingen ontvangen</li>
                                    <li>Je bestaande gegevens blijven bewaard</li>
                                </ul>
                            </div>

                            <!-- Reactivate Card -->
                            <div style="background:linear-gradient(135deg,#f0fdf4,#f5f5f5);border:2px solid #333333;border-radius:12px;padding:25px;margin:25px 0;">
                                <h3 style="margin:0 0 15px;color:#000000;font-size:16px;">🔄 Wil je toch doorgaan?</h3>
                                <p style="margin:0 0 20px;color:#000000;font-size:15px;line-height:1.6;">
                                    Je kunt je account op elk moment heractiveren door in te loggen en je abonnement te betalen.
                                    Al je gegevens, diensten en instellingen blijven bewaard!
                                </p>
                                <a href="{$dashboardUrl}" style="display:inline-block;background:linear-gradient(135deg,#333333,#000000);color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:15px;">
                                    Heractiveer mijn account
                                </a>
                            </div>

                            <p style="font-size:14px;color:#888;text-align:center;margin:25px 0 0;">
                                We hopen je snel weer terug te zien!<br>
                                Vragen? <a href="mailto:support@glamourschedule.nl" style="color:#000000;">support@glamourschedule.nl</a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#fafafa;padding:25px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">&copy; 2025 GlamourSchedule. Alle rechten voorbehouden.</p>
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
                        <td style="background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:40px;text-align:center;">
                            <div style="font-size:48px;margin-bottom:10px;">🚀</div>
                            <h1 style="margin:0;font-size:26px;font-weight:700;">Tijd om je pagina in te stellen!</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#333;margin:0 0 20px;">
                                Beste <strong>{$data['company_name']}</strong>,
                            </p>
                            <p style="font-size:16px;color:#555;line-height:1.7;margin:0 0 25px;">
                                Je account is geverifieerd en betaald. Nu is het tijd om je bedrijfspagina aantrekkelijk te maken voor klanten!
                            </p>

                            <div style="background:#f5f3ff;border-radius:12px;padding:25px;margin:25px 0;">
                                <h3 style="margin:0 0 15px;color:#000000;">Wat kun je instellen?</h3>
                                <ul style="margin:0;padding-left:20px;color:#333;line-height:2;">
                                    <li>Profielfoto en cover afbeelding</li>
                                    <li>Beschrijving van je salon</li>
                                    <li>Je diensten met prijzen en duur</li>
                                    <li>Openingstijden per dag</li>
                                    <li>Foto's van je werk</li>
                                </ul>
                            </div>

                            <p style="text-align:center;margin:35px 0;">
                                <a href="{$data['dashboard_url']}" style="display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:#ffffff;padding:18px 50px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:17px;box-shadow:0 4px 20px rgba(0,0,0,0.3);">
                                    Stel je pagina in
                                </a>
                            </p>

                            <p style="font-size:14px;color:#888;text-align:center;">
                                Neem de tijd om je pagina compleet te maken. Hoe completer je profiel, hoe meer boekingen je ontvangt!
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#fafafa;padding:25px;text-align:center;border-top:1px solid #eee;">
                            <p style="margin:0;color:#666;font-size:13px;">&copy; 2025 GlamourSchedule</p>
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
}

<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;

class PagesController extends Controller
{
    public function terms(): string
    {
        return $this->view('pages/terms', [
            'pageTitle' => $this->getTranslations()['terms'] ?? 'Terms & Conditions'
        ]);
    }

    public function privacy(): string
    {
        return $this->view('pages/privacy', [
            'pageTitle' => $this->getTranslations()['privacy'] ?? 'Privacy Policy'
        ]);
    }

    public function about(): string
    {
        return $this->view('pages/about', [
            'pageTitle' => 'Functionaliteit'
        ]);
    }

    public function faq(): string
    {
        return $this->view('pages/faq', [
            'pageTitle' => $this->getTranslations()['faq'] ?? 'FAQ'
        ]);
    }

    public function marketing(): string
    {
        return $this->view('pages/marketing', [
            'pageTitle' => 'Marketing'
        ]);
    }

    public function contact(): string
    {
        return $this->view('pages/contact', [
            'pageTitle' => $this->getTranslations()['contact'] ?? 'Contact',
            'csrfToken' => $this->csrf()
        ]);
    }

    public function submitContact(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->view('pages/contact', [
                'pageTitle' => $this->getTranslations()['contact'] ?? 'Contact',
                'csrfToken' => $this->csrf(),
                'error' => 'Ongeldige aanvraag. Probeer opnieuw.'
            ]);
        }

        // SPAM PROTECTION
        $spamCheck = $this->checkForSpam();
        if ($spamCheck !== true) {
            error_log("Contact form spam blocked: " . $spamCheck . " - IP: " . $this->getClientIp());
            return $this->view('pages/contact', [
                'pageTitle' => $this->getTranslations()['contact'] ?? 'Contact',
                'csrfToken' => $this->csrf(),
                'error' => 'Je bericht kon niet worden verzonden. Probeer het later opnieuw.'
            ]);
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validation
        $errors = [];
        if (empty($name)) $errors[] = 'Naam is verplicht';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Geldig e-mailadres is verplicht';
        if (empty($type)) $errors[] = 'Type melding is verplicht';
        if (empty($subject)) $errors[] = 'Onderwerp is verplicht';
        if (empty($message)) $errors[] = 'Bericht is verplicht';

        // Additional spam validation on content
        if ($this->isSpamContent($name, $email, $subject, $message)) {
            error_log("Contact form spam content blocked - IP: " . $this->getClientIp() . " Email: " . $email);
            $errors[] = 'Je bericht is gedetecteerd als spam';
        }

        if (!empty($errors)) {
            return $this->view('pages/contact', [
                'pageTitle' => $this->getTranslations()['contact'] ?? 'Contact',
                'csrfToken' => $this->csrf(),
                'error' => implode(', ', $errors),
                'formData' => compact('name', 'email', 'type', 'subject', 'message')
            ]);
        }

        // Generate ticket number
        $ticketNumber = 'GS-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Type labels
        $typeLabels = [
            'bug' => 'Bug / Fout',
            'request' => 'Verzoek / Feature',
            'problem' => 'Probleem / Hulp',
            'other' => 'Overig'
        ];
        $typeLabel = $typeLabels[$type] ?? $type;

        // Send email to support
        $this->sendSupportEmail($ticketNumber, $name, $email, $typeLabel, $subject, $message);

        // Send confirmation to user
        $this->sendConfirmationEmail($ticketNumber, $name, $email, $typeLabel, $subject, $message);

        return $this->view('pages/contact', [
            'pageTitle' => $this->getTranslations()['contact'] ?? 'Contact',
            'csrfToken' => $this->csrf(),
            'success' => "Bedankt voor je bericht! Je ticketnummer is: {$ticketNumber}. We nemen zo snel mogelijk contact met je op."
        ]);
    }

    private function sendSupportEmail(string $ticket, string $name, string $email, string $type, string $subject, string $message): void
    {
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:20px;font-family:Arial,sans-serif;background:#0a0a0a;">
    <div style="max-width:600px;margin:0 auto;background:#1a1a1a;border-radius:10px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.3);">
        <div style="background:linear-gradient(135deg,#000000,#000000);color:white;padding:25px;text-align:center;">
            <h1 style="margin:0;font-size:22px;">Nieuwe Support Ticket</h1>
            <p style="margin:10px 0 0;opacity:0.9;">#{$ticket}</p>
        </div>
        <div style="padding:30px;color:#ffffff;">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#cccccc;width:120px;"><strong>Type:</strong></td>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;">{$type}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#cccccc;"><strong>Naam:</strong></td>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;">{$name}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#cccccc;"><strong>E-mail:</strong></td>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;"><a href="mailto:{$email}">{$email}</a></td>
                </tr>
                <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#cccccc;"><strong>Onderwerp:</strong></td>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;">{$subject}</td>
                </tr>
            </table>

            <div style="margin-top:20px;">
                <strong style="color:#cccccc;">Bericht:</strong>
                <div style="background:#0a0a0a;border-radius:8px;padding:15px;margin-top:10px;white-space:pre-wrap;line-height:1.6;color:#ffffff;border:1px solid #333;">{$message}</div>
            </div>

            <div style="margin-top:25px;padding-top:20px;border-top:1px solid #333;text-align:center;">
                <a href="mailto:{$email}?subject=Re: [{$ticket}] {$subject}" style="display:inline-block;background:linear-gradient(135deg,#000000,#000000);color:white;padding:12px 25px;border-radius:25px;text-decoration:none;font-weight:600;">Beantwoorden</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

        try {
            $mailer = new Mailer();
            // Send to both support addresses
            $mailer->send('support@glamourschedule.com', "[{$ticket}] {$type}: {$subject}", $htmlBody);
            $mailer->send('jjt-services@outlook.com', "[{$ticket}] {$type}: {$subject}", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send support email: " . $e->getMessage());
        }
    }

    private function sendConfirmationEmail(string $ticket, string $name, string $email, string $type, string $subject, string $message): void
    {
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:20px;font-family:Arial,sans-serif;background:#0a0a0a;">
    <div style="max-width:600px;margin:0 auto;background:#1a1a1a;border-radius:10px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.3);">
        <div style="background:linear-gradient(135deg,#000000,#000000);color:white;padding:25px;text-align:center;">
            <h1 style="margin:0;font-size:22px;">Bedankt voor je bericht!</h1>
        </div>
        <div style="padding:30px;color:#ffffff;">
            <p style="font-size:16px;color:#ffffff;">Beste {$name},</p>

            <p style="color:#cccccc;line-height:1.6;">
                Bedankt voor het contact opnemen met GlamourSchedule. We hebben je bericht ontvangen en zullen zo snel mogelijk reageren.
            </p>

            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:20px;margin:25px 0;text-align:center;">
                <p style="margin:0;color:#166534;font-weight:600;">Je ticketnummer:</p>
                <p style="margin:10px 0 0;font-size:24px;font-weight:700;color:#ffffff;">{$ticket}</p>
            </div>

            <p style="color:#cccccc;font-size:14px;line-height:1.6;">
                Bewaar dit ticketnummer voor je administratie. Je kunt dit nummer gebruiken als je contact met ons opneemt over deze melding.
            </p>

            <div style="background:#0a0a0a;border-radius:8px;padding:15px;margin-top:20px;">
                <p style="margin:0 0 10px;color:#cccccc;font-size:14px;"><strong>Type:</strong> {$type}</p>
                <p style="margin:0 0 10px;color:#cccccc;font-size:14px;"><strong>Onderwerp:</strong> {$subject}</p>
                <p style="margin:0;color:#cccccc;font-size:14px;"><strong>Je bericht:</strong></p>
                <p style="margin:10px 0 0;color:#cccccc;font-size:14px;white-space:pre-wrap;">{$message}</p>
            </div>

            <p style="margin-top:25px;color:#cccccc;line-height:1.6;">
                Met vriendelijke groet,<br>
                <strong>Het GlamourSchedule Team</strong>
            </p>
        </div>
        <div style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
            <p style="margin:0;color:#9ca3af;font-size:12px;">&copy; 2026 GlamourSchedule - Beauty & Wellness Booking Platform</p>
        </div>
    </div>
</body>
</html>
HTML;

        try {
            $mailer = new Mailer();
            $mailer->send($email, "Bevestiging: Je bericht is ontvangen [{$ticket}]", $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send confirmation email: " . $e->getMessage());
        }
    }

    /**
     * Check for spam submissions
     * @return bool|string Returns true if valid, or error reason string if spam
     */
    private function checkForSpam()
    {
        // 1. Honeypot check - if filled, it's a bot
        if (!empty($_POST['website_url'])) {
            return 'honeypot';
        }

        // 2. Form timing check - humans take at least 3 seconds to fill a form
        $formTime = (int)($_POST['form_time'] ?? 0);
        if ($formTime > 0 && (time() - $formTime) < 3) {
            return 'too_fast';
        }

        // 3. Rate limiting - max 3 submissions per 10 minutes per IP
        if ($this->isRateLimited()) {
            return 'rate_limited';
        }

        return true;
    }

    /**
     * Check if IP is rate limited
     */
    private function isRateLimited(): bool
    {
        $ip = $this->getClientIp();
        $cacheDir = BASE_PATH . '/storage/cache/contact_rate';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $cacheFile = $cacheDir . '/' . md5($ip) . '.json';
        $maxAttempts = 3;
        $windowSeconds = 600; // 10 minutes

        $attempts = [];
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if (is_array($data)) {
                // Filter out old attempts
                $attempts = array_filter($data, fn($t) => $t > (time() - $windowSeconds));
            }
        }

        // Check if rate limited
        if (count($attempts) >= $maxAttempts) {
            return true;
        }

        // Record this attempt
        $attempts[] = time();
        file_put_contents($cacheFile, json_encode(array_values($attempts)));

        return false;
    }

    /**
     * Check if content appears to be spam
     */
    private function isSpamContent(string $name, string $email, string $subject, string $message): bool
    {
        // Blacklisted email domains (common spam sources)
        $blacklistedDomains = [
            'mailbox.in.ua', 'tempmail.com', 'guerrillamail.com', 'mailinator.com',
            'throwaway.email', '10minutemail.com', 'fakeinbox.com', 'trashmail.com',
            'maildrop.cc', 'getnada.com', 'mohmal.com', 'temp-mail.org',
            'yopmail.com', 'dispostable.com', 'sharklasers.com', 'spam4.me',
            'grr.la', 'guerrillamail.info', 'pokemail.net', 'spamgourmet.com',
            'mytrashmail.com', 'mt2009.com', 'thankyou2010.com', 'trash2009.com',
            'temporary-mail.net', 'spambox.us', 'emailondeck.com', 'tempinbox.com'
        ];

        $emailDomain = strtolower(substr(strrchr($email, '@'), 1));
        if (in_array($emailDomain, $blacklistedDomains)) {
            return true;
        }

        // Spam patterns in content
        $spamPatterns = [
            '/\$\d+[,.]?\d*\s*(deposit|available|transfer|bitcoin|btc|crypto)/i',
            '/confirm\s+(your|the)\s+(transaction|transfer|deposit)/i',
            '/(casino|poker|slot|betting|lottery|prize|winner|winning)/i',
            '/(viagra|cialis|pharmacy|medication|pills|drugs)/i',
            '/(click\s+here|act\s+now|limited\s+time|urgent)/i',
            '/https?:\/\/[^\s]+\.(ru|cn|tk|ml|ga|cf|gq|xyz)\//i',
            '/(make\s+money|earn\s+\$|work\s+from\s+home)/i',
            '/[\x{0400}-\x{04FF}]{3,}/u', // Cyrillic text (common in spam)
        ];

        $fullContent = $name . ' ' . $email . ' ' . $subject . ' ' . $message;
        foreach ($spamPatterns as $pattern) {
            if (preg_match($pattern, $fullContent)) {
                return true;
            }
        }

        // Check for very short/random content (like the spam you received)
        if (strlen($subject) < 5 && strlen($message) < 10) {
            // Both subject and message very short - likely spam
            if (preg_match('/^[a-z0-9]{4,8}$/i', $subject) && preg_match('/^[a-z0-9]{4,8}$/i', $message)) {
                return true;
            }
        }

        // Check for too many URLs
        if (preg_match_all('/https?:\/\//i', $fullContent) > 3) {
            return true;
        }

        return false;
    }

    /**
     * Get client IP address
     */
    private function getClientIp(): string
    {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Handle comma-separated IPs (X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    // Design Concepts
    public function concept1(): string
    {
        return $this->viewDirect('pages/concept1');
    }

    public function concept2(): string
    {
        return $this->viewDirect('pages/concept2');
    }

    public function concept3(): string
    {
        return $this->viewDirect('pages/concept3');
    }

    public function concept4(): string
    {
        return $this->viewDirect('pages/concept4');
    }

    public function concept5(): string
    {
        return $this->viewDirect('pages/concept5');
    }

    public function concept6(): string
    {
        return $this->viewDirect('pages/concept6');
    }

    public function concept7(): string
    {
        return $this->viewDirect('pages/concept7');
    }

    public function concept8(): string
    {
        return $this->viewDirect('pages/concept8');
    }

    public function concept9(): string
    {
        return $this->viewDirect('pages/concept9');
    }

    public function concept10(): string
    {
        return $this->viewDirect('pages/concept10');
    }

    public function concept11(): string
    {
        return $this->viewDirect('pages/concept11');
    }

    public function concept12(): string
    {
        return $this->viewDirect('pages/concept12');
    }

    public function concept13(): string
    {
        return $this->viewDirect('pages/concept13');
    }

    public function concept14(): string
    {
        return $this->viewDirect('pages/concept14');
    }

    public function concept15(): string
    {
        return $this->viewDirect('pages/concept15');
    }

    public function concept16(): string
    {
        return $this->viewDirect('pages/concept16');
    }

    public function concept17(): string
    {
        return $this->viewDirect('pages/concept17');
    }

    public function concept18(): string
    {
        return $this->viewDirect('pages/concept18');
    }

    public function concept19(): string
    {
        return $this->viewDirect('pages/concept19');
    }

    public function concept20(): string
    {
        return $this->viewDirect('pages/concept20');
    }

    private function viewDirect(string $view): string
    {
        $viewPath = BASE_PATH . '/resources/views/' . $view . '.php';
        if (file_exists($viewPath)) {
            ob_start();
            include $viewPath;
            return ob_get_clean();
        }
        return 'View not found';
    }
}

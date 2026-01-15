<?php
namespace GlamourSchedule\Core;

abstract class Controller
{
    protected Database $db;
    protected array $config;
    protected string $lang = 'nl';

    public function __construct()
    {
        $this->config = require BASE_PATH . '/config/config.php';
        $this->db = new Database($this->config['database']);
        $this->lang = $this->detectLanguage();
    }

    protected function detectLanguage(): string
    {
        $availableLangs = ['nl', 'en', 'de', 'fr'];

        // 1. Check URL parameter (highest priority)
        if (isset($_GET['lang']) && in_array($_GET['lang'], $availableLangs)) {
            $_SESSION['lang'] = $_GET['lang'];
            setcookie('lang', $_GET['lang'], time() + (365 * 24 * 60 * 60), '/');
            return $_GET['lang'];
        }

        // 2. Check session
        if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $availableLangs)) {
            return $_SESSION['lang'];
        }

        // 3. Check cookie
        if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $availableLangs)) {
            $_SESSION['lang'] = $_COOKIE['lang'];
            return $_COOKIE['lang'];
        }

        // 4. Detect from IP address (GeoIP)
        $ipLang = $this->detectLanguageFromIP();
        if ($ipLang && in_array($ipLang, $availableLangs)) {
            $_SESSION['lang'] = $ipLang;
            return $ipLang;
        }

        // 5. Default to English for all other countries
        $_SESSION['lang'] = 'en';
        return 'en';
    }

    protected function detectLanguageFromIP(): ?string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

        // Skip for local IPs
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost']) || strpos($ip, '192.168.') === 0) {
            return null;
        }

        // Check cache first
        $cacheKey = 'geoip_' . md5($ip);
        if (isset($_SESSION[$cacheKey])) {
            return $_SESSION[$cacheKey];
        }

        // Country to language mapping (only NL, BE, DE, FR - all others get English)
        $countryToLang = [
            'NL' => 'nl', // Netherlands
            'BE' => 'nl', // Belgium
            'DE' => 'de', // Germany
            'FR' => 'fr', // France
        ];

        try {
            // Use free ip-api.com service
            $context = stream_context_create(['http' => ['timeout' => 2]]);
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode", false, $context);

            if ($response) {
                $data = json_decode($response, true);
                $countryCode = $data['countryCode'] ?? null;

                if ($countryCode) {
                    // Return mapped language or English for all other countries
                    $lang = $countryToLang[$countryCode] ?? 'en';
                    $_SESSION[$cacheKey] = $lang;
                    return $lang;
                }
            }
        } catch (\Exception $e) {
            // Silently fail - IP detection is optional
        }

        return 'en';
    }

    protected function view(string $template, array $data = []): string
    {
        $data['lang'] = $this->lang;
        $data['translations'] = $this->getTranslations();
        $data['user'] = $this->getCurrentUser();
        $data['config'] = $this->config;

        // Helper function for translations
        $translations = $data['translations'];
        $data['__'] = function($key, $replacements = []) use ($translations) {
            $text = $translations[$key] ?? $key;
            foreach ($replacements as $search => $replace) {
                $text = str_replace(':' . $search, $replace, $text);
            }
            return $text;
        };

        extract($data);

        ob_start();
        $viewPath = BASE_PATH . '/resources/views/' . $template . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View not found: $template";
        }
        return ob_get_clean();
    }

    protected function getTranslations(): array
    {
        $langFile = BASE_PATH . '/resources/lang/' . $this->lang . '/messages.php';
        if (file_exists($langFile)) {
            return require $langFile;
        }
        return require BASE_PATH . '/resources/lang/nl/messages.php';
    }

    protected function getCurrentUser(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        $stmt = $this->db->query(
            "SELECT * FROM users WHERE id = ? AND status = 'active'",
            [$_SESSION['user_id']]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    protected function getCurrentBusiness(): ?array
    {
        if (!isset($_SESSION['business_id'])) {
            return null;
        }
        $stmt = $this->db->query(
            "SELECT * FROM businesses WHERE id = ?",
            [$_SESSION['business_id']]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) || isset($_SESSION['business_id']);
    }

    protected function isBusinessOwner(): bool
    {
        return isset($_SESSION['business_id']);
    }

    protected function isAdmin(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    protected function redirect(string $url): string
    {
        header("Location: $url");
        exit;
    }

    protected function json(array $data, int $status = 200): string
    {
        http_response_code($status);
        header('Content-Type: application/json');
        return json_encode($data);
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            $ruleList = explode('|', $rule);
            foreach ($ruleList as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field] = 'Dit veld is verplicht';
                }
                if (strpos($r, 'min:') === 0 && isset($data[$field])) {
                    $min = (int)substr($r, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field] = "Minimaal $min karakters vereist";
                    }
                }
                if ($r === 'email' && isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Ongeldig e-mailadres';
                }
            }
        }
        return $errors;
    }

    protected function csrf(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}

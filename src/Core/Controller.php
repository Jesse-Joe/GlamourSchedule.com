<?php
namespace GlamourSchedule\Core;

abstract class Controller
{
    protected Database $db;
    protected array $config;
    protected string $lang = 'nl';
    protected ?string $detectedCountry = null;

    public function __construct()
    {
        $this->config = require BASE_PATH . '/config/config.php';
        $this->db = new Database($this->config['database']);
        $this->lang = $this->detectLanguage();
    }

    /**
     * Detect language based on domain, user preference, and IP location
     *
     * Rules:
     * - .com domain: Default to English (international site)
     * - .nl domain: Use IP-based detection (Dutch for NL/BE, etc.)
     * - User preference (URL param, session, cookie) always takes priority
     */
    protected function detectLanguage(): string
    {
        $availableLangs = ['nl', 'en', 'de', 'fr'];
        $currentDomain = Router::getCurrentDomain();

        // 1. Check URL parameter (highest priority - explicit user choice)
        if (isset($_GET['lang']) && in_array($_GET['lang'], $availableLangs)) {
            $_SESSION['lang'] = $_GET['lang'];
            $_SESSION['lang_user_chosen'] = true; // Mark as explicit user choice
            setcookie('lang', $_GET['lang'], time() + (365 * 24 * 60 * 60), '/');
            return $_GET['lang'];
        }

        // 2. Check session (user already made a choice this session)
        if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $availableLangs)) {
            return $_SESSION['lang'];
        }

        // 3. Check cookie (returning user preference)
        if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $availableLangs)) {
            $_SESSION['lang'] = $_COOKIE['lang'];
            $_SESSION['lang_user_chosen'] = true;
            return $_COOKIE['lang'];
        }

        // 4. Domain-based defaults with IP detection
        // Detect country for popup logic
        $countryData = $this->detectCountryFromIP();
        $this->detectedCountry = $countryData['country'] ?? null;
        $_SESSION['detected_country'] = $this->detectedCountry;

        if ($currentDomain === 'com') {
            // .com is international - always default to English
            // But store detected country for potential redirect popup
            $_SESSION['lang'] = 'en';
            return 'en';
        }

        // .nl domain - use IP-based language detection
        $ipLang = $countryData['lang'] ?? 'nl';
        if (in_array($ipLang, $availableLangs)) {
            $_SESSION['lang'] = $ipLang;
            return $ipLang;
        }

        // 5. Default fallback based on domain
        $defaultLang = ($currentDomain === 'com') ? 'en' : 'nl';
        $_SESSION['lang'] = $defaultLang;
        return $defaultLang;
    }

    /**
     * Detect country and suggested language from IP
     * Returns both country code and suggested language
     */
    protected function detectCountryFromIP(): array
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

        // Handle multiple IPs in X-Forwarded-For
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }

        // Skip for local IPs
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost']) || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return ['country' => null, 'lang' => null];
        }

        // Check cache first
        $cacheKey = 'geoip_data_' . md5($ip);
        if (isset($_SESSION[$cacheKey])) {
            return $_SESSION[$cacheKey];
        }

        // Country to language mapping
        $countryToLang = [
            'NL' => 'nl', // Netherlands
            'BE' => 'nl', // Belgium (Dutch)
            'DE' => 'de', // Germany
            'AT' => 'de', // Austria
            'CH' => 'de', // Switzerland (German default)
            'FR' => 'fr', // France
            'LU' => 'fr', // Luxembourg (French default)
            'GB' => 'en', // United Kingdom
            'US' => 'en', // United States
            'CA' => 'en', // Canada
            'AU' => 'en', // Australia
            'IE' => 'en', // Ireland
        ];

        try {
            // Use free ip-api.com service
            $context = stream_context_create(['http' => ['timeout' => 2]]);
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode,country", false, $context);

            if ($response) {
                $data = json_decode($response, true);
                $countryCode = $data['countryCode'] ?? null;

                if ($countryCode) {
                    $result = [
                        'country' => $countryCode,
                        'country_name' => $data['country'] ?? null,
                        'lang' => $countryToLang[$countryCode] ?? 'en'
                    ];
                    $_SESSION[$cacheKey] = $result;
                    return $result;
                }
            }
        } catch (\Exception $e) {
            // Silently fail - IP detection is optional
        }

        return ['country' => null, 'lang' => 'en'];
    }

    /**
     * Check if user should see the domain switch popup
     * Returns popup data or null if no popup needed
     */
    protected function getDomainSwitchPopupData(): ?array
    {
        // Don't show if user has already made a choice
        if (isset($_SESSION['lang_user_chosen']) && $_SESSION['lang_user_chosen']) {
            return null;
        }

        // Don't show if user dismissed the popup
        if (isset($_COOKIE['domain_popup_dismissed'])) {
            return null;
        }

        $currentDomain = Router::getCurrentDomain();
        $detectedCountry = $_SESSION['detected_country'] ?? null;

        if (!$detectedCountry) {
            return null;
        }

        // Country to suggested domain mapping
        $countryToDomain = [
            'NL' => 'nl', // Netherlands -> .nl
            'BE' => 'nl', // Belgium -> .nl (Dutch)
        ];

        $suggestedDomain = $countryToDomain[$detectedCountry] ?? 'com';

        // If user is on .com but detected in NL/BE, suggest .nl
        if ($currentDomain === 'com' && $suggestedDomain === 'nl') {
            return [
                'show' => true,
                'detected_country' => $detectedCountry,
                'current_domain' => 'com',
                'suggested_domain' => 'nl',
                'switch_url' => Router::getSwitchDomainUrl('nl'),
                'stay_url' => null, // Stay on current
            ];
        }

        // If user is on .nl but detected outside NL/BE, suggest .com
        if ($currentDomain === 'nl' && !in_array($detectedCountry, ['NL', 'BE'])) {
            return [
                'show' => true,
                'detected_country' => $detectedCountry,
                'current_domain' => 'nl',
                'suggested_domain' => 'com',
                'switch_url' => Router::getSwitchDomainUrl('com'),
                'stay_url' => null,
            ];
        }

        return null;
    }

    protected function view(string $template, array $data = []): string
    {
        $data['lang'] = $this->lang;
        $data['translations'] = $this->getTranslations();
        $data['user'] = $this->getCurrentUser();
        $data['config'] = $this->config;
        $data['currentDomain'] = Router::getCurrentDomain();
        $data['domainSwitchPopup'] = $this->getDomainSwitchPopupData();
        $data['detectedCountry'] = $_SESSION['detected_country'] ?? null;

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

    /**
     * Generate business URL (with subdomain support)
     * If we're on a business subdomain, use relative URLs
     * Otherwise, generate subdomain URLs
     */
    protected function businessUrl(string $slug, string $path = ''): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        // Check if we're already on a business subdomain
        if ($this->isBusinessSubdomain()) {
            // Use relative URL
            return '/' . ltrim($path, '/');
        }

        // Generate subdomain URL
        // Determine base domain
        $baseDomain = 'glamourschedule.nl';
        if (str_contains($host, 'glamourschedule.com')) {
            $baseDomain = 'glamourschedule.com';
        }

        $subdomainUrl = "{$protocol}://{$slug}.{$baseDomain}";

        if ($path) {
            $subdomainUrl .= '/' . ltrim($path, '/');
        }

        return $subdomainUrl;
    }

    /**
     * Check if current request is on a business subdomain
     */
    protected function isBusinessSubdomain(): bool
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $host = preg_replace('/:\d+$/', '', $host);

        $mainDomains = ['glamourschedule.nl', 'glamourschedule.com', 'www.glamourschedule.nl', 'www.glamourschedule.com', 'new.glamourschedule.nl', 'localhost'];

        if (in_array($host, $mainDomains)) {
            return false;
        }

        // Check if it's a subdomain
        foreach (['glamourschedule.nl', 'glamourschedule.com'] as $baseDomain) {
            if (str_ends_with($host, '.' . $baseDomain)) {
                $subdomain = str_replace('.' . $baseDomain, '', $host);
                if ($subdomain !== 'www' && $subdomain !== 'new') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the current business subdomain slug, or null if not on a subdomain
     */
    protected function getBusinessSubdomain(): ?string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $host = preg_replace('/:\d+$/', '', $host);

        foreach (['glamourschedule.nl', 'glamourschedule.com'] as $baseDomain) {
            if (str_ends_with($host, '.' . $baseDomain)) {
                $subdomain = str_replace('.' . $baseDomain, '', $host);
                if ($subdomain !== 'www' && $subdomain !== 'new') {
                    return $subdomain;
                }
            }
        }

        return null;
    }
}

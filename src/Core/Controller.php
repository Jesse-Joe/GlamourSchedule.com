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
     * - .nl domain: Always Dutch (not used anymore, focus on .com)
     * - .com domain: IP-based detection with popup to choose language
     * - User preference (URL param or cookie) can override
     */
    protected function detectLanguage(): string
    {
        $availableLangs = [
            'nl', 'en', 'de', 'fr', 'es', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar', 'tr', 'pl',
            'sv', 'no', 'da', 'fi', 'el', 'cs', 'hu', 'ro', 'bg', 'hr', 'sk', 'sl', 'et', 'lv',
            'lt', 'uk', 'hi', 'th', 'vi', 'id', 'ms', 'tl', 'he', 'fa', 'sw', 'af'
        ];
        $currentDomain = Router::getCurrentDomain();

        // 1. Check URL parameter (highest priority - explicit user choice)
        if (isset($_GET['lang']) && in_array($_GET['lang'], $availableLangs)) {
            $_SESSION['lang'] = $_GET['lang'];
            $_SESSION['lang_user_chosen'] = true;
            setcookie('lang', $_GET['lang'], [
                'expires' => time() + (365 * 24 * 60 * 60),
                'path' => '/',
                'secure' => true,
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
            return $_GET['lang'];
        }

        // 2. Check cookie (user previously made a choice)
        if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $availableLangs)) {
            $_SESSION['lang'] = $_COOKIE['lang'];
            $_SESSION['lang_user_chosen'] = true;
            return $_COOKIE['lang'];
        }

        // 3. For .com domain: detect country from IP and set suggested language
        if ($currentDomain === 'com') {
            $ipData = $this->detectCountryFromIP();

            // Store detected country for popup
            if ($ipData['country']) {
                $_SESSION['detected_country'] = $ipData['country'];
                $_SESSION['detected_country_name'] = $ipData['country_name'] ?? null;
                $_SESSION['detected_lang'] = $ipData['lang'] ?? 'en';
            }

            // Default to English, popup will offer to switch
            $_SESSION['lang'] = 'en';
            return 'en';
        }

        // 4. .nl domain: Always Dutch
        $_SESSION['lang'] = 'nl';
        return 'nl';
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

        // Country to language mapping (ISO 3166-1 alpha-2 to language code)
        $countryToLang = [
            // English
            'GB' => 'en', 'US' => 'en', 'CA' => 'en', 'AU' => 'en', 'NZ' => 'en', 'IE' => 'en',
            // Dutch
            'NL' => 'nl', 'BE' => 'nl', 'SR' => 'nl',
            // German
            'DE' => 'de', 'AT' => 'de', 'CH' => 'de', 'LI' => 'de',
            // French
            'FR' => 'fr', 'LU' => 'fr', 'MC' => 'fr', 'SN' => 'fr', 'CI' => 'fr',
            // Spanish
            'ES' => 'es', 'MX' => 'es', 'AR' => 'es', 'CO' => 'es', 'CL' => 'es', 'PE' => 'es', 'VE' => 'es', 'EC' => 'es', 'GT' => 'es', 'CU' => 'es', 'BO' => 'es', 'DO' => 'es', 'HN' => 'es', 'PY' => 'es', 'SV' => 'es', 'NI' => 'es', 'CR' => 'es', 'PA' => 'es', 'UY' => 'es',
            // Italian
            'IT' => 'it', 'SM' => 'it', 'VA' => 'it',
            // Portuguese
            'PT' => 'pt', 'BR' => 'pt', 'AO' => 'pt', 'MZ' => 'pt',
            // Russian
            'RU' => 'ru', 'BY' => 'ru', 'KZ' => 'ru', 'KG' => 'ru',
            // Japanese
            'JP' => 'ja',
            // Korean
            'KR' => 'ko', 'KP' => 'ko',
            // Chinese
            'CN' => 'zh', 'TW' => 'zh', 'HK' => 'zh', 'MO' => 'zh', 'SG' => 'zh',
            // Arabic
            'SA' => 'ar', 'AE' => 'ar', 'EG' => 'ar', 'DZ' => 'ar', 'MA' => 'ar', 'IQ' => 'ar', 'SD' => 'ar', 'SY' => 'ar', 'YE' => 'ar', 'TN' => 'ar', 'JO' => 'ar', 'LY' => 'ar', 'LB' => 'ar', 'OM' => 'ar', 'KW' => 'ar', 'QA' => 'ar', 'BH' => 'ar',
            // Turkish
            'TR' => 'tr', 'CY' => 'tr',
            // Polish
            'PL' => 'pl',
            // Swedish
            'SE' => 'sv',
            // Norwegian
            'NO' => 'no',
            // Danish
            'DK' => 'da',
            // Finnish
            'FI' => 'fi',
            // Greek
            'GR' => 'el',
            // Czech
            'CZ' => 'cs',
            // Hungarian
            'HU' => 'hu',
            // Romanian
            'RO' => 'ro', 'MD' => 'ro',
            // Bulgarian
            'BG' => 'bg',
            // Croatian
            'HR' => 'hr', 'BA' => 'hr',
            // Slovak
            'SK' => 'sk',
            // Slovenian
            'SI' => 'sl',
            // Estonian
            'EE' => 'et',
            // Latvian
            'LV' => 'lv',
            // Lithuanian
            'LT' => 'lt',
            // Ukrainian
            'UA' => 'uk',
            // Hindi
            'IN' => 'hi',
            // Thai
            'TH' => 'th',
            // Vietnamese
            'VN' => 'vi',
            // Indonesian
            'ID' => 'id',
            // Malay
            'MY' => 'ms', 'BN' => 'ms',
            // Tagalog/Filipino
            'PH' => 'tl',
            // Hebrew
            'IL' => 'he',
            // Persian/Farsi
            'IR' => 'fa', 'AF' => 'fa',
            // Swahili
            'KE' => 'sw', 'TZ' => 'sw', 'UG' => 'sw',
            // Afrikaans
            'ZA' => 'af', 'NA' => 'af',
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
     * Check if user should see the language selection popup on .com
     * Shows popup when detected language differs from English
     * Returns popup data or null if no popup needed
     */
    protected function getLanguagePopupData(): ?array
    {
        $currentDomain = Router::getCurrentDomain();

        // Only show on .com domain
        if ($currentDomain !== 'com') {
            return null;
        }

        // Don't show if user has already made a choice
        if (isset($_SESSION['lang_user_chosen']) && $_SESSION['lang_user_chosen']) {
            return null;
        }

        // Don't show if user dismissed the popup
        if (isset($_COOKIE['lang_popup_dismissed'])) {
            return null;
        }

        $detectedCountry = $_SESSION['detected_country'] ?? null;
        $detectedLang = $_SESSION['detected_lang'] ?? 'en';
        $detectedCountryName = $_SESSION['detected_country_name'] ?? null;

        // Don't show popup if no country detected or if detected language is English
        if (!$detectedCountry || $detectedLang === 'en') {
            return null;
        }

        // Language names in different languages
        $langNames = [
            'nl' => ['en' => 'Dutch', 'nl' => 'Nederlands', 'de' => 'Niederländisch', 'fr' => 'Néerlandais'],
            'de' => ['en' => 'German', 'nl' => 'Duits', 'de' => 'Deutsch', 'fr' => 'Allemand'],
            'fr' => ['en' => 'French', 'nl' => 'Frans', 'de' => 'Französisch', 'fr' => 'Français'],
            'en' => ['en' => 'English', 'nl' => 'Engels', 'de' => 'Englisch', 'fr' => 'Anglais'],
        ];

        // Country names for display
        $countryNames = [
            'NL' => 'the Netherlands',
            'BE' => 'Belgium',
            'DE' => 'Germany',
            'AT' => 'Austria',
            'CH' => 'Switzerland',
            'FR' => 'France',
            'LU' => 'Luxembourg',
        ];

        return [
            'show' => true,
            'detected_country' => $detectedCountry,
            'detected_country_name' => $countryNames[$detectedCountry] ?? $detectedCountryName ?? $detectedCountry,
            'detected_lang' => $detectedLang,
            'detected_lang_name' => $langNames[$detectedLang]['en'] ?? $detectedLang,
            'detected_lang_native' => $langNames[$detectedLang][$detectedLang] ?? $detectedLang,
            'current_lang' => 'en',
            'switch_url' => '?lang=' . $detectedLang,
            'stay_url' => '?lang=en',
        ];
    }

    /**
     * Legacy method - redirects to new language popup
     * @deprecated Use getLanguagePopupData() instead
     */
    protected function getDomainSwitchPopupData(): ?array
    {
        return $this->getLanguagePopupData();
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
     * Generate business URL using UUID
     */
    protected function businessUrl(string $uuid, string $path = ''): string
    {
        $baseUrl = '/s/' . $uuid;
        return $path ? $baseUrl . '/' . ltrim($path, '/') : $baseUrl;
    }
}

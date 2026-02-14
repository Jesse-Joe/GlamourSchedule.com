<?php
/**
 * GlamourSchedule - Hoofdconfiguratie
 * 
 * @package GlamourSchedule
 * @version 2.1.0
 */

// Voorkom directe toegang
if (!defined('GLAMOUR_LOADED')) {
    die('Direct access not allowed');
}

// Load .env file
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            putenv(trim($name) . '=' . trim($value));
        }
    }
}

return [
    // ═══════════════════════════════════════════════════════════════════════
    // APPLICATIE SETTINGS (loaded from .env for security)
    // ═══════════════════════════════════════════════════════════════════════
    'app' => [
        'name' => 'GlamourSchedule',
        'version' => '2.1.0',
        'url' => getenv('APP_URL') ?: 'https://glamourschedule.com',
        'debug' => getenv('APP_DEBUG') === 'true',
        'timezone' => 'Europe/Amsterdam',
        'locale' => 'en',
        'key' => getenv('APP_KEY') ?: '',
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // DATABASE SETTINGS (loaded from .env for security)
    // ═══════════════════════════════════════════════════════════════════════
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => (int)(getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_DATABASE') ?: '',
        'user' => getenv('DB_USERNAME') ?: '',
        'pass' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'timezone' => 'Europe/Amsterdam',
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // PRICING CONFIGURATIE
    // ═══════════════════════════════════════════════════════════════════════
    'pricing' => [
        // Klanten - GRATIS
        'customer_registration_fee' => 0.00,
        'customer_guest_checkout' => true,
        
        // Bedrijven
        'business_registration_fee' => 99.99,
        'business_early_adopter_fee' => 0.99,
        'early_adopter_limit' => 100,
        
        // Per boeking
        'admin_fee_per_booking' => 1.75,
        'monthly_fee_booking_limit' => 25,
        
        // Annulering
        'cancellation_percentage' => 50,
        'free_cancellation_hours' => 24,
        
        // Uitbetaling
        'payout_days' => 14,
        
        // Sales Partners
        'sales_commission_per_signup' => 25.00,
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // MULTILANGUAGE SETTINGS - Global Support (88 languages)
    // ═══════════════════════════════════════════════════════════════════════
    'languages' => [
        'available' => [
            // Europe
            'nl', 'en', 'us', 'de', 'fr', 'es', 'pt', 'it', 'ru', 'pl', 'uk', 'be',
            'sv', 'no', 'da', 'fi', 'is', 'el', 'cs', 'hu', 'ro', 'bg', 'hr', 'sr',
            'bs', 'sk', 'sl', 'mk', 'sq', 'et', 'lv', 'lt', 'mt', 'lb', 'cy', 'ga',
            'ca', 'eu', 'gl',
            // Asia
            'ja', 'ko', 'zh', 'hi', 'bn', 'pa', 'ur', 'ta', 'te', 'mr', 'gu', 'kn',
            'ml', 'th', 'vi', 'id', 'ms', 'tl', 'my', 'km', 'lo', 'ne', 'si', 'mn',
            'ka', 'hy', 'az', 'kk', 'uz', 'ky', 'tg', 'tk', 'ps', 'ku',
            // Middle East
            'ar', 'he', 'fa', 'tr',
            // Africa
            'sw', 'af', 'am', 'ha', 'yo', 'ig', 'zu', 'xh', 'so', 'mg', 'rw'
        ],
        'default' => 'en',
        'fallback' => 'nl',
        'detect_from_ip' => true,
        'detect_from_browser' => true,
        'names' => [
            // Europe - Western
            'nl' => 'Nederlands',
            'en' => 'English (UK)',
            'us' => 'English (US)',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'pt' => 'Português',
            'it' => 'Italiano',
            'ca' => 'Català',
            'eu' => 'Euskara',
            'gl' => 'Galego',
            'cy' => 'Cymraeg',
            'ga' => 'Gaeilge',
            'lb' => 'Lëtzebuergesch',
            'mt' => 'Malti',
            // Europe - Nordic
            'sv' => 'Svenska',
            'no' => 'Norsk',
            'da' => 'Dansk',
            'fi' => 'Suomi',
            'is' => 'Íslenska',
            // Europe - Eastern
            'ru' => 'Русский',
            'uk' => 'Українська',
            'be' => 'Беларуская',
            'pl' => 'Polski',
            'cs' => 'Čeština',
            'sk' => 'Slovenčina',
            'hu' => 'Magyar',
            'ro' => 'Română',
            'bg' => 'Български',
            'mk' => 'Македонски',
            // Europe - Balkan
            'hr' => 'Hrvatski',
            'sr' => 'Српски',
            'bs' => 'Bosanski',
            'sl' => 'Slovenščina',
            'sq' => 'Shqip',
            'el' => 'Ελληνικά',
            // Europe - Baltic
            'et' => 'Eesti',
            'lv' => 'Latviešu',
            'lt' => 'Lietuvių',
            // Asia - East
            'ja' => '日本語',
            'ko' => '한국어',
            'zh' => '中文',
            'mn' => 'Монгол',
            // Asia - South
            'hi' => 'हिन्दी',
            'bn' => 'বাংলা',
            'pa' => 'ਪੰਜਾਬੀ',
            'ur' => 'اردو',
            'ta' => 'தமிழ்',
            'te' => 'తెలుగు',
            'mr' => 'मराठी',
            'gu' => 'ગુજરાતી',
            'kn' => 'ಕನ್ನಡ',
            'ml' => 'മലയാളം',
            'ne' => 'नेपाली',
            'si' => 'සිංහල',
            // Asia - Southeast
            'th' => 'ไทย',
            'vi' => 'Tiếng Việt',
            'id' => 'Bahasa Indonesia',
            'ms' => 'Bahasa Melayu',
            'tl' => 'Tagalog',
            'my' => 'မြန်မာ',
            'km' => 'ភាសាខ្មែរ',
            'lo' => 'ລາວ',
            // Asia - Caucasus & Central
            'ka' => 'ქართული',
            'hy' => 'Հայերեն',
            'az' => 'Azərbaycan',
            'kk' => 'Қазақ',
            'uz' => 'Oʻzbek',
            'ky' => 'Кыргызча',
            'tg' => 'Тоҷикӣ',
            'tk' => 'Türkmen',
            'ps' => 'پښتو',
            'ku' => 'Kurdî',
            // Middle East
            'ar' => 'العربية',
            'he' => 'עברית',
            'fa' => 'فارسی',
            'tr' => 'Türkçe',
            // Africa
            'sw' => 'Kiswahili',
            'af' => 'Afrikaans',
            'am' => 'አማርኛ',
            'ha' => 'Hausa',
            'yo' => 'Yorùbá',
            'ig' => 'Igbo',
            'zu' => 'isiZulu',
            'xh' => 'isiXhosa',
            'so' => 'Soomaali',
            'mg' => 'Malagasy',
            'rw' => 'Kinyarwanda',
        ],
        'flags' => [
            // Europe - Western
            'nl' => '🇳🇱',
            'en' => '🇬🇧',
            'us' => '🇺🇸',
            'de' => '🇩🇪',
            'fr' => '🇫🇷',
            'es' => '🇪🇸',
            'pt' => '🇵🇹',
            'it' => '🇮🇹',
            'ca' => '🇪🇸',
            'eu' => '🇪🇸',
            'gl' => '🇪🇸',
            'cy' => '🏴󠁧󠁢󠁷󠁬󠁳󠁿',
            'ga' => '🇮🇪',
            'lb' => '🇱🇺',
            'mt' => '🇲🇹',
            // Europe - Nordic
            'sv' => '🇸🇪',
            'no' => '🇳🇴',
            'da' => '🇩🇰',
            'fi' => '🇫🇮',
            'is' => '🇮🇸',
            // Europe - Eastern
            'ru' => '🇷🇺',
            'uk' => '🇺🇦',
            'be' => '🇧🇾',
            'pl' => '🇵🇱',
            'cs' => '🇨🇿',
            'sk' => '🇸🇰',
            'hu' => '🇭🇺',
            'ro' => '🇷🇴',
            'bg' => '🇧🇬',
            'mk' => '🇲🇰',
            // Europe - Balkan
            'hr' => '🇭🇷',
            'sr' => '🇷🇸',
            'bs' => '🇧🇦',
            'sl' => '🇸🇮',
            'sq' => '🇦🇱',
            'el' => '🇬🇷',
            // Europe - Baltic
            'et' => '🇪🇪',
            'lv' => '🇱🇻',
            'lt' => '🇱🇹',
            // Asia - East
            'ja' => '🇯🇵',
            'ko' => '🇰🇷',
            'zh' => '🇨🇳',
            'mn' => '🇲🇳',
            // Asia - South
            'hi' => '🇮🇳',
            'bn' => '🇧🇩',
            'pa' => '🇮🇳',
            'ur' => '🇵🇰',
            'ta' => '🇮🇳',
            'te' => '🇮🇳',
            'mr' => '🇮🇳',
            'gu' => '🇮🇳',
            'kn' => '🇮🇳',
            'ml' => '🇮🇳',
            'ne' => '🇳🇵',
            'si' => '🇱🇰',
            // Asia - Southeast
            'th' => '🇹🇭',
            'vi' => '🇻🇳',
            'id' => '🇮🇩',
            'ms' => '🇲🇾',
            'tl' => '🇵🇭',
            'my' => '🇲🇲',
            'km' => '🇰🇭',
            'lo' => '🇱🇦',
            // Asia - Caucasus & Central
            'ka' => '🇬🇪',
            'hy' => '🇦🇲',
            'az' => '🇦🇿',
            'kk' => '🇰🇿',
            'uz' => '🇺🇿',
            'ky' => '🇰🇬',
            'tg' => '🇹🇯',
            'tk' => '🇹🇲',
            'ps' => '🇦🇫',
            'ku' => '🇮🇶',
            // Middle East
            'ar' => '🇸🇦',
            'he' => '🇮🇱',
            'fa' => '🇮🇷',
            'tr' => '🇹🇷',
            // Africa
            'sw' => '🇰🇪',
            'af' => '🇿🇦',
            'am' => '🇪🇹',
            'ha' => '🇳🇬',
            'yo' => '🇳🇬',
            'ig' => '🇳🇬',
            'zu' => '🇿🇦',
            'xh' => '🇿🇦',
            'so' => '🇸🇴',
            'mg' => '🇲🇬',
            'rw' => '🇷🇼',
        ],
        // RTL (Right-to-Left) languages
        'rtl' => ['ar', 'he', 'fa', 'ur', 'ps', 'ku'],
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // THEMA SETTINGS - Ultra Premium Exclusive
    // ═══════════════════════════════════════════════════════════════════════
    'themes' => [
        'modes' => ['light', 'dark'],
        'default_mode' => 'dark',

        // Light mode - Bright & Fresh
        'light' => [
            'primary' => '#000000',        // Luxury Violet
            'secondary' => '#000000',      // Rose Pink
            'accent' => '#fbbf24',         // Gold
            'background' => '#ffffff',
            'card' => '#ffffff',
            'text' => '#1a1a2e',
            'text_secondary' => '#4a4a5a',
            'border' => 'rgba(0,0,0,0.08)',
        ],

        // Dark mode - Deep Luxury (Default)
        'dark' => [
            'primary' => '#000000',        // Luxury Violet
            'secondary' => '#000000',      // Rose Pink
            'accent' => '#fbbf24',         // Gold
            'background' => '#0a0a0f',
            'card' => '#1a1a25',
            'text' => '#ffffff',
            'text_secondary' => 'rgba(255,255,255,0.7)',
            'border' => 'rgba(255,255,255,0.1)',
        ],

        // Status kleuren
        'status' => [
            'success' => '#16a34a',
            'warning' => '#404040',
            'error' => '#dc2626',
            'info' => '#0284c7',
        ],
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // MOLLIE PAYMENT SETTINGS
    // ═══════════════════════════════════════════════════════════════════════
    'mollie' => [
        'api_key' => getenv('MOLLIE_API_KEY') ?: '',
        'test_mode' => false,
        'webhook_url' => 'https://glamourschedule.com/api/webhooks/mollie',
        'redirect_url' => 'https://glamourschedule.com/payment/complete',
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // STRIPE PAYMENT SETTINGS (Internationaal)
    // ═══════════════════════════════════════════════════════════════════════
    'stripe' => [
        'public_key' => getenv('STRIPE_PUBLIC_KEY') ?: '',
        'secret_key' => getenv('STRIPE_SECRET_KEY') ?: '',
        'webhook_secret' => getenv('STRIPE_WEBHOOK_SECRET') ?: '',
        'webhook_url' => 'https://glamourschedule.com/api/webhooks/stripe',
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // EMAIL SETTINGS
    // ═══════════════════════════════════════════════════════════════════════
    'mail' => [
        'driver' => 'smtp',
        'host' => getenv('MAIL_HOST') ?: 'localhost',
        'port' => getenv('MAIL_PORT') ?: 25,
        'username' => getenv('MAIL_USERNAME') ?: '',
        'password' => getenv('MAIL_PASSWORD') ?: '',
        'encryption' => getenv('MAIL_ENCRYPTION') ?: '',
        'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@glamourschedule.com',
        'from_name' => 'GlamourSchedule',
        'fallback' => [
            'host' => getenv('MAIL_FALLBACK_HOST') ?: '',
            'port' => getenv('MAIL_FALLBACK_PORT') ?: 587,
            'username' => getenv('MAIL_FALLBACK_USERNAME') ?: '',
            'password' => getenv('MAIL_FALLBACK_PASSWORD') ?: '',
            'encryption' => getenv('MAIL_FALLBACK_ENCRYPTION') ?: 'tls',
        ],
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // SMS SETTINGS (MessageBird)
    // ═══════════════════════════════════════════════════════════════════════
    'sms' => [
        'enabled' => !empty(getenv('MESSAGEBIRD_API_KEY')),
        'api_key' => getenv('MESSAGEBIRD_API_KEY') ?: '',
        'originator' => getenv('MESSAGEBIRD_ORIGINATOR') ?: 'GlamourSched',
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // GOOGLE MAPS SETTINGS
    // ═══════════════════════════════════════════════════════════════════════
    'google_maps' => [
        'api_key' => getenv('GOOGLE_MAPS_API_KEY') ?: '',
        'default_center' => [
            'lat' => 52.3676,  // Amsterdam
            'lng' => 4.9041,
        ],
        'default_zoom' => 12,
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // GEOIP SETTINGS (voor taaldetectie) - Complete World Coverage
    // ═══════════════════════════════════════════════════════════════════════
    'geoip' => [
        'enabled' => true,
        'provider' => 'ip-api',
        'country_language_map' => [
            // Europe - Western
            'NL' => 'nl', // Netherlands
            'BE' => 'nl', // Belgium
            'SR' => 'nl', // Suriname
            'DE' => 'de', // Germany
            'AT' => 'de', // Austria
            'CH' => 'de', // Switzerland
            'LI' => 'de', // Liechtenstein
            'LU' => 'de', // Luxembourg
            'FR' => 'fr', // France
            'MC' => 'fr', // Monaco
            'GB' => 'en', // United Kingdom
            'IE' => 'en', // Ireland
            'IT' => 'it', // Italy
            'SM' => 'it', // San Marino
            'VA' => 'it', // Vatican
            'ES' => 'es', // Spain
            'AD' => 'es', // Andorra
            'PT' => 'pt', // Portugal

            // Europe - Northern (Scandinavia)
            'SE' => 'sv', // Sweden
            'NO' => 'no', // Norway
            'DK' => 'da', // Denmark
            'FI' => 'fi', // Finland
            'IS' => 'en', // Iceland (fallback to English)

            // Europe - Eastern
            'PL' => 'pl', // Poland
            'CZ' => 'cs', // Czech Republic
            'SK' => 'sk', // Slovakia
            'HU' => 'hu', // Hungary
            'RO' => 'ro', // Romania
            'MD' => 'ro', // Moldova
            'BG' => 'bg', // Bulgaria
            'SI' => 'sl', // Slovenia
            'HR' => 'hr', // Croatia
            'BA' => 'hr', // Bosnia
            'RS' => 'hr', // Serbia (similar)
            'ME' => 'hr', // Montenegro
            'MK' => 'bg', // North Macedonia
            'AL' => 'en', // Albania
            'XK' => 'en', // Kosovo

            // Europe - Baltic
            'EE' => 'et', // Estonia
            'LV' => 'lv', // Latvia
            'LT' => 'lt', // Lithuania

            // Europe - Eastern Slavic
            'RU' => 'ru', // Russia
            'UA' => 'uk', // Ukraine
            'BY' => 'ru', // Belarus

            // Europe - Southern
            'GR' => 'el', // Greece
            'CY' => 'el', // Cyprus
            'TR' => 'tr', // Turkey
            'MT' => 'en', // Malta

            // Americas - North
            'US' => 'en', // United States
            'CA' => 'en', // Canada
            'MX' => 'es', // Mexico

            // Americas - Central
            'GT' => 'es', // Guatemala
            'BZ' => 'en', // Belize
            'SV' => 'es', // El Salvador
            'HN' => 'es', // Honduras
            'NI' => 'es', // Nicaragua
            'CR' => 'es', // Costa Rica
            'PA' => 'es', // Panama

            // Americas - Caribbean
            'CU' => 'es', // Cuba
            'DO' => 'es', // Dominican Republic
            'PR' => 'es', // Puerto Rico
            'JM' => 'en', // Jamaica
            'HT' => 'fr', // Haiti
            'TT' => 'en', // Trinidad and Tobago
            'BB' => 'en', // Barbados
            'BS' => 'en', // Bahamas
            'AW' => 'nl', // Aruba
            'CW' => 'nl', // Curaçao
            'SX' => 'nl', // Sint Maarten

            // Americas - South
            'BR' => 'pt', // Brazil
            'AR' => 'es', // Argentina
            'CL' => 'es', // Chile
            'CO' => 'es', // Colombia
            'PE' => 'es', // Peru
            'VE' => 'es', // Venezuela
            'EC' => 'es', // Ecuador
            'BO' => 'es', // Bolivia
            'PY' => 'es', // Paraguay
            'UY' => 'es', // Uruguay
            'GY' => 'en', // Guyana
            'GF' => 'fr', // French Guiana

            // Asia - East
            'JP' => 'ja', // Japan
            'KR' => 'ko', // South Korea
            'CN' => 'zh', // China
            'TW' => 'zh', // Taiwan
            'HK' => 'zh', // Hong Kong
            'MO' => 'zh', // Macau
            'MN' => 'ru', // Mongolia (Russian common)

            // Asia - Southeast
            'TH' => 'th', // Thailand
            'VN' => 'vi', // Vietnam
            'ID' => 'id', // Indonesia
            'MY' => 'ms', // Malaysia
            'SG' => 'en', // Singapore
            'PH' => 'tl', // Philippines
            'MM' => 'en', // Myanmar
            'KH' => 'en', // Cambodia
            'LA' => 'en', // Laos
            'BN' => 'ms', // Brunei
            'TL' => 'pt', // Timor-Leste

            // Asia - South
            'IN' => 'hi', // India
            'PK' => 'en', // Pakistan
            'BD' => 'en', // Bangladesh
            'LK' => 'en', // Sri Lanka
            'NP' => 'hi', // Nepal
            'BT' => 'en', // Bhutan
            'MV' => 'en', // Maldives

            // Asia - Central
            'KZ' => 'ru', // Kazakhstan
            'UZ' => 'ru', // Uzbekistan
            'TM' => 'ru', // Turkmenistan
            'KG' => 'ru', // Kyrgyzstan
            'TJ' => 'ru', // Tajikistan
            'AF' => 'fa', // Afghanistan

            // Asia - West (Middle East)
            'IR' => 'fa', // Iran
            'IQ' => 'ar', // Iraq
            'SA' => 'ar', // Saudi Arabia
            'AE' => 'ar', // UAE
            'KW' => 'ar', // Kuwait
            'QA' => 'ar', // Qatar
            'BH' => 'ar', // Bahrain
            'OM' => 'ar', // Oman
            'YE' => 'ar', // Yemen
            'JO' => 'ar', // Jordan
            'LB' => 'ar', // Lebanon
            'SY' => 'ar', // Syria
            'IL' => 'he', // Israel
            'PS' => 'ar', // Palestine

            // Asia - Caucasus
            'GE' => 'en', // Georgia
            'AM' => 'en', // Armenia
            'AZ' => 'tr', // Azerbaijan (Turkic)

            // Africa - North
            'EG' => 'ar', // Egypt
            'LY' => 'ar', // Libya
            'TN' => 'ar', // Tunisia
            'DZ' => 'ar', // Algeria
            'MA' => 'ar', // Morocco
            'SD' => 'ar', // Sudan

            // Africa - West
            'NG' => 'en', // Nigeria
            'GH' => 'en', // Ghana
            'SN' => 'fr', // Senegal
            'CI' => 'fr', // Ivory Coast
            'ML' => 'fr', // Mali
            'BF' => 'fr', // Burkina Faso
            'NE' => 'fr', // Niger
            'GN' => 'fr', // Guinea
            'BJ' => 'fr', // Benin
            'TG' => 'fr', // Togo
            'SL' => 'en', // Sierra Leone
            'LR' => 'en', // Liberia
            'GM' => 'en', // Gambia
            'GW' => 'pt', // Guinea-Bissau
            'CV' => 'pt', // Cape Verde
            'MR' => 'ar', // Mauritania

            // Africa - East
            'KE' => 'sw', // Kenya
            'TZ' => 'sw', // Tanzania
            'UG' => 'en', // Uganda
            'RW' => 'en', // Rwanda
            'BI' => 'fr', // Burundi
            'ET' => 'en', // Ethiopia
            'ER' => 'en', // Eritrea
            'SO' => 'ar', // Somalia
            'DJ' => 'fr', // Djibouti
            'MG' => 'fr', // Madagascar
            'MU' => 'en', // Mauritius
            'SC' => 'en', // Seychelles
            'KM' => 'ar', // Comoros

            // Africa - Central
            'CD' => 'fr', // DR Congo
            'CG' => 'fr', // Congo
            'CF' => 'fr', // Central African Republic
            'CM' => 'fr', // Cameroon
            'TD' => 'fr', // Chad
            'GA' => 'fr', // Gabon
            'GQ' => 'es', // Equatorial Guinea
            'ST' => 'pt', // São Tomé and Príncipe
            'AO' => 'pt', // Angola

            // Africa - Southern
            'ZA' => 'af', // South Africa
            'NA' => 'en', // Namibia
            'BW' => 'en', // Botswana
            'ZW' => 'en', // Zimbabwe
            'ZM' => 'en', // Zambia
            'MW' => 'en', // Malawi
            'MZ' => 'pt', // Mozambique
            'SZ' => 'en', // Eswatini
            'LS' => 'en', // Lesotho

            // Oceania
            'AU' => 'en', // Australia
            'NZ' => 'en', // New Zealand
            'PG' => 'en', // Papua New Guinea
            'FJ' => 'en', // Fiji
            'SB' => 'en', // Solomon Islands
            'VU' => 'en', // Vanuatu
            'WS' => 'en', // Samoa
            'TO' => 'en', // Tonga
            'NC' => 'fr', // New Caledonia
            'PF' => 'fr', // French Polynesia
            'GU' => 'en', // Guam
        ],
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // UPLOAD SETTINGS
    // ═══════════════════════════════════════════════════════════════════════
    'uploads' => [
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'image_quality' => 85,
        'thumbnails' => [
            'small' => [150, 150],
            'medium' => [400, 400],
            'large' => [800, 800],
        ],
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // SECURITY SETTINGS
    // ═══════════════════════════════════════════════════════════════════════
    'security' => [
        'session_lifetime' => 2592000, // 30 dagen
        'remember_me_lifetime' => 2592000, // 30 dagen
        'password_min_length' => 8,
        'rate_limit' => [
            'login_attempts' => 5,
            'lockout_minutes' => 15,
        ],
        'csrf_enabled' => true,
    ],
];

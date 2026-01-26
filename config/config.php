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
    // APPLICATIE SETTINGS
    // ═══════════════════════════════════════════════════════════════════════
    'app' => [
        'name' => 'GlamourSchedule',
        'version' => '2.1.0',
        'url' => 'https://glamourschedule.com',
        'debug' => false,
        'timezone' => 'Europe/Amsterdam',
        'locale' => 'nl',
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // DATABASE SETTINGS
    // ═══════════════════════════════════════════════════════════════════════
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'glamourschedule_db',
        'user' => 'glamour_user',
        'pass' => '+AtQ3Vs2Vd6FYcyTPMRX7UhLsG0B6MLX9q05Cg8l32E=',
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
        
        // Annulering
        'cancellation_percentage' => 50,
        'free_cancellation_hours' => 24,
        
        // Uitbetaling
        'payout_days' => 14,
        
        // Sales Partners
        'sales_commission_per_signup' => 25.00,
    ],
    
    // ═══════════════════════════════════════════════════════════════════════
    // MULTILANGUAGE SETTINGS
    // ═══════════════════════════════════════════════════════════════════════
    'languages' => [
        'available' => ['nl', 'en', 'de', 'fr'],
        'default' => 'nl',
        'fallback' => 'en',
        'detect_from_ip' => true,
        'detect_from_browser' => true,
        'names' => [
            'nl' => 'Nederlands',
            'en' => 'English',
            'de' => 'Deutsch',
            'fr' => 'Français',
        ],
        'flags' => [
            'nl' => '🇳🇱',
            'en' => '🇬🇧',
            'de' => '🇩🇪',
            'fr' => '🇫🇷',
        ],
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
    // EMAIL SETTINGS
    // ═══════════════════════════════════════════════════════════════════════
    'mail' => [
        'driver' => 'smtp',
        'host' => getenv('MAIL_HOST') ?: 'smtp.mailtrap.io',
        'port' => getenv('MAIL_PORT') ?: 587,
        'username' => getenv('MAIL_USERNAME') ?: '',
        'password' => getenv('MAIL_PASSWORD') ?: '',
        'encryption' => 'tls',
        'from_address' => 'noreply@glamourschedule.com',
        'from_name' => 'GlamourSchedule',
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
    // GEOIP SETTINGS (voor taaldetectie)
    // ═══════════════════════════════════════════════════════════════════════
    'geoip' => [
        'enabled' => true,
        'provider' => 'ip-api', // Gratis service
        'country_language_map' => [
            'NL' => 'nl',
            'BE' => 'nl', // België - Nederlands
            'DE' => 'de',
            'AT' => 'de', // Oostenrijk
            'CH' => 'de', // Zwitserland
            'FR' => 'fr',
            'GB' => 'en',
            'US' => 'en',
            'CA' => 'en',
            'AU' => 'en',
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

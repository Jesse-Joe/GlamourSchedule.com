<?php
namespace GlamourSchedule\Core;

/**
 * Main Application Class
 */
class Application
{
    private array $config;
    private Router $router;
    private Database $db;
    
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->init();
    }
    
    private function init(): void
    {
        // Set timezone
        date_default_timezone_set($this->config['app']['timezone'] ?? 'Europe/Amsterdam');
        
        // Initialize database
        $this->db = new Database($this->config['database']);
        
        // Initialize router
        $this->router = new Router();
        $this->registerRoutes();
    }
    
    private function registerRoutes(): void
    {
        // Public pages
        $this->router->get('/', 'HomeController@index');
        $this->router->get('/search', 'SearchController@index');
        $this->router->get('/terms', 'PagesController@terms');
        $this->router->get('/privacy', 'PagesController@privacy');
        $this->router->get('/about', 'PagesController@about');
        $this->router->get('/contact', 'PagesController@contact');
        $this->router->post('/contact', 'PagesController@submitContact');

        // Design Concepts
        $this->router->get('/concept1', 'PagesController@concept1');
        $this->router->get('/concept2', 'PagesController@concept2');
        $this->router->get('/concept3', 'PagesController@concept3');
        $this->router->get('/concept4', 'PagesController@concept4');
        $this->router->get('/concept5', 'PagesController@concept5');
        $this->router->get('/concept6', 'PagesController@concept6');
        $this->router->get('/concept7', 'PagesController@concept7');
        $this->router->get('/concept8', 'PagesController@concept8');
        $this->router->get('/concept9', 'PagesController@concept9');
        $this->router->get('/concept10', 'PagesController@concept10');
        $this->router->get('/concept11', 'PagesController@concept11');
        $this->router->get('/concept12', 'PagesController@concept12');
        $this->router->get('/concept13', 'PagesController@concept13');
        $this->router->get('/concept14', 'PagesController@concept14');
        $this->router->get('/concept15', 'PagesController@concept15');
        $this->router->get('/concept16', 'PagesController@concept16');
        $this->router->get('/concept17', 'PagesController@concept17');
        $this->router->get('/concept18', 'PagesController@concept18');
        $this->router->get('/concept19', 'PagesController@concept19');
        $this->router->get('/concept20', 'PagesController@concept20');

        // Authentication
        $this->router->get('/login', 'AuthController@showLogin');
        $this->router->post('/login', 'AuthController@login');
        $this->router->get('/register', 'AuthController@showRegister');
        $this->router->post('/register', 'AuthController@register');
        $this->router->get('/logout', 'AuthController@logout');

        // 2FA Verification
        $this->router->get('/verify-login', 'AuthController@showVerifyLogin');
        $this->router->post('/verify-login', 'AuthController@verifyLogin');
        $this->router->get('/verify-registration', 'AuthController@showVerifyRegistration');
        $this->router->post('/verify-registration', 'AuthController@verifyRegistration');
        $this->router->post('/api/resend-login-code', 'AuthController@resendLoginCode');
        $this->router->post('/api/resend-registration-code', 'AuthController@resendRegistrationCode');

        // Email verification
        $this->router->get('/verify-email', 'VerifyEmailController@show');
        $this->router->post('/verify-email', 'VerifyEmailController@verify');
        $this->router->post('/verify-email/resend', 'VerifyEmailController@resend');

        // Password reset
        $this->router->get('/forgot-password', 'PasswordResetController@showForgotForm');
        $this->router->post('/forgot-password', 'PasswordResetController@sendResetLink');
        $this->router->get('/reset-password/{token}', 'PasswordResetController@showResetForm');
        $this->router->post('/reset-password', 'PasswordResetController@resetPassword');

        // Business registration (before /business/{slug})
        $this->router->get('/business/register', 'BusinessRegisterController@show');
        $this->router->post('/business/register', 'BusinessRegisterController@register');

        // Partner/Sales registration (with discount)
        $this->router->get('/partner/register', 'BusinessRegisterController@showPartnerRegister');
        $this->router->post('/partner/register', 'BusinessRegisterController@partnerRegister');
        $this->router->get('/partner/payment-complete', 'BusinessRegisterController@partnerPaymentComplete');
        $this->router->get('/partner/complete/{token}', 'BusinessRegisterController@showCompleteRegistration');
        $this->router->post('/partner/complete/{token}', 'BusinessRegisterController@completeRegistration');
        $this->router->post('/webhook/partner-payment', 'BusinessRegisterController@partnerPaymentWebhook');

        // Business dashboard routes (before /business/{slug})
        $this->router->group(['middleware' => 'business'], function($router) {
            $router->get('/business/dashboard', 'BusinessDashboardController@index');
            $router->get('/business/bookings', 'BusinessDashboardController@bookings');
            $router->get('/business/services', 'BusinessDashboardController@services');
            $router->post('/business/services', 'BusinessDashboardController@services');
            $router->get('/business/calendar', 'BusinessDashboardController@calendar');
            $router->get('/business/payouts', 'BusinessDashboardController@payouts');

            // QR Scanner voor check-in
            $router->get('/business/scanner', 'BusinessDashboardController@scanner');
            $router->post('/business/checkin', 'BusinessDashboardController@processCheckin');

            // Website Management
            $router->get('/business/website', 'BusinessDashboardController@website');
            $router->post('/business/website', 'BusinessDashboardController@updateWebsite');

            // Photos Management
            $router->get('/business/photos', 'BusinessDashboardController@photos');
            $router->post('/business/photos', 'BusinessDashboardController@uploadPhoto');
            $router->post('/business/photos/delete', 'BusinessDashboardController@deletePhoto');
            $router->post('/business/photos/reorder', 'BusinessDashboardController@reorderPhotos');

            // Theme Settings
            $router->get('/business/theme', 'BusinessDashboardController@theme');
            $router->post('/business/theme', 'BusinessDashboardController@updateTheme');

            // Business Profile
            $router->get('/business/profile', 'BusinessDashboardController@profile');
            $router->post('/business/profile', 'BusinessDashboardController@updateProfile');

            // IBAN Verification (direct Mollie flow)
            $router->post('/business/iban/add', 'BusinessDashboardController@addIban');
            $router->get('/business/iban/complete', 'BusinessDashboardController@ibanPaymentComplete');

            // IBAN Change (with 2FA)
            $router->get('/business/change-iban', 'BusinessDashboardController@changeIban');
            $router->post('/business/iban/verify-change', 'BusinessDashboardController@verifyIbanChange');
            $router->post('/business/iban/resend-change-code', 'BusinessDashboardController@resendIbanChange2FA');

            // Reviews Management
            $router->get('/business/reviews', 'BusinessDashboardController@reviews');
            $router->post('/business/reviews/respond', 'BusinessDashboardController@respondToReview');
        });

        // Business page (after specific routes)
        $this->router->get('/business/{slug}', 'BusinessController@show');
        
        // Bookings
        $this->router->get('/book/{businessSlug}', 'BookingController@create');
        $this->router->post('/book/{businessSlug}', 'BookingController@store');
        $this->router->get('/booking/{uuid}', 'BookingController@show');
        $this->router->post('/booking/{uuid}/cancel', 'BookingController@cancel');

        // Check-in (QR code scanning by business)
        $this->router->get('/checkin/{uuid}', 'BookingController@showCheckin');
        $this->router->post('/checkin/{uuid}', 'BookingController@processCheckin');

        // Customer Reviews
        $this->router->get('/review/{uuid}', 'BookingController@showReview');
        $this->router->post('/review/{uuid}', 'BookingController@submitReview');

        // Waitlist
        $this->router->post('/waitlist/{businessSlug}', 'BookingController@addToWaitlist');
        $this->router->get('/api/waitlist/status', 'BookingController@getWaitlistStatus');
        $this->router->post('/waitlist/{uuid}/cancel', 'BookingController@cancelWaitlist');

        // Payments
        $this->router->get('/payment/create/{bookingUuid}', 'PaymentController@create');
        $this->router->get('/payment/return/{bookingUuid}', 'PaymentController@returnUrl');

        // Payment webhooks
        $this->router->post('/api/webhooks/mollie', 'WebhookController@mollie');
        
        // API endpoints
        $this->router->get('/api/translations/{lang}', 'ApiController@translations');
        $this->router->get('/api/services/{businessId}', 'ApiController@services');
        $this->router->get('/api/availability/{businessId}', 'ApiController@availability');
        $this->router->get('/api/available-times/{businessSlug}', 'BookingController@getAvailableTimes');
        $this->router->get('/api/categories', 'ApiController@categories');
        $this->router->get('/api/stats', 'ApiController@stats');
        $this->router->post('/api/consent', 'ApiController@consent');
        $this->router->post('/api/pwa-installed', 'ApiController@pwaInstalled');
        $this->router->post('/api/track-page', 'ApiController@trackPageView');

        // Push Notifications
        $this->router->get('/api/push/vapid-key', 'ApiController@vapidKey');
        $this->router->post('/api/push/subscribe', 'ApiController@pushSubscribe');
        $this->router->post('/api/push/unsubscribe', 'ApiController@pushUnsubscribe');

        // Theme Preference
        $this->router->get('/api/theme', 'ApiController@getTheme');
        $this->router->post('/api/theme', 'ApiController@saveTheme');

        // QR Code scanning
        $this->router->post('/api/qr/scan', 'QrController@scan');
        
        // Dashboard routes (authenticated)
        $this->router->group(['middleware' => 'auth'], function($router) {
            $router->get('/dashboard', 'DashboardController@index');
            $router->get('/dashboard/bookings', 'DashboardController@bookings');
            $router->get('/dashboard/profile', 'DashboardController@profile');
            $router->get('/dashboard/settings', 'DashboardController@settings');
            $router->post('/dashboard/settings', 'DashboardController@settings');
            $router->get('/dashboard/security', 'DashboardController@security');
            $router->post('/dashboard/security', 'DashboardController@security');
            $router->post('/api/verify-pin', 'DashboardController@verifyPin');
        });

        // Sales dashboard routes
        $this->router->get('/sales', 'SalesController@index');
        $this->router->get('/sales/login', 'SalesController@showLogin');
        $this->router->post('/sales/login', 'SalesController@login');
        $this->router->get('/sales/logout', 'SalesController@logout');
        $this->router->get('/sales/register', 'SalesController@showRegister');
        $this->router->post('/sales/register', 'SalesController@register');
        $this->router->get('/sales/dashboard', 'SalesController@dashboard');
        $this->router->get('/sales/referrals', 'SalesController@referrals');
        $this->router->get('/sales/payouts', 'SalesController@payouts');
        $this->router->get('/sales/materials', 'SalesController@materials');
        $this->router->post('/sales/send-referral-email', 'SalesController@sendReferralEmail');
        $this->router->get('/sales/guide', 'SalesController@guide');

        // Sales registration verification & payment
        $this->router->get('/sales/payment-complete', 'SalesController@paymentComplete');
        $this->router->get('/sales/verify-email', 'SalesController@showVerifyEmail');
        $this->router->post('/sales/verify-email', 'SalesController@verifyEmail');
        $this->router->post('/sales/verify-email/resend', 'SalesController@resendVerificationCode');
        $this->router->get('/sales/payment', 'SalesController@showPayment');
        $this->router->post('/sales/payment', 'SalesController@processPayment');
        $this->router->get('/sales/payment/complete', 'SalesController@paymentComplete');
        $this->router->post('/sales/payment/webhook', 'SalesController@paymentWebhook');

        // Cron routes (protected by secret key)
        $this->router->get('/cron/trial-expiry', 'CronController@trialExpiry');
        $this->router->get('/cron/deactivate-expired', 'CronController@deactivateExpired');

        // Admin routes
        $this->router->get('/admin', 'AdminController@showLogin');
        $this->router->get('/admin/login', 'AdminController@showLogin');
        $this->router->post('/admin/login', 'AdminController@login');
        $this->router->get('/admin/logout', 'AdminController@logout');
        $this->router->get('/admin/dashboard', 'AdminController@dashboard');
        $this->router->get('/admin/users', 'AdminController@users');
        $this->router->get('/admin/businesses', 'AdminController@businesses');
        $this->router->get('/admin/sales-partners', 'AdminController@salesPartners');
        $this->router->get('/admin/revenue', 'AdminController@revenue');
        $this->router->post('/admin/user/{id}/update', 'AdminController@updateUser');
        $this->router->post('/admin/user/{id}/delete', 'AdminController@deleteUser');
        $this->router->post('/admin/business/{id}/update', 'AdminController@updateBusiness');
        $this->router->post('/admin/business/{id}/delete', 'AdminController@deleteBusiness');
        $this->router->post('/admin/business/{id}/activate', 'AdminController@activateBusiness');
        $this->router->post('/admin/sales-partner/{id}/update', 'AdminController@updateSalesPartner');
        $this->router->post('/admin/sales-partner/{id}/delete', 'AdminController@deleteSalesPartner');
    }
    
    public function run(): void
    {
        try {
            $response = $this->router->dispatch(
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI']
            );
            
            echo $response;
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    private function handleException(\Exception $e): void
    {
        // Log error
        error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        
        // Show error page
        http_response_code(500);
        
        if ($this->config['app']['debug']) {
            echo "<h1>Error</h1><pre>" . $e->getMessage() . "</pre>";
        } else {
            include BASE_PATH . '/resources/views/errors/500.php';
        }
    }
    
    public function getConfig(string $key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public function getDb(): Database
    {
        return $this->db;
    }
}

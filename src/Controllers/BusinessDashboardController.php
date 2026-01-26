<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\GlamoriManager;

class BusinessDashboardController extends Controller
{
    private array $business;

    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['business_id'])) {
            $this->redirect('/login');
            exit;
        }
        $business = $this->getCurrentBusiness();
        if (!$business) {
            $this->redirect('/login');
            exit;
        }
        $this->business = $business;
    }

    public function index(): string
    {
        $stats = $this->getStats();
        $todayBookings = $this->getTodayBookings();
        $recentBookings = $this->getRecentBookings();

        // Check if this is a new registration
        $isNewRegistration = isset($_SESSION['new_business_registration']) && $_SESSION['new_business_registration'] === true;

        // Clear the flag after reading (only show popup once)
        if ($isNewRegistration) {
            unset($_SESSION['new_business_registration']);
        }

        // Check profile completion
        $profileCompletion = $this->getProfileCompletion();

        // Get AI Manager widget data
        $aiManager = new GlamoriManager($this->db);
        $aiManagerData = $aiManager->getWidgetData($this->business['id']);

        return $this->view('pages/business/dashboard/index', [
            'pageTitle' => 'Bedrijf Dashboard',
            'business' => $this->business,
            'stats' => $stats,
            'todayBookings' => $todayBookings,
            'recentBookings' => $recentBookings,
            'isNewRegistration' => $isNewRegistration,
            'profileCompletion' => $profileCompletion,
            'aiManager' => $aiManagerData
        ]);
    }

    private function getProfileCompletion(): array
    {
        $items = [];
        $completed = 0;
        $total = 5;

        // Check services
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM services WHERE business_id = ?", [$this->business['id']]);
        $hasServices = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] > 0;
        $items['services'] = ['label' => 'Diensten toevoegen', 'done' => $hasServices, 'url' => '/business/services'];
        if ($hasServices) $completed++;

        // Check business hours
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM business_hours WHERE business_id = ? AND is_closed = 0", [$this->business['id']]);
        $hasHours = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] > 0;
        $items['hours'] = ['label' => 'Openingstijden instellen', 'done' => $hasHours, 'url' => '/business/profile'];
        if ($hasHours) $completed++;

        // Check photos
        $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM business_photos WHERE business_id = ?", [$this->business['id']]);
        $hasPhotos = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] > 0;
        $items['photos'] = ['label' => "Foto's uploaden", 'done' => $hasPhotos, 'url' => '/business/photos'];
        if ($hasPhotos) $completed++;

        // Check description
        $hasDescription = !empty($this->business['description']) && strlen($this->business['description']) > 20;
        $items['description'] = ['label' => 'Beschrijving toevoegen', 'done' => $hasDescription, 'url' => '/business/profile'];
        if ($hasDescription) $completed++;

        // Check IBAN for payouts
        $hasIban = !empty($this->business['iban']) || !empty($this->business['iban_verified']);
        $items['iban'] = ['label' => 'Bankrekening koppelen', 'done' => $hasIban, 'url' => '/business/payouts'];
        if ($hasIban) $completed++;

        return [
            'items' => $items,
            'completed' => $completed,
            'total' => $total,
            'percentage' => round(($completed / $total) * 100)
        ];
    }

    public function bookings(): string
    {
        $filter = $_GET['filter'] ?? 'upcoming';
        $bookings = $this->getBookings($filter);

        return $this->view('pages/business/dashboard/bookings', [
            'pageTitle' => 'Boekingen',
            'business' => $this->business,
            'bookings' => $bookings,
            'filter' => $filter
        ]);
    }

    public function services(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleServiceAction();
            return $this->redirect('/business/services');
        }

        $services = $this->getServices();

        return $this->view('pages/business/dashboard/services', [
            'pageTitle' => 'Diensten',
            'business' => $this->business,
            'services' => $services,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function calendar(): string
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $bookings = $this->getBookingsForDate($date);

        return $this->view('pages/business/dashboard/calendar', [
            'pageTitle' => 'Agenda',
            'business' => $this->business,
            'bookings' => $bookings,
            'selectedDate' => $date
        ]);
    }

    public function payouts(): string
    {
        $payouts = $this->getPayouts();
        $pendingAmount = $this->getPendingAmount();

        return $this->view('pages/business/dashboard/payouts', [
            'pageTitle' => 'Uitbetalingen',
            'business' => $this->business,
            'payouts' => $payouts,
            'pendingAmount' => $pendingAmount
        ]);
    }

    private function getStats(): array
    {
        $businessId = $this->business['id'];

        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM bookings WHERE business_id = ? AND status != 'cancelled'",
            [$businessId]
        );
        $totalBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM bookings WHERE business_id = ? AND appointment_date = CURDATE() AND status != 'cancelled'",
            [$businessId]
        );
        $todayBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(COALESCE(business_payout, total_price - platform_fee)), 0) as revenue FROM bookings WHERE business_id = ? AND status = 'completed'",
            [$businessId]
        );
        $totalRevenue = $stmt->fetch(\PDO::FETCH_ASSOC)['revenue'];

        $stmt = $this->db->query(
            "SELECT COALESCE(AVG(r.rating), 0) as avg FROM reviews r WHERE r.business_id = ?",
            [$businessId]
        );
        $avgRating = $stmt->fetch(\PDO::FETCH_ASSOC)['avg'];

        return [
            'totalBookings' => $totalBookings,
            'todayBookings' => $todayBookings,
            'totalRevenue' => $totalRevenue,
            'avgRating' => $avgRating
        ];
    }

    private function getTodayBookings(): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, s.name as service_name, u.first_name, u.last_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.business_id = ? AND b.appointment_date = CURDATE() AND b.status != 'cancelled'
             ORDER BY b.appointment_time",
            [$this->business['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getRecentBookings(): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, s.name as service_name, u.first_name, u.last_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.business_id = ?
             ORDER BY b.created_at DESC
             LIMIT 10",
            [$this->business['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getBookings(string $filter): array
    {
        $sql = "SELECT b.*, s.name as service_name, u.first_name, u.last_name
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.business_id = ?";

        if ($filter === 'upcoming') {
            $sql .= " AND b.appointment_date >= CURDATE() AND b.status != 'cancelled'";
        } elseif ($filter === 'past') {
            $sql .= " AND b.appointment_date < CURDATE()";
        } elseif ($filter === 'cancelled') {
            $sql .= " AND b.status = 'cancelled'";
        }

        $sql .= " ORDER BY b.appointment_date DESC, b.appointment_time DESC";

        $stmt = $this->db->query($sql, [$this->business['id']]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getServices(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM services WHERE business_id = ? ORDER BY sort_order, name",
            [$this->business['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function handleServiceAction(): void
    {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

            $this->db->query(
                "INSERT INTO services (uuid, business_id, name, description, price, duration_minutes, is_active)
                 VALUES (?, ?, ?, ?, ?, ?, 1)",
                [
                    $uuid,
                    $this->business['id'],
                    trim($_POST['name']),
                    trim($_POST['description'] ?? ''),
                    (float)$_POST['price'],
                    (int)$_POST['duration']
                ]
            );
        } elseif ($action === 'delete') {
            $this->db->query(
                "DELETE FROM services WHERE id = ? AND business_id = ?",
                [(int)$_POST['service_id'], $this->business['id']]
            );
        }
    }

    private function getBookingsForDate(string $date): array
    {
        $stmt = $this->db->query(
            "SELECT b.*, s.name as service_name, s.duration_minutes, u.first_name, u.last_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.business_id = ? AND b.appointment_date = ? AND b.status != 'cancelled'
             ORDER BY b.appointment_time",
            [$this->business['id'], $date]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getPayouts(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM business_payouts WHERE business_id = ? ORDER BY created_at DESC LIMIT 20",
            [$this->business['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getPendingAmount(): float
    {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(COALESCE(business_payout, total_price - platform_fee)), 0) as pending
             FROM bookings
             WHERE business_id = ? AND status = 'completed' AND payout_status = 'pending'",
            [$this->business['id']]
        );
        return (float)$stmt->fetch(\PDO::FETCH_ASSOC)['pending'];
    }

    // ============================================================
    // WEBSITE MANAGEMENT
    // ============================================================

    public function website(): string
    {
        $settings = $this->getBusinessSettings();

        return $this->view('pages/business/dashboard/website', [
            'pageTitle' => 'Webpagina Beheer',
            'business' => $this->business,
            'settings' => $settings,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function updateWebsite(): string
    {
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }

        $settings = [
            'tagline' => trim($_POST['tagline'] ?? ''),
            'about_title' => trim($_POST['about_title'] ?? ''),
            'about_text' => trim($_POST['about_text'] ?? ''),
            'welcome_message' => trim($_POST['welcome_message'] ?? ''),
            'facebook_url' => trim($_POST['facebook_url'] ?? ''),
            'instagram_url' => trim($_POST['instagram_url'] ?? ''),
            'twitter_url' => trim($_POST['twitter_url'] ?? ''),
            'tiktok_url' => trim($_POST['tiktok_url'] ?? ''),
            'show_reviews' => isset($_POST['show_reviews']) ? 1 : 0,
            'show_prices' => isset($_POST['show_prices']) ? 1 : 0,
            'show_duration' => isset($_POST['show_duration']) ? 1 : 0,
            'show_availability' => isset($_POST['show_availability']) ? 1 : 0,
            'loyalty_enabled' => isset($_POST['loyalty_enabled']) ? 1 : 0,
            'loyalty_max_redeem_points' => (int)($_POST['loyalty_max_redeem_points'] ?? 2000),
        ];

        $this->saveBusinessSettings($settings);

        // Update business description too
        $this->db->query(
            "UPDATE businesses SET description = ? WHERE id = ?",
            [trim($_POST['description'] ?? ''), $this->business['id']]
        );

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Webpagina instellingen opgeslagen!'];
        return $this->redirect('/business/website');
    }

    // ============================================================
    // PHOTOS MANAGEMENT
    // ============================================================

    public function photos(): string
    {
        $images = $this->getBusinessImages();

        return $this->view('pages/business/dashboard/photos', [
            'pageTitle' => 'Foto Beheer',
            'business' => $this->business,
            'images' => $images,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function uploadPhoto(): string
    {
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }

        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Geen bestand geüpload of er was een fout.'];
            return $this->redirect('/business/photos');
        }

        $file = $_FILES['photo'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Alleen JPG, PNG, WebP en GIF bestanden zijn toegestaan.'];
            return $this->redirect('/business/photos');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Bestand is te groot. Maximaal 5MB.'];
            return $this->redirect('/business/photos');
        }

        // Create upload directory
        $uploadDir = BASE_PATH . '/public/uploads/businesses/' . $this->business['id'];
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $filepath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $imageType = $_POST['image_type'] ?? 'gallery';
            $caption = trim($_POST['caption'] ?? '');
            $altText = trim($_POST['alt_text'] ?? '');

            // Get current max sort order
            $stmt = $this->db->query(
                "SELECT COALESCE(MAX(sort_order), 0) + 1 as next_order FROM business_images WHERE business_id = ?",
                [$this->business['id']]
            );
            $nextOrder = $stmt->fetch(\PDO::FETCH_ASSOC)['next_order'];

            $this->db->query(
                "INSERT INTO business_images (business_id, image_path, image_type, caption, alt_text, sort_order) VALUES (?, ?, ?, ?, ?, ?)",
                [$this->business['id'], '/uploads/businesses/' . $this->business['id'] . '/' . $filename, $imageType, $caption, $altText, $nextOrder]
            );

            // If it's a logo or cover, update the business table
            if ($imageType === 'logo') {
                $this->db->query("UPDATE businesses SET logo = ? WHERE id = ?", ['/uploads/businesses/' . $this->business['id'] . '/' . $filename, $this->business['id']]);
            } elseif ($imageType === 'cover') {
                $this->db->query("UPDATE businesses SET cover_image = ? WHERE id = ?", ['/uploads/businesses/' . $this->business['id'] . '/' . $filename, $this->business['id']]);
            }

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Foto succesvol geüpload!'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Er is een fout opgetreden bij het uploaden.'];
        }

        return $this->redirect('/business/photos');
    }

    public function deletePhoto(): string
    {
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }

        $imageId = (int)($_POST['image_id'] ?? 0);

        $stmt = $this->db->query(
            "SELECT * FROM business_images WHERE id = ? AND business_id = ?",
            [$imageId, $this->business['id']]
        );
        $image = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($image) {
            // Delete file
            $filepath = BASE_PATH . '/public' . $image['image_path'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            // Delete from database
            $this->db->query("DELETE FROM business_images WHERE id = ?", [$imageId]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Foto verwijderd!'];
        }

        return $this->redirect('/business/photos');
    }

    public function reorderPhotos(): string
    {
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }

        $order = json_decode($_POST['order'] ?? '[]', true);

        if (is_array($order)) {
            foreach ($order as $position => $imageId) {
                $this->db->query(
                    "UPDATE business_images SET sort_order = ? WHERE id = ? AND business_id = ?",
                    [$position, (int)$imageId, $this->business['id']]
                );
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    // ============================================================
    // THEME SETTINGS
    // ============================================================

    public function theme(): string
    {
        $settings = $this->getBusinessSettings();

        // Available color presets - Extended collection
        $colorPresets = [
            // Classic & Elegant
            ['name' => 'Midnight Black', 'primary' => '#000000', 'secondary' => '#1a1a1a', 'accent' => '#fbbf24'],
            ['name' => 'Pure White', 'primary' => '#ffffff', 'secondary' => '#f5f5f5', 'accent' => '#000000'],
            ['name' => 'Rose Gold', 'primary' => '#b76e79', 'secondary' => '#e8c4c4', 'accent' => '#ffd700'],
            ['name' => 'Champagne', 'primary' => '#d4af37', 'secondary' => '#f5e6c4', 'accent' => '#8b7355'],

            // Beauty & Wellness
            ['name' => 'Blush Pink', 'primary' => '#f8b4c4', 'secondary' => '#fce4ec', 'accent' => '#c2185b'],
            ['name' => 'Lavender Dream', 'primary' => '#9c89b8', 'secondary' => '#e6e0f0', 'accent' => '#6a5acd'],
            ['name' => 'Soft Coral', 'primary' => '#ff7f7f', 'secondary' => '#ffe4e1', 'accent' => '#ff4500'],
            ['name' => 'Mint Fresh', 'primary' => '#98d8c8', 'secondary' => '#e0f7f4', 'accent' => '#20b2aa'],

            // Modern & Bold
            ['name' => 'Electric Blue', 'primary' => '#0077be', 'secondary' => '#00a8e8', 'accent' => '#ffd700'],
            ['name' => 'Sunset Orange', 'primary' => '#ff6b35', 'secondary' => '#ffb347', 'accent' => '#2d3436'],
            ['name' => 'Berry Purple', 'primary' => '#7b2cbf', 'secondary' => '#c77dff', 'accent' => '#f72585'],
            ['name' => 'Forest Green', 'primary' => '#2d6a4f', 'secondary' => '#74c69d', 'accent' => '#d4a373'],

            // Luxe & Sophisticated
            ['name' => 'Navy Gold', 'primary' => '#1b3a4b', 'secondary' => '#2c5364', 'accent' => '#d4af37'],
            ['name' => 'Burgundy Wine', 'primary' => '#722f37', 'secondary' => '#9e4a4c', 'accent' => '#f5e6c4'],
            ['name' => 'Slate Gray', 'primary' => '#4a5568', 'secondary' => '#718096', 'accent' => '#ed8936'],
            ['name' => 'Teal Elegance', 'primary' => '#008080', 'secondary' => '#20b2aa', 'accent' => '#ffd700'],

            // Trending
            ['name' => 'Terracotta', 'primary' => '#c96445', 'secondary' => '#e8cebf', 'accent' => '#5d4e37'],
            ['name' => 'Sage Green', 'primary' => '#9caf88', 'secondary' => '#d1dbc6', 'accent' => '#4a5043'],
            ['name' => 'Dusty Rose', 'primary' => '#d4a5a5', 'secondary' => '#f0e4e4', 'accent' => '#8b5a5a'],
            ['name' => 'Ocean Wave', 'primary' => '#1e88e5', 'secondary' => '#64b5f6', 'accent' => '#ff7043'],
        ];

        return $this->view('pages/business/dashboard/theme', [
            'pageTitle' => 'Thema Instellingen',
            'business' => $this->business,
            'settings' => $settings,
            'colorPresets' => $colorPresets,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function updateTheme(): string
    {
        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'CSRF token ongeldig. Vernieuw de pagina en probeer opnieuw.'];
            return $this->redirect('/business/theme');
        }

        try {
            // Validate colors (must be valid hex)
            $primaryColor = $this->validateHexColor($_POST['primary_color'] ?? '#000000');
            $secondaryColor = $this->validateHexColor($_POST['secondary_color'] ?? '#333333');
            $accentColor = $this->validateHexColor($_POST['accent_color'] ?? '#fbbf24');

        // Validate font family
        $allowedFonts = ['playfair', 'cormorant', 'lora', 'montserrat', 'poppins', 'dancing', 'great-vibes', 'raleway'];
        $fontFamily = in_array($_POST['font_family'] ?? '', $allowedFonts) ? $_POST['font_family'] : 'playfair';

        // Validate font style
        $allowedFontStyles = ['elegant', 'modern', 'bold', 'light'];
        $fontStyle = in_array($_POST['font_style'] ?? '', $allowedFontStyles) ? $_POST['font_style'] : 'elegant';

        // Validate layout template
        $allowedLayouts = ['classic', 'sidebar', 'hero', 'minimal', 'cards', 'magazine'];
        $layoutTemplate = in_array($_POST['layout_template'] ?? '', $allowedLayouts) ? $_POST['layout_template'] : 'classic';

        // Validate button style
        $allowedButtonStyles = ['rounded', 'square', 'pill', 'sharp'];
        $buttonStyle = in_array($_POST['button_style'] ?? '', $allowedButtonStyles) ? $_POST['button_style'] : 'rounded';

        // Validate header style
        $allowedHeaderStyles = ['gradient', 'solid', 'image', 'transparent'];
        $headerStyle = in_array($_POST['header_style'] ?? '', $allowedHeaderStyles) ? $_POST['header_style'] : 'gradient';

        // Sanitize custom CSS (basic XSS prevention)
        $customCss = $_POST['custom_css'] ?? '';
        $customCss = strip_tags($customCss);
        // Remove potentially dangerous CSS (javascript, expression, url with data)
        $customCss = preg_replace('/javascript\s*:/i', '', $customCss);
        $customCss = preg_replace('/expression\s*\(/i', '', $customCss);
        $customCss = preg_replace('/url\s*\(\s*["\']?\s*data:/i', '', $customCss);

        $settings = [
            'primary_color' => $primaryColor,
            'secondary_color' => $secondaryColor,
            'accent_color' => $accentColor,
            'font_family' => $fontFamily,
            'font_style' => $fontStyle,
            'layout_template' => $layoutTemplate,
            'button_style' => $buttonStyle,
            'header_style' => $headerStyle,
            'custom_css' => $customCss,
            'gallery_style' => in_array($_POST['gallery_style'] ?? '', ['grid', 'carousel', 'masonry']) ? $_POST['gallery_style'] : 'grid',
            'show_reviews' => isset($_POST['show_reviews']) ? 1 : 0,
            'show_prices' => isset($_POST['show_prices']) ? 1 : 0,
            'show_duration' => isset($_POST['show_duration']) ? 1 : 0,
            'show_availability' => isset($_POST['show_availability']) ? 1 : 0,
        ];

        $this->saveBusinessSettings($settings);

        // Update theme in businesses table
        $theme = in_array($_POST['theme'] ?? '', ['light', 'dark']) ? $_POST['theme'] : 'light';

        $this->db->query(
            "UPDATE businesses SET theme = ? WHERE id = ?",
            [$theme, $this->business['id']]
        );

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Thema instellingen opgeslagen!'];
        } catch (\Exception $e) {
            error_log('Theme update error: ' . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Er ging iets mis bij het opslaan. Probeer het opnieuw.'];
        }

        return $this->redirect('/business/theme');
    }

    private function validateHexColor(string $color): string
    {
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            return $color;
        }
        return '#000000'; // Default to luxury violet
    }

    // ============================================================
    // BUSINESS PROFILE
    // ============================================================

    public function profile(): string
    {
        $hours = $this->getBusinessHours();

        return $this->view('pages/business/dashboard/profile', [
            'pageTitle' => 'Bedrijfsprofiel',
            'business' => $this->business,
            'hours' => $hours,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function updateProfile(): string
    {
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }

        $data = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'email' => filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: $this->business['email'],
            'phone' => trim($_POST['phone'] ?? ''),
            'website' => trim($_POST['website'] ?? ''),
            'street' => trim($_POST['street'] ?? ''),
            'house_number' => trim($_POST['house_number'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
        ];

        $this->db->query(
            "UPDATE businesses SET
             company_name = ?, email = ?, phone = ?, website = ?,
             street = ?, house_number = ?, postal_code = ?, city = ?
             WHERE id = ?",
            [
                $data['company_name'], $data['email'], $data['phone'], $data['website'],
                $data['street'], $data['house_number'], $data['postal_code'], $data['city'],
                $this->business['id']
            ]
        );

        // Update business hours
        if (isset($_POST['hours']) && is_array($_POST['hours'])) {
            foreach ($_POST['hours'] as $day => $times) {
                $isClosed = isset($times['closed']) ? 1 : 0;
                $openTime = $times['open'] ?? '09:00';
                $closeTime = $times['close'] ?? '18:00';

                $this->db->query(
                    "INSERT INTO business_hours (business_id, day_of_week, open_time, close_time, is_closed)
                     VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE open_time = ?, close_time = ?, is_closed = ?",
                    [$this->business['id'], (int)$day, $openTime, $closeTime, $isClosed, $openTime, $closeTime, $isClosed]
                );
            }
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profiel opgeslagen!'];
        return $this->redirect('/business/profile');
    }

    /**
     * Delete business account
     */
    public function deleteBusiness(): string
    {
        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return $this->redirect('/business/profile');
        }

        $confirmText = trim($_POST['confirm_text'] ?? '');

        if ($confirmText !== 'VERWIJDER') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Bevestigingstekst onjuist'];
            return $this->redirect('/business/profile');
        }

        $businessId = $this->business['id'];
        $userId = $_SESSION['user_id'] ?? null;

        try {
            // Delete related records in order of dependencies
            $this->db->query("DELETE FROM bookings WHERE business_id = ?", [$businessId]);
            $this->db->query("DELETE FROM pos_bookings WHERE business_id = ?", [$businessId]);
            $this->db->query("DELETE FROM services WHERE business_id = ?", [$businessId]);
            $this->db->query("DELETE FROM business_hours WHERE business_id = ?", [$businessId]);
            $this->db->query("DELETE FROM business_categories WHERE business_id = ?", [$businessId]);
            $this->db->query("DELETE FROM business_images WHERE business_id = ?", [$businessId]);
            $this->db->query("DELETE FROM reviews WHERE business_id = ?", [$businessId]);
            $this->db->query("DELETE FROM business_settings WHERE business_id = ?", [$businessId]);
            $this->db->query("DELETE FROM business_employees WHERE business_id = ?", [$businessId]);
            $this->db->query("DELETE FROM push_subscriptions WHERE business_id = ?", [$businessId]);

            // Delete the business itself
            $this->db->query("DELETE FROM businesses WHERE id = ?", [$businessId]);

            // Update user's business_id
            if ($userId) {
                $this->db->query("UPDATE users SET business_id = NULL WHERE id = ?", [$userId]);
            }

            // Clear session
            unset($_SESSION['business_id']);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Je bedrijf is succesvol verwijderd'];
            return $this->redirect('/dashboard');

        } catch (\Exception $e) {
            error_log("Business deletion error: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Fout bij verwijderen bedrijf'];
            return $this->redirect('/business/profile');
        }
    }

    // ============================================================
    // REVIEWS MANAGEMENT
    // ============================================================

    public function reviews(): string
    {
        $reviews = $this->getAllReviews();
        $stats = $this->getReviewStats();

        return $this->view('pages/business/dashboard/reviews', [
            'pageTitle' => 'Reviews',
            'business' => $this->business,
            'reviews' => $reviews,
            'stats' => $stats,
            'csrfToken' => $this->csrf()
        ]);
    }

    public function respondToReview(): string
    {
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }

        $reviewId = (int)($_POST['review_id'] ?? 0);
        $response = trim($_POST['response'] ?? '');

        if ($reviewId && $response) {
            $this->db->query(
                "UPDATE reviews SET business_response = ?, responded_at = NOW() WHERE id = ? AND business_id = ?",
                [$response, $reviewId, $this->business['id']]
            );
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Reactie geplaatst!'];
        }

        return $this->redirect('/business/reviews');
    }

    /**
     * AI Manager Insights pagina
     */
    public function insights(): string
    {
        $aiManager = new GlamoriManager($this->db);
        $stats = $aiManager->getBusinessStats($this->business['id']);
        $tips = $aiManager->getProactiveTips($this->business['id']);
        $notifications = $aiManager->getNotifications($this->business['id'], 20);

        return $this->view('pages/business/dashboard/insights', [
            'pageTitle' => 'Inzichten & Statistieken',
            'business' => $this->business,
            'stats' => $stats,
            'tips' => $tips,
            'notifications' => $notifications
        ]);
    }

    private function getAllReviews(): array
    {
        $stmt = $this->db->query(
            "SELECT r.*, u.first_name, u.last_name, b.booking_number, s.name as service_name
             FROM reviews r
             LEFT JOIN users u ON r.user_id = u.id
             LEFT JOIN bookings b ON r.booking_id = b.id
             LEFT JOIN services s ON b.service_id = s.id
             WHERE r.business_id = ?
             ORDER BY r.created_at DESC",
            [$this->business['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getReviewStats(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) as total,
                AVG(rating) as average,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
             FROM reviews WHERE business_id = ?",
            [$this->business['id']]
        );
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    private function getBusinessSettings(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM business_settings WHERE business_id = ?",
            [$this->business['id']]
        );
        $settings = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$settings) {
            // Create default settings
            $this->db->query(
                "INSERT INTO business_settings (business_id) VALUES (?)",
                [$this->business['id']]
            );
            return [
                'primary_color' => '#000000',
                'secondary_color' => '#333333',
                'accent_color' => '#fbbf24',
                'font_family' => 'playfair',
                'font_style' => 'elegant',
                'layout_template' => 'classic',
                'button_style' => 'rounded',
                'header_style' => 'gradient',
                'custom_css' => '',
                'tagline' => '',
                'about_title' => '',
                'about_text' => '',
                'welcome_message' => '',
                'show_reviews' => 1,
                'show_prices' => 1,
                'show_duration' => 1,
                'show_availability' => 1,
                'gallery_style' => 'grid',
            ];
        }

        return $settings;
    }

    private function saveBusinessSettings(array $data): void
    {
        // Check if settings exist for this business
        $stmt = $this->db->query(
            "SELECT id FROM business_settings WHERE business_id = ?",
            [$this->business['id']]
        );

        // Get existing columns in business_settings table to avoid errors
        $columnsStmt = $this->db->query("DESCRIBE business_settings");
        $existingColumns = [];
        while ($col = $columnsStmt->fetch()) {
            $existingColumns[] = $col['Field'];
        }

        // Filter data to only include existing columns
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $existingColumns)) {
                $filteredData[$key] = $value;
            }
        }

        if ($stmt->fetch()) {
            // Update existing record
            $fields = [];
            $values = [];

            foreach ($filteredData as $key => $value) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }

            if (!empty($fields)) {
                $values[] = $this->business['id'];

                $this->db->query(
                    "UPDATE business_settings SET " . implode(', ', $fields) . " WHERE business_id = ?",
                    $values
                );
            }
        } else {
            // Insert new record
            $filteredData['business_id'] = $this->business['id'];
            $columns = array_keys($filteredData);
            $placeholders = array_fill(0, count($filteredData), '?');

            $this->db->query(
                "INSERT INTO business_settings (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")",
                array_values($filteredData)
            );
        }
    }

    private function getBusinessImages(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM business_images WHERE business_id = ? ORDER BY sort_order",
            [$this->business['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getBusinessHours(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM business_hours WHERE business_id = ? ORDER BY day_of_week",
            [$this->business['id']]
        );
        $hours = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        foreach ($hours as $h) {
            $result[$h['day_of_week']] = $h;
        }
        return $result;
    }

    // ============================================================
    // LANGUAGE SETTINGS
    // ============================================================

    /**
     * Update business language setting
     * POST /business/settings/language
     */
    public function updateLanguage(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->json(['success' => false, 'message' => 'Ongeldige aanvraag']);
        }

        $language = $_POST['language'] ?? 'nl';

        // Validate language
        $validLangs = ['nl', 'en', 'de', 'fr', 'es', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar', 'tr', 'pl', 'sv', 'no', 'da', 'fi', 'el', 'cs', 'hu', 'ro', 'bg', 'hr', 'sk', 'sl', 'et', 'lv', 'lt', 'uk', 'hi', 'th', 'vi', 'id', 'ms', 'tl', 'he', 'fa', 'sw', 'af'];
        if (!in_array($language, $validLangs)) {
            return $this->json(['success' => false, 'message' => 'Ongeldige taal geselecteerd']);
        }

        try {
            // Update business language
            $this->db->query(
                "UPDATE businesses SET language = ? WHERE id = ?",
                [$language, $this->business['id']]
            );

            // Also update the owner's user account language
            $this->db->query(
                "UPDATE users SET language = ? WHERE id = ?",
                [$language, $this->business['user_id']]
            );

            // Update session language
            $_SESSION['lang'] = $language;

            // Set language cookie
            setcookie('lang', $language, [
                'expires' => time() + (365 * 24 * 60 * 60),
                'path' => '/',
                'secure' => true,
                'httponly' => false,
                'samesite' => 'Lax'
            ]);

            return $this->json([
                'success' => true,
                'message' => $this->getLanguageUpdateMessage($language),
                'language' => $language
            ]);

        } catch (\Exception $e) {
            error_log('Language update failed: ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Er is een fout opgetreden']);
        }
    }

    /**
     * Get language update success message in the new language
     */
    private function getLanguageUpdateMessage(string $lang): string
    {
        $messages = [
            'nl' => 'Taalinstelling opgeslagen',
            'en' => 'Language setting saved',
            'de' => 'Spracheinstellung gespeichert',
            'fr' => 'Paramètre de langue enregistré'
        ];
        return $messages[$lang] ?? $messages['nl'];
    }

    /**
     * Get current business language
     * GET /business/settings/language
     */
    public function getLanguage(): string
    {
        return $this->json([
            'success' => true,
            'language' => $this->business['language'] ?? 'nl'
        ]);
    }

    // ============================================================
    // IBAN VERIFICATION (Direct Mollie Payment)
    // ============================================================

    /**
     * Start IBAN verification - redirect to Mollie for €0.01 iDEAL payment
     * IBAN will be automatically retrieved from the payment
     */
    public function addIban(): string
    {
        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return $this->redirect('/business/profile');
        }

        // Directly create Mollie payment - IBAN will be retrieved from payment details
        return $this->createIbanMolliePayment();
    }

    /**
     * Create Mollie payment for €0.01 verification
     * IBAN will be extracted from iDEAL payment response
     */
    private function createIbanMolliePayment(): string
    {
        $mollieApiKey = $this->config['mollie']['api_key'] ?? '';

        if (empty($mollieApiKey)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betalingssysteem niet geconfigureerd'];
            return $this->redirect('/business/profile');
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($mollieApiKey);

            // Create unique payment reference
            $reference = 'IBAN-' . $this->business['id'] . '-' . time();

            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '0.01'
                ],
                'description' => 'GlamourSchedule IBAN Verificatie',
                'redirectUrl' => 'https://glamourschedule.nl/business/iban/complete?ref=' . $reference,
                'webhookUrl' => 'https://glamourschedule.nl/api/webhooks/mollie',
                'method' => 'ideal',
                'metadata' => [
                    'type' => 'iban_verification',
                    'business_id' => $this->business['id'],
                    'reference' => $reference
                ]
            ]);

            // Store payment reference (IBAN will be filled after payment)
            $this->db->query(
                "INSERT INTO iban_verifications (business_id, verification_code, mollie_payment_id, status, expires_at)
                 VALUES (?, ?, ?, 'payment_pending', DATE_ADD(NOW(), INTERVAL 1 HOUR))",
                [$this->business['id'], $reference, $payment->id]
            );

            $checkoutUrl = $payment->getCheckoutUrl();

            // Redirect to Mollie iDEAL
            return $this->redirect($checkoutUrl);

        } catch (\Exception $e) {
            error_log("IBAN verification error: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betaling kon niet worden gestart. Probeer later opnieuw.'];
            return $this->redirect('/business/profile');
        }
    }

    /**
     * Handle Mollie payment return - extract IBAN from payment details
     */
    public function ibanPaymentComplete(): string
    {
        $reference = $_GET['ref'] ?? '';

        if (empty($reference)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige verificatie'];
            return $this->redirect('/business/profile');
        }

        // Check verification status
        $stmt = $this->db->query(
            "SELECT * FROM iban_verifications WHERE business_id = ? AND verification_code = ? ORDER BY id DESC LIMIT 1",
            [$this->business['id'], $reference]
        );
        $verification = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$verification) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Verificatie niet gevonden'];
            return $this->redirect('/business/profile');
        }

        // Check Mollie payment status and get IBAN from payment details
        $mollieApiKey = $this->config['mollie']['api_key'] ?? '';
        if (!empty($mollieApiKey) && !empty($verification['mollie_payment_id'])) {
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($mollieApiKey);
                $payment = $mollie->payments->get($verification['mollie_payment_id']);

                if ($payment->isPaid()) {
                    // Extract IBAN and account holder from iDEAL payment details
                    $details = $payment->details;
                    $iban = $details->consumerAccount ?? null;
                    $accountHolder = $details->consumerName ?? null;

                    if ($iban && $accountHolder) {
                        $this->completeIbanVerification($iban, $accountHolder, $verification['mollie_payment_id']);
                        $_SESSION['flash'] = ['type' => 'success', 'message' => 'IBAN succesvol geverifieerd! Let op: uitbetalingen zijn beschikbaar na 72 uur.'];
                    } else {
                        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Kon IBAN niet ophalen uit betaling. Neem contact op.'];
                    }
                } elseif ($payment->isFailed() || $payment->isCanceled() || $payment->isExpired()) {
                    $this->db->query("UPDATE iban_verifications SET status = 'failed' WHERE id = ?", [$verification['id']]);
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betaling niet gelukt. Probeer opnieuw.'];
                } else {
                    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Betaling wordt verwerkt. Ververs de pagina over enkele momenten.'];
                }
            } catch (\Exception $e) {
                error_log("Mollie check error: " . $e->getMessage());
                $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Status wordt gecontroleerd...'];
            }
        }

        return $this->redirect('/business/profile');
    }

    /**
     * Complete IBAN verification - sets 72-hour payout delay for security
     */
    private function completeIbanVerification(string $iban, string $accountHolder, string $paymentId): void
    {
        // Update business with IBAN and set 72-hour security delay
        $this->db->query(
            "UPDATE businesses SET iban = ?, iban_verified = 1, account_holder = ?, iban_changed_at = NOW() WHERE id = ?",
            [$iban, $accountHolder, $this->business['id']]
        );

        // Update verification record with IBAN data
        $this->db->query(
            "UPDATE iban_verifications SET iban = ?, account_holder = ?, status = 'verified', verified_at = NOW()
             WHERE business_id = ? AND mollie_payment_id = ?",
            [$iban, $accountHolder, $this->business['id'], $paymentId]
        );

        // Send confirmation email
        $this->sendIbanVerifiedEmail($iban);
    }

    /**
     * Show 2FA form for IBAN change
     */
    public function changeIban(): string
    {
        // Generate and send 2FA code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['iban_change_2fa'] = [
            'code' => $code,
            'expires_at' => time() + 900, // 15 minutes
            'attempts' => 0
        ];

        $this->sendIbanChange2FAEmail($code);

        return $this->view('pages/business/dashboard/iban-change-verify', [
            'pageTitle' => 'IBAN Wijzigen - Verificatie',
            'business' => $this->business,
            'email' => $this->business['email'],
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Verify 2FA and allow IBAN change
     */
    public function verifyIbanChange(): string
    {
        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return $this->redirect('/business/profile');
        }

        if (empty($_SESSION['iban_change_2fa'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Verificatie sessie verlopen'];
            return $this->redirect('/business/profile');
        }

        $verification = &$_SESSION['iban_change_2fa'];
        $inputCode = preg_replace('/\s+/', '', $_POST['code'] ?? '');

        // Check expiration
        if ($verification['expires_at'] < time()) {
            unset($_SESSION['iban_change_2fa']);
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Code verlopen. Probeer opnieuw.'];
            return $this->redirect('/business/change-iban');
        }

        // Check attempts
        $verification['attempts']++;
        if ($verification['attempts'] > 5) {
            unset($_SESSION['iban_change_2fa']);
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Te veel pogingen. Probeer opnieuw.'];
            return $this->redirect('/business/profile');
        }

        // Verify code
        if ($inputCode !== $verification['code']) {
            return $this->view('pages/business/dashboard/iban-change-verify', [
                'pageTitle' => 'IBAN Wijzigen - Verificatie',
                'business' => $this->business,
                'email' => $this->business['email'],
                'csrfToken' => $this->csrf(),
                'error' => 'Ongeldige code. Nog ' . (5 - $verification['attempts']) . ' pogingen.'
            ]);
        }

        // Code correct - reset IBAN verification
        unset($_SESSION['iban_change_2fa']);

        $this->db->query(
            "UPDATE businesses SET iban_verified = 0 WHERE id = ?",
            [$this->business['id']]
        );

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Verificatie gelukt! Voer je nieuwe IBAN in.'];
        return $this->redirect('/business/profile');
    }

    /**
     * Resend 2FA code for IBAN change
     */
    public function resendIbanChange2FA(): string
    {
        if (!$this->verifyCsrf()) {
            return $this->redirect('/business/profile');
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['iban_change_2fa'] = [
            'code' => $code,
            'expires_at' => time() + 900,
            'attempts' => 0
        ];

        $this->sendIbanChange2FAEmail($code);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Nieuwe code verzonden!'];
        return $this->redirect('/business/change-iban');
    }

    private function sendIbanChange2FAEmail(string $code): void
    {
        $subject = "IBAN Wijzigen - Verificatiecode";
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#000000,#404040);color:#ffffff;padding:35px;text-align:center;">
                    <div style="font-size:42px;margin-bottom:8px;">🔐</div>
                    <h1 style="margin:0;font-size:24px;">IBAN Wijzigen</h1>
                </td></tr>
                <tr><td style="padding:35px;">
                    <p style="font-size:16px;color:#ffffff;">Beste {$this->business['company_name']},</p>
                    <p style="font-size:16px;color:#555;">Je wilt je IBAN wijzigen. Gebruik deze code om door te gaan:</p>
                    <div style="background:#0a0a0a;border:3px solid #000000;border-radius:12px;padding:25px;margin:25px 0;text-align:center;">
                        <p style="margin:0;font-size:42px;font-weight:bold;color:#000000;letter-spacing:8px;font-family:monospace;">{$code}</p>
                    </div>
                    <p style="font-size:14px;color:#cccccc;">Deze code is 15 minuten geldig.</p>
                    <p style="font-size:14px;color:#dc2626;margin-top:20px;"><strong>Let op:</strong> Deel deze code nooit met anderen!</p>
                </td></tr>
                <tr><td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                    <p style="margin:0;color:#cccccc;font-size:12px;">&copy; 2025 GlamourSchedule</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;
        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($this->business['email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send IBAN change 2FA email: " . $e->getMessage());
        }
    }

    /**
     * Legacy change IBAN (now requires 2FA)
     */
    private function resetIbanVerification(): void
    {
        $this->db->query(
            "UPDATE businesses SET iban_verified = 0 WHERE id = ?",
            [$this->business['id']]
        );
    }

    private function validateIban(string $iban): bool
    {
        $iban = strtoupper(preg_replace('/\s+/', '', $iban));
        if (strlen($iban) < 15 || strlen($iban) > 34) return false;
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban)) return false;
        return true;
    }

    private function sendIbanVerifiedEmail(string $iban): void
    {
        $maskedIban = substr($iban, 0, 4) . ' **** **** ' . substr($iban, -4);
        $subject = "IBAN Geverifieerd - GlamourSchedule";
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#333333,#000000);color:#ffffff;padding:35px;text-align:center;">
                    <div style="font-size:42px;margin-bottom:8px;">✓</div>
                    <h1 style="margin:0;font-size:24px;">IBAN Geverifieerd!</h1>
                </td></tr>
                <tr><td style="padding:35px;">
                    <p style="font-size:16px;color:#ffffff;">Beste {$this->business['company_name']},</p>
                    <p style="font-size:16px;color:#555;">Je IBAN is succesvol geverifieerd en gekoppeld aan je account.</p>
                    <div style="background:#f0fdf4;border-radius:10px;padding:1rem;margin:20px 0;">
                        <p style="margin:0;font-family:monospace;font-size:1.1rem;color:#000000;">{$maskedIban}</p>
                    </div>
                    <p style="font-size:14px;color:#cccccc;">Uitbetalingen worden voortaan naar dit rekeningnummer overgemaakt.</p>
                </td></tr>
                <tr><td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                    <p style="margin:0;color:#cccccc;font-size:12px;">&copy; 2025 GlamourSchedule</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;
        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($this->business['email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send IBAN verified email: " . $e->getMessage());
        }
    }

    /**
     * QR Scanner page for check-in
     */
    public function scanner(): string
    {
        return $this->view('pages/business/dashboard/scanner', [
            'pageTitle' => 'QR Scanner',
            'business' => $this->business,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Process QR code check-in
     */
    public function processCheckin(): string
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        // Check CSRF from JSON body or header
        $csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            return json_encode(['success' => false, 'error' => 'Ongeldige sessie. Ververs de pagina.']);
        }

        $qrData = trim($input['qr_data'] ?? '');

        if (empty($qrData)) {
            return json_encode(['success' => false, 'error' => 'Voer een code in']);
        }

        // Try to find booking by UUID (from QR URL), booking number, or verification code
        $booking = null;

        // Check if it's a URL with UUID
        if (preg_match('/checkin\/([a-f0-9\-]+)/i', $qrData, $matches)) {
            $uuid = $matches[1];
            $stmt = $this->db->query(
                "SELECT b.*, s.name as service_name, u.first_name, u.last_name, u.email as user_email
                 FROM bookings b
                 JOIN services s ON b.service_id = s.id
                 LEFT JOIN users u ON b.user_id = u.id
                 WHERE b.uuid = ? AND b.business_id = ?",
                [$uuid, $this->business['id']]
            );
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        // Check if it's a raw UUID
        elseif (preg_match('/^[a-f0-9\-]{36}$/i', $qrData)) {
            $stmt = $this->db->query(
                "SELECT b.*, s.name as service_name, u.first_name, u.last_name, u.email as user_email
                 FROM bookings b
                 JOIN services s ON b.service_id = s.id
                 LEFT JOIN users u ON b.user_id = u.id
                 WHERE b.uuid = ? AND b.business_id = ?",
                [$qrData, $this->business['id']]
            );
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        // Check if it's a SHA256 verification code (format: XXXX-XXXX-XXXX)
        elseif (preg_match('/^[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}$/i', $qrData)) {
            $verificationCode = strtoupper($qrData);
            $stmt = $this->db->query(
                "SELECT b.*, s.name as service_name, u.first_name, u.last_name, u.email as user_email
                 FROM bookings b
                 JOIN services s ON b.service_id = s.id
                 LEFT JOIN users u ON b.user_id = u.id
                 WHERE b.verification_code = ? AND b.business_id = ?",
                [$verificationCode, $this->business['id']]
            );
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        // Check if it's a booking number (e.g., GS12345678)
        else {
            $bookingNumber = strtoupper($qrData);
            $stmt = $this->db->query(
                "SELECT b.*, s.name as service_name, u.first_name, u.last_name, u.email as user_email
                 FROM bookings b
                 JOIN services s ON b.service_id = s.id
                 LEFT JOIN users u ON b.user_id = u.id
                 WHERE b.booking_number = ? AND b.business_id = ?",
                [$bookingNumber, $this->business['id']]
            );
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        if (!$booking) {
            return json_encode(['success' => false, 'error' => 'Boeking niet gevonden of behoort niet tot uw bedrijf']);
        }

        // Check if already checked in
        if ($booking['status'] === 'checked_in') {
            return json_encode([
                'success' => false,
                'error' => 'Klant is al ingecheckt',
                'booking' => $this->formatBookingResponse($booking)
            ]);
        }

        // Check if paid
        if ($booking['payment_status'] !== 'paid') {
            return json_encode([
                'success' => false,
                'error' => 'Boeking is nog niet betaald',
                'booking' => $this->formatBookingResponse($booking)
            ]);
        }

        // Update status to checked_in
        $this->db->query(
            "UPDATE bookings SET status = 'checked_in', checked_in_at = NOW() WHERE uuid = ?",
            [$uuid]
        );

        // Send confirmation email to customer
        $this->sendCheckinConfirmation($booking);

        return json_encode([
            'success' => true,
            'message' => 'Klant succesvol ingecheckt!',
            'booking' => $this->formatBookingResponse($booking)
        ]);
    }

    private function formatBookingResponse(array $booking): array
    {
        $customerName = $booking['guest_name'] ?? trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '')) ?: 'Klant';
        return [
            'booking_number' => $booking['booking_number'],
            'verification_code' => $booking['verification_code'] ?? null,
            'customer_name' => $customerName,
            'service_name' => $booking['service_name'],
            'date' => date('d-m-Y', strtotime($booking['appointment_date'])),
            'time' => date('H:i', strtotime($booking['appointment_time'])),
            'price' => number_format($booking['total_price'], 2, ',', '.')
        ];
    }

    private function sendCheckinConfirmation(array $booking): void
    {
        $customerEmail = $booking['guest_email'] ?? $booking['user_email'] ?? null;
        if (!$customerEmail) return;

        $customerName = $booking['guest_name'] ?? trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '')) ?: 'Klant';
        $reviewUrl = "https://glamourschedule.nl/review/{$booking['uuid']}";

        $subject = "Check-in bevestigd - {$this->business['company_name']}";
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#333333,#000000);padding:40px;text-align:center;color:#fff;">
                            <div style="font-size:48px;margin-bottom:10px;">✓</div>
                            <h1 style="margin:0;font-size:24px;">Je bent ingecheckt!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;">Beste {$customerName},</p>
                            <p style="color:#555;line-height:1.6;">
                                Je aanwezigheid bij <strong>{$this->business['company_name']}</strong> is bevestigd.
                            </p>
                            <div style="background:#f0fdf4;border-radius:12px;padding:20px;margin:25px 0;">
                                <p style="margin:0;color:#000000;"><strong>Boeking:</strong> #{$booking['booking_number']}</p>
                                <p style="margin:10px 0 0;color:#000000;"><strong>Dienst:</strong> {$booking['service_name']}</p>
                                <p style="margin:10px 0 0;color:#000000;"><strong>Tijd:</strong> {$booking['appointment_time']}</p>
                            </div>
                            <p style="color:#555;">Veel plezier met je afspraak!</p>

                            <!-- Review Request Section -->
                            <div style="background:linear-gradient(135deg,#f5f5f5,#f5f5f5);border-radius:12px;padding:25px;margin:30px 0;text-align:center;">
                                <div style="font-size:32px;margin-bottom:10px;">⭐</div>
                                <h3 style="margin:0 0 10px;color:#000000;font-size:18px;">Wat vond je van je bezoek?</h3>
                                <p style="color:#78350f;margin:0 0 20px;font-size:14px;line-height:1.5;">
                                    Help andere klanten door je ervaring te delen met {$this->business['company_name']}
                                </p>
                                <a href="{$reviewUrl}" style="display:inline-block;background:linear-gradient(135deg,#000000,#404040);color:#fff;padding:14px 35px;border-radius:30px;text-decoration:none;font-weight:600;font-size:15px;">
                                    Laat een review achter
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#cccccc;font-size:13px;">&copy; 2025 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($customerEmail, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send check-in confirmation: " . $e->getMessage());
        }
    }

    // ============================================================
    // BUSINESS BOOST / MARKETING
    // ============================================================

    /**
     * Show boost page
     */
    public function boost(): string
    {
        return $this->view('pages/business/dashboard/boost', [
            'pageTitle' => 'Boost je Bedrijf',
            'business' => $this->business,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Activate boost - redirect to payment
     */
    public function activateBoost(): string
    {
        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return $this->redirect('/business/boost');
        }

        $boostPrice = 299.99;

        // Create Mollie payment for boost
        $mollieApiKey = $this->config['mollie']['api_key'] ?? '';
        if (empty($mollieApiKey)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betalingssysteem niet geconfigureerd'];
            return $this->redirect('/business/boost');
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($mollieApiKey);

            $reference = 'BOOST-' . $this->business['id'] . '-' . time();

            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($boostPrice, 2, '.', '')
                ],
                'description' => 'GlamourSchedule Boost 30 dagen - ' . $this->business['company_name'],
                'redirectUrl' => 'https://glamourschedule.nl/business/boost/complete?ref=' . $reference,
                'webhookUrl' => 'https://glamourschedule.nl/api/webhooks/mollie',
                'method' => ['ideal', 'creditcard', 'bancontact', 'paypal'],
                'metadata' => [
                    'type' => 'business_boost',
                    'business_id' => $this->business['id'],
                    'reference' => $reference,
                    'duration_days' => 30
                ]
            ]);

            // Store boost payment reference
            $this->db->query(
                "INSERT INTO boost_payments (business_id, reference, mollie_payment_id, amount, status, created_at)
                 VALUES (?, ?, ?, ?, 'pending', NOW())",
                [$this->business['id'], $reference, $payment->id, $boostPrice]
            );

            return $this->redirect($payment->getCheckoutUrl());

        } catch (\Exception $e) {
            error_log("Boost payment error: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betaling kon niet worden gestart. Probeer later opnieuw.'];
            return $this->redirect('/business/boost');
        }
    }

    /**
     * Extend existing boost
     */
    public function extendBoost(): string
    {
        // Same as activate, but will add to existing expiry
        return $this->activateBoost();
    }

    /**
     * Handle boost payment completion
     */
    public function boostComplete(): string
    {
        $reference = $_GET['ref'] ?? '';

        if (empty($reference)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige verificatie'];
            return $this->redirect('/business/boost');
        }

        // Get payment record
        $stmt = $this->db->query(
            "SELECT * FROM boost_payments WHERE business_id = ? AND reference = ? ORDER BY id DESC LIMIT 1",
            [$this->business['id'], $reference]
        );
        $boostPayment = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$boostPayment) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betaling niet gevonden'];
            return $this->redirect('/business/boost');
        }

        // Check Mollie payment status
        $mollieApiKey = $this->config['mollie']['api_key'] ?? '';
        if (!empty($mollieApiKey) && !empty($boostPayment['mollie_payment_id'])) {
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($mollieApiKey);
                $payment = $mollie->payments->get($boostPayment['mollie_payment_id']);

                if ($payment->isPaid()) {
                    // Update payment status
                    $this->db->query(
                        "UPDATE boost_payments SET status = 'paid', paid_at = NOW() WHERE id = ?",
                        [$boostPayment['id']]
                    );

                    // Calculate new expiry date
                    $currentExpiry = strtotime($this->business['boost_expires_at'] ?? 'now');
                    $baseTime = ($currentExpiry > time()) ? $currentExpiry : time();
                    $newExpiry = date('Y-m-d H:i:s', strtotime('+30 days', $baseTime));

                    // Activate boost
                    $this->db->query(
                        "UPDATE businesses SET is_boosted = 1, boost_expires_at = ? WHERE id = ?",
                        [$newExpiry, $this->business['id']]
                    );

                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Boost geactiveerd! Je bedrijf wordt nu uitgelicht op de homepage tot ' . date('d-m-Y', strtotime($newExpiry)) . '.'];

                } elseif ($payment->isFailed() || $payment->isCanceled() || $payment->isExpired()) {
                    $this->db->query("UPDATE boost_payments SET status = 'failed' WHERE id = ?", [$boostPayment['id']]);
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betaling niet gelukt. Probeer opnieuw.'];
                } else {
                    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Betaling wordt verwerkt. Ververs de pagina over enkele momenten.'];
                }
            } catch (\Exception $e) {
                error_log("Boost payment check error: " . $e->getMessage());
                $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Status wordt gecontroleerd...'];
            }
        }

        return $this->redirect('/business/boost');
    }

    // ============================================================
    // SUBSCRIPTION ACTIVATION (After Trial)
    // ============================================================

    /**
     * Show subscription activation page
     */
    public function subscription(): string
    {
        $isEarlyAdopter = !empty($this->business['is_early_adopter']);
        $subscriptionPrice = (float)($this->business['subscription_price'] ?? 99.99);

        // Early adopters don't get welcome discount - they pay the early bird price
        $welcomeDiscount = $isEarlyAdopter ? 0 : (float)($this->business['welcome_discount'] ?? 0);
        $finalPrice = max(0, $subscriptionPrice - $welcomeDiscount);

        // Calculate trial status
        $trialEndsAt = !empty($this->business['trial_ends_at']) ? strtotime($this->business['trial_ends_at']) : 0;
        $daysRemaining = max(0, ceil(($trialEndsAt - time()) / 86400));
        $isOnTrial = $this->business['subscription_status'] === 'trial' && $daysRemaining > 0;
        $trialExpired = $this->business['subscription_status'] === 'trial' && $daysRemaining <= 0;

        return $this->view('pages/business/dashboard/subscription', [
            'pageTitle' => 'Abonnement Activeren',
            'business' => $this->business,
            'isEarlyAdopter' => $isEarlyAdopter,
            'subscriptionPrice' => $subscriptionPrice,
            'welcomeDiscount' => $welcomeDiscount,
            'finalPrice' => $finalPrice,
            'isOnTrial' => $isOnTrial,
            'trialExpired' => $trialExpired,
            'daysRemaining' => $daysRemaining,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Activate subscription - redirect to Mollie payment
     */
    public function activateSubscription(): string
    {
        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return $this->redirect('/business/subscription');
        }

        // Calculate price
        $isEarlyAdopter = !empty($this->business['is_early_adopter']);
        $subscriptionPrice = (float)($this->business['subscription_price'] ?? 99.99);
        $welcomeDiscount = $isEarlyAdopter ? 0 : (float)($this->business['welcome_discount'] ?? 0);
        $finalPrice = max(0, $subscriptionPrice - $welcomeDiscount);

        // Don't allow if already active
        if ($this->business['subscription_status'] === 'active') {
            $_SESSION['flash'] = ['type' => 'info', 'message' => 'Je abonnement is al actief!'];
            return $this->redirect('/business/dashboard');
        }

        // Create Mollie payment
        $mollieApiKey = $this->config['mollie']['api_key'] ?? '';
        if (empty($mollieApiKey)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betalingssysteem niet geconfigureerd'];
            return $this->redirect('/business/subscription');
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($mollieApiKey);

            $reference = 'SUB-' . $this->business['id'] . '-' . time();
            $priceType = $isEarlyAdopter ? 'Early Bird aanmeldkosten' : 'Abonnement activatie';

            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($finalPrice, 2, '.', '')
                ],
                'description' => 'GlamourSchedule ' . $priceType . ' - ' . $this->business['company_name'],
                'redirectUrl' => 'https://glamourschedule.nl/business/subscription/complete?ref=' . $reference,
                'webhookUrl' => 'https://glamourschedule.nl/api/webhooks/mollie',
                'method' => ['ideal', 'creditcard', 'bancontact', 'paypal'],
                'metadata' => [
                    'type' => 'subscription_activation',
                    'business_id' => $this->business['id'],
                    'reference' => $reference,
                    'is_early_adopter' => $isEarlyAdopter
                ]
            ]);

            // Store payment reference in business record
            $this->db->query(
                "UPDATE businesses SET payment_id = ? WHERE id = ?",
                [$payment->id, $this->business['id']]
            );

            return $this->redirect($payment->getCheckoutUrl());

        } catch (\Exception $e) {
            error_log("Subscription payment error: " . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betaling kon niet worden gestart. Probeer later opnieuw.'];
            return $this->redirect('/business/subscription');
        }
    }

    /**
     * Handle subscription payment completion
     */
    public function subscriptionComplete(): string
    {
        $reference = $_GET['ref'] ?? '';

        if (empty($reference)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige verificatie'];
            return $this->redirect('/business/subscription');
        }

        // Verify reference matches this business
        if (strpos($reference, 'SUB-' . $this->business['id'] . '-') !== 0) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige betaling'];
            return $this->redirect('/business/subscription');
        }

        // Check Mollie payment status
        $mollieApiKey = $this->config['mollie']['api_key'] ?? '';
        if (!empty($mollieApiKey) && !empty($this->business['payment_id'])) {
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($mollieApiKey);

                $payment = $mollie->payments->get($this->business['payment_id']);

                if ($payment->isPaid()) {
                    // Activate subscription
                    $this->db->query(
                        "UPDATE businesses SET
                            subscription_status = 'active',
                            status = 'active',
                            registration_fee_paid = subscription_price
                         WHERE id = ?",
                        [$this->business['id']]
                    );

                    // Check if there's a sales referral and notify the sales partner
                    if (!empty($this->business['referred_by_sales_partner'])) {
                        $this->notifySalesPartnerOnActivation();
                    }

                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Welkom! Je abonnement is geactiveerd. Je kunt nu volledig gebruik maken van GlamourSchedule.'];
                    return $this->redirect('/business/dashboard');

                } elseif ($payment->isFailed() || $payment->isCanceled() || $payment->isExpired()) {
                    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betaling niet gelukt. Probeer opnieuw.'];
                } else {
                    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Betaling wordt verwerkt. Ververs de pagina over enkele momenten.'];
                }
            } catch (\Exception $e) {
                error_log("Subscription payment check error: " . $e->getMessage());
                $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Status wordt gecontroleerd...'];
            }
        }

        return $this->redirect('/business/subscription');
    }

    /**
     * Notify sales partner when business activates subscription
     */
    private function notifySalesPartnerOnActivation(): void
    {
        try {
            // Get sales referral info
            $stmt = $this->db->query(
                "SELECT sr.*, su.id as sales_user_id, su.name, su.email
                 FROM sales_referrals sr
                 JOIN sales_users su ON su.id = sr.sales_user_id
                 WHERE sr.business_id = ?",
                [$this->business['id']]
            );
            $referral = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($referral) {
                // Update referral status to converted
                $this->db->query(
                    "UPDATE sales_referrals SET status = 'converted', converted_at = NOW() WHERE id = ?",
                    [$referral['id']]
                );

                // Send notification email
                $salesUser = [
                    'id' => $referral['sales_user_id'],
                    'name' => $referral['name'],
                    'email' => $referral['email']
                ];

                SalesController::notifySalesPartnerActivation(
                    $salesUser,
                    $this->business,
                    (float)$referral['commission']
                );
            }
        } catch (\Exception $e) {
            error_log("Failed to notify sales partner on activation: " . $e->getMessage());
        }
    }

    // ============================================================
    // EMPLOYEES MANAGEMENT
    // ============================================================

    /**
     * Employees management page
     */
    public function employees(): string
    {
        // Only allow BV businesses
        if (($this->business['business_type'] ?? 'eenmanszaak') !== 'bv') {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Werknemersbeheer is alleen beschikbaar voor BV bedrijven.'];
            return $this->redirect('/business/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEmployeeAction();
            return $this->redirect('/business/employees');
        }

        $employees = $this->getEmployees();
        $allServices = $this->getServices();
        $employeeServices = $this->getEmployeeServicesMap();
        $employeeHours = $this->getEmployeeHoursMap();

        return $this->view('pages/business/dashboard/employees', [
            'pageTitle' => 'Medewerkers',
            'business' => $this->business,
            'employees' => $employees,
            'allServices' => $allServices,
            'employeeServices' => $employeeServices,
            'employeeHours' => $employeeHours,
            'csrfToken' => $this->csrf()
        ]);
    }

    private function getEmployees(): array
    {
        $stmt = $this->db->query(
            "SELECT e.*,
                    (SELECT GROUP_CONCAT(s.name SEPARATOR ', ')
                     FROM employee_services es
                     JOIN services s ON es.service_id = s.id
                     WHERE es.employee_id = e.id) as service_names
             FROM employees e
             WHERE e.business_id = ?
             ORDER BY e.sort_order, e.name",
            [$this->business['id']]
        );
        $employees = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get services for each employee
        foreach ($employees as &$emp) {
            $servStmt = $this->db->query(
                "SELECT s.id, s.name FROM employee_services es
                 JOIN services s ON es.service_id = s.id
                 WHERE es.employee_id = ?",
                [$emp['id']]
            );
            $emp['services'] = $servStmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $employees;
    }

    private function getEmployeeServicesMap(): array
    {
        $stmt = $this->db->query(
            "SELECT es.employee_id, es.service_id
             FROM employee_services es
             JOIN employees e ON es.employee_id = e.id
             WHERE e.business_id = ?",
            [$this->business['id']]
        );
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $map = [];
        foreach ($rows as $row) {
            if (!isset($map[$row['employee_id']])) {
                $map[$row['employee_id']] = [];
            }
            $map[$row['employee_id']][] = $row['service_id'];
        }
        return $map;
    }

    private function getEmployeeHoursMap(): array
    {
        $stmt = $this->db->query(
            "SELECT eh.*
             FROM employee_hours eh
             JOIN employees e ON eh.employee_id = e.id
             WHERE e.business_id = ?",
            [$this->business['id']]
        );
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $map = [];
        foreach ($rows as $row) {
            if (!isset($map[$row['employee_id']])) {
                $map[$row['employee_id']] = [];
            }
            $map[$row['employee_id']][] = $row;
        }
        return $map;
    }

    private function handleEmployeeAction(): void
    {
        if (!$this->verifyCsrf()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return;
        }

        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'add':
                $this->addEmployee();
                break;
            case 'update':
                $this->updateEmployee();
                break;
            case 'delete':
                $this->deleteEmployee();
                break;
            case 'update_services':
                $this->updateEmployeeServices();
                break;
            case 'update_hours':
                $this->updateEmployeeHours();
                break;
        }
    }

    private function addEmployee(): void
    {
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Naam is verplicht'];
            return;
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: null;
        $phone = trim($_POST['phone'] ?? '') ?: null;
        $bio = trim($_POST['bio'] ?? '') ?: null;
        $color = preg_match('/^#[0-9A-Fa-f]{6}$/', $_POST['color'] ?? '') ? $_POST['color'] : '#000000';

        // Handle photo upload
        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoPath = $this->uploadEmployeePhoto($_FILES['photo']);
        }

        // Get next sort order
        $stmt = $this->db->query(
            "SELECT COALESCE(MAX(sort_order), 0) + 1 as next_order FROM employees WHERE business_id = ?",
            [$this->business['id']]
        );
        $nextOrder = $stmt->fetch(\PDO::FETCH_ASSOC)['next_order'];

        $this->db->query(
            "INSERT INTO employees (business_id, name, email, phone, bio, color, photo, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$this->business['id'], $name, $email, $phone, $bio, $color, $photoPath, $nextOrder]
        );

        // Update employee count
        $this->updateEmployeeCount();

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Medewerker toegevoegd!'];
    }

    private function updateEmployee(): void
    {
        $employeeId = (int)($_POST['employee_id'] ?? 0);
        if (!$employeeId) return;

        // Verify employee belongs to this business
        $stmt = $this->db->query(
            "SELECT id FROM employees WHERE id = ? AND business_id = ?",
            [$employeeId, $this->business['id']]
        );
        if (!$stmt->fetch()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Medewerker niet gevonden'];
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Naam is verplicht'];
            return;
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: null;
        $phone = trim($_POST['phone'] ?? '') ?: null;
        $bio = trim($_POST['bio'] ?? '') ?: null;
        $color = preg_match('/^#[0-9A-Fa-f]{6}$/', $_POST['color'] ?? '') ? $_POST['color'] : '#000000';
        $isActive = isset($_POST['is_active']) && $_POST['is_active'] === '1' ? 1 : 0;

        // Handle photo upload
        $photoSql = '';
        $params = [$name, $email, $phone, $bio, $color, $isActive];

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoPath = $this->uploadEmployeePhoto($_FILES['photo']);
            if ($photoPath) {
                $photoSql = ', photo = ?';
                $params[] = $photoPath;
            }
        }

        $params[] = $employeeId;

        $this->db->query(
            "UPDATE employees SET name = ?, email = ?, phone = ?, bio = ?, color = ?, is_active = ? {$photoSql} WHERE id = ?",
            $params
        );

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Medewerker bijgewerkt!'];
    }

    private function deleteEmployee(): void
    {
        $employeeId = (int)($_POST['employee_id'] ?? 0);
        if (!$employeeId) return;

        // Verify employee belongs to this business
        $stmt = $this->db->query(
            "SELECT id, photo FROM employees WHERE id = ? AND business_id = ?",
            [$employeeId, $this->business['id']]
        );
        $employee = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$employee) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Medewerker niet gevonden'];
            return;
        }

        // Delete photo if exists
        if (!empty($employee['photo'])) {
            $photoPath = BASE_PATH . '/public' . $employee['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        // Delete employee (cascade will handle related records)
        $this->db->query("DELETE FROM employees WHERE id = ?", [$employeeId]);

        // Update employee count
        $this->updateEmployeeCount();

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Medewerker verwijderd!'];
    }

    private function updateEmployeeServices(): void
    {
        $employeeId = (int)($_POST['employee_id'] ?? 0);
        if (!$employeeId) return;

        // Verify employee belongs to this business
        $stmt = $this->db->query(
            "SELECT id FROM employees WHERE id = ? AND business_id = ?",
            [$employeeId, $this->business['id']]
        );
        if (!$stmt->fetch()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Medewerker niet gevonden'];
            return;
        }

        // Remove all current services
        $this->db->query("DELETE FROM employee_services WHERE employee_id = ?", [$employeeId]);

        // Add selected services
        $services = $_POST['services'] ?? [];
        if (is_array($services)) {
            foreach ($services as $serviceId) {
                $serviceId = (int)$serviceId;
                // Verify service belongs to this business
                $stmt = $this->db->query(
                    "SELECT id FROM services WHERE id = ? AND business_id = ?",
                    [$serviceId, $this->business['id']]
                );
                if ($stmt->fetch()) {
                    $this->db->query(
                        "INSERT INTO employee_services (employee_id, service_id) VALUES (?, ?)",
                        [$employeeId, $serviceId]
                    );
                }
            }
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Diensten bijgewerkt!'];
    }

    private function updateEmployeeHours(): void
    {
        $employeeId = (int)($_POST['employee_id'] ?? 0);
        if (!$employeeId) return;

        // Verify employee belongs to this business
        $stmt = $this->db->query(
            "SELECT id FROM employees WHERE id = ? AND business_id = ?",
            [$employeeId, $this->business['id']]
        );
        if (!$stmt->fetch()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Medewerker niet gevonden'];
            return;
        }

        $hours = $_POST['hours'] ?? [];
        if (is_array($hours)) {
            foreach ($hours as $dayOfWeek => $times) {
                $dayOfWeek = (int)$dayOfWeek;
                $isClosed = isset($times['closed']) ? 1 : 0;
                $openTime = $times['open'] ?? '09:00';
                $closeTime = $times['close'] ?? '18:00';

                $this->db->query(
                    "INSERT INTO employee_hours (employee_id, day_of_week, open_time, close_time, is_closed)
                     VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE open_time = ?, close_time = ?, is_closed = ?",
                    [$employeeId, $dayOfWeek, $openTime, $closeTime, $isClosed, $openTime, $closeTime, $isClosed]
                );
            }
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Werktijden bijgewerkt!'];
    }

    private function uploadEmployeePhoto(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $uploadDir = BASE_PATH . '/public/uploads/employees/' . $this->business['id'];
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $filepath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return '/uploads/employees/' . $this->business['id'] . '/' . $filename;
        }

        return null;
    }

    private function updateEmployeeCount(): void
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) as count FROM employees WHERE business_id = ? AND is_active = 1",
            [$this->business['id']]
        );
        $count = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        $this->db->query(
            "UPDATE businesses SET employee_count = ? WHERE id = ?",
            [$count, $this->business['id']]
        );
    }

    // ============================================================
    // POS SYSTEM
    // ============================================================

    /**
     * POS hoofdpagina
     */
    public function pos(): string
    {
        $services = $this->getServices();
        $employees = [];

        // Get employees if BV
        if (($this->business['business_type'] ?? 'eenmanszaak') === 'bv') {
            $stmt = $this->db->query(
                "SELECT * FROM employees WHERE business_id = ? AND is_active = 1 ORDER BY name",
                [$this->business['id']]
            );
            $employees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Get today's POS bookings
        $stmt = $this->db->query(
            "SELECT pb.*, s.name as service_name, e.name as employee_name
             FROM pos_bookings pb
             LEFT JOIN services s ON pb.service_id = s.id
             LEFT JOIN employees e ON pb.employee_id = e.id
             WHERE pb.business_id = ? AND pb.appointment_date = CURDATE()
             ORDER BY pb.appointment_time",
            [$this->business['id']]
        );
        $todayBookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get recent customers
        $stmt = $this->db->query(
            "SELECT * FROM pos_customers WHERE business_id = ? ORDER BY last_appointment_at DESC LIMIT 10",
            [$this->business['id']]
        );
        $recentCustomers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get business hours for today
        $dayOfWeek = date('w');
        $stmt = $this->db->query(
            "SELECT * FROM business_hours WHERE business_id = ? AND day_of_week = ?",
            [$this->business['id'], $dayOfWeek]
        );
        $todayHours = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->view('pages/business/dashboard/pos', [
            'pageTitle' => 'POS Systeem',
            'business' => $this->business,
            'services' => $services,
            'employees' => $employees,
            'todayBookings' => $todayBookings,
            'recentCustomers' => $recentCustomers,
            'todayHours' => $todayHours,
            'csrfToken' => $this->csrf()
        ]);
    }

    /**
     * Voeg een klant toe
     */
    public function posAddCustomer(): string
    {
        header('Content-Type: application/json');

        if (!$this->verifyCsrf()) {
            return json_encode(['success' => false, 'error' => 'Ongeldige sessie']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $name = trim($input['name'] ?? '');
        $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: null;
        $phone = trim($input['phone'] ?? '') ?: null;
        $notes = trim($input['notes'] ?? '') ?: null;

        if (empty($name)) {
            return json_encode(['success' => false, 'error' => 'Naam is verplicht']);
        }

        // Check if customer already exists (by email or phone)
        if ($email) {
            $stmt = $this->db->query(
                "SELECT id FROM pos_customers WHERE business_id = ? AND email = ?",
                [$this->business['id'], $email]
            );
            if ($stmt->fetch()) {
                return json_encode(['success' => false, 'error' => 'Klant met dit e-mailadres bestaat al']);
            }
        }

        $this->db->query(
            "INSERT INTO pos_customers (business_id, name, email, phone, notes) VALUES (?, ?, ?, ?, ?)",
            [$this->business['id'], $name, $email, $phone, $notes]
        );

        $customerId = $this->db->lastInsertId();

        return json_encode([
            'success' => true,
            'customer' => [
                'id' => $customerId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            ]
        ]);
    }

    /**
     * Zoek klanten (op naam, email, telefoon of klant-ID)
     */
    public function posSearchCustomers(): string
    {
        header('Content-Type: application/json');

        $query = trim($_GET['q'] ?? '');

        if (strlen($query) < 1) {
            return json_encode(['customers' => []]);
        }

        // Check if query is a numeric ID
        if (is_numeric($query)) {
            $stmt = $this->db->query(
                "SELECT * FROM pos_customers
                 WHERE business_id = ? AND (id = ? OR name LIKE ? OR email LIKE ? OR phone LIKE ?)
                 ORDER BY CASE WHEN id = ? THEN 0 ELSE 1 END, name LIMIT 10",
                [$this->business['id'], $query, "%$query%", "%$query%", "%$query%", $query]
            );
        } else {
            if (strlen($query) < 2) {
                return json_encode(['customers' => []]);
            }
            $stmt = $this->db->query(
                "SELECT * FROM pos_customers
                 WHERE business_id = ? AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)
                 ORDER BY name LIMIT 10",
                [$this->business['id'], "%$query%", "%$query%", "%$query%"]
            );
        }
        $customers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return json_encode(['customers' => $customers]);
    }

    /**
     * Maak een POS boeking aan
     */
    public function posCreateBooking(): string
    {
        header('Content-Type: application/json');

        if (!$this->verifyCsrf()) {
            return json_encode(['success' => false, 'error' => 'Ongeldige sessie']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $serviceId = (int)($input['service_id'] ?? 0);
        $customerName = trim($input['customer_name'] ?? '');
        $appointmentDate = $input['appointment_date'] ?? '';
        $appointmentTime = $input['appointment_time'] ?? '';
        $paymentMethod = $input['payment_method'] ?? 'online';

        if (!$serviceId || !$customerName || !$appointmentDate || !$appointmentTime) {
            return json_encode(['success' => false, 'error' => 'Vul alle verplichte velden in']);
        }

        // Get service details
        $stmt = $this->db->query(
            "SELECT * FROM services WHERE id = ? AND business_id = ?",
            [$serviceId, $this->business['id']]
        );
        $service = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$service) {
            return json_encode(['success' => false, 'error' => 'Dienst niet gevonden']);
        }

        // Calculate prices
        $servicePrice = (float)$service['price'];
        $serviceFee = 1.75; // Platform fee that customer pays online

        // For cash: customer pays €1.75 online, rest at appointment
        // For online: customer pays full amount online
        $totalPrice = $servicePrice;

        // Generate UUID
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

        // Customer details
        $customerId = !empty($input['customer_id']) ? (int)$input['customer_id'] : null;
        $customerEmail = filter_var($input['customer_email'] ?? '', FILTER_VALIDATE_EMAIL) ?: null;
        $customerPhone = trim($input['customer_phone'] ?? '') ?: null;
        $employeeId = !empty($input['employee_id']) ? (int)$input['employee_id'] : null;
        $notes = trim($input['notes'] ?? '') ?: null;

        // Create booking
        $this->db->query(
            "INSERT INTO pos_bookings (uuid, business_id, customer_id, service_id, employee_id,
             customer_name, customer_email, customer_phone, appointment_date, appointment_time,
             duration_minutes, total_price, service_fee, payment_method, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $uuid, $this->business['id'], $customerId, $serviceId, $employeeId,
                $customerName, $customerEmail, $customerPhone, $appointmentDate, $appointmentTime,
                $service['duration_minutes'], $totalPrice, $serviceFee, $paymentMethod, $notes
            ]
        );

        $bookingId = $this->db->lastInsertId();

        // Update customer if exists
        if ($customerId) {
            $this->db->query(
                "UPDATE pos_customers SET total_appointments = total_appointments + 1, last_appointment_at = NOW() WHERE id = ?",
                [$customerId]
            );
        }

        // Generate payment link
        $paymentLink = 'https://glamourschedule.nl/pay/' . $uuid;

        $this->db->query(
            "UPDATE pos_bookings SET payment_link = ? WHERE id = ?",
            [$paymentLink, $bookingId]
        );

        return json_encode([
            'success' => true,
            'booking' => [
                'id' => $bookingId,
                'uuid' => $uuid,
                'payment_link' => $paymentLink,
                'service_name' => $service['name'],
                'customer_name' => $customerName,
                'date' => date('d-m-Y', strtotime($appointmentDate)),
                'time' => $appointmentTime,
                'total_price' => number_format($totalPrice, 2, ',', '.'),
                'payment_method' => $paymentMethod,
                'online_amount' => $paymentMethod === 'cash' ? number_format($serviceFee, 2, ',', '.') : number_format($totalPrice, 2, ',', '.')
            ]
        ]);
    }

    /**
     * Verstuur betalingslink naar klant
     */
    public function posSendPaymentLink(): string
    {
        header('Content-Type: application/json');

        if (!$this->verifyCsrf()) {
            return json_encode(['success' => false, 'error' => 'Ongeldige sessie']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $bookingUuid = $input['uuid'] ?? '';

        if (empty($bookingUuid)) {
            return json_encode(['success' => false, 'error' => 'Boeking niet gevonden']);
        }

        // Get booking
        $stmt = $this->db->query(
            "SELECT pb.*, s.name as service_name, b.company_name, b.email as business_email
             FROM pos_bookings pb
             JOIN services s ON pb.service_id = s.id
             JOIN businesses b ON pb.business_id = b.id
             WHERE pb.uuid = ? AND pb.business_id = ?",
            [$bookingUuid, $this->business['id']]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            return json_encode(['success' => false, 'error' => 'Boeking niet gevonden']);
        }

        if (empty($booking['customer_email'])) {
            return json_encode(['success' => false, 'error' => 'Geen e-mailadres ingevuld voor deze klant']);
        }

        // Send payment link email
        $this->sendPosPaymentLinkEmail($booking);

        // Update sent timestamp
        $this->db->query(
            "UPDATE pos_bookings SET payment_link_sent_at = NOW() WHERE uuid = ?",
            [$bookingUuid]
        );

        return json_encode([
            'success' => true,
            'message' => 'Betalingslink verzonden naar ' . $booking['customer_email']
        ]);
    }

    /**
     * Verstuur betalingslink email
     */
    private function sendPosPaymentLinkEmail(array $booking): void
    {
        $paymentAmount = $booking['payment_method'] === 'cash'
            ? number_format($booking['service_fee'], 2, ',', '.')
            : number_format($booking['total_price'], 2, ',', '.');

        $totalAmount = number_format($booking['total_price'], 2, ',', '.');
        $appointmentDate = date('d-m-Y', strtotime($booking['appointment_date']));
        $appointmentTime = date('H:i', strtotime($booking['appointment_time']));

        $cashNote = $booking['payment_method'] === 'cash'
            ? "<p style='color:#cccccc;font-size:14px;margin-top:15px;'><strong>Let op:</strong> Je betaalt €{$paymentAmount} online (reserveringskosten). Het resterende bedrag van €" . number_format($booking['total_price'] - $booking['service_fee'], 2, ',', '.') . " betaal je contant bij je afspraak.</p>"
            : "";

        $subject = "Bevestig je afspraak bij {$booking['company_name']}";
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#000000,#333333);padding:40px;text-align:center;color:#fff;">
                            <div style="font-size:48px;margin-bottom:10px;">📅</div>
                            <h1 style="margin:0;font-size:24px;">Afspraak Bevestigen</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;">Beste {$booking['customer_name']},</p>
                            <p style="color:#555;line-height:1.6;">
                                {$booking['company_name']} heeft een afspraak voor je ingepland. Bevestig je afspraak door te betalen.
                            </p>

                            <div style="background:#f9fafb;border-radius:12px;padding:20px;margin:25px 0;">
                                <p style="margin:0 0 10px;color:#ffffff;"><strong>Dienst:</strong> {$booking['service_name']}</p>
                                <p style="margin:0 0 10px;color:#ffffff;"><strong>Datum:</strong> {$appointmentDate}</p>
                                <p style="margin:0 0 10px;color:#ffffff;"><strong>Tijd:</strong> {$appointmentTime}</p>
                                <p style="margin:0;color:#ffffff;"><strong>Totaal:</strong> €{$totalAmount}</p>
                            </div>

                            {$cashNote}

                            <div style="text-align:center;margin:30px 0;">
                                <a href="{$booking['payment_link']}" style="display:inline-block;background:#000000;color:#fff;padding:16px 40px;border-radius:30px;text-decoration:none;font-weight:600;font-size:16px;">
                                    Betaal €{$paymentAmount}
                                </a>
                            </div>

                            <p style="color:#999;font-size:13px;text-align:center;">
                                Deze link is 48 uur geldig.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#cccccc;font-size:13px;">&copy; 2025 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($booking['customer_email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send POS payment link email: " . $e->getMessage());
        }
    }

    /**
     * Haal POS boekingen op
     */
    public function posGetBookings(): string
    {
        header('Content-Type: application/json');

        $date = $_GET['date'] ?? date('Y-m-d');

        $stmt = $this->db->query(
            "SELECT pb.*, s.name as service_name, e.name as employee_name
             FROM pos_bookings pb
             LEFT JOIN services s ON pb.service_id = s.id
             LEFT JOIN employees e ON pb.employee_id = e.id
             WHERE pb.business_id = ? AND pb.appointment_date = ?
             ORDER BY pb.appointment_time",
            [$this->business['id'], $date]
        );
        $bookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return json_encode(['bookings' => $bookings]);
    }

    /**
     * Update booking status
     */
    /**
     * Get POS booking status (for real-time polling)
     */
    public function posGetBookingStatus(): string
    {
        header('Content-Type: application/json');

        $uuid = $_GET['uuid'] ?? '';

        if (empty($uuid)) {
            return json_encode(['success' => false, 'error' => 'UUID required']);
        }

        $stmt = $this->db->query(
            "SELECT uuid, payment_status, booking_status, paid_at FROM pos_bookings WHERE uuid = ? AND business_id = ?",
            [$uuid, $this->business['id']]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            return json_encode(['success' => false, 'error' => 'Boeking niet gevonden']);
        }

        return json_encode([
            'success' => true,
            'uuid' => $booking['uuid'],
            'payment_status' => $booking['payment_status'],
            'booking_status' => $booking['booking_status'],
            'paid_at' => $booking['paid_at']
        ]);
    }

    public function posUpdateBookingStatus(): string
    {
        header('Content-Type: application/json');

        if (!$this->verifyCsrf()) {
            return json_encode(['success' => false, 'error' => 'Ongeldige sessie']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $uuid = $input['uuid'] ?? '';
        $status = $input['status'] ?? '';

        $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        if (!in_array($status, $validStatuses)) {
            return json_encode(['success' => false, 'error' => 'Ongeldige status']);
        }

        $this->db->query(
            "UPDATE pos_bookings SET booking_status = ? WHERE uuid = ? AND business_id = ?",
            [$status, $uuid, $this->business['id']]
        );

        return json_encode(['success' => true]);
    }

    /**
     * Annuleer POS boeking en refund betaling
     */
    public function posCancelBooking(): string
    {
        header('Content-Type: application/json');

        if (!$this->verifyCsrf()) {
            return json_encode(['success' => false, 'error' => 'Ongeldige sessie']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $uuid = $input['uuid'] ?? '';

        // Get booking details
        $stmt = $this->db->query(
            "SELECT * FROM pos_bookings WHERE uuid = ? AND business_id = ?",
            [$uuid, $this->business['id']]
        );
        $booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$booking) {
            return json_encode(['success' => false, 'error' => 'Boeking niet gevonden']);
        }

        // Process refund if payment was made
        $refundSuccess = false;
        $refundError = null;

        if ($booking['payment_status'] === 'paid' && !empty($booking['mollie_payment_id'])) {
            $mollieApiKey = $this->config['mollie']['api_key'] ?? '';

            if (!empty($mollieApiKey)) {
                try {
                    $mollie = new \Mollie\Api\MollieApiClient();
                    $mollie->setApiKey($mollieApiKey);

                    // Get the payment
                    $payment = $mollie->payments->get($booking['mollie_payment_id']);

                    // Check if payment can be refunded
                    if ($payment->canBeRefunded() && $payment->amountRemaining->value > 0) {
                        // Create refund for the full amount
                        $refund = $payment->refund([
                            'amount' => [
                                'currency' => 'EUR',
                                'value' => $payment->amountRemaining->value
                            ],
                            'description' => 'Afspraak geannuleerd - GlamourSchedule'
                        ]);

                        $refundSuccess = true;

                        // Update payment status
                        $this->db->query(
                            "UPDATE pos_bookings SET payment_status = 'refunded' WHERE uuid = ?",
                            [$uuid]
                        );

                        // Send refund notification email
                        $this->sendPosRefundEmail($booking);

                    } elseif ($payment->canBePartiallyRefunded()) {
                        // Try partial refund
                        $refund = $payment->refund([
                            'description' => 'Afspraak geannuleerd - GlamourSchedule'
                        ]);
                        $refundSuccess = true;

                        $this->db->query(
                            "UPDATE pos_bookings SET payment_status = 'refunded' WHERE uuid = ?",
                            [$uuid]
                        );

                        $this->sendPosRefundEmail($booking);
                    } else {
                        $refundError = 'Betaling kan niet worden teruggestort. Neem contact op met de klant.';
                    }

                } catch (\Exception $e) {
                    error_log("POS Refund error: " . $e->getMessage());
                    $refundError = 'Refund mislukt: ' . $e->getMessage();
                }
            }
        }

        // Update booking status
        $this->db->query(
            "UPDATE pos_bookings SET booking_status = 'cancelled' WHERE uuid = ?",
            [$uuid]
        );

        // Send cancellation email to customer
        $this->sendPosCancellationEmail($booking);

        $response = ['success' => true];
        if ($refundSuccess) {
            $response['message'] = 'Afspraak geannuleerd en betaling wordt teruggestort.';
        } elseif ($refundError) {
            $response['warning'] = $refundError;
        }

        return json_encode($response);
    }

    /**
     * Send refund notification email
     */
    private function sendPosRefundEmail(array $booking): void
    {
        if (empty($booking['customer_email'])) return;

        $subject = "Terugbetaling - Je afspraak is geannuleerd";
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:#000000;padding:40px;text-align:center;color:#fff;">
                            <div style="font-size:48px;margin-bottom:10px;">💸</div>
                            <h1 style="margin:0;font-size:24px;">Terugbetaling Onderweg</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;">Beste {$booking['customer_name']},</p>
                            <p style="color:#555;line-height:1.6;">
                                Je afspraak is geannuleerd en je betaling wordt teruggestort naar je rekening.
                            </p>

                            <div style="background:#f0fdf4;border-radius:12px;padding:20px;margin:25px 0;border:1px solid #86efac;">
                                <p style="margin:0;color:#166534;">
                                    <strong>Let op:</strong> Het terugstorten kan 3-5 werkdagen duren, afhankelijk van je bank.
                                </p>
                            </div>

                            <p style="color:#555;">
                                Excuses voor het ongemak. We hopen je snel weer te zien!
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#cccccc;font-size:13px;">&copy; 2025 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($booking['customer_email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send POS refund email: " . $e->getMessage());
        }
    }

    /**
     * Send cancellation email to customer
     */
    private function sendPosCancellationEmail(array $booking): void
    {
        if (empty($booking['customer_email'])) return;

        $appointmentDate = date('d-m-Y', strtotime($booking['appointment_date']));
        $appointmentTime = date('H:i', strtotime($booking['appointment_time']));

        $subject = "Afspraak Geannuleerd";
        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#0a0a0a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0a0a;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:#000000;padding:40px;text-align:center;color:#fff;">
                            <div style="font-size:48px;margin-bottom:10px;">❌</div>
                            <h1 style="margin:0;font-size:24px;">Afspraak Geannuleerd</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:18px;color:#ffffff;">Beste {$booking['customer_name']},</p>
                            <p style="color:#555;line-height:1.6;">
                                Je afspraak op <strong>{$appointmentDate}</strong> om <strong>{$appointmentTime}</strong> is geannuleerd.
                            </p>

                            <p style="color:#555;line-height:1.6;margin-top:20px;">
                                Excuses voor het ongemak. Neem gerust contact op als je vragen hebt of een nieuwe afspraak wilt maken.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0a0a0a;padding:20px;text-align:center;border-top:1px solid #333;">
                            <p style="margin:0;color:#cccccc;font-size:13px;">&copy; 2025 GlamourSchedule</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($booking['customer_email'], $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send POS cancellation email: " . $e->getMessage());
        }
    }

    // =========================================================================
    // INVENTORY MANAGEMENT
    // =========================================================================

    public function inventory(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleInventoryAction();
            return $this->redirect('/business/inventory');
        }

        $inventory = $this->getInventory();
        $services = $this->getServices();
        $lowStockItems = $this->getLowStockItems();

        return $this->view('pages/business/dashboard/inventory', [
            'pageTitle' => 'Voorraad',
            'business' => $this->business,
            'inventory' => $inventory,
            'services' => $services,
            'lowStockItems' => $lowStockItems,
            'csrfToken' => $this->csrf()
        ]);
    }

    private function handleInventoryAction(): void
    {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

            $quantity = (int)$_POST['quantity'];

            $this->db->query(
                "INSERT INTO inventory (uuid, business_id, name, description, sku, quantity, min_quantity, unit, purchase_price, sell_price)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $uuid,
                    $this->business['id'],
                    trim($_POST['name']),
                    trim($_POST['description'] ?? ''),
                    trim($_POST['sku'] ?? ''),
                    $quantity,
                    (int)($_POST['min_quantity'] ?? 0),
                    trim($_POST['unit'] ?? 'stuks'),
                    (float)($_POST['purchase_price'] ?? 0),
                    (float)($_POST['sell_price'] ?? 0)
                ]
            );

            // Log initial stock transaction
            $inventoryId = $this->db->lastInsertId();
            if ($quantity > 0) {
                $this->logInventoryTransaction($inventoryId, 'purchase', $quantity, 0, $quantity, 'Beginvoorraad');
            }

        } elseif ($action === 'edit') {
            $inventoryId = (int)$_POST['inventory_id'];

            // Verify ownership
            $item = $this->db->fetch(
                "SELECT * FROM inventory WHERE id = ? AND business_id = ?",
                [$inventoryId, $this->business['id']]
            );

            if ($item) {
                $this->db->query(
                    "UPDATE inventory SET name = ?, description = ?, sku = ?, min_quantity = ?, unit = ?, purchase_price = ?, sell_price = ?
                     WHERE id = ? AND business_id = ?",
                    [
                        trim($_POST['name']),
                        trim($_POST['description'] ?? ''),
                        trim($_POST['sku'] ?? ''),
                        (int)($_POST['min_quantity'] ?? 0),
                        trim($_POST['unit'] ?? 'stuks'),
                        (float)($_POST['purchase_price'] ?? 0),
                        (float)($_POST['sell_price'] ?? 0),
                        $inventoryId,
                        $this->business['id']
                    ]
                );
            }

        } elseif ($action === 'delete') {
            $inventoryId = (int)$_POST['inventory_id'];
            $this->db->query(
                "DELETE FROM inventory WHERE id = ? AND business_id = ?",
                [$inventoryId, $this->business['id']]
            );
        }
    }

    public function adjustInventory(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/business/inventory');
        }

        $inventoryId = (int)$_POST['inventory_id'];
        $adjustmentType = $_POST['adjustment_type'] ?? 'add';
        $quantity = abs((int)$_POST['quantity']);
        $notes = trim($_POST['notes'] ?? '');

        // Get current item
        $item = $this->db->fetch(
            "SELECT * FROM inventory WHERE id = ? AND business_id = ?",
            [$inventoryId, $this->business['id']]
        );

        if (!$item) {
            return $this->redirect('/business/inventory');
        }

        $quantityBefore = $item['quantity'];

        if ($adjustmentType === 'add') {
            $quantityAfter = $quantityBefore + $quantity;
            $transactionType = 'purchase';
        } else {
            $quantityAfter = max(0, $quantityBefore - $quantity);
            $quantity = -$quantity;
            $transactionType = 'adjustment';
        }

        // Update quantity
        $this->db->query(
            "UPDATE inventory SET quantity = ? WHERE id = ?",
            [$quantityAfter, $inventoryId]
        );

        // Log transaction
        $this->logInventoryTransaction($inventoryId, $transactionType, $quantity, $quantityBefore, $quantityAfter, $notes);

        return $this->redirect('/business/inventory');
    }

    public function linkInventoryToService(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/business/inventory');
        }

        $inventoryId = (int)$_POST['inventory_id'];
        $serviceId = (int)$_POST['service_id'];
        $quantityUsed = (float)($_POST['quantity_used'] ?? 1);

        // Verify ownership of both inventory and service
        $item = $this->db->fetch(
            "SELECT id FROM inventory WHERE id = ? AND business_id = ?",
            [$inventoryId, $this->business['id']]
        );

        $service = $this->db->fetch(
            "SELECT id FROM services WHERE id = ? AND business_id = ?",
            [$serviceId, $this->business['id']]
        );

        if ($item && $service) {
            // Insert or update link
            $this->db->query(
                "INSERT INTO inventory_service_link (inventory_id, service_id, quantity_used)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE quantity_used = ?",
                [$inventoryId, $serviceId, $quantityUsed, $quantityUsed]
            );
        }

        return $this->redirect('/business/inventory');
    }

    public function unlinkInventoryFromService(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/business/inventory');
        }

        $inventoryId = (int)$_POST['inventory_id'];
        $serviceId = (int)$_POST['service_id'];

        // Verify ownership
        $item = $this->db->fetch(
            "SELECT id FROM inventory WHERE id = ? AND business_id = ?",
            [$inventoryId, $this->business['id']]
        );

        if ($item) {
            $this->db->query(
                "DELETE FROM inventory_service_link WHERE inventory_id = ? AND service_id = ?",
                [$inventoryId, $serviceId]
            );
        }

        return $this->redirect('/business/inventory');
    }

    private function getInventory(): array
    {
        $stmt = $this->db->query(
            "SELECT i.*,
                    GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as linked_services,
                    GROUP_CONCAT(DISTINCT CONCAT(isl.service_id, ':', isl.quantity_used) SEPARATOR ',') as service_links
             FROM inventory i
             LEFT JOIN inventory_service_link isl ON i.id = isl.inventory_id
             LEFT JOIN services s ON isl.service_id = s.id
             WHERE i.business_id = ? AND i.is_active = 1
             GROUP BY i.id
             ORDER BY i.name ASC",
            [$this->business['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getLowStockItems(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM inventory
             WHERE business_id = ? AND is_active = 1 AND quantity <= min_quantity AND min_quantity > 0
             ORDER BY quantity ASC",
            [$this->business['id']]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function logInventoryTransaction(int $inventoryId, string $type, int $quantity, int $before, int $after, string $notes = '', ?int $bookingId = null): void
    {
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

        $this->db->query(
            "INSERT INTO inventory_transactions (uuid, inventory_id, business_id, booking_id, transaction_type, quantity, quantity_before, quantity_after, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$uuid, $inventoryId, $this->business['id'], $bookingId, $type, $quantity, $before, $after, $notes]
        );
    }
}

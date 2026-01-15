<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

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

        return $this->view('pages/business/dashboard/index', [
            'pageTitle' => 'Bedrijf Dashboard',
            'business' => $this->business,
            'stats' => $stats,
            'todayBookings' => $todayBookings,
            'recentBookings' => $recentBookings
        ]);
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
            "SELECT COALESCE(SUM(total_price - admin_fee), 0) as revenue FROM bookings WHERE business_id = ? AND status = 'completed'",
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
            "SELECT COALESCE(SUM(total_price - admin_fee), 0) as pending
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
        if (!$this->verifyCsrf()) { die('CSRF token mismatch'); }

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
        $genderTheme = in_array($_POST['gender_theme'] ?? '', ['neutral', 'feminine', 'masculine']) ? $_POST['gender_theme'] : 'neutral';

        $this->db->query(
            "UPDATE businesses SET theme = ?, gender_theme = ? WHERE id = ?",
            [$theme, $genderTheme, $this->business['id']]
        );

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Thema instellingen opgeslagen!'];
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

        if ($stmt->fetch()) {
            // Update existing record
            $fields = [];
            $values = [];

            foreach ($data as $key => $value) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }

            $values[] = $this->business['id'];

            $this->db->query(
                "UPDATE business_settings SET " . implode(', ', $fields) . " WHERE business_id = ?",
                $values
            );
        } else {
            // Insert new record
            $data['business_id'] = $this->business['id'];
            $columns = array_keys($data);
            $placeholders = array_fill(0, count($data), '?');

            $this->db->query(
                "INSERT INTO business_settings (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")",
                array_values($data)
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
    // IBAN VERIFICATION (Direct Mollie Payment)
    // ============================================================

    /**
     * Start IBAN verification - redirect to Mollie for €0.01 iDEAL payment
     * IBAN will be automatically retrieved from the payment
     */
    public function addIban(): string
    {
        file_put_contents('/tmp/iban_debug.log', date('Y-m-d H:i:s') . " - addIban() called for business ID: " . $this->business['id'] . "\n", FILE_APPEND);

        if (!$this->verifyCsrf()) {
            file_put_contents('/tmp/iban_debug.log', date('Y-m-d H:i:s') . " - CSRF failed\n", FILE_APPEND);
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ongeldige aanvraag'];
            return $this->redirect('/business/profile');
        }

        file_put_contents('/tmp/iban_debug.log', date('Y-m-d H:i:s') . " - CSRF OK, creating Mollie payment...\n", FILE_APPEND);

        // Directly create Mollie payment - IBAN will be retrieved from payment details
        return $this->createIbanMolliePayment();
    }

    /**
     * Create Mollie payment for €0.01 verification
     * IBAN will be extracted from iDEAL payment response
     */
    private function createIbanMolliePayment(): string
    {
        $log = function($msg) { file_put_contents('/tmp/iban_debug.log', date('Y-m-d H:i:s') . " - " . $msg . "\n", FILE_APPEND); };

        $mollieApiKey = $this->config['mollie']['api_key'] ?? '';
        $log("API key present: " . (!empty($mollieApiKey) ? 'yes (' . substr($mollieApiKey, 0, 10) . '...)' : 'no'));

        if (empty($mollieApiKey)) {
            $log("No API key configured - aborting");
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Betalingssysteem niet geconfigureerd'];
            return $this->redirect('/business/profile');
        }

        try {
            $log("Creating Mollie client...");
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($mollieApiKey);

            // Create unique payment reference
            $reference = 'IBAN-' . $this->business['id'] . '-' . time();
            $log("Reference: " . $reference);

            $log("Creating payment...");
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

            $log("Payment created: " . $payment->id);

            // Store payment reference (IBAN will be filled after payment)
            $this->db->query(
                "INSERT INTO iban_verifications (business_id, verification_code, mollie_payment_id, status, expires_at)
                 VALUES (?, ?, ?, 'payment_pending', DATE_ADD(NOW(), INTERVAL 1 HOUR))",
                [$this->business['id'], $reference, $payment->id]
            );

            $checkoutUrl = $payment->getCheckoutUrl();
            $log("Redirecting to: " . $checkoutUrl);

            // Redirect to Mollie iDEAL
            return $this->redirect($checkoutUrl);

        } catch (\Exception $e) {
            $log("ERROR: " . $e->getMessage() . " | File: " . $e->getFile() . ":" . $e->getLine());
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
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#000000,#404040);color:#ffffff;padding:35px;text-align:center;">
                    <div style="font-size:42px;margin-bottom:8px;">🔐</div>
                    <h1 style="margin:0;font-size:24px;">IBAN Wijzigen</h1>
                </td></tr>
                <tr><td style="padding:35px;">
                    <p style="font-size:16px;color:#333;">Beste {$this->business['company_name']},</p>
                    <p style="font-size:16px;color:#555;">Je wilt je IBAN wijzigen. Gebruik deze code om door te gaan:</p>
                    <div style="background:#f5f5f5;border:3px solid #000000;border-radius:12px;padding:25px;margin:25px 0;text-align:center;">
                        <p style="margin:0;font-size:42px;font-weight:bold;color:#000000;letter-spacing:8px;font-family:monospace;">{$code}</p>
                    </div>
                    <p style="font-size:14px;color:#666;">Deze code is 15 minuten geldig.</p>
                    <p style="font-size:14px;color:#dc2626;margin-top:20px;"><strong>Let op:</strong> Deel deze code nooit met anderen!</p>
                </td></tr>
                <tr><td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                    <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#333333,#000000);color:#ffffff;padding:35px;text-align:center;">
                    <div style="font-size:42px;margin-bottom:8px;">✓</div>
                    <h1 style="margin:0;font-size:24px;">IBAN Geverifieerd!</h1>
                </td></tr>
                <tr><td style="padding:35px;">
                    <p style="font-size:16px;color:#333;">Beste {$this->business['company_name']},</p>
                    <p style="font-size:16px;color:#555;">Je IBAN is succesvol geverifieerd en gekoppeld aan je account.</p>
                    <div style="background:#f0fdf4;border-radius:10px;padding:1rem;margin:20px 0;">
                        <p style="margin:0;font-family:monospace;font-size:1.1rem;color:#000000;">{$maskedIban}</p>
                    </div>
                    <p style="font-size:14px;color:#666;">Uitbetalingen worden voortaan naar dit rekeningnummer overgemaakt.</p>
                </td></tr>
                <tr><td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
                    <p style="margin:0;color:#666;font-size:12px;">&copy; 2025 GlamourSchedule</p>
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

        // Try to find booking by UUID (from QR URL) or booking number
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
        // Check if it's a booking number (e.g., GS-240104-ABC1)
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
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:20px;">
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
                            <p style="font-size:18px;color:#333;">Beste {$customerName},</p>
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
                        <td style="background:#fafafa;padding:20px;text-align:center;border-top:1px solid #eee;">
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

        try {
            $mailer = new \GlamourSchedule\Core\Mailer();
            $mailer->send($customerEmail, $subject, $htmlBody);
        } catch (\Exception $e) {
            error_log("Failed to send check-in confirmation: " . $e->getMessage());
        }
    }
}

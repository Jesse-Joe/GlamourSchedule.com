<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;

class SearchController extends Controller
{
    public function index(): string
    {
        $query = trim($_GET['q'] ?? '');
        $category = $_GET['category'] ?? '';
        $categories = $_GET['categories'] ?? ''; // Multiple categories comma-separated
        $group = $_GET['group'] ?? '';
        $location = trim($_GET['location'] ?? '');
        $sort = $_GET['sort'] ?? 'rating';

        // GPS coordinates from URL params (user granted browser GPS)
        $userLat = !empty($_GET['lat']) ? (float)$_GET['lat'] : null;
        $userLng = !empty($_GET['lng']) ? (float)$_GET['lng'] : null;

        // Determine location source for the view
        $locationSource = ($userLat && $userLng) ? 'gps' : 'none';

        // Server-side coordinates for distance calculation (GPS or IP fallback)
        $calcLat = $userLat;
        $calcLng = $userLng;
        if (!$calcLat || !$calcLng) {
            $ipLocation = $this->getLocationFromIP();
            if ($ipLocation) {
                $calcLat = $ipLocation['lat'];
                $calcLng = $ipLocation['lng'];
                if ($locationSource === 'none') {
                    $locationSource = 'ip';
                }
            }
        }

        // New filter parameters (use calc coordinates for distance sorting)
        $filters = [
            'price_min' => !empty($_GET['price_min']) ? (float)$_GET['price_min'] : null,
            'price_max' => !empty($_GET['price_max']) ? (float)$_GET['price_max'] : null,
            'high_rated' => isset($_GET['high_rated']) && $_GET['high_rated'] === '1',
            'open_now' => isset($_GET['open_now']) && $_GET['open_now'] === '1',
            'open_weekend' => isset($_GET['open_weekend']) && $_GET['open_weekend'] === '1',
            'open_evening' => isset($_GET['open_evening']) && $_GET['open_evening'] === '1',
            'user_lat' => $calcLat,
            'user_lng' => $calcLng,
            'group' => $group,
            'categories' => $categories,
        ];

        $businesses = [];
        $categories = $this->getCategories();

        $hasFilters = $query || $category || $categories || $group || $location ||
                      $filters['price_min'] || $filters['price_max'] ||
                      $filters['high_rated'] || $filters['open_now'] ||
                      $filters['open_weekend'] || $filters['open_evening'];

        if ($hasFilters) {
            $businesses = $this->searchBusinesses($query, $category, $location, $sort, $filters);
        } else {
            $businesses = $this->getFeaturedBusinesses($sort, $filters);
        }

        // Enrich businesses with additional data
        $businesses = $this->enrichBusinessData($businesses);

        return $this->view('pages/search/index', [
            'pageTitle' => $this->t('search'),
            'businesses' => $businesses,
            'categories' => $categories,
            'query' => $query,
            'category' => $category,
            'location' => $location,
            'sort' => $sort,
            'filters' => $filters,
            'userLat' => $userLat,
            'userLng' => $userLng,
            'locationSource' => $locationSource,
            'searchCountry' => $this->detectCountryFromLocation($location, $calcLat, $calcLng)
        ]);
    }

    /**
     * Get location from IP address using free IP geolocation API
     */
    private function getLocationFromIP(): ?array
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

        // Skip localhost/private IPs
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            // Default to Amsterdam for local development
            return ['lat' => 52.3676, 'lng' => 4.9041, 'city' => 'Amsterdam'];
        }

        // Handle multiple IPs (X-Forwarded-For can contain comma-separated list)
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }

        // Use ip-api.com (free, no key required, 45 requests/minute)
        $cacheKey = 'ip_geo_' . md5($ip);
        $cached = $_SESSION[$cacheKey] ?? null;

        if ($cached && $cached['expires'] > time()) {
            return $cached['data'];
        }

        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,lat,lon,city");
            if ($response) {
                $data = json_decode($response, true);
                if ($data && $data['status'] === 'success') {
                    $result = [
                        'lat' => (float)$data['lat'],
                        'lng' => (float)$data['lon'],
                        'city' => $data['city'] ?? ''
                    ];
                    // Cache for 1 hour
                    $_SESSION[$cacheKey] = ['data' => $result, 'expires' => time() + 3600];
                    return $result;
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return null;
    }

    /**
     * Detect country from location string or coordinates
     * Used for auto-selecting map view on search page
     */
    private function detectCountryFromLocation(?string $location, ?float $lat, ?float $lng): string
    {
        // Only detect country if user actually entered a search location
        // Don't use IP-based coordinates - let the frontend handle that with language fallback
        $locationLower = strtolower(trim($location ?? ''));

        if (empty($locationLower)) {
            // No location search - return empty to let frontend use language-based default
            return '';
        }

        // Dutch cities
        $nlCities = ['amsterdam', 'rotterdam', 'den haag', 'the hague', 'utrecht', 'eindhoven',
                     'tilburg', 'groningen', 'almere', 'breda', 'nijmegen', 'arnhem', 'haarlem',
                     'enschede', 'maastricht', 'zwolle', 'leiden', 'dordrecht', 'zoetermeer',
                     'amersfoort', 'delft', 'alkmaar', 'deventer', 'hilversum', 'apeldoorn',
                     'leeuwarden', 'zaandam', 'den bosch', 's-hertogenbosch', 'venlo', 'assen', 'gouda'];

        // Belgian cities
        $beCities = ['brussel', 'brussels', 'antwerpen', 'antwerp', 'gent', 'ghent', 'brugge',
                     'bruges', 'leuven', 'luik', 'liège', 'charleroi', 'namur', 'mons', 'oostende'];

        // German cities
        $deCities = ['berlin', 'münchen', 'munich', 'hamburg', 'köln', 'koln', 'cologne',
                     'frankfurt', 'stuttgart', 'düsseldorf', 'dusseldorf', 'dortmund', 'essen',
                     'leipzig', 'bremen', 'dresden', 'hannover', 'nürnberg', 'nuremberg'];

        // French cities
        $frCities = ['paris', 'marseille', 'lyon', 'toulouse', 'nice', 'nantes', 'strasbourg',
                     'montpellier', 'bordeaux', 'lille', 'rennes', 'reims', 'saint-etienne'];

        // Italian cities
        $itCities = ['roma', 'rome', 'milano', 'milan', 'napoli', 'naples', 'torino', 'turin',
                     'palermo', 'genova', 'genoa', 'bologna', 'firenze', 'florence', 'bari',
                     'catania', 'venezia', 'venice', 'verona', 'messina', 'padova', 'trieste'];

        // Spanish cities
        $esCities = ['madrid', 'barcelona', 'valencia', 'sevilla', 'seville', 'zaragoza',
                     'málaga', 'malaga', 'murcia', 'palma', 'bilbao', 'alicante', 'córdoba'];

        // UK cities
        $gbCities = ['london', 'birmingham', 'manchester', 'leeds', 'glasgow', 'liverpool',
                     'bristol', 'sheffield', 'edinburgh', 'cardiff', 'belfast', 'nottingham'];

        // US cities
        $usCities = ['new york', 'los angeles', 'chicago', 'houston', 'phoenix', 'philadelphia',
                     'san antonio', 'san diego', 'dallas', 'san jose', 'austin', 'miami', 'seattle'];

        // Check for Dutch postal code (4 digits + optional 2 letters)
        if (preg_match('/^\d{4}\s?[a-z]{0,2}$/i', $locationLower)) {
            return 'NL';
        }

        // Country detection mapping
        $countryMappings = [
            'NL' => $nlCities,
            'BE' => $beCities,
            'DE' => $deCities,
            'FR' => $frCities,
            'IT' => $itCities,
            'ES' => $esCities,
            'GB' => $gbCities,
            'US' => $usCities,
        ];

        // Check city names
        foreach ($countryMappings as $country => $cities) {
            foreach ($cities as $city) {
                if (strpos($locationLower, $city) !== false) {
                    return $country;
                }
            }
        }

        // No match found - return empty to use language-based default
        return '';
    }

    /**
     * Detect country from coordinates using bounding boxes
     */
    private function detectCountryFromCoordinates(?float $lat, ?float $lng): string
    {
        if (!$lat || !$lng) {
            return '';
        }

        // Netherlands: lat 50.75-53.5, lng 3.3-7.2
        if ($lat >= 50.75 && $lat <= 53.5 && $lng >= 3.3 && $lng <= 7.2) {
            return 'NL';
        }
        // Belgium: lat 49.5-51.5, lng 2.5-6.4
        if ($lat >= 49.5 && $lat <= 51.5 && $lng >= 2.5 && $lng <= 6.4) {
            return 'BE';
        }
        // Germany: lat 47.3-55.1, lng 5.9-15.0
        if ($lat >= 47.3 && $lat <= 55.1 && $lng >= 5.9 && $lng <= 15.0) {
            return 'DE';
        }
        // France: lat 41.3-51.1, lng -5.1-9.6
        if ($lat >= 41.3 && $lat <= 51.1 && $lng >= -5.1 && $lng <= 9.6) {
            return 'FR';
        }

        return '';
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }

    /**
     * Geocode a location string to coordinates
     * Uses cache for common cities, falls back to Nominatim API
     */
    private function geocodeLocation(string $location): ?array
    {
        $location = strtolower(trim($location));
        if (empty($location)) {
            return null;
        }

        // Common Dutch/Belgian cities cache
        $cityCache = [
            'amsterdam' => ['lat' => 52.3676, 'lng' => 4.9041],
            'rotterdam' => ['lat' => 51.9244, 'lng' => 4.4777],
            'den haag' => ['lat' => 52.0705, 'lng' => 4.3007],
            'the hague' => ['lat' => 52.0705, 'lng' => 4.3007],
            'utrecht' => ['lat' => 52.0907, 'lng' => 5.1214],
            'eindhoven' => ['lat' => 51.4416, 'lng' => 5.4697],
            'tilburg' => ['lat' => 51.5555, 'lng' => 5.0913],
            'groningen' => ['lat' => 53.2194, 'lng' => 6.5665],
            'almere' => ['lat' => 52.3508, 'lng' => 5.2647],
            'breda' => ['lat' => 51.5719, 'lng' => 4.7683],
            'nijmegen' => ['lat' => 51.8426, 'lng' => 5.8546],
            'arnhem' => ['lat' => 51.9851, 'lng' => 5.8987],
            'haarlem' => ['lat' => 52.3874, 'lng' => 4.6462],
            'enschede' => ['lat' => 52.2215, 'lng' => 6.8937],
            'maastricht' => ['lat' => 50.8514, 'lng' => 5.6909],
            'zwolle' => ['lat' => 52.5168, 'lng' => 6.0830],
            'leiden' => ['lat' => 52.1601, 'lng' => 4.4970],
            'dordrecht' => ['lat' => 51.8133, 'lng' => 4.6901],
            'zoetermeer' => ['lat' => 52.0575, 'lng' => 4.4931],
            'amersfoort' => ['lat' => 52.1561, 'lng' => 5.3878],
            'delft' => ['lat' => 52.0116, 'lng' => 4.3571],
            'alkmaar' => ['lat' => 52.6324, 'lng' => 4.7534],
            'deventer' => ['lat' => 52.2549, 'lng' => 6.1636],
            'hilversum' => ['lat' => 52.2292, 'lng' => 5.1669],
            'apeldoorn' => ['lat' => 52.2112, 'lng' => 5.9699],
            'leeuwarden' => ['lat' => 53.2012, 'lng' => 5.7999],
            'zaandam' => ['lat' => 52.4388, 'lng' => 4.8262],
            'den bosch' => ['lat' => 51.6978, 'lng' => 5.3037],
            's-hertogenbosch' => ['lat' => 51.6978, 'lng' => 5.3037],
            'venlo' => ['lat' => 51.3704, 'lng' => 6.1724],
            'assen' => ['lat' => 52.9925, 'lng' => 6.5649],
            'gouda' => ['lat' => 52.0115, 'lng' => 4.7104],
            // Belgium
            'brussel' => ['lat' => 50.8503, 'lng' => 4.3517],
            'brussels' => ['lat' => 50.8503, 'lng' => 4.3517],
            'antwerpen' => ['lat' => 51.2194, 'lng' => 4.4025],
            'antwerp' => ['lat' => 51.2194, 'lng' => 4.4025],
            'gent' => ['lat' => 51.0543, 'lng' => 3.7174],
            'ghent' => ['lat' => 51.0543, 'lng' => 3.7174],
            'brugge' => ['lat' => 51.2093, 'lng' => 3.2247],
            'bruges' => ['lat' => 51.2093, 'lng' => 3.2247],
            'leuven' => ['lat' => 50.8798, 'lng' => 4.7005],
            'luik' => ['lat' => 50.6326, 'lng' => 5.5797],
            'liège' => ['lat' => 50.6326, 'lng' => 5.5797],
            // Germany border cities
            'düsseldorf' => ['lat' => 51.2277, 'lng' => 6.7735],
            'dusseldorf' => ['lat' => 51.2277, 'lng' => 6.7735],
            'köln' => ['lat' => 50.9375, 'lng' => 6.9603],
            'koln' => ['lat' => 50.9375, 'lng' => 6.9603],
            'cologne' => ['lat' => 50.9375, 'lng' => 6.9603],
        ];

        // Check cache first
        if (isset($cityCache[$location])) {
            return $cityCache[$location];
        }

        // Check for postal code (Dutch format: 4 digits + 2 letters, or just 4 digits)
        if (preg_match('/^(\d{4})\s*([a-zA-Z]{2})?$/', $location, $matches)) {
            $postalCode = $matches[1] . (isset($matches[2]) ? strtoupper($matches[2]) : '');
            return $this->geocodePostalCode($postalCode);
        }

        // Fallback to Nominatim API for unknown locations
        return $this->geocodeWithNominatim($location);
    }

    /**
     * Geocode Dutch postal code using Nominatim
     */
    private function geocodePostalCode(string $postalCode): ?array
    {
        // Add NL country code for Dutch postal codes
        $query = urlencode($postalCode . ', Netherlands');
        return $this->geocodeWithNominatim($postalCode . ', Netherlands');
    }

    /**
     * Geocode using OpenStreetMap Nominatim API
     */
    private function geocodeWithNominatim(string $query): ?array
    {
        try {
            $query = urlencode($query);
            $url = "https://nominatim.openstreetmap.org/search?q={$query}&format=json&limit=1&countrycodes=nl,be,de";

            $context = stream_context_create([
                'http' => [
                    'timeout' => 3,
                    'header' => "User-Agent: GlamourSchedule/1.0\r\n"
                ]
            ]);

            $response = @file_get_contents($url, false, $context);
            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data[0]['lat']) && !empty($data[0]['lon'])) {
                    return [
                        'lat' => (float)$data[0]['lat'],
                        'lng' => (float)$data[0]['lon']
                    ];
                }
            }
        } catch (\Exception $e) {
            error_log("Geocoding error: " . $e->getMessage());
        }

        return null;
    }

    private function searchBusinesses(string $query, string $category, string $location, string $sort, array $filters = []): array
    {
        // Group to category slug mapping
        $groupMapping = [
            'haar' => ['hair', 'kapper', 'hairdresser', 'hairstylist', 'barber', 'barbershop', 'herenkapper', 'dameskapper', 'afro-hair', 'curly-hair', 'hair-colorist', 'bridal-hair'],
            'nagels' => ['nails', 'nail-salon', 'nagelstudio', 'manicure', 'pedicure', 'gel-nails', 'gelnagels', 'acrylic-nails', 'polygel', 'nail-art'],
            'huid' => ['beauty', 'beauty-salon', 'skincare', 'huidverzorging', 'facial', 'gezichtsbehandeling', 'acne-treatment', 'dermapen', 'microneedling', 'hydrafacial'],
            'lichaam' => ['massage', 'massage-therapist', 'deep-tissue', 'hot-stone', 'swedish-massage', 'thai-massage', 'sports-massage', 'body-treatments', 'body-contouring', 'aromatherapy', 'reflexology'],
            'ontharing' => ['waxing', 'waxsalon', 'brazilian-wax', 'laser-hair-removal', 'ipl-treatment', 'electrolysis', 'threading', 'sugaring'],
            'makeup' => ['makeup', 'makeup-artist', 'visagie', 'bridal-makeup', 'permanent-makeup', 'microblading', 'eyelash-extensions', 'wimperextensions', 'lash-lift', 'brow-lamination'],
            'wellness' => ['spa', 'day-spa', 'wellness', 'wellness-center', 'hammam', 'sauna', 'infrared-sauna', 'float-therapy'],
            'bruinen' => ['tanning-salon', 'sunbed', 'zonnestudio', 'spray-tan', 'self-tan'],
            'medisch' => ['botox', 'fillers', 'huidtherapeut', 'cosmetisch-arts', 'physiotherapy'],
            'alternatief' => ['acupuncture', 'acupunctuur', 'ayurveda', 'reiki', 'meditation', 'yoga-studio', 'pilates', 'holistic-therapy'],
        ];

        $sql = "SELECT DISTINCT b.*, b.company_name as name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.id) as review_count,
                       MIN(s.price) as min_price,
                       MAX(s.price) as max_price
                FROM businesses b
                LEFT JOIN reviews r ON b.id = r.business_id
                LEFT JOIN services s ON b.id = s.business_id AND s.is_active = 1";

        // Join categories if filtering by category, categories, or group
        $needsCategoryJoin = $category || !empty($filters['categories']) || !empty($filters['group']);
        if ($needsCategoryJoin) {
            $sql .= " INNER JOIN business_categories bc ON b.id = bc.business_id
                      INNER JOIN categories c ON bc.category_id = c.id";
        }

        $sql .= " WHERE b.status = 'active'";

        $params = [];

        if ($query) {
            $sql .= " AND (b.company_name LIKE ? OR b.description LIKE ?)";
            $searchTerm = "%$query%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Single category filter
        if ($category) {
            $sql .= " AND bc.category_id = ?";
            $params[] = $category;
        }

        // Multiple categories filter (comma-separated IDs)
        if (!empty($filters['categories'])) {
            $catIds = array_filter(array_map('intval', explode(',', $filters['categories'])));
            if (!empty($catIds)) {
                $placeholders = implode(',', array_fill(0, count($catIds), '?'));
                $sql .= " AND bc.category_id IN ($placeholders)";
                $params = array_merge($params, $catIds);
            }
        }

        // Group filter (e.g., 'haar', 'nagels', etc.)
        if (!empty($filters['group']) && isset($groupMapping[$filters['group']])) {
            $slugs = $groupMapping[$filters['group']];
            $placeholders = implode(',', array_fill(0, count($slugs), '?'));
            $sql .= " AND c.slug IN ($placeholders)";
            $params = array_merge($params, $slugs);
        }

        if ($location) {
            $sql .= " AND (b.city LIKE ? OR b.postal_code LIKE ?)";
            $locationTerm = "%$location%";
            $params[] = $locationTerm;
            $params[] = $locationTerm;
        }

        // Opening hours filters
        if (!empty($filters['open_now'])) {
            $currentDay = (int)date('w'); // 0=Sunday, 6=Saturday
            $currentTime = date('H:i:s');
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.day_of_week = ?
                AND bh.is_closed = 0
                AND bh.open_time <= ?
                AND bh.close_time >= ?
            )";
            $params[] = $currentDay;
            $params[] = $currentTime;
            $params[] = $currentTime;
        }

        if (!empty($filters['open_weekend'])) {
            // Saturday (6) or Sunday (0)
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.day_of_week IN (0, 6)
                AND bh.is_closed = 0
            )";
        }

        if (!empty($filters['open_evening'])) {
            // Evening = closes at 18:00 or later
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.is_closed = 0
                AND bh.close_time >= '18:00:00'
            )";
        }

        $sql .= " GROUP BY b.id";

        // Price filters (applied after GROUP BY via HAVING)
        $havingClauses = [];
        if (!empty($filters['price_min'])) {
            $havingClauses[] = "min_price >= ?";
            $params[] = $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $havingClauses[] = "min_price <= ?";
            $params[] = $filters['price_max'];
        }

        // High rated filter (4+ stars)
        if (!empty($filters['high_rated'])) {
            $havingClauses[] = "avg_rating >= 4";
        }

        if (!empty($havingClauses)) {
            $sql .= " HAVING " . implode(" AND ", $havingClauses);
        }

        $sql .= $this->getOrderByClause($sort);
        $sql .= " LIMIT 50";

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getFeaturedBusinesses(string $sort, array $filters = []): array
    {
        // Group to category slug mapping
        $groupMapping = [
            'haar' => ['hair', 'kapper', 'hairdresser', 'hairstylist', 'barber', 'barbershop', 'herenkapper', 'dameskapper', 'afro-hair', 'curly-hair', 'hair-colorist', 'bridal-hair'],
            'nagels' => ['nails', 'nail-salon', 'nagelstudio', 'manicure', 'pedicure', 'gel-nails', 'gelnagels', 'acrylic-nails', 'polygel', 'nail-art'],
            'huid' => ['beauty', 'beauty-salon', 'skincare', 'huidverzorging', 'facial', 'gezichtsbehandeling', 'acne-treatment', 'dermapen', 'microneedling', 'hydrafacial'],
            'lichaam' => ['massage', 'massage-therapist', 'deep-tissue', 'hot-stone', 'swedish-massage', 'thai-massage', 'sports-massage', 'body-treatments', 'body-contouring', 'aromatherapy', 'reflexology'],
            'ontharing' => ['waxing', 'waxsalon', 'brazilian-wax', 'laser-hair-removal', 'ipl-treatment', 'electrolysis', 'threading', 'sugaring'],
            'makeup' => ['makeup', 'makeup-artist', 'visagie', 'bridal-makeup', 'permanent-makeup', 'microblading', 'eyelash-extensions', 'wimperextensions', 'lash-lift', 'brow-lamination'],
            'wellness' => ['spa', 'day-spa', 'wellness', 'wellness-center', 'hammam', 'sauna', 'infrared-sauna', 'float-therapy'],
            'bruinen' => ['tanning-salon', 'sunbed', 'zonnestudio', 'spray-tan', 'self-tan'],
            'medisch' => ['botox', 'fillers', 'huidtherapeut', 'cosmetisch-arts', 'physiotherapy'],
            'alternatief' => ['acupuncture', 'acupunctuur', 'ayurveda', 'reiki', 'meditation', 'yoga-studio', 'pilates', 'holistic-therapy'],
        ];

        $sql = "SELECT b.*, b.company_name as name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.id) as review_count,
                       MIN(s.price) as min_price,
                       MAX(s.price) as max_price
                FROM businesses b
                LEFT JOIN reviews r ON b.id = r.business_id
                LEFT JOIN services s ON b.id = s.business_id AND s.is_active = 1";

        // Join categories if filtering by group
        if (!empty($filters['group']) || !empty($filters['categories'])) {
            $sql .= " INNER JOIN business_categories bc ON b.id = bc.business_id
                      INNER JOIN categories c ON bc.category_id = c.id";
        }

        $sql .= " WHERE b.status = 'active'";

        $params = [];

        // Multiple categories filter
        if (!empty($filters['categories'])) {
            $catIds = array_filter(array_map('intval', explode(',', $filters['categories'])));
            if (!empty($catIds)) {
                $placeholders = implode(',', array_fill(0, count($catIds), '?'));
                $sql .= " AND bc.category_id IN ($placeholders)";
                $params = array_merge($params, $catIds);
            }
        }

        // Group filter
        if (!empty($filters['group']) && isset($groupMapping[$filters['group']])) {
            $slugs = $groupMapping[$filters['group']];
            $placeholders = implode(',', array_fill(0, count($slugs), '?'));
            $sql .= " AND c.slug IN ($placeholders)";
            $params = array_merge($params, $slugs);
        }

        // Opening hours filters
        if (!empty($filters['open_now'])) {
            $currentDay = (int)date('w');
            $currentTime = date('H:i:s');
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.day_of_week = ?
                AND bh.is_closed = 0
                AND bh.open_time <= ?
                AND bh.close_time >= ?
            )";
            $params[] = $currentDay;
            $params[] = $currentTime;
            $params[] = $currentTime;
        }

        if (!empty($filters['open_weekend'])) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.day_of_week IN (0, 6)
                AND bh.is_closed = 0
            )";
        }

        if (!empty($filters['open_evening'])) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM business_hours bh
                WHERE bh.business_id = b.id
                AND bh.is_closed = 0
                AND bh.close_time >= '18:00:00'
            )";
        }

        $sql .= " GROUP BY b.id";

        // Price and rating filters via HAVING
        $havingClauses = [];
        if (!empty($filters['price_min'])) {
            $havingClauses[] = "min_price >= ?";
            $params[] = $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $havingClauses[] = "min_price <= ?";
            $params[] = $filters['price_max'];
        }
        if (!empty($filters['high_rated'])) {
            $havingClauses[] = "avg_rating >= 4";
        }

        if (!empty($havingClauses)) {
            $sql .= " HAVING " . implode(" AND ", $havingClauses);
        }

        $sql .= $this->getOrderByClause($sort);
        $sql .= " LIMIT 24";

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getOrderByClause(string $sort): string
    {
        switch ($sort) {
            case 'name':
                return " ORDER BY b.company_name ASC";
            case 'reviews':
                return " ORDER BY review_count DESC, avg_rating DESC";
            case 'price_asc':
                return " ORDER BY min_price ASC, avg_rating DESC";
            case 'price_desc':
                return " ORDER BY min_price DESC, avg_rating DESC";
            case 'newest':
                return " ORDER BY b.created_at DESC, avg_rating DESC";
            case 'rating':
            default:
                return " ORDER BY avg_rating DESC, review_count DESC";
        }
    }

    private function enrichBusinessData(array $businesses, ?float $userLat = null, ?float $userLng = null): array
    {
        if (empty($businesses)) {
            return [];
        }

        // Use GPS coordinates if available
        if (!$userLat || !$userLng) {
            $userLat = !empty($_GET['lat']) ? (float)$_GET['lat'] : null;
            $userLng = !empty($_GET['lng']) ? (float)$_GET['lng'] : null;
        }

        // If user entered a location text, try to geocode it
        $locationText = trim($_GET['location'] ?? '');
        if ((!$userLat || !$userLng) && $locationText) {
            $geocoded = $this->geocodeLocation($locationText);
            if ($geocoded) {
                $userLat = $geocoded['lat'];
                $userLng = $geocoded['lng'];
            }
        }

        // Fallback to IP-based location for distance calculation
        if (!$userLat || !$userLng) {
            $ipLocation = $this->getLocationFromIP();
            if ($ipLocation) {
                $userLat = $ipLocation['lat'];
                $userLng = $ipLocation['lng'];
            }
        }

        $businessIds = array_column($businesses, 'id');
        $placeholders = implode(',', array_fill(0, count($businessIds), '?'));

        // Get minimum prices
        $priceStmt = $this->db->query(
            "SELECT business_id, MIN(price) as min_price
             FROM services
             WHERE business_id IN ($placeholders) AND is_active = 1
             GROUP BY business_id",
            $businessIds
        );
        $prices = [];
        while ($row = $priceStmt->fetch(\PDO::FETCH_ASSOC)) {
            $prices[$row['business_id']] = $row['min_price'];
        }

        // Get services preview (first 4 service names)
        $servicesStmt = $this->db->query(
            "SELECT business_id, GROUP_CONCAT(name ORDER BY price ASC SEPARATOR ', ') as services
             FROM (
                 SELECT business_id, name, price,
                        ROW_NUMBER() OVER (PARTITION BY business_id ORDER BY price ASC) as rn
                 FROM services
                 WHERE business_id IN ($placeholders) AND is_active = 1
             ) sub
             WHERE rn <= 4
             GROUP BY business_id",
            $businessIds
        );
        $servicesMap = [];
        while ($row = $servicesStmt->fetch(\PDO::FETCH_ASSOC)) {
            $servicesMap[$row['business_id']] = $row['services'];
        }

        // Get primary category for each business
        $lang = $this->lang ?? 'nl';
        $categoryStmt = $this->db->query(
            "SELECT bc.business_id, COALESCE(ct.name, c.slug) as category_name
             FROM business_categories bc
             INNER JOIN categories c ON bc.category_id = c.id
             LEFT JOIN category_translations ct ON c.id = ct.category_id AND ct.language = ?
             WHERE bc.business_id IN ($placeholders)
             GROUP BY bc.business_id",
            array_merge([$lang], $businessIds)
        );
        $categoriesMap = [];
        while ($row = $categoryStmt->fetch(\PDO::FETCH_ASSOC)) {
            $categoriesMap[$row['business_id']] = $row['category_name'];
        }

        // Enrich each business
        foreach ($businesses as &$biz) {
            $biz['min_price'] = $prices[$biz['id']] ?? null;
            $biz['services_preview'] = $servicesMap[$biz['id']] ?? '';
            $biz['category_name'] = $categoriesMap[$biz['id']] ?? '';

            // Calculate distance if user location is available and business has coordinates
            $biz['distance'] = null;
            if ($userLat && $userLng && !empty($biz['latitude']) && !empty($biz['longitude'])) {
                $biz['distance'] = $this->calculateDistance(
                    (float)$userLat,
                    (float)$userLng,
                    (float)$biz['latitude'],
                    (float)$biz['longitude']
                );
            }
        }

        // Sort by distance if requested
        $sort = $_GET['sort'] ?? 'rating';
        if ($sort === 'distance' && $userLat && $userLng) {
            usort($businesses, function($a, $b) {
                // Put businesses without distance at the end
                if ($a['distance'] === null && $b['distance'] === null) return 0;
                if ($a['distance'] === null) return 1;
                if ($b['distance'] === null) return -1;
                return $a['distance'] <=> $b['distance'];
            });
        }

        return $businesses;
    }

    /**
     * Return compact JSON with salon map data for Leaflet markers
     */
    public function mapData(): string
    {
        $country = trim($_GET['country'] ?? '');

        // Map country codes to full names (as stored in database)
        $countryCodeToName = [
            'NL' => 'Nederland',
            'BE' => 'België',
            'DE' => 'Duitsland',
            'FR' => 'Frankrijk',
            'GB' => 'United Kingdom',
            'ES' => 'Spanje',
            'IT' => 'Italië',
            'PT' => 'Portugal',
            'AT' => 'Oostenrijk',
            'CH' => 'Zwitserland',
        ];

        $sql = "SELECT b.id, b.company_name as name, b.slug, b.city, b.latitude as lat, b.longitude as lng, b.country,
                       COALESCE(AVG(r.rating), 0) as rating,
                       COUNT(DISTINCT r.id) as reviews
                FROM businesses b
                LEFT JOIN reviews r ON b.id = r.business_id
                WHERE b.status = 'active'
                  AND b.latitude IS NOT NULL
                  AND b.longitude IS NOT NULL
                  AND b.latitude != 0
                  AND b.longitude != 0";

        $params = [];

        if ($country) {
            // Convert country code to full name if needed
            $countryUpper = strtoupper($country);
            $countryName = $countryCodeToName[$countryUpper] ?? $country;
            $sql .= " AND (UPPER(b.country) = ? OR UPPER(b.country) = ?)";
            $params[] = $countryUpper;
            $params[] = strtoupper($countryName);
        }

        $sql .= " GROUP BY b.id ORDER BY rating DESC LIMIT 500";

        $stmt = $this->db->query($sql, $params);
        $salons = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Cast numeric fields
        foreach ($salons as &$s) {
            $s['lat'] = (float)$s['lat'];
            $s['lng'] = (float)$s['lng'];
            $s['rating'] = round((float)$s['rating'], 1);
            $s['reviews'] = (int)$s['reviews'];
        }

        header('Content-Type: application/json');
        header('Cache-Control: public, max-age=300');
        return json_encode($salons);
    }

    private function getCategories(): array
    {
        $stmt = $this->db->query(
            "SELECT c.*, ct.name as translated_name
             FROM categories c
             LEFT JOIN category_translations ct ON c.id = ct.category_id AND ct.language = ?
             WHERE c.is_active = 1
             ORDER BY c.sort_order",
            [$this->lang]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

<?php
/**
 * Test Admin Payouts Page Queries
 */

define('BASE_PATH', dirname(__DIR__));

// Load environment
$envFile = BASE_PATH . '/.env';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos($line, '#') === 0) continue;
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// Create DB connection
$db = new PDO(
    'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8mb4',
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD')
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           ADMIN PAYOUTS PAGE TEST                            ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// 1. Pending business payouts
echo "1️⃣  Openstaande bedrijfsuitbetalingen\n";
echo str_repeat('-', 60) . "\n";

$stmt = $db->query(
    "SELECT
        b.id as booking_id,
        b.booking_number,
        b.service_price,
        b.platform_fee,
        b.business_payout,
        b.payout_status,
        b.checked_in_at,
        bus.id as business_id,
        bus.company_name,
        bus.iban,
        s.name as service_name
     FROM bookings b
     JOIN businesses bus ON b.business_id = bus.id
     JOIN services s ON b.service_id = s.id
     WHERE b.payment_status = 'paid'
       AND b.payout_status IN ('pending', 'processing')
       AND (bus.mollie_account_id IS NULL OR bus.mollie_account_id = '')
     ORDER BY bus.company_name, b.created_at"
);
$pendingBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by business
$businessPayouts = [];
foreach ($pendingBookings as $booking) {
    $bizId = $booking['business_id'];
    if (!isset($businessPayouts[$bizId])) {
        $businessPayouts[$bizId] = [
            'company_name' => $booking['company_name'],
            'iban' => $booking['iban'],
            'bookings' => [],
            'total' => 0
        ];
    }
    $amount = $booking['business_payout'] ?? ($booking['service_price'] - ($booking['platform_fee'] ?? 1.75));
    $businessPayouts[$bizId]['bookings'][] = $booking;
    $businessPayouts[$bizId]['total'] += $amount;
}

if (empty($businessPayouts)) {
    echo "   ✅ Geen openstaande bedrijfsuitbetalingen\n";
} else {
    $totalBusiness = 0;
    foreach ($businessPayouts as $biz) {
        echo "   🏢 {$biz['company_name']}\n";
        echo "      IBAN: " . ($biz['iban'] ?: 'Geen IBAN!') . "\n";
        echo "      Boekingen: " . count($biz['bookings']) . "\n";
        echo "      Bedrag: €" . number_format($biz['total'], 2, ',', '.') . "\n\n";
        $totalBusiness += $biz['total'];
    }
    echo "   📊 TOTAAL: €" . number_format($totalBusiness, 2, ',', '.') . " (" . count($businessPayouts) . " bedrijven)\n";
}

echo "\n";

// 2. Sales partner payouts
echo "2️⃣  Openstaande sales commissies\n";
echo str_repeat('-', 60) . "\n";

$stmt = $db->query(
    "SELECT
        su.id as sales_user_id,
        su.name as sales_name,
        su.email as sales_email,
        su.iban as sales_iban,
        COUNT(sr.id) as referral_count,
        SUM(sr.commission) as total_commission
     FROM sales_users su
     JOIN sales_referrals sr ON sr.sales_user_id = su.id
     WHERE su.status = 'active'
       AND sr.status = 'converted'
       AND su.iban IS NOT NULL AND su.iban != ''
     GROUP BY su.id
     HAVING total_commission >= 49.99"
);
$salesPayouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($salesPayouts)) {
    echo "   ✅ Geen openstaande sales commissies (min. €49,99)\n";
} else {
    $totalSales = 0;
    foreach ($salesPayouts as $partner) {
        echo "   👤 {$partner['sales_name']}\n";
        echo "      IBAN: {$partner['sales_iban']}\n";
        echo "      Referrals: {$partner['referral_count']}\n";
        echo "      Commissie: €" . number_format($partner['total_commission'], 2, ',', '.') . "\n\n";
        $totalSales += $partner['total_commission'];
    }
    echo "   📊 TOTAAL: €" . number_format($totalSales, 2, ',', '.') . " (" . count($salesPayouts) . " partners)\n";
}

echo "\n";

// 3. Failed/pending payout records
echo "3️⃣  Uitbetalingsrecords (pending/failed)\n";
echo str_repeat('-', 60) . "\n";

$stmt = $db->query(
    "SELECT bp.id, bp.amount, bp.status, bp.notes, bp.created_at, bus.company_name
     FROM business_payouts bp
     JOIN businesses bus ON bp.business_id = bus.id
     WHERE bp.status IN ('pending', 'failed', 'processing')
     ORDER BY bp.created_at DESC
     LIMIT 10"
);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($records)) {
    echo "   ✅ Geen openstaande uitbetalingsrecords\n";
} else {
    foreach ($records as $r) {
        $statusIcon = $r['status'] === 'failed' ? '❌' : ($r['status'] === 'pending' ? '⏳' : '🔄');
        echo "   $statusIcon #{$r['id']} {$r['company_name']}: €" . number_format($r['amount'], 2, ',', '.') . " ({$r['status']})\n";
        if ($r['notes']) {
            echo "      Note: {$r['notes']}\n";
        }
    }
}

echo "\n";

// 4. Wise balance
echo "4️⃣  Wise saldo\n";
echo str_repeat('-', 60) . "\n";

require_once BASE_PATH . '/vendor/autoload.php';
$wise = new \GlamourSchedule\Services\WiseService();

if ($wise->isConfigured()) {
    $balance = $wise->getEurBalance();
    if ($balance !== null) {
        echo "   💰 EUR Saldo: €" . number_format($balance, 2, ',', '.') . "\n";
    } else {
        echo "   ⚠️  Kon Wise saldo niet ophalen\n";
    }
} else {
    echo "   ⚠️  Wise niet geconfigureerd\n";
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  Bekijk volledige pagina: /admin/payouts                     ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";

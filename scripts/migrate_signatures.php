<?php
/**
 * Migration Script: Generate signing keys and sign existing bookings
 *
 * This script:
 * 1. Generates HMAC-SHA256 signing keys for all businesses without one
 * 2. Signs all existing bookings with their business's signing key
 *
 * Run this AFTER applying the database migration:
 * mysql glamourschedule_db < database/migrations/2026_02_03_add_crypto_signing.sql
 *
 * Usage:
 *   php scripts/migrate_signatures.php
 *   php scripts/migrate_signatures.php --dry-run  (preview without changes)
 */

// Define constants for the application
define('GLAMOUR_LOADED', true);
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

// Load environment using dotenv
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

use GlamourSchedule\Core\Database;
use GlamourSchedule\Services\BookingSignatureService;

// Parse arguments
$dryRun = in_array('--dry-run', $argv);

echo "=== Booking Signature Migration ===\n\n";

if ($dryRun) {
    echo "** DRY RUN MODE - No changes will be made **\n\n";
}

try {
    $db = Database::getInstance()->getConnection();
    $signatureService = new BookingSignatureService();

    // Step 1: Generate signing keys for businesses without one
    echo "Step 1: Generating signing keys for businesses...\n";

    $stmt = $db->query("SELECT id, company_name FROM businesses WHERE signing_key IS NULL");
    $businessesWithoutKey = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $keysGenerated = 0;
    foreach ($businessesWithoutKey as $business) {
        if ($dryRun) {
            echo "  [DRY] Would generate key for: {$business['company_name']} (ID: {$business['id']})\n";
        } else {
            $key = $signatureService->generateSigningKey();
            $stmt = $db->prepare("UPDATE businesses SET signing_key = ?, signing_key_created_at = NOW() WHERE id = ?");
            $stmt->execute([$key, $business['id']]);
            echo "  Generated key for: {$business['company_name']} (ID: {$business['id']})\n";
        }
        $keysGenerated++;
    }

    echo "  Keys generated: {$keysGenerated}\n\n";

    // Step 2: Sign existing bookings without signatures
    echo "Step 2: Signing existing bookings...\n";

    $stmt = $db->query(
        "SELECT b.id, b.uuid, b.booking_number, b.business_id, b.appointment_date, b.appointment_time
         FROM bookings b
         WHERE b.signature IS NULL
         ORDER BY b.id ASC"
    );
    $unsignedBookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $totalBookings = count($unsignedBookings);
    $signedCount = 0;
    $failedCount = 0;
    $currentBusiness = null;
    $batchSize = 100;

    echo "  Found {$totalBookings} bookings to sign\n";

    foreach ($unsignedBookings as $index => $booking) {
        // Show progress every 100 bookings
        if ($index > 0 && $index % $batchSize === 0) {
            $percent = round(($index / $totalBookings) * 100);
            echo "  Progress: {$index}/{$totalBookings} ({$percent}%)\n";
        }

        if ($dryRun) {
            $signedCount++;
            continue;
        }

        $signatureData = $signatureService->signBooking(
            (int)$booking['business_id'],
            $booking['uuid'],
            $booking['booking_number'],
            $booking['appointment_date'],
            $booking['appointment_time']
        );

        if ($signatureData) {
            $stmt = $db->prepare(
                "UPDATE bookings SET signature = ?, signature_version = ? WHERE id = ?"
            );
            $stmt->execute([
                $signatureData['signature'],
                $signatureData['version'],
                $booking['id']
            ]);
            $signedCount++;
        } else {
            echo "  WARNING: Failed to sign booking {$booking['booking_number']}\n";
            $failedCount++;
        }
    }

    echo "\n  Bookings signed: {$signedCount}\n";
    if ($failedCount > 0) {
        echo "  Bookings failed: {$failedCount}\n";
    }

    // Step 3: Verify migration
    echo "\nStep 3: Verification...\n";

    $stmt = $db->query("SELECT COUNT(*) as cnt FROM businesses WHERE signing_key IS NOT NULL");
    $businessesWithKey = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

    $stmt = $db->query("SELECT COUNT(*) as cnt FROM businesses");
    $totalBusinesses = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

    $stmt = $db->query("SELECT COUNT(*) as cnt FROM bookings WHERE signature IS NOT NULL");
    $signedBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

    $stmt = $db->query("SELECT COUNT(*) as cnt FROM bookings");
    $allBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

    echo "  Businesses with signing key: {$businessesWithKey}/{$totalBusinesses}\n";
    echo "  Bookings with signature: {$signedBookings}/{$allBookings}\n";

    // Step 4: Test signature verification
    echo "\nStep 4: Testing signature verification...\n";

    if (!$dryRun) {
        $stmt = $db->query(
            "SELECT b.*, biz.signing_key
             FROM bookings b
             JOIN businesses biz ON b.business_id = biz.id
             WHERE b.signature IS NOT NULL
             LIMIT 5"
        );
        $testBookings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $verified = 0;
        foreach ($testBookings as $booking) {
            if ($signatureService->verifyBooking((int)$booking['business_id'], $booking)) {
                $verified++;
            } else {
                echo "  FAILED: Booking {$booking['booking_number']} verification failed!\n";
            }
        }
        echo "  Verified {$verified}/" . count($testBookings) . " test bookings successfully\n";
    } else {
        echo "  [DRY] Skipping verification test\n";
    }

    echo "\n=== Migration Complete ===\n";

    if ($dryRun) {
        echo "\nRun without --dry-run to apply changes.\n";
    }

} catch (\Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

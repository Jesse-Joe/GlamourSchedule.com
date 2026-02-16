<?php
/**
 * Backfill Script: Generate account codes for existing users
 *
 * This script:
 * 1. Generates GS-XXXXXX account codes for all users without one
 * 2. Links pos_customers to users based on email match
 *
 * Run this AFTER applying the database migration:
 * mysql glamourschedule_db < database/migrations/2026_02_16_customer_account_codes.sql
 *
 * Usage:
 *   php scripts/backfill_account_codes.php
 *   php scripts/backfill_account_codes.php --dry-run  (preview without changes)
 */

// Define constants for the application
define('GLAMOUR_LOADED', true);
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

// Load environment using dotenv
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

use GlamourSchedule\Core\Database;

// Parse arguments
$dryRun = in_array('--dry-run', $argv ?? []);

if ($dryRun) {
    echo "=== DRY RUN MODE - No changes will be made ===\n\n";
}

// Connect to database
$db = Database::getInstance();

// Character set for account codes (no ambiguous characters: 0, 1, O, I, L)
$chars = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
$charsLen = strlen($chars);

/**
 * Generate a unique account code
 */
function generateAccountCode(\PDO $db, string $chars, int $charsLen): string
{
    for ($attempt = 0; $attempt < 20; $attempt++) {
        $code = 'GS-';
        for ($i = 0; $i < 6; $i++) {
            $code .= $chars[random_int(0, $charsLen - 1)];
        }

        // Check uniqueness
        $stmt = $db->prepare("SELECT id FROM users WHERE account_code = ?");
        $stmt->execute([$code]);
        if (!$stmt->fetch()) {
            return $code;
        }
    }

    throw new RuntimeException('Could not generate unique account code after 20 attempts');
}

// Step 1: Generate account codes for users without one
echo "Step 1: Generating account codes for users...\n";

$stmt = $db->query("SELECT id, email, first_name, last_name FROM users WHERE account_code IS NULL ORDER BY id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($users) . " users without account code.\n";

$codesGenerated = 0;
foreach ($users as $user) {
    $code = generateAccountCode($db, $chars, $charsLen);

    if (!$dryRun) {
        $stmt = $db->prepare("UPDATE users SET account_code = ? WHERE id = ? AND account_code IS NULL");
        $stmt->execute([$code, $user['id']]);
    }

    $codesGenerated++;
    echo "  User #{$user['id']} ({$user['first_name']} {$user['last_name']}): {$code}\n";

    // Progress every 100
    if ($codesGenerated % 100 === 0) {
        echo "  ... processed {$codesGenerated} users\n";
    }
}

echo "Generated {$codesGenerated} account codes.\n\n";

// Step 2: Link pos_customers to users based on email match
echo "Step 2: Linking pos_customers to users...\n";

$stmt = $db->query(
    "SELECT pc.id, pc.email, pc.business_id, u.id as user_id, u.account_code
     FROM pos_customers pc
     INNER JOIN users u ON pc.email = u.email AND u.status = 'active'
     WHERE pc.user_id IS NULL AND pc.email IS NOT NULL AND pc.email != ''"
);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($matches) . " pos_customers to link.\n";

$linked = 0;
foreach ($matches as $match) {
    if (!$dryRun) {
        $stmt = $db->prepare("UPDATE pos_customers SET user_id = ?, account_code = ? WHERE id = ?");
        $stmt->execute([$match['user_id'], $match['account_code'], $match['id']]);
    }

    $linked++;
    echo "  pos_customer #{$match['id']} (business {$match['business_id']}) -> user #{$match['user_id']} ({$match['account_code']})\n";
}

echo "Linked {$linked} pos_customers.\n\n";

echo "=== Done! ===\n";
if ($dryRun) {
    echo "This was a dry run. Run without --dry-run to apply changes.\n";
}

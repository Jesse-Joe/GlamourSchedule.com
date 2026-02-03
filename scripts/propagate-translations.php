<?php
/**
 * Propagate new translation keys to all language files
 * Uses English as the base/fallback
 */

$basePath = dirname(__DIR__) . '/resources/lang';

// New keys to propagate (from English file)
$newKeys = [
    // TRIAL & SUBSCRIPTION EMAILS
    'email_trial_expiry_subject' => 'Your GlamourSchedule trial ends today',
    'email_trial_greeting' => 'Hello',
    'email_trial_ends' => 'Your 14-day trial for <strong>{company}</strong> ends today.',
    'email_price_label_early' => 'Early Bird registration fee',
    'email_price_label_normal' => 'Monthly subscription',
    'email_price_subtext_early' => 'one-time',
    'email_price_subtext_normal' => 'per month',
    'email_activate_text_early' => 'To continue using GlamourSchedule, please complete your Early Bird registration.',
    'email_activate_text_normal' => 'To continue using GlamourSchedule, please activate your monthly subscription.',
    'email_trial_warning' => 'Note: If you don\'t activate within 3 days, your account will be deactivated.',
    'email_activate_button' => 'Activate Subscription',
    'email_questions_contact' => 'Questions? Contact us at info@glamourschedule.nl',
    'email_copyright' => '&copy; {year} GlamourSchedule. All rights reserved.',

    // Deactivation email
    'email_deactivation_subject' => 'Your GlamourSchedule account has been deactivated',
    'email_deactivated_text' => 'Your account for <strong>{company}</strong> has been deactivated because the trial period expired without subscription activation.',
    'email_reactivate_info' => 'You can reactivate your account at any time by logging in and activating your subscription.',
    'email_reactivate_button' => 'Reactivate Account',

    // PAYOUT EMAILS
    'email_payout_completed' => 'Payout Completed',
    'email_payout_completed_subject' => 'Payout completed - {amount}',
    'email_payout_great_news' => 'Great news! Your payout has been automatically processed via Mollie Connect.',
    'email_payout_bookings_count' => '{count} booking(s) - Payout #{id}',
    'email_payout_booking' => 'Booking',
    'email_payout_service' => 'Service',
    'email_payout_amount' => 'Amount',
    'email_payout_fee' => 'Fee',
    'email_payout_payout' => 'Payout',
    'email_payout_total' => 'Total',
    'email_payout_details' => 'Payout details:',
    'email_payout_transferred' => 'The amount has been transferred to your linked Mollie account and will be automatically forwarded to your bank account.',
    'email_payout_view_all' => 'View all your payouts in your',
    'email_payout_dashboard' => 'dashboard',

    // Weekly payout
    'email_weekly_payout' => 'Weekly Payout',
    'email_weekly_payout_processing' => 'Weekly payout in processing - {amount}',
    'email_payout_automatic_via' => '{count} booking(s) - Automatic via {method}',
    'email_payout_auto_transferred' => 'The amount has been automatically transferred to your bank account. You will receive it within {time}.',
    'email_payout_expected_time' => 'Expected processing time: 1-3 business days',
    'email_payout_bank_account' => 'Bank account:',
    'email_payout_tip' => 'Tip: Connect your Mollie account to get paid automatically within 24 hours after each booking!',
    'email_payout_connect_now' => 'Connect now',
    'email_payout_1_business_day' => '1 business day',
    'email_payout_1_2_business_days' => '1-2 business days',
    'email_payout_1_3_business_days' => '1-3 business days',

    // CONTACT FORM & SUPPORT EMAILS
    'contact_type_bug' => 'Bug / Error',
    'contact_type_request' => 'Request / Feature',
    'contact_type_problem' => 'Problem / Help',
    'contact_type_other' => 'Other',
    'contact_name_required' => 'Name is required',
    'contact_email_required' => 'Email is required',
    'contact_subject_required' => 'Subject is required',
    'contact_message_required' => 'Message is required',
    'contact_spam_detected' => 'Your message was detected as spam',
    'contact_success' => 'Thank you for your message! Your ticket number is: {ticket}. We will contact you as soon as possible.',
    'email_contact_thanks' => 'Thank you for your message!',
    'email_contact_dear' => 'Dear {name},',
    'email_contact_received' => 'Thank you for contacting GlamourSchedule. We have received your message and will respond as soon as possible.',
    'email_contact_ticket_number' => 'Your ticket number:',
    'email_contact_save_ticket' => 'Save this ticket number for your records. You can use this number when contacting us about this matter.',
    'email_contact_type' => 'Type:',
    'email_contact_subject' => 'Subject:',
    'email_contact_your_message' => 'Your message:',
    'email_contact_regards' => 'Kind regards,',
    'email_contact_team' => 'The GlamourSchedule Team',
    'email_contact_confirmation_subject' => 'Confirmation: Your message has been received [{ticket}]',

    // BUSINESS REGISTRATION EMAILS
    'email_verify_subject' => 'Confirm your GlamourSchedule account - Code: {code}',
    'email_verify_title' => 'Confirm your account',
    'email_verify_dear' => 'Dear',
    'email_verify_use_code' => 'Use the code below to confirm your account:',
    'email_verify_valid_10min' => 'This code is valid for 10 minutes.',
    'email_verify_not_requested' => 'Did you not request this code? Ignore this email.',
    'email_completion_subject' => 'Complete your registration - GlamourSchedule',
    'email_completion_welcome' => 'Welcome to GlamourSchedule!',
    'email_completion_almost_done' => 'Welcome! Your registration for <strong>{company}</strong> is almost complete.',
    'email_completion_trial_starts' => 'Your 14-day trial period starts now!',
    'email_completion_trial_info' => 'Try GlamourSchedule free for 14 days. You only need to pay after the trial period.',
    'email_completion_complete_now' => 'Complete your registration now:',
    'email_completion_click_button' => 'Click the button below to set your password and add your business details.',
    'email_completion_button' => 'Complete Registration',
    'email_completion_copy_link' => 'Or copy this link:',
    'email_completion_what_you_need' => 'What you need:',
    'email_completion_need_password' => '- A new password',
    'email_completion_need_address' => '- Your business address',
    'email_completion_need_phone' => '- Your phone number (optional)',
    'email_completion_need_kvk' => '- Your registration number (optional)',
    'email_welcome_title' => 'Welcome!',
    'email_welcome_trial_started' => 'Your 14-day free trial has started.',
    'email_welcome_explore' => 'Explore all features of GlamourSchedule and start receiving online bookings.',
    'email_welcome_dashboard_button' => 'Go to Dashboard',
];

// Get all language directories
$langs = array_filter(scandir($basePath), function($dir) use ($basePath) {
    return $dir !== '.' && $dir !== '..' && is_dir($basePath . '/' . $dir);
});

$updated = 0;
$skipped = 0;

foreach ($langs as $lang) {
    // Skip en and nl as they already have translations
    if ($lang === 'en' || $lang === 'nl') {
        echo "Skipping {$lang} (already has translations)\n";
        $skipped++;
        continue;
    }

    $filePath = $basePath . '/' . $lang . '/messages.php';

    if (!file_exists($filePath)) {
        echo "Warning: {$filePath} does not exist\n";
        continue;
    }

    // Read existing translations
    $existing = require $filePath;

    // Check which keys are missing
    $missingKeys = [];
    foreach ($newKeys as $key => $value) {
        if (!isset($existing[$key])) {
            $missingKeys[$key] = $value;
        }
    }

    if (empty($missingKeys)) {
        echo "Skipping {$lang} (all keys present)\n";
        $skipped++;
        continue;
    }

    // Read file content
    $content = file_get_contents($filePath);

    // Find the position of the closing ];
    $lastBracket = strrpos($content, '];');

    if ($lastBracket === false) {
        echo "Error: Could not find closing bracket in {$lang}\n";
        continue;
    }

    // Build the new keys section
    $newSection = "\n    // ═══════════════════════════════════════════════════════════════════════\n";
    $newSection .= "    // EMAIL TRANSLATIONS (English fallback - needs translation)\n";
    $newSection .= "    // ═══════════════════════════════════════════════════════════════════════\n";

    foreach ($missingKeys as $key => $value) {
        // Escape single quotes in value
        $escapedValue = str_replace("'", "\\'", $value);
        $newSection .= "    '{$key}' => '{$escapedValue}',\n";
    }

    // Insert new keys before the closing bracket
    $newContent = substr($content, 0, $lastBracket) . $newSection . substr($content, $lastBracket);

    // Write back
    file_put_contents($filePath, $newContent);

    echo "Updated {$lang} with " . count($missingKeys) . " new keys\n";
    $updated++;
}

echo "\nDone! Updated: {$updated}, Skipped: {$skipped}\n";

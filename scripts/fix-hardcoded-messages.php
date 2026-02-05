<?php
/**
 * Fix hardcoded Dutch messages in controllers
 * Replace with translation function calls
 */

$basePath = dirname(__DIR__) . '/src/Controllers';

// Common replacements (pattern => replacement)
$replacements = [
    // Error messages
    "'Ongeldige aanvraag. Probeer het opnieuw.'" => "\$this->t('error_invalid_request')",
    "'Ongeldige aanvraag. Probeer opnieuw.'" => "\$this->t('error_invalid_request')",
    "'Ongeldige aanvraag'" => "\$this->t('error_invalid_request')",
    "'Er ging iets mis. Probeer het opnieuw.'" => "\$this->t('error_generic')",
    "'Er ging iets mis bij het opslaan. Probeer het opnieuw.'" => "\$this->t('error_save_failed')",
    "'Er ging iets mis bij het registreren'" => "\$this->t('error_registration_failed')",
    "'Er ging iets mis met de betaling. Probeer het opnieuw.'" => "\$this->t('error_payment_failed')",
    "'Er ging iets mis'" => "\$this->t('error_generic')",
    "'Er is een fout opgetreden bij de registratie. Probeer het opnieuw.'" => "\$this->t('error_registration_failed')",
    "'Er is een fout opgetreden. Probeer het opnieuw.'" => "\$this->t('error_generic')",
    "'Er is een fout opgetreden bij het uploaden.'" => "\$this->t('error_upload_failed')",
    "'Er is een fout opgetreden bij het verwerken van de banner.'" => "\$this->t('error_upload_failed')",
    "'Betaling kon niet worden gestart. Probeer het opnieuw.'" => "\$this->t('error_payment_start_failed')",
    "'Betaling niet gelukt. Probeer het opnieuw.'" => "\$this->t('error_payment_failed')",
    "'Sessie verlopen. Log opnieuw in.'" => "\$this->t('error_session_expired')",

    // Validation messages
    "'Voornaam is verplicht.'" => "\$this->t('validation_first_name_required')",
    "'Voornaam is verplicht'" => "\$this->t('validation_first_name_required')",
    "'Achternaam is verplicht'" => "\$this->t('validation_last_name_required')",
    "'Geldig e-mailadres is verplicht'" => "\$this->t('validation_email_required')",
    "'Dit e-mailadres is al in gebruik'" => "\$this->t('error_already_exists')",

    // Success messages
    "'Succesvol opgeslagen!'" => "\$this->t('success_saved')",
    "'Succesvol verwijderd!'" => "\$this->t('success_deleted')",
];

// Files to process
$files = glob($basePath . '/*.php');

$totalReplacements = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;
    $fileReplacements = 0;

    foreach ($replacements as $search => $replace) {
        $count = 0;
        $content = str_replace($search, $replace, $content, $count);
        $fileReplacements += $count;
    }

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "Updated " . basename($file) . " with {$fileReplacements} replacements\n";
        $totalReplacements += $fileReplacements;
    }
}

echo "\nTotal replacements: {$totalReplacements}\n";

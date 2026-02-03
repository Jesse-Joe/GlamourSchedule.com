<?php
namespace GlamourSchedule\Services;

/**
 * Business Registration Service
 * Handles country-specific business registration numbers and tax IDs
 */
class BusinessRegistrationService
{
    /**
     * Get business registration info per country
     */
    public static function getCountryConfig(string $countryCode): array
    {
        $configs = [
            // Netherlands
            'NL' => [
                'registration' => [
                    'type' => 'KVK',
                    'label' => 'KVK-nummer',
                    'label_en' => 'Chamber of Commerce Number',
                    'pattern' => '^\d{8}$',
                    'placeholder' => '12345678',
                    'maxlength' => 8,
                    'help' => 'Je 8-cijferig KVK-nummer',
                    'verification_url' => 'https://www.kvk.nl/zoeken/'
                ],
                'tax' => [
                    'type' => 'BTW',
                    'label' => 'BTW-nummer',
                    'label_en' => 'VAT Number',
                    'pattern' => '^NL\d{9}B\d{2}$',
                    'placeholder' => 'NL123456789B01',
                    'maxlength' => 14,
                    'help' => 'Format: NL + 9 cijfers + B + 2 cijfers'
                ]
            ],

            // Belgium
            'BE' => [
                'registration' => [
                    'type' => 'KBO',
                    'label' => 'Ondernemingsnummer (KBO)',
                    'label_en' => 'Enterprise Number (CBE)',
                    'pattern' => '^0?\d{9,10}$',
                    'placeholder' => '0123456789',
                    'maxlength' => 10,
                    'help' => 'Je 10-cijferig ondernemingsnummer',
                    'verification_url' => 'https://kbopub.economie.fgov.be/'
                ],
                'tax' => [
                    'type' => 'BTW',
                    'label' => 'BTW-nummer',
                    'label_en' => 'VAT Number',
                    'pattern' => '^BE0?\d{9,10}$',
                    'placeholder' => 'BE0123456789',
                    'maxlength' => 12,
                    'help' => 'Format: BE + ondernemingsnummer'
                ]
            ],

            // Germany
            'DE' => [
                'registration' => [
                    'type' => 'HRB',
                    'label' => 'Handelsregisternummer',
                    'label_en' => 'Commercial Register Number',
                    'pattern' => '^(HRA|HRB)?\s?\d+.*$',
                    'placeholder' => 'HRB 12345',
                    'maxlength' => 20,
                    'help' => 'Ihr Handelsregisternummer (z.B. HRB 12345)',
                    'verification_url' => 'https://www.handelsregister.de/'
                ],
                'tax' => [
                    'type' => 'USt-IdNr',
                    'label' => 'Umsatzsteuer-ID',
                    'label_en' => 'VAT ID',
                    'pattern' => '^DE\d{9}$',
                    'placeholder' => 'DE123456789',
                    'maxlength' => 11,
                    'help' => 'Format: DE + 9 Ziffern'
                ]
            ],

            // France
            'FR' => [
                'registration' => [
                    'type' => 'SIRET',
                    'label' => 'Numéro SIRET',
                    'label_en' => 'SIRET Number',
                    'pattern' => '^\d{14}$',
                    'placeholder' => '12345678901234',
                    'maxlength' => 14,
                    'help' => 'Votre numéro SIRET à 14 chiffres',
                    'verification_url' => 'https://www.societe.com/'
                ],
                'tax' => [
                    'type' => 'TVA',
                    'label' => 'Numéro de TVA',
                    'label_en' => 'VAT Number',
                    'pattern' => '^FR\d{2}\d{9}$',
                    'placeholder' => 'FR12345678901',
                    'maxlength' => 13,
                    'help' => 'Format: FR + 2 chiffres + SIREN'
                ]
            ],

            // United Kingdom
            'GB' => [
                'registration' => [
                    'type' => 'CRN',
                    'label' => 'Company Registration Number',
                    'label_en' => 'Company Registration Number',
                    'pattern' => '^[A-Z]{0,2}\d{6,8}$',
                    'placeholder' => '12345678',
                    'maxlength' => 10,
                    'help' => 'Your Companies House registration number',
                    'verification_url' => 'https://find-and-update.company-information.service.gov.uk/'
                ],
                'tax' => [
                    'type' => 'VAT',
                    'label' => 'VAT Registration Number',
                    'label_en' => 'VAT Registration Number',
                    'pattern' => '^GB\d{9}(\d{3})?$',
                    'placeholder' => 'GB123456789',
                    'maxlength' => 14,
                    'help' => 'Format: GB + 9 or 12 digits'
                ]
            ],

            // Spain
            'ES' => [
                'registration' => [
                    'type' => 'NIF',
                    'label' => 'NIF/CIF',
                    'label_en' => 'Tax Identification Number',
                    'pattern' => '^[A-Z]\d{7}[A-Z0-9]$',
                    'placeholder' => 'B12345678',
                    'maxlength' => 9,
                    'help' => 'Tu Número de Identificación Fiscal',
                    'verification_url' => 'https://sede.agenciatributaria.gob.es/'
                ],
                'tax' => [
                    'type' => 'IVA',
                    'label' => 'Número de IVA',
                    'label_en' => 'VAT Number',
                    'pattern' => '^ES[A-Z]\d{7}[A-Z0-9]$',
                    'placeholder' => 'ESB12345678',
                    'maxlength' => 11,
                    'help' => 'Format: ES + NIF'
                ]
            ],

            // Italy
            'IT' => [
                'registration' => [
                    'type' => 'REA',
                    'label' => 'Numero REA',
                    'label_en' => 'REA Number',
                    'pattern' => '^[A-Z]{2}\d+$',
                    'placeholder' => 'MI-1234567',
                    'maxlength' => 15,
                    'help' => 'Il tuo numero REA (es. MI-1234567)',
                    'verification_url' => 'https://www.registroimprese.it/'
                ],
                'tax' => [
                    'type' => 'P.IVA',
                    'label' => 'Partita IVA',
                    'label_en' => 'VAT Number',
                    'pattern' => '^IT\d{11}$',
                    'placeholder' => 'IT12345678901',
                    'maxlength' => 13,
                    'help' => 'Format: IT + 11 cifre'
                ]
            ],

            // Portugal
            'PT' => [
                'registration' => [
                    'type' => 'NIPC',
                    'label' => 'NIPC',
                    'label_en' => 'Corporate Tax ID',
                    'pattern' => '^\d{9}$',
                    'placeholder' => '123456789',
                    'maxlength' => 9,
                    'help' => 'O seu NIPC de 9 dígitos',
                    'verification_url' => 'https://www.racius.com/'
                ],
                'tax' => [
                    'type' => 'NIF',
                    'label' => 'Número de IVA',
                    'label_en' => 'VAT Number',
                    'pattern' => '^PT\d{9}$',
                    'placeholder' => 'PT123456789',
                    'maxlength' => 11,
                    'help' => 'Format: PT + 9 dígitos'
                ]
            ],

            // Austria
            'AT' => [
                'registration' => [
                    'type' => 'FN',
                    'label' => 'Firmenbuchnummer',
                    'label_en' => 'Commercial Register Number',
                    'pattern' => '^\d+[a-z]$',
                    'placeholder' => '123456a',
                    'maxlength' => 10,
                    'help' => 'Ihre Firmenbuchnummer',
                    'verification_url' => 'https://www.firmenbuch.at/'
                ],
                'tax' => [
                    'type' => 'UID',
                    'label' => 'UID-Nummer',
                    'label_en' => 'VAT Number',
                    'pattern' => '^ATU\d{8}$',
                    'placeholder' => 'ATU12345678',
                    'maxlength' => 11,
                    'help' => 'Format: ATU + 8 Ziffern'
                ]
            ],

            // Switzerland
            'CH' => [
                'registration' => [
                    'type' => 'UID',
                    'label' => 'UID-Nummer',
                    'label_en' => 'Enterprise ID',
                    'pattern' => '^CHE-?\d{3}\.?\d{3}\.?\d{3}$',
                    'placeholder' => 'CHE-123.456.789',
                    'maxlength' => 15,
                    'help' => 'Ihre Unternehmens-ID (CHE-xxx.xxx.xxx)',
                    'verification_url' => 'https://www.uid.admin.ch/'
                ],
                'tax' => [
                    'type' => 'MWST',
                    'label' => 'MWST-Nummer',
                    'label_en' => 'VAT Number',
                    'pattern' => '^CHE-?\d{3}\.?\d{3}\.?\d{3}\s?MWST$',
                    'placeholder' => 'CHE-123.456.789 MWST',
                    'maxlength' => 20,
                    'help' => 'Format: UID + MWST'
                ]
            ],

            // Luxembourg
            'LU' => [
                'registration' => [
                    'type' => 'RCS',
                    'label' => 'Numéro RCS',
                    'label_en' => 'RCS Number',
                    'pattern' => '^B\d+$',
                    'placeholder' => 'B123456',
                    'maxlength' => 10,
                    'help' => 'Votre numéro RCS Luxembourg',
                    'verification_url' => 'https://www.lbr.lu/'
                ],
                'tax' => [
                    'type' => 'TVA',
                    'label' => 'Numéro TVA',
                    'label_en' => 'VAT Number',
                    'pattern' => '^LU\d{8}$',
                    'placeholder' => 'LU12345678',
                    'maxlength' => 10,
                    'help' => 'Format: LU + 8 chiffres'
                ]
            ],

            // Poland
            'PL' => [
                'registration' => [
                    'type' => 'KRS',
                    'label' => 'Numer KRS',
                    'label_en' => 'KRS Number',
                    'pattern' => '^\d{10}$',
                    'placeholder' => '0000123456',
                    'maxlength' => 10,
                    'help' => 'Twój numer KRS',
                    'verification_url' => 'https://ekrs.ms.gov.pl/'
                ],
                'tax' => [
                    'type' => 'NIP',
                    'label' => 'Numer NIP',
                    'label_en' => 'VAT Number',
                    'pattern' => '^PL\d{10}$',
                    'placeholder' => 'PL1234567890',
                    'maxlength' => 12,
                    'help' => 'Format: PL + 10 cyfr'
                ]
            ],

            // USA
            'US' => [
                'registration' => [
                    'type' => 'EIN',
                    'label' => 'EIN (Employer ID)',
                    'label_en' => 'Employer Identification Number',
                    'pattern' => '^\d{2}-?\d{7}$',
                    'placeholder' => '12-3456789',
                    'maxlength' => 10,
                    'help' => 'Your IRS Employer Identification Number',
                    'verification_url' => 'https://www.irs.gov/'
                ],
                'tax' => [
                    'type' => 'Sales Tax',
                    'label' => 'Sales Tax ID',
                    'label_en' => 'Sales Tax ID',
                    'pattern' => '^.+$',
                    'placeholder' => 'State-specific',
                    'maxlength' => 20,
                    'help' => 'Your state sales tax permit number (varies by state)'
                ]
            ],

            // Canada
            'CA' => [
                'registration' => [
                    'type' => 'BN',
                    'label' => 'Business Number',
                    'label_en' => 'Business Number',
                    'pattern' => '^\d{9}$',
                    'placeholder' => '123456789',
                    'maxlength' => 9,
                    'help' => 'Your 9-digit CRA Business Number',
                    'verification_url' => 'https://www.canada.ca/en/revenue-agency/'
                ],
                'tax' => [
                    'type' => 'GST/HST',
                    'label' => 'GST/HST Number',
                    'label_en' => 'GST/HST Number',
                    'pattern' => '^\d{9}RT\d{4}$',
                    'placeholder' => '123456789RT0001',
                    'maxlength' => 15,
                    'help' => 'Format: BN + RT + 4 digits'
                ]
            ],

            // Australia
            'AU' => [
                'registration' => [
                    'type' => 'ABN',
                    'label' => 'ABN',
                    'label_en' => 'Australian Business Number',
                    'pattern' => '^\d{11}$',
                    'placeholder' => '12345678901',
                    'maxlength' => 11,
                    'help' => 'Your 11-digit Australian Business Number',
                    'verification_url' => 'https://abr.business.gov.au/'
                ],
                'tax' => [
                    'type' => 'GST',
                    'label' => 'GST Registration',
                    'label_en' => 'GST Registration',
                    'pattern' => '^\d{11}$',
                    'placeholder' => '12345678901',
                    'maxlength' => 11,
                    'help' => 'Same as ABN if GST registered'
                ]
            ],

            // Turkey
            'TR' => [
                'registration' => [
                    'type' => 'VKN',
                    'label' => 'Vergi Kimlik Numarası',
                    'label_en' => 'Tax Identification Number',
                    'pattern' => '^\d{10}$',
                    'placeholder' => '1234567890',
                    'maxlength' => 10,
                    'help' => '10 haneli Vergi Kimlik Numaranız',
                    'verification_url' => 'https://ivd.gib.gov.tr/'
                ],
                'tax' => [
                    'type' => 'KDV',
                    'label' => 'KDV Numarası',
                    'label_en' => 'VAT Number',
                    'pattern' => '^TR\d{10}$',
                    'placeholder' => 'TR1234567890',
                    'maxlength' => 12,
                    'help' => 'Format: TR + VKN'
                ]
            ],

            // Morocco
            'MA' => [
                'registration' => [
                    'type' => 'RC',
                    'label' => 'رقم السجل التجاري',
                    'label_en' => 'Trade Register Number',
                    'pattern' => '^\d+$',
                    'placeholder' => '123456',
                    'maxlength' => 15,
                    'help' => 'Numéro du Registre de Commerce',
                    'verification_url' => 'https://www.directinfo.ma/'
                ],
                'tax' => [
                    'type' => 'IF',
                    'label' => 'رقم التعريف الضريبي',
                    'label_en' => 'Tax ID',
                    'pattern' => '^\d+$',
                    'placeholder' => '12345678',
                    'maxlength' => 15,
                    'help' => 'Identifiant Fiscal'
                ]
            ],

            // UAE
            'AE' => [
                'registration' => [
                    'type' => 'License',
                    'label' => 'رقم الرخصة التجارية',
                    'label_en' => 'Trade License Number',
                    'pattern' => '^.+$',
                    'placeholder' => '123456',
                    'maxlength' => 20,
                    'help' => 'Your Trade License Number',
                    'verification_url' => 'https://www.dubaided.gov.ae/'
                ],
                'tax' => [
                    'type' => 'TRN',
                    'label' => 'رقم التسجيل الضريبي',
                    'label_en' => 'Tax Registration Number',
                    'pattern' => '^\d{15}$',
                    'placeholder' => '100000000000003',
                    'maxlength' => 15,
                    'help' => '15-digit TRN for VAT'
                ]
            ],
        ];

        // Return config for country or default
        return $configs[strtoupper($countryCode)] ?? self::getDefaultConfig();
    }

    /**
     * Default config for countries not specifically listed
     */
    public static function getDefaultConfig(): array
    {
        return [
            'registration' => [
                'type' => 'Business ID',
                'label' => 'Business Registration Number',
                'label_en' => 'Business Registration Number',
                'pattern' => '^.+$',
                'placeholder' => '',
                'maxlength' => 30,
                'help' => 'Your official business registration number',
                'verification_url' => null
            ],
            'tax' => [
                'type' => 'Tax ID',
                'label' => 'Tax/VAT Number',
                'label_en' => 'Tax/VAT Number',
                'pattern' => '^.+$',
                'placeholder' => '',
                'maxlength' => 20,
                'help' => 'Your tax or VAT registration number'
            ]
        ];
    }

    /**
     * Get all supported countries with their registration types
     */
    public static function getSupportedCountries(): array
    {
        return [
            'NL' => ['name' => 'Nederland', 'flag' => '🇳🇱', 'type' => 'KVK'],
            'BE' => ['name' => 'België', 'flag' => '🇧🇪', 'type' => 'KBO'],
            'DE' => ['name' => 'Deutschland', 'flag' => '🇩🇪', 'type' => 'HRB'],
            'FR' => ['name' => 'France', 'flag' => '🇫🇷', 'type' => 'SIRET'],
            'GB' => ['name' => 'United Kingdom', 'flag' => '🇬🇧', 'type' => 'CRN'],
            'ES' => ['name' => 'España', 'flag' => '🇪🇸', 'type' => 'NIF'],
            'IT' => ['name' => 'Italia', 'flag' => '🇮🇹', 'type' => 'REA'],
            'PT' => ['name' => 'Portugal', 'flag' => '🇵🇹', 'type' => 'NIPC'],
            'AT' => ['name' => 'Österreich', 'flag' => '🇦🇹', 'type' => 'FN'],
            'CH' => ['name' => 'Schweiz', 'flag' => '🇨🇭', 'type' => 'UID'],
            'LU' => ['name' => 'Luxembourg', 'flag' => '🇱🇺', 'type' => 'RCS'],
            'PL' => ['name' => 'Polska', 'flag' => '🇵🇱', 'type' => 'KRS'],
            'US' => ['name' => 'United States', 'flag' => '🇺🇸', 'type' => 'EIN'],
            'CA' => ['name' => 'Canada', 'flag' => '🇨🇦', 'type' => 'BN'],
            'AU' => ['name' => 'Australia', 'flag' => '🇦🇺', 'type' => 'ABN'],
            'TR' => ['name' => 'Türkiye', 'flag' => '🇹🇷', 'type' => 'VKN'],
            'MA' => ['name' => 'Morocco', 'flag' => '🇲🇦', 'type' => 'RC'],
            'AE' => ['name' => 'UAE', 'flag' => '🇦🇪', 'type' => 'License'],
        ];
    }

    /**
     * Validate a business registration number for a specific country
     */
    public static function validate(string $countryCode, string $number, string $type = 'registration'): bool
    {
        $config = self::getCountryConfig($countryCode);
        $pattern = $config[$type]['pattern'] ?? '^.+$';

        return preg_match('/' . $pattern . '/i', $number) === 1;
    }
}

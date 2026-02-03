<?php
namespace GlamourSchedule\Services;

/**
 * Currency conversion service
 * Converts EUR to local currencies for display, keeps EUR as base
 */
class CurrencyService
{
    private array $config;
    private array $ratesCache = [];
    private string $cacheFile;

    // Country to currency mapping
    private array $countryCurrencies = [
        'NL' => 'EUR', 'BE' => 'EUR', 'DE' => 'EUR', 'FR' => 'EUR', 'ES' => 'EUR',
        'IT' => 'EUR', 'PT' => 'EUR', 'AT' => 'EUR', 'IE' => 'EUR', 'FI' => 'EUR',
        'GR' => 'EUR', 'LU' => 'EUR', 'MT' => 'EUR', 'CY' => 'EUR', 'SK' => 'EUR',
        'SI' => 'EUR', 'EE' => 'EUR', 'LV' => 'EUR', 'LT' => 'EUR', 'HR' => 'EUR',
        'MC' => 'EUR', 'SM' => 'EUR', 'VA' => 'EUR', 'AD' => 'EUR',
        'GB' => 'GBP', 'US' => 'USD', 'CA' => 'CAD', 'AU' => 'AUD', 'NZ' => 'NZD',
        'CH' => 'CHF', 'LI' => 'CHF', 'SE' => 'SEK', 'NO' => 'NOK', 'DK' => 'DKK',
        'IS' => 'ISK', 'PL' => 'PLN', 'CZ' => 'CZK', 'HU' => 'HUF', 'RO' => 'RON',
        'BG' => 'BGN', 'JP' => 'JPY', 'CN' => 'CNY', 'IN' => 'INR', 'BR' => 'BRL',
        'MX' => 'MXN', 'ZA' => 'ZAR', 'RU' => 'RUB', 'KR' => 'KRW', 'SG' => 'SGD',
        'HK' => 'HKD', 'TW' => 'TWD', 'TH' => 'THB', 'MY' => 'MYR', 'ID' => 'IDR',
        'PH' => 'PHP', 'VN' => 'VND', 'TR' => 'TRY', 'IL' => 'ILS', 'AE' => 'AED',
        'SA' => 'SAR', 'QA' => 'QAR', 'KW' => 'KWD', 'BH' => 'BHD', 'OM' => 'OMR',
        'EG' => 'EGP', 'NG' => 'NGN', 'KE' => 'KES', 'AR' => 'ARS', 'CL' => 'CLP',
        'CO' => 'COP', 'PE' => 'PEN', 'UA' => 'UAH', 'PK' => 'PKR', 'BD' => 'BDT'
    ];

    // Currency symbols
    private array $currencySymbols = [
        'EUR' => '€', 'USD' => '$', 'GBP' => '£', 'JPY' => '¥', 'CHF' => 'CHF',
        'CAD' => 'C$', 'AUD' => 'A$', 'NZD' => 'NZ$', 'SEK' => 'kr', 'NOK' => 'kr',
        'DKK' => 'kr', 'ISK' => 'kr', 'PLN' => 'zł', 'CZK' => 'Kč', 'HUF' => 'Ft',
        'RON' => 'lei', 'BGN' => 'лв', 'CNY' => '¥', 'INR' => '₹', 'BRL' => 'R$',
        'MXN' => 'MX$', 'ZAR' => 'R', 'RUB' => '₽', 'KRW' => '₩', 'SGD' => 'S$',
        'HKD' => 'HK$', 'TWD' => 'NT$', 'THB' => '฿', 'MYR' => 'RM', 'IDR' => 'Rp',
        'PHP' => '₱', 'VND' => '₫', 'TRY' => '₺', 'ILS' => '₪', 'AED' => 'د.إ',
        'SAR' => '﷼', 'QAR' => 'ر.ق', 'KWD' => 'د.ك', 'BHD' => 'د.ب', 'OMR' => 'ر.ع',
        'EGP' => 'E£', 'NGN' => '₦', 'KES' => 'KSh', 'ARS' => 'AR$', 'CLP' => 'CL$',
        'COP' => 'CO$', 'PEN' => 'S/', 'UAH' => '₴', 'PKR' => '₨', 'BDT' => '৳'
    ];

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->cacheFile = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/storage/cache/exchange_rates.json';
        $this->loadCachedRates();
    }

    /**
     * Get currency code for country
     */
    public function getCurrencyForCountry(string $countryCode): string
    {
        return $this->countryCurrencies[strtoupper($countryCode)] ?? 'EUR';
    }

    /**
     * Get currency symbol
     */
    public function getSymbol(string $currency): string
    {
        return $this->currencySymbols[strtoupper($currency)] ?? $currency;
    }

    /**
     * Convert EUR amount to local currency for display
     * Returns both local amount and EUR amount
     */
    public function convertFromEur(float $eurAmount, string $targetCurrency): array
    {
        $targetCurrency = strtoupper($targetCurrency);

        if ($targetCurrency === 'EUR') {
            return [
                'local_amount' => $eurAmount,
                'local_currency' => 'EUR',
                'local_symbol' => '€',
                'local_formatted' => '€' . number_format($eurAmount, 2, ',', '.'),
                'eur_amount' => $eurAmount,
                'eur_formatted' => '€' . number_format($eurAmount, 2, ',', '.'),
                'exchange_rate' => 1.0
            ];
        }

        $rate = $this->getExchangeRate('EUR', $targetCurrency);
        $localAmount = round($eurAmount * $rate, 2);
        $symbol = $this->getSymbol($targetCurrency);

        return [
            'local_amount' => $localAmount,
            'local_currency' => $targetCurrency,
            'local_symbol' => $symbol,
            'local_formatted' => $symbol . number_format($localAmount, 2, ',', '.'),
            'eur_amount' => $eurAmount,
            'eur_formatted' => '€' . number_format($eurAmount, 2, ',', '.'),
            'exchange_rate' => $rate
        ];
    }

    /**
     * Get display price for country (shows local + EUR)
     */
    public function getDisplayPrice(float $eurAmount, string $countryCode): array
    {
        $currency = $this->getCurrencyForCountry($countryCode);
        return $this->convertFromEur($eurAmount, $currency);
    }

    /**
     * Format price for display with both currencies
     */
    public function formatDualPrice(float $eurAmount, string $countryCode): string
    {
        $price = $this->getDisplayPrice($eurAmount, $countryCode);

        if ($price['local_currency'] === 'EUR') {
            return $price['eur_formatted'];
        }

        return $price['local_formatted'] . ' (' . $price['eur_formatted'] . ')';
    }

    /**
     * Get exchange rate (cached, refreshed daily)
     */
    public function getExchangeRate(string $from, string $to): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) return 1.0;

        $key = "{$from}_{$to}";

        if (isset($this->ratesCache['rates'][$key])) {
            return (float)$this->ratesCache['rates'][$key];
        }

        // Fetch fresh rates if not cached or expired
        if ($this->shouldRefreshRates()) {
            $this->fetchExchangeRates();
        }

        return (float)($this->ratesCache['rates'][$key] ?? 1.0);
    }

    /**
     * Check if rates need refresh (older than 24 hours)
     */
    private function shouldRefreshRates(): bool
    {
        if (empty($this->ratesCache['updated_at'])) return true;
        return (time() - $this->ratesCache['updated_at']) > 86400;
    }

    /**
     * Load cached exchange rates
     */
    private function loadCachedRates(): void
    {
        if (file_exists($this->cacheFile)) {
            $data = json_decode(file_get_contents($this->cacheFile), true);
            if ($data) {
                $this->ratesCache = $data;
            }
        }

        // Set default rates if no cache
        if (empty($this->ratesCache['rates'])) {
            $this->setDefaultRates();
        }
    }

    /**
     * Fetch exchange rates from free API
     */
    private function fetchExchangeRates(): void
    {
        try {
            // Using exchangerate-api.com free tier
            $url = "https://api.exchangerate-api.com/v4/latest/EUR";

            $context = stream_context_create([
                'http' => ['timeout' => 5, 'ignore_errors' => true]
            ]);

            $response = @file_get_contents($url, false, $context);

            if ($response) {
                $data = json_decode($response, true);

                if ($data && isset($data['rates'])) {
                    $rates = [];
                    foreach ($data['rates'] as $currency => $rate) {
                        $rates["EUR_{$currency}"] = $rate;
                    }

                    $this->ratesCache = [
                        'rates' => $rates,
                        'updated_at' => time()
                    ];

                    $this->saveCachedRates();
                }
            }
        } catch (\Exception $e) {
            error_log("Failed to fetch exchange rates: " . $e->getMessage());
        }
    }

    /**
     * Save rates to cache file
     */
    private function saveCachedRates(): void
    {
        $dir = dirname($this->cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->cacheFile, json_encode($this->ratesCache));
    }

    /**
     * Set default exchange rates (approximate, used as fallback)
     */
    private function setDefaultRates(): void
    {
        $this->ratesCache = [
            'rates' => [
                'EUR_USD' => 1.08, 'EUR_GBP' => 0.86, 'EUR_JPY' => 162.0,
                'EUR_CHF' => 0.95, 'EUR_CAD' => 1.47, 'EUR_AUD' => 1.65,
                'EUR_NZD' => 1.78, 'EUR_SEK' => 11.20, 'EUR_NOK' => 11.50,
                'EUR_DKK' => 7.46, 'EUR_ISK' => 150.0, 'EUR_PLN' => 4.32,
                'EUR_CZK' => 25.0, 'EUR_HUF' => 395.0, 'EUR_RON' => 4.97,
                'EUR_BGN' => 1.96, 'EUR_CNY' => 7.85, 'EUR_INR' => 90.0,
                'EUR_BRL' => 5.35, 'EUR_MXN' => 18.5, 'EUR_ZAR' => 20.0,
                'EUR_RUB' => 98.0, 'EUR_KRW' => 1420.0, 'EUR_SGD' => 1.45,
                'EUR_HKD' => 8.45, 'EUR_TRY' => 35.0, 'EUR_AED' => 3.97,
                'EUR_SAR' => 4.05, 'EUR_QAR' => 3.93, 'EUR_KWD' => 0.33,
                'EUR_BHD' => 0.41, 'EUR_OMR' => 0.42
            ],
            'updated_at' => time()
        ];
    }
}

<?php
namespace GlamourSchedule\Core;

class DateFormatter
{
    private string $country;

    private const FORMAT_GROUPS = [
        // NL standaard: dd-mm-yyyy, 24u
        'NL' => ['date' => 'd-m-Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'BE' => ['date' => 'd-m-Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],

        // Duits: dd.mm.yyyy, 24u
        'DE' => ['date' => 'd.m.Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'AT' => ['date' => 'd.m.Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'CH' => ['date' => 'd.m.Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'PL' => ['date' => 'd.m.Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'CZ' => ['date' => 'd.m.Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'RU' => ['date' => 'd.m.Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'TR' => ['date' => 'd.m.Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],

        // Frans/Zuid-EU: dd/mm/yyyy, 24u
        'FR' => ['date' => 'd/m/Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'IT' => ['date' => 'd/m/Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'ES' => ['date' => 'd/m/Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'PT' => ['date' => 'd/m/Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'BR' => ['date' => 'd/m/Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'AR' => ['date' => 'd/m/Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'GR' => ['date' => 'd/m/Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],

        // VS: mm/dd/yyyy, 12u AM/PM
        'US' => ['date' => 'm/d/Y', 'time' => 'g:i A', 'short' => 'M j', 'shortOrder' => 'Md'],
        'PH' => ['date' => 'm/d/Y', 'time' => 'g:i A', 'short' => 'M j', 'shortOrder' => 'Md'],

        // Brits/Commonwealth: dd/mm/yyyy, 12u AM/PM
        'GB' => ['date' => 'd/m/Y', 'time' => 'g:i A', 'short' => 'j M', 'shortOrder' => 'dM'],
        'IE' => ['date' => 'd/m/Y', 'time' => 'g:i A', 'short' => 'j M', 'shortOrder' => 'dM'],
        'AU' => ['date' => 'd/m/Y', 'time' => 'g:i A', 'short' => 'j M', 'shortOrder' => 'dM'],
        'NZ' => ['date' => 'd/m/Y', 'time' => 'g:i A', 'short' => 'j M', 'shortOrder' => 'dM'],
        'IN' => ['date' => 'd/m/Y', 'time' => 'g:i A', 'short' => 'j M', 'shortOrder' => 'dM'],
        'PK' => ['date' => 'd/m/Y', 'time' => 'g:i A', 'short' => 'j M', 'shortOrder' => 'dM'],
        'SA' => ['date' => 'd/m/Y', 'time' => 'g:i A', 'short' => 'j M', 'shortOrder' => 'dM'],
        'AE' => ['date' => 'd/m/Y', 'time' => 'g:i A', 'short' => 'j M', 'shortOrder' => 'dM'],
        'EG' => ['date' => 'd/m/Y', 'time' => 'g:i A', 'short' => 'j M', 'shortOrder' => 'dM'],

        // Oost-Azië: yyyy-variant, 24u
        'JP' => ['date' => 'Y/m/d', 'time' => 'H:i', 'short' => 'M j', 'shortOrder' => 'Md'],
        'KR' => ['date' => 'Y/m/d', 'time' => 'H:i', 'short' => 'M j', 'shortOrder' => 'Md'],
        'CN' => ['date' => 'Y/m/d', 'time' => 'H:i', 'short' => 'M j', 'shortOrder' => 'Md'],
        'SE' => ['date' => 'Y-m-d', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'],
        'HU' => ['date' => 'Y-m-d', 'time' => 'H:i', 'short' => 'M j', 'shortOrder' => 'Md'],

        // Canada: yyyy-mm-dd, 12u AM/PM
        'CA' => ['date' => 'Y-m-d', 'time' => 'g:i A', 'short' => 'M j', 'shortOrder' => 'Md'],
    ];

    private const DEFAULT_FORMAT = ['date' => 'd-m-Y', 'time' => 'H:i', 'short' => 'j M', 'shortOrder' => 'dM'];

    private const COUNTRY_NAME_MAP = [
        'NEDERLAND' => 'NL', 'NETHERLANDS' => 'NL', 'THE NETHERLANDS' => 'NL',
        'BELGIE' => 'BE', 'BELGIUM' => 'BE', 'BELGIË' => 'BE',
        'DUITSLAND' => 'DE', 'GERMANY' => 'DE', 'DEUTSCHLAND' => 'DE',
        'FRANKRIJK' => 'FR', 'FRANCE' => 'FR',
        'SPANJE' => 'ES', 'SPAIN' => 'ES', 'ESPAÑA' => 'ES',
        'ITALIE' => 'IT', 'ITALY' => 'IT', 'ITALIA' => 'IT', 'ITALIË' => 'IT',
        'PORTUGAL' => 'PT', 'BRAZILIE' => 'BR', 'BRAZIL' => 'BR', 'BRASIL' => 'BR', 'BRAZILIË' => 'BR',
        'VERENIGD KONINKRIJK' => 'GB', 'UNITED KINGDOM' => 'GB', 'GROOT-BRITTANNIE' => 'GB', 'GROOT-BRITTANNIË' => 'GB',
        'IERLAND' => 'IE', 'IRELAND' => 'IE',
        'VERENIGDE STATEN' => 'US', 'UNITED STATES' => 'US', 'USA' => 'US',
        'CANADA' => 'CA',
        'AUSTRALIE' => 'AU', 'AUSTRALIA' => 'AU', 'AUSTRALIË' => 'AU',
        'NIEUW-ZEELAND' => 'NZ', 'NEW ZEALAND' => 'NZ',
        'OOSTENRIJK' => 'AT', 'AUSTRIA' => 'AT', 'ÖSTERREICH' => 'AT',
        'ZWITSERLAND' => 'CH', 'SWITZERLAND' => 'CH', 'SCHWEIZ' => 'CH',
        'POLEN' => 'PL', 'POLAND' => 'PL', 'POLSKA' => 'PL',
        'TSJECHIE' => 'CZ', 'CZECH REPUBLIC' => 'CZ', 'CZECHIA' => 'CZ', 'TSJECHIË' => 'CZ',
        'RUSLAND' => 'RU', 'RUSSIA' => 'RU',
        'TURKIJE' => 'TR', 'TURKEY' => 'TR', 'TÜRKIYE' => 'TR',
        'GRIEKENLAND' => 'GR', 'GREECE' => 'GR',
        'JAPAN' => 'JP', 'ZUID-KOREA' => 'KR', 'SOUTH KOREA' => 'KR',
        'CHINA' => 'CN', 'ZWEDEN' => 'SE', 'SWEDEN' => 'SE',
        'HONGARIJE' => 'HU', 'HUNGARY' => 'HU',
        'INDIA' => 'IN', 'PAKISTAN' => 'PK',
        'SAOEDI-ARABIE' => 'SA', 'SAUDI ARABIA' => 'SA', 'SAOEDI-ARABIË' => 'SA',
        'VERENIGDE ARABISCHE EMIRATEN' => 'AE', 'UNITED ARAB EMIRATES' => 'AE',
        'EGYPTE' => 'EG', 'EGYPT' => 'EG',
        'FILIPPIJNEN' => 'PH', 'PHILIPPINES' => 'PH',
        'ARGENTINIE' => 'AR', 'ARGENTINA' => 'AR', 'ARGENTINIË' => 'AR',
    ];

    public function __construct(?string $country = null)
    {
        $raw = strtoupper(trim($country ?? $_SESSION['detected_country'] ?? 'NL'));
        $this->country = self::COUNTRY_NAME_MAP[$raw] ?? (strlen($raw) === 2 ? $raw : 'NL');
    }

    private function getFormats(): array
    {
        return self::FORMAT_GROUPS[$this->country] ?? self::DEFAULT_FORMAT;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Format a date string: "05-02-2026" or "02/05/2026" etc.
     */
    public function formatDate($date): string
    {
        $timestamp = is_numeric($date) ? (int)$date : strtotime($date);
        if ($timestamp === false) return (string)$date;
        return date($this->getFormats()['date'], $timestamp);
    }

    /**
     * Format a time string: "14:30" or "2:30 PM"
     */
    public function formatTime($time): string
    {
        $timestamp = is_numeric($time) ? (int)$time : strtotime($time);
        if ($timestamp === false) return (string)$time;
        return date($this->getFormats()['time'], $timestamp);
    }

    /**
     * Format date and time combined: "05-02-2026 14:30"
     */
    public function formatDateTime($datetime): string
    {
        $timestamp = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        if ($timestamp === false) return (string)$datetime;
        $formats = $this->getFormats();
        return date($formats['date'], $timestamp) . ' ' . date($formats['time'], $timestamp);
    }

    /**
     * Format a short date: "5 Feb" or "Feb 5"
     */
    public function formatShortDate($date): string
    {
        $timestamp = is_numeric($date) ? (int)$date : strtotime($date);
        if ($timestamp === false) return (string)$date;
        $formats = $this->getFormats();
        if ($formats['shortOrder'] === 'Md') {
            return date('M', $timestamp) . ' ' . date('j', $timestamp);
        }
        return date('j', $timestamp) . ' ' . date('M', $timestamp);
    }

    /**
     * Get a DateFormatter instance for a specific country
     */
    public static function forCountry(string $country): self
    {
        return new self($country);
    }

    /**
     * Static shortcut: format date using session country
     */
    public static function date($date): string
    {
        return (new self())->formatDate($date);
    }

    /**
     * Static shortcut: format time using session country
     */
    public static function time($time): string
    {
        return (new self())->formatTime($time);
    }

    /**
     * Static shortcut: format datetime using session country
     */
    public static function dateTime($datetime): string
    {
        return (new self())->formatDateTime($datetime);
    }
}

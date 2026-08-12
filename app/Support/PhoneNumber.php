<?php

namespace App\Support;

class PhoneNumber
{
    private const BD_COUNTRY_CODE = '880';

    /**
     * Digits only, with the Bangladesh country code — the format wa.me expects
     * (e.g. "8801712345678"). Adds the "88" prefix automatically when an admin
     * enters a local-format number like "01712345678".
     */
    public static function whatsapp(?string $raw): string
    {
        return self::withCountryCode($raw);
    }

    /**
     * "+"-prefixed, with the Bangladesh country code — the format tel: expects
     * (e.g. "+8801712345678").
     */
    public static function tel(?string $raw): string
    {
        $digits = self::withCountryCode($raw);

        return $digits === '' ? '' : '+' . $digits;
    }

    private static function withCountryCode(?string $raw): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $raw);

        if ($digits === '') {
            return '';
        }

        // Already has the BD country code (880 + subscriber number).
        if (str_starts_with($digits, self::BD_COUNTRY_CODE) && strlen($digits) >= 12) {
            return $digits;
        }

        // Local format with the leading 0, e.g. 01712345678.
        if (str_starts_with($digits, '0')) {
            return self::BD_COUNTRY_CODE . substr($digits, 1);
        }

        // Leading 0 dropped too, e.g. 1712345678 (BD mobile numbers start with 1).
        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return self::BD_COUNTRY_CODE . $digits;
        }

        // Doesn't match a recognizable BD pattern — leave as-is rather than guess.
        return $digits;
    }
}

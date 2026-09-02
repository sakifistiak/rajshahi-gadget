<?php

namespace App\Support;

class NumberToWords
{
    /**
     * Convert an integer/float amount into formatted English words.
     * Example: 62000 => "Sixty-Two Thousand BDT Only"
     * Example: 100069 => "One Hundred Thousand Sixty-Nine BDT Only"
     */
    public static function takaInWords(int|float $amount): string
    {
        $number = (int) round($amount);
        if ($number <= 0) {
            return 'Zero BDT Only';
        }

        return self::convert($number) . ' BDT Only';
    }

    private static function convert(int $num): string
    {
        $ones = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
            5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
            14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
            18 => 'Eighteen', 19 => 'Nineteen',
        ];

        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety',
        ];

        if ($num < 20) {
            return $ones[$num];
        }

        if ($num < 100) {
            $t = (int) ($num / 10);
            $r = $num % 10;
            return $tens[$t] . ($r ? '-' . $ones[$r] : '');
        }

        if ($num < 1000) {
            $h = (int) ($num / 100);
            $r = $num % 100;
            return $ones[$h] . ' Hundred' . ($r ? ' ' . self::convert($r) : '');
        }

        if ($num < 1000000) { // Up to Thousands
            $th = (int) ($num / 1000);
            $r = $num % 1000;
            return self::convert($th) . ' Thousand' . ($r ? ' ' . self::convert($r) : '');
        }

        if ($num < 1000000000) { // Millions
            $m = (int) ($num / 1000000);
            $r = $num % 1000000;
            return self::convert($m) . ' Million' . ($r ? ' ' . self::convert($r) : '');
        }

        $b = (int) ($num / 1000000000);
        $r = $num % 1000000000;
        return self::convert($b) . ' Billion' . ($r ? ' ' . self::convert($r) : '');
    }
}

<?php

namespace App\Helpers;

class TerbilangHelper
{
    public static function convert(float $number): string
    {
        $number = abs($number);
        $words = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp = "";

        if ($number < 12) {
            $temp = " " . $words[(int)$number];
        } else if ($number < 20) {
            $temp = self::convert($number - 10) . " Belas";
        } else if ($number < 100) {
            $temp = self::convert((int)($number / 10)) . " Puluh" . self::convert($number % 10);
        } else if ($number < 200) {
            $temp = " Seratus" . self::convert($number - 100);
        } else if ($number < 1000) {
            $temp = self::convert((int)($number / 100)) . " Ratus" . self::convert($number % 100);
        } else if ($number < 2000) {
            $temp = " Seribu" . self::convert($number - 1000);
        } else if ($number < 1000000) {
            $temp = self::convert((int)($number / 1000)) . " Ribu" . self::convert($number % 1000);
        } else if ($number < 1000000000) {
            $temp = self::convert((int)($number / 1000000)) . " Juta" . self::convert($number % 1000000);
        } else if ($number < 1000000000000) {
            $temp = self::convert((int)($number / 1000000000)) . " Milyar" . self::convert(fmod($number, 1000000000));
        } else if ($number < 1000000000000000) {
            $temp = self::convert((int)($number / 1000000000000)) . " Triliun" . self::convert(fmod($number, 1000000000000));
        }

        return trim(preg_replace('/\s+/', ' ', $temp)) . " Rupiah";
    }
}

<?php

namespace App\Support;

class Money
{
    public static function rupiah(int|float|null $amount): string
    {
        return 'Rp'.number_format((float) ($amount ?? 0), 0, ',', '.');
    }

    /**
     * Angka dengan titik pemisah ribuan tanpa prefiks "Rp" - dipakai buat
     * isi awal input harga (class="money-input") supaya sudah rapi
     * ("3.500.000") begitu halaman dibuka, bukan cuma pas mulai diketik.
     */
    public static function digits(int|float|string|null $amount): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        return number_format((float) $amount, 0, ',', '.');
    }

    /**
     * Compact Indonesian currency shorthand for tight card layouts,
     * e.g. 750000 -> "Rp750rb", 2800000 -> "Rp2,8jt", 5000000 -> "Rp5jt".
     */
    public static function compact(int|float|null $amount): string
    {
        $amount = (float) ($amount ?? 0);

        if ($amount >= 1_000_000) {
            return 'Rp'.static::trimmed($amount / 1_000_000).'jt';
        }

        if ($amount >= 1_000) {
            return 'Rp'.static::trimmed($amount / 1_000).'rb';
        }

        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    private static function trimmed(float $value): string
    {
        return $value == floor($value)
            ? number_format($value, 0, ',', '.')
            : number_format($value, 1, ',', '.');
    }
}

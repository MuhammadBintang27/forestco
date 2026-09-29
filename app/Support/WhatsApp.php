<?php

namespace App\Support;

class WhatsApp
{
    /**
     * Build a wa.me deep link that opens a chat with a pre-filled message.
     */
    public static function link(?string $phone, string $message): ?string
    {
        if (blank($phone)) {
            return null;
        }

        return 'https://wa.me/'.static::normalize($phone).'?text='.rawurlencode($message);
    }

    /**
     * Normalize a local Indonesian phone number (08xx / +62xx / 62xx) into
     * the digits-only 62xx format wa.me expects.
     */
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62'.$digits;
        }

        return $digits;
    }
}

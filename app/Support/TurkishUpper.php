<?php

namespace App\Support;

class TurkishUpper
{
    /**
     * Uppercase helper that preserves Turkish dotted/dotless characters.
     */
    public static function of(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        // Only fix dotted i without touching other characters
        return str_replace('i', 'İ', $value);
    }
}

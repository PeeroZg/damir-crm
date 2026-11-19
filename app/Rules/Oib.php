<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Croatian personal/company identification number (OIB).
 * 11 digits, the last one is a ISO 7064 MOD 11,10 check digit.
 */
class Oib implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::isValid((string) $value)) {
            $fail(__('The :attribute is not a valid OIB.'));
        }
    }

    public static function isValid(string $oib): bool
    {
        if (! preg_match('/^\d{11}$/', $oib)) {
            return false;
        }

        return self::checkDigit(substr($oib, 0, 10)) === (int) $oib[10];
    }

    /** Calculate the check digit for the first 10 digits. */
    public static function checkDigit(string $first10): int
    {
        $a = 10;

        for ($i = 0; $i < 10; $i++) {
            $a = ($a + (int) $first10[$i]) % 10;
            $a = ($a === 0 ? 10 : $a) * 2 % 11;
        }

        $control = 11 - $a;

        return $control === 10 ? 0 : $control;
    }
}

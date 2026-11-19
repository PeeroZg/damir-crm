<?php

namespace App\Support;

/**
 * Locale-aware money / number formatting without requiring the intl extension.
 */
class Money
{
    public static function format(float|int|string|null $amount, ?string $currency = null, ?string $locale = null): string
    {
        $currency ??= config('crm.currency', 'EUR');
        $symbol = $currency === 'EUR' ? '€' : $currency;

        $number = self::number($amount, 2, $locale);

        return ($locale ?? app()->getLocale()) === 'hr'
            ? $number.' '.$symbol
            : $symbol.$number;
    }

    /** Short form for dashboards: €12.5k / 12,5 tis. € */
    public static function compact(float|int|string|null $amount, ?string $locale = null): string
    {
        $amount = (float) $amount;
        $locale ??= app()->getLocale();

        if (abs($amount) < 1000) {
            return self::format($amount, null, $locale);
        }

        [$value, $suffix] = abs($amount) >= 1_000_000
            ? [$amount / 1_000_000, $locale === 'hr' ? ' mil.' : 'M']
            : [$amount / 1000, $locale === 'hr' ? ' tis.' : 'k'];

        $number = self::number($value, 1, $locale);

        return $locale === 'hr' ? "{$number}{$suffix} €" : "€{$number}{$suffix}";
    }

    public static function number(float|int|string|null $value, int $decimals = 2, ?string $locale = null): string
    {
        $hr = ($locale ?? app()->getLocale()) === 'hr';

        return number_format((float) $value, $decimals, $hr ? ',' : '.', $hr ? '.' : ',');
    }
}

<?php

use App\Support\Money;
use Illuminate\Support\Carbon;

if (! function_exists('money')) {
    function money(float|int|string|null $amount, ?string $currency = null): string
    {
        return Money::format($amount, $currency);
    }
}

if (! function_exists('money_compact')) {
    function money_compact(float|int|string|null $amount): string
    {
        return Money::compact($amount);
    }
}

if (! function_exists('format_date')) {
    function format_date(?DateTimeInterface $date, bool $withTime = false): string
    {
        if ($date === null) {
            return '—';
        }

        $format = app()->getLocale() === 'hr'
            ? ($withTime ? 'd.m.Y. H:i' : 'd.m.Y.')
            : ($withTime ? 'M j, Y g:i A' : 'M j, Y');

        return Carbon::instance($date)->format($format);
    }
}

if (! function_exists('format_date_short')) {
    /** "Nov 18" / "18. stu" (with the year only when it is not the current one). */
    function format_date_short(?DateTimeInterface $date): string
    {
        if ($date === null) {
            return '—';
        }

        $date = Carbon::instance($date);
        $thisYear = $date->isCurrentYear();

        return app()->getLocale() === 'hr'
            ? $date->format($thisYear ? 'j.n.' : 'j.n.Y.')
            : $date->format($thisYear ? 'M j' : "M j 'y");
    }
}

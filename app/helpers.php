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

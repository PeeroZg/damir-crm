<?php

namespace App\Enums;

/**
 * Shared helpers for backed enums that are shown in the UI.
 *
 * Every enum using this trait implements label() and color().
 */
trait HasOptions
{
    /** @return array<string, string> value => translated label */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

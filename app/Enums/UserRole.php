<?php

namespace App\Enums;

enum UserRole: string
{
    use HasOptions;

    case Admin = 'admin';
    case Sales = 'sales';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('Administrator'),
            self::Sales => __('Sales rep'),
        };
    }

    /** Design token tone: pill-{tone}, bg-{tone}-bg, text-{tone}-fg, bg-{tone}-solid. */
    public function color(): string
    {
        return match ($this) {
            self::Admin => 'volt',
            self::Sales => 'neutral',
        };
    }
}

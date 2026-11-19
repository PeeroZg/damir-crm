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

    public function color(): string
    {
        return match ($this) {
            self::Admin => 'violet',
            self::Sales => 'sky',
        };
    }
}

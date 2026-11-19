<?php

namespace App\Enums;

enum ContactStatus: string
{
    use HasOptions;

    case New = 'new';
    case Contacted = 'contacted';
    case Engaged = 'engaged';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::New => __('New'),
            self::Contacted => __('Contacted'),
            self::Engaged => __('Engaged'),
            self::Inactive => __('Inactive'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'sky',
            self::Contacted => 'amber',
            self::Engaged => 'emerald',
            self::Inactive => 'slate',
        };
    }
}

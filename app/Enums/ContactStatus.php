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

    /** Design token tone: pill-{tone}, bg-{tone}-bg, text-{tone}-fg, bg-{tone}-solid. */
    public function color(): string
    {
        return match ($this) {
            self::New => 'lead',
            self::Contacted => 'negotiation',
            self::Engaged => 'won',
            self::Inactive => 'draft',
        };
    }
}

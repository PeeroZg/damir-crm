<?php

namespace App\Enums;

enum CompanyStatus: string
{
    use HasOptions;

    case Lead = 'lead';
    case Prospect = 'prospect';
    case Customer = 'customer';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Lead => __('Lead'),
            self::Prospect => __('Prospect'),
            self::Customer => __('Customer'),
            self::Inactive => __('Inactive'),
        };
    }

    /** Design token tone: pill-{tone}, bg-{tone}-bg, text-{tone}-fg, bg-{tone}-solid. */
    public function color(): string
    {
        return match ($this) {
            self::Lead => 'lead',
            self::Prospect => 'proposal',
            self::Customer => 'won',
            self::Inactive => 'draft',
        };
    }
}

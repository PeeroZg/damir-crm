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

    public function color(): string
    {
        return match ($this) {
            self::Lead => 'sky',
            self::Prospect => 'violet',
            self::Customer => 'emerald',
            self::Inactive => 'slate',
        };
    }
}

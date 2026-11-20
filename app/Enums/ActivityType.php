<?php

namespace App\Enums;

enum ActivityType: string
{
    use HasOptions;

    case Note = 'note';
    case Call = 'call';
    case Meeting = 'meeting';
    case Email = 'email';
    case Task = 'task';

    public function label(): string
    {
        return match ($this) {
            self::Note => __('Note'),
            self::Call => __('Call'),
            self::Meeting => __('Meeting'),
            self::Email => __('Email'),
            self::Task => __('Task'),
        };
    }

    /** Design token tone: pill-{tone}, bg-{tone}-bg, text-{tone}-fg, bg-{tone}-solid. */
    public function color(): string
    {
        return match ($this) {
            self::Note => 'draft',
            self::Call => 'lead',
            self::Meeting => 'proposal',
            self::Email => 'sent',
            self::Task => 'negotiation',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Note => 'note',
            self::Call => 'phone',
            self::Meeting => 'user-group',
            self::Email => 'mail',
            self::Task => 'check-circle',
        };
    }
}

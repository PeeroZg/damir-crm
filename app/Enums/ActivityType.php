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

    public function color(): string
    {
        return match ($this) {
            self::Note => 'slate',
            self::Call => 'sky',
            self::Meeting => 'violet',
            self::Email => 'indigo',
            self::Task => 'amber',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Note => 'note',
            self::Call => 'phone',
            self::Meeting => 'users',
            self::Email => 'mail',
            self::Task => 'check',
        };
    }
}

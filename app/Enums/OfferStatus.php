<?php

namespace App\Enums;

enum OfferStatus: string
{
    use HasOptions;

    case Draft = 'draft';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('Draft'),
            self::Sent => __('Sent'),
            self::Accepted => __('Accepted'),
            self::Rejected => __('Rejected'),
            self::Expired => __('Expired'),
        };
    }

    /** Design token tone: pill-{tone}, bg-{tone}-bg, text-{tone}-fg, bg-{tone}-solid. */
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'draft',
            self::Sent => 'sent',
            self::Accepted => 'won',
            self::Rejected => 'lost',
            self::Expired => 'expired',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Accepted, self::Rejected, self::Expired], true);
    }
}

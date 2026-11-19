<?php

namespace App\Enums;

enum DealStage: string
{
    use HasOptions;

    case Lead = 'lead';
    case Qualified = 'qualified';
    case Proposal = 'proposal';
    case Negotiation = 'negotiation';
    case Won = 'won';
    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::Lead => __('Lead'),
            self::Qualified => __('Qualified'),
            self::Proposal => __('Proposal'),
            self::Negotiation => __('Negotiation'),
            self::Won => __('Won'),
            self::Lost => __('Lost'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Lead => 'sky',
            self::Qualified => 'indigo',
            self::Proposal => 'violet',
            self::Negotiation => 'amber',
            self::Won => 'emerald',
            self::Lost => 'rose',
        };
    }

    /** Win probability used for the weighted pipeline forecast (0-100). */
    public function probability(): int
    {
        return match ($this) {
            self::Lead => 10,
            self::Qualified => 25,
            self::Proposal => 50,
            self::Negotiation => 75,
            self::Won => 100,
            self::Lost => 0,
        };
    }

    public function isClosed(): bool
    {
        return $this === self::Won || $this === self::Lost;
    }

    /** Position in the sales funnel, used to only ever move deals forward automatically. */
    public function order(): int
    {
        return array_search($this, self::cases(), true);
    }

    /** @return list<self> */
    public static function open(): array
    {
        return [self::Lead, self::Qualified, self::Proposal, self::Negotiation];
    }
}

<?php

namespace Database\Factories;

use App\Enums\OfferStatus;
use App\Models\Deal;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Offer> */
class OfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'deal_id' => Deal::factory(),
            'company_id' => fn (array $attributes) => Deal::find($attributes['deal_id'])->company_id,
            'owner_id' => fn (array $attributes) => Deal::find($attributes['deal_id'])->owner_id,
            'number' => 'OFF-'.now()->year.'-'.fake()->unique()->numerify('9###'),
            'status' => OfferStatus::Draft,
            'issued_at' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'vat_rate' => 25,
            'subtotal' => 1000,
            'vat_amount' => 250,
            'total' => 1250,
        ];
    }

    public function status(OfferStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}

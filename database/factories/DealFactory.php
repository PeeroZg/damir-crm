<?php

namespace Database\Factories;

use App\Enums\DealStage;
use App\Models\Company;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Deal> */
class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'owner_id' => User::factory(),
            'title' => fake()->catchPhrase(),
            'value' => fake()->numberBetween(10, 500) * 100,
            'stage' => DealStage::Lead,
            'expected_close_date' => now()->addDays(fake()->numberBetween(5, 90)),
        ];
    }

    public function stage(DealStage $stage): static
    {
        return $this->state(fn () => ['stage' => $stage]);
    }
}

<?php

namespace Database\Factories;

use App\Enums\CompanyStatus;
use App\Models\Company;
use App\Rules\Oib;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Company> */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'status' => fake()->randomElement(CompanyStatus::cases()),
            'oib' => self::fakeOib(),
            'industry' => fake()->randomElement(['Software', 'Retail', 'Logistics', 'Tourism', 'Manufacturing', 'Healthcare']),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'city' => fake()->city(),
        ];
    }

    /** A random but checksum-valid OIB. */
    public static function fakeOib(): string
    {
        $first10 = (string) fake()->numberBetween(1000000000, 9999999999);

        return $first10.Oib::checkDigit($first10);
    }
}

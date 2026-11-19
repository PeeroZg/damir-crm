<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Activity> */
class ActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject_type' => 'company',
            'subject_id' => Company::factory(),
            'user_id' => User::factory(),
            'type' => ActivityType::Note,
            'body' => fake()->sentence(),
        ];
    }

    public function task(?User $assignee = null, mixed $dueAt = null): static
    {
        return $this->state(fn () => [
            'type' => ActivityType::Task,
            'title' => fake()->sentence(4),
            'body' => null,
            'assigned_to' => $assignee?->id ?? User::factory(),
            'due_at' => $dueAt ?? now()->addDays(2),
        ]);
    }
}

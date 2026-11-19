<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
| Helpers
*/
function admin(): User
{
    return User::factory()->create(['role' => UserRole::Admin, 'name' => 'Ada Admin']);
}

function salesRep(string $name = 'Sam Sales'): User
{
    return User::factory()->create(['role' => UserRole::Sales, 'name' => $name]);
}

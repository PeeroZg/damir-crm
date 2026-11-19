<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

it('is only available to admins', function () {
    $this->actingAs(salesRep())->get('/users')->assertForbidden();
    $this->actingAs(salesRep())->post('/users', [])->assertForbidden();
    $this->actingAs(admin())->get('/users')->assertOk();
});

it('lets an admin create a sales rep who can log in', function () {
    $this->actingAs(admin())->post('/users', [
        'name' => 'Nova Osoba', 'email' => 'nova@example.com', 'role' => 'sales', 'password' => 'secret-pass-123',
    ])->assertRedirect('/users');

    auth()->logout();

    $this->post('/login', ['email' => 'nova@example.com', 'password' => 'secret-pass-123'])->assertRedirect('/dashboard');
});

it('prevents admins from changing their own role', function () {
    $admin = admin();

    $this->actingAs($admin)->put("/users/{$admin->id}", [
        'name' => $admin->name, 'email' => $admin->email, 'role' => 'sales',
    ])->assertRedirect();

    expect($admin->fresh()->role)->toBe(UserRole::Admin);
});

it('protects demo accounts in demo mode', function () {
    config(['crm.demo.enabled' => true]);
    $demo = User::factory()->create(['email' => 'ana@damircrm.test']);

    $this->actingAs(admin())->get("/users/{$demo->id}/edit")->assertForbidden();
});

it('refuses demo:reset when demo mode is off', function () {
    config(['crm.demo.enabled' => false]);
    User::factory()->create();

    expect(Artisan::call('demo:reset', ['--force' => true]))->toBe(1)
        ->and(User::count())->toBe(1);
});

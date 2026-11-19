<?php

use App\Models\User;

test('profile page is displayed', function () {
    $this->actingAs(User::factory()->create())->get('/profile')->assertOk();
});

test('profile information and language can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', ['name' => 'Test User', 'email' => 'test@example.com', 'locale' => 'hr'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    expect($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->locale)->toBe('hr')
        ->and($user->email_verified_at)->toBeNull();
});

test('the UI switches to Croatian', function () {
    $user = User::factory()->create(['locale' => 'hr']);

    $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Otvoreni pipeline');
});

test('guests can switch the language on the login page', function () {
    $this->post('/locale/hr')->assertRedirect();
    $this->get('/login')->assertSee('Prijava');
});

test('demo account e-mail cannot be changed', function () {
    config(['crm.demo.enabled' => true]);
    $user = User::factory()->create(['email' => 'marko@damircrm.test']);

    $this->actingAs($user)
        ->patch('/profile', ['name' => 'X', 'email' => 'hacker@example.com', 'locale' => 'en'])
        ->assertSessionHas('error');

    expect($user->refresh()->email)->toBe('marko@damircrm.test');
});

test('api tokens can be created and revoked', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/profile/tokens', ['name' => 'Zapier'])
        ->assertSessionHas('new_token');

    $token = $user->apiTokens()->first();
    expect($token->name)->toBe('Zapier');

    $this->actingAs($user)->delete("/profile/tokens/{$token->id}")->assertRedirect('/profile');
    expect($user->apiTokens()->count())->toBe(0);
});

test('a user cannot revoke somebody else\'s token', function () {
    $owner = User::factory()->create();
    $owner->createApiToken('mine');

    $this->actingAs(User::factory()->create())
        ->delete('/profile/tokens/'.$owner->apiTokens()->first()->id)
        ->assertNotFound();
});

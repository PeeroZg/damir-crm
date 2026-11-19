<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $this->get('/login')->assertOk()->assertSee('Damir');
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('users can logout', function () {
    $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/');

    $this->assertGuest();
});

test('there is no public registration', function () {
    $this->get('/register')->assertNotFound();
});

test('guests are redirected to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

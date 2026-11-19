<?php

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Offer;
use App\Models\User;
use Database\Seeders\DemoSeeder;

/*
| Smoke test: every screen renders with realistic demo data, for both roles.
| Strict mode is on in tests, so any N+1 lazy load fails here.
*/
beforeEach(function () {
    $this->seed(DemoSeeder::class);
});

dataset('roles', ['admin@damircrm.test', 'ana@damircrm.test']);

it('renders every list, board and form', function (string $email) {
    $this->actingAs(User::where('email', $email)->first());

    $pages = [
        '/dashboard', '/dashboard?scope=mine',
        '/companies', '/companies?q=a&status=customer&sort=deals_count&dir=desc', '/companies/kanban', '/companies/create',
        '/contacts', '/contacts?status=engaged&sort=status', '/contacts/kanban', '/contacts/create',
        '/deals', '/deals?stage=open&owner=me&sort=value', '/deals/kanban', '/deals/kanban?owner=me', '/deals/create',
        '/offers', '/offers?status=sent', '/offers/kanban', '/offers/create',
        '/tasks', '/tasks?filter=overdue', '/tasks?filter=done',
        '/profile',
    ];

    foreach ($pages as $page) {
        $this->get($page)->assertOk();
    }
})->with('roles');

it('renders every detail page', function (string $email) {
    $this->actingAs(User::where('email', $email)->first());

    $this->get(route('companies.show', Company::first()))->assertOk();
    $this->get(route('companies.edit', Company::first()))->assertOk();
    $this->get(route('contacts.show', Contact::first()))->assertOk();
    $this->get(route('contacts.edit', Contact::first()))->assertOk();

    Deal::all()->each(fn (Deal $deal) => $this->get(route('deals.show', $deal))->assertOk());
    Offer::all()->each(fn (Offer $offer) => $this->get(route('offers.show', $offer))->assertOk());
})->with('roles');

it('renders the team page for admins only', function () {
    $this->actingAs(User::where('email', 'admin@damircrm.test')->first())->get('/users')->assertOk()->assertSee('Ana Kovač');
    $this->actingAs(User::where('email', 'ana@damircrm.test')->first())->get('/users')->assertForbidden();
});

it('renders pages in Croatian', function () {
    $user = User::where('email', 'admin@damircrm.test')->first();
    $user->update(['locale' => 'hr']);

    $this->actingAs($user)->get('/deals/kanban')->assertOk()->assertSee('Pregovori');
    $this->actingAs($user)->get(route('offers.show', Offer::first()))->assertOk()->assertSee('Međuzbroj');
});

it('downloads CSV exports', function (string $path) {
    $this->actingAs(User::first());

    $response = $this->get($path)->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain(';');
})->with(['/companies/export', '/contacts/export', '/deals/export?stage=won']);

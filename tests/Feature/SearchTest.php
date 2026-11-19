<?php

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;

it('searches across companies, contacts and deals', function () {
    $company = Company::factory()->create(['name' => 'Velebit Energija']);
    Contact::factory()->for($company)->create(['first_name' => 'Ivana', 'last_name' => 'Velić']);
    Deal::factory()->for($company)->create(['title' => 'Solar monitoring portal']);

    $results = $this->actingAs(salesRep())->getJson('/search?q=vel')->assertOk()->json('results');

    expect(collect($results)->pluck('title'))->toContain('Velebit Energija', 'Ivana Velić', 'Solar monitoring portal');
});

it('needs at least two characters', function () {
    $this->actingAs(salesRep())->getJson('/search?q=a')->assertOk()->assertExactJson(['results' => []]);
});

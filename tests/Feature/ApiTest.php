<?php

use App\Enums\DealStage;
use App\Models\Company;
use App\Models\Deal;

it('rejects requests without a valid token', function () {
    $this->getJson('/api/v1/deals')->assertUnauthorized();
    $this->withToken('1|wrong')->getJson('/api/v1/deals')->assertUnauthorized();
    $this->withToken('garbage')->getJson('/api/v1/me')->assertUnauthorized();
});

it('authenticates with a personal token', function () {
    $user = salesRep();
    $token = $user->createApiToken('test');

    $this->withToken($token)->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonPath('data.email', $user->email);

    expect($user->apiTokens()->first()->last_used_at)->not->toBeNull();
});

it('stops accepting a revoked or expired token', function () {
    $user = salesRep();
    $revoked = $user->createApiToken('revoked');
    $user->apiTokens()->delete();
    $expired = $user->createApiToken('expired', now()->subMinute());

    $this->withToken($revoked)->getJson('/api/v1/me')->assertUnauthorized();
    $this->withToken($expired)->getJson('/api/v1/me')->assertUnauthorized();
});

it('lists and filters deals', function () {
    $token = salesRep()->createApiToken('t');
    Deal::factory()->count(2)->stage(DealStage::Won)->create();
    Deal::factory()->stage(DealStage::Lead)->create();

    $this->withToken($token)->getJson('/api/v1/deals?stage=won')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => [['id', 'title', 'value', 'stage', 'probability', 'company']], 'meta', 'links']);
});

it('creates a deal and moves it through the pipeline', function () {
    $user = salesRep();
    $token = $user->createApiToken('t');

    $id = $this->withToken($token)->postJson('/api/v1/deals', [
        'title' => 'API deal', 'company_id' => Company::factory()->create()->id, 'value' => 5000, 'stage' => 'lead',
    ])->assertCreated()->assertJsonPath('data.owner.id', $user->id)->json('data.id');

    $this->withToken($token)->patchJson("/api/v1/deals/{$id}/stage", ['stage' => 'qualified'])
        ->assertOk()
        ->assertJsonPath('data.stage', 'qualified')
        ->assertJsonPath('data.probability', 25);
});

it('applies the same permissions as the web app', function () {
    $deal = Deal::factory()->create();

    $this->withToken(salesRep('Other')->createApiToken('t'))
        ->patchJson("/api/v1/deals/{$deal->id}/stage", ['stage' => 'won'])
        ->assertForbidden();
});

it('returns validation errors as JSON', function () {
    $this->withToken(salesRep()->createApiToken('t'))
        ->postJson('/api/v1/deals', ['title' => ''])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'company_id', 'value', 'stage']);
});

<?php

use App\Enums\DealStage;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;

it('creates a deal owned by the current user', function () {
    $user = salesRep();
    $company = Company::factory()->create();
    $contact = Contact::factory()->for($company)->create();

    $this->actingAs($user)->post('/deals', [
        'title' => 'Website redesign',
        'company_id' => $company->id,
        'contact_id' => $contact->id,
        'value' => 12500,
        'stage' => 'lead',
        'owner_id' => admin()->id, // ignored for sales reps
    ])->assertRedirect();

    $deal = Deal::firstWhere('title', 'Website redesign');

    expect($deal->owner_id)->toBe($user->id)
        ->and($deal->stage)->toBe(DealStage::Lead)
        ->and($deal->stage_changed_at)->not->toBeNull();
});

it('lets admins assign a deal to another rep', function () {
    $rep = salesRep();

    $this->actingAs(admin())->post('/deals', [
        'title' => 'ERP integration', 'company_id' => Company::factory()->create()->id,
        'value' => 1000, 'stage' => 'lead', 'owner_id' => $rep->id,
    ]);

    expect(Deal::firstWhere('title', 'ERP integration')->owner_id)->toBe($rep->id);
});

it('rejects a contact from another company', function () {
    $this->actingAs(salesRep())->post('/deals', [
        'title' => 'X',
        'company_id' => Company::factory()->create()->id,
        'contact_id' => Contact::factory()->create()->id,
        'value' => 100,
        'stage' => 'lead',
    ])->assertSessionHasErrors('contact_id');
});

it('moves a deal on the board and keeps closed_at consistent', function () {
    $owner = salesRep();
    $deal = Deal::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->patchJson("/deals/{$deal->id}/stage", ['stage' => 'won'])
        ->assertOk()->assertJson(['stage' => 'won']);
    expect($deal->fresh()->closed_at)->not->toBeNull();

    // Re-opening a deal clears the close date again.
    $this->actingAs($owner)->patchJson("/deals/{$deal->id}/stage", ['stage' => 'negotiation'])->assertOk();
    expect($deal->fresh()->closed_at)->toBeNull();
});

it('stores the lost reason when a deal is lost', function () {
    $owner = salesRep();
    $deal = Deal::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->patchJson("/deals/{$deal->id}/stage", ['stage' => 'lost', 'lost_reason' => 'Budget cut'])->assertOk();

    expect($deal->fresh())->stage->toBe(DealStage::Lost)->lost_reason->toBe('Budget cut');
});

it('forbids other sales reps from moving or editing a deal', function () {
    $deal = Deal::factory()->create();
    $intruder = salesRep('Intruder');

    $this->actingAs($intruder)->patchJson("/deals/{$deal->id}/stage", ['stage' => 'won'])->assertForbidden();
    $this->actingAs($intruder)->get("/deals/{$deal->id}/edit")->assertForbidden();
    $this->actingAs($intruder)->delete("/deals/{$deal->id}")->assertForbidden();

    expect($deal->fresh()->stage)->toBe(DealStage::Lead);
});

it('lets admins move any deal', function () {
    $deal = Deal::factory()->create();

    $this->actingAs(admin())->patchJson("/deals/{$deal->id}/stage", ['stage' => 'qualified'])->assertOk();

    expect($deal->fresh()->stage)->toBe(DealStage::Qualified);
});

it('logs stage changes in the history', function () {
    $owner = salesRep();
    $deal = Deal::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->patch("/deals/{$deal->id}/stage", ['stage' => 'proposal']);

    $log = $deal->auditLogs()->where('event', 'updated')->first();

    expect($log->diff['stage'])->toBe(['old' => 'lead', 'new' => 'proposal'])
        ->and($log->isStatusChange())->toBeTrue();

    $this->actingAs($owner)->get("/deals/{$deal->id}")->assertSee('Proposal');
});

it('never moves a deal backwards automatically', function () {
    $deal = Deal::factory()->stage(DealStage::Negotiation)->create();

    expect($deal->advanceTo(DealStage::Proposal))->toBeFalse()
        ->and($deal->fresh()->stage)->toBe(DealStage::Negotiation);
});

it('calculates the weighted value from the stage probability', function () {
    $deal = Deal::factory()->stage(DealStage::Proposal)->make(['value' => 10000]);

    expect($deal->weightedValue())->toBe(5000.0);
});

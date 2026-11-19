<?php

use App\Enums\CompanyStatus;
use App\Enums\DealStage;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Deal;
use App\Rules\Oib;

it('creates a company with a valid OIB and records it in the audit log', function () {
    $user = salesRep();
    $oib = '1234567890'.Oib::checkDigit('1234567890');

    $this->actingAs($user)->post('/companies', [
        'name' => 'Jadran Logistika d.o.o.',
        'status' => 'lead',
        'oib' => $oib,
        'website' => 'jadran.hr',
    ])->assertRedirect();

    $company = Company::firstWhere('name', 'Jadran Logistika d.o.o.');

    expect($company->oib)->toBe($oib)
        ->and($company->website)->toBe('https://jadran.hr')
        ->and($company->owner_id)->toBe($user->id)
        ->and(AuditLog::where('auditable_type', 'company')->where('event', 'created')->where('user_id', $user->id)->exists())->toBeTrue();
});

it('rejects an OIB with a wrong check digit', function () {
    $invalid = '1234567890'.((Oib::checkDigit('1234567890') + 1) % 10);

    $this->actingAs(salesRep())
        ->post('/companies', ['name' => 'X', 'status' => 'lead', 'oib' => $invalid])
        ->assertSessionHasErrors('oib');
});

it('records a field level diff when a company is updated', function () {
    $user = salesRep();
    $company = Company::factory()->create(['status' => CompanyStatus::Lead, 'city' => 'Split']);

    $this->actingAs($user)->put("/companies/{$company->id}", [
        'name' => $company->name, 'status' => 'customer', 'city' => 'Zagreb',
    ])->assertRedirect();

    $log = $company->auditLogs()->where('event', 'updated')->first();

    expect($log->diff)->toMatchArray([
        'status' => ['old' => 'lead', 'new' => 'customer'],
        'city' => ['old' => 'Split', 'new' => 'Zagreb'],
    ])->and($log->user_id)->toBe($user->id);
});

it('only lets the account owner or an admin delete a company', function () {
    $owner = salesRep();
    $company = Company::factory()->create(['owner_id' => $owner->id]);

    $this->actingAs(salesRep('Other'))->delete("/companies/{$company->id}")->assertForbidden();
    $this->actingAs($owner)->delete("/companies/{$company->id}")->assertRedirect('/companies');

    expect($company->fresh()->trashed())->toBeTrue();
});

it('refuses to delete a company that still has open deals', function () {
    $company = Company::factory()->create();
    Deal::factory()->for($company)->stage(DealStage::Negotiation)->create();

    $this->actingAs(admin())->delete("/companies/{$company->id}")->assertSessionHas('error');

    expect($company->fresh()->trashed())->toBeFalse();
});

it('changes the status from the kanban board via JSON', function () {
    $company = Company::factory()->create(['status' => CompanyStatus::Lead]);

    $this->actingAs(salesRep())
        ->patchJson("/companies/{$company->id}/status", ['status' => 'prospect'])
        ->assertOk()
        ->assertJson(['status' => 'prospect']);

    $this->actingAs(salesRep())
        ->patchJson("/companies/{$company->id}/status", ['status' => 'nonsense'])
        ->assertUnprocessable();

    expect($company->fresh()->status)->toBe(CompanyStatus::Prospect);
});

it('filters and searches the company list', function () {
    Company::factory()->create(['name' => 'Kvarner Soft', 'status' => CompanyStatus::Customer]);
    Company::factory()->create(['name' => 'Panonija Agro', 'status' => CompanyStatus::Lead]);

    $this->actingAs(salesRep())->get('/companies?q=kvarner')
        ->assertSee('Kvarner Soft')->assertDontSee('Panonija Agro');

    $this->actingAs(salesRep())->get('/companies?status=lead')
        ->assertSee('Panonija Agro')->assertDontSee('Kvarner Soft');
});

it('neutralises spreadsheet formulas in CSV exports', function () {
    Company::factory()->create(['name' => '=HYPERLINK("http://evil")']);

    $csv = $this->actingAs(salesRep())->get('/companies/export')->streamedContent();

    expect($csv)->toContain("'=HYPERLINK");
});

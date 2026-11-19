<?php

use App\Enums\DealStage;
use App\Enums\OfferStatus;
use App\Models\Deal;
use App\Models\Offer;
use App\Services\OfferService;
use Illuminate\Support\Facades\Artisan;

function offerPayload(Deal $deal, array $overrides = []): array
{
    return array_merge([
        'deal_id' => $deal->id,
        'issued_at' => now()->toDateString(),
        'valid_until' => now()->addDays(30)->toDateString(),
        'vat_rate' => 25,
        'items' => [
            ['description' => 'Development', 'quantity' => 10, 'unit' => 'h', 'unit_price' => 50, 'discount_percent' => 0],
            ['description' => 'Design', 'quantity' => 2, 'unit' => 'day', 'unit_price' => 400, 'discount_percent' => 10],
            ['description' => '', 'quantity' => 1, 'unit_price' => 0], // empty row is ignored
        ],
        // Totals sent by a (malicious) browser are ignored:
        'total' => 1,
    ], $overrides);
}

it('creates an offer from a deal with server side totals', function () {
    $owner = salesRep();
    $deal = Deal::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->post('/offers', offerPayload($deal))->assertRedirect();

    $offer = Offer::with('items')->first();

    // 10 × 50 = 500, 2 × 400 − 10 % = 720 → subtotal 1220, VAT 305, total 1525
    expect($offer->items)->toHaveCount(2)
        ->and((float) $offer->subtotal)->toBe(1220.0)
        ->and((float) $offer->vat_amount)->toBe(305.0)
        ->and((float) $offer->total)->toBe(1525.0)
        ->and($offer->number)->toBe('OFF-'.now()->year.'-0001')
        ->and($offer->status)->toBe(OfferStatus::Draft)
        ->and($deal->fresh()->stage)->toBe(DealStage::Proposal);
});

it('numbers offers sequentially and survives deleted offers', function () {
    $owner = salesRep();
    $deal = Deal::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->post('/offers', offerPayload($deal));
    $this->actingAs($owner)->post('/offers', offerPayload($deal));
    Offer::latest('id')->first()->delete();
    $this->actingAs($owner)->post('/offers', offerPayload($deal));

    expect(Offer::withTrashed()->orderBy('id')->pluck('number')->all())->toBe([
        'OFF-'.now()->year.'-0001',
        'OFF-'.now()->year.'-0002',
        'OFF-'.now()->year.'-0003',
    ]);
});

it('requires at least one line item', function () {
    $owner = salesRep();
    $deal = Deal::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->post('/offers', offerPayload($deal, ['items' => []]))->assertSessionHasErrors('items');
});

it('does not let reps create offers for other reps\' deals', function () {
    $deal = Deal::factory()->create();

    $this->actingAs(salesRep('Other'))->post('/offers', offerPayload($deal))->assertForbidden();
    $this->actingAs(salesRep('Other'))->get('/offers/create?deal_id='.$deal->id)->assertForbidden();
});

it('marks the deal as won and rejects competing offers when an offer is accepted', function () {
    $owner = salesRep();
    $deal = Deal::factory()->for($owner, 'owner')->stage(DealStage::Negotiation)->create(['value' => 999]);
    $service = app(OfferService::class);

    $first = $service->create($deal, $owner, [], [['description' => 'A', 'quantity' => 1, 'unit_price' => 1000]]);
    $second = $service->create($deal, $owner, [], [['description' => 'B', 'quantity' => 1, 'unit_price' => 800]]);
    $service->changeStatus($first, OfferStatus::Sent);

    $this->actingAs($owner)->patchJson("/offers/{$second->id}/status", ['status' => 'accepted'])
        ->assertOk()->assertJson(['status' => 'accepted']);

    $deal->refresh();

    expect($deal->stage)->toBe(DealStage::Won)
        ->and((float) $deal->value)->toBe(800.0)
        ->and($deal->closed_at)->not->toBeNull()
        ->and($first->fresh()->status)->toBe(OfferStatus::Rejected);
});

it('only allows editing drafts', function () {
    $owner = salesRep();
    $offer = Offer::factory()->for(Deal::factory()->for($owner, 'owner'))->status(OfferStatus::Sent)->create();

    $this->actingAs($owner)->get("/offers/{$offer->id}/edit")->assertRedirect("/offers/{$offer->id}");
    $this->actingAs($owner)->put("/offers/{$offer->id}", offerPayload($offer->deal))->assertForbidden();
});

it('creates a revision with copied items and a suffixed number', function () {
    $owner = salesRep();
    $deal = Deal::factory()->for($owner, 'owner')->create();
    $offer = app(OfferService::class)->create($deal, $owner, [], [
        ['description' => 'A', 'quantity' => 2, 'unit_price' => 100],
    ]);
    $offer->update(['status' => OfferStatus::Rejected]);

    $this->actingAs($owner)->post("/offers/{$offer->id}/revision")->assertRedirect();

    $revision = Offer::where('parent_id', $offer->id)->with('items')->first();

    expect($revision->number)->toBe($offer->number.'-R2')
        ->and($revision->revision)->toBe(2)
        ->and($revision->status)->toBe(OfferStatus::Draft)
        ->and($revision->items)->toHaveCount(1)
        ->and((float) $revision->total)->toBe(250.0);
});

it('exports an offer as PDF in both languages', function (string $lang) {
    $owner = salesRep();
    $deal = Deal::factory()->for($owner, 'owner')->create();
    $offer = app(OfferService::class)->create($deal, $owner, [], [['description' => 'Razvoj aplikacije – čćžšđ', 'quantity' => 1, 'unit_price' => 100]]);

    $response = $this->actingAs($owner)->get("/offers/{$offer->id}/pdf?lang={$lang}");

    $response->assertOk()->assertHeader('content-type', 'application/pdf');
    expect($response->headers->get('content-disposition'))->toContain($offer->number.'.pdf');
})->with(['en', 'hr']);

it('expires sent offers past their validity date', function () {
    $expired = Offer::factory()->status(OfferStatus::Sent)->create(['valid_until' => now()->subDay()]);
    $valid = Offer::factory()->status(OfferStatus::Sent)->create(['valid_until' => now()->addDay()]);
    $draft = Offer::factory()->status(OfferStatus::Draft)->create(['valid_until' => now()->subDay()]);

    expect(Artisan::call('offers:expire'))->toBe(0);

    expect($expired->fresh()->status)->toBe(OfferStatus::Expired)
        ->and($valid->fresh()->status)->toBe(OfferStatus::Sent)
        ->and($draft->fresh()->status)->toBe(OfferStatus::Draft);
});

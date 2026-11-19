<?php

namespace App\Services;

use App\Enums\DealStage;
use App\Enums\OfferStatus;
use App\Models\Deal;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * All offer business rules live here so web controllers, the API and the
 * scheduler behave the same way.
 */
class OfferService
{
    public function __construct(private readonly OfferNumberGenerator $numbers) {}

    /**
     * @param  array<string, mixed>  $data  validated offer fields
     * @param  list<array<string, mixed>>  $items
     */
    public function create(Deal $deal, User $owner, array $data, array $items): Offer
    {
        return DB::transaction(function () use ($deal, $owner, $data, $items) {
            $offer = new Offer([
                'issued_at' => $data['issued_at'] ?? now()->toDateString(),
                'valid_until' => $data['valid_until'] ?? now()->addDays(config('crm.offer_validity_days'))->toDateString(),
                'vat_rate' => $data['vat_rate'] ?? config('crm.vat_rate'),
                'currency' => config('crm.currency'),
                'notes' => $data['notes'] ?? null,
                'status' => OfferStatus::Draft,
            ]);

            $offer->deal()->associate($deal);
            $offer->company_id = $deal->company_id;
            $offer->owner()->associate($owner);
            $offer->number = $this->numbers->next();
            $offer->save();

            $this->syncItems($offer, $items, quiet: true);

            // Preparing an offer means the deal is at least in the proposal stage.
            $deal->advanceTo(DealStage::Proposal);

            return $offer;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>  $items
     */
    public function update(Offer $offer, array $data, array $items): Offer
    {
        return DB::transaction(function () use ($offer, $data, $items) {
            $offer->fill([
                'issued_at' => $data['issued_at'],
                'valid_until' => $data['valid_until'] ?? null,
                'vat_rate' => $data['vat_rate'],
                'notes' => $data['notes'] ?? null,
            ]);

            $this->syncItems($offer, $items);

            return $offer;
        });
    }

    /**
     * Replace the line items and recalculate all totals server-side
     * (never trust totals coming from the browser).
     *
     * @param  list<array<string, mixed>>  $items
     */
    public function syncItems(Offer $offer, array $items, bool $quiet = false): void
    {
        $offer->items()->delete();

        $subtotal = 0.0;

        foreach (array_values($items) as $position => $item) {
            $lineTotal = self::lineTotal((float) $item['quantity'], (float) $item['unit_price'], (float) ($item['discount_percent'] ?? 0));
            $subtotal += $lineTotal;

            $offer->items()->create([
                'position' => $position,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? 'pcs',
                'unit_price' => $item['unit_price'],
                'discount_percent' => $item['discount_percent'] ?? 0,
                'line_total' => $lineTotal,
            ]);
        }

        $vat = round($subtotal * (float) $offer->vat_rate / 100, 2);

        $offer->forceFill([
            'subtotal' => round($subtotal, 2),
            'vat_amount' => $vat,
            'total' => round($subtotal + $vat, 2),
        ]);

        // Right after creation the totals are part of the "created" entry, not a separate change.
        $quiet ? $offer->saveQuietly() : $offer->save();

        $offer->unsetRelation('items');
    }

    public static function lineTotal(float $quantity, float $unitPrice, float $discountPercent = 0): float
    {
        return round($quantity * $unitPrice * (1 - $discountPercent / 100), 2);
    }

    /**
     * Change the status and apply the side effects on the deal:
     *  - sent      → deal moves (forward only) to "proposal"
     *  - accepted  → deal is won, other open offers of the deal are rejected
     */
    public function changeStatus(Offer $offer, OfferStatus $status): Offer
    {
        if ($offer->status === $status) {
            return $offer;
        }

        return DB::transaction(function () use ($offer, $status) {
            $offer->status = $status;

            if ($status === OfferStatus::Sent) {
                $offer->sent_at ??= now();
            }

            $offer->save();

            $deal = $offer->deal;

            if ($deal && ! $deal->trashed()) {
                match ($status) {
                    OfferStatus::Sent => $deal->advanceTo(DealStage::Proposal),
                    OfferStatus::Accepted => $this->acceptOffer($offer, $deal),
                    default => null,
                };
            }

            return $offer;
        });
    }

    private function acceptOffer(Offer $offer, Deal $deal): void
    {
        $deal->offers()
            ->whereKeyNot($offer->getKey())
            ->whereIn('status', [OfferStatus::Draft, OfferStatus::Sent])
            ->get()
            ->each(fn (Offer $other) => $other->update(['status' => OfferStatus::Rejected]));

        if ($deal->isOpen()) {
            $deal->value = $offer->subtotal;
            $deal->moveTo(DealStage::Won);
        }
    }

    /** Copy an offer (with items) into a new draft revision, e.g. after negotiation. */
    public function createRevision(Offer $offer, User $owner): Offer
    {
        return DB::transaction(function () use ($offer, $owner) {
            $root = $offer->parent_id ? $offer->parent()->first() : $offer;
            $revision = (int) Offer::withTrashed()
                ->where(fn ($q) => $q->whereKey($root->id)->orWhere('parent_id', $root->id))
                ->max('revision') + 1;

            $copy = $offer->replicate(['number', 'status', 'sent_at', 'revision', 'parent_id']);
            $copy->fill([
                'number' => $this->numbers->forRevision($root, $revision),
                'revision' => $revision,
                'parent_id' => $root->id,
                'status' => OfferStatus::Draft,
                'issued_at' => now()->toDateString(),
                'valid_until' => now()->addDays(config('crm.offer_validity_days'))->toDateString(),
            ]);
            $copy->owner()->associate($owner);
            $copy->save();

            $this->syncItems($copy, $offer->items()->get()->map(fn ($item) => $item->only([
                'description', 'quantity', 'unit', 'unit_price', 'discount_percent',
            ]))->all(), quiet: true);

            return $copy;
        });
    }

    /** Mark sent offers whose validity date has passed as expired. Returns the count. */
    public function expireOverdue(): int
    {
        $count = 0;

        Offer::query()
            ->where('status', OfferStatus::Sent)
            ->whereNotNull('valid_until')
            ->whereDate('valid_until', '<', now()->toDateString())
            ->with('deal')
            ->each(function (Offer $offer) use (&$count) {
                $this->changeStatus($offer, OfferStatus::Expired);
                $count++;
            });

        return $count;
    }
}

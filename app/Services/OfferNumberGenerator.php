<?php

namespace App\Services;

use App\Models\Offer;
use Illuminate\Support\Facades\DB;

/**
 * Generates sequential, per-year offer numbers: OFF-2026-0001, OFF-2026-0002, ...
 *
 * The previous implementation used max(id)+1, which breaks with soft deletes and
 * concurrent requests. Here the sequence is derived from existing numbers inside
 * a locked transaction, and revisions reuse the base number with an -R suffix.
 */
class OfferNumberGenerator
{
    public function next(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        $prefix = "OFF-{$year}-";

        return DB::transaction(function () use ($prefix) {
            $last = Offer::withTrashed()
                ->where('number', 'like', $prefix.'%')
                ->where('revision', 1)
                ->lockForUpdate()
                ->orderByDesc('number')
                ->value('number');

            $sequence = $last ? ((int) substr($last, strlen($prefix), 4)) + 1 : 1;

            return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        });
    }

    public function forRevision(Offer $original, int $revision): string
    {
        $base = preg_replace('/-R\d+$/', '', $original->number);

        return "{$base}-R{$revision}";
    }
}

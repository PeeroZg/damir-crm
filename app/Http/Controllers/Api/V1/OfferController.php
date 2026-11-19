<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OfferController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $offers = Offer::query()
            ->when(OfferStatus::tryFrom((string) $request->query('status')), fn (Builder $q, $s) => $q->where('status', $s))
            ->when($request->integer('deal_id'), fn (Builder $q, $id) => $q->where('deal_id', $id))
            ->latest('issued_at')
            ->paginate(min($request->integer('per_page', 25), 100));

        return OfferResource::collection($offers);
    }

    public function show(Offer $offer): OfferResource
    {
        return new OfferResource($offer->load('items'));
    }
}

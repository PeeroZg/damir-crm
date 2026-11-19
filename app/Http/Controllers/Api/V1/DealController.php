<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DealStage;
use App\Http\Controllers\Controller;
use App\Http\Requests\DealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $deals = Deal::query()
            ->search($request->query('q'))
            ->when(DealStage::tryFrom((string) $request->query('stage')), fn (Builder $q, $s) => $q->where('stage', $s))
            ->when($request->boolean('mine'), fn (Builder $q) => $q->where('owner_id', $request->user()->id))
            ->with(['company', 'owner'])
            ->latest()
            ->paginate(min($request->integer('per_page', 25), 100));

        return DealResource::collection($deals);
    }

    public function show(Deal $deal): DealResource
    {
        return new DealResource($deal->load(['company', 'contact', 'owner', 'offers']));
    }

    public function store(DealRequest $request): JsonResponse
    {
        $deal = Deal::create(['owner_id' => $request->user()->id] + collect($request->validated())->except('owner_id')->all());

        return (new DealResource($deal->load(['company', 'owner'])))
            ->response()
            ->setStatusCode(201);
    }

    public function updateStage(Request $request, Deal $deal): DealResource
    {
        $this->authorize('update', $deal);

        $data = $request->validate([
            'stage' => ['required', Rule::enum(DealStage::class)],
            'lost_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $deal->moveTo(DealStage::from($data['stage']), $data['lost_reason'] ?? null);

        return new DealResource($deal->load(['company', 'owner']));
    }
}

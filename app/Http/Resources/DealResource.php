<?php

namespace App\Http\Resources;

use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Deal */
class DealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'value' => (float) $this->value,
            'stage' => $this->stage->value,
            'probability' => $this->stage->probability(),
            'expected_close_date' => $this->expected_close_date?->toDateString(),
            'lost_reason' => $this->lost_reason,
            'closed_at' => $this->closed_at?->toIso8601String(),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'contact' => new ContactResource($this->whenLoaded('contact')),
            'owner' => new UserResource($this->whenLoaded('owner')),
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Contact */
class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'status' => $this->status->value,
            'position' => $this->position,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => new CompanyResource($this->whenLoaded('company')),
        ];
    }
}

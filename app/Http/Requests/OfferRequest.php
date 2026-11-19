<?php

namespace App\Http\Requests;

use App\Models\Deal;
use App\Models\Offer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($offer = $this->route('offer')) {
            return $this->user()->can('update', $offer);
        }

        // A missing/unknown deal is reported by validation instead.
        $deal = Deal::find($this->integer('deal_id'));

        return $deal === null || $this->user()->can('create', [Offer::class, $deal]);
    }

    protected function prepareForValidation(): void
    {
        // Drop completely empty rows the user added but never filled in.
        $items = collect($this->input('items', []))
            ->filter(fn ($item) => is_array($item) && trim((string) ($item['description'] ?? '')) !== '')
            ->values()
            ->all();

        $this->merge(['items' => $items]);
    }

    public function rules(): array
    {
        $creating = $this->route('offer') === null;

        return [
            'deal_id' => [Rule::requiredIf($creating), Rule::exists('deals', 'id')->whereNull('deleted_at')],
            'issued_at' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'vat_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:999999'],
            'items.*.unit' => ['nullable', 'string', 'max:20'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'deal_id' => __('deal'),
            'issued_at' => __('issue date'),
            'valid_until' => __('valid until'),
            'vat_rate' => __('VAT rate'),
            'items' => __('line items'),
            'items.*.description' => __('description'),
            'items.*.quantity' => __('quantity'),
            'items.*.unit_price' => __('unit price'),
            'items.*.discount_percent' => __('discount'),
        ];
    }
}

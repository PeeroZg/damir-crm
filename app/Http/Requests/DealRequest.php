<?php

namespace App\Http\Requests;

use App\Enums\DealStage;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class DealRequest extends FormRequest
{
    /** Authorize before validating, so forbidden users never see validation details. */
    public function authorize(): bool
    {
        $deal = $this->route('deal');

        return $deal ? $this->user()->can('update', $deal) : $this->user()->can('create', Deal::class);
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', Rule::exists('companies', 'id')->whereNull('deleted_at')],
            'contact_id' => ['nullable', Rule::exists('contacts', 'id')->whereNull('deleted_at')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'value' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'stage' => ['required', Rule::enum(DealStage::class)],
            'expected_close_date' => ['nullable', 'date'],
            'lost_reason' => ['nullable', 'string', 'max:255'],
            'owner_id' => ['nullable', 'exists:users,id'],
        ];
    }

    /** The contact must work at the selected company. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->filled('contact_id') || $validator->errors()->has('company_id')) {
                    return;
                }

                $belongs = Contact::whereKey($this->input('contact_id'))
                    ->where('company_id', $this->input('company_id'))
                    ->exists();

                if (! $belongs) {
                    $validator->errors()->add('contact_id', __('The selected contact does not belong to this company.'));
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'company_id' => __('company'),
            'contact_id' => __('contact'),
            'title' => __('title'),
            'value' => __('value'),
            'stage' => __('stage'),
            'expected_close_date' => __('expected close date'),
        ];
    }
}

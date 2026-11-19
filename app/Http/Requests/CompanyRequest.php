<?php

namespace App\Http\Requests;

use App\Enums\CompanyStatus;
use App\Models\Company;
use App\Rules\Oib;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->route('company');

        return $company ? $this->user()->can('update', $company) : $this->user()->can('create', Company::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'oib' => $this->filled('oib') ? preg_replace('/\D/', '', (string) $this->input('oib')) : null,
            'website' => $this->normalizeUrl($this->input('website')),
        ]);
    }

    public function rules(): array
    {
        $company = $this->route('company');

        return [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(CompanyStatus::class)],
            'oib' => ['nullable', 'digits:11', new Oib, Rule::unique('companies', 'oib')->ignore($company)],
            'industry' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('name'),
            'oib' => 'OIB',
            'owner_id' => __('account owner'),
        ];
    }

    private function normalizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        return preg_match('#^https?://#i', $url) ? $url : 'https://'.$url;
    }
}

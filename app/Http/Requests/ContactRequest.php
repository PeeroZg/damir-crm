<?php

namespace App\Http\Requests;

use App\Enums\ContactStatus;
use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        $contact = $this->route('contact');

        return $contact ? $this->user()->can('update', $contact) : $this->user()->can('create', Contact::class);
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', Rule::exists('companies', 'id')->whereNull('deleted_at')],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::enum(ContactStatus::class)],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'company_id' => __('company'),
            'first_name' => __('first name'),
            'last_name' => __('last name'),
        ];
    }
}

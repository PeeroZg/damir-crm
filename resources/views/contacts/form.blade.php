@php $statuses = App\Enums\ContactStatus::cases(); @endphp
<div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
    <x-field :label="__('First name')" name="first_name" required class="sm:col-span-3">
        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $contact->first_name) }}" required maxlength="100" @class(['input', 'input-error' => $errors->has('first_name')]) autofocus>
    </x-field>
    <x-field :label="__('Last name')" name="last_name" required class="sm:col-span-3">
        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $contact->last_name) }}" required maxlength="100" @class(['input', 'input-error' => $errors->has('last_name')])>
    </x-field>
    <x-field :label="__('Company')" name="company_id" required class="sm:col-span-4">
        <select id="company_id" name="company_id" required @class(['select', 'input-error' => $errors->has('company_id')])>
            <option value="">{{ __('Select a company…') }}</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected((int) old('company_id', $contact->company_id) === $company->id)>{{ $company->name }}</option>
            @endforeach
        </select>
    </x-field>
    <x-field :label="__('Status')" name="status" required class="sm:col-span-2">
        <select id="status" name="status" class="select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $contact->status?->value) === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </x-field>
    <x-field :label="__('Position')" name="position" class="sm:col-span-6">
        <input id="position" name="position" type="text" value="{{ old('position', $contact->position) }}" maxlength="255" class="input" placeholder="{{ __('e.g. Head of procurement') }}">
    </x-field>
    <x-field :label="__('Email')" name="email" class="sm:col-span-3">
        <input id="email" name="email" type="email" value="{{ old('email', $contact->email) }}" maxlength="255" @class(['input', 'input-error' => $errors->has('email')])>
    </x-field>
    <x-field :label="__('Phone')" name="phone" class="sm:col-span-3">
        <input id="phone" name="phone" type="tel" value="{{ old('phone', $contact->phone) }}" maxlength="50" class="input tnum">
    </x-field>
</div>

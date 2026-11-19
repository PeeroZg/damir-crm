@php $statuses = App\Enums\CompanyStatus::cases(); @endphp
<div class="grid gap-5 sm:grid-cols-6">
    <x-field :label="__('Company name')" name="name" required class="sm:col-span-4">
        <input id="name" name="name" type="text" value="{{ old('name', $company->name) }}" required maxlength="255" class="form-input" autofocus>
    </x-field>

    <x-field :label="__('Status')" name="status" required class="sm:col-span-2">
        <select id="status" name="status" class="form-input">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $company->status?->value) === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </x-field>

    <x-field label="OIB" name="oib" class="sm:col-span-3" :hint="__('11 digits, the check digit is validated.')">
        <input id="oib" name="oib" type="text" inputmode="numeric" value="{{ old('oib', $company->oib) }}" maxlength="13" class="form-input font-mono">
    </x-field>

    <x-field :label="__('Industry')" name="industry" class="sm:col-span-3">
        <input id="industry" name="industry" type="text" value="{{ old('industry', $company->industry) }}" maxlength="100" class="form-input" list="industries">
        <datalist id="industries">
            @foreach (['Software', 'Retail', 'Logistics', 'Tourism', 'Hospitality', 'Manufacturing', 'Construction', 'Healthcare', 'Finance', 'Energy', 'Agriculture', 'Food & beverage'] as $industry)
                <option value="{{ $industry }}">
            @endforeach
        </datalist>
    </x-field>

    <x-field :label="__('Email')" name="email" class="sm:col-span-3">
        <input id="email" name="email" type="email" value="{{ old('email', $company->email) }}" maxlength="255" class="form-input">
    </x-field>

    <x-field :label="__('Phone')" name="phone" class="sm:col-span-3">
        <input id="phone" name="phone" type="tel" value="{{ old('phone', $company->phone) }}" maxlength="50" class="form-input">
    </x-field>

    <x-field :label="__('Website')" name="website" class="sm:col-span-6">
        <input id="website" name="website" type="text" value="{{ old('website', $company->website) }}" maxlength="255" class="form-input" placeholder="www.example.hr">
    </x-field>

    <x-field :label="__('Address')" name="address" class="sm:col-span-4">
        <input id="address" name="address" type="text" value="{{ old('address', $company->address) }}" maxlength="255" class="form-input">
    </x-field>

    <x-field :label="__('City')" name="city" class="sm:col-span-2">
        <input id="city" name="city" type="text" value="{{ old('city', $company->city) }}" maxlength="100" class="form-input">
    </x-field>

    <x-field :label="__('Account owner')" name="owner_id" class="sm:col-span-3">
        <select id="owner_id" name="owner_id" class="form-input">
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((int) old('owner_id', $company->owner_id) === $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </x-field>

    <x-field :label="__('Notes')" name="notes" class="sm:col-span-6">
        <textarea id="notes" name="notes" rows="3" maxlength="5000" class="form-input">{{ old('notes', $company->notes) }}</textarea>
    </x-field>
</div>

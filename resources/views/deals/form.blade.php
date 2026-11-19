@php
    $stages = App\Enums\DealStage::cases();
    $contactOptions = $contacts->map(fn ($c) => ['id' => $c->id, 'company_id' => $c->company_id, 'name' => $c->full_name])->values();
@endphp
<div class="grid gap-5 sm:grid-cols-6"
     x-data="{ companyId: @js((string) old('company_id', $deal->company_id)), contactId: @js((string) old('contact_id', $deal->contact_id)), stage: @js(old('stage', $deal->stage?->value)), contacts: @js($contactOptions) }">
    <x-field :label="__('Title')" name="title" required class="sm:col-span-6">
        <input id="title" name="title" type="text" value="{{ old('title', $deal->title) }}" required maxlength="255" class="form-input" placeholder="{{ __('e.g. Website redesign') }}" autofocus>
    </x-field>

    <x-field :label="__('Company')" name="company_id" required class="sm:col-span-3">
        <select id="company_id" name="company_id" x-model="companyId" @change="contactId = ''" class="form-input" required>
            <option value="">{{ __('Select a company…') }}</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>
    </x-field>

    <x-field :label="__('Contact person')" name="contact_id" class="sm:col-span-3">
        <select id="contact_id" name="contact_id" x-model="contactId" class="form-input" :disabled="!companyId">
            <option value="">{{ __('— none —') }}</option>
            <template x-for="contact in contacts.filter(c => String(c.company_id) === String(companyId))" :key="contact.id">
                <option :value="String(contact.id)" x-text="contact.name" :selected="String(contact.id) === contactId"></option>
            </template>
        </select>
    </x-field>

    <x-field :label="__('Value (excl. VAT)')" name="value" required class="sm:col-span-2">
        <div class="relative">
            <input id="value" name="value" type="number" step="0.01" min="0" value="{{ old('value', $deal->value) }}" required class="form-input pr-8">
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">€</span>
        </div>
    </x-field>

    <x-field :label="__('Stage')" name="stage" required class="sm:col-span-2">
        <select id="stage" name="stage" x-model="stage" class="form-input">
            @foreach ($stages as $stage)
                <option value="{{ $stage->value }}">{{ $stage->label() }} ({{ $stage->probability() }} %)</option>
            @endforeach
        </select>
    </x-field>

    <x-field :label="__('Expected close')" name="expected_close_date" class="sm:col-span-2">
        <input id="expected_close_date" name="expected_close_date" type="date" value="{{ old('expected_close_date', $deal->expected_close_date?->toDateString()) }}" class="form-input">
    </x-field>

    <x-field :label="__('Lost reason')" name="lost_reason" class="sm:col-span-6" x-show="stage === 'lost'" x-cloak>
        <input id="lost_reason" name="lost_reason" type="text" value="{{ old('lost_reason', $deal->lost_reason) }}" maxlength="255" class="form-input">
    </x-field>

    @can('reassign', App\Models\Deal::class)
        <x-field :label="__('Owner')" name="owner_id" class="sm:col-span-3">
            <select id="owner_id" name="owner_id" class="form-input">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((int) old('owner_id', $deal->owner_id ?? auth()->id()) === $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </x-field>
    @endcan

    <x-field :label="__('Description')" name="description" class="sm:col-span-6">
        <textarea id="description" name="description" rows="4" maxlength="5000" class="form-input">{{ old('description', $deal->description) }}</textarea>
    </x-field>
</div>

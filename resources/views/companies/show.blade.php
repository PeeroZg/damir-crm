@php
    $openDeals = $company->deals->filter->isOpen();
    $wonValue = $company->deals->where('stage', App\Enums\DealStage::Won)->sum('value');
@endphp
<x-layouts.app :title="$company->name">
    <x-page-header :title="$company->name" :back="['url' => route('companies.index'), 'label' => __('Companies')]">
        <x-slot:subtitle>
            <x-status :value="$company->status" />
            @if ($company->industry)<span>{{ $company->industry }}</span>@endif
            @if ($company->city)<span>· {{ $company->city }}</span>@endif
        </x-slot:subtitle>
        <x-slot:actions>
            <x-button variant="secondary" icon="pencil" :href="route('companies.edit', $company)">{{ __('Edit') }}</x-button>
            <x-button icon="plus" :href="route('deals.create', ['company_id' => $company->id])">{{ __('New deal') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat :label="__('Open deals')" :value="$openDeals->count()" :hint="money($openDeals->sum('value'))" icon="briefcase" />
        <x-stat :label="__('Won revenue')" :value="money_compact($wonValue)" icon="trending" tone="emerald" />
        <x-stat :label="__('Contacts')" :value="$company->contacts->count()" icon="users" tone="sky" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-card :title="__('Details')">
                <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">OIB</dt><dd class="mt-0.5 font-mono text-slate-900">{{ $company->oib ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">{{ __('Account owner') }}</dt><dd class="mt-0.5 text-slate-900">{{ $company->owner?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">{{ __('Email') }}</dt><dd class="mt-0.5">@if ($company->email)<a href="mailto:{{ $company->email }}" class="link">{{ $company->email }}</a>@else — @endif</dd></div>
                    <div><dt class="text-slate-500">{{ __('Phone') }}</dt><dd class="mt-0.5">@if ($company->phone)<a href="tel:{{ $company->phone }}" class="link">{{ $company->phone }}</a>@else — @endif</dd></div>
                    <div><dt class="text-slate-500">{{ __('Website') }}</dt><dd class="mt-0.5">@if ($company->website)<a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="link">{{ preg_replace('#^https?://#', '', $company->website) }}</a>@else — @endif</dd></div>
                    <div><dt class="text-slate-500">{{ __('Address') }}</dt><dd class="mt-0.5 text-slate-900">{{ collect([$company->address, $company->city])->filter()->implode(', ') ?: '—' }}</dd></div>
                    @if ($company->notes)
                        <div class="sm:col-span-2"><dt class="text-slate-500">{{ __('Notes') }}</dt><dd class="mt-0.5 whitespace-pre-line text-slate-900">{{ $company->notes }}</dd></div>
                    @endif
                </dl>
            </x-card>

            <x-card :title="__('Deals')" :padding="false">
                <x-slot:actions><x-button size="sm" variant="secondary" icon="plus" :href="route('deals.create', ['company_id' => $company->id])">{{ __('Add') }}</x-button></x-slot:actions>
                @include('deals.table', ['deals' => $company->deals, 'showCompany' => false])
            </x-card>

            <x-card :title="__('Contacts')" :padding="false">
                <x-slot:actions><x-button size="sm" variant="secondary" icon="plus" :href="route('contacts.create', ['company_id' => $company->id])">{{ __('Add') }}</x-button></x-slot:actions>
                @if ($company->contacts->isEmpty())
                    <x-empty-state icon="users" :title="__('No contacts yet')" />
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($company->contacts as $contact)
                            <li class="flex items-center gap-3 px-5 py-3">
                                <x-avatar :name="$contact->full_name" />
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-medium text-slate-900 hover:text-brand-700">{{ $contact->full_name }}</a>
                                    <p class="text-xs text-slate-500">{{ $contact->position }}</p>
                                </div>
                                <div class="hidden text-right text-xs text-slate-500 sm:block">
                                    @if ($contact->email)<a href="mailto:{{ $contact->email }}" class="block hover:text-brand-700">{{ $contact->email }}</a>@endif
                                    {{ $contact->phone }}
                                </div>
                                <x-status :value="$contact->status" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>

            <x-card :title="__('Offers')" :padding="false">
                @include('offers.table', ['offers' => $company->offers, 'showCompany' => false])
            </x-card>
        </div>

        <div class="space-y-6">
            @include('partials.activity-panel', ['subject' => $company])
            @include('partials.history', ['logs' => $company->auditLogs])
            @can('delete', $company)
                <div class="flex justify-end">
                    <x-delete-button :action="route('companies.destroy', $company)" :label="__('Delete company')" />
                </div>
            @endcan
        </div>
    </div>
</x-layouts.app>

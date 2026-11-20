@php
    $openDeals = $company->deals->filter->isOpen();
    $wonValue = $company->deals->where('stage', App\Enums\DealStage::Won)->sum('value');
@endphp
<x-layouts.app :title="$company->name">
<div class="mx-auto max-w-[1280px]">
    <x-page-header :title="$company->name" :back="['url' => route('companies.index'), 'label' => __('Companies')]">
        <x-slot:subtitle>
            <x-status :value="$company->status" />
            @if ($company->industry)<span>{{ $company->industry }}</span>@endif
            @if ($company->city)<span class="text-ink-300">·</span><span class="inline-flex items-center gap-1.5">{{ $company->city }}</span>@endif
        </x-slot:subtitle>
        <x-slot:actions>
            <x-button variant="secondary" icon="pencil" :href="route('companies.edit', $company)">{{ __('Edit') }}</x-button>
            <x-button icon="plus" :href="route('deals.create', ['company_id' => $company->id])">{{ __('New deal') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="relative overflow-hidden rounded-xl bg-ink-900 p-4 text-white shadow-card sm:p-5">
            <div class="absolute inset-0 opacity-[0.35] [background-image:radial-gradient(rgba(255,255,255,.14)_1px,transparent_1px)] [background-size:16px_16px]" aria-hidden="true"></div>
            <div class="relative">
                <div class="flex items-center justify-between"><p class="text-sm font-medium text-ink-300">{{ __('Open deals') }}</p><span class="flex h-7 w-7 items-center justify-center rounded-md bg-volt-400 text-ink-900"><x-icon name="briefcase" class="h-4 w-4" /></span></div>
                <p class="mt-3 font-display text-[26px] font-bold leading-none tracking-tight tnum">{{ $openDeals->count() }}</p>
                <p class="mt-2 text-sm text-ink-300">{{ money($openDeals->sum('value')) }}</p>
            </div>
        </div>
        <x-stat :label="__('Won revenue')" :value="money_compact($wonValue)" icon="trophy" tone="won" />
        <x-stat :label="__('Contacts')" :value="$company->contacts->count()" icon="users" />
    </div>

    <div class="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_400px]">
        <div class="flex min-w-0 flex-col gap-3">
            <section class="card">
                <div class="card-header"><h2 class="card-title">{{ __('Details') }}</h2></div>
                <dl class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2">
                    <div><dt class="meta-label">OIB</dt><dd class="meta-value font-mono">{{ $company->oib ?? '—' }}</dd></div>
                    <div><dt class="meta-label">{{ __('Account owner') }}</dt><dd class="meta-value">@include('partials.owner', ['user' => $company->owner])</dd></div>
                    <div><dt class="meta-label">{{ __('Email') }}</dt><dd class="mt-1">@if ($company->email)<a href="mailto:{{ $company->email }}" class="link inline-flex items-center gap-1.5"><x-icon name="mail" class="h-4 w-4 text-ink-400" />{{ $company->email }}</a>@else — @endif</dd></div>
                    <div><dt class="meta-label">{{ __('Phone') }}</dt><dd class="mt-1">@if ($company->phone)<a href="tel:{{ $company->phone }}" class="link inline-flex items-center gap-1.5 tnum"><x-icon name="phone" class="h-4 w-4 text-ink-400" />{{ $company->phone }}</a>@else — @endif</dd></div>
                    <div><dt class="meta-label">{{ __('Website') }}</dt><dd class="mt-1">@if ($company->website)<a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="link inline-flex items-center gap-1.5"><x-icon name="globe" class="h-4 w-4 text-ink-400" />{{ preg_replace('#^https?://#', '', $company->website) }}</a>@else — @endif</dd></div>
                    <div><dt class="meta-label">{{ __('Address') }}</dt><dd class="meta-value">{{ collect([$company->address, $company->city])->filter()->implode(', ') ?: '—' }}</dd></div>
                    @if ($company->notes)
                        <div class="sm:col-span-2"><dt class="meta-label">{{ __('Notes') }}</dt><dd class="mt-1 whitespace-pre-line rounded-lg bg-ink-25 p-3 text-base text-ink-800 ring-1 ring-inset ring-ink-100">{{ $company->notes }}</dd></div>
                    @endif
                </dl>
            </section>

            <section class="card overflow-hidden">
                <div class="card-header"><h2 class="card-title flex items-center gap-2">{{ __('Deals') }} <span class="count">{{ $company->deals->count() }}</span></h2><x-button size="sm" variant="secondary" icon="plus" :href="route('deals.create', ['company_id' => $company->id])">{{ __('Add') }}</x-button></div>
                @include('deals.table', ['deals' => $company->deals, 'showCompany' => false])
            </section>

            <section class="card overflow-hidden">
                <div class="card-header"><h2 class="card-title flex items-center gap-2">{{ __('Contacts') }} <span class="count">{{ $company->contacts->count() }}</span></h2><x-button size="sm" variant="secondary" icon="plus" :href="route('contacts.create', ['company_id' => $company->id])">{{ __('Add') }}</x-button></div>
                @if ($company->contacts->isEmpty())
                    <x-empty-state icon="users" :title="__('No contacts yet')" />
                @else
                    <ul class="divide-y divide-ink-100">
                        @foreach ($company->contacts as $contact)
                            <li class="flex items-center gap-3 px-5 py-3">
                                <x-avatar :name="$contact->full_name" size="lg" class="ring-0" />
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('contacts.show', $contact) }}" class="font-medium text-ink-900 hover:underline hover:decoration-volt-500 hover:decoration-2 hover:underline-offset-2">{{ $contact->full_name }}</a>
                                    <p class="text-xs text-ink-500">{{ $contact->position }}</p>
                                </div>
                                <div class="hidden text-right text-xs text-ink-500 sm:block">
                                    @if ($contact->email)<a href="mailto:{{ $contact->email }}" class="block hover:text-ink-900">{{ $contact->email }}</a>@endif
                                    <span class="tnum">{{ $contact->phone }}</span>
                                </div>
                                <x-status :value="$contact->status" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="card overflow-hidden">
                <div class="card-header"><h2 class="card-title flex items-center gap-2">{{ __('Offers') }} <span class="count">{{ $company->offers->count() }}</span></h2></div>
                @include('offers.table', ['offers' => $company->offers, 'showCompany' => false])
            </section>
        </div>

        <div class="flex flex-col gap-3">
            @include('partials.activity-panel', ['subject' => $company])
            @include('partials.history', ['logs' => $company->auditLogs])
            @can('delete', $company)
                <div class="flex justify-end"><x-delete-button :action="route('companies.destroy', $company)" :label="__('Delete company')" /></div>
            @endcan
        </div>
    </div>
</div>
</x-layouts.app>

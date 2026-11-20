<x-layouts.app :title="$contact->full_name">
<div class="mx-auto max-w-[1280px]">
    <a href="{{ route('contacts.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-ink-500 hover:text-ink-900"><x-icon name="arrow-left" class="h-4 w-4" />{{ __('Contacts') }}</a>
    <div class="mb-6 mt-2 flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <x-avatar :name="$contact->full_name" class="h-14 w-14 font-display text-lg ring-0" />
            <div class="min-w-0">
                <h1 class="page-title">{{ $contact->full_name }}</h1>
                <p class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-base text-ink-600">
                    <x-status :value="$contact->status" />
                    @if ($contact->position)<span>{{ $contact->position }}</span>@endif
                    @if ($contact->company)<span class="text-ink-300">·</span><a href="{{ route('companies.show', $contact->company) }}" class="inline-flex items-center gap-1.5 hover:text-ink-900"><x-icon name="building" class="h-4 w-4 text-ink-400" />{{ $contact->company->name }}</a>@endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <x-button variant="secondary" icon="pencil" :href="route('contacts.edit', $contact)">{{ __('Edit') }}</x-button>
            <x-button icon="plus" :href="route('deals.create', ['company_id' => $contact->company_id, 'contact_id' => $contact->id])">{{ __('New deal') }}</x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_400px]">
        <div class="flex min-w-0 flex-col gap-3">
            <section class="card">
                <div class="card-header"><h2 class="card-title">{{ __('Contact details') }}</h2></div>
                <dl class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2">
                    <div><dt class="meta-label">{{ __('Email') }}</dt><dd class="mt-1">@if ($contact->email)<a href="mailto:{{ $contact->email }}" class="link inline-flex items-center gap-1.5"><x-icon name="mail" class="h-4 w-4 text-ink-400" />{{ $contact->email }}</a>@else — @endif</dd></div>
                    <div><dt class="meta-label">{{ __('Phone') }}</dt><dd class="mt-1">@if ($contact->phone)<a href="tel:{{ $contact->phone }}" class="link inline-flex items-center gap-1.5 tnum"><x-icon name="phone" class="h-4 w-4 text-ink-400" />{{ $contact->phone }}</a>@else — @endif</dd></div>
                </dl>
            </section>

            <section class="card overflow-hidden">
                <div class="card-header"><h2 class="card-title flex items-center gap-2">{{ __('Deals') }} <span class="count">{{ $contact->deals->count() }}</span></h2></div>
                @include('deals.table', ['deals' => $contact->deals, 'showCompany' => false, 'emptyText' => __('When :name is attached to a deal it shows up here.', ['name' => $contact->first_name])])
            </section>
        </div>

        <div class="flex flex-col gap-3">
            @include('partials.activity-panel', ['subject' => $contact])
            @include('partials.history', ['logs' => $contact->auditLogs])
            @can('delete', $contact)
                <div class="flex justify-end"><x-delete-button :action="route('contacts.destroy', $contact)" :label="__('Delete contact')" /></div>
            @endcan
        </div>
    </div>
</div>
</x-layouts.app>

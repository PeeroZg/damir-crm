<x-layouts.app :title="$contact->full_name">
    <x-page-header :title="$contact->full_name" :back="['url' => route('contacts.index'), 'label' => __('Contacts')]">
        <x-slot:subtitle>
            <x-status :value="$contact->status" />
            @if ($contact->position)<span>{{ $contact->position }}</span>@endif
            @if ($contact->company)<span>· <a href="{{ route('companies.show', $contact->company) }}" class="hover:text-brand-700">{{ $contact->company->name }}</a></span>@endif
        </x-slot:subtitle>
        <x-slot:actions>
            <x-button variant="secondary" icon="pencil" :href="route('contacts.edit', $contact)">{{ __('Edit') }}</x-button>
            <x-button icon="plus" :href="route('deals.create', ['company_id' => $contact->company_id, 'contact_id' => $contact->id])">{{ __('New deal') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-card :title="__('Contact details')">
                <div class="flex items-center gap-4">
                    <x-avatar :name="$contact->full_name" size="lg" />
                    <dl class="grid flex-1 gap-4 text-sm sm:grid-cols-2">
                        <div><dt class="text-slate-500">{{ __('Email') }}</dt><dd class="mt-0.5">@if ($contact->email)<a href="mailto:{{ $contact->email }}" class="link">{{ $contact->email }}</a>@else — @endif</dd></div>
                        <div><dt class="text-slate-500">{{ __('Phone') }}</dt><dd class="mt-0.5">@if ($contact->phone)<a href="tel:{{ $contact->phone }}" class="link">{{ $contact->phone }}</a>@else — @endif</dd></div>
                    </dl>
                </div>
            </x-card>

            <x-card :title="__('Deals')" :padding="false">
                @include('deals.table', ['deals' => $contact->deals, 'showCompany' => false])
            </x-card>
        </div>

        <div class="space-y-6">
            @include('partials.activity-panel', ['subject' => $contact])
            @include('partials.history', ['logs' => $contact->auditLogs])
            @can('delete', $contact)
                <div class="flex justify-end"><x-delete-button :action="route('contacts.destroy', $contact)" :label="__('Delete contact')" /></div>
            @endcan
        </div>
    </div>
</x-layouts.app>

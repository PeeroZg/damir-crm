<x-layouts.app :title="__('Contacts')">
<div class="mx-auto max-w-[1280px]">
    <x-page-header :title="__('Contacts')">
        <x-slot:description>{{ __('The people behind your companies.') }}</x-slot:description>
        <x-slot:actions>
            <x-view-toggle :list="route('contacts.index')" :kanban="route('contacts.kanban')" active="list" />
            <x-button variant="secondary" icon="download" :href="route('contacts.export', request()->query())">CSV</x-button>
            <x-button icon="plus" :href="route('contacts.create')">{{ __('New contact') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <section class="card overflow-hidden">
        <x-filter-bar :action="route('contacts.index')" :placeholder="__('Search by name, e-mail or phone…')">
            <label for="status" class="sr-only">{{ __('Status') }}</label>
            <select id="status" name="status" class="select w-full sm:w-[170px]">
                <option value="">{{ __('All statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </x-filter-bar>

        @if ($contacts->isEmpty())
            <x-empty-state icon="users" :title="__('No contacts found')">
                <x-button icon="plus" :href="route('contacts.create')">{{ __('New contact') }}</x-button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table min-w-[860px]">
                    <thead>
                        <tr>
                            <th><x-sort-link column="last_name" :label="__('Name')" default="last_name" /></th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th><x-sort-link column="status" :label="__('Status')" default="last_name" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr>
                                <td>
                                    <a href="{{ route('contacts.show', $contact) }}" class="flex items-center gap-3">
                                        <x-avatar :name="$contact->full_name" size="lg" class="ring-0" />
                                        <span class="min-w-0"><span class="block truncate font-medium text-ink-900 hover:underline hover:decoration-volt-500 hover:decoration-2 hover:underline-offset-2">{{ $contact->full_name }}</span><span class="block truncate text-xs text-ink-500">{{ $contact->position }}</span></span>
                                    </a>
                                </td>
                                <td>@if ($contact->company)<a href="{{ route('companies.show', $contact->company) }}" class="link-quiet">{{ $contact->company->name }}</a>@endif</td>
                                <td>@if ($contact->email)<a href="mailto:{{ $contact->email }}" class="link-quiet">{{ $contact->email }}</a>@else<span class="text-ink-400">—</span>@endif</td>
                                <td class="tnum">{{ $contact->phone ?? '—' }}</td>
                                <td><x-status :value="$contact->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $contacts->links() }}
        @endif
    </section>
</div>
</x-layouts.app>

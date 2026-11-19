<x-layouts.app :title="__('Contacts')">
    <x-page-header :title="__('Contacts')" :subtitle="__('The people behind your companies.')">
        <x-slot:actions>
            <x-view-toggle :list="route('contacts.index')" :kanban="route('contacts.kanban')" active="list" />
            <x-button variant="secondary" icon="download" :href="route('contacts.export', request()->query())">CSV</x-button>
            <x-button icon="plus" :href="route('contacts.create')">{{ __('New contact') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false">
        <x-filter-bar :action="route('contacts.index')" :placeholder="__('Search by name, e-mail or phone…')">
            <select name="status" class="form-input sm:w-40" aria-label="{{ __('Status') }}">
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
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/60">
                        <tr>
                            <th class="table-th"><x-sort-link column="last_name" :label="__('Name')" default="last_name" /></th>
                            <th class="table-th">{{ __('Company') }}</th>
                            <th class="table-th">{{ __('Email') }}</th>
                            <th class="table-th">{{ __('Phone') }}</th>
                            <th class="table-th"><x-sort-link column="status" :label="__('Status')" default="last_name" /></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($contacts as $contact)
                            <tr class="hover:bg-slate-50/60">
                                <td class="table-td">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :name="$contact->full_name" />
                                        <div>
                                            <a href="{{ route('contacts.show', $contact) }}" class="font-medium text-slate-900 hover:text-brand-700">{{ $contact->full_name }}</a>
                                            <p class="text-xs text-slate-500">{{ $contact->position }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-td">@if ($contact->company)<a href="{{ route('companies.show', $contact->company) }}" class="hover:text-brand-700">{{ $contact->company->name }}</a>@endif</td>
                                <td class="table-td">@if ($contact->email)<a href="mailto:{{ $contact->email }}" class="hover:text-brand-700">{{ $contact->email }}</a>@else — @endif</td>
                                <td class="table-td">{{ $contact->phone ?? '—' }}</td>
                                <td class="table-td"><x-status :value="$contact->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $contacts->links() }}
        @endif
    </x-card>
</x-layouts.app>

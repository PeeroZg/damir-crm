<x-layouts.app :title="__('Companies')">
    <x-page-header :title="__('Companies')" :subtitle="__('Your customers, prospects and leads.')">
        <x-slot:actions>
            <x-view-toggle :list="route('companies.index')" :kanban="route('companies.kanban')" active="list" />
            <x-button variant="secondary" icon="download" :href="route('companies.export', request()->query())">CSV</x-button>
            <x-button icon="plus" :href="route('companies.create')">{{ __('New company') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false">
        <x-filter-bar :action="route('companies.index')" :placeholder="__('Search by name, OIB, e-mail or city…')">
            <select name="status" class="form-input sm:w-40" aria-label="{{ __('Status') }}">
                <option value="">{{ __('All statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <select name="owner" class="form-input sm:w-40" aria-label="{{ __('Owner') }}">
                <option value="">{{ __('All owners') }}</option>
                <option value="me" @selected(request('owner') === 'me')>{{ __('My accounts') }}</option>
            </select>
        </x-filter-bar>

        @if ($companies->isEmpty())
            <x-empty-state icon="building" :title="__('No companies found')" :description="__('Try a different search or add your first company.')">
                <x-button icon="plus" :href="route('companies.create')">{{ __('New company') }}</x-button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/60">
                        <tr>
                            <th class="table-th"><x-sort-link column="name" :label="__('Name')" default="name" /></th>
                            <th class="table-th"><x-sort-link column="status" :label="__('Status')" default="name" /></th>
                            <th class="table-th"><x-sort-link column="city" :label="__('City')" default="name" /></th>
                            <th class="table-th text-right">{{ __('Contacts') }}</th>
                            <th class="table-th text-right"><x-sort-link column="deals_count" :label="__('Deals')" default="name" /></th>
                            <th class="table-th text-right">{{ __('Won') }}</th>
                            <th class="table-th">{{ __('Owner') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($companies as $company)
                            <tr class="hover:bg-slate-50/60">
                                <td class="table-td">
                                    <a href="{{ route('companies.show', $company) }}" class="font-medium text-slate-900 hover:text-brand-700">{{ $company->name }}</a>
                                    <p class="text-xs text-slate-500">{{ $company->industry }}@if ($company->oib) · OIB {{ $company->oib }}@endif</p>
                                </td>
                                <td class="table-td"><x-status :value="$company->status" /></td>
                                <td class="table-td">{{ $company->city ?? '—' }}</td>
                                <td class="table-td text-right">{{ $company->contacts_count }}</td>
                                <td class="table-td text-right">{{ $company->deals_count }}</td>
                                <td class="table-td text-right font-medium">{{ $company->won_value ? money($company->won_value) : '—' }}</td>
                                <td class="table-td">
                                    @if ($company->owner)
                                        <span class="inline-flex items-center gap-2"><x-avatar :name="$company->owner->name" size="sm" /> {{ $company->owner->name }}</span>
                                    @else — @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $companies->links() }}
        @endif
    </x-card>
</x-layouts.app>

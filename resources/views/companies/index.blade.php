<x-layouts.app :title="__('Companies')">
<div class="mx-auto max-w-[1280px]">
    <x-page-header :title="__('Companies')">
        <x-slot:description>{{ __('Your customers, prospects and leads.') }}</x-slot:description>
        <x-slot:actions>
            <x-view-toggle :list="route('companies.index')" :kanban="route('companies.kanban')" active="list" />
            <x-button variant="secondary" icon="download" :href="route('companies.export', request()->query())">CSV</x-button>
            <x-button icon="plus" :href="route('companies.create')">{{ __('New company') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <section class="card overflow-hidden">
        <x-filter-bar :action="route('companies.index')" :placeholder="__('Search by name, OIB, e-mail or city…')">
            <label for="status" class="sr-only">{{ __('Status') }}</label>
            <select id="status" name="status" class="select w-full sm:w-[170px]">
                <option value="">{{ __('All statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <label for="owner" class="sr-only">{{ __('Owner') }}</label>
            <select id="owner" name="owner" class="select w-full sm:w-[170px]">
                <option value="">{{ __('All owners') }}</option>
                <option value="me" @selected(request('owner') === 'me')>{{ __('My accounts') }}</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(request('owner') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </x-filter-bar>

        @if ($companies->isEmpty())
            <x-empty-state icon="building" :title="__('No companies found')" :description="__('Try a different search or add your first company.')">
                <x-button icon="plus" :href="route('companies.create')">{{ __('New company') }}</x-button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table min-w-[920px]">
                    <thead>
                        <tr>
                            <th><x-sort-link column="name" :label="__('Name')" default="name" /></th>
                            <th><x-sort-link column="status" :label="__('Status')" default="name" /></th>
                            <th><x-sort-link column="city" :label="__('City')" default="name" /></th>
                            <th class="text-right">{{ __('Contacts') }}</th>
                            <th class="text-right"><x-sort-link column="deals_count" :label="__('Deals')" default="name" /></th>
                            <th class="text-right">{{ __('Won') }}</th>
                            <th>{{ __('Owner') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td>
                                    <a href="{{ route('companies.show', $company) }}" class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-ink-50 font-display text-sm font-bold text-ink-700 ring-1 ring-inset ring-ink-150">{{ mb_strtoupper(mb_substr($company->name, 0, 1)) }}</span>
                                        <span class="min-w-0"><span class="block truncate font-medium text-ink-900 hover:underline hover:decoration-volt-500 hover:decoration-2 hover:underline-offset-2">{{ $company->name }}</span><span class="block truncate text-xs text-ink-500">{{ $company->industry }}@if ($company->oib) · <span class="font-mono">OIB {{ $company->oib }}</span>@endif</span></span>
                                    </a>
                                </td>
                                <td><x-status :value="$company->status" /></td>
                                <td class="text-ink-700">{{ $company->city ?? '—' }}</td>
                                <td class="text-right tnum">{{ $company->contacts_count }}</td>
                                <td @class(['text-right tnum', 'text-ink-400' => ! $company->deals_count])>{{ $company->deals_count }}</td>
                                <td class="text-right tnum">@if ($company->won_value)<span class="font-semibold text-ink-900">{{ money($company->won_value) }}</span>@else<span class="text-ink-400">—</span>@endif</td>
                                <td>@include('partials.owner', ['user' => $company->owner])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $companies->links() }}
        @endif
    </section>
</div>
</x-layouts.app>

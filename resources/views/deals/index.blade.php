<x-layouts.app :title="__('Deals')">
    <x-page-header :title="__('Deals')" :subtitle="__('Every opportunity in the pipeline.')">
        <x-slot:actions>
            <x-view-toggle :list="route('deals.index')" :kanban="route('deals.kanban')" active="list" />
            <x-button variant="secondary" icon="download" :href="route('deals.export', request()->query())">CSV</x-button>
            <x-button icon="plus" :href="route('deals.create')">{{ __('New deal') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false">
        <x-filter-bar :action="route('deals.index')" :placeholder="__('Search by title or company…')">
            <select name="stage" class="form-input sm:w-40" aria-label="{{ __('Stage') }}">
                <option value="">{{ __('All stages') }}</option>
                <option value="open" @selected(request('stage') === 'open')>{{ __('Open only') }}</option>
                @foreach ($stages as $stage)
                    <option value="{{ $stage->value }}" @selected(request('stage') === $stage->value)>{{ $stage->label() }}</option>
                @endforeach
            </select>
            <select name="owner" class="form-input sm:w-44" aria-label="{{ __('Owner') }}">
                <option value="">{{ __('All owners') }}</option>
                <option value="me" @selected(request('owner') === 'me')>{{ __('My deals') }}</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(request('owner') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </x-filter-bar>

        @if ($deals->isEmpty())
            <x-empty-state icon="briefcase" :title="__('No deals found')">
                <x-button icon="plus" :href="route('deals.create')">{{ __('New deal') }}</x-button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/60">
                        <tr>
                            <th class="table-th"><x-sort-link column="title" :label="__('Deal')" /></th>
                            <th class="table-th"><x-sort-link column="stage" :label="__('Stage')" /></th>
                            <th class="table-th text-right"><x-sort-link column="value" :label="__('Value')" /></th>
                            <th class="table-th"><x-sort-link column="expected_close_date" :label="__('Expected close')" /></th>
                            <th class="table-th">{{ __('Owner') }}</th>
                            <th class="table-th"><x-sort-link column="created_at" :label="__('Created')" default="created_at" /></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($deals as $deal)
                            <tr class="hover:bg-slate-50/60">
                                <td class="table-td">
                                    <a href="{{ route('deals.show', $deal) }}" class="font-medium text-slate-900 hover:text-brand-700">{{ $deal->title }}</a>
                                    <p class="text-xs text-slate-500">{{ $deal->company?->name }}</p>
                                </td>
                                <td class="table-td"><x-status :value="$deal->stage" /></td>
                                <td class="table-td text-right font-medium">{{ money($deal->value) }}</td>
                                <td @class(['table-td', 'font-medium text-rose-600' => $deal->isOpen() && $deal->expected_close_date?->isPast()])>{{ format_date($deal->expected_close_date) }}</td>
                                <td class="table-td">
                                    @if ($deal->owner)<span class="inline-flex items-center gap-2"><x-avatar :name="$deal->owner->name" size="sm" /> {{ $deal->owner->name }}</span>@else — @endif
                                </td>
                                <td class="table-td text-slate-500">{{ format_date($deal->created_at) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $deals->links() }}
        @endif
    </x-card>
</x-layouts.app>

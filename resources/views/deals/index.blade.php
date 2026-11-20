<x-layouts.app :title="__('Deals')">
<div class="mx-auto max-w-[1280px]">
    <x-page-header :title="__('Deals')">
        <x-slot:description>{{ __('Every opportunity in the pipeline.') }}</x-slot:description>
        <x-slot:actions>
            <x-view-toggle :list="route('deals.index')" :kanban="route('deals.kanban')" active="list" />
            <x-button variant="secondary" icon="download" :href="route('deals.export', request()->query())">CSV</x-button>
            <x-button icon="plus" :href="route('deals.create')">{{ __('New deal') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <section class="card overflow-hidden">
        <x-filter-bar :action="route('deals.index')" :placeholder="__('Search by title or company…')">
            <label for="stage" class="sr-only">{{ __('Stage') }}</label>
            <select id="stage" name="stage" class="select w-full sm:w-[170px]">
                <option value="">{{ __('All stages') }}</option>
                <option value="open" @selected(request('stage') === 'open')>{{ __('Open only') }}</option>
                @foreach ($stages as $stage)
                    <option value="{{ $stage->value }}" @selected(request('stage') === $stage->value)>{{ $stage->label() }}</option>
                @endforeach
            </select>
            <label for="owner" class="sr-only">{{ __('Owner') }}</label>
            <select id="owner" name="owner" class="select w-full sm:w-[170px]">
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
                <table class="table min-w-[920px]">
                    <thead>
                        <tr>
                            <th><x-sort-link column="title" :label="__('Deal')" /></th>
                            <th><x-sort-link column="stage" :label="__('Stage')" /></th>
                            <th class="text-right"><x-sort-link column="value" :label="__('Value')" /></th>
                            <th><x-sort-link column="expected_close_date" :label="__('Expected close')" /></th>
                            <th>{{ __('Owner') }}</th>
                            <th><x-sort-link column="created_at" :label="__('Created')" default="created_at" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deals as $deal)
                            @php $late = $deal->isOpen() && $deal->expected_close_date?->isPast() && ! $deal->expected_close_date->isToday(); @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('deals.show', $deal) }}" class="font-medium text-ink-900 hover:underline hover:decoration-volt-500 hover:decoration-2 hover:underline-offset-2">{{ $deal->title }}</a>
                                    <p class="text-xs text-ink-500">{{ $deal->company?->name }}</p>
                                </td>
                                <td><x-status :value="$deal->stage" /></td>
                                <td class="text-right font-semibold tnum">{{ money($deal->value) }}</td>
                                <td @class(['tnum', 'font-medium text-lost-fg' => $late])>{{ format_date($deal->expected_close_date) }}</td>
                                <td>@include('partials.owner', ['user' => $deal->owner])</td>
                                <td class="tnum text-ink-500">{{ format_date($deal->created_at) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $deals->links() }}
        @endif
    </section>
</div>
</x-layouts.app>

@php
    $maxStage = max(1, $pipeline->max('value'));
    $maxMonth = max(1, $wonByMonth->max('value'));
@endphp
<x-layouts.app :title="__('Dashboard')">
    <x-page-header :title="__('Hi, :name', ['name' => Str::before(auth()->user()->name, ' ')]) . ' 👋'" :subtitle="__('Here is how the pipeline looks today.')">
        <x-slot:actions>
            <div class="inline-flex rounded-lg bg-slate-100 p-0.5">
                <a href="{{ route('dashboard') }}" @class(['rounded-md px-3 py-1.5 text-xs font-semibold', 'bg-white shadow-sm text-slate-900' => ! $mine, 'text-slate-500' => $mine])>{{ __('Whole team') }}</a>
                <a href="{{ route('dashboard', ['scope' => 'mine']) }}" @class(['rounded-md px-3 py-1.5 text-xs font-semibold', 'bg-white shadow-sm text-slate-900' => $mine, 'text-slate-500' => ! $mine])>{{ __('Only mine') }}</a>
            </div>
            <x-button :href="route('deals.create')" icon="plus">{{ __('New deal') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- KPIs --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat :label="__('Open pipeline')" :value="money_compact($kpis['open_value'])" icon="briefcase"
                :hint="trans_choice('{1} :count open deal|[2,*] :count open deals', $kpis['open_count'], ['count' => $kpis['open_count']])" />
        <x-stat :label="__('Weighted forecast')" :value="money_compact($kpis['weighted_value'])" icon="target" tone="sky"
                :hint="__('Value × stage probability')" />
        <x-stat :label="__('Won this year')" :value="money_compact($kpis['won_value_ytd'])" icon="trending" tone="emerald"
                :hint="$kpis['avg_won'] ? __('Avg. deal :value', ['value' => money_compact($kpis['avg_won'])]) : null" />
        <x-stat :label="__('Win rate')" :value="$kpis['win_rate'] !== null ? $kpis['win_rate'].' %' : '—'" icon="chart" tone="amber"
                :hint="trans_choice('{0} No offers waiting|{1} :count offer waiting for answer|[2,*] :count offers waiting for answer', $kpis['offers_pending'], ['count' => $kpis['offers_pending']])" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Pipeline by stage --}}
        <x-card :title="__('Pipeline by stage')" class="xl:col-span-2">
            <x-slot:actions><a href="{{ route('deals.kanban') }}" class="link text-xs">{{ __('Open board') }} →</a></x-slot:actions>
            <ul class="space-y-3">
                @foreach ($pipeline as $row)
                    <li>
                        <a href="{{ route('deals.index', ['stage' => $row['stage']->value] + ($mine ? ['owner' => 'me'] : [])) }}" class="group block">
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 font-medium text-slate-700 group-hover:text-brand-700">
                                    <x-status :value="$row['stage']" />
                                    <span class="text-xs text-slate-400">{{ $row['stage']->probability() }} %</span>
                                </span>
                                <span class="text-slate-500"><span class="font-semibold text-slate-900">{{ money($row['value']) }}</span> · {{ $row['count'] }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                @php
                                    $bar = ['sky' => 'bg-sky-500', 'indigo' => 'bg-indigo-500', 'violet' => 'bg-violet-500', 'amber' => 'bg-amber-500', 'emerald' => 'bg-emerald-500', 'rose' => 'bg-rose-400'][$row['stage']->color()];
                                @endphp
                                <div class="h-full rounded-full {{ $bar }} transition-all" style="width: {{ round($row['value'] / $maxStage * 100, 1) }}%"></div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </x-card>

        {{-- Tasks --}}
        <x-card :title="__('My next tasks')" :padding="false">
            <x-slot:actions><a href="{{ route('tasks.index') }}" class="link text-xs">{{ __('All tasks') }} →</a></x-slot:actions>
            @forelse ($tasks as $task)
                @include('partials.task-row', ['task' => $task, 'compact' => true])
            @empty
                <x-empty-state icon="check-circle" :title="__('Inbox zero!')" :description="__('No open tasks assigned to you.')" />
            @endforelse
        </x-card>

        {{-- Won by month --}}
        <x-card :title="__('Won revenue — last 6 months')" class="xl:col-span-2">
            <div class="flex h-56 items-end gap-3 sm:gap-6">
                @foreach ($wonByMonth as $month)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-xs font-semibold text-slate-700">{{ $month['value'] > 0 ? money_compact($month['value']) : '' }}</span>
                        <div class="w-full max-w-[3.5rem] rounded-t-lg bg-gradient-to-t from-brand-600 to-brand-400 transition-all hover:from-brand-700"
                             style="height: {{ max(2, round($month['value'] / $maxMonth * 100)) }}%"
                             title="{{ money($month['value']) }} · {{ trans_choice('{1} :count deal|[0,*] :count deals', $month['count'], ['count' => $month['count']]) }}"></div>
                        <span class="text-xs capitalize text-slate-500">{{ $month['month']->translatedFormat('M') }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>

        {{-- Closing soon --}}
        <x-card :title="__('Expected to close soon')" :padding="false">
            <ul class="divide-y divide-slate-100">
                @forelse ($closingSoon as $deal)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('deals.show', $deal) }}" class="block truncate text-sm font-medium text-slate-900 hover:text-brand-700">{{ $deal->title }}</a>
                            <p class="truncate text-xs text-slate-500">{{ $deal->company?->name }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-semibold text-slate-900">{{ money_compact($deal->value) }}</p>
                            <p @class(['text-xs', 'font-medium text-rose-600' => $deal->expected_close_date->isPast(), 'text-slate-500' => ! $deal->expected_close_date->isPast()])>{{ format_date($deal->expected_close_date) }}</p>
                        </div>
                    </li>
                @empty
                    <li><x-empty-state icon="calendar" :title="__('Nothing scheduled')" /></li>
                @endforelse
            </ul>
        </x-card>

        {{-- Recent activity --}}
        <x-card :title="__('Recent activity')" class="xl:col-span-3">
            @if ($recent->isEmpty())
                <x-empty-state icon="history" :title="__('No activity yet')" />
            @else
                <ul class="grid gap-x-8 gap-y-4 md:grid-cols-2">
                    @foreach ($recent as $log)
                        @include('partials.audit-entry', ['log' => $log])
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>
</x-layouts.app>

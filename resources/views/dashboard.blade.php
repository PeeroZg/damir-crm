@php
    $maxStage = max(1, $pipeline->max('value'));
    $totalStage = max(1, $pipeline->sum('value'));
    $maxMonth = $wonByMonth->max('value');
    $magnitude = $maxMonth > 0 ? 10 ** floor(log10($maxMonth)) : 1;
    $niceMax = $maxMonth > 0 ? ceil($maxMonth / ($magnitude / 2)) * ($magnitude / 2) : 1;
    $bestMonth = $maxMonth > 0 ? $wonByMonth->sortByDesc('value')->keys()->first() : null;
    $weightedShare = $kpis['open_value'] > 0 ? round($kpis['weighted_value'] / $kpis['open_value'] * 100) : 0;
    $winRate = $kpis['win_rate'] ?? 0;
    $firstName = Str::before(auth()->user()->name, ' ');
@endphp
<x-layouts.app :title="__('Dashboard')">
<div class="mx-auto max-w-[1280px]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium capitalize text-ink-500">{{ now()->translatedFormat(app()->getLocale() === 'hr' ? 'l, j. F' : 'l, M j') }}</p>
            <h1 class="page-title mt-1">{{ __('Hi,') }} <span class="relative inline-block"><span class="absolute inset-x-[-4px] bottom-[3px] h-[12px] -skew-x-6 rounded-sm bg-volt-400" aria-hidden="true"></span><span class="relative">{{ $firstName }}</span></span></h1>
            <p class="page-sub">{{ __('Here is how the pipeline looks today.') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="seg" role="group" aria-label="{{ __('Scope') }}">
                <a href="{{ route('dashboard') }}" class="seg-item" @if (! $mine) aria-current="page" @endif>{{ __('Whole team') }}</a>
                <a href="{{ route('dashboard', ['scope' => 'mine']) }}" class="seg-item" @if ($mine) aria-current="page" @endif>{{ __('Only mine') }}</a>
            </div>
            <x-button :href="route('deals.create')" icon="plus">{{ __('New deal') }}</x-button>
        </div>
    </div>

    {{-- KPI tiles --}}
    <div class="mt-6 grid grid-cols-2 gap-2.5 sm:gap-3 xl:grid-cols-4">
        <div class="relative overflow-hidden rounded-xl bg-ink-900 p-4 text-white shadow-card sm:p-5">
            <div class="absolute inset-0 opacity-[0.35] [background-image:radial-gradient(rgba(255,255,255,.14)_1px,transparent_1px)] [background-size:16px_16px]" aria-hidden="true"></div>
            <div class="relative">
                <div class="flex items-center justify-between"><p class="text-sm font-medium text-ink-300">{{ __('Open pipeline') }}</p><span class="flex h-7 w-7 items-center justify-center rounded-md bg-volt-400 text-ink-900"><x-icon name="briefcase" class="h-4 w-4" /></span></div>
                <p class="mt-3 font-display text-[26px] font-bold leading-none tracking-tight tnum sm:text-[34px]">{{ money_compact($kpis['open_value']) }}</p>
                <p class="mt-2 text-sm text-ink-300">{{ trans_choice('{1} :count open deal|[2,*] :count open deals', $kpis['open_count'], ['count' => $kpis['open_count']]) }}</p>
            </div>
        </div>
        <div class="card p-4 sm:p-5">
            <div class="flex items-center justify-between"><p class="text-sm font-medium text-ink-600">{{ __('Weighted forecast') }}</p><span class="flex h-7 w-7 items-center justify-center rounded-md bg-ink-50 text-ink-600 ring-1 ring-inset ring-ink-150"><x-icon name="scale" class="h-4 w-4" /></span></div>
            <p class="mt-3 font-display text-[26px] font-bold leading-none tracking-tight text-ink-900 tnum sm:text-[34px]">{{ money_compact($kpis['weighted_value']) }}</p>
            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-ink-100" role="img" aria-label="{{ __(':percent % of open pipeline', ['percent' => $weightedShare]) }}"><div class="h-full rounded-full bg-ink-900" style="width: {{ $weightedShare }}%"></div></div>
            <p class="mt-2 text-xs text-ink-500">{{ __('Value × stage probability') }}</p>
        </div>
        <div class="card p-4 sm:p-5">
            <div class="flex items-center justify-between"><p class="text-sm font-medium text-ink-600">{{ __('Won this year') }}</p><span class="flex h-7 w-7 items-center justify-center rounded-md bg-won-bg text-won-fg"><x-icon name="trending" class="h-4 w-4" /></span></div>
            <p class="mt-3 font-display text-[26px] font-bold leading-none tracking-tight text-ink-900 tnum sm:text-[34px]">{{ money_compact($kpis['won_value_ytd']) }}</p>
            <p class="mt-2 text-xs text-ink-500 sm:mt-[22px]">
                @if ($kpis['avg_won'])
                    {{ __('Avg. deal') }} <span class="font-medium text-ink-900">{{ money_compact($kpis['avg_won']) }}</span>
                @else
                    {{ __('No won deals yet') }}
                @endif
            </p>
        </div>
        <div class="card flex items-center gap-4 p-4 sm:p-5">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-ink-600">{{ __('Win rate') }}</p>
                <p class="mt-3 font-display text-[26px] font-bold leading-none tracking-tight text-ink-900 tnum sm:text-[34px]">{{ $kpis['win_rate'] !== null ? $kpis['win_rate'] : '—' }}@if ($kpis['win_rate'] !== null)<span class="text-2xl text-ink-400"> %</span>@endif</p>
                <p class="mt-2 text-xs text-ink-500">{{ trans_choice('{0} No offers waiting|{1} :count offer waiting for answer|[2,*] :count offers waiting for answer', $kpis['offers_pending'], ['count' => $kpis['offers_pending']]) }}</p>
            </div>
            @php $arc = round($winRate / 100 * 94.25, 2); @endphp
            <svg class="hidden h-[68px] w-[68px] shrink-0 -rotate-90 sm:block" viewBox="0 0 36 36" role="img" aria-label="{{ __('Win rate') }} {{ $winRate }} %">
                <circle cx="18" cy="18" r="15" fill="none" stroke="#EDEDE9" stroke-width="5" />
                @if ($winRate > 0)
                    <circle cx="18" cy="18" r="15" fill="none" stroke="#1A1A18" stroke-width="5" stroke-dasharray="{{ $arc }} 94.25" stroke-linecap="round" />
                    <circle cx="18" cy="18" r="15" fill="none" stroke="#D4FA4E" stroke-width="2" stroke-dasharray="{{ $arc }} 94.25" stroke-linecap="round" />
                @endif
            </svg>
        </div>
    </div>

    <div class="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        {{-- Pipeline by stage --}}
        <section class="card xl:col-span-2" aria-labelledby="pipe-h">
            <div class="card-header"><h2 id="pipe-h" class="card-title">{{ __('Pipeline by stage') }}</h2><a href="{{ route('deals.kanban') }}" class="link text-sm">{{ __('Open board') }}</a></div>
            <div class="card-body">
                <div class="flex h-3 gap-[3px] overflow-hidden rounded-full" role="img" aria-label="{{ __('Value share per stage') }}">
                    @foreach ($pipeline->where('value', '>', 0) as $row)
                        <div class="h-full bg-{{ $row['stage']->color() }}-solid first:rounded-l-full last:rounded-r-full" style="width: {{ round($row['value'] / $totalStage * 100, 2) }}%" title="{{ $row['stage']->label() }}"></div>
                    @endforeach
                </div>
                <ul class="mt-5 divide-y divide-ink-100">
                    @foreach ($pipeline as $row)
                        <li>
                            <a href="{{ route('deals.index', ['stage' => $row['stage']->value] + ($mine ? ['owner' => 'me'] : [])) }}" class="grid grid-cols-[132px_1fr_auto] items-center gap-4 py-2.5 sm:grid-cols-[150px_1fr_160px]">
                                <span class="flex items-center gap-2"><x-status :value="$row['stage']" /><span class="text-xs text-ink-500 tnum">{{ $row['stage']->probability() }} %</span></span>
                                <span class="h-2 overflow-hidden rounded-full bg-ink-100"><span class="block h-full rounded-full bg-{{ $row['stage']->color() }}-solid" style="width: {{ round($row['value'] / $maxStage * 100, 1) }}%"></span></span>
                                <span class="text-right text-base font-semibold text-ink-900 tnum">{{ money($row['value']) }}<span class="ml-1.5 text-xs font-normal text-ink-500">· {{ $row['count'] }}</span></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        {{-- Tasks --}}
        <section class="card flex flex-col" aria-labelledby="tasks-h">
            <div class="card-header"><h2 id="tasks-h" class="card-title flex items-center gap-2">{{ __('My next tasks') }} <span class="count">{{ $tasks->count() }}</span></h2><a href="{{ route('tasks.index') }}" class="link text-sm">{{ __('All tasks') }}</a></div>
            @if ($tasks->isEmpty())
                <x-empty-state icon="check-circle" :title="__('Inbox zero!')" :description="__('No open tasks assigned to you.')" />
            @else
                <ul class="divide-y divide-ink-100">
                    @foreach ($tasks as $task)
                        <li>@include('partials.task-row', ['task' => $task, 'compact' => true])</li>
                    @endforeach
                </ul>
                @if ($tasks->count() < 6)
                    <div class="mt-auto border-t border-dashed border-ink-150 px-5 py-3 text-xs text-ink-500">{{ __('That’s everything for now. Nice.') }}</div>
                @endif
            @endif
        </section>
    </div>

    <div class="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-3">
        {{-- Won revenue --}}
        <section class="card xl:col-span-2" aria-labelledby="rev-h">
            <div class="card-header"><h2 id="rev-h" class="card-title">{{ __('Won revenue — last 6 months') }}</h2><span class="flex items-center gap-1.5 text-xs text-ink-500"><span class="h-2.5 w-2.5 rounded-sm bg-volt-400 ring-1 ring-inset ring-ink-900/20"></span>{{ __('Best month') }}</span></div>
            <div class="card-body">
                <div class="relative h-[232px]">
                    <div class="absolute inset-x-0 bottom-7 top-6 flex flex-col justify-between" aria-hidden="true">
                        @foreach ([$niceMax, $niceMax / 2, 0] as $tick)
                            <div class="flex items-center gap-2"><span class="w-10 text-right text-[10px] text-ink-400 tnum">{{ money_compact($tick) }}</span><span @class(['h-px flex-1', 'border-t border-dashed border-ink-150' => $tick > 0, 'bg-ink-200' => $tick == 0])></span></div>
                        @endforeach
                    </div>
                    <ol class="absolute inset-0 left-12 grid grid-cols-6 items-end gap-3 pb-7 pt-6 sm:gap-6">
                        @foreach ($wonByMonth as $i => $month)
                            @php $current = $month['month']->isSameMonth(now()); @endphp
                            <li class="relative flex h-full flex-col items-center justify-end" title="{{ money($month['value']) }} · {{ trans_choice('{1} :count deal|[0,*] :count deals', $month['count'], ['count' => $month['count']]) }}">
                                @if ($month['value'] > 0)
                                    <span class="mb-1.5 whitespace-nowrap text-xs font-semibold text-ink-900 tnum">{{ money_compact($month['value']) }}</span>
                                @else
                                    <span class="mb-1.5 text-xs text-ink-400">—</span>
                                @endif
                                <div @class(['w-full max-w-[52px] rounded-t-md', 'bg-volt-400 ring-1 ring-inset ring-ink-900/15' => $i === $bestMonth, 'bg-ink-900' => $i !== $bestMonth]) style="height: {{ max(1.2, round($month['value'] / $niceMax * 100, 1)) }}%"></div>
                                <span @class(['absolute -bottom-6 text-xs capitalize', 'font-semibold text-ink-900' => $current, 'text-ink-500' => ! $current])>{{ $month['month']->translatedFormat('M') }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>

        {{-- Closing soon --}}
        <section class="card" aria-labelledby="soon-h">
            <div class="card-header"><h2 id="soon-h" class="card-title">{{ __('Expected to close soon') }}</h2></div>
            @if ($closingSoon->isEmpty())
                <x-empty-state icon="calendar" :title="__('Nothing scheduled')" />
            @else
                <ul class="divide-y divide-ink-100">
                    @foreach ($closingSoon as $deal)
                        @php $late = $deal->expected_close_date->isPast() && ! $deal->expected_close_date->isToday(); @endphp
                        <li><a href="{{ route('deals.show', $deal) }}" class="flex items-center gap-3 px-5 py-3 transition hover:bg-ink-25">
                            <div class="min-w-0 flex-1"><p class="truncate text-base font-medium text-ink-900">{{ $deal->title }}</p><p class="truncate text-xs text-ink-500">{{ $deal->company?->name }}</p></div>
                            <div class="shrink-0 text-right">
                                <p class="text-base font-semibold text-ink-900 tnum">{{ money_compact($deal->value) }}</p>
                                <p @class(['flex items-center justify-end gap-1 text-xs tnum', 'font-medium text-lost-fg' => $late, 'text-ink-500' => ! $late]) @if ($late) title="{{ __('Expected close date has passed') }}" @endif><x-icon :name="$late ? 'clock' : 'calendar'" class="h-3.5 w-3.5" />{{ format_date_short($deal->expected_close_date) }}</p>
                            </div>
                        </a></li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    {{-- Recent activity --}}
    <section class="card mt-3" aria-labelledby="act-h">
        <div class="card-header"><h2 id="act-h" class="card-title">{{ __('Recent activity') }}</h2></div>
        @if ($recent->isEmpty())
            <x-empty-state icon="clock" :title="__('No activity yet')" />
        @else
            <ul class="grid grid-cols-1 gap-x-10 px-5 py-4 md:grid-cols-2">
                @foreach ($recent as $log)
                    @include('partials.audit-entry', ['log' => $log])
                @endforeach
            </ul>
        @endif
    </section>
</div>
</x-layouts.app>

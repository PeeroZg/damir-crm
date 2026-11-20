@use('App\Enums\DealStage')
@php
    $canEdit = auth()->user()->can('update', $deal);
    $open = array_filter($stages, fn ($s) => ! $s->isClosed());
    $since = $deal->closed_at ?? $deal->stage_changed_at;
@endphp
<x-layouts.app :title="$deal->title">
<div class="mx-auto max-w-[1280px]" x-data="{ lostOpen: false }">
    <x-page-header :title="$deal->title" :back="['url' => route('deals.kanban'), 'label' => __('Deal pipeline')]">
        <x-slot:subtitle>
            <x-status :value="$deal->stage" />
            @if ($deal->company)<a href="{{ route('companies.show', $deal->company) }}" class="inline-flex items-center gap-1.5 hover:text-ink-900"><x-icon name="building" class="h-4 w-4 text-ink-400" />{{ $deal->company->name }}</a>@endif
            @if ($deal->contact)<span class="text-ink-300">·</span><a href="{{ route('contacts.show', $deal->contact) }}" class="inline-flex items-center gap-1.5 hover:text-ink-900"><x-icon name="user" class="h-4 w-4 text-ink-400" />{{ $deal->contact->full_name }}</a>@endif
        </x-slot:subtitle>
        <x-slot:actions>
            @if ($canEdit)
                <x-button variant="secondary" icon="pencil" :href="route('deals.edit', $deal)">{{ __('Edit') }}</x-button>
            @endif
            @if ($deal->isOpen() && auth()->user()->can('create', [App\Models\Offer::class, $deal]))
                <x-button icon="document-plus" :href="route('offers.create', ['deal_id' => $deal->id])">{{ __('Create offer') }}</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Stage stepper --}}
    <section class="card flex flex-col gap-5 p-4 lg:flex-row lg:items-center lg:gap-6 lg:p-5" aria-label="{{ __('Stage') }}">
        <ol class="flex flex-1 flex-wrap items-center gap-1.5">
            @foreach ($open as $i => $stage)
                @php
                    $current = $deal->stage === $stage;
                    $done = $deal->stage === DealStage::Won || ($deal->isOpen() && $stage->order() < $deal->stage->order());
                @endphp
                @if (! $loop->first)
                    <li aria-hidden="true" class="hidden text-ink-300 sm:block"><x-icon name="chevron-right" class="h-4 w-4" /></li>
                @endif
                <li class="flex min-w-[120px] flex-1">
                    <form method="POST" action="{{ route('deals.stage', $deal) }}" class="w-full">
                        @csrf @method('PATCH')
                        <input type="hidden" name="stage" value="{{ $stage->value }}">
                        <button type="submit" @disabled(! $canEdit || $current) @if ($current) aria-current="step" @endif
                                @class(['group relative flex h-12 w-full items-center gap-2.5 rounded-lg px-3 text-left transition disabled:cursor-default',
                                    'bg-ink-900 text-white shadow-xs' => $current,
                                    'bg-ink-50 text-ink-900 ring-1 ring-inset ring-ink-150 hover:ring-ink-300' => ! $current])>
                            <span @class(['flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-semibold',
                                'bg-volt-400 text-ink-900' => $current,
                                'bg-ink-900 text-white' => ! $current && $done,
                                'bg-white text-ink-600 ring-1 ring-inset ring-ink-200' => ! $current && ! $done])>
                                @if ($done && ! $current)<x-icon name="check" class="h-3.5 w-3.5" />@else{{ $loop->iteration }}@endif
                            </span>
                            <span class="min-w-0"><span class="block text-sm font-semibold leading-4">{{ $stage->label() }}</span><span @class(['block text-[11px] leading-4', 'text-ink-300' => $current, 'text-ink-500' => ! $current])>{{ $stage->probability() }} %</span></span>
                        </button>
                    </form>
                </li>
            @endforeach
            <li aria-hidden="true" class="mx-1 hidden h-8 w-px bg-ink-150 sm:block"></li>
            <li class="flex gap-1.5">
                <form method="POST" action="{{ route('deals.stage', $deal) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="stage" value="won">
                    <button type="submit" @disabled(! $canEdit || $deal->stage === DealStage::Won) @class(['inline-flex h-12 items-center gap-2 rounded-lg px-3.5 text-sm font-semibold transition disabled:cursor-default', 'bg-won-solid text-white' => $deal->stage === DealStage::Won, 'text-won-fg ring-1 ring-inset ring-won-solid/30 hover:bg-won-bg' => $deal->stage !== DealStage::Won])><x-icon name="trophy" class="h-[18px] w-[18px]" />{{ __('Won') }}</button>
                </form>
                <button type="button" @click="lostOpen = true" @disabled(! $canEdit || $deal->stage === DealStage::Lost) @class(['inline-flex h-12 items-center gap-2 rounded-lg px-3.5 text-sm font-semibold transition disabled:cursor-default', 'bg-lost-fg text-white' => $deal->stage === DealStage::Lost, 'text-lost-fg ring-1 ring-inset ring-lost-solid/30 hover:bg-lost-bg' => $deal->stage !== DealStage::Lost])><x-icon name="x-circle" class="h-[18px] w-[18px]" />{{ __('Lost') }}</button>
            </li>
        </ol>
        <dl class="flex shrink-0 gap-6 border-t border-ink-100 pt-4 lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">
            <div><dt class="meta-label">{{ __('Value') }}</dt><dd class="font-display text-2xl font-bold text-ink-900 tnum">{{ money($deal->value) }}</dd></div>
            <div><dt class="meta-label flex items-center gap-1">{{ __('Weighted') }} <span class="rounded bg-ink-100 px-1 text-[10px] font-medium text-ink-600">× {{ $deal->stage->probability() }} %</span></dt><dd class="font-display text-2xl font-bold text-ink-900 tnum">{{ money($deal->weightedValue()) }}</dd></div>
        </dl>
    </section>

    @if ($deal->stage === DealStage::Lost && $deal->lost_reason)
        <p class="mt-3 flex items-center gap-2 rounded-xl bg-lost-bg/60 px-4 py-3 text-sm text-lost-fg"><x-icon name="thumb-down" class="h-4 w-4" /><span class="font-semibold">{{ __('Lost reason') }}:</span> {{ $deal->lost_reason }}</p>
    @endif
    @unless ($canEdit)
        <p class="mt-3 flex items-center gap-2 text-xs text-ink-500"><x-icon name="exclamation-circle" class="h-4 w-4" />{{ __('Only :owner or an admin can change this deal.', ['owner' => $deal->owner?->name ?? __('the owner')]) }}</p>
    @endunless

    <div class="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_400px]">
        <div class="flex min-w-0 flex-col gap-3">
            <section class="card" aria-labelledby="det-h">
                <div class="card-header"><h2 id="det-h" class="card-title">{{ __('Details') }}</h2></div>
                <dl class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-3">
                    <div><dt class="meta-label">{{ __('Owner') }}</dt><dd class="meta-value">@include('partials.owner', ['user' => $deal->owner])</dd></div>
                    <div><dt class="meta-label">{{ __('Expected close') }}</dt><dd class="meta-value flex items-center gap-1.5 tnum"><x-icon name="calendar" class="h-4 w-4 text-ink-400" />{{ format_date($deal->expected_close_date) }}</dd></div>
                    <div><dt class="meta-label">{{ $deal->closed_at ? __('Closed') : __('In stage since') }}</dt><dd class="meta-value tnum">{{ format_date($since) }}@if ($since && ! $deal->closed_at) <span class="text-xs font-normal text-ink-500">· {{ trans_choice('{0} today|{1} :count day|[2,*] :count days', (int) $since->diffInDays(now()), ['count' => (int) $since->diffInDays(now())]) }}</span>@endif</dd></div>
                    @if ($deal->description)
                        <div class="sm:col-span-3"><dt class="meta-label">{{ __('Description') }}</dt><dd class="mt-1 whitespace-pre-line rounded-lg bg-ink-25 p-3 text-base text-ink-800 ring-1 ring-inset ring-ink-100">{{ $deal->description }}</dd></div>
                    @endif
                </dl>
            </section>

            <section class="card overflow-hidden" aria-labelledby="off-h">
                <div class="card-header">
                    <h2 id="off-h" class="card-title flex items-center gap-2">{{ __('Offers') }} <span class="count">{{ $deal->offers->count() }}</span></h2>
                    @if ($deal->isOpen() && auth()->user()->can('create', [App\Models\Offer::class, $deal]))
                        <x-button size="sm" variant="secondary" icon="plus" :href="route('offers.create', ['deal_id' => $deal->id])">{{ __('Create offer') }}</x-button>
                    @endif
                </div>
                @include('offers.table', ['offers' => $deal->offers, 'showCompany' => false])
            </section>
        </div>

        <div class="flex flex-col gap-3">
            @include('partials.activity-panel', ['subject' => $deal])
            @include('partials.history', ['logs' => $deal->auditLogs])
            @can('delete', $deal)
                <div class="flex justify-end"><x-delete-button :action="route('deals.destroy', $deal)" :label="__('Delete deal')" /></div>
            @endcan
        </div>
    </div>

    @if ($canEdit)
        {{-- Lost reason modal --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="lostOpen" x-cloak role="dialog" aria-modal="true" aria-labelledby="lost-h">
            <div class="absolute inset-0 bg-ink-950/40 backdrop-blur-[2px]" @click="lostOpen = false"></div>
            <form method="POST" action="{{ route('deals.stage', $deal) }}" class="modal relative animate-pop-in" x-trap.noscroll="lostOpen" @keydown.escape.prevent="lostOpen = false">
                @csrf @method('PATCH')
                <input type="hidden" name="stage" value="lost">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-lost-bg text-lost-fg"><x-icon name="thumb-down" /></span>
                        <div class="min-w-0">
                            <h2 id="lost-h" class="font-display text-xl font-bold text-ink-900">{{ __('Why was this deal lost?') }}</h2>
                            <p class="mt-1 text-sm text-ink-500"><span class="font-medium text-ink-900">{{ $deal->title }}</span> {{ __('will move to') }} <x-pill tone="lost" class="h-5 align-middle">{{ __('Lost') }}</x-pill></p>
                        </div>
                    </div>
                    <label for="lost-reason" class="label mt-5">{{ __('Lost reason') }}<span class="req" aria-hidden="true">*</span></label>
                    <textarea id="lost-reason" name="lost_reason" required maxlength="255" class="textarea" placeholder="{{ __('e.g. Chose a cheaper competitor, budget cut, no decision…') }}"></textarea>
                    <p class="hint">{{ __('Visible on the deal and in reports, so future you knows what happened.') }}</p>
                </div>
                <div class="flex justify-end gap-2 rounded-b-2xl border-t border-ink-100 bg-ink-25 px-6 py-4">
                    <x-button variant="secondary" @click="lostOpen = false">{{ __('Cancel') }}</x-button>
                    <button type="submit" class="btn bg-lost-fg text-white shadow-xs hover:bg-[#8C1A34]">{{ __('Mark as lost') }}</button>
                </div>
            </form>
        </div>
    @endif
</div>
</x-layouts.app>

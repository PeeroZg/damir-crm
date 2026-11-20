{{--
    Generic drag & drop board.
    $columns  enum cases               $items    Collection grouped by enum value
    $card     view rendered per card   $url      PATCH url with __ID__ placeholder
    $field    request field name       $totals   show money totals ($valueField)
    $collapse statuses whose columns only show the latest 3 cards ("Show N more" links to the list)
    $moreUrl  fn (status) => url        $probability  show "X % probability" (deals)
--}}
@php
    $totals ??= true;
    $valueField ??= 'value';
    $reasonFor ??= null;
    $celebrate ??= null;
    $collapse ??= [];
    $probability ??= false;
    $canMove ??= fn ($item) => true;
    $titleOf ??= fn ($item) => $item->title ?? $item->name ?? '';
@endphp

<form method="GET" action="{{ $action }}" class="flex flex-wrap items-center gap-3">
    <div class="relative w-full sm:w-[300px]">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-ink-400" />
        <label for="kb-filter" class="sr-only">{{ __('Filter cards') }}</label>
        <input id="kb-filter" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Filter cards…') }}" class="input pl-9">
    </div>
    @if ($mineToggle ?? false)
        <label class="flex h-10 items-center gap-2.5 rounded-md px-1 text-base text-ink-700">
            <input type="checkbox" name="owner" value="me" @checked(request('owner') === 'me') onchange="this.form.submit()" class="checkbox">{{ __('Only mine') }}
        </label>
    @endif
    <p class="ml-auto hidden items-center gap-1.5 text-xs text-ink-500 md:flex"><x-icon name="cursor" class="h-4 w-4" />{{ __('Tip: drag cards between columns. On mobile use the select on each card.') }}</p>
</form>

<div x-data="kanbanBoard({ url: @js($url), field: @js($field), reasonFor: @js($reasonFor), celebrate: @js($celebrate), currency: @js(config('crm.currency')) })"
     class="-mx-4 mt-4 overflow-x-auto px-4 pb-4 [scrollbar-width:thin] sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" tabindex="0" aria-label="{{ __('Board') }}">
    <div class="flex items-start gap-3">
        @foreach ($columns as $column)
            @php
                $all = $items->get($column->value, collect());
                $collapsed = in_array($column, $collapse, true) && $all->count() > 3;
                $visible = $collapsed ? $all->take(3) : $all;
                $hidden = $all->slice($visible->count());
                $tone = $column->color();
            @endphp
            <section data-kanban-column data-hidden-count="{{ $hidden->count() }}" data-hidden-value="{{ $totals ? $hidden->sum($valueField) : 0 }}"
                     @class(['kb-col', 'bg-won-bg/50 ring-won-solid/20' => $tone === 'won']) aria-label="{{ $column->label() }}">
                <div class="h-1 rounded-t-xl bg-{{ $tone }}-solid" aria-hidden="true"></div>
                <header class="kb-col-head">
                    @if ($tone === 'won')
                        <x-icon name="trophy" class="h-4 w-4 text-won-fg" />
                    @elseif ($tone === 'lost')
                        <x-icon name="thumb-down" class="h-4 w-4 text-lost-fg" />
                    @else
                        <span class="h-2 w-2 rounded-full bg-{{ $tone }}-solid"></span>
                    @endif
                    <h2 class="text-base font-semibold text-ink-900">{{ $column->label() }}</h2>
                    <span class="count bg-white ring-1 ring-inset ring-ink-150" data-count>{{ $all->count() }}</span>
                    @if ($totals)
                        <span class="ml-auto text-sm font-semibold text-ink-900 tnum" data-total>{{ money($all->sum($valueField)) }}</span>
                    @endif
                </header>
                @if ($probability)
                    <p class="-mt-1 px-3 pb-2 text-2xs font-medium uppercase text-ink-500">{{ __(':percent % probability', ['percent' => $column->probability()]) }}</p>
                @endif

                <div data-kanban-list data-status="{{ $column->value }}" class="kb-list">
                    @foreach ($visible as $item)
                        @php $movable = $canMove($item); @endphp
                        <article data-id="{{ $item->getKey() }}" data-value="{{ $totals ? $item->{$valueField} : 0 }}" data-title="{{ $titleOf($item) }}"
                                 @unless ($movable) data-locked title="{{ __('Only the owner or an admin can move this card.') }}" @endunless
                                 @class(['group kb-card', 'cursor-default' => ! $movable])>
                            @include($card, ['item' => $item])

                            @if ($movable)
                                <label class="mt-2.5 block sm:hidden"><span class="sr-only">{{ __('Move to') }}</span>
                                    <select data-move @change="moveWithSelect($event.target)" class="select input-sm">
                                        @foreach ($columns as $option)
                                            <option value="{{ $option->value }}" @selected($option === $column)>{{ $option->label() }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            @endif
                        </article>
                    @endforeach
                    <p data-empty @if ($all->isNotEmpty()) hidden @endif class="kb-drop pointer-events-none px-3 py-8 text-center text-xs text-ink-500">{{ __('Drop cards here') }}</p>
                    @if ($collapsed && isset($moreUrl))
                        <a href="{{ $moreUrl($column) }}" class="flex h-9 items-center justify-center gap-1 rounded-lg text-xs font-medium text-ink-600 hover:bg-white">{{ __('Show :count more', ['count' => $hidden->count()]) }}<x-icon name="chevron-right" class="h-3.5 w-3.5" /></a>
                    @endif
                </div>
            </section>
        @endforeach
    </div>

    @if ($reasonFor)
        {{-- Asked when a deal is dropped into "Lost" --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="pending" x-cloak role="dialog" aria-modal="true" aria-labelledby="lost-h">
            <div class="absolute inset-0 bg-ink-950/40 backdrop-blur-[2px]" @click="cancelReason()"></div>
            <form class="modal relative animate-pop-in" @submit.prevent="confirmReason()" @keydown.escape.prevent="cancelReason()" x-trap.noscroll="pending">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-lost-bg text-lost-fg"><x-icon name="thumb-down" /></span>
                        <div class="min-w-0">
                            <h2 id="lost-h" class="font-display text-xl font-bold text-ink-900">{{ __('Why was this deal lost?') }}</h2>
                            <p class="mt-1 text-sm text-ink-500"><span class="font-medium text-ink-900" x-text="pendingTitle"></span> {{ __('will move to') }} <x-pill tone="lost" class="h-5 align-middle">{{ __('Lost') }}</x-pill></p>
                        </div>
                    </div>
                    <label for="lost-reason" class="label mt-5">{{ __('Lost reason') }}<span class="req" aria-hidden="true">*</span></label>
                    <textarea id="lost-reason" x-ref="reason" x-model="reason" required maxlength="255" class="textarea" placeholder="{{ __('e.g. Chose a cheaper competitor, budget cut, no decision…') }}"></textarea>
                    <p class="hint">{{ __('Visible on the deal and in reports, so future you knows what happened.') }}</p>
                </div>
                <div class="flex justify-end gap-2 rounded-b-2xl border-t border-ink-100 bg-ink-25 px-6 py-4">
                    <x-button variant="secondary" @click="cancelReason()">{{ __('Cancel') }}</x-button>
                    <button type="submit" class="btn bg-lost-fg text-white shadow-xs hover:bg-[#8C1A34]">{{ __('Mark as lost') }}</button>
                </div>
            </form>
        </div>
    @endif
</div>

{{--
    Generic drag & drop board.
    $columns  list of enum cases      $items  Collection grouped by enum value
    $card     view rendered per item  $url    PATCH url with __ID__ placeholder
    $field    request field name      $totals show money totals (uses $item->{$valueField})
--}}
@php
    $totals ??= true;
    $valueField ??= 'value';
    $reasonFor ??= null;
    $canMove ??= fn ($item) => true;
    $headerColors = ['slate' => 'bg-slate-400', 'sky' => 'bg-sky-500', 'indigo' => 'bg-indigo-500', 'violet' => 'bg-violet-500', 'amber' => 'bg-amber-500', 'emerald' => 'bg-emerald-500', 'rose' => 'bg-rose-500'];
@endphp

<form method="GET" action="{{ $action }}" class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center">
    <div class="relative sm:w-80">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Filter cards…') }}" class="form-input pl-9" aria-label="{{ __('Filter cards') }}">
    </div>
    @if ($mineToggle ?? false)
        <label class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="owner" value="me" @checked(request('owner') === 'me') onchange="this.form.submit()" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            {{ __('Only mine') }}
        </label>
    @endif
    <p class="text-xs text-slate-500 sm:ml-auto">{{ __('Tip: drag cards between columns. On mobile use the select on each card.') }}</p>
</form>

<div x-data="kanbanBoard({ url: @js($url), field: @js($field), reasonFor: @js($reasonFor), currency: @js(config('crm.currency')) })"
     class="-mx-4 overflow-x-auto px-4 pb-4 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
    <div class="flex min-w-max gap-4">
        @foreach ($columns as $column)
            @php $columnItems = $items->get($column->value, collect()); @endphp
            <section data-kanban-column class="flex w-72 shrink-0 flex-col rounded-xl bg-slate-100/80">
                <header class="flex items-center justify-between gap-2 px-3 pb-2 pt-3">
                    <h2 class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <span class="h-2 w-2 rounded-full {{ $headerColors[$column->color()] }}"></span>
                        {{ $column->label() }}
                        <span data-count class="rounded-full bg-white px-2 py-0.5 text-xs font-medium text-slate-500">{{ $columnItems->count() }}</span>
                    </h2>
                    @if ($totals)
                        <span data-total class="text-xs font-semibold text-slate-500">{{ money($columnItems->sum($valueField)) }}</span>
                    @endif
                </header>

                <div data-kanban-list data-status="{{ $column->value }}" class="flex min-h-[8rem] flex-1 flex-col gap-2 px-2 pb-2">
                    @foreach ($columnItems as $item)
                        @php $movable = $canMove($item); @endphp
                        <article data-id="{{ $item->getKey() }}" data-value="{{ $totals ? $item->{$valueField} : 0 }}"
                                 @unless ($movable) data-locked title="{{ __('Only the owner or an admin can move this card.') }}" @endunless
                                 @class(['rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition', 'cursor-grab hover:border-brand-300 hover:shadow active:cursor-grabbing' => $movable, 'opacity-80' => ! $movable])>
                            @include($card, ['item' => $item])

                            @if ($movable)
                                <label class="mt-2 block sm:hidden">
                                    <span class="sr-only">{{ __('Move to') }}</span>
                                    <select data-move @change="moveWithSelect($event.target)" class="form-input py-1 text-xs">
                                        @foreach ($columns as $option)
                                            <option value="{{ $option->value }}" @selected($option === $column)>{{ $option->label() }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            @endif
                        </article>
                    @endforeach
                    <p data-empty @if ($columnItems->isNotEmpty()) hidden @endif class="pointer-events-none rounded-xl border-2 border-dashed border-slate-200 px-3 py-8 text-center text-xs text-slate-400">{{ __('Drop cards here') }}</p>
                </div>
            </section>
        @endforeach
    </div>

    @if ($reasonFor)
        {{-- Asked when a deal is dropped into "Lost" --}}
        <div x-cloak x-show="pending" class="fixed inset-0 z-[65] flex items-center justify-center bg-slate-900/40 p-4" @keydown.escape.window="pending && cancelReason()">
            <div x-show="pending" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.outside="cancelReason()">
                <h3 class="text-base font-semibold text-slate-900">{{ __('Why was this deal lost?') }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ __('Optional, but it helps to spot patterns later.') }}</p>
                <input x-ref="reason" x-model="reason" @keydown.enter.prevent="confirmReason()" type="text" maxlength="255" class="form-input mt-4" placeholder="{{ __('e.g. Chose a cheaper competitor') }}">
                <div class="mt-5 flex justify-end gap-2">
                    <x-button variant="secondary" @click="cancelReason()">{{ __('Cancel') }}</x-button>
                    <x-button variant="danger" @click="confirmReason()">{{ __('Mark as lost') }}</x-button>
                </div>
            </div>
        </div>
    @endif
</div>

<div x-data="commandPalette({ url: @js(route('search')), actions: @js($actions) })"
     @open-palette.window="show()"
     @keydown.window.ctrl.k.prevent="open ? hide() : show()"
     @keydown.window.meta.k.prevent="open ? hide() : show()"
     @keydown.escape.window="hide()">
    <div x-cloak x-show="open" class="fixed inset-0 z-[70] overflow-y-auto p-4 sm:p-6 md:p-20" role="dialog" aria-modal="true" aria-label="{{ __('Search') }}">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="hide()"></div>

        <div x-show="open" x-transition class="relative mx-auto max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5">
            <div class="flex items-center gap-3 border-b border-slate-100 px-4">
                <x-icon name="search" class="h-5 w-5 text-slate-400" />
                <input x-ref="input" x-model="query" @input.debounce.200ms="search()"
                       @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)" @keydown.enter.prevent="go()"
                       type="text" class="h-14 w-full border-0 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0"
                       placeholder="{{ __('Type to search, or pick an action…') }}" autocomplete="off">
                <span x-show="loading" class="h-4 w-4 animate-spin rounded-full border-2 border-slate-300 border-t-brand-600"></span>
            </div>

            <ul x-ref="list" class="max-h-80 overflow-y-auto p-2" role="listbox">
                <template x-for="(item, index) in items" :key="item.url + index">
                    <li role="option" :data-active="index === active" :aria-selected="index === active"
                        @mouseenter="active = index" @click="go(item)"
                        class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5"
                        :class="index === active ? 'bg-brand-50' : ''">
                        <span class="w-16 shrink-0 text-[11px] font-semibold uppercase tracking-wide text-slate-400" x-text="item.type"></span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-slate-900" x-text="item.title"></span>
                            <span class="block truncate text-xs text-slate-500" x-text="item.subtitle"></span>
                        </span>
                        <span x-show="index === active" class="text-xs text-brand-600">↵</span>
                    </li>
                </template>
                <li x-show="query.trim().length >= 2 && !loading && results.length === 0" class="px-3 py-8 text-center text-sm text-slate-500">
                    {{ __('No results for') }} „<span x-text="query"></span>“
                </li>
            </ul>

            <div class="flex items-center gap-4 border-t border-slate-100 bg-slate-50 px-4 py-2 text-xs text-slate-500">
                <span><kbd class="font-sans">↑↓</kbd> {{ __('navigate') }}</span>
                <span><kbd class="font-sans">↵</kbd> {{ __('open') }}</span>
                <span><kbd class="font-sans">esc</kbd> {{ __('close') }}</span>
            </div>
        </div>
    </div>
</div>

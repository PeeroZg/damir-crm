<div x-data="commandPalette({ url: @js(route('search')), actions: @js($actions) })"
     @open-palette.window="show()"
     @keydown.window.ctrl.k.prevent="open ? hide() : show()"
     @keydown.window.meta.k.prevent="open ? hide() : show()"
     @keydown.escape.window="open && hide()">
    <div class="fixed inset-0 z-50 flex items-start justify-center px-4 pt-[12vh]" x-show="open" x-cloak role="dialog" aria-modal="true" aria-label="{{ __('Search') }}">
        <div class="absolute inset-0 bg-ink-950/40 backdrop-blur-[2px]" @click="hide()"></div>
        <div class="relative w-full max-w-[600px] overflow-hidden rounded-2xl bg-white shadow-modal animate-pop-in" x-trap.noscroll="open">
            <div class="flex items-center gap-3 border-b border-ink-150 px-4">
                <x-icon name="search" class="h-5 w-5 text-ink-400" />
                <label for="palette-q" class="sr-only">{{ __('Search') }}</label>
                <input id="palette-q" x-ref="input" x-model="query" @input.debounce.200ms="search()"
                       @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)" @keydown.enter.prevent="go()"
                       type="text" autocomplete="off" placeholder="{{ __('Search companies, contacts, deals, offers…') }}"
                       class="h-14 w-full border-0 bg-transparent p-0 text-md text-ink-900 placeholder:text-ink-500 focus:outline-none focus:ring-0">
                <span x-show="loading" class="h-4 w-4 shrink-0 animate-spin rounded-full border-2 border-ink-200 border-t-ink-900"></span>
                <span class="kbd">Esc</span>
            </div>

            <div x-ref="list" class="max-h-[420px] overflow-y-auto p-2" role="listbox">
                <template x-for="(item, index) in items" :key="item.url + index">
                    <div>
                        <p x-show="heading(index)" x-text="heading(index)" class="px-3 pb-1.5 pt-2 text-2xs font-medium uppercase text-ink-500"></p>
                        <a :href="item.url" role="option" :aria-selected="(index === active).toString()" @mouseenter="active = index"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5" :class="index === active ? 'bg-ink-900 text-white' : 'hover:bg-ink-50'">
                            <template x-if="item.initials">
                                <span class="avatar h-8 w-8 ring-0" :class="'avatar-' + item.avatar" x-text="item.initials"></span>
                            </template>
                            <template x-if="!item.initials">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md" :class="index === active ? 'bg-white/10 text-volt-400' : 'bg-ink-50 text-ink-500 ring-1 ring-inset ring-ink-150'">
                                    @foreach (['building', 'briefcase', 'document', 'document-plus', 'columns', 'check-circle', 'user'] as $icon)
                                        <x-icon :name="$icon" class="h-[18px] w-[18px]" x-show="item.icon === '{{ $icon }}'" />
                                    @endforeach
                                </span>
                            </template>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-base font-medium" :class="[index === active ? 'text-white' : 'text-ink-900', item.kind === 'offer' ? 'font-mono text-sm' : '']" x-text="item.title"></span>
                                <span class="block truncate text-xs" :class="index === active ? 'text-ink-300' : 'text-ink-500'" x-text="item.subtitle"></span>
                            </span>
                            <template x-if="item.pill">
                                <span class="pill" :class="'pill-' + item.pill.tone"><span class="pill-dot"></span><span x-text="item.pill.label"></span></span>
                            </template>
                            <span x-show="index === active" class="flex items-center gap-1 text-xs text-ink-300">{{ __('Open') }}<span class="kbd border-white/20 bg-white/10 text-white">↵</span></span>
                        </a>
                    </div>
                </template>
                <p x-show="query.trim().length >= 2 && !loading && results.length === 0" class="px-3 py-8 text-center text-sm text-ink-500">
                    {{ __('No results for') }} „<span x-text="query"></span>“
                </p>
            </div>

            <div class="flex items-center gap-4 border-t border-ink-150 bg-ink-25 px-4 py-2.5 text-xs text-ink-500">
                <span class="flex items-center gap-1.5"><span class="kbd">↑</span><span class="kbd">↓</span>{{ __('navigate') }}</span>
                <span class="flex items-center gap-1.5"><span class="kbd">↵</span>{{ __('open') }}</span>
                <span class="ml-auto flex items-center gap-1.5"><span class="kbd">Ctrl</span><span class="kbd">K</span>{{ __('toggle') }}</span>
            </div>
        </div>
    </div>
</div>

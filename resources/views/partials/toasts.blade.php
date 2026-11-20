<div x-data x-init="
        @if (session('success')) $store.toasts.push({ title: @js(session('success')) }); @endif
        @if (session('error')) $store.toasts.push({ type: 'error', title: @js(session('error')) }); @endif
    "
     class="pointer-events-none fixed bottom-4 right-4 z-[60] flex flex-col items-end gap-2" aria-live="polite" aria-atomic="false">
    <template x-for="t in $store.toasts.items" :key="t.id">
        <div class="toast" role="status">
            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
                  :class="t.type === 'won' ? 'bg-volt-400 text-ink-900' : (t.type === 'error' ? 'bg-lost-solid text-white' : 'bg-white/10 text-volt-400')">
                <x-icon name="trophy" class="h-4 w-4" x-show="t.type === 'won'" />
                <x-icon name="exclamation-circle" class="h-4 w-4" x-show="t.type === 'error'" />
                <x-icon name="check" class="h-4 w-4" x-show="t.type !== 'won' && t.type !== 'error'" />
            </span>
            <div class="min-w-0 flex-1 pt-0.5"><p class="font-medium" x-text="t.title"></p><p class="mt-0.5 text-ink-300" x-show="t.body" x-text="t.body"></p></div>
            <button type="button" class="-m-1 rounded-md p-1 text-ink-400 hover:bg-white/10 hover:text-white" @click="$store.toasts.dismiss(t.id)" aria-label="{{ __('Dismiss') }}"><x-icon name="x" class="h-4 w-4" /></button>
        </div>
    </template>
</div>

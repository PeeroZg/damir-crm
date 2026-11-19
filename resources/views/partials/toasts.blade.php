<div x-data x-init="
        @if (session('success')) $store.toasts.push(@js(session('success')), 'success'); @endif
        @if (session('error')) $store.toasts.push(@js(session('error')), 'error'); @endif
    "
     class="pointer-events-none fixed inset-x-0 bottom-0 z-[60] flex flex-col items-center gap-2 p-4 sm:items-end sm:p-6"
     aria-live="polite">
    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div x-transition.opacity.duration.200ms
             class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl bg-white p-4 shadow-lg ring-1 ring-slate-900/5">
            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-white"
                  :class="toast.type === 'error' ? 'bg-rose-500' : 'bg-emerald-500'">
                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path x-show="toast.type !== 'error'" fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 011.4-1.4L8 12.6l7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd" />
                    <path x-show="toast.type === 'error'" fill-rule="evenodd" d="M10 3a1 1 0 011 1v7a1 1 0 11-2 0V4a1 1 0 011-1zm0 11a1.25 1.25 0 110 2.5 1.25 1.25 0 010-2.5z" clip-rule="evenodd" />
                </svg>
            </span>
            <p class="flex-1 text-sm text-slate-700" x-text="toast.message"></p>
            <button type="button" class="text-slate-400 hover:text-slate-600" @click="$store.toasts.dismiss(toast.id)" aria-label="{{ __('Close') }}">✕</button>
        </div>
    </template>
</div>

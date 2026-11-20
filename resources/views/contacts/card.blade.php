<div class="flex items-center gap-2.5">
    <x-avatar :name="$item->full_name" class="ring-0" />
    <div class="min-w-0">
        <a href="{{ route('contacts.show', $item) }}" class="block truncate text-base font-semibold text-ink-900 hover:underline hover:decoration-volt-500 hover:decoration-2 hover:underline-offset-2">{{ $item->full_name }}</a>
        <p class="truncate text-xs text-ink-500">{{ $item->position }}</p>
    </div>
</div>
<p class="mt-3 flex items-center gap-1.5 truncate text-xs text-ink-600"><x-icon name="building" class="h-3.5 w-3.5 shrink-0 text-ink-400" />{{ $item->company?->name }}</p>

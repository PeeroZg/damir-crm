<div class="flex items-center gap-2.5">
    <x-avatar :name="$item->full_name" size="sm" />
    <div class="min-w-0">
        <a href="{{ route('contacts.show', $item) }}" class="block truncate text-sm font-semibold text-slate-900 hover:text-brand-700">{{ $item->full_name }}</a>
        <p class="truncate text-xs text-slate-500">{{ $item->position }}</p>
    </div>
</div>
<p class="mt-2 truncate text-xs text-slate-600">{{ $item->company?->name }}</p>

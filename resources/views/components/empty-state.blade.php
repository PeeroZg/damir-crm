@props(['icon' => 'sparkles', 'title', 'description' => null])
<div {{ $attributes->merge(['class' => 'empty']) }}>
    <div class="empty-icon"><x-icon :name="$icon" class="h-6 w-6" /></div>
    <p class="font-display text-lg font-semibold text-ink-900">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-[280px] text-sm text-ink-500">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>

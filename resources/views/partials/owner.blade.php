@if ($user)
    <span class="flex items-center gap-2"><x-avatar :name="$user->name" size="sm" /><span class="whitespace-nowrap">{{ $user->name }}</span></span>
@else
    <span class="text-ink-400">—</span>
@endif

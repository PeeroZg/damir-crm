<div class="flex items-start gap-2">
    <a href="{{ route('companies.show', $item) }}" class="line-clamp-2 flex-1 text-base font-semibold leading-5 text-ink-900 hover:underline hover:decoration-volt-500 hover:decoration-2 hover:underline-offset-2">{{ $item->name }}</a>
    @if ($item->owner)<x-avatar :name="$item->owner->name" size="sm" class="ring-0" />@endif
</div>
<p class="mt-1 truncate text-xs text-ink-500">{{ collect([$item->industry, $item->city])->filter()->implode(' · ') }}</p>
<p class="mt-3 flex items-center gap-1.5 text-xs text-ink-600"><x-icon name="briefcase" class="h-3.5 w-3.5 text-ink-400" />{{ trans_choice('{0} No deals|{1} :count deal|[2,*] :count deals', $item->deals_count, ['count' => $item->deals_count]) }}</p>

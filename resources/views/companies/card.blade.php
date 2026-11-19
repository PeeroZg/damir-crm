<a href="{{ route('companies.show', $item) }}" class="block text-sm font-semibold text-slate-900 hover:text-brand-700">{{ $item->name }}</a>
<p class="mt-0.5 text-xs text-slate-500">{{ collect([$item->industry, $item->city])->filter()->implode(' · ') }}</p>
<div class="mt-3 flex items-center justify-between text-xs text-slate-500">
    <span>{{ trans_choice('{0} No deals|{1} :count deal|[2,*] :count deals', $item->deals_count, ['count' => $item->deals_count]) }}</span>
    @if ($item->owner)<x-avatar :name="$item->owner->name" size="sm" />@endif
</div>

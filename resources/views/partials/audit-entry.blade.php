{{-- One line of the automatic history (audit log). --}}
@php $showSubject ??= true; @endphp
<li class="flex gap-3">
    <x-avatar :name="$log->user?->name ?? __('System')" size="sm" class="mt-0.5" />
    <div class="min-w-0 flex-1 text-sm">
        @php
            $userHtml = '<span class="font-medium text-slate-900">'.e($log->user?->name ?? __('System')).'</span>';
            if ($showSubject) {
                $url = $log->subjectUrl();
                $titleHtml = $url
                    ? '<a href="'.e($url).'" class="link">'.e($log->subjectTitle()).'</a>'
                    : '<span class="font-medium">'.e($log->subjectTitle()).'</span>';
            }
        @endphp
        <p class="text-slate-700">
            @if ($showSubject)
                {!! __(':user :event :subject', ['user' => $userHtml, 'event' => e($log->eventLabel()), 'subject' => e(mb_strtolower($log->subjectLabel())).' '.$titleHtml]) !!}
            @else
                {!! __(':user :event', ['user' => $userHtml, 'event' => e($log->eventLabel())]) !!}
            @endif
        </p>
        @if ($log->event === 'updated' && $log->diff)
            <ul class="mt-1 space-y-0.5 text-xs text-slate-500">
                @foreach (array_slice($log->formattedChanges(), 0, 4) as $change)
                    <li><span class="font-medium text-slate-600">{{ $change['field'] }}:</span> <span class="line-through decoration-slate-300">{{ $change['old'] }}</span> → <span class="text-slate-700">{{ $change['new'] }}</span></li>
                @endforeach
            </ul>
        @endif
        <p class="mt-0.5 text-xs text-slate-400" title="{{ format_date($log->created_at, true) }}">{{ $log->created_at->diffForHumans() }}</p>
    </div>
</li>

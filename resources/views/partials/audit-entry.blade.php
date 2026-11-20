{{-- One line of the automatic history (audit log). Vars: $log, $showSubject (default true), $timeline --}}
@php
    $showSubject ??= true;
    $timeline ??= false;
    $userName = $log->user?->name ?? __('System');
    if ($showSubject) {
        $url = $log->subjectUrl();
        $mono = $log->auditable_type === 'offer';
        $titleHtml = $url
            ? '<a href="'.e($url).'" class="link-quiet font-medium'.($mono ? ' font-mono text-[13px]' : '').'">'.e($log->subjectTitle()).'</a>'
            : '<span class="font-medium text-ink-900'.($mono ? ' font-mono text-[13px]' : '').'">'.e($log->subjectTitle()).'</span>';
    }
    $userHtml = '<span class="font-medium text-ink-900">'.e($userName).'</span>';
@endphp
<li @class(['flex gap-3', 'relative pb-5 last:pb-0' => $timeline, 'py-2.5' => ! $timeline])>
    @if ($timeline)
        <span class="absolute left-[13px] top-8 h-[calc(100%-28px)] w-px bg-ink-150" aria-hidden="true"></span>
    @endif
    <x-avatar :name="$userName" />
    <div class="min-w-0 pt-0.5 text-sm">
        <p class="text-ink-600">
            @if ($showSubject)
                {!! __(':user :event :subject', ['user' => $userHtml, 'event' => e($log->eventLabel()), 'subject' => e(mb_strtolower($log->subjectLabel())).' '.$titleHtml]) !!}
            @else
                {!! __(':user :event', ['user' => $userHtml, 'event' => e($log->eventLabel())]) !!}
            @endif
        </p>
        @if ($log->event === 'updated' && $log->diff)
            @foreach (array_slice($log->formattedChanges(), 0, 3) as $change)
                <p class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-ink-500">
                    {{ $change['field'] }}:
                    <span class="rounded bg-ink-100 px-1.5 py-0.5 text-ink-600 line-through decoration-ink-300">{{ $change['old'] }}</span>
                    <x-icon name="arrow-right" class="h-3 w-3" />
                    <span class="rounded bg-volt-200 px-1.5 py-0.5 font-medium text-ink-900">{{ $change['new'] }}</span>
                </p>
            @endforeach
        @endif
        <p class="mt-1 text-xs text-ink-500" title="{{ format_date($log->created_at, true) }}">{{ $log->created_at->diffForHumans() }}</p>
    </div>
</li>

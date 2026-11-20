{{-- Automatic change history (audit log) for one record. --}}
<section class="card">
    <div class="card-header"><h2 class="card-title">{{ __('History') }}</h2></div>
    @if ($logs->isEmpty())
        <p class="flex items-center gap-2 p-5 text-sm text-ink-500"><x-icon name="clock" class="h-4 w-4" />{{ __('No changes recorded yet.') }}</p>
    @else
        <ul class="p-5">
            @foreach ($logs as $log)
                @include('partials.audit-entry', ['log' => $log, 'showSubject' => false, 'timeline' => true])
            @endforeach
        </ul>
    @endif
</section>

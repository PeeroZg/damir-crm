{{-- Automatic change history (audit log) for one record. --}}
<x-card :title="__('History')">
    @if ($logs->isEmpty())
        <p class="text-sm text-slate-500">{{ __('No changes recorded yet.') }}</p>
    @else
        <ul class="space-y-4">
            @foreach ($logs as $log)
                @include('partials.audit-entry', ['log' => $log, 'showSubject' => false])
            @endforeach
        </ul>
    @endif
</x-card>

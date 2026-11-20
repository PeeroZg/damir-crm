{{-- One task with an inline "done" toggle. Vars: $task, $compact (hide assignee), $hideSubject --}}
@php
    $subjectTitle = match ($task->subject_type) {
        'company' => $task->subject?->name,
        'contact' => $task->subject?->full_name,
        'deal' => $task->subject?->title,
        default => null,
    };
    $subjectUrl = $task->subject && ! $task->subject->trashed()
        ? route(Str::plural($task->subject_type).'.show', $task->subject_id)
        : null;
    $overdue = $task->isOverdue();
    $today = ! $overdue && $task->due_at?->isToday();
@endphp
<div x-data="taskToggle({ url: @js(route('activities.toggle', $task)), completed: @js($task->isCompleted()) })" class="flex gap-3 px-5 py-3.5">
    <button type="button" @click="toggle()" :aria-pressed="completed.toString()"
            class="group mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition"
            :class="completed ? 'border-ink-900 bg-ink-900 text-volt-400' : 'border-ink-300 hover:border-ink-900 hover:bg-volt-400'"
            aria-label="{{ __('Mark “:task” as done', ['task' => $task->title]) }}">
        <x-icon name="check" class="h-3 w-3" ::class="completed ? 'opacity-100' : 'text-ink-900 opacity-0 group-hover:opacity-100'" />
    </button>
    <div class="min-w-0 flex-1">
        <p class="text-base font-medium text-ink-900" :class="completed && 'text-ink-400 line-through decoration-ink-300'">{{ $task->title }}</p>
        <p class="mt-0.5 truncate text-sm text-ink-500">
            @if ($subjectTitle && empty($hideSubject))
                @if ($subjectUrl)<a href="{{ $subjectUrl }}" class="hover:text-ink-900">{{ $subjectTitle }}</a>@else{{ $subjectTitle }}@endif
            @elseif (! empty($hideSubject))
                {{ __('Task') }}
            @endif
            @if (empty($compact) && $task->assignee)<span class="divider-dot"></span>{{ $task->assignee->name }}@endif
        </p>
    </div>
    @if ($task->due_at)
        <span @class([
            'flex h-6 shrink-0 items-center gap-1 rounded-md px-2 text-xs font-medium tnum',
            'bg-lost-bg text-lost-fg' => $overdue,
            'bg-volt-300 text-ink-900' => $today,
            'bg-ink-50 text-ink-600 ring-1 ring-inset ring-ink-150' => ! $overdue && ! $today,
        ]) title="{{ format_date($task->due_at, true) }}">
            <x-icon :name="$overdue ? 'clock' : 'calendar'" class="h-3.5 w-3.5" />{{ $overdue ? __('Overdue') : ($today ? __('Today') : format_date_short($task->due_at)) }}
        </span>
    @endif
</div>

@php
    $subjectUrl = $task->subject && ! $task->subject->trashed()
        ? route(Str::plural($task->subject_type).'.show', $task->subject_id)
        : null;
    $subjectTitle = match ($task->subject_type) {
        'company' => $task->subject?->name,
        'contact' => $task->subject?->full_name,
        'deal' => $task->subject?->title,
        default => null,
    };
@endphp
<div x-data="taskToggle({ url: @js(route('activities.toggle', $task)), completed: @js($task->isCompleted()) })"
     class="flex items-start gap-3 border-b border-slate-100 px-5 py-3 last:border-0">
    <button type="button" @click="toggle()" :aria-pressed="completed.toString()"
            class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition"
            :class="completed ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 hover:border-brand-500'"
            title="{{ __('Mark as done') }}">
        <x-icon name="check" class="h-3 w-3" x-show="completed" />
    </button>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-medium text-slate-900" :class="completed && 'line-through text-slate-400'">{{ $task->title }}</p>
        <p class="mt-0.5 flex flex-wrap items-center gap-x-2 text-xs text-slate-500">
            @if ($subjectTitle && empty($hideSubject))
                @if ($subjectUrl)<a href="{{ $subjectUrl }}" class="hover:text-brand-700 hover:underline">{{ $subjectTitle }}</a>@else<span>{{ $subjectTitle }}</span>@endif
            @endif
            @if (empty($compact) && $task->assignee)
                <span>· {{ $task->assignee->name }}</span>
            @endif
        </p>
    </div>
    @if ($task->due_at)
        <span @class(['shrink-0 rounded-md px-2 py-0.5 text-xs font-medium', 'bg-rose-50 text-rose-700' => $task->isOverdue(), 'bg-amber-50 text-amber-700' => ! $task->isOverdue() && $task->due_at->isToday(), 'text-slate-500' => ! $task->isOverdue() && ! $task->due_at->isToday()])
              title="{{ format_date($task->due_at, true) }}">
            {{ $task->isOverdue() ? __('Overdue') : ($task->due_at->isToday() ? __('Today') : format_date($task->due_at)) }}
        </span>
    @endif
</div>

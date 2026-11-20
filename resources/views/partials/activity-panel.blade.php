{{--
    Notes, calls, meetings, e-mails and tasks for a company, contact or deal.
    Expects: $subject (model with activities loaded), $users
--}}
@php
    $types = App\Enums\ActivityType::cases();
    $placeholders = [
        'note' => __('Write a note…'), 'call' => __('How did the call go?'),
        'meeting' => __('Meeting summary…'), 'email' => __('What was the e-mail about?'),
    ];
    $key = $subject->getMorphClass().'-'.$subject->getKey();
@endphp
<section class="card">
    <div class="card-header"><h2 class="card-title">{{ __('Activity') }}</h2></div>

    <form method="POST" action="{{ route('activities.store') }}" class="border-b border-ink-100 p-4" x-data="{ type: @js(old('type', 'note')) }"
          @keydown.ctrl.enter.prevent="$el.requestSubmit()" @keydown.meta.enter.prevent="$el.requestSubmit()">
        @csrf
        <input type="hidden" name="subject_type" value="{{ $subject->getMorphClass() }}">
        <input type="hidden" name="subject_id" value="{{ $subject->getKey() }}">
        <input type="hidden" name="type" :value="type">

        <div class="flex flex-wrap gap-1" role="tablist" aria-label="{{ __('Activity type') }}">
            @foreach ($types as $type)
                <button type="button" role="tab" @click="type = @js($type->value)" :aria-selected="(type === @js($type->value)).toString()"
                        class="inline-flex h-8 items-center gap-1.5 rounded-full px-3 text-sm font-medium transition"
                        :class="type === @js($type->value) ? 'bg-ink-900 text-white' : 'text-ink-600 ring-1 ring-inset ring-ink-150 hover:bg-ink-50'">
                    <x-icon :name="$type->icon()" class="h-4 w-4" />{{ $type->label() }}
                </button>
            @endforeach
        </div>

        <div x-show="type === 'task'" x-cloak class="mt-3 space-y-3">
            <label for="title-{{ $key }}" class="sr-only">{{ __('Task') }}</label>
            <input id="title-{{ $key }}" name="title" type="text" value="{{ old('title') }}" maxlength="255" class="input" placeholder="{{ __('What needs to be done?') }}" :disabled="type !== 'task'">
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="due-{{ $key }}" class="label">{{ __('Due') }}</label>
                    <input id="due-{{ $key }}" name="due_at" type="datetime-local" value="{{ old('due_at', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')) }}" class="input tnum" :disabled="type !== 'task'">
                </div>
                <div>
                    <label for="assignee-{{ $key }}" class="label">{{ __('Assignee') }}</label>
                    <select id="assignee-{{ $key }}" name="assigned_to" class="select" :disabled="type !== 'task'">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((int) old('assigned_to', auth()->id()) === $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <label for="body-{{ $key }}" class="sr-only">{{ __('Note') }}</label>
        <textarea id="body-{{ $key }}" x-show="type !== 'task'" name="body" maxlength="5000" class="textarea mt-3 min-h-[84px] resize-y" :disabled="type === 'task'"
                  :placeholder="@js($placeholders)[type]">{{ old('body') }}</textarea>

        @if ($errors->hasAny(['title', 'body', 'due_at', 'type']))
            <p class="error">{{ $errors->first('title') ?: $errors->first('body') ?: $errors->first('due_at') }}</p>
        @endif

        <div class="mt-2.5 flex items-center justify-between">
            <span class="flex items-center gap-1.5 text-xs text-ink-500"><span class="kbd">Ctrl</span><span class="kbd">↵</span> {{ __('to add') }}</span>
            <x-button type="submit" size="sm" icon="plus">{{ __('Add') }}</x-button>
        </div>
    </form>

    @if ($subject->activities->isEmpty())
        <x-empty-state icon="chat" :title="__('No activity yet')" :description="__('Log a call, write a note or plan the next step.')" />
    @else
        <ul class="divide-y divide-ink-100">
            @foreach ($subject->activities as $activity)
                @if ($activity->isTask())
                    <li>@include('partials.task-row', ['task' => $activity->setRelation('subject', $subject), 'compact' => false, 'hideSubject' => true])</li>
                @else
                    @php
                        $tone = $activity->type->color();
                        $badge = $tone === 'draft' ? 'bg-ink-50 text-ink-600 ring-1 ring-inset ring-ink-150' : "bg-{$tone}-bg text-{$tone}-fg";
                    @endphp
                    <li class="group flex gap-3 px-4 py-3.5">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $badge }}"><x-icon :name="$activity->type->icon()" class="h-4 w-4" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="whitespace-pre-line text-base text-ink-900">{{ $activity->body }}</p>
                            <p class="mt-1 text-xs text-ink-500">
                                {{ $activity->type->label() }}<span class="divider-dot"></span>{{ $activity->author?->name ?? __('System') }}<span class="divider-dot"></span><span title="{{ format_date($activity->created_at, true) }}">{{ $activity->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                        @can('delete', $activity)
                            <form method="POST" action="{{ route('activities.destroy', $activity) }}" class="opacity-0 transition focus-within:opacity-100 group-hover:opacity-100" onsubmit="return confirm(@js(__('Delete this entry?')))">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-ghost h-8 w-8 text-ink-400 hover:bg-lost-bg hover:text-lost-fg" aria-label="{{ __('Delete') }}"><x-icon name="trash" class="h-4 w-4" /></button>
                            </form>
                        @endcan
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</section>

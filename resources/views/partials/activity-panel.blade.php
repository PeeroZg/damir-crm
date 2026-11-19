{{--
    Notes, calls, meetings, e-mails and tasks for a company, contact or deal.
    Expects: $subject (model with activities loaded), $users
--}}
@php
    $types = App\Enums\ActivityType::cases();
    $oldType = old('type', 'note');
@endphp
<x-card :title="__('Activity')" :padding="false">
    <form method="POST" action="{{ route('activities.store') }}" class="space-y-3 border-b border-slate-100 p-5"
          x-data="{ type: @js($oldType) }">
        @csrf
        <input type="hidden" name="subject_type" value="{{ $subject->getMorphClass() }}">
        <input type="hidden" name="subject_id" value="{{ $subject->getKey() }}">
        <input type="hidden" name="type" :value="type">

        <div class="flex flex-wrap gap-1" role="tablist">
            @foreach ($types as $type)
                <button type="button" role="tab" @click="type = @js($type->value)" :aria-selected="(type === @js($type->value)).toString()"
                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold transition"
                        :class="type === @js($type->value) ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                    <x-icon :name="$type->icon()" class="h-3.5 w-3.5" /> {{ $type->label() }}
                </button>
            @endforeach
        </div>

        <div x-show="type === 'task'" x-cloak class="space-y-3">
            <input name="title" type="text" value="{{ old('title') }}" maxlength="255" class="form-input" placeholder="{{ __('What needs to be done?') }}" aria-label="{{ __('Task') }}" :disabled="type !== 'task'">
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="due_at_{{ $subject->getKey() }}" class="form-label text-xs">{{ __('Due') }}</label>
                    <input id="due_at_{{ $subject->getKey() }}" name="due_at" type="datetime-local" value="{{ old('due_at', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')) }}" class="form-input" :disabled="type !== 'task'">
                </div>
                <div>
                    <label for="assigned_to_{{ $subject->getKey() }}" class="form-label text-xs">{{ __('Assignee') }}</label>
                    <select id="assigned_to_{{ $subject->getKey() }}" name="assigned_to" class="form-input" :disabled="type !== 'task'">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((int) old('assigned_to', auth()->id()) === $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <textarea x-show="type !== 'task'" name="body" rows="3" maxlength="5000" class="form-input" :disabled="type === 'task'"
                  :placeholder="{ note: @js(__('Write a note…')), call: @js(__('How did the call go?')), meeting: @js(__('Meeting summary…')), email: @js(__('What was the e-mail about?')) }[type]"
                  aria-label="{{ __('Note') }}">{{ old('body') }}</textarea>

        @if ($errors->hasAny(['title', 'body', 'due_at', 'type']))
            <p class="text-sm text-rose-600">{{ $errors->first('title') ?: $errors->first('body') ?: $errors->first('due_at') }}</p>
        @endif

        <div class="flex justify-end">
            <x-button type="submit" size="sm" icon="plus">{{ __('Add') }}</x-button>
        </div>
    </form>

    @if ($subject->activities->isEmpty())
        <x-empty-state icon="note" :title="__('No activity yet')" :description="__('Log a call, write a note or plan the next step.')" />
    @else
        <ul class="divide-y divide-slate-100">
            @foreach ($subject->activities as $activity)
                @if ($activity->isTask())
                    <li>@include('partials.task-row', ['task' => $activity->setRelation('subject', $subject), 'compact' => false, 'hideSubject' => true])</li>
                @else
                    <li class="group flex gap-3 px-5 py-3">
                        @php
                            $tone = ['slate' => 'bg-slate-100 text-slate-600', 'sky' => 'bg-sky-100 text-sky-700', 'violet' => 'bg-violet-100 text-violet-700', 'indigo' => 'bg-indigo-100 text-indigo-700', 'amber' => 'bg-amber-100 text-amber-700'][$activity->type->color()];
                        @endphp
                        <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $tone }}">
                            <x-icon :name="$activity->type->icon()" class="h-4 w-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="whitespace-pre-line text-sm text-slate-700">{{ $activity->body }}</p>
                            <p class="mt-1 text-xs text-slate-400">
                                {{ $activity->type->label() }} · {{ $activity->author?->name ?? __('System') }} ·
                                <span title="{{ format_date($activity->created_at, true) }}">{{ $activity->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                        @can('delete', $activity)
                            <form method="POST" action="{{ route('activities.destroy', $activity) }}" class="opacity-0 transition group-hover:opacity-100" onsubmit="return confirm(@js(__('Delete this entry?')))">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded p-1 text-slate-400 hover:bg-rose-50 hover:text-rose-600" title="{{ __('Delete') }}"><x-icon name="trash" class="h-4 w-4" /></button>
                            </form>
                        @endcan
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</x-card>

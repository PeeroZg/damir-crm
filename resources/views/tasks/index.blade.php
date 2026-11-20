<x-layouts.app :title="__('Tasks')">
<div class="mx-auto max-w-[960px]">
    <x-page-header :title="__('Tasks')">
        <x-slot:description>{{ $showAll ? __('Tasks of the whole team.') : __('Everything assigned to you.') }}</x-slot:description>
        <x-slot:actions>
            @if (auth()->user()->isAdmin())
                <div class="seg" role="group" aria-label="{{ __('Scope') }}">
                    <a href="{{ request()->fullUrlWithQuery(['user' => null, 'page' => null]) }}" class="seg-item" @if (! $showAll) aria-current="page" @endif>{{ __('Mine') }}</a>
                    <a href="{{ request()->fullUrlWithQuery(['user' => 'all', 'page' => null]) }}" class="seg-item" @if ($showAll) aria-current="page" @endif>{{ __('Whole team') }}</a>
                </div>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="seg mb-3" role="group" aria-label="{{ __('Filter') }}">
        @foreach (['open' => __('Open'), 'overdue' => __('Overdue'), 'done' => __('Done')] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['filter' => $key, 'page' => null]) }}" class="seg-item" @if ($filter === $key) aria-current="page" @endif>
                {{ $label }} <span @class(['count', 'bg-lost-bg text-lost-fg' => $key === 'overdue' && $counts['overdue'] > 0])>{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    <section class="card overflow-hidden">
        @if ($tasks->isEmpty())
            <x-empty-state icon="check-circle" :title="$filter === 'done' ? __('Nothing completed yet') : __('All caught up!')" :description="__('Tasks are created from the Activity panel on a company, contact or deal.')" />
        @else
            <ul class="divide-y divide-ink-100">
                @foreach ($tasks as $task)
                    <li>@include('partials.task-row', ['task' => $task, 'compact' => ! $showAll])</li>
                @endforeach
            </ul>
            {{ $tasks->links() }}
        @endif
    </section>
</div>
</x-layouts.app>

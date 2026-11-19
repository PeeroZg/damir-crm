<x-layouts.app :title="__('Tasks')">
    <x-page-header :title="__('Tasks')" :subtitle="$showAll ? __('Tasks of the whole team.') : __('Everything assigned to you.')">
        <x-slot:actions>
            @if (auth()->user()->isAdmin())
                <div class="inline-flex rounded-lg bg-slate-100 p-0.5">
                    <a href="{{ request()->fullUrlWithQuery(['user' => null, 'page' => null]) }}" @class(['rounded-md px-3 py-1.5 text-xs font-semibold', 'bg-white shadow-sm text-slate-900' => ! $showAll, 'text-slate-500' => $showAll])>{{ __('Mine') }}</a>
                    <a href="{{ request()->fullUrlWithQuery(['user' => 'all', 'page' => null]) }}" @class(['rounded-md px-3 py-1.5 text-xs font-semibold', 'bg-white shadow-sm text-slate-900' => $showAll, 'text-slate-500' => ! $showAll])>{{ __('Whole team') }}</a>
                </div>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 flex gap-2">
        @foreach (['open' => __('Open'), 'overdue' => __('Overdue'), 'done' => __('Done')] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['filter' => $key, 'page' => null]) }}"
               @class(['inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium', 'bg-brand-600 text-white' => $filter === $key, 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' => $filter !== $key])>
                {{ $label }}
                <span @class(['rounded-full px-1.5 text-xs', 'bg-white/20' => $filter === $key, 'bg-slate-100' => $filter !== $key, 'text-rose-600' => $key === 'overdue' && $counts['overdue'] > 0 && $filter !== $key])>{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    <x-card :padding="false">
        @forelse ($tasks as $task)
            @include('partials.task-row', ['task' => $task, 'compact' => ! $showAll])
        @empty
            <x-empty-state icon="check-circle" :title="$filter === 'done' ? __('Nothing completed yet') : __('All caught up!')" :description="__('Tasks are created from the Activity panel on a company, contact or deal.')" />
        @endforelse
        {{ $tasks->links() }}
    </x-card>
</x-layouts.app>

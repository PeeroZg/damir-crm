@props(['list', 'kanban', 'active'])
<div class="inline-flex rounded-lg bg-slate-100 p-0.5" role="group" aria-label="{{ __('View') }}">
    <a href="{{ $list }}" @class(['inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-semibold transition', 'bg-white text-slate-900 shadow-sm' => $active === 'list', 'text-slate-500 hover:text-slate-700' => $active !== 'list'])>
        <x-icon name="list" class="h-4 w-4" /> {{ __('List') }}
    </a>
    <a href="{{ $kanban }}" @class(['inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-semibold transition', 'bg-white text-slate-900 shadow-sm' => $active === 'kanban', 'text-slate-500 hover:text-slate-700' => $active !== 'kanban'])>
        <x-icon name="columns" class="h-4 w-4" /> {{ __('Kanban') }}
    </a>
</div>

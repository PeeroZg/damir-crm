@php
    $nav = [
        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => __('Dashboard'), 'icon' => 'home'],
        ['route' => 'companies.index', 'match' => 'companies.*', 'label' => __('Companies'), 'icon' => 'building'],
        ['route' => 'contacts.index', 'match' => 'contacts.*', 'label' => __('Contacts'), 'icon' => 'users'],
        ['route' => 'deals.kanban', 'match' => 'deals.*', 'label' => __('Deals'), 'icon' => 'briefcase'],
        ['route' => 'offers.index', 'match' => 'offers.*', 'label' => __('Offers'), 'icon' => 'document'],
        ['route' => 'tasks.index', 'match' => 'tasks.*', 'label' => __('Tasks'), 'icon' => 'check-circle', 'badge' => $openTasks],
    ];
@endphp
<a href="{{ route('dashboard') }}" class="mb-6 flex h-9 items-center px-2" aria-label="Damir CRM — {{ __('Dashboard') }}"><x-logo /></a>
<nav class="flex flex-col gap-0.5" aria-label="{{ __('Main navigation') }}">
    @foreach ($nav as $item)
        <a href="{{ route($item['route']) }}" class="nav-item" @if (request()->routeIs($item['match'])) aria-current="page" @endif>
            <x-icon :name="$item['icon']" /><span class="truncate">{{ $item['label'] }}</span>
            @if (! empty($item['badge']))<span class="nav-badge" aria-label="{{ __(':count open', ['count' => $item['badge']]) }}">{{ $item['badge'] }}</span>@endif
        </a>
    @endforeach

    @can('viewAny', App\Models\User::class)
        <p class="mb-1.5 mt-6 px-2.5 text-2xs font-medium uppercase text-ink-500">{{ __('Admin') }}</p>
        <a href="{{ route('users.index') }}" class="nav-item" @if (request()->routeIs('users.*')) aria-current="page" @endif><x-icon name="user-group" /><span>{{ __('Team') }}</span></a>
    @endcan
</nav>
<div class="mt-auto pt-6">
    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl border border-ink-150 bg-white p-2.5 shadow-card transition hover:border-ink-300">
        <x-avatar :name="auth()->user()->name" size="lg" class="ring-0" />
        <span class="min-w-0 flex-1">
            <span class="block truncate text-base font-semibold text-ink-900">{{ auth()->user()->name }}</span>
            <span class="block truncate text-xs text-ink-500">{{ auth()->user()->role->label() }}</span>
        </span>
        <x-icon name="chevron-up-down" class="h-4 w-4 text-ink-400" />
    </a>
</div>

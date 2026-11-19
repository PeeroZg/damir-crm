@props(['title' => null])
@php
    $nav = [
        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => __('Dashboard'), 'icon' => 'home'],
        ['route' => 'companies.index', 'match' => 'companies.*', 'label' => __('Companies'), 'icon' => 'building'],
        ['route' => 'contacts.index', 'match' => 'contacts.*', 'label' => __('Contacts'), 'icon' => 'users'],
        ['route' => 'deals.kanban', 'match' => 'deals.*', 'label' => __('Deals'), 'icon' => 'briefcase'],
        ['route' => 'offers.index', 'match' => 'offers.*', 'label' => __('Offers'), 'icon' => 'document'],
        ['route' => 'tasks.index', 'match' => 'tasks.*', 'label' => __('Tasks'), 'icon' => 'check-circle'],
    ];
    $openTasks = auth()->user()->assignedTasks()->tasks()->open()->count();
    $paletteActions = [
        ['type' => __('Action'), 'title' => __('New company'), 'subtitle' => __('Add a customer or lead'), 'url' => route('companies.create')],
        ['type' => __('Action'), 'title' => __('New deal'), 'subtitle' => __('Start a new opportunity'), 'url' => route('deals.create')],
        ['type' => __('Action'), 'title' => __('New offer'), 'subtitle' => __('Prepare a quote with line items'), 'url' => route('offers.create')],
        ['type' => __('Go to'), 'title' => __('Deal pipeline'), 'subtitle' => __('Kanban board'), 'url' => route('deals.kanban')],
        ['type' => __('Go to'), 'title' => __('My tasks'), 'subtitle' => __('Open and overdue tasks'), 'url' => route('tasks.index')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 32 32%22><rect width=%2232%22 height=%2232%22 rx=%228%22 fill=%22%234f46e5%22/><text x=%2216%22 y=%2222%22 font-size=%2216%22 text-anchor=%22middle%22 fill=%22white%22 font-family=%22Arial%22 font-weight=%22bold%22>D</text></svg>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-900 antialiased" x-data="{ sidebar: false }">

    {{-- Mobile sidebar backdrop --}}
    <div x-cloak x-show="sidebar" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" @click="sidebar = false"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-slate-900 transition-transform lg:translate-x-0"
           :class="{ 'translate-x-0': sidebar }">
        <div class="flex h-16 items-center gap-2.5 px-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500 font-bold text-white">D</span>
            <span class="text-lg font-bold tracking-tight text-white">Damir <span class="text-brand-400">CRM</span></span>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="{{ __('Main navigation') }}">
            @foreach ($nav as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   @class(['group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition', 'bg-slate-800 text-white' => $active, 'text-slate-400 hover:bg-slate-800/60 hover:text-white' => ! $active])
                   @if ($active) aria-current="page" @endif>
                    <x-icon :name="$item['icon']" class="h-5 w-5" />
                    <span class="flex-1">{{ $item['label'] }}</span>
                    @if ($item['route'] === 'tasks.index' && $openTasks > 0)
                        <span class="rounded-full bg-brand-500 px-2 py-0.5 text-xs font-semibold text-white">{{ $openTasks }}</span>
                    @endif
                </a>
            @endforeach

            @can('viewAny', App\Models\User::class)
                <p class="px-3 pb-1 pt-6 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Admin') }}</p>
                <a href="{{ route('users.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition', 'bg-slate-800 text-white' => request()->routeIs('users.*'), 'text-slate-400 hover:bg-slate-800/60 hover:text-white' => ! request()->routeIs('users.*')])>
                    <x-icon name="user" class="h-5 w-5" /> {{ __('Team') }}
                </a>
            @endcan
        </nav>

        <div class="border-t border-slate-800 p-4">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg p-2 hover:bg-slate-800">
                <x-avatar :name="auth()->user()->name" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->role->label() }}</p>
                </div>
            </a>
        </div>
    </aside>

    <div class="flex min-h-full flex-col lg:pl-64">
        {{-- Top bar --}}
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6">
            <button type="button" class="-m-2 p-2 text-slate-600 lg:hidden" @click="sidebar = true" aria-label="{{ __('Open menu') }}">
                <x-icon name="menu" class="h-6 w-6" />
            </button>

            <button type="button" @click="$dispatch('open-palette')"
                    class="flex max-w-md flex-1 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-left text-sm text-slate-400 transition hover:border-slate-300 hover:bg-white">
                <x-icon name="search" class="h-4 w-4" />
                <span class="hidden flex-1 truncate sm:inline">{{ __('Search companies, contacts, deals, offers…') }}</span>
                <span class="flex-1 sm:hidden">{{ __('Search…') }}</span>
                <kbd class="hidden rounded border border-slate-200 bg-white px-1.5 font-sans text-xs text-slate-500 sm:inline">Ctrl K</kbd>
            </button>

            <div class="ml-auto flex items-center gap-1">
                @foreach (config('crm.locales') as $code => $label)
                    <form method="POST" action="{{ route('locale', $code) }}">
                        @csrf
                        <button type="submit" title="{{ $label }}"
                                @class(['rounded-md px-2 py-1 text-xs font-semibold uppercase', 'bg-brand-50 text-brand-700' => app()->getLocale() === $code, 'text-slate-500 hover:bg-slate-100' => app()->getLocale() !== $code])>{{ $code }}</button>
                    </form>
                @endforeach

                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf
                    <button type="submit" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700" title="{{ __('Log out') }}">
                        <x-icon name="logout" class="h-5 w-5" />
                        <span class="sr-only">{{ __('Log out') }}</span>
                    </button>
                </form>
            </div>
        </header>

        @if (config('crm.demo.enabled'))
            <div class="border-b border-amber-200 bg-amber-50 px-4 py-2 text-center text-xs text-amber-800 sm:px-6">
                {{ __('Public demo — feel free to click around and change things. All data is fictional and resets every night.') }}
            </div>
        @endif

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <footer class="px-4 pb-6 text-center text-xs text-slate-400 sm:px-6">
            Damir CRM · Laravel {{ app()->version() }} · PHP {{ PHP_VERSION }}
        </footer>
    </div>

    @include('partials.command-palette', ['actions' => $paletteActions])
    @include('partials.toasts')
</body>
</html>

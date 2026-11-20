@props(['title' => null])
@php
    $openTasks = auth()->user()->assignedTasks()->tasks()->open()->count();
    $paletteActions = [
        ['group' => __('Quick actions'), 'kind' => 'action', 'icon' => 'building', 'title' => __('New company'), 'subtitle' => __('Add a customer or lead'), 'url' => route('companies.create')],
        ['group' => __('Quick actions'), 'kind' => 'action', 'icon' => 'briefcase', 'title' => __('New deal'), 'subtitle' => __('Start a new opportunity'), 'url' => route('deals.create')],
        ['group' => __('Quick actions'), 'kind' => 'action', 'icon' => 'document-plus', 'title' => __('New offer'), 'subtitle' => __('Prepare a quote with line items'), 'url' => route('offers.create')],
        ['group' => __('Go to'), 'kind' => 'action', 'icon' => 'columns', 'title' => __('Deal pipeline'), 'subtitle' => __('Kanban board'), 'url' => route('deals.kanban')],
        ['group' => __('Go to'), 'kind' => 'action', 'icon' => 'check-circle', 'title' => __('My tasks'), 'subtitle' => __('Open and overdue tasks'), 'url' => route('tasks.index')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @include('partials.fonts')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="min-h-screen bg-ink-50" x-data="app" @keydown.window.escape="nav = false">
    @if (config('crm.demo.enabled'))
        <div class="relative z-20 flex min-h-[32px] items-center justify-center gap-2.5 bg-ink-950 px-4 py-1.5 text-center text-xs text-ink-300">
            <span class="inline-flex h-[18px] shrink-0 items-center rounded-full bg-volt-400 px-2 text-[10px] font-bold uppercase tracking-[0.08em] text-ink-900">Demo</span>
            <span>{{ __('Public demo — feel free to click around and change things. All data is fictional and resets every night.') }}</span>
        </div>
    @endif

    <div class="flex">
        {{-- Desktop sidebar --}}
        <aside @class(['sticky top-0 hidden w-[244px] shrink-0 flex-col px-3 pb-3 pt-4 lg:flex', 'h-[calc(100vh-32px)]' => config('crm.demo.enabled'), 'h-screen' => ! config('crm.demo.enabled')])>
            @include('partials.sidebar')
        </aside>

        {{-- Mobile drawer --}}
        <div class="fixed inset-0 z-40 lg:hidden" x-show="nav" x-cloak role="dialog" aria-modal="true" aria-label="{{ __('Navigation') }}">
            <div class="absolute inset-0 bg-ink-950/40" @click="nav = false"></div>
            <aside class="absolute inset-y-0 left-0 flex w-[280px] flex-col overflow-y-auto bg-ink-50 px-3 pb-3 pt-4 shadow-modal" x-show="nav" x-transition x-trap.noscroll="nav">
                <button type="button" class="btn btn-icon btn-ghost absolute right-3 top-3.5" @click="nav = false" aria-label="{{ __('Close navigation') }}"><x-icon name="x" /></button>
                @include('partials.sidebar')
            </aside>
        </div>

        <main class="min-w-0 flex-1 lg:py-2 lg:pr-2">
            <div @class(['flex flex-col bg-ink-25 lg:rounded-2xl lg:border lg:border-ink-150 lg:shadow-card', 'min-h-[calc(100vh-32px)] lg:min-h-[calc(100vh-48px)]' => config('crm.demo.enabled'), 'min-h-screen lg:min-h-[calc(100vh-16px)]' => ! config('crm.demo.enabled')])>
                <header class="sticky top-0 z-30 flex h-14 items-center gap-2 border-b border-ink-150 bg-ink-25/90 px-3 backdrop-blur sm:px-4 lg:rounded-t-2xl lg:px-6">
                    <button type="button" class="btn btn-icon btn-ghost lg:hidden" @click="nav = true" aria-label="{{ __('Open navigation') }}"><x-icon name="menu" /></button>
                    <button type="button" class="group flex h-9 min-w-0 flex-1 items-center gap-2 rounded-lg border border-ink-200 bg-white px-3 text-left text-base text-ink-500 shadow-xs transition hover:border-ink-300 sm:max-w-[420px]" @click="$dispatch('open-palette')">
                        <x-icon name="search" class="h-[18px] w-[18px] shrink-0 text-ink-400 group-hover:text-ink-900" />
                        <span class="truncate"><span class="sm:hidden">{{ __('Search…') }}</span><span class="hidden sm:inline">{{ __('Search companies, contacts, deals, offers…') }}</span></span>
                        <span class="ml-auto hidden items-center gap-1 sm:flex"><span class="kbd">Ctrl</span><span class="kbd">K</span></span>
                    </button>
                    <div class="ml-auto flex items-center gap-1.5">
                        @include('partials.locale-switch')
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-icon btn-ghost" aria-label="{{ __('Log out') }}" title="{{ __('Log out') }}"><x-icon name="logout" /></button>
                        </form>
                    </div>
                </header>

                <div class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    {{ $slot }}
                </div>

                <footer class="flex items-center justify-center gap-2 px-6 pb-5 text-xs text-ink-500">
                    <svg width="14" height="14" viewBox="0 0 32 32" fill="none" aria-hidden="true"><rect width="32" height="32" rx="9" fill="#1A1A18" /><rect x="8" y="13.9" width="5" height="4.2" rx="1.6" fill="#D4FA4E" /><path d="M15 10.1h1.2a5.9 5.9 0 0 1 0 11.8H15" stroke="#FFFFFF" stroke-width="4.2" stroke-linecap="round" /></svg>
                    <span>Damir CRM · Laravel {{ app()->version() }} · PHP {{ PHP_VERSION }}</span>
                </footer>
            </div>
        </main>
    </div>

    @include('partials.command-palette', ['actions' => $paletteActions])
    @include('partials.toasts')
</div>
</body>
</html>

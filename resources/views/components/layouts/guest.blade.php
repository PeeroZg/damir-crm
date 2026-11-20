@props(['title' => null])
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
<div class="flex min-h-screen bg-white">
    <section class="relative hidden w-[46%] max-w-[680px] flex-col overflow-hidden bg-ink-950 p-12 text-white lg:flex" aria-label="{{ __('About Damir CRM') }}">
        <div class="absolute inset-0 opacity-60 [background-image:radial-gradient(rgba(255,255,255,.10)_1px,transparent_1px)] [background-size:20px_20px]" aria-hidden="true"></div>
        <div class="relative"><x-logo dark /></div>

        <div class="relative mt-14 flex gap-3" aria-hidden="true">
            <div class="w-[188px] -rotate-[3deg] rounded-xl bg-white/[0.06] p-2.5 ring-1 ring-white/10">
                <p class="mb-2 flex items-center gap-1.5 px-1 text-[11px] font-medium text-ink-300"><span class="h-1.5 w-1.5 rounded-full bg-negotiation-solid"></span>{{ __('Negotiation') }} <span class="ml-auto text-ink-400">€27.4k</span></p>
                <div class="rounded-lg bg-white p-2.5 text-ink-900 shadow-pop"><p class="text-[12px] font-semibold leading-4">Cloud migration — Mura Metal</p><p class="mt-2 text-[12px] font-bold tnum">€7,150.00</p></div>
                <div class="mt-2 rounded-lg bg-white/[0.08] p-2.5"><div class="h-2 w-24 rounded bg-white/20"></div><div class="mt-2 h-2 w-12 rounded bg-white/10"></div></div>
            </div>
            <div class="mt-8 w-[196px] rotate-[2deg] rounded-xl bg-volt-400 p-2.5 text-ink-900 shadow-drag">
                <p class="mb-2 flex items-center gap-1.5 px-1 text-[11px] font-semibold"><x-icon name="check" class="h-3.5 w-3.5" />{{ __('Won') }} <span class="ml-auto">€116.4k</span></p>
                <div class="rounded-lg bg-white p-2.5 shadow-pop"><p class="text-[12px] font-semibold leading-4">Online shop — Sljeme Gradnja</p><div class="mt-2 flex items-center justify-between"><p class="text-[12px] font-bold tnum">€10,070.00</p><span class="avatar avatar-violet avatar-sm">MH</span></div></div>
            </div>
        </div>

        <div class="relative mt-auto">
            <h1 class="max-w-[520px] font-display text-5xl font-bold">{!! __('From first contact to :accent — in one place.', ['accent' => '<span class="text-volt-400">'.e(__('signed offer')).'</span>']) !!}</h1>
            <ul class="mt-8 grid gap-3 text-md text-ink-200">
                @foreach ([__('Drag & drop deal pipeline with live totals'), __('Offers with line items, VAT and PDF export'), __('Tasks, notes and a full audit trail'), __('REST API, English & Croatian UI')] as $feature)
                    <li class="flex items-center gap-3"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-volt-400 text-ink-900"><x-icon name="check" class="h-3 w-3" /></span>{{ $feature }}</li>
                @endforeach
            </ul>
            <div class="mt-10 flex flex-wrap gap-2">
                @foreach (['Laravel', 'Blade', 'Alpine.js', 'Tailwind CSS'] as $tech)
                    <span class="rounded-full px-3 py-1 font-mono text-xs text-ink-300 ring-1 ring-inset ring-white/15">{{ $tech }}</span>
                @endforeach
            </div>
        </div>
    </section>

    <main class="flex flex-1 flex-col px-6 py-6 sm:px-12">
        <div class="flex items-center justify-between">
            <span class="lg:hidden"><x-logo /></span>
            <div class="ml-auto">@include('partials.locale-switch')</div>
        </div>
        <div class="mx-auto flex w-full max-w-[400px] flex-1 flex-col justify-center py-10">
            {{ $slot }}
        </div>
        <p class="text-center text-xs text-ink-500 lg:hidden">Laravel · Blade · Alpine.js · Tailwind CSS</p>
    </main>
</div>
@include('partials.toasts')
</body>
</html>

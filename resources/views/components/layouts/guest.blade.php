@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-900 antialiased">
    <div class="flex min-h-full">
        <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-slate-900 p-12 lg:flex">
            <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-brand-500/30 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-16 h-96 w-96 rounded-full bg-violet-500/20 blur-3xl"></div>
            <div class="relative flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-500 font-bold text-white">D</span>
                <span class="text-xl font-bold text-white">Damir <span class="text-brand-400">CRM</span></span>
            </div>
            <div class="relative">
                <h2 class="text-3xl font-bold leading-tight text-white">{{ __('From first contact to signed offer — in one place.') }}</h2>
                <ul class="mt-8 space-y-3 text-slate-300">
                    @foreach ([__('Drag & drop deal pipeline with live totals'), __('Offers with line items, VAT and PDF export'), __('Tasks, notes and a full audit trail'), __('REST API, English & Croatian UI')] as $feature)
                        <li class="flex items-center gap-3"><x-icon name="check-circle" class="h-5 w-5 text-brand-400" /> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
            <p class="relative text-sm text-slate-500">Laravel · Blade · Alpine.js · Tailwind CSS</p>
        </div>

        <div class="flex flex-1 flex-col justify-center px-6 py-12 sm:px-12">
            <div class="mx-auto w-full max-w-sm">
                <div class="mb-6 flex justify-end gap-1">
                    @foreach (config('crm.locales') as $code => $label)
                        <form method="POST" action="{{ route('locale', $code) }}">
                            @csrf
                            <button type="submit" title="{{ $label }}" @class(['rounded-md px-2 py-1 text-xs font-semibold uppercase', 'bg-brand-50 text-brand-700' => app()->getLocale() === $code, 'text-slate-500 hover:bg-slate-100' => app()->getLocale() !== $code])>{{ $code }}</button>
                        </form>
                    @endforeach
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>
    @include('partials.toasts')
</body>
</html>

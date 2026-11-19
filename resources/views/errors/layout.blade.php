<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex h-full items-center justify-center p-6 font-sans antialiased">
    <div class="text-center">
        <p class="text-sm font-semibold text-brand-600">@yield('code')</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">@yield('title')</h1>
        <p class="mt-3 text-slate-500">@yield('message')</p>
        <a href="{{ url('/') }}" class="mt-6 inline-flex rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-500">{{ __('Back to the dashboard') }}</a>
    </div>
</body>
</html>

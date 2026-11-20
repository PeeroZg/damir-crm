<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @include('partials.fonts')
    @vite(['resources/css/app.css'])
</head>
<body class="grid-dots flex min-h-screen items-center justify-center bg-ink-50 p-6">
    <div class="card max-w-md p-8 text-center">
        <div class="flex justify-center"><x-logo /></div>
        <p class="mt-8 font-mono text-sm font-medium text-ink-500">@yield('code')</p>
        <h1 class="mt-1 font-display text-3xl font-bold text-ink-900">@yield('title')</h1>
        <p class="mt-3 text-base text-ink-600">@yield('message')</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-6"><x-icon name="arrow-left" />{{ __('Back to the dashboard') }}</a>
    </div>
</body>
</html>

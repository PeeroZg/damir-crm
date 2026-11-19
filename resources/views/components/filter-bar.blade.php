{{-- GET filter form; keeps the current sort. --}}
@props(['action', 'placeholder' => null])
<form method="GET" action="{{ $action }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center">
    @foreach (request()->only(['sort', 'dir']) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    <div class="relative flex-1">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder ?? __('Search…') }}" class="form-input pl-9" aria-label="{{ __('Search') }}">
    </div>
    {{ $slot }}
    <div class="flex gap-2">
        <x-button type="submit" variant="secondary">{{ __('Filter') }}</x-button>
        @if (request()->hasAny(['q', 'status', 'stage', 'owner', 'company']))
            <x-button variant="ghost" :href="$action">{{ __('Reset') }}</x-button>
        @endif
    </div>
</form>

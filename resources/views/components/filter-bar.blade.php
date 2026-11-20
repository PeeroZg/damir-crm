{{-- GET filter form; keeps the current sort. --}}
@props(['action', 'placeholder' => null])
<form method="GET" action="{{ $action }}" class="flex flex-wrap items-center gap-2 border-b border-ink-100 p-3">
    @foreach (request()->only(['sort', 'dir']) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    <div class="relative min-w-[220px] flex-1">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-ink-400" />
        <label for="q" class="sr-only">{{ __('Search') }}</label>
        <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder ?? __('Search…') }}" class="input pl-9">
    </div>
    {{ $slot }}
    <x-button type="submit" variant="secondary" icon="funnel" class="h-10">{{ __('Filter') }}</x-button>
    @if (request()->hasAny(['q', 'status', 'stage', 'owner', 'company']))
        <x-button variant="ghost" :href="$action" class="h-10">{{ __('Reset') }}</x-button>
    @endif
</form>

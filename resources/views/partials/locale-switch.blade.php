<div class="seg h-8" role="group" aria-label="{{ __('Language') }}">
    @foreach (config('crm.locales') as $code => $label)
        <form method="POST" action="{{ route('locale', $code) }}" class="h-full">
            @csrf
            <button type="submit" lang="{{ $code }}" title="{{ $label }}" @class(['seg-item px-2.5 text-xs uppercase', 'is-active' => app()->getLocale() === $code]) @if (app()->getLocale() === $code) aria-current="true" @endif>{{ $code }}</button>
        </form>
    @endforeach
</div>

@props(['action', 'confirm' => null, 'label' => null])
<form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirm ?? __('Are you sure? This cannot be undone.')))">
    @csrf
    @method('DELETE')
    <x-button type="submit" variant="ghost" icon="trash" {{ $attributes->merge(['class' => 'text-rose-600 hover:bg-rose-50 hover:text-rose-700']) }}>{{ $label ?? __('Delete') }}</x-button>
</form>

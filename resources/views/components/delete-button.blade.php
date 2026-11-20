@props(['action', 'confirm' => null, 'label' => null, 'size' => 'md'])
<form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirm ?? __('Are you sure? This cannot be undone.')))">
    @csrf
    @method('DELETE')
    <x-button type="submit" variant="danger" icon="trash" :size="$size" {{ $attributes }}>{{ $label ?? __('Delete') }}</x-button>
</form>

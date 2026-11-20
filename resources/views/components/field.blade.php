{{-- Label + control + validation error. Pass the control in the slot. --}}
@props(['label', 'name', 'hint' => null, 'required' => false])
@php $errorKey = str_replace(['[', ']'], ['.', ''], $name); @endphp
<div {{ $attributes }}>
    <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="req" aria-hidden="true">*</span>@endif</label>
    {{ $slot }}
    @error($errorKey)
        <p class="error">{{ $message }}</p>
    @else
        @if ($hint)<p class="hint">{{ $hint }}</p>@endif
    @enderror
</div>

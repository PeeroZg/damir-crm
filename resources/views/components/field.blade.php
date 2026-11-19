{{-- Label + control + validation error. Pass the control in the slot. --}}
@props(['label', 'name', 'hint' => null, 'required' => false])
@php $errorKey = str_replace(['[', ']'], ['.', ''], $name); @endphp
<div {{ $attributes }}>
    <label for="{{ $name }}" class="form-label">
        {{ $label }} @if ($required)<span class="text-rose-500">*</span>@endif
    </label>
    {{ $slot }}
    @error($errorKey)
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @else
        @if ($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
    @enderror
</div>

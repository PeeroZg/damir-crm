@props(['column', 'label', 'default' => null])
@php
    $current = request('sort', $default);
    $dir = request('dir', 'asc');
    $next = $current === $column && $dir === 'asc' ? 'desc' : 'asc';
@endphp
<a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'dir' => $next, 'page' => null]) }}" class="group inline-flex items-center gap-1 hover:text-slate-700">
    {{ $label }}
    <span @class(['text-slate-300 group-hover:text-slate-400' => $current !== $column, 'text-brand-600' => $current === $column])>
        {{ $current === $column && $dir === 'desc' ? '↓' : '↑' }}
    </span>
</a>

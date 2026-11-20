@props(['column', 'label', 'default' => null])
@php
    $current = request('sort', $default);
    $dir = request('dir', 'asc');
    $next = $current === $column && $dir === 'asc' ? 'desc' : 'asc';
    $active = $current === $column;
@endphp
<a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'dir' => $next, 'page' => null]) }}" @class(['th-sort uppercase', 'text-ink-900' => $active])>
    {{ $label }}<x-icon :name="$active && $dir === 'desc' ? 'chevron-down' : 'arrow-up'" @class(['h-3 w-3', 'text-ink-900' => $active, 'text-ink-300' => ! $active]) />
</a>

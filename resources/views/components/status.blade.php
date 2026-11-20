{{-- Renders any UI enum (label + tone) as a pill. --}}
@props(['value'])
<x-pill :tone="$value->color()" {{ $attributes }}>{{ $value->label() }}</x-pill>

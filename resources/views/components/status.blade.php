{{-- Renders any UI enum (label + color) as a badge. --}}
@props(['value'])
<x-badge :color="$value->color()" {{ $attributes }}>{{ $value->label() }}</x-badge>

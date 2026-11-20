@props(['list', 'kanban', 'active'])
<div class="seg" role="group" aria-label="{{ __('View') }}">
    <a href="{{ $list }}" class="seg-item" @if ($active === 'list') aria-current="page" @endif><x-icon name="list" />{{ __('List') }}</a>
    <a href="{{ $kanban }}" class="seg-item" @if ($active === 'kanban') aria-current="page" @endif><x-icon name="columns" />{{ __('Kanban') }}</a>
</div>

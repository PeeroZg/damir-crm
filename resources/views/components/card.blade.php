@props(['title' => null, 'count' => null, 'padding' => true])
<section {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || isset($actions))
        <div class="card-header">
            <h2 class="card-title flex items-center gap-2">{{ $title }}@if ($count !== null)<span class="count">{{ $count }}</span>@endif</h2>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif
    <div @class(['card-body' => $padding])>
        {{ $slot }}
    </div>
</section>

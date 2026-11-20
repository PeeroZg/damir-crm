@props(['dark' => false, 'size' => 30])
<span {{ $attributes->merge(['class' => 'flex items-center gap-2.5']) }}>
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 32 32" fill="none" role="img" aria-label="Damir CRM">
        <rect width="32" height="32" rx="9" fill="{{ $dark ? '#2A2A27' : '#1A1A18' }}" />
        <rect x="8" y="8" width="5" height="4.2" rx="1.6" fill="#FFFFFF" />
        <rect x="8" y="13.9" width="5" height="4.2" rx="1.6" fill="#D4FA4E" />
        <rect x="8" y="19.8" width="5" height="4.2" rx="1.6" fill="#FFFFFF" />
        <path d="M15 10.1h1.2a5.9 5.9 0 0 1 0 11.8H15" stroke="#FFFFFF" stroke-width="4.2" stroke-linecap="round" />
    </svg>
    <span @class(['font-display text-[19px] font-bold tracking-tight', 'text-white' => $dark, 'text-ink-900' => ! $dark])>Damir <span class="text-ink-400">CRM</span></span>
</span>

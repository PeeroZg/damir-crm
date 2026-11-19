@props(['color' => 'slate', 'dot' => true])
@php
    // Full class strings so Tailwind's JIT can see them.
    $styles = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-500/20',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'violet' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    ];
    $dots = [
        'slate' => 'bg-slate-400', 'sky' => 'bg-sky-500', 'indigo' => 'bg-indigo-500', 'violet' => 'bg-violet-500',
        'amber' => 'bg-amber-500', 'emerald' => 'bg-emerald-500', 'rose' => 'bg-rose-500',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset '.($styles[$color] ?? $styles['slate'])]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $dots[$color] ?? $dots['slate'] }}"></span>
    @endif
    {{ $slot }}
</span>

@if ($paginator->hasPages())
    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-ink-100 px-5 py-3">
        <p class="text-sm text-ink-500">{!! __('Showing :from–:to of :total', [
            'from' => '<span class="font-medium text-ink-900 tnum">'.$paginator->firstItem(),
            'to' => $paginator->lastItem().'</span>',
            'total' => '<span class="font-medium text-ink-900 tnum">'.$paginator->total().'</span>',
        ]) !!}</p>
        <nav class="flex items-center gap-1" aria-label="{{ __('Pagination') }}">
            @if ($paginator->onFirstPage())
                <span class="btn btn-sm btn-ghost pointer-events-none opacity-40" aria-disabled="true"><x-icon name="chevron-left" />{{ __('Previous') }}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-sm btn-ghost"><x-icon name="chevron-left" />{{ __('Previous') }}</a>
            @endif

            <span class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-1 text-sm text-ink-400">{{ $element }}</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <a href="{{ $url }}" @class(['inline-flex h-8 min-w-[32px] items-center justify-center rounded-md px-2 text-sm font-medium tnum', 'bg-ink-900 text-white' => $page == $paginator->currentPage(), 'text-ink-600 hover:bg-ink-100' => $page != $paginator->currentPage()]) @if ($page == $paginator->currentPage()) aria-current="page" @endif>{{ $page }}</a>
                        @endforeach
                    @endif
                @endforeach
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-sm btn-secondary">{{ __('Next') }}<x-icon name="chevron-right" /></a>
            @else
                <span class="btn btn-sm btn-secondary pointer-events-none opacity-40" aria-disabled="true">{{ __('Next') }}<x-icon name="chevron-right" /></span>
            @endif
        </nav>
    </div>
@endif

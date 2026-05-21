@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-8 h-8 border border-gray-200 text-gray-300 text-xs font-semibold rounded-lg cursor-not-allowed bg-white flex items-center justify-center select-none">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="w-8 h-8 border border-gray-200 text-gray-500 hover:bg-gray-50 text-xs font-semibold rounded-lg transition-colors bg-white flex items-center justify-center">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="w-8 h-8 border border-gray-200 text-gray-400 text-xs font-semibold rounded-lg bg-white cursor-default flex items-center justify-center select-none">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-8 h-8 bg-[#0066FF] text-white text-xs font-semibold rounded-lg select-none flex items-center justify-center shadow-sm font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 border border-gray-200 text-gray-600 hover:bg-blue-50/50 hover:text-[#0066FF] hover:border-blue-200 text-xs font-semibold rounded-lg transition-colors bg-white flex items-center justify-center font-medium">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="w-8 h-8 border border-gray-200 text-gray-500 hover:bg-gray-50 text-xs font-semibold rounded-lg transition-colors bg-white flex items-center justify-center">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        @else
            <span class="w-8 h-8 border border-gray-200 text-gray-300 text-xs font-semibold rounded-lg cursor-not-allowed bg-white flex items-center justify-center select-none">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </span>
        @endif
    </nav>
@endif

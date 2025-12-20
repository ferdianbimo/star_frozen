@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        {{-- Mobile View --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 cursor-default rounded-xl">
                    <i class="fas fa-chevron-left mr-2 text-xs"></i>
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all shadow-sm">
                    <i class="fas fa-chevron-left mr-2 text-xs"></i>
                    Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all shadow-sm">
                    Selanjutnya
                    <i class="fas fa-chevron-right ml-2 text-xs"></i>
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 cursor-default rounded-xl">
                    Selanjutnya
                    <i class="fas fa-chevron-right ml-2 text-xs"></i>
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-slate-600">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-slate-800">{{ $paginator->firstItem() }}</span>
                        sampai
                        <span class="font-semibold text-slate-800">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari
                    <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rtl:flex-row-reverse gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-slate-400 bg-slate-100 cursor-default rounded-xl" aria-hidden="true">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm" aria-label="{{ __('pagination.previous') }}">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-slate-400 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 cursor-default rounded-xl shadow-lg shadow-indigo-500/30">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm" aria-label="{{ __('pagination.next') }}">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-slate-400 bg-slate-100 cursor-default rounded-xl" aria-hidden="true">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-3">

        <!-- Mobile View (Previous and Next Buttons) -->
        <div class="flex items-center justify-between w-full sm:hidden gap-3">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-900 border border-zinc-800 text-zinc-500 cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-[#18181b] hover:bg-white text-zinc-200 hover:text-black border border-zinc-700 shadow-sm transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    Previous
                </a>
            @endif

            <span class="text-xs font-bold text-zinc-400">
                Page <span class="text-white">{{ $paginator->currentPage() }}</span> of <span class="text-white">{{ $paginator->lastPage() }}</span>
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-[#18181b] hover:bg-white text-zinc-200 hover:text-black border border-zinc-700 shadow-sm transition-all duration-150">
                    Next
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-900 border border-zinc-800 text-zinc-500 cursor-not-allowed select-none">
                    Next
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </span>
            @endif
        </div>

        <!-- Desktop View (Showing results count + Page buttons) -->
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full gap-4">
            <div>
                <p class="text-xs text-zinc-400 font-medium">
                    Showing
                    @if ($paginator->firstItem())
                        <span class="font-bold text-white">{{ $paginator->firstItem() }}</span>
                        to
                        <span class="font-bold text-white">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    of
                    <span class="font-bold text-white">{{ $paginator->total() }}</span>
                    jobs
                </p>
            </div>

            <div>
                <div class="inline-flex items-center gap-1.5">

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-zinc-900 border border-zinc-800 text-zinc-500 cursor-not-allowed select-none">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                            <span>Previous</span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-[#18181b] hover:bg-white text-zinc-200 hover:text-black border border-zinc-700 shadow-sm transition-all duration-150">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                            <span>Previous</span>
                        </a>
                    @endif

                    {{-- Pagination Elements (Page Numbers) --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="px-2.5 py-1 text-xs font-bold text-zinc-500 select-none">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                        class="inline-flex items-center justify-center min-w-[34px] h-[32px] px-2.5 rounded-xl text-xs font-extrabold bg-white text-black border border-white shadow-md">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                        class="inline-flex items-center justify-center min-w-[34px] h-[32px] px-2.5 rounded-xl text-xs font-bold bg-[#18181b] hover:bg-zinc-800 text-zinc-300 hover:text-white border border-zinc-700 transition-all duration-150">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-[#18181b] hover:bg-white text-zinc-200 hover:text-black border border-zinc-700 shadow-sm transition-all duration-150">
                            <span>Next</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-zinc-900 border border-zinc-800 text-zinc-500 cursor-not-allowed select-none">
                            <span>Next</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </span>
                    @endif

                </div>
            </div>
        </div>

    </nav>
@endif

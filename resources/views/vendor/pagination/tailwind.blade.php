@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $start = max(1, $currentPage - 1);
        $end = min($lastPage, $start + 2);
        $start = max(1, $end - 2);
    @endphp

    <nav role="navigation" aria-label="{{ __('Sayfalama Gezintisi') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-700 leading-5 dark:text-gray-400">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span class="font-medium">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        <div class="flex items-center gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed dark:bg-gray-800 dark:text-gray-600">&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">&lsaquo;</a>
            @endif

            {{-- Limited Page Links --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $currentPage)
                    <span class="px-3 py-2 text-sm font-semibold text-white rounded-md" style="background-color: #0f766e">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">{{ $page }}</a>
                @endif
            @endfor

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">&rsaquo;</a>
            @else
                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed dark:bg-gray-800 dark:text-gray-600">&rsaquo;</span>
            @endif

            {{-- Last Page Link --}}
            @if ($currentPage === $lastPage)
                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed dark:bg-gray-800 dark:text-gray-600">&raquo;</span>
            @else
                <a href="{{ $paginator->url($lastPage) }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">&raquo;</a>
            @endif
        </div>
    </nav>
@endif

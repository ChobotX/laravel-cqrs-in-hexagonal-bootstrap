@props(['result'])

@if ($result->totalPages() > 1)
    @php
        $current = $result->pagination->page;
        $last = $result->totalPages();
        $perPage = $result->pagination->perPage;
        $window = 2;
        $from = max(1, $current - $window);
        $to = min($last, $current + $window);

        $queryParams = collect(request()->query())
            ->except('page')
            ->all();
        $buildUrl = fn(int $page): string => url()->current() .
            '?' .
            http_build_query(array_merge($queryParams, ['page' => $page, 'per_page' => $perPage]));
    @endphp

    <nav class="mt-6 flex items-center justify-between"
         aria-label="{{ __('messages.pagination.navigation') }}">
        <p class="text-sm text-gray-500">
            {{ __('messages.pagination.showing', [
                'from' => ($current - 1) * $perPage + 1,
                'to' => min($current * $perPage, $result->total),
                'total' => $result->total,
            ]) }}
        </p>
        <div class="flex items-center gap-1">
            @if ($result->hasPreviousPage())
                <a class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100"
                   href="{{ $buildUrl($current - 1) }}"
                   title="{{ __('messages.pagination.previous') }}">
                    &laquo;
                </a>
            @else
                <span class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300"
                      aria-disabled="true">
                    &laquo;
                </span>
            @endif

            @if ($from > 1)
                <a class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100"
                   href="{{ $buildUrl(1) }}"
                   title="{{ __('messages.pagination.page', ['page' => 1]) }}">
                    1
                </a>
                @if ($from > 2)
                    <span class="px-1 text-gray-400">&hellip;</span>
                @endif
            @endif

            @for ($i = $from; $i <= $to; $i++)
                @if ($i === $current)
                    <span class="inline-flex items-center rounded-lg bg-indigo-100 px-3 py-2 text-sm font-semibold text-indigo-700"
                          aria-current="page">
                        {{ $i }}
                    </span>
                @else
                    <a class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100"
                       href="{{ $buildUrl($i) }}"
                       title="{{ __('messages.pagination.page', ['page' => $i]) }}">
                        {{ $i }}
                    </a>
                @endif
            @endfor

            @if ($to < $last)
                @if ($to < $last - 1)
                    <span class="px-1 text-gray-400">&hellip;</span>
                @endif
                <a class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100"
                   href="{{ $buildUrl($last) }}"
                   title="{{ __('messages.pagination.page', ['page' => $last]) }}">
                    {{ $last }}
                </a>
            @endif

            @if ($result->hasNextPage())
                <a class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100"
                   href="{{ $buildUrl($current + 1) }}"
                   title="{{ __('messages.pagination.next') }}">
                    &raquo;
                </a>
            @else
                <span class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300"
                      aria-disabled="true">
                    &raquo;
                </span>
            @endif
        </div>
    </nav>
@endif

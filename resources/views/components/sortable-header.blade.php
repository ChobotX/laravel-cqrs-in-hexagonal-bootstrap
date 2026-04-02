@props(['column', 'label', 'sorting'])

@php
    $isActive = $sorting->column === $column;
    $nextDirection = $isActive ? $sorting->direction->toggle()->value : 'asc';
    $queryParams = collect(request()->query())
        ->except(['page', 'sort', 'direction'])
        ->all();
    $url =
        url()->current() .
        '?' .
        http_build_query(
            array_merge($queryParams, [
                'sort' => $column,
                'direction' => $nextDirection,
            ]),
        );
@endphp

<th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
    scope="col">
    <a class="group inline-flex items-center gap-1 transition-colors hover:text-gray-700"
       href="{{ $url }}"
       title="{{ $label }}">
        {{ $label }}
        @if ($isActive)
            @if ($sorting->direction->value === 'asc')
                <x-heroicon-s-chevron-up class="size-3.5 text-indigo-600" />
            @else
                <x-heroicon-s-chevron-down class="size-3.5 text-indigo-600" />
            @endif
        @else
            <x-heroicon-s-chevron-up-down class="size-3.5 text-gray-300 group-hover:text-gray-400" />
        @endif
    </a>
</th>

@props(['items' => []])

@if (count($items) > 1)
    <nav class="mb-4"
         aria-label="{{ __('messages.breadcrumb.navigation') }}">
        <ol class="flex items-center gap-1.5 text-sm text-gray-500">
            @foreach ($items as $index => $item)
                @if ($index > 0)
                    <li class="flex items-center"
                        aria-hidden="true">
                        <svg class="size-3.5 text-gray-300"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </li>
                @endif
                <li>
                    @if ($loop->last)
                        <span class="font-medium text-gray-900"
                              aria-current="page">{{ $item['label'] }}</span>
                    @else
                        <a class="cursor-pointer transition-colors hover:text-indigo-600"
                           href="{{ $item['href'] }}"
                           title="{{ $item['label'] }}">{{ $item['label'] }}</a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif

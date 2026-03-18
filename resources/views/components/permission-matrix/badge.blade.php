@php
    $colors = match ($variant) {
        'green' => 'bg-green-50 text-green-700 ring-green-700/10',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-700/10',
        'red' => 'bg-red-50 text-red-700 ring-red-700/10',
    };
@endphp

<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 {{ $colors }} {{ ($strikethrough ?? false) ? 'line-through' : '' }}"
      title="{{ $title }}">
    {{ $content }}
</span>

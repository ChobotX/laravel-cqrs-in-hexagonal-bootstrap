@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->class('rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5') }}>
    @if ($title || $subtitle)
        <div class="border-b border-gray-200 px-6 py-4">
            @if ($title)
                <h3 class="text-base font-medium text-gray-900 sm:text-sm">{{ $title }}</h3>
            @endif
            @if ($subtitle)
                <p class="mt-0.5 text-base text-gray-500 sm:text-sm">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>

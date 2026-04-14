@props(['label', 'class' => ''])

<button class="cursor-pointer"
        data-tooltip="{{ $label }}"
        aria-label="{{ $label }}"
        {{ $attributes->merge(['class' => $class, 'type' => 'button']) }}>
    {{ $slot }}
</button>

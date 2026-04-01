@props(['label', 'class' => ''])

<button data-tooltip="{{ $label }}"
        aria-label="{{ $label }}"
        {{ $attributes->merge(['class' => $class, 'type' => 'button']) }}>
    {{ $slot }}
</button>

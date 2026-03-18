@props(['label', 'class' => ''])

<button title="{{ $label }}"
        aria-label="{{ $label }}"
        {{ $attributes->merge(['class' => $class, 'type' => 'button']) }}>
    {{ $slot }}
</button>

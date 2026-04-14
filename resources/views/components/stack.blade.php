@props([
    'gap' => 'default',
])

@php
    $gapClass = \App\Presentation\Support\StackGapClassMap::forGap($gap);
@endphp

<div {{ $attributes->class('flex flex-col ' . $gapClass) }}>
    {{ $slot }}
</div>

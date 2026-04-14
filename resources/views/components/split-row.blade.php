@props([
    'leadingMaxWidth' => 'max-w-[220px]',
    'contentGap' => 'default',
])

@php
    $contentGapClass = \App\Presentation\Support\StackGapClassMap::forGap($contentGap);
@endphp

<div {{ $attributes->class('flex flex-col md:flex-row md:items-start md:gap-10') }}>
    <div class="{{ $leadingMaxWidth }} mx-auto mb-10 w-full shrink-0 md:mb-0">
        {{ $leading }}
    </div>
    <div class="{{ $contentGapClass }} flex min-w-0 flex-1 flex-col">
        {{ $trailing }}
    </div>
</div>

@props([
    'name' => 'value',
    'checked' => false,
    'disabled' => false,
    'autoSubmit' => false,
    'ariaLabel' => null,
    'ariaLabelledby' => null,
    'toggleId' => null,
    'testId' => null,
])

@php
    $testIdAttr = is_string($testId) && $testId !== '' ? 'data-testid="' . e($testId) . '"' : '';
@endphp

<label class="relative inline-flex cursor-pointer items-center"
       {!! $testIdAttr !!}>
    <input name="{{ $name }}"
           type="hidden"
           value="0">
    <input class="peer sr-only"
           name="{{ $name }}"
           type="checkbox"
           value="1"
           @if (is_string($toggleId) && $toggleId !== '') id="{{ $toggleId }}" @endif
           {{ $checked ? 'checked' : '' }}
           {{ $disabled ? 'disabled' : '' }}
           {{ $autoSubmit ? 'data-auto-submit' : '' }}
           @if (is_string($ariaLabelledby) && $ariaLabelledby !== '') aria-labelledby="{{ $ariaLabelledby }}"
           @elseif (is_string($ariaLabel) && $ariaLabel !== '')
               aria-label="{{ $ariaLabel }}" @endif>
    <div
         class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-600 peer-focus:ring-offset-2 peer-disabled:cursor-not-allowed peer-disabled:opacity-50">
    </div>
</label>

@props([
    'permission' => null,
    'skipPermission' => false,
    'action',
    'icon',
    'label',
    'variant' => 'default',
    'testId' => null,
])

@php
    $testIdAttr = $testId !== null ? 'data-testid="' . e($testId) . '"' : '';
    $colorClasses = $variant === 'amber' ? 'text-amber-600 hover:text-amber-800' : 'text-gray-500 hover:text-gray-700';
@endphp

@if ($permission !== null || $skipPermission)
    @if ($permission !== null)
        @hasPermission($permission)
            <form method="POST"
                  action="{{ $action }}">
                @csrf
                <button class="{{ $colorClasses }} flex items-center gap-1.5 text-sm transition-colors"
                        data-tooltip="{{ $label }}"
                        type="submit"
                        aria-label="{{ $label }}"
                        {!! $testIdAttr !!}>
                    <x-dynamic-component class="h-5 w-5"
                                         aria-hidden="true"
                                         :component="$icon" />
                </button>
            </form>
        @endhasPermission
    @else
        <form method="POST"
              action="{{ $action }}">
            @csrf
            <button class="{{ $colorClasses }} flex items-center gap-1.5 text-sm transition-colors"
                    data-tooltip="{{ $label }}"
                    type="submit"
                    aria-label="{{ $label }}"
                    {!! $testIdAttr !!}>
                <x-dynamic-component class="h-5 w-5"
                                     aria-hidden="true"
                                     :component="$icon" />
            </button>
        </form>
    @endif
@endif

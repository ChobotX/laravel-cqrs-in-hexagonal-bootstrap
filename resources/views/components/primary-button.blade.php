@props([
    'permission' => null,
    'skipPermission' => false,
    'href' => null,
    'action' => null,
    'method' => 'POST',
    'label',
    'variant' => 'primary',
    'testId' => null,
])

@php
    $testIdAttr = $testId !== null ? 'data-testid="' . e($testId) . '"' : '';
    $variantClasses = match ($variant) {
        'secondary'
            => 'rounded-lg border border-gray-300 px-4 py-2.5 text-base font-semibold text-gray-700 transition-colors hover:bg-gray-50 sm:text-sm',
        'amber'
            => 'rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-600',
        'login'
            => 'w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-base font-semibold text-white shadow-sm transition-colors hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 sm:text-sm',
        default => $href !== null
            ? 'rounded-lg bg-indigo-600 px-4 py-2.5 text-base font-semibold text-white shadow-sm transition-colors hover:bg-indigo-500 sm:text-sm'
            : 'rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-500',
    };
@endphp

@if ($permission !== null || $skipPermission)
    @if ($permission !== null)
        @hasPermission($permission)
            @if ($action !== null)
                <form class="inline"
                      method="POST"
                      action="{{ $action }}">
                    @csrf
                    @if (strtoupper($method) !== 'POST')
                        @method($method)
                    @endif
                    <button class="{{ $variantClasses }}"
                            type="submit"
                            title="{{ $label }}"
                            aria-label="{{ $label }}"
                            {!! $testIdAttr !!}>
                        {{ $label }}
                    </button>
                </form>
            @elseif ($href !== null)
                <a class="{{ $variantClasses }}"
                   href="{{ $href }}"
                   title="{{ $label }}"
                   aria-label="{{ $label }}"
                   {!! $testIdAttr !!}>
                    {{ $label }}
                </a>
            @else
                <button class="{{ $variantClasses }}"
                        type="submit"
                        title="{{ $label }}"
                        aria-label="{{ $label }}"
                        {!! $testIdAttr !!}>
                    {{ $label }}
                </button>
            @endif
        @endhasPermission
    @else
        @if ($action !== null)
            <form class="inline"
                  method="POST"
                  action="{{ $action }}">
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method($method)
                @endif
                <button class="{{ $variantClasses }}"
                        type="submit"
                        title="{{ $label }}"
                        aria-label="{{ $label }}"
                        {!! $testIdAttr !!}>
                    {{ $label }}
                </button>
            </form>
        @elseif ($href !== null)
            <a class="{{ $variantClasses }}"
               href="{{ $href }}"
               title="{{ $label }}"
               aria-label="{{ $label }}"
               {!! $testIdAttr !!}>
                {{ $label }}
            </a>
        @else
            <button class="{{ $variantClasses }}"
                    type="submit"
                    title="{{ $label }}"
                    aria-label="{{ $label }}"
                    {!! $testIdAttr !!}>
                {{ $label }}
            </button>
        @endif
    @endif
@endif

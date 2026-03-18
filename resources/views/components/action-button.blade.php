@props([
    'permission' => null,
    'skipPermission' => false,
    'href' => null,
    'action' => null,
    'method' => 'POST',
    'icon',
    'label',
    'variant' => 'default',
    'confirm' => false,
    'confirmTitle' => null,
    'confirmMessage' => null,
])

@php
    $hoverClasses =
        $variant === 'danger' ? 'hover:bg-red-50 hover:text-red-600' : 'hover:bg-indigo-50 hover:text-indigo-600';
    $baseClasses = "$hoverClasses inline-flex cursor-pointer items-center justify-center rounded-lg p-2 text-gray-400 transition-colors";
@endphp

@if ($permission !== null || $skipPermission)
    @if ($permission !== null)
        @hasPermission($permission)
            @if ($href !== null)
                <a class="{{ $baseClasses }}"
                   href="{{ $href }}"
                   title="{{ $label }}"
                   aria-label="{{ $label }}">
                    <x-dynamic-component class="h-5 w-5"
                                         aria-hidden="true"
                                         :component="$icon" />
                </a>
            @elseif ($action !== null)
                <form class="inline"
                      @if ($confirm) data-confirm-delete @endif
                      method="POST"
                      action="{{ $action }}">
                    @csrf
                    @if (strtoupper($method) !== 'POST')
                        @method($method)
                    @endif
                    <button class="{{ $baseClasses }}"
                            type="submit"
                            title="{{ $label }}"
                            aria-label="{{ $label }}"
                            @if ($confirm && $confirmTitle !== null) data-confirm-title="{{ $confirmTitle }}" @endif
                            @if ($confirm && $confirmMessage !== null) data-confirm-message="{{ $confirmMessage }}" @endif>
                        <x-dynamic-component class="h-5 w-5"
                                             aria-hidden="true"
                                             :component="$icon" />
                    </button>
                </form>
            @endif
        @endhasPermission
    @else
        @if ($href !== null)
            <a class="{{ $baseClasses }}"
               href="{{ $href }}"
               title="{{ $label }}"
               aria-label="{{ $label }}">
                <x-dynamic-component class="h-5 w-5"
                                     aria-hidden="true"
                                     :component="$icon" />
            </a>
        @elseif ($action !== null)
            <form class="inline"
                  @if ($confirm) data-confirm-delete @endif
                  method="POST"
                  action="{{ $action }}">
                @csrf
                @if (strtoupper($method) !== 'POST')
                    @method($method)
                @endif
                <button class="{{ $baseClasses }}"
                        type="submit"
                        title="{{ $label }}"
                        aria-label="{{ $label }}"
                        @if ($confirm && $confirmTitle !== null) data-confirm-title="{{ $confirmTitle }}" @endif
                        @if ($confirm && $confirmMessage !== null) data-confirm-message="{{ $confirmMessage }}" @endif>
                    <x-dynamic-component class="h-5 w-5"
                                         aria-hidden="true"
                                         :component="$icon" />
                </button>
            </form>
        @endif
    @endif
@endif

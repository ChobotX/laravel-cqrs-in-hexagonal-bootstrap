@props([
    'permission' => null,
    'skipPermission' => false,
    'icon',
    'label',
    'class' => 'text-gray-400 transition-colors hover:text-gray-600',
])

@if ($permission !== null || $skipPermission)
    @if ($permission !== null)
        @hasPermission($permission)
            <button class="{{ $class }}"
                    type="submit"
                    title="{{ $label }}"
                    aria-label="{{ $label }}">
                <x-dynamic-component class="h-4 w-4"
                                     aria-hidden="true"
                                     :component="$icon" />
            </button>
        @endhasPermission
    @else
        <button class="{{ $class }}"
                type="submit"
                title="{{ $label }}"
                aria-label="{{ $label }}">
            <x-dynamic-component class="h-4 w-4"
                                 aria-hidden="true"
                                 :component="$icon" />
        </button>
    @endif
@endif

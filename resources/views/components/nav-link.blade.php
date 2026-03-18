@props([
    'permission' => null,
    'skipPermission' => false,
    'href',
    'icon',
    'label',
    'active' => false,
])

@if ($permission !== null || $skipPermission)
    @if ($permission !== null)
        @hasPermission($permission)
            <a class="{{ $active ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 rounded-lg px-3 py-2 text-base font-medium transition-colors sm:text-sm"
               href="{{ $href }}"
               title="{{ $label }}"
               {{ $active ? 'aria-current=page' : '' }}>
                <x-dynamic-component class="h-5 w-5 shrink-0"
                                     aria-hidden="true"
                                     :component="$icon" />
                {{ $label }}
            </a>
        @endhasPermission
    @else
        <a class="{{ $active ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 rounded-lg px-3 py-2 text-base font-medium transition-colors sm:text-sm"
           href="{{ $href }}"
           title="{{ $label }}"
           {{ $active ? 'aria-current=page' : '' }}>
            <x-dynamic-component class="h-5 w-5 shrink-0"
                                 aria-hidden="true"
                                 :component="$icon" />
            {{ $label }}
        </a>
    @endif
@endif

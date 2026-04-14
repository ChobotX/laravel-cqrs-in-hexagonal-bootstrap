<div class="relative"
     data-dropdown>
    <button class="flex cursor-pointer items-center text-gray-500 transition-colors hover:text-gray-700"
            data-dropdown-toggle
            data-tooltip="{{ __('messages.a11y.language_switcher') }}"
            type="button"
            aria-label="{{ __('messages.a11y.language_switcher') }}"
            aria-expanded="false"
            aria-haspopup="true">
        <x-heroicon-o-globe-europe-africa class="h-5 w-5"
                                          aria-hidden="true" />
    </button>

    <div class="absolute right-0 z-50 mt-2 hidden w-40 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-950/5"
         data-dropdown-menu
         role="menu">
        @foreach ([
        'cs' => ['flag' => "\u{1F1E8}\u{1F1FF}", 'label' => 'Čeština'],
        'en' => ['flag' => "\u{1F1EC}\u{1F1E7}", 'label' => 'English'],
    ] as $locale => $meta)
            @if ($locale === app()->getLocale())
                <span class="flex items-center gap-2.5 bg-indigo-50 px-3 py-2 text-base font-medium text-indigo-600 sm:text-sm"
                      role="menuitem"
                      aria-current="true">
                    <span class="text-base">{{ $meta['flag'] }}</span>
                    {{ $meta['label'] }}
                </span>
            @else
                <form method="POST"
                      action="{{ route('locale.update') }}">
                    @csrf
                    <input name="locale"
                           type="hidden"
                           value="{{ $locale }}">
                    <button class="flex w-full cursor-pointer items-center gap-2.5 px-3 py-2 text-left text-base text-gray-700 transition-colors hover:bg-gray-50 sm:text-sm"
                            type="submit"
                            title="{{ $meta['label'] }}"
                            role="menuitem">
                        <span class="text-base">{{ $meta['flag'] }}</span>
                        {{ $meta['label'] }}
                    </button>
                </form>
            @endif
        @endforeach
    </div>
</div>

<nav class="flex flex-1 flex-col px-3 py-4"
     aria-label="{{ __('messages.a11y.main_navigation') }}">
    <div class="flex-1">
        <x-nav-link skip-permission
                    :href="$sidebarNav->dashboard->href"
                    :icon="$sidebarNav->dashboard->icon"
                    :label="$sidebarNav->dashboard->label"
                    :active="$sidebarNav->dashboard->active" />

        @foreach ($sidebarNav->sections as $section)
            <p class="mb-2 mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                {{ $section->label }}
            </p>

            @foreach ($section->blocks as $block)
                @if ($block->collapsible)
                    <details class="group/nav-block mb-1"
                             id="{{ $sidebarNavInstance ?? 'sidebar' }}-{{ $block->id }}"
                             @if ($block->open) open @endif>
                        <summary
                                 class="flex cursor-pointer list-none items-center justify-between gap-2 rounded-lg px-3 py-2 text-base font-medium text-gray-300 transition-colors hover:bg-white/10 hover:text-white sm:text-sm [&::-webkit-details-marker]:hidden">
                            <span class="select-none">{{ $block->label }}</span>
                            <x-heroicon-s-chevron-down class="h-4 w-4 shrink-0 text-gray-400 transition-transform group-open/nav-block:rotate-180"
                                                       aria-hidden="true" />
                        </summary>
                        <div class="mb-1 ml-2 mt-1 space-y-0.5 border-l border-white/10 pl-3">
                            @foreach ($block->items as $item)
                                <x-nav-link skip-permission
                                            :href="$item->href"
                                            :icon="$item->icon"
                                            :label="$item->label"
                                            :active="$item->active" />
                            @endforeach
                        </div>
                    </details>
                @else
                    @foreach ($block->items as $item)
                        <x-nav-link skip-permission
                                    :href="$item->href"
                                    :icon="$item->icon"
                                    :label="$item->label"
                                    :active="$item->active" />
                    @endforeach
                @endif
            @endforeach
        @endforeach
    </div>
</nav>

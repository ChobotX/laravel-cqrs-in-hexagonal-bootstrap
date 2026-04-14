<aside class="hidden min-h-screen w-64 flex-col bg-gray-950 text-white lg:flex">
    <a class="flex items-center gap-2.5 border-b border-white/10 px-5 py-4 hover:bg-white/5"
       href="{{ route('dashboard') }}"
       title="{{ __('messages.nav.dashboard') }}">
        @if (isset($tenantLogoUrl) && $tenantLogoUrl)
            <img class="h-8 w-8 rounded-lg object-cover"
                 src="{{ $tenantLogoUrl }}"
                 alt="{{ $tenantName ?? '' }}">
        @else
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600">
                <svg class="h-5 w-5 text-white"
                     aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
            </div>
        @endif
        <span class="text-base font-semibold">{{ $tenantName ?? 'Bootstrap' }}</span>
    </a>

    @include('components.sidebar-nav', ['sidebarNavInstance' => 'desktop'])
</aside>

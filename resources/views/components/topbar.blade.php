@php
    $authenticatedUser = app(\App\Contract\Auth\AuthenticatedUser::class);
@endphp

<header class="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3 sm:px-6">
    <div class="flex items-center gap-2 sm:gap-4">
        <x-control-button class="text-gray-500 hover:text-gray-700 lg:hidden"
                          data-sidebar-open
                          :label="__('messages.a11y.open_menu')">
            <svg class="h-6 w-6"
                 aria-hidden="true"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke-width="1.5"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </x-control-button>
        <h1 class="text-lg font-semibold text-gray-900">@yield('title', __('messages.nav.dashboard'))</h1>
    </div>
    <div class="flex items-center gap-2 sm:gap-4">
        <div class="flex items-center gap-2.5">
            <div
                 class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->name, strpos(auth()->user()->name, ' ') + 1, 1)) }}
            </div>
            <span class="hidden text-base text-gray-600 sm:inline sm:text-sm">{{ auth()->user()->email }}</span>
        </div>
        @include('components.locale-dropdown')
        @if ($authenticatedUser->isImpersonating())
            <x-topbar-button skip-permission
                             :action="route('impersonation.stop')"
                             icon="heroicon-o-arrow-uturn-left"
                             :label="__('messages.impersonation.stop')"
                             variant="amber" />
        @endif
        <x-topbar-button skip-permission
                         :action="route('logout')"
                         icon="heroicon-o-arrow-right-on-rectangle"
                         :label="__('messages.nav.logout')" />
    </div>
</header>

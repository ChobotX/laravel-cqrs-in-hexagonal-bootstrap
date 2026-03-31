<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token"
          content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.nav.dashboard'))</title>
    @include('components.sentry-meta')
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>

<body class="flex min-h-screen bg-gray-50 font-sans">
    <a class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:rounded-lg focus:bg-indigo-600 focus:p-4 focus:text-white"
       href="#main-content"
       title="{{ __('messages.a11y.skip_to_content') }}">{{ __('messages.a11y.skip_to_content') }}</a>

    <div class="fixed inset-0 z-40 hidden lg:hidden"
         id="mobile-sidebar-overlay"
         role="dialog"
         aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/80"
             data-sidebar-backdrop></div>
        <aside class="fixed inset-y-0 left-0 flex w-64 flex-col bg-gray-950 text-white">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <div class="flex items-center gap-2.5">
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
                    <span class="text-base font-semibold">Bootstrap</span>
                </div>
                <x-control-button class="text-gray-400 hover:text-white"
                                  data-sidebar-close
                                  :label="__('messages.a11y.close_menu')">
                    <svg class="h-6 w-6"
                         aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </x-control-button>
            </div>
            @include('components.sidebar-nav')
        </aside>
    </div>

    @include('components.sidebar')

    <div class="flex min-w-0 flex-1 flex-col">
        @include('components.impersonation-banner')
        @include('components.topbar')

        <main class="flex-1 p-6"
              id="main-content">
            @include('components.flash')
            @yield('content')
        </main>
    </div>
    <div id="app-dialog"></div>
    <div id="app-toast"></div>
</body>

</html>

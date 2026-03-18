<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('messages.auth.login'))</title>
    @include('components.sentry-meta')
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>

<body
      class="flex min-h-screen items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-cyan-50 font-sans">
    <div class="absolute right-4 top-4">
        @include('components.locale-dropdown')
    </div>

    <main class="w-full max-w-md px-4">
        <div class="rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-950/5">
            <div class="mb-6 flex justify-center">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                        <svg class="h-6 w-6 text-white"
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
                    <span class="text-xl font-bold text-gray-900">Bootstrap</span>
                </div>
            </div>
            @yield('content')
        </div>
    </main>
    <div id="app-toast"></div>
</body>

</html>

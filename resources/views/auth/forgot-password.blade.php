@extends('layouts.guest')

@section('title', __('messages.auth.forgot_password'))

@section('content')
    <h2 class="text-center text-xl font-semibold text-gray-900">{{ __('messages.auth.forgot_password_title') }}</h2>
    <p class="mb-6 mt-1 text-center text-base text-gray-500 sm:text-sm">{{ __('messages.auth.forgot_password_subtitle') }}
    </p>

    @include('components.flash')

    <form class="space-y-5"
          method="POST"
          action="{{ route('password.email') }}">
        @csrf

        <div>
            <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                   for="email">{{ __('messages.auth.email') }}</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <x-heroicon-o-envelope class="h-5 w-5 text-gray-400"
                                           aria-hidden="true" />
                </div>
                <input class="block w-full rounded-lg border border-gray-300 py-2.5 pl-11 pr-3.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                       id="email"
                       name="email"
                       type="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       placeholder="you@example.com"
                       @error('email') aria-describedby="email-error" aria-invalid="true" @enderror>
            </div>
            @error('email')
                <p class="mt-1 text-base text-red-600 sm:text-sm"
                   id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <x-primary-button skip-permission
                          variant="login"
                          :label="__('messages.auth.send_reset_link')" />
    </form>

    <p class="mt-4 text-center text-base text-gray-500 sm:text-sm">
        <a class="font-medium text-indigo-600 hover:text-indigo-500"
           href="{{ route('login') }}"
           title="{{ __('messages.auth.sign_in') }}">{{ __('messages.auth.sign_in') }}</a>
    </p>
@endsection

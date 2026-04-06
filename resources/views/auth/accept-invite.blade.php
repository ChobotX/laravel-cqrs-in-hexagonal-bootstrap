@extends('layouts.guest')

@section('title', __('messages.auth.accept_invite'))

@section('content')
    <h2 class="text-center text-xl font-semibold text-gray-900">{{ __('messages.auth.accept_invite_title') }}</h2>
    <p class="mb-6 mt-1 text-center text-base text-gray-500 sm:text-sm">
        {{ __('messages.auth.accept_invite_subtitle', ['name' => $userName]) }}</p>

    @include('components.flash')

    <form class="space-y-5"
          method="POST"
          action="{{ url()->full() }}">
        @csrf

        <div>
            <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                   for="password">{{ __('messages.auth.password') }}</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400"
                                              aria-hidden="true" />
                </div>
                <input class="block w-full rounded-lg border border-gray-300 py-2.5 pl-11 pr-3.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                       id="password"
                       name="password"
                       type="password"
                       autocomplete="new-password"
                       required
                       autofocus
                       placeholder="••••••••"
                       @error('password') aria-describedby="password-error" aria-invalid="true" @enderror>
            </div>
            @error('password')
                <p class="mt-1 text-base text-red-600 sm:text-sm"
                   id="password-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                   for="password_confirmation">{{ __('messages.auth.confirm_password') }}</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400"
                                              aria-hidden="true" />
                </div>
                <input class="block w-full rounded-lg border border-gray-300 py-2.5 pl-11 pr-3.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                       id="password_confirmation"
                       name="password_confirmation"
                       type="password"
                       autocomplete="new-password"
                       required
                       placeholder="••••••••"
                       @error('password_confirmation') aria-describedby="password_confirmation-error" aria-invalid="true" @enderror>
            </div>
            @error('password_confirmation')
                <p class="mt-1 text-base text-red-600 sm:text-sm"
                   id="password_confirmation-error">{{ $message }}</p>
            @enderror
        </div>

        <x-primary-button skip-permission
                          variant="login"
                          :label="__('messages.auth.set_password')" />
    </form>
@endsection

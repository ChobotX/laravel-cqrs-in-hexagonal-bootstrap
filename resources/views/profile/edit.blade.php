@extends('layouts.app')

@section('title', __('messages.profile.title'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.profile.title')],
    ]" />

    <form class="space-y-6"
          method="POST"
          action="{{ route('profile.update') }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-form-card :title="__('messages.profile.info_title')"
                         :subtitle="__('messages.profile.subtitle')">
                <div class="space-y-5">
                    @include('partials.form.avatar-field', ['user' => $user])
                    @include('partials.form.profile-fields', [
                        'user' => $user,
                        'canEditEmail' => $canEditEmail,
                    ])
                </div>
            </x-form-card>

            <x-form-card :title="__('messages.profile.password_title')"
                         :subtitle="__('messages.profile.password_subtitle')">
                <div class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                               for="password">{{ __('messages.users.password') }}</label>
                        <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                               id="password"
                               name="password"
                               type="password"
                               autocomplete="new-password"
                               @error('password') aria-describedby="password-error" aria-invalid="true" @enderror>
                        @error('password')
                            <p class="mt-1 text-base text-red-600 sm:text-sm"
                               id="password-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                               for="password_confirmation">{{ __('messages.users.confirm_password') }}</label>
                        <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                               id="password_confirmation"
                               name="password_confirmation"
                               type="password"
                               autocomplete="new-password"
                               @error('password_confirmation') aria-describedby="password_confirmation-error" aria-invalid="true" @enderror>
                        @error('password_confirmation')
                            <p class="mt-1 text-base text-red-600 sm:text-sm"
                               id="password_confirmation-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-form-card>
        </div>

        <x-form-card :title="__('messages.notifications.preferences_title')"
                     :subtitle="__('messages.notifications.preferences_subtitle')">
            <div id="app-notification-preferences"
                 data-preferences="{{ json_encode($notificationPreferences) }}">
            </div>
        </x-form-card>

        <div class="flex items-center gap-3">
            <x-primary-button skip-permission
                              :label="__('messages.users.update_action')" />
            <x-primary-button skip-permission
                              variant="secondary"
                              :href="route('dashboard')"
                              :label="__('messages.users.cancel')" />
        </div>
    </form>
@endsection

@extends('layouts.app')

@section('title', __('messages.users.create'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.users'), 'href' => route('users.index')],
        ['label' => __('messages.users.create')],
    ]" />

    <div class="max-w-lg">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.users.create_subtitle') }}</p>
            </div>

            <form class="space-y-5 p-6"
                  method="POST"
                  action="{{ route('users.store') }}"
                  enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="avatar">{{ __('messages.users.avatar') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2 text-base text-gray-700 shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="avatar"
                           name="avatar"
                           type="file"
                           accept="image/*"
                           @error('avatar') aria-describedby="avatar-error" aria-invalid="true" @enderror>
                    @error('avatar')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="avatar-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="name">{{ __('messages.users.name') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="name"
                           name="name"
                           type="text"
                           value="{{ old('name') }}"
                           required
                           @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
                    @error('name')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="email">{{ __('messages.users.email') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="email"
                           name="email"
                           type="email"
                           value="{{ old('email') }}"
                           required
                           @error('email') aria-describedby="email-error" aria-invalid="true" @enderror>
                    @error('email')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="password">{{ __('messages.users.password') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="password"
                           name="password"
                           type="password"
                           autocomplete="new-password"
                           required
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
                           required
                           @error('password_confirmation') aria-describedby="password_confirmation-error" aria-invalid="true" @enderror>
                    @error('password_confirmation')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="password_confirmation-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.users.create_action')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('users.index')"
                                      :label="__('messages.users.cancel')" />
                </div>
            </form>
        </div>
    </div>
@endsection

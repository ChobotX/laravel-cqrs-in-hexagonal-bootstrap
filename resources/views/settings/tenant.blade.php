@extends('layouts.app')

@section('title', __('messages.settings.title'))

@section('content')
    <div class="mb-6">
        <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.settings.subtitle') }}</p>
    </div>

    <div class="mb-6 max-w-lg">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <form class="space-y-5 p-6"
                  method="POST"
                  action="{{ route('settings.update') }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="name">{{ __('messages.settings.name') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="name"
                           name="name"
                           data-testid="tenant-name-input"
                           type="text"
                           value="{{ old('name', $settings->name) }}"
                           required
                           @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
                    @error('name')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="display_timezone">{{ __('messages.settings.display_timezone') }}</label>
                    <select class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                            id="display_timezone"
                            name="display_timezone"
                            data-testid="tenant-display-timezone-select">
                        <option value="">{{ __('messages.settings.display_timezone_browser') }}</option>
                        @foreach ($ianaTimezones as $ianaTimezone)
                            <option value="{{ $ianaTimezone }}"
                                    @selected(old('display_timezone', $settings->displayTimezone) === $ianaTimezone)>{{ $ianaTimezone }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">{{ __('messages.settings.display_timezone_hint') }}</p>
                    @error('display_timezone')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="display-timezone-error">{{ $message }}</p>
                    @enderror
                </div>

                <div data-avatar-field>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="logo">{{ __('messages.settings.logo') }}</label>
                    <input name="remove_logo"
                           data-avatar-remove
                           data-testid="remove-logo-checkbox"
                           type="hidden"
                           value="0">
                    <div class="flex items-center gap-4">
                        @if ($settings->logoUrl)
                            <div class="relative shrink-0"
                                 data-avatar-preview>
                                <img class="h-16 w-16 rounded-lg object-cover ring-1 ring-gray-200"
                                     src="{{ $settings->logoUrl }}"
                                     alt="{{ __('messages.settings.current_logo') }}">
                                <button class="absolute -right-1 -top-1 flex h-5 w-5 cursor-pointer items-center justify-center rounded-full bg-red-500 text-white shadow transition-colors hover:bg-red-600"
                                        data-avatar-remove-btn
                                        type="button"
                                        title="{{ __('messages.settings.remove_logo') }}"
                                        aria-label="{{ __('messages.settings.remove_logo') }}">
                                    <x-heroicon-s-x-mark class="h-3 w-3" />
                                </button>
                            </div>
                        @else
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-sm font-semibold text-indigo-700"
                                 data-avatar-preview>
                                <x-heroicon-o-photo class="h-6 w-6 text-indigo-400" />
                            </div>
                        @endif
                        <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2 text-base text-gray-700 shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                               id="logo"
                               name="logo"
                               data-testid="logo-upload-input"
                               type="file"
                               accept="image/*"
                               @error('logo') aria-describedby="logo-error" aria-invalid="true" @enderror>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">{{ __('messages.settings.logo_hint') }}</p>
                    @error('logo')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="logo-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.settings.update_action')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('dashboard')"
                                      :label="__('messages.settings.cancel')" />
                </div>
            </form>
        </div>
    </div>
@endsection

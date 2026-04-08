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
                           type="text"
                           value="{{ old('name', $settings->name) }}"
                           required
                           data-testid="tenant-name-input"
                           @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
                    @error('name')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm">{{ __('messages.settings.logo') }}</label>

                    @if ($settings->logoUrl)
                        <div class="mb-3 flex items-center gap-4">
                            <img class="h-16 w-16 rounded-lg object-cover ring-1 ring-gray-200"
                                 src="{{ $settings->logoUrl }}"
                                 alt="{{ __('messages.settings.current_logo') }}">
                            <label class="flex cursor-pointer items-center gap-2 text-base text-red-600 hover:text-red-700 sm:text-sm">
                                <input name="remove_logo"
                                       type="checkbox"
                                       value="1"
                                       class="rounded border-gray-300 text-red-600 focus:ring-red-600"
                                       data-testid="remove-logo-checkbox">
                                {{ __('messages.settings.remove_logo') }}
                            </label>
                        </div>
                    @endif

                    <input class="block w-full text-base text-gray-500 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2.5 file:text-base file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 sm:text-sm sm:file:text-sm"
                           id="logo"
                           name="logo"
                           type="file"
                           accept="image/*"
                           data-testid="logo-upload-input"
                           @error('logo') aria-describedby="logo-error" aria-invalid="true" @enderror>
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

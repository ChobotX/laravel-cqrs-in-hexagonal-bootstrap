@extends('layouts.app')

@section('title', __('messages.settings.password_rotation_title'))

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-2xl font-semibold text-gray-900"
            data-testid="password-rotation-title">{{ __('messages.settings.password_rotation_title') }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ __('messages.settings.password_rotation_intro') }}</p>

        <div class="mt-8">
            <x-form-card>
                <form class="space-y-5"
                      method="post"
                      action="{{ route('settings.password-rotation.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-2">
                        <input name="rotation_enabled"
                               type="hidden"
                               value="0">
                        <input class="h-4 w-4 cursor-pointer rounded border border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               id="rotation_enabled"
                               name="rotation_enabled"
                               type="checkbox"
                               value="1"
                               @checked($rotationEnabled)>
                        <label class="text-base font-medium text-gray-700 sm:text-sm"
                               for="rotation_enabled">{{ __('messages.settings.rotation_enabled') }}</label>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                               for="max_age_days">{{ __('messages.settings.max_age_days') }}</label>
                        <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                               id="max_age_days"
                               name="max_age_days"
                               type="number"
                               value="{{ old('max_age_days', $maxAgeDays) }}"
                               min="{{ $minPasswordAgeDays }}"
                               max="{{ $maxPasswordAgeDays }}"
                               @error('max_age_days') aria-describedby="max-age-days-error" aria-invalid="true" @enderror>
                        @error('max_age_days')
                            <p class="mt-1 text-base text-red-600 sm:text-sm"
                               id="max-age-days-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                               for="history_count">{{ __('messages.settings.history_count') }}</label>
                        <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                               id="history_count"
                               name="history_count"
                               type="number"
                               value="{{ old('history_count', $historyCount) }}"
                               min="{{ $minHistoryCount }}"
                               max="{{ $maxHistoryCount }}"
                               required
                               @error('history_count') aria-describedby="history-count-error" aria-invalid="true" @enderror>
                        @error('history_count')
                            <p class="mt-1 text-base text-red-600 sm:text-sm"
                               id="history-count-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <x-primary-button skip-permission
                                          testId="password-rotation-save-button"
                                          :label="__('messages.settings.update_action')" />
                    </div>
                </form>
            </x-form-card>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', __('messages.settings.title'))

@section('content')
    @if ($activeTab === 'password-rotation')
        <h1 class="mb-2 text-2xl font-semibold text-gray-900"
            data-testid="password-rotation-title">{{ __('messages.settings.password_rotation_title') }}</h1>
    @elseif ($activeTab === 'two-factor')
        <h1 class="mb-2 text-2xl font-semibold text-gray-900"
            data-testid="two-factor-title">{{ __('messages.settings.two_factor_title') }}</h1>
    @else
        <h1 class="mb-2 text-2xl font-semibold text-gray-900">{{ __('messages.settings.title') }}</h1>
    @endif

    <div class="mb-6">
        <p class="text-base text-gray-500 sm:text-sm">
            {{ $activeTab === 'password-rotation' ? __('messages.settings.password_rotation_intro') : ($activeTab === 'two-factor' ? __('messages.settings.two_factor_intro') : __('messages.settings.subtitle')) }}
        </p>
    </div>

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a class="{{ $activeTab === 'general' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} inline-flex rounded-lg px-3 py-2 text-sm font-medium transition-colors"
           href="{{ route('settings.index') }}"
           title="{{ __('messages.settings.title') }}">{{ __('messages.settings.title') }}</a>
        <a class="{{ $activeTab === 'password-rotation' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} inline-flex rounded-lg px-3 py-2 text-sm font-medium transition-colors"
           href="{{ route('settings.index', ['tab' => 'password-rotation']) }}"
           title="{{ __('messages.settings.password_rotation_title') }}">{{ __('messages.settings.password_rotation_title') }}</a>
        <a class="{{ $activeTab === 'two-factor' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }} inline-flex rounded-lg px-3 py-2 text-sm font-medium transition-colors"
           href="{{ route('settings.index', ['tab' => 'two-factor']) }}"
           title="{{ __('messages.settings.two_factor_title') }}">{{ __('messages.settings.two_factor_title') }}</a>
    </div>

    <div class="mb-6 max-w-lg">
        @if ($activeTab === 'password-rotation')
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                <form class="space-y-5 p-6"
                      method="POST"
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
                               @checked(old('rotation_enabled', $rotationEnabled))>
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
            </div>
        @elseif ($activeTab === 'two-factor')
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                <form class="space-y-5 p-6"
                      method="POST"
                      action="{{ route('settings.two-factor.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-2">
                        <input name="required_for_all_users"
                               type="hidden"
                               value="0">
                        <input class="h-4 w-4 cursor-pointer rounded border border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               id="required_for_all_users"
                               name="required_for_all_users"
                               type="checkbox"
                               value="1"
                               @checked(old('required_for_all_users', $twoFactorRequiredForAllUsers))>
                        <label class="text-base font-medium text-gray-700 sm:text-sm"
                               for="required_for_all_users">{{ __('messages.settings.two_factor_required_for_all') }}</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input name="email_otp_enabled"
                               type="hidden"
                               value="0">
                        <input class="h-4 w-4 cursor-pointer rounded border border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               id="email_otp_enabled"
                               name="email_otp_enabled"
                               type="checkbox"
                               value="1"
                               @checked(old('email_otp_enabled', $twoFactorEmailOtpEnabled))>
                        <label class="text-base font-medium text-gray-700 sm:text-sm"
                               for="email_otp_enabled">{{ __('messages.settings.two_factor_email_method') }}</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input name="totp_enabled"
                               type="hidden"
                               value="0">
                        <input class="h-4 w-4 cursor-pointer rounded border border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               id="totp_enabled"
                               name="totp_enabled"
                               type="checkbox"
                               value="1"
                               @checked(old('totp_enabled', $twoFactorTotpEnabled))>
                        <label class="text-base font-medium text-gray-700 sm:text-sm"
                               for="totp_enabled">{{ __('messages.settings.two_factor_totp_method') }}</label>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <x-primary-button skip-permission
                                          testId="two-factor-save-button"
                                          :label="__('messages.settings.update_action')" />
                    </div>
                </form>
            </div>
        @else
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
        @endif
    </div>
@endsection

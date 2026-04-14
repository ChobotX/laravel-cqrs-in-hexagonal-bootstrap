@extends('layouts.app')

@section('title', __('messages.settings.two_factor_title'))

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-2xl font-semibold text-gray-900"
            data-testid="own-two-factor-title">{{ __('messages.settings.two_factor_title') }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ __('messages.settings.own_two_factor_intro') }}</p>

        <x-stack class="mt-8"
                 gap="loose">
            <x-form-card :title="__('messages.settings.email_otp_section_title')"
                         :subtitle="__('messages.settings.email_otp_section_subtitle')">
                <div class="space-y-3 pt-1">
                    @if (!$status->emailAllowed)
                        <p class="text-sm text-gray-500">{{ __('messages.settings.email_otp_disabled_by_tenant') }}</p>
                    @endif
                    <form class="flex items-center justify-between gap-4"
                          method="POST"
                          action="{{ route('profile.two-factor.update') }}">
                        @csrf
                        @method('PUT')
                        <input name="action"
                               type="hidden"
                               value="email-save">
                        <span class="text-base font-medium text-gray-700 sm:text-sm"
                              id="email_otp_switch_label">{{ __('messages.settings.email_otp_switch_label') }}</span>
                        <x-toggle-switch name="email_two_factor_enabled"
                                         test-id="own-two-factor-email-switch"
                                         toggle-id="email_two_factor_toggle"
                                         :checked="$status->emailOtpActive"
                                         :disabled="!$status->emailAllowed"
                                         :autoSubmit="true"
                                         :ariaLabelledby="'email_otp_switch_label'" />
                    </form>
                </div>
            </x-form-card>

            <x-form-card :title="__('messages.settings.totp_section_title')"
                         :subtitle="__('messages.settings.totp_section_subtitle')">
                <x-stack gap="relaxed">
                    @if (!$status->totpAllowed)
                        <p class="text-sm text-gray-500">{{ __('messages.settings.totp_disabled_by_tenant') }}</p>
                    @endif
                    <div class="space-y-3 pt-1">
                        <form class="flex items-center justify-between gap-4"
                              method="POST"
                              action="{{ route('profile.two-factor.update') }}">
                            @csrf
                            @method('PUT')
                            <input name="action"
                                   type="hidden"
                                   value="totp-save">
                            <span class="text-base font-medium text-gray-700 sm:text-sm"
                                  id="totp_app_switch_label">{{ __('messages.settings.totp_switch_label') }}</span>
                            <x-toggle-switch name="totp_two_factor_enabled"
                                             test-id="own-two-factor-totp-switch"
                                             toggle-id="totp_two_factor_toggle"
                                             :checked="filled($totpSetup->secret)"
                                             :disabled="!$status->totpAllowed"
                                             :autoSubmit="true"
                                             :ariaLabelledby="'totp_app_switch_label'" />
                        </form>
                    </div>

                    @if (filled($totpSetup->secret))
                        @if (!$totpSetup->confirmed && $totpQrSvg)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 ring-1 ring-gray-950/5">
                                <x-split-row>
                                    <x-slot:leading>
                                        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-950/5 [&_svg]:block [&_svg]:h-auto [&_svg]:w-full"
                                             data-testid="own-two-factor-totp-qr"
                                             role="img"
                                             aria-label="{{ __('messages.settings.totp_qr_aria') }}">
                                            {!! $totpQrSvg !!}
                                        </div>
                                    </x-slot:leading>
                                    <x-slot:trailing>
                                        <p class="text-sm leading-relaxed text-gray-600">
                                            {{ __('messages.settings.totp_qr_hint') }}</p>
                                        @if (is_array($totpSetup->backupCodesPlaintext) && $totpSetup->backupCodesPlaintext !== [])
                                            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 sm:p-5">
                                                <h4 class="text-sm font-semibold text-amber-950">
                                                    {{ __('messages.settings.totp_backup_codes_title') }}</h4>
                                                <p class="mt-1.5 text-sm leading-relaxed text-amber-900/90">
                                                    {{ __('messages.settings.totp_backup_codes_instruction') }}</p>
                                                <ol
                                                    class="mt-4 list-decimal space-y-1.5 pl-5 font-mono text-sm text-gray-900">
                                                    @foreach ($totpSetup->backupCodesPlaintext as $backupCode)
                                                        <li>{{ $backupCode }}</li>
                                                    @endforeach
                                                </ol>
                                                <div class="mt-5">
                                                    <a class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-500"
                                                       data-testid="own-two-factor-totp-backup-download"
                                                       data-tooltip="{{ __('messages.settings.totp_backup_codes_download') }}"
                                                       href="{{ route('profile.two-factor.backup-codes.download') }}"
                                                       download>{{ __('messages.settings.totp_backup_codes_download') }}</a>
                                                </div>
                                                @if (!$totpSetup->backupCodesDownloadRecorded)
                                                    <p class="mt-4 text-sm font-medium text-red-800">
                                                        {{ __('messages.settings.totp_backup_codes_download_required_hint') }}
                                                    </p>
                                                @else
                                                    <p class="mt-4 text-sm font-medium text-green-900"
                                                       data-testid="own-two-factor-totp-download-ack">
                                                        {{ __('messages.settings.totp_backup_codes_downloaded_ack') }}</p>
                                                @endif
                                            </div>
                                        @endif
                                        <details class="rounded-lg border border-gray-200 bg-white p-4 sm:p-5"
                                                 data-testid="own-two-factor-totp-secret-details">
                                            <summary class="cursor-pointer text-sm font-medium text-gray-700"
                                                     data-testid="own-two-factor-totp-secret-summary">
                                                {{ __('messages.settings.totp_secret_label') }}</summary>
                                            <p class="mt-3 break-all font-mono text-sm leading-relaxed text-gray-800"
                                               data-testid="own-two-factor-totp-secret">
                                                {{ $totpSetup->secret }}</p>
                                        </details>
                                    </x-slot:trailing>
                                </x-split-row>
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <div class="flex flex-col gap-5">
                                    <form class="space-y-6"
                                          method="POST"
                                          action="{{ route('profile.two-factor.update') }}">
                                        @csrf
                                        @method('PUT')
                                        <input name="action"
                                               type="hidden"
                                               value="totp-confirm">
                                        <div>
                                            <label class="mb-2 block text-base font-medium text-gray-700 sm:text-sm"
                                                   for="totp_code">{{ __('messages.settings.totp_code_label') }}</label>
                                            <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                                                   id="totp_code"
                                                   name="totp_code"
                                                   data-testid="own-two-factor-totp-code-input"
                                                   type="text"
                                                   placeholder="123456"
                                                   inputmode="numeric"
                                                   autocomplete="one-time-code"
                                                   required>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3 pt-2">
                                            <x-primary-button skip-permission
                                                              test-id="own-two-factor-totp-confirm-submit"
                                                              :label="__('messages.settings.confirm_totp_setup')" />
                                        </div>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('profile.two-factor.update') }}">
                                        @csrf
                                        @method('PUT')
                                        <input name="action"
                                               type="hidden"
                                               value="totp-disable">
                                        <x-primary-button skip-permission
                                                          variant="secondary"
                                                          :label="__('messages.settings.cancel_totp_setup')" />
                                    </form>
                                </div>
                            </div>
                        @elseif (!$totpSetup->confirmed)
                            <form class="border-t border-gray-200 pt-8"
                                  method="POST"
                                  action="{{ route('profile.two-factor.update') }}">
                                @csrf
                                @method('PUT')
                                <input name="action"
                                       type="hidden"
                                       value="totp-disable">
                                <x-primary-button skip-permission
                                                  variant="secondary"
                                                  :label="__('messages.settings.cancel_totp_setup')" />
                            </form>
                        @else
                            <p class="border-t border-gray-200 pt-6 text-sm text-gray-600">
                                {{ __('messages.settings.totp_enabled_active_hint') }}</p>
                        @endif
                    @endif
                </x-stack>
            </x-form-card>
        </x-stack>
    </div>
@endsection

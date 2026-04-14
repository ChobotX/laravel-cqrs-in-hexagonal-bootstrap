@extends('layouts.guest')

@section('title', __('messages.auth.two_factor_title'))

@section('content')
    <x-form-card>
        <h1 class="text-xl font-semibold text-gray-900">{{ __('messages.auth.two_factor_title') }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ __('messages.auth.two_factor_subtitle') }}</p>

        <div class="mt-6 space-y-4">
            @if ($status->emailAllowed)
                <form method="POST"
                      action="{{ route('two-factor.email-code') }}">
                    @csrf
                    <x-primary-button skip-permission
                                      test-id="two-factor-send-email-submit"
                                      :label="__('messages.auth.send_email_code')" />
                </form>
            @endif

            <form class="space-y-3"
                  method="POST"
                  action="{{ route('two-factor.verify') }}">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700"
                           for="method">{{ __('messages.auth.two_factor_method') }}</label>
                    <select class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm"
                            id="method"
                            name="method"
                            data-testid="two-factor-method-select">
                        @if ($status->emailAllowed)
                            <option value="email">Email OTP</option>
                        @endif
                        @if ($status->totpAllowed)
                            <option value="totp">Authenticator App (TOTP)</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700"
                           for="code">{{ __('messages.auth.two_factor_code') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm"
                           id="code"
                           name="code"
                           data-testid="two-factor-code-input"
                           type="text"
                           required />
                </div>
                <x-primary-button skip-permission
                                  test-id="two-factor-verify-submit"
                                  :label="__('messages.auth.verify_two_factor')" />
            </form>
        </div>
    </x-form-card>
@endsection

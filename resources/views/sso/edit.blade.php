@extends('layouts.app')

@section('title', __('messages.sso.edit'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6 p-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ __('messages.sso.edit') }}</h1>

        @include('components.flash')

        <form class="space-y-4"
              method="POST"
              action="{{ route('settings.sso.update', ['id' => $configuration->id->value]) }}">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                       for="display_name">{{ __('messages.sso.display_name') }}</label>
                <input class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-base shadow-sm sm:text-sm"
                       id="display_name"
                       name="display_name"
                       data-testid="sso-display-name"
                       type="text"
                       value="{{ old('display_name', $configuration->displayName) }}"
                       required>
            </div>

            <div>
                <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                       for="jit_mode">{{ __('messages.sso.jit_mode') }}</label>
                <select class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-base shadow-sm sm:text-sm"
                        id="jit_mode"
                        name="jit_mode"
                        data-testid="sso-jit-mode">
                    @foreach ($jitModes as $jitMode)
                        <option value="{{ $jitMode->value }}"
                                @selected($jitMode === $configuration->jitMode)>{{ $jitMode->value }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                       for="allowed_email_domains">{{ __('messages.sso.allowed_email_domains') }}</label>
                <input class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-base shadow-sm sm:text-sm"
                       id="allowed_email_domains"
                       name="allowed_email_domains"
                       data-testid="sso-allowed-email-domains"
                       type="text"
                       value="{{ old('allowed_email_domains', implode(',', $configuration->allowedEmailDomains->domains)) }}">
            </div>

            <div class="flex items-center gap-4">
                <label class="inline-flex cursor-pointer items-center text-base text-gray-700 sm:text-sm">
                    <input class="cursor-pointer rounded border-gray-300"
                           name="enabled"
                           data-testid="sso-enabled"
                           type="checkbox"
                           value="1"
                           @checked($configuration->enabled)>
                    <span class="ml-2">{{ __('messages.sso.enabled') }}</span>
                </label>

                <label class="inline-flex cursor-pointer items-center text-base text-gray-700 sm:text-sm">
                    <input class="cursor-pointer rounded border-gray-300"
                           name="enforce"
                           data-testid="sso-enforce"
                           type="checkbox"
                           value="1"
                           @checked($configuration->enforce)>
                    <span class="ml-2">{{ __('messages.sso.enforce') }}</span>
                </label>
            </div>

            <div class="flex justify-end gap-2">
                <a class="cursor-pointer rounded-lg border border-gray-300 px-4 py-2 text-base text-gray-700 hover:bg-gray-50 sm:text-sm"
                   href="{{ route('settings.sso.index') }}"
                   title="{{ __('messages.sso.cancel') }}">{{ __('messages.sso.cancel') }}</a>
                <button class="cursor-pointer rounded-lg bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 sm:text-sm"
                        data-testid="sso-submit"
                        type="submit"
                        title="{{ __('messages.sso.update_action') }}">{{ __('messages.sso.update_action') }}</button>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', __('messages.sso.title'))

@section('content')
    <div class="mx-auto max-w-4xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('messages.sso.title') }}</h1>
            <a class="rounded-lg bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 sm:text-sm"
               data-testid="sso-create-link"
               href="{{ route('settings.sso.create') }}"
               title="{{ __('messages.sso.create') }}">{{ __('messages.sso.create') }}</a>
        </div>

        @include('components.flash')

        @if (count($configurations) === 0)
            <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.sso.empty') }}</p>
        @else
            <ul class="space-y-3">
                @foreach ($configurations as $configuration)
                    <li class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
                        data-testid="sso-configuration-{{ $configuration->id->value }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-base font-medium text-gray-900">{{ $configuration->displayName }}</p>
                                <p class="text-base text-gray-500 sm:text-sm">{{ $configuration->providerType->value }} / {{ $configuration->slug }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a class="cursor-pointer rounded-lg border border-gray-300 px-3 py-1.5 text-base text-gray-700 hover:bg-gray-50 sm:text-sm"
                                   data-testid="sso-edit-{{ $configuration->id->value }}"
                                   href="{{ route('settings.sso.edit', ['id' => $configuration->id->value]) }}"
                                   title="{{ __('messages.sso.edit_action') }}">{{ __('messages.sso.edit_action') }}</a>
                                <form class="inline"
                                      method="POST"
                                      action="{{ route('settings.sso.test', ['id' => $configuration->id->value]) }}">
                                    @csrf
                                    <button class="cursor-pointer rounded-lg border border-gray-300 px-3 py-1.5 text-base text-gray-700 hover:bg-gray-50 sm:text-sm"
                                            data-testid="sso-test-{{ $configuration->id->value }}"
                                            type="submit"
                                            title="{{ __('messages.sso.test_action') }}">{{ __('messages.sso.test_action') }}</button>
                                </form>
                                <form class="inline"
                                      method="POST"
                                      action="{{ route('settings.sso.destroy', ['id' => $configuration->id->value]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="cursor-pointer rounded-lg border border-red-300 px-3 py-1.5 text-base text-red-700 hover:bg-red-50 sm:text-sm"
                                            data-testid="sso-delete-{{ $configuration->id->value }}"
                                            type="submit"
                                            title="{{ __('messages.sso.delete_action') }}">{{ __('messages.sso.delete_action') }}</button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection

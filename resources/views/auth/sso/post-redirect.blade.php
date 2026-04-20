@extends('layouts.guest')

@section('title', __('messages.sso.redirecting'))

@section('content')
    <p class="mb-4 text-center text-base text-gray-500 sm:text-sm">{{ __('messages.sso.redirecting') }}</p>
    <form class="space-y-3"
          method="POST"
          action="{{ $actionUrl }}"
          data-testid="sso-post-redirect">
        @foreach ($fields as $name => $value)
            <input name="{{ $name }}"
                   type="hidden"
                   value="{{ $value }}">
        @endforeach
        <button class="block w-full cursor-pointer rounded-lg bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 sm:text-sm"
                data-testid="sso-post-redirect-submit"
                type="submit"
                title="{{ __('messages.sso.continue_to_idp') }}">{{ __('messages.sso.continue_to_idp') }}</button>
    </form>
@endsection

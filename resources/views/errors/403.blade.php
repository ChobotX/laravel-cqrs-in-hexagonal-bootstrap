@extends('layouts.guest')

@section('title', '403')

@section('content')
    <div class="text-center">
        <h1 class="text-6xl font-bold text-indigo-600">403</h1>
        <p class="mt-4 text-lg text-gray-600">{{ $exception->getMessage() ?: __('messages.exceptions.permission_denied') }}
        </p>
        <div class="mt-6 flex items-center justify-center gap-3">
            @if ($authenticatedUser?->isImpersonating())
                <x-primary-button skip-permission
                                  variant="amber"
                                  :action="route('impersonation.stop')"
                                  :label="__('messages.impersonation.stop')" />
            @endif
            <x-primary-button skip-permission
                              :href="url()->previous() !== url()->current() ? url()->previous() : '/'"
                              :label="__('messages.errors.go_back')" />
        </div>
    </div>
@endsection

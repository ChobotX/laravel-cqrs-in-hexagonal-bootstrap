@extends('layouts.app')

@section('title', __('messages.nav.dashboard'))

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.dashboard.welcome', ['name' => auth()->user()->name]) }}
        </h2>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @if ($userCount !== null)
            <a class="block rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition-colors hover:bg-gray-50"
               data-testid="widget-users"
               href="{{ route('users.index') }}"
               title="{{ __('messages.dashboard.users') }}">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100">
                        <x-dynamic-component class="h-6 w-6 text-indigo-600"
                                             aria-hidden="true"
                                             component="heroicon-o-users" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('messages.dashboard.users') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $userCount }}</p>
                    </div>
                </div>
            </a>
        @endif

        @if ($roleCount !== null)
            <a class="block rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition-colors hover:bg-gray-50"
               data-testid="widget-roles"
               href="{{ route('roles.index') }}"
               title="{{ __('messages.dashboard.roles') }}">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100">
                        <x-dynamic-component class="h-6 w-6 text-indigo-600"
                                             aria-hidden="true"
                                             component="heroicon-o-shield-check" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('messages.dashboard.roles') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $roleCount }}</p>
                    </div>
                </div>
            </a>
        @endif

        @if ($teamCount !== null)
            <a class="block rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition-colors hover:bg-gray-50"
               data-testid="widget-teams"
               href="{{ route('teams.index') }}"
               title="{{ __('messages.dashboard.teams') }}">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100">
                        <x-dynamic-component class="h-6 w-6 text-indigo-600"
                                             aria-hidden="true"
                                             component="heroicon-o-user-group" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('messages.dashboard.teams') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $teamCount }}</p>
                    </div>
                </div>
            </a>
        @endif
    </div>
@endsection

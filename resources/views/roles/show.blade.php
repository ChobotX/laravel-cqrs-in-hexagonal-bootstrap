@extends('layouts.app')

@section('title', $role->name)

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-semibold text-gray-900 sm:text-sm">{{ $role->name }}</h2>
                    @if ($role->isSystem)
                        <span
                              class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-700/10">
                            {{ __('messages.roles.system_badge') }}
                        </span>
                    @endif
                </div>
                @if ($role->description)
                    <p class="mt-1 text-base text-gray-500 sm:text-sm">{{ $role->description }}</p>
                @endif
            </div>

            <div class="space-y-5 p-6">
                <div>
                    <h3 class="mb-3 text-base font-medium text-gray-700 sm:text-sm">{{ __('messages.roles.permissions') }}
                    </h3>
                    @include('components.permission-matrix', [
                        'modules' => $modules,
                        'permissions' => $role->permissions,
                        'mode' => 'readonly',
                    ])
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('roles.index')"
                                      :label="__('messages.roles.back')" />
                </div>
            </div>
        </div>
    </div>
@endsection

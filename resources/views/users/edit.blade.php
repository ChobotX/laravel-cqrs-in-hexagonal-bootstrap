@extends('layouts.app')

@section('title', __('messages.users.edit'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.users'), 'href' => route('users.index')],
        ['label' => __('messages.users.edit')],
    ]" />

    <form class="space-y-6"
          method="POST"
          action="{{ route('users.update', $user->id) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-form-card :title="__('messages.users.info_title')"
                         :subtitle="__('messages.users.edit_subtitle')">
                <div class="space-y-5">
                    @include('partials.form.avatar-field', ['user' => $user])
                    @include('partials.form.profile-fields', [
                        'user' => $user,
                        'canEditEmail' => $canEditEmail,
                    ])
                </div>
            </x-form-card>

            @if ($canManageRoles || $canManageTeams || $canManageLabels)
                <x-form-card :title="__('messages.users.access_title')"
                             :subtitle="__('messages.users.access_subtitle')">
                    <div class="space-y-5">
                        @if ($canManageRoles)
                            <div>
                                <label
                                       class="mb-1 block text-base font-medium text-gray-700 sm:text-sm">{{ __('messages.users.roles') }}</label>
                                <p class="mb-2 text-xs text-gray-400">{{ __('messages.users.roles_subtitle') }}</p>
                                <div data-chip-selector
                                     data-options="{{ json_encode(array_map(fn($r) => ['id' => $r->id->value, 'name' => $r->name->value, 'badge' => $r->isSystem ? __('messages.roles.system_badge') : null], $assignableRoles)) }}"
                                     data-selected-ids="{{ json_encode($userRoleIds) }}"
                                     data-input-name="roles[]"
                                     data-placeholder="{{ __('messages.users.roles_search') }}">
                                </div>
                            </div>
                        @endif

                        @if ($canManageTeams)
                            <div @if ($canManageRoles) class="border-t border-gray-200 pt-5" @endif>
                                <label
                                       class="mb-1 block text-base font-medium text-gray-700 sm:text-sm">{{ __('messages.users.teams') }}</label>
                                <p class="mb-2 text-xs text-gray-400">{{ __('messages.users.teams_subtitle') }}</p>
                                <div data-chip-selector
                                     data-search-url="{{ route('internal-api.teams.search') }}"
                                     data-selected-items="{{ json_encode(array_map(fn($t) => ['id' => $t->id->value, 'name' => $t->name->value], $userTeams)) }}"
                                     data-input-name="teams[]"
                                     data-placeholder="{{ __('messages.users.teams_search') }}"
                                     data-no-results-text="{{ __('messages.teams.no_results') }}">
                                </div>
                            </div>
                        @endif

                        @if ($canManageLabels)
                            <div @if ($canManageRoles || $canManageTeams) class="border-t border-gray-200 pt-5" @endif>
                                <label
                                       class="mb-1 block text-base font-medium text-gray-700 sm:text-sm">{{ __('messages.labels.title') }}</label>
                                <p class="mb-2 text-xs text-gray-400">{{ __('messages.labels.subtitle') }}</p>
                                <div data-chip-selector
                                     data-search-url="{{ route('internal-api.labels.search') }}?namespace=users"
                                     data-selected-items="{{ json_encode($userLabels) }}"
                                     data-input-name="labels[]"
                                     data-placeholder="{{ __('messages.labels.search') }}"
                                     data-no-results-text="{{ __('messages.labels.no_results') }}"
                                     @if ($canCreateLabels) data-allow-create="true"
                                         data-create-url="{{ route('internal-api.labels.create') }}"
                                         data-create-namespace="users"
                                         data-create-text="{{ __('messages.labels.create') }}" @endif>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-form-card>
            @endif
        </div>

        <div class="flex items-center gap-3 pb-6">
            <x-primary-button skip-permission
                              :label="__('messages.users.update_action')" />
            <x-primary-button skip-permission
                              variant="secondary"
                              :href="route('users.index')"
                              :label="__('messages.users.cancel')" />
        </div>
    </form>

    @if (!$user->isActivated)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <div class="flex items-center justify-between">
                <p class="text-sm text-amber-800">{{ __('messages.users.pending_activation') }}</p>
                <form method="POST"
                      action="{{ route('users.resend-invite', $user->id->value) }}">
                    @csrf
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :label="__('messages.users.resend_invite')" />
                </form>
            </div>
        </div>
    @endif

    @if ($canViewPermissions)
        @include('partials.permissions-section', [
            'effectivePermissions' => $effectivePermissions,
            'modules' => $modules,
            'userOverrides' => $userOverrides,
            'canManageOverrides' => $canManageOverrides,
            'userId' => $user->id->value,
        ])
    @endif
@endsection

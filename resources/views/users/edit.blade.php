@extends('layouts.app')

@section('title', __('messages.users.edit'))

@section('content')
    <div class="mb-6 max-w-lg">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.users.edit_subtitle') }}</p>
            </div>

            <form class="space-y-5 p-6"
                  method="POST"
                  action="{{ route('users.update', $user->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div data-avatar-field>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="avatar">{{ __('messages.users.avatar') }}</label>
                    <input name="remove_avatar"
                           data-avatar-remove
                           type="hidden"
                           value="0">
                    <div class="flex items-center gap-4">
                        @if ($user->avatarFileId !== null)
                            <div class="relative shrink-0"
                                 data-avatar-preview>
                                <img class="h-16 w-16 rounded-full object-cover ring-1 ring-gray-200"
                                     src="{{ route('files.show', $user->avatarFileId->value) }}"
                                     alt="{{ $user->name->value }}">
                                <button class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white shadow transition-colors hover:bg-red-600"
                                        data-avatar-remove-btn
                                        type="button"
                                        title="{{ __('messages.users.avatar_remove') }}"
                                        aria-label="{{ __('messages.users.avatar_remove') }}">
                                    <x-heroicon-s-x-mark class="h-3 w-3" />
                                </button>
                            </div>
                        @else
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700"
                                 data-avatar-preview>
                                {{ strtoupper(substr($user->name->value, 0, 1)) }}{{ strtoupper(substr($user->name->value, strpos($user->name->value, ' ') + 1, 1)) }}
                            </div>
                        @endif
                        <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2 text-base text-gray-700 shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                               id="avatar"
                               name="avatar"
                               type="file"
                               accept="image/*"
                               @error('avatar') aria-describedby="avatar-error" aria-invalid="true" @enderror>
                    </div>
                    @error('avatar')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="avatar-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="name">{{ __('messages.users.name') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="name"
                           name="name"
                           type="text"
                           value="{{ old('name', $user->name->value) }}"
                           required
                           @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
                    @error('name')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="email">{{ __('messages.users.email') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="email"
                           name="email"
                           type="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           @error('email') aria-describedby="email-error" aria-invalid="true" @enderror>
                    @error('email')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="password">{{ __('messages.users.password') }} <span
                              class="text-gray-400">{{ __('messages.users.password_keep') }}</span></label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="password"
                           name="password"
                           type="password"
                           autocomplete="new-password"
                           @error('password') aria-describedby="password-error" aria-invalid="true" @enderror>
                    @error('password')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="password-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="password_confirmation">{{ __('messages.users.confirm_password') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="password_confirmation"
                           name="password_confirmation"
                           type="password"
                           autocomplete="new-password"
                           @error('password_confirmation') aria-describedby="password_confirmation-error" aria-invalid="true" @enderror>
                    @error('password_confirmation')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="password_confirmation-error">{{ $message }}</p>
                    @enderror
                </div>

                @if ($canManageRoles)
                    <div class="border-t border-gray-200 pt-5">
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
                    <div class="border-t border-gray-200 pt-5">
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
                    <div class="border-t border-gray-200 pt-5">
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

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.users.update_action')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('users.index')"
                                      :label="__('messages.users.cancel')" />
                </div>
            </form>
        </div>
    </div>

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

@extends('layouts.app')

@section('title', __('messages.profile.title'))

@section('content')
    <div class="mb-6">
        <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.profile.subtitle') }}</p>
    </div>

    <div class="mb-6 max-w-lg">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <form class="space-y-5 p-6"
                  method="POST"
                  action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="name">{{ __('messages.users.name') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="name"
                           name="name"
                           type="text"
                           value="{{ old('name', $user->name) }}"
                           required
                           @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
                    @error('name')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="name-error">{{ $message }}</p>
                    @enderror
                </div>

                @if ($canEditEmail)
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
                @endif

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="password">{{ __('messages.users.password') }} <span
                              class="text-gray-400">{{ __('messages.users.password_keep') }}</span></label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="password"
                           name="password"
                           type="password"
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

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.users.update_action')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('dashboard')"
                                      :label="__('messages.users.cancel')" />
                </div>
            </form>
        </div>
    </div>

    @include('partials.permissions-section', [
        'effectivePermissions' => $effectivePermissions,
        'modules' => $modules,
        'userOverrides' => $userOverrides,
        'canManageOverrides' => $canManageOverrides,
        'userId' => $user->id->value,
    ])
@endsection

<nav class="flex flex-1 flex-col px-3 py-4"
     aria-label="{{ __('messages.a11y.main_navigation') }}">
    <div class="flex-1">
        <x-nav-link skip-permission
                    :href="route('dashboard')"
                    icon="heroicon-o-home"
                    :label="__('messages.nav.dashboard')"
                    :active="request()->routeIs('dashboard')" />

    <p class="mb-2 mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
        {{ __('messages.nav.management') }}
    </p>
    <x-nav-link permission="users.list.read"
                :href="route('users.index')"
                icon="heroicon-o-users"
                :label="__('messages.nav.users')"
                :active="request()->routeIs('users.*')" />
    <x-nav-link permission="users.roles.read"
                :href="route('roles.index')"
                icon="heroicon-o-shield-check"
                :label="__('messages.nav.roles')"
                :active="request()->routeIs('roles.*')" />
    <x-nav-link permission="teams.management.read"
                :href="route('teams.index')"
                icon="heroicon-o-user-group"
                :label="__('messages.nav.teams')"
                :active="request()->routeIs('teams.*')" />
    @feature('registry.schema-builder')
        <x-nav-link permission="registry.definitions.read"
                    :href="route('registry.definitions.index')"
                    icon="heroicon-o-rectangle-stack"
                    :label="__('messages.nav.registry')"
                    :active="request()->routeIs('registry.*')" />
    @endfeature

    <p class="mb-2 mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
        {{ __('messages.nav.system') }}
    </p>
    <x-nav-link permission="feature_flags.management.read"
                :href="route('feature-flags.index')"
                icon="heroicon-o-flag"
                :label="__('messages.nav.feature_flags')"
                :active="request()->routeIs('feature-flags.*')" />
    <x-nav-link permission="settings.tenant.read"
                :href="route('settings.index')"
                icon="heroicon-o-cog-6-tooth"
                :label="__('messages.nav.settings')"
                :active="request()->routeIs('settings.*')" />
</nav>

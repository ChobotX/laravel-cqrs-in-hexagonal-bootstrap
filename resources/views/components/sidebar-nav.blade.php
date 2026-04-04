<nav class="flex-1 px-3 py-4"
     aria-label="{{ __('messages.a11y.main_navigation') }}">
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
    <x-nav-link permission="registry.definitions.read"
                :href="route('registry.definitions.index')"
                icon="heroicon-o-rectangle-stack"
                :label="__('messages.nav.registry')"
                :active="request()->routeIs('registry.*')" />
</nav>

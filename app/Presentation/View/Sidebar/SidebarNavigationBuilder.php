<?php

declare(strict_types=1);

namespace App\Presentation\View\Sidebar;

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagChecker;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\Http\Request;

final readonly class SidebarNavigationBuilder
{
    private const int COLLAPSIBLE_MIN_ITEMS = 2;

    private const string REGISTRY_SCHEMA_FLAG = 'registry.schema-builder';

    public function __construct(
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private FeatureFlagChecker $featureFlagChecker,
        private Request $request,
        private Translator $translator,
    ) {}

    public function build(): SidebarNavViewData
    {
        $userId = $this->authenticatedUser->id();

        $sidebarNavItemViewData = new SidebarNavItemViewData(
            href: route('dashboard'),
            icon: 'heroicon-o-home',
            label: $this->translator->translate('messages.nav.dashboard'),
            active: $this->request->routeIs('dashboard'),
        );

        $sections = [];

        $managementBlocks = $this->managementBlocks($userId);
        if ($managementBlocks !== []) {
            $sections[] = new SidebarNavSectionViewData(
                label: $this->translator->translate('messages.nav.management'),
                blocks: $managementBlocks,
            );
        }

        $systemBlocks = $this->systemBlocks($userId);
        if ($systemBlocks !== []) {
            $sections[] = new SidebarNavSectionViewData(
                label: $this->translator->translate('messages.nav.system'),
                blocks: $systemBlocks,
            );
        }

        return new SidebarNavViewData(
            dashboard: $sidebarNavItemViewData,
            sections: $sections,
        );
    }

    /**
     * @return list<SidebarNavBlockViewData>
     */
    private function managementBlocks(?string $userId): array
    {
        $blocks = [];

        $accessItems = $this->filterItems([
            $this->maybeItem(
                userId: $userId,
                permission: 'users.list.read',
                routeName: 'users.index',
                routeIsPatterns: ['users.*'],
                icon: 'heroicon-o-users',
                labelKey: 'messages.nav.users',
            ),
            $this->maybeItem(
                userId: $userId,
                permission: 'users.roles.read',
                routeName: 'roles.index',
                routeIsPatterns: ['roles.*'],
                icon: 'heroicon-o-shield-check',
                labelKey: 'messages.nav.roles',
            ),
            $this->maybeItem(
                userId: $userId,
                permission: 'teams.management.read',
                routeName: 'teams.index',
                routeIsPatterns: ['teams.*'],
                icon: 'heroicon-o-user-group',
                labelKey: 'messages.nav.teams',
            ),
        ]);

        if ($accessItems !== []) {
            $blocks[] = $this->blockFromItems(
                id: 'mgmt-access',
                groupLabelKey: 'messages.nav.group_users_access',
                items: $accessItems,
                groupIcon: 'heroicon-o-users',
            );
        }

        $registryItem = $this->maybeRegistryItem($userId);
        if ($registryItem instanceof SidebarNavItemViewData) {
            $blocks[] = $this->blockFromItems(
                id: 'mgmt-registry',
                groupLabelKey: '',
                items: [$registryItem],
                groupIcon: null,
            );
        }

        return $blocks;
    }

    /**
     * @return list<SidebarNavBlockViewData>
     */
    private function systemBlocks(?string $userId): array
    {
        $blocks = [];

        $platformItems = $this->filterItems([
            $this->maybeItem(
                userId: $userId,
                permission: 'settings.tenant.read',
                routeName: 'settings.index',
                routeIsPatterns: ['settings.index', 'settings.update', 'settings.password-rotation', 'settings.password-rotation.update'],
                icon: 'heroicon-o-cog-6-tooth',
                labelKey: 'messages.nav.settings',
            ),
            $this->maybeItem(
                userId: $userId,
                permission: 'feature_flags.management.read',
                routeName: 'feature-flags.index',
                routeIsPatterns: ['feature-flags.*'],
                icon: 'heroicon-o-flag',
                labelKey: 'messages.nav.feature_flags',
            ),
        ]);

        if ($platformItems !== []) {
            $blocks[] = $this->blockFromItems(
                id: 'sys-platform',
                groupLabelKey: 'messages.nav.group_platform',
                items: $platformItems,
                groupIcon: 'heroicon-o-cog-6-tooth',
            );
        }

        $emailItems = $this->filterItems([
            $this->maybeItem(
                userId: $userId,
                permission: 'email_templates.templates.read',
                routeName: 'settings.email-templates.index',
                routeIsPatterns: ['settings.email-templates.*'],
                icon: 'heroicon-o-envelope',
                labelKey: 'messages.nav.email_templates',
            ),
            $this->maybeItem(
                userId: $userId,
                permission: 'email_templates.logs.read',
                routeName: 'settings.email-logs.index',
                routeIsPatterns: ['settings.email-logs.*'],
                icon: 'heroicon-o-paper-airplane',
                labelKey: 'messages.nav.email_logs',
            ),
        ]);

        if ($emailItems !== []) {
            $blocks[] = $this->blockFromItems(
                id: 'sys-email',
                groupLabelKey: 'messages.nav.group_email',
                items: $emailItems,
                groupIcon: 'heroicon-o-envelope',
            );
        }

        $auditItem = $this->maybeItem(
            userId: $userId,
            permission: 'audit_log.history.read',
            routeName: 'audit-log.index',
            routeIsPatterns: ['audit-log.*'],
            icon: 'heroicon-o-clipboard-document-list',
            labelKey: 'messages.nav.audit_log',
        );

        if ($auditItem instanceof SidebarNavItemViewData) {
            $blocks[] = $this->blockFromItems(
                id: 'sys-audit',
                groupLabelKey: '',
                items: [$auditItem],
                groupIcon: null,
            );
        }

        return $blocks;
    }

    private function maybeRegistryItem(?string $userId): ?SidebarNavItemViewData
    {
        if (! $this->featureFlagChecker->isEnabled(self::REGISTRY_SCHEMA_FLAG)) {
            return null;
        }

        return $this->maybeItem(
            userId: $userId,
            permission: 'registry.definitions.read',
            routeName: 'registry.definitions.index',
            routeIsPatterns: ['registry.*'],
            icon: 'heroicon-o-rectangle-stack',
            labelKey: 'messages.nav.registry',
        );
    }

    /**
     * @param  list<string>  $routeIsPatterns
     */
    private function maybeItem(
        ?string $userId,
        string $permission,
        string $routeName,
        array $routeIsPatterns,
        string $icon,
        string $labelKey,
    ): ?SidebarNavItemViewData {
        if ($userId === null) {
            return null;
        }

        if (! $this->authorizationChecker->can($userId, $permission)) {
            return null;
        }

        $active = array_any($routeIsPatterns, fn ($routeIPattern) => $this->request->routeIs($routeIPattern));

        return new SidebarNavItemViewData(
            href: route($routeName),
            icon: $icon,
            label: $this->translator->translate($labelKey),
            active: $active,
        );
    }

    /**
     * @param  list<SidebarNavItemViewData|null>  $items
     * @return list<SidebarNavItemViewData>
     */
    private function filterItems(array $items): array
    {
        $out = [];

        foreach ($items as $item) {
            if ($item !== null) {
                $out[] = $item;
            }
        }

        return $out;
    }

    /**
     * @param  list<SidebarNavItemViewData>  $items
     */
    private function blockFromItems(string $id, string $groupLabelKey, array $items, ?string $groupIcon): SidebarNavBlockViewData
    {
        $count = count($items);
        $collapsible = $count >= self::COLLAPSIBLE_MIN_ITEMS;
        $label = $collapsible && $groupLabelKey !== '' ? $this->translator->translate($groupLabelKey) : '';
        $anyActive = array_any($items, fn ($item) => $item->active);
        $resolvedGroupIcon = $collapsible ? $groupIcon : null;

        return new SidebarNavBlockViewData(
            id: $id,
            label: $label,
            collapsible: $collapsible,
            open: $collapsible && $anyActive,
            items: $items,
            groupIcon: $resolvedGroupIcon,
        );
    }
}

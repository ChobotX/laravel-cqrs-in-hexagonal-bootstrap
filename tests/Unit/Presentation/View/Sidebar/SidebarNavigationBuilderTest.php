<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagChecker;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\View\Sidebar\SidebarNavigationBuilder;
use App\Presentation\View\Sidebar\SidebarNavViewData;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\Helper\FakeAuthorizationChecker;
use Tests\Helper\FakeFeatureFlagChecker;
use Tests\TestCase;

uses(TestCase::class);

function sidebarRequestWithRouteName(string $routeName): Request
{
    $request = Request::create('/', 'GET');
    $route = new Route(['GET'], '/', []);
    $route->name($routeName);

    $request->setRouteResolver(static fn (): Route => $route);

    return $request;
}

function authenticatedUser(?string $id): AuthenticatedUser
{
    return new readonly class($id) implements AuthenticatedUser
    {
        public function __construct(private ?string $id) {}

        public function id(): ?string
        {
            return $this->id;
        }

        public function name(): ?string
        {
            return null;
        }

        public function impersonatorId(): ?string
        {
            return null;
        }

        public function isImpersonating(): bool
        {
            return false;
        }
    };
}

function makeBuilder(
    AuthenticatedUser $authenticatedUser,
    AuthorizationChecker $authorizationChecker,
    FeatureFlagChecker $featureFlagChecker,
    Request $request,
): SidebarNavigationBuilder {
    return new SidebarNavigationBuilder(
        authenticatedUser: $authenticatedUser,
        authorizationChecker: $authorizationChecker,
        featureFlagChecker: $featureFlagChecker,
        request: $request,
        translator: app(Translator::class),
    );
}

it('returns only dashboard when user is not authenticated', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser(null),
        new FakeAuthorizationChecker,
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('dashboard'),
    );

    $nav = $sidebarNavigationBuilder->build();

    expect($nav->sections)->toBeEmpty()
        ->and($nav->dashboard->active)->toBeTrue();
});

it('omits management and system sections when user has no matching permissions', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker([]),
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('dashboard'),
    );

    expect($sidebarNavigationBuilder->build()->sections)->toBeEmpty();
});

it('builds collapsible access group when at least two access links are visible', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker(['users.list.read', 'users.roles.read']),
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('roles.index'),
    );

    $nav = $sidebarNavigationBuilder->build();

    expect($nav->sections)->toHaveCount(1)
        ->and($nav->sections[0]->blocks)->toHaveCount(1)
        ->and($nav->sections[0]->blocks[0]->collapsible)->toBeTrue()
        ->and($nav->sections[0]->blocks[0]->open)->toBeTrue()
        ->and($nav->sections[0]->blocks[0]->items)->toHaveCount(2);
});

it('flattens access links when only one is visible', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker(['users.list.read']),
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('users.index'),
    );

    $block = $sidebarNavigationBuilder->build()->sections[0]->blocks[0];

    expect($block->collapsible)->toBeFalse()
        ->and($block->label)->toBe('')
        ->and($block->items)->toHaveCount(1)
        ->and($block->items[0]->active)->toBeTrue();
});

it('adds registry block when feature flag and permission allow', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker(['registry.definitions.read']),
        new FakeFeatureFlagChecker([
            'registry.schema-builder' => ['enabled' => true, 'value' => ''],
        ]),
        sidebarRequestWithRouteName('registry.definitions.index'),
    );

    $blocks = $sidebarNavigationBuilder->build()->sections[0]->blocks;

    expect($blocks)->toHaveCount(1)
        ->and($blocks[0]->id)->toBe('mgmt-registry')
        ->and($blocks[0]->collapsible)->toBeFalse()
        ->and($blocks[0]->items[0]->active)->toBeTrue();
});

it('omits registry when feature flag is disabled', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker(['registry.definitions.read']),
        new FakeFeatureFlagChecker([
            'registry.schema-builder' => ['enabled' => false, 'value' => ''],
        ]),
        sidebarRequestWithRouteName('dashboard'),
    );

    expect($sidebarNavigationBuilder->build()->sections)->toBeEmpty();
});

it('builds collapsible email group when two email links are visible', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker([
            'email_templates.templates.read',
            'email_templates.logs.read',
        ]),
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('settings.email-templates.index'),
    );

    $system = $sidebarNavigationBuilder->build()->sections[0];

    expect($system->blocks[0]->id)->toBe('sys-email')
        ->and($system->blocks[0]->collapsible)->toBeTrue()
        ->and($system->blocks[0]->open)->toBeTrue();
});

it('omits system section when no system permissions are granted', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker(['users.list.read']),
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('dashboard'),
    );

    $sections = $sidebarNavigationBuilder->build()->sections;

    expect($sections)->toHaveCount(1)
        ->and($sections[0]->label)->toBe(__('messages.nav.management'));
});

it('marks settings active for settings update route name', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker(['settings.tenant.read']),
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('settings.update'),
    );

    $items = $sidebarNavigationBuilder->build()->sections[0]->blocks[0]->items;

    expect($items[0]->active)->toBeTrue();
});

it('keeps collapsible group closed when no child route is active', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker([
            'email_templates.templates.read',
            'email_templates.logs.read',
        ]),
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('dashboard'),
    );

    $emailBlock = $sidebarNavigationBuilder->build()->sections[0]->blocks[0];

    expect($emailBlock->collapsible)->toBeTrue()
        ->and($emailBlock->open)->toBeFalse();
});

it('runs view composer so sidebar-nav receives SidebarNavViewData', function (): void {
    app()->instance(AuthenticatedUser::class, authenticatedUser('user-1'));
    app()->instance(AuthorizationChecker::class, new FakeAuthorizationChecker(['users.list.read']));
    app()->instance(FeatureFlagChecker::class, new FakeFeatureFlagChecker);
    app()->instance('request', sidebarRequestWithRouteName('dashboard'));

    $view = view('components.sidebar-nav', ['sidebarNavInstance' => 'test']);
    $view->render();

    expect($view->offsetGet('sidebarNav'))->toBeInstanceOf(SidebarNavViewData::class);
});

it('orders system blocks as platform then email then audit', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker([
            'feature_flags.management.read',
            'settings.tenant.read',
            'email_templates.templates.read',
            'email_templates.logs.read',
            'audit_log.history.read',
        ]),
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('dashboard'),
    );

    $ids = [];

    foreach ($sidebarNavigationBuilder->build()->sections[0]->blocks as $block) {
        $ids[] = $block->id;
    }

    expect($ids)->toBe(['sys-platform', 'sys-email', 'sys-audit']);
});

it('renders audit as a flat block when it is the only system link', function (): void {
    $sidebarNavigationBuilder = makeBuilder(
        authenticatedUser('u-1'),
        new FakeAuthorizationChecker(['audit_log.history.read']),
        new FakeFeatureFlagChecker,
        sidebarRequestWithRouteName('audit-log.index'),
    );

    $blocks = $sidebarNavigationBuilder->build()->sections[0]->blocks;

    expect($blocks)->toHaveCount(1)
        ->and($blocks[0]->id)->toBe('sys-audit')
        ->and($blocks[0]->collapsible)->toBeFalse()
        ->and($blocks[0]->items[0]->active)->toBeTrue();
});

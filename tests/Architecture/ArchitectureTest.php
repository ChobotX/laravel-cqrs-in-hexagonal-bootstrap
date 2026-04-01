<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

final class ArchitectureTest
{
    // ── Layer dependency rules ──────────────────────────────────

    public function testDomainHasNoExternalDependencies(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Domain'))
            ->shouldNotDependOn()
            ->classes(Selector::all())
            ->excluding(
                Selector::inNamespace('App\Domain'),
                Selector::inNamespace('App\Contract'),
                // Application-layer attributes and interfaces used by Domain commands/queries
                Selector::inNamespace('App\Application\Authorization'),
                Selector::inNamespace('App\Application\Pagination'),
                Selector::inNamespace('App\Application\Sorting'),
                Selector::inNamespace('App\Application\Bus'),
                Selector::inNamespace('App\Application\Event'),
                Selector::inNamespace('App\Application\Tenancy'),
            );
    }

    public function testApplicationDependsOnlyOnDomainAndContract(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Application'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App'))
            ->excluding(
                Selector::inNamespace('App\Application'),
                Selector::inNamespace('App\Domain'),
                Selector::inNamespace('App\Contract'),
            );
    }

    public function testContractHasNoAppDependencies(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Contract'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App'))
            ->excluding(Selector::inNamespace('App\Contract'));
    }

    public function testInfrastructureDoesNotDependOnPresentation(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Infrastructure'))
            // Service providers wire both layers — exclude them from this rule
            ->excluding(Selector::inNamespace('App\Infrastructure\Provider'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App\Presentation'));
    }

    public function testPresentationDoesNotDependOnInfrastructure(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Presentation'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App\Infrastructure'));
    }

    public function testNonTenancyDomainDoesNotDependOnTenancy(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Domain'))
            // Domain\Tenancy naturally depends on its own contracts
            ->excluding(Selector::inNamespace('App\Domain\Tenancy'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App\Contract\Tenancy'));
    }

    public function testPresentationMustUseBusForBusinessOperations(): Rule
    {
        // Controllers and Console commands must dispatch through CommandBus/QueryBus.
        // Direct use of service contracts bypasses bus middleware (auth, events, etc.).
        // Only middleware may use cross-cutting contracts (TenantBootstrapper, TenantContext).
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Presentation'))
            ->excluding(
                Selector::inNamespace('App\Presentation\Http\Middleware'),
                Selector::inNamespace('App\Presentation\Http\Request'),
            )
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App\Contract\Tenancy'));
    }

    public function testPresentationDoesNotDependOnDatabase(): Rule
    {
        // Presentation must never talk to the database directly.
        // All data access goes through CommandBus/QueryBus → Domain → Infrastructure.
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Presentation'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('Illuminate\Database'));
    }

    // ── Structural rules ────────────────────────────────────────

    public function testContractClassesAreInterfaces(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Contract'))
            ->excluding(
                Selector::isEnum(),
            )
            ->shouldBeInterface();
    }

    public function testDomainClassesAreFinal(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Domain'))
            ->excluding(Selector::isInterface())
            ->shouldBeFinal();
    }

    public function testDomainValueObjectsAreReadonly(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Domain'))
            ->excluding(
                Selector::isInterface(),
                Selector::isThrowable(),
                Selector::isEnum(),
            )
            ->shouldBeReadonly();
    }

    public function testInfrastructureClassesAreFinal(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Infrastructure'))
            ->shouldBeFinal();
    }

    public function testPresentationClassesAreFinal(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Presentation'))
            ->excluding(
                Selector::isTrait(),
                // Abstract base FormRequest is extended by concrete form requests
                Selector::isAbstract(),
            )
            ->shouldBeFinal();
    }

    public function testControllersAreInvokable(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Presentation\Http\Controller'))
            ->shouldBeInvokable();
    }

    // ── Scope filtering boundary ──────────────────────────────────

    public function testPresentationDoesNotDependOnTeamMembershipChecker(): Rule
    {
        // Scope resolution via team membership is a bus middleware concern.
        // Controllers must not know about team membership trees.
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Presentation'))
            ->shouldNotDependOn()
            ->classes(Selector::classname(\App\Contract\Team\TeamMembershipChecker::class));
    }

    public function testPresentationDoesNotDependOnAccessContext(): Rule
    {
        // AccessContext and AccessScope are scope-resolution types used by
        // ResolveScopeFilter middleware. If controllers can't reference these
        // types, they can't do scope filtering manually.
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Presentation'))
            ->shouldNotDependOn()
            ->classes(
                Selector::classname(\App\Application\Authorization\AccessContext::class),
                Selector::classname(\App\Contract\Authorization\AccessScope::class),
            );
    }

    // ── Safety nets ─────────────────────────────────────────────

    public function testNoCustomInheritance(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App'))
            // Form requests extend our abstract FormRequest base
            ->excluding(Selector::inNamespace('App\Presentation\Http\Request'))
            ->shouldNotExtend()
            ->classes(Selector::inNamespace('App'));
    }
}

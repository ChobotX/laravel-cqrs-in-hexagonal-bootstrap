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
                // Application-layer attributes are metadata annotations on Domain commands
                Selector::inNamespace('App\Application\Authorization'),
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

    // ── Structural rules ────────────────────────────────────────

    public function testContractClassesAreInterfaces(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Contract'))
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

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
                Selector::inNamespace('App\Application\Filtering'),
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
            // Dev-only CLI commands may depend on dev infrastructure
            ->excluding(Selector::classname(\App\Presentation\Console\Tenancy\DropSchemasCommand::class))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App\Infrastructure'));
    }

    public function testPresentationOnlyImportsDomainContracts(): Rule
    {
        // Presentation may only depend on Domain Contract — the public API of each module.
        // Contract contains shared types, events, exceptions, commands, and queries.
        // Everything else in Domain (handlers, services, internal VOs) is off-limits.
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Presentation'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App\Domain'))
            ->excluding(
                Selector::inNamespace('App\Domain\Authorization\Contract'),
                Selector::inNamespace('App\Domain\FeatureFlag\Contract'),
                Selector::inNamespace('App\Domain\File\Contract'),
                Selector::inNamespace('App\Domain\GridPreset\Contract'),
                Selector::inNamespace('App\Domain\Label\Contract'),
                Selector::inNamespace('App\Domain\Notification\Contract'),
                Selector::inNamespace('App\Domain\Registry\Contract'),
                Selector::inNamespace('App\Domain\Team\Contract'),
                Selector::inNamespace('App\Domain\Tenancy\Contract'),
                Selector::inNamespace('App\Domain\User\Contract'),
            );
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
            ->classes(Selector::classname(\App\Domain\Team\Contract\Service\TeamMembershipChecker::class));
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
                Selector::classname(\App\Domain\Authorization\Contract\Enum\AccessScope::class),
            );
    }

    // ── Infrastructure exception boundary ─────────────────────────

    public function testInfrastructureDoesNotDependOnHttpExceptions(): Rule
    {
        // Infrastructure should throw domain exceptions (implementing DomainException).
        // The Presentation layer translates them to HTTP status codes.
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Infrastructure'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('Symfony\Component\HttpKernel\Exception'));
    }

    // ── Middleware placement ──────────────────────────────────────

    public function testMiddlewareDoesNotLiveInInfrastructure(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Infrastructure'))
            ->shouldNotImplement()
            ->classes(Selector::classname(\App\Contract\Bus\Middleware::class))
            ->because('Middleware is business logic — place in Domain (context-specific) or Application (shared).');
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

<?php

declare(strict_types=1);

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
            );
    }

    public function testApplicationDependsOnlyOnDomainAndContract(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Application'))
            ->shouldNotDependOn()
            ->classes(Selector::all())
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
            ->excluding(Selector::isTrait())
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
            ->shouldNotExtend()
            ->classes(Selector::inNamespace('App'));
    }

    public function testNoGenericExceptions(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App'))
            ->shouldNotConstruct()
            ->classes(
                Selector::classname(Exception::class),
                Selector::classname(RuntimeException::class),
                Selector::classname(LogicException::class),
                Selector::classname(InvalidArgumentException::class),
                Selector::classname(BadMethodCallException::class),
                Selector::classname(DomainException::class),
                Selector::classname(RangeException::class),
                Selector::classname(OverflowException::class),
                Selector::classname(UnderflowException::class),
                Selector::classname(UnexpectedValueException::class),
                Selector::classname(LengthException::class),
                Selector::classname(OutOfRangeException::class),
                Selector::classname(OutOfBoundsException::class),
            );
    }
}

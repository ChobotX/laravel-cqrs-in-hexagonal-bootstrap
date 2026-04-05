<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Contract\Command\Command;
use App\Contract\Event\DomainEvent;
use App\Contract\Exception\DomainException;
use App\Contract\Query\Query;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function count;
use function explode;
use function sprintf;
use function str_starts_with;

/**
 * Enforces type constraints in Domain/{Module}/Contract/ subdirectories:
 * - Repository/, Service/ → interface
 * - Enum/ → enum
 * - Entity/, ValueObject/ → class (final+readonly enforced by PHPat)
 * - Command/ → class implementing Command
 * - Query/ → class implementing Query
 * - Event/ → class implementing DomainEvent
 * - Exception/ → class implementing DomainException
 *
 * @implements Rule<ClassLike>
 */
final readonly class ContractSubdirectoryTypeEnforcementRule implements Rule
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Domain\\')) {
            return [];
        }

        $parts = explode('\\', $namespace);

        // App\Domain\{Module}\Contract\{SubDir} = at least 5 parts
        if (count($parts) < 5 || $parts[3] !== 'Contract') {
            return [];
        }

        $subDir = $parts[4];
        $className = $node->namespacedName?->toString() ?? '';

        return match ($subDir) {
            'Repository', 'Service' => $this->mustBeInterface($node, $className, $subDir),
            'Enum' => $this->mustBeEnum($node, $className),
            'Entity', 'ValueObject' => $this->mustBeClass($node, $className, $subDir),
            'Command' => $this->mustImplementInterface($node, $className, Command::class, 'Command'),
            'Query' => $this->mustImplementInterface($node, $className, Query::class, 'Query'),
            'Event' => $this->mustImplementInterface($node, $className, DomainEvent::class, 'Event'),
            'Exception' => $this->mustImplementInterface($node, $className, DomainException::class, 'Exception'),
            default => [],
        };
    }

    /** @return list<IdentifierRuleError> */
    private function mustBeInterface(ClassLike $classLike, string $className, string $subDir): array
    {
        if ($classLike instanceof Interface_) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                '%s in Contract/%s/ must be an interface.',
                $className,
                $subDir,
            ))
                ->identifier('contract.subdirectoryType')
                ->build(),
        ];
    }

    /** @return list<IdentifierRuleError> */
    private function mustBeEnum(ClassLike $classLike, string $className): array
    {
        if ($classLike instanceof Enum_) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                '%s in Contract/Enum/ must be an enum.',
                $className,
            ))
                ->identifier('contract.subdirectoryType')
                ->build(),
        ];
    }

    /** @return list<IdentifierRuleError> */
    private function mustBeClass(ClassLike $classLike, string $className, string $subDir): array
    {
        if ($classLike instanceof Class_) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                '%s in Contract/%s/ must be a class.',
                $className,
                $subDir,
            ))
                ->identifier('contract.subdirectoryType')
                ->build(),
        ];
    }

    /** @return list<IdentifierRuleError> */
    private function mustImplementInterface(ClassLike $classLike, string $className, string $interface, string $subDir): array
    {
        if (! $classLike instanceof Class_) {
            return [
                RuleErrorBuilder::message(sprintf(
                    '%s in Contract/%s/ must be a class implementing %s.',
                    $className,
                    $subDir,
                    $interface,
                ))
                    ->identifier('contract.subdirectoryType')
                    ->build(),
            ];
        }

        if ($className === '' || ! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        if (! $classReflection->implementsInterface($interface)) {
            return [
                RuleErrorBuilder::message(sprintf(
                    '%s in Contract/%s/ must implement %s.',
                    $className,
                    $subDir,
                    $interface,
                ))
                    ->identifier('contract.subdirectoryType')
                    ->build(),
            ];
        }

        return [];
    }
}

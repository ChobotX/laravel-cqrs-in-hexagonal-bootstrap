<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Contract\Bus\BusMiddleware;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Exception\DomainException;
use App\Contract\Query\QueryHandler;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Enum_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function count;
use function explode;
use function sprintf;
use function str_ends_with;
use function str_starts_with;

/**
 * Enforces type constraints in Domain/{Module}/ subdirectories (non-Contract):
 * - Enum/ → enum
 * - ValueObject/, Service/, Policy/, Constant/ → class (final+readonly enforced by PHPat)
 * - Handler/Command/ → class implementing CommandHandler, name ends with Handler
 * - Handler/Query/ → class implementing QueryHandler, name ends with Handler
 * - EventHandler/ → class implementing DomainEventHandler
 * - Middleware/ → class implementing Bus\Middleware
 * - Exception/ → class implementing DomainException
 *
 * Exempt: Registry/Schema/ (mixed types by design).
 *
 * @implements Rule<ClassLike>
 */
final readonly class DomainSubdirectoryTypeEnforcementRule implements Rule
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

        // App\Domain\{Module}\{SubDir} = at least 4 parts
        if (count($parts) < 4) {
            return [];
        }

        $subDir = $parts[3];

        // Contract subdirectories handled by ContractSubdirectoryTypeEnforcementRule
        if ($subDir === 'Contract') {
            return [];
        }

        // Registry/Schema is exempt (mixed types by design)
        if ($parts[2] === 'Registry' && $subDir === 'Schema') {
            return [];
        }

        $className = $node->namespacedName?->toString() ?? '';

        // Handler/Command and Handler/Query (5+ parts)
        if ($subDir === 'Handler' && count($parts) >= 5) {
            return match ($parts[4]) {
                'Command' => $this->mustBeHandler($node, $className, CommandHandler::class, 'Handler/Command'),
                'Query' => $this->mustBeHandler($node, $className, QueryHandler::class, 'Handler/Query'),
                default => [],
            };
        }

        return match ($subDir) {
            'Enum' => $this->mustBeEnum($node, $className),
            'ValueObject', 'Service', 'Policy', 'Constant' => $this->mustBeClass($node, $className, $subDir),
            'EventHandler' => $this->mustImplementInterface($node, $className, DomainEventHandler::class, 'EventHandler'),
            'Middleware' => $this->mustImplementInterface($node, $className, BusMiddleware::class, 'Middleware'),
            'Exception' => $this->mustImplementInterface($node, $className, DomainException::class, 'Exception'),
            default => [],
        };
    }

    /** @return list<IdentifierRuleError> */
    private function mustBeEnum(ClassLike $classLike, string $className): array
    {
        if ($classLike instanceof Enum_) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                '%s in Enum/ must be an enum.',
                $className,
            ))
                ->identifier('domain.subdirectoryType')
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
                '%s in %s/ must be a class.',
                $className,
                $subDir,
            ))
                ->identifier('domain.subdirectoryType')
                ->build(),
        ];
    }

    /** @return list<IdentifierRuleError> */
    private function mustBeHandler(ClassLike $classLike, string $className, string $interface, string $subDir): array
    {
        $errors = $this->mustImplementInterface($classLike, $className, $interface, $subDir);

        if ($errors !== []) {
            return $errors;
        }

        if (! str_ends_with($className, 'Handler')) {
            return [
                RuleErrorBuilder::message(sprintf(
                    '%s in %s/ must have a name ending with Handler.',
                    $className,
                    $subDir,
                ))
                    ->identifier('domain.subdirectoryType')
                    ->build(),
            ];
        }

        return [];
    }

    /** @return list<IdentifierRuleError> */
    private function mustImplementInterface(ClassLike $classLike, string $className, string $interface, string $subDir): array
    {
        if (! $classLike instanceof Class_) {
            return [
                RuleErrorBuilder::message(sprintf(
                    '%s in %s/ must be a class implementing %s.',
                    $className,
                    $subDir,
                    $interface,
                ))
                    ->identifier('domain.subdirectoryType')
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
                    '%s in %s/ must implement %s.',
                    $className,
                    $subDir,
                    $interface,
                ))
                    ->identifier('domain.subdirectoryType')
                    ->build(),
            ];
        }

        return [];
    }
}

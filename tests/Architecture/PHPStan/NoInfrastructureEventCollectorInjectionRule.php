<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Contract\Event\EventCollector;
use App\Infrastructure\Bus\InMemoryEventCollector;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionClass as BetterReflectionAdapterReflectionClass;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

/**
 * Infrastructure must not hold {@see EventCollector} as state (properties, promoted constructor
 * properties, or method return types) except in {@see InMemoryEventCollector}.
 *
 * @implements Rule<Class_>
 */
final readonly class NoInfrastructureEventCollectorInjectionRule implements Rule
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAbstract() || $node->namespacedName === null) {
            return [];
        }

        $className = $node->namespacedName->toString();

        if (! str_starts_with($className, 'App\\Infrastructure\\')) {
            return [];
        }

        if ($className === InMemoryEventCollector::class) {
            return [];
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $nativeReflection = $this->reflectionProvider->getClass($className)->getNativeReflection();

        if (! $nativeReflection instanceof BetterReflectionAdapterReflectionClass) {
            return [];
        }

        return [
            ...$this->violationsFromProperties($nativeReflection, $className, $node->getStartLine()),
            ...$this->violationsFromMethodReturnTypes($nativeReflection, $className, $node->getStartLine()),
        ];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function violationsFromProperties(BetterReflectionAdapterReflectionClass $betterReflectionAdapterReflectionClass, string $className, int $fallbackLine): array
    {
        $errors = [];

        foreach ($betterReflectionAdapterReflectionClass->getProperties() as $reflectionProperty) {
            $error = $this->violationForType(
                $className,
                sprintf('property $%s', $reflectionProperty->getName()),
                $reflectionProperty->getType(),
                $fallbackLine,
            );

            if ($error instanceof IdentifierRuleError) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function violationsFromMethodReturnTypes(BetterReflectionAdapterReflectionClass $betterReflectionAdapterReflectionClass, string $className, int $fallbackLine): array
    {
        $errors = [];

        foreach ($betterReflectionAdapterReflectionClass->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->getDeclaringClass()->getName() !== $className) {
                continue;
            }

            $error = $this->violationForMethodReturn($className, $reflectionMethod, $fallbackLine);

            if ($error instanceof IdentifierRuleError) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    private function violationForMethodReturn(string $className, ReflectionMethod $reflectionMethod, int $fallbackLine): ?IdentifierRuleError
    {
        return $this->violationForType(
            $className,
            sprintf('method %s() return type', $reflectionMethod->getName()),
            $reflectionMethod->getReturnType(),
            $fallbackLine,
        );
    }

    private function violationForType(string $className, string $location, ?ReflectionType $reflectionType, int $line): ?IdentifierRuleError
    {
        if (! $this->isEventCollectorType($reflectionType)) {
            return null;
        }

        return RuleErrorBuilder::message(sprintf(
            'Infrastructure class %s must not declare %s as %s. Only %s may own the collector implementation.',
            $className,
            EventCollector::class,
            $location,
            InMemoryEventCollector::class,
        ))
            ->identifier('infrastructure.noEventCollectorInjection')
            ->line($line)
            ->build();
    }

    private function isEventCollectorType(?ReflectionType $reflectionType): bool
    {
        if (! $reflectionType instanceof ReflectionType) {
            return false;
        }

        if ($reflectionType instanceof ReflectionUnionType) {
            return array_any($reflectionType->getTypes(), fn (ReflectionType $reflectionType): bool => $this->isEventCollectorNamedType($reflectionType));
        }

        return $this->isEventCollectorNamedType($reflectionType);
    }

    private function isEventCollectorNamedType(ReflectionType $reflectionType): bool
    {
        if (! $reflectionType instanceof ReflectionNamedType || $reflectionType->isBuiltin()) {
            return false;
        }

        return $reflectionType->getName() === EventCollector::class;
    }
}

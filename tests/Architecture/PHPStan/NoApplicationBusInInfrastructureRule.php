<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;

/**
 * Infrastructure adapters must not inject the application CommandBus or QueryBus.
 *
 * @implements Rule<Class_>
 */
final readonly class NoApplicationBusInInfrastructureRule implements Rule
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

        if (! str_starts_with($className, 'App\\Infrastructure\\') || $this->isExcludedNamespace($className)) {
            return [];
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        /** @var list<IdentifierRuleError> $errors */
        $errors = [
            ...$this->violationsFromProperties($className, $this->reflectionProvider),
            ...$this->violationsFromConstructor($className, $this->reflectionProvider),
            ...$this->violationsFromMethods($className, $this->reflectionProvider),
        ];

        return $errors;
    }

    private function isExcludedNamespace(string $className): bool
    {
        return str_starts_with($className, 'App\\Infrastructure\\Bus\\')
            || str_starts_with($className, 'App\\Infrastructure\\Provider\\');
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function violationsFromProperties(string $className, ReflectionProvider $reflectionProvider): array
    {
        $native = $reflectionProvider->getClass($className)->getNativeReflection();
        $errors = [];

        foreach ($native->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isPromoted()) {
                continue;
            }

            $error = $this->busViolation($className, 'property $'.$reflectionProperty->getName(), $reflectionProperty->getType());

            if ($error instanceof IdentifierRuleError) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function violationsFromConstructor(string $className, ReflectionProvider $reflectionProvider): array
    {
        $native = $reflectionProvider->getClass($className)->getNativeReflection();
        $constructor = $native->getConstructor();

        if ($constructor === null) {
            return [];
        }

        $errors = [];

        foreach ($constructor->getParameters() as $reflectionParameter) {
            $error = $this->busViolation($className, 'constructor parameter $'.$reflectionParameter->getName(), $reflectionParameter->getType());

            if ($error instanceof IdentifierRuleError) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function violationsFromMethods(string $className, ReflectionProvider $reflectionProvider): array
    {
        $native = $reflectionProvider->getClass($className)->getNativeReflection();
        $errors = [];

        foreach ($native->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->getDeclaringClass()->getName() !== $className) {
                continue;
            }

            if ($reflectionMethod->isConstructor()) {
                continue;
            }

            foreach ($this->violationsFromMethodParameters($className, $reflectionMethod) as $error) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function violationsFromMethodParameters(string $className, ReflectionMethod $reflectionMethod): array
    {
        $errors = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $error = $this->busViolation(
                $className,
                sprintf('method %s() parameter $%s', $reflectionMethod->getName(), $reflectionParameter->getName()),
                $reflectionParameter->getType(),
            );

            if ($error instanceof IdentifierRuleError) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    private function busViolation(string $className, string $location, ?ReflectionType $reflectionType): ?IdentifierRuleError
    {
        if (! $reflectionType instanceof ReflectionNamedType || $reflectionType->isBuiltin()) {
            return null;
        }

        $typeName = $reflectionType->getName();

        if ($typeName !== CommandBus::class && $typeName !== QueryBus::class) {
            return null;
        }

        return RuleErrorBuilder::message(
            sprintf(
                'Infrastructure class %s must not depend on %s (%s). Use Domain handlers/services and repository ports instead.',
                $className,
                $typeName,
                $location,
            ),
        )
            ->identifier('infrastructure.noApplicationBus')
            ->build();
    }
}

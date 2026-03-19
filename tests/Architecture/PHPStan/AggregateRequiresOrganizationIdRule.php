<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Organization\TenantAgnostic;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Class_>
 */
final readonly class AggregateRequiresOrganizationIdRule implements Rule
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isFinal() || ! $node->isReadonly() || $node->namespacedName === null) {
            return [];
        }

        $className = $node->namespacedName->toString();

        if (! str_starts_with($className, 'App\\Domain\\')) {
            return [];
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $repositoryName = $this->deriveRepositoryName($className);

        if (! $this->reflectionProvider->hasClass($repositoryName)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        $nativeReflection = $classReflection->getNativeReflection();

        if ($nativeReflection->getAttributes(TenantAgnostic::class) !== []) {
            return [];
        }

        if ($this->hasOrganizationIdParameter($node)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Aggregate %s must have an $organizationId constructor parameter or use #[TenantAgnostic] attribute.',
                    $className,
                ),
            )
                ->identifier('domain.aggregateRequiresOrganizationId')
                ->build(),
        ];
    }

    private function deriveRepositoryName(string $className): string
    {
        $parts = explode('\\', $className);
        $shortName = end($parts);

        array_pop($parts);

        return implode('\\', $parts).'\\'.$shortName.'Repository';
    }

    private function hasOrganizationIdParameter(Class_ $class): bool
    {
        $constructor = $class->getMethod('__construct');

        if (! $constructor instanceof Node\Stmt\ClassMethod) {
            return false;
        }

        return array_any($constructor->params, fn ($param): bool => $param->var instanceof Node\Expr\Variable && $param->var->name === 'organizationId');
    }
}

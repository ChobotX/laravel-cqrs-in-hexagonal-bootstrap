<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Organization\AllowNullableOrganizationId;
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
        $className = $this->resolveTargetAggregateName($node);

        if ($className === null) {
            return [];
        }

        $organizationIdParam = $this->findOrganizationIdParameter($node);

        if (! $organizationIdParam instanceof Node\Param) {
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

        $nativeReflection = $this->reflectionProvider->getClass($className)->getNativeReflection();

        if ($this->isNullableParam($organizationIdParam) && $nativeReflection->getAttributes(AllowNullableOrganizationId::class) === []) {
            return [
                RuleErrorBuilder::message(
                    sprintf(
                        'Aggregate %s has a nullable $organizationId — it must be non-nullable or use #[AllowNullableOrganizationId] attribute.',
                        $className,
                    ),
                )
                    ->identifier('domain.aggregateNullableOrganizationId')
                    ->build(),
            ];
        }

        return [];
    }

    private function resolveTargetAggregateName(Class_ $class): ?string
    {
        if (! $class->isFinal() || ! $class->isReadonly() || ! $class->namespacedName instanceof Node\Name) {
            return null;
        }

        $className = $class->namespacedName->toString();

        if (! str_starts_with($className, 'App\\Domain\\') || ! $this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $repositoryName = $this->deriveRepositoryName($className);

        if (! $this->reflectionProvider->hasClass($repositoryName)) {
            return null;
        }

        $nativeReflection = $this->reflectionProvider->getClass($className)->getNativeReflection();

        if ($nativeReflection->getAttributes(TenantAgnostic::class) !== []) {
            return null;
        }

        return $className;
    }

    private function deriveRepositoryName(string $className): string
    {
        $parts = explode('\\', $className);
        $shortName = end($parts);

        array_pop($parts);

        return implode('\\', $parts).'\\'.$shortName.'Repository';
    }

    private function findOrganizationIdParameter(Class_ $class): ?Node\Param
    {
        $constructor = $class->getMethod('__construct');

        if (! $constructor instanceof Node\Stmt\ClassMethod) {
            return null;
        }

        foreach ($constructor->params as $param) {
            if ($param->var instanceof Node\Expr\Variable && $param->var->name === 'organizationId') {
                return $param;
            }
        }

        return null;
    }

    private function isNullableParam(Node\Param $param): bool
    {
        if ($param->type instanceof Node\NullableType) {
            return true;
        }

        if ($param->type instanceof Node\UnionType) {
            return array_any(
                $param->type->types,
                static fn (Node $type): bool => $type instanceof Node\Identifier && $type->name === 'null',
            );
        }

        return $param->default instanceof Node\Expr\ConstFetch
            && $param->default->name->toLowerString() === 'null';
    }
}

<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Contract\Event\EventCollector;
use App\Infrastructure\Bus\InMemoryEventCollector;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;

/**
 * Domain events must be collected in Domain (handlers/services), not in Infrastructure adapters.
 *
 * @implements Rule<MethodCall>
 */
final class NoInfrastructureEventCollectorCollectRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        /** @var MethodCall $node */
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\Infrastructure\\')) {
            return [];
        }

        $classReflection = $scope->getClassReflection();

        if ($classReflection instanceof \PHPStan\Reflection\ClassReflection && $classReflection->getName() === InMemoryEventCollector::class) {
            return [];
        }

        if (! $node->name instanceof Identifier || $node->name->toString() !== 'collect') {
            return [];
        }

        $receiverType = $scope->getType($node->var);
        $objectType = new ObjectType(EventCollector::class);

        if (! $objectType->isSuperTypeOf($receiverType)->yes()) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Infrastructure must not call EventCollector::collect(). Raise domain events from Domain command handlers or domain services.',
            )
                ->identifier('infrastructure.noEventCollectorCollect')
                ->build(),
        ];
    }
}

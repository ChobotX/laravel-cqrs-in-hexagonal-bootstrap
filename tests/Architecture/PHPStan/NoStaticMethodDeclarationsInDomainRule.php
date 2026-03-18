<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<ClassMethod>
 */
final class NoStaticMethodDeclarationsInDomainRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->isStatic()) {
            return [];
        }

        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Domain')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Static method %s::%s() is not allowed in the Domain layer. Use instance methods with dependency injection instead.',
                $scope->getClassReflection()?->getName() ?? '<unknown>',
                $node->name->toString(),
            ))
                ->identifier('domain.noStaticDeclaration')
                ->build(),
        ];
    }
}

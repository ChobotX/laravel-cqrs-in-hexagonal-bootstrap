<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_starts_with;

/**
 * @implements Rule<Node>
 */
final class NoMixedInDomainRule implements Rule
{
    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\Domain')) {
            return [];
        }

        if ($node instanceof ClassMethod) {
            return $this->checkClassMethod($node);
        }

        if ($node instanceof Property) {
            return $this->checkProperty($node);
        }

        return [];
    }

    /** @return list<IdentifierRuleError> */
    private function checkClassMethod(ClassMethod $classMethod): array
    {
        $errors = [];

        if ($this->isMixedIdentifier($classMethod->returnType)) {
            $errors[] = $this->buildError('return');
        }

        foreach ($classMethod->params as $param) {
            if ($this->isMixedIdentifier($param->type)) {
                $errors[] = $this->buildError('parameter');
            }
        }

        return $errors;
    }

    /** @return list<IdentifierRuleError> */
    private function checkProperty(Property $property): array
    {
        if ($this->isMixedIdentifier($property->type)) {
            return [$this->buildError('property')];
        }

        return [];
    }

    private function isMixedIdentifier(?Node $node): bool
    {
        return $node instanceof Identifier && $node->name === 'mixed';
    }

    private function buildError(string $position): IdentifierRuleError
    {
        return RuleErrorBuilder::message(
            sprintf('Using "mixed" %s type in App\Domain is not allowed. Use a specific type instead.', $position),
        )
            ->identifier('domain.noMixed')
            ->build();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
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

        if ($this->containsMixed($classMethod->returnType) && ! $this->hasSpecificReturnAnnotation($classMethod)) {
            $errors[] = $this->buildError('return');
        }

        foreach ($classMethod->params as $param) {
            if ($this->containsMixed($param->type)) {
                $errors[] = $this->buildError('parameter');
            }
        }

        return $errors;
    }

    /** @return list<IdentifierRuleError> */
    private function checkProperty(Property $property): array
    {
        if ($this->containsMixed($property->type)) {
            return [$this->buildError('property')];
        }

        return [];
    }

    private function hasSpecificReturnAnnotation(ClassMethod $classMethod): bool
    {
        $docComment = $classMethod->getDocComment();

        if (! $docComment instanceof \PhpParser\Comment\Doc) {
            return false;
        }

        if (preg_match('/@return\s+(\S+)/', $docComment->getText(), $matches) !== 1) {
            return false;
        }

        return $matches[1] !== 'mixed';
    }

    private function containsMixed(?Node $node): bool
    {
        if ($node instanceof Identifier) {
            return $node->name === 'mixed';
        }

        if ($node instanceof NullableType) {
            return $this->containsMixed($node->type);
        }

        if (! $node instanceof UnionType && ! $node instanceof IntersectionType) {
            return false;
        }

        return array_any($node->types, fn (?Node $node): bool => $this->containsMixed($node));
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

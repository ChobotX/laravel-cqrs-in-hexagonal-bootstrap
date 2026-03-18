<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Contract\Exception\DomainException;
use PhpParser\Node;
use PhpParser\Node\Expr\Throw_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;

/**
 * @implements Rule<Throw_>
 */
final class OnlyDomainExceptionsInDomainRule implements Rule
{
    public function getNodeType(): string
    {
        return Throw_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Domain')) {
            return [];
        }

        $thrownType = $scope->getType($node->expr);
        $objectType = new ObjectType(DomainException::class);

        if ($objectType->isSuperTypeOf($thrownType)->yes()) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Only exceptions implementing %s can be thrown in the Domain layer. Got: %s',
                DomainException::class,
                $thrownType->describe(VerbosityLevel::typeOnly()),
            ))
                ->identifier('domain.onlyDomainExceptions')
                ->build(),
        ];
    }
}

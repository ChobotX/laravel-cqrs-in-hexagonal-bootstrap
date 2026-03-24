<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<New_>
 */
final class NoGenericExceptionsRule implements Rule
{
    private const array FORBIDDEN = [
        'Exception',
        'RuntimeException',
        'LogicException',
        'InvalidArgumentException',
        'BadMethodCallException',
        'DomainException',
        'RangeException',
        'OverflowException',
        'UnderflowException',
        'UnexpectedValueException',
        'LengthException',
        'OutOfRangeException',
        'OutOfBoundsException',
    ];

    public function getNodeType(): string
    {
        return New_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\')) {
            return [];
        }

        if (! $node->class instanceof Name) {
            return [];
        }

        $className = $scope->resolveName($node->class);

        if (! in_array($className, self::FORBIDDEN, true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Constructing generic %s is not allowed in App. Create a specific exception class instead.',
                $className,
            ))
                ->identifier('app.noGenericException')
                ->build(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_starts_with;

/**
 * @implements Rule<FuncCall>
 */
final class NoAssertInAppRule implements Rule
{
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name || $node->name->toString() !== 'assert') {
            return [];
        }

        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Using assert() in application code is not allowed. Use a proper exception instead.',
            )
                ->identifier('app.noAssert')
                ->build(),
        ];
    }
}

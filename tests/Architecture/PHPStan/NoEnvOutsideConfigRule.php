<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_contains;

/**
 * @implements Rule<FuncCall>
 */
final class NoEnvOutsideConfigRule implements Rule
{
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name || $node->name->toString() !== 'env') {
            return [];
        }

        if (str_contains($scope->getFile(), '/config/')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Calling env() outside config files is not allowed. Read environment variables through config() instead.',
            )
                ->identifier('app.noEnvOutsideConfig')
                ->build(),
        ];
    }
}

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
final class NoAppHelperInPresentationRule implements Rule
{
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name) {
            return [];
        }

        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Presentation')) {
            return [];
        }

        $functionName = $node->name->toString();

        if ($functionName !== 'app') {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Calling app() in the Presentation layer is not allowed. Use constructor dependency injection instead.',
            )
                ->identifier('presentation.noAppHelper')
                ->build(),
        ];
    }
}

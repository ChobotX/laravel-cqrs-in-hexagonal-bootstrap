<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Presentation\Console\Trait\StrictArguments;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function in_array;
use function sprintf;

/**
 * @implements Rule<MethodCall>
 */
final class UseStrictArgumentsInConsoleRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();
        if ($namespace === null || ! str_starts_with($namespace, 'App\Presentation\Console')) {
            return [];
        }

        if ($scope->isInTrait() && $scope->getTraitReflection()->getName() === StrictArguments::class) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->name;
        if (! in_array($methodName, ['argument', 'option'], true)) {
            return [];
        }

        if (! $node->var instanceof Variable || $node->var->name !== 'this') {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Direct $this->%s() is banned in console commands. Use StrictArguments trait methods (e.g. stringArgument) instead.',
                $methodName,
            ))
                ->identifier('console.useStrictArguments')
                ->build(),
        ];
    }
}

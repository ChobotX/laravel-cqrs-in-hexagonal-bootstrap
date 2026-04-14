<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function in_array;
use function str_starts_with;

/**
 * @implements Rule<FuncCall>
 */
final class NoTranslationHelperInInfrastructureRule implements Rule
{
    private const array BLOCKED_HELPERS = ['__', 'trans', 'trans_choice'];

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

        if ($namespace === null || ! str_starts_with($namespace, 'App\\Infrastructure\\')) {
            return [];
        }

        if (str_starts_with($namespace, 'App\\Infrastructure\\Translation')) {
            return [];
        }

        $functionName = $node->name->toString();

        if (! in_array($functionName, self::BLOCKED_HELPERS, true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Infrastructure must not call %s(). Translation key selection belongs to Domain/Presentation; pass translated strings through a port.',
                    $functionName,
                ),
            )
                ->identifier('infrastructure.noTranslationHelper')
                ->build(),
        ];
    }
}

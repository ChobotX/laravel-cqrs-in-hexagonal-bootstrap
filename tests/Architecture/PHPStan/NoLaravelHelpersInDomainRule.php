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
final class NoLaravelHelpersInDomainRule implements Rule
{
    private const array BLOCKED_HELPERS = [
        '__',
        'trans',
        'trans_choice',
        'app',
        'resolve',
        'config',
        'session',
        'cache',
        'event',
        'dispatch',
        'redirect',
        'response',
        'view',
        'route',
        'url',
        'abort',
        'abort_if',
        'abort_unless',
        'logger',
        'info',
        'report',
        'request',
        'cookie',
        'old',
        'now',
        'today',
        'auth',
        'back',
        'bcrypt',
        'encrypt',
        'decrypt',
        'validator',
        'rescue',
        'retry',
        'throw_if',
        'throw_unless',
    ];

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

        if ($namespace === null || ! str_starts_with($namespace, 'App\Domain')) {
            return [];
        }

        $functionName = $node->name->toString();

        if (! in_array($functionName, self::BLOCKED_HELPERS, true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf('Calling %s() in the Domain layer is not allowed. Use a Contract interface instead.', $functionName),
            )
                ->identifier('domain.noLaravelHelpers')
                ->build(),
        ];
    }
}

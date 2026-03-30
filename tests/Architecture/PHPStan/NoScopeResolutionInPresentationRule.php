<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_starts_with;

/**
 * Prevents controllers from resolving access scope directly.
 * Scope resolution happens in ResolveScopeFilter bus middleware,
 * not in the presentation layer. Controllers may use can() for
 * binary UI visibility checks, but must never call canWithScope().
 *
 * @implements Rule<MethodCall>
 */
final class NoScopeResolutionInPresentationRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Presentation')) {
            return [];
        }

        if (! $node->name instanceof Identifier || $node->name->name !== 'canWithScope') {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Calling canWithScope() in the Presentation layer is not allowed. '
                .'Scope-based filtering is handled by ResolveScopeFilter bus middleware. '
                .'Use can() for binary permission checks only.',
            )
                ->identifier('presentation.noScopeResolution')
                ->build(),
        ];
    }
}

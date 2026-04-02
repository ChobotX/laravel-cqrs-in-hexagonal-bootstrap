<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Controllers must not call ->dispatch() inside foreach loops.
 * Looping over dispatch calls — whether commands or queries — is an
 * N+1 pattern. Commands should use aggregate handlers; queries should
 * use batch queries that accept multiple IDs.
 *
 * @implements Rule<Foreach_>
 */
final class NoBusDispatchInControllerLoopsRule implements Rule
{
    public function getNodeType(): string
    {
        return Foreach_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Presentation\Http\Controller')) {
            return [];
        }

        $nodeFinder = new NodeFinder;

        $dispatchCalls = $nodeFinder->find($node->stmts, static function (Node $child): bool {
            if (! $child instanceof MethodCall) {
                return false;
            }

            if (! $child->name instanceof Identifier) {
                return false;
            }

            return $child->name->name === 'dispatch';
        });

        if ($dispatchCalls === []) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Controllers must not dispatch bus messages in loops. Use batch queries or aggregate commands instead.',
            )
                ->identifier('controller.noBusDispatchInLoop')
                ->build(),
        ];
    }
}

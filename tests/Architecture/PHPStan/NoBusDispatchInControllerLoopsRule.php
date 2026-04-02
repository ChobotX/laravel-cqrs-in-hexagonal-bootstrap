<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Bus\CommandBus;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;

/**
 * Controllers must not call CommandBus->dispatch() inside foreach loops.
 * Looping over command dispatches is the signature of orchestration/sync logic
 * that belongs in a Domain command handler. A controller should dispatch
 * a single aggregate command instead.
 *
 * Query dispatches in loops (for data assembly) are allowed.
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

        $objectType = new ObjectType(CommandBus::class);
        $nodeFinder = new NodeFinder;

        $dispatchCalls = $nodeFinder->find($node->stmts, static function (Node $child) use ($scope, $objectType): bool {
            if (! $child instanceof MethodCall) {
                return false;
            }

            if (! $child->name instanceof Identifier || $child->name->name !== 'dispatch') {
                return false;
            }

            $callerType = $scope->getType($child->var);

            return $objectType->isSuperTypeOf($callerType)->yes();
        });

        if ($dispatchCalls === []) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Controllers must not dispatch commands in loops. Extract the orchestration into a Domain command handler.',
            )
                ->identifier('controller.noCommandDispatchInLoop')
                ->build(),
        ];
    }
}

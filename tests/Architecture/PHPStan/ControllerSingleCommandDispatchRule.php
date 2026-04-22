<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Presentation controllers must dispatch at most one CommandBus::dispatch() call
 * per class. Multiple command dispatches belong in an orchestration handler so the
 * transactional + permission boundary stays coherent and the controller is thin.
 *
 * QueryBus dispatches are unlimited; only commandBus->dispatch() counts.
 *
 * @implements Rule<Class_>
 */
final class ControllerSingleCommandDispatchRule implements Rule
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isControllerNamespace($scope)) {
            return [];
        }

        $dispatchCalls = (new NodeFinder)->find($node->stmts, $this->isCommandBusDispatch(...));

        if (count($dispatchCalls) <= 1) {
            return [];
        }

        $className = $node->namespacedName?->toString() ?? 'anonymous';

        return [
            RuleErrorBuilder::message(sprintf(
                '%s dispatches %d commands. Controllers may dispatch at most one command. Move orchestration into a domain CommandHandler that dispatches sub-commands via the bus.',
                $className,
                count($dispatchCalls),
            ))
                ->identifier('controller.singleCommandDispatch')
                ->build(),
        ];
    }

    private function isControllerNamespace(Scope $scope): bool
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null) {
            return false;
        }

        return str_starts_with($namespace, 'App\Presentation\Http\Controller')
            || str_starts_with($namespace, 'App\Presentation\Console');
    }

    private function isCommandBusDispatch(Node $node): bool
    {
        if (! $node instanceof MethodCall) {
            return false;
        }

        if (! $node->name instanceof Identifier || $node->name->name !== 'dispatch') {
            return false;
        }

        if (! $node->var instanceof PropertyFetch) {
            return false;
        }

        return $node->var->name instanceof Identifier && $node->var->name->name === 'commandBus';
    }
}

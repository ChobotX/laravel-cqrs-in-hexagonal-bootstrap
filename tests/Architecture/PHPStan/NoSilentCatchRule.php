<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function in_array;
use function str_contains;

/**
 * @implements Rule<Catch_>
 */
final class NoSilentCatchRule implements Rule
{
    private const array LOG_METHODS = ['error', 'warning', 'info', 'debug', 'critical'];

    public function getNodeType(): string
    {
        return Catch_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! str_contains($scope->getFile(), '/app/')) {
            return [];
        }

        if ($this->hasThrowOrAbort($node) || $this->hasLogMethodCall($node) || $this->hasSilentComment($node)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Catch block silently swallows the exception. Either rethrow, log (Logger::error(), $this->error()), or add a // @silent: <reason> comment.',
            )
                ->identifier('app.noSilentCatch')
                ->build(),
        ];
    }

    private function hasThrowOrAbort(Catch_ $catch): bool
    {
        $nodeFinder = new NodeFinder;

        return $nodeFinder->findFirst($catch->stmts, static function (Node $n): bool {
            if ($n instanceof Expr\Throw_) {
                return true;
            }

            return $n instanceof FuncCall
                && $n->name instanceof Name
                && $n->name->toString() === 'abort';
        }) instanceof Node;
    }

    private function hasLogMethodCall(Catch_ $catch): bool
    {
        $nodeFinder = new NodeFinder;

        return $nodeFinder->findFirst($catch->stmts, static fn (Node $n): bool => $n instanceof MethodCall
            && $n->name instanceof Identifier
            && in_array($n->name->toString(), self::LOG_METHODS, true)) instanceof Node;
    }

    private function hasSilentComment(Catch_ $catch): bool
    {
        if ($this->commentsContainSilent($catch)) {
            return true;
        }

        return array_any($catch->stmts, fn (Node $stmt): bool => $this->commentsContainSilent($stmt));
    }

    private function commentsContainSilent(Node $node): bool
    {
        return array_any($node->getComments(), fn (\PhpParser\Comment $comment): bool => str_contains($comment->getText(), '@silent'));
    }
}

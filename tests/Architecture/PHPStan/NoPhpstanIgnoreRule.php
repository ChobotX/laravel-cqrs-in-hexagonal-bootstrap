<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_contains;

/**
 * @implements Rule<Stmt>
 */
final class NoPhpstanIgnoreRule implements Rule
{
    public function getNodeType(): string
    {
        return Stmt::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if (! str_contains($file, '/app/') && ! str_contains($file, '/tests/')) {
            return [];
        }

        $errors = [];

        foreach ($node->getComments() as $comment) {
            if ($this->containsIgnoreDirective($comment)) {
                $errors[] = RuleErrorBuilder::message(
                    '@phpstan-ignore comments are not allowed. Fix the underlying type issue instead.',
                )
                    ->identifier('app.noPhpstanIgnore')
                    ->line($comment->getStartLine())
                    ->build();
            }
        }

        return $errors;
    }

    private function containsIgnoreDirective(Comment $comment): bool
    {
        $text = $comment->getText();

        return str_contains($text, '@phpstan-ignore')
            || str_contains($text, '@phpstan-ignore-next-line')
            || str_contains($text, '@phpstan-ignore-line');
    }
}

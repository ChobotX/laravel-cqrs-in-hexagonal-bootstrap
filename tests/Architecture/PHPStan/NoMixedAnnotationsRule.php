<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function preg_match_all;
use function str_starts_with;

/**
 * @implements Rule<Node>
 */
final class NoMixedAnnotationsRule implements Rule
{
    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! ($node instanceof ClassMethod || $node instanceof Property || $node instanceof Function_ || $node instanceof ClassLike)) {
            return [];
        }

        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\')) {
            return [];
        }

        $docComment = $node->getDocComment();

        if (! $docComment instanceof \PhpParser\Comment\Doc) {
            return [];
        }

        $text = $docComment->getText();
        $errors = [];

        preg_match_all('/@(param|return|var)\s+\S*\bmixed\b/', $text, $matches);

        foreach ($matches[0] as $match) {
            $errors[] = RuleErrorBuilder::message(sprintf(
                'Using "mixed" in PHPDoc annotation "%s" is not allowed in application code. Use a specific type instead.',
                $match,
            ))
                ->identifier('app.noMixedAnnotation')
                ->build();
        }

        return $errors;
    }
}

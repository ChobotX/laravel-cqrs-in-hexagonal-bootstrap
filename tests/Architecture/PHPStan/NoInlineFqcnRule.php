<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_starts_with;

/**
 * @implements Rule<Name>
 */
final class NoInlineFqcnRule implements Rule
{
    public function getNodeType(): string
    {
        return Name::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\')) {
            return [];
        }

        $referencedName = $node->toString();

        if (! str_starts_with($referencedName, 'App\\')) {
            return [];
        }

        $originalName = $node->getAttribute('originalName');

        if ($node->isFullyQualified() && $originalName instanceof Name && $originalName->isFullyQualified()) {
            return [
                RuleErrorBuilder::message(
                    sprintf('Inline fully-qualified class name \\%s should be imported with a use statement.', $referencedName),
                )
                    ->identifier('app.noInlineFqcn')
                    ->build(),
            ];
        }

        return [];
    }
}

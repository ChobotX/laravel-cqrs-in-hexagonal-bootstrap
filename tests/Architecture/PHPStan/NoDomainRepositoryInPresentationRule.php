<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_contains;
use function str_starts_with;

/**
 * @implements Rule<Name>
 */
final class NoDomainRepositoryInPresentationRule implements Rule
{
    public function getNodeType(): string
    {
        return Name::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Presentation')) {
            return [];
        }

        $referencedName = $node->toString();

        if (! str_starts_with($referencedName, 'App\Domain\\') || ! str_contains($referencedName, 'Repository')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf('Presentation layer must not import domain repository %s. Use QueryBus/CommandBus instead.', $referencedName),
            )
                ->identifier('presentation.noDomainRepository')
                ->build(),
        ];
    }
}

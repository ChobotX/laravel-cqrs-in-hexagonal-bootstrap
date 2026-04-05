<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function count;
use function explode;
use function sprintf;
use function str_starts_with;

/**
 * No PHP classes directly at Domain/{Module}/Contract/ root — must be in a typed subdirectory
 * (Entity/, ValueObject/, Repository/, Service/, Command/, Query/, Event/, Exception/, Enum/).
 *
 * @implements Rule<ClassLike>
 */
final class NoLooseFilesInContractRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Domain\\')) {
            return [];
        }

        $parts = explode('\\', $namespace);

        // App\Domain\{Module}\Contract = 4 parts with Contract at index 3
        if (count($parts) !== 4 || $parts[3] !== 'Contract') {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Class in Domain\%s\Contract root is not allowed. Move it to a typed subdirectory (Entity/, ValueObject/, Repository/, Service/, Command/, Query/, Event/, Exception/, Enum/).',
                $parts[2],
            ))
                ->identifier('domain.noLooseFilesInContract')
                ->build(),
        ];
    }
}

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
 * No PHP classes directly at Domain/{Module}/ root — must be in a typed subdirectory
 * (Handler/, ValueObject/, Enum/, Service/, Exception/, etc.).
 *
 * @implements Rule<ClassLike>
 */
final class NoLooseFilesInDomainModuleRule implements Rule
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

        // App\Domain\{Module} = 3 parts → file is at module root (loose)
        if (count($parts) !== 3) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Class in Domain\%s root is not allowed. Move it to a typed subdirectory (Handler/, ValueObject/, Enum/, Service/, Exception/, etc.).',
                $parts[2],
            ))
                ->identifier('domain.noLooseFilesInModule')
                ->build(),
        ];
    }
}

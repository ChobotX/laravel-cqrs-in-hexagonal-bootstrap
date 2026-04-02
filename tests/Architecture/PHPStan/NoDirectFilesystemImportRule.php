<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function sprintf;
use function str_contains;
use function str_starts_with;

/**
 * @implements Rule<Name>
 */
final class NoDirectFilesystemImportRule implements Rule
{
    public function getNodeType(): string
    {
        return Name::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if (! str_contains($file, '/app/')
            || str_contains($file, '/Infrastructure/Filesystem/')
            || str_contains($file, '/Infrastructure/Provider/')) {
            return [];
        }

        $name = $node->toString();

        if (! str_starts_with($name, 'Illuminate\Contracts\Filesystem\\')
            && ! str_starts_with($name, 'Illuminate\Filesystem\\')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Direct use of %s is not allowed outside Infrastructure\Filesystem. Inject App\Domain\File\Contract\FileStorage instead.',
                $name,
            ))
                ->identifier('app.noDirectFilesystemImport')
                ->build(),
        ];
    }
}

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
final class FileStorageOnlyInFileDomainRule implements Rule
{
    private const string FILE_STORAGE_CLASS = \App\Domain\File\Contract\Service\FileStorage::class;

    public function getNodeType(): string
    {
        return Name::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if (! str_contains($file, '/app/')) {
            return [];
        }

        $referencedName = $node->toString();

        if ($referencedName !== self::FILE_STORAGE_CLASS) {
            return [];
        }

        $namespace = $scope->getNamespace();

        if ($namespace === null) {
            return [];
        }

        if (str_starts_with($namespace, 'App\Domain\File')
            || str_starts_with($namespace, 'App\Infrastructure\Filesystem')
            || str_starts_with($namespace, 'App\Infrastructure\Provider')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Direct use of FileStorage is not allowed outside Domain\File and Infrastructure\Filesystem. Use StoreFileCommand/DeleteFileCommand/GetFileContentQuery through the bus instead.',
            )
                ->identifier('app.fileStorageOnlyInFileDomain')
                ->build(),
        ];
    }
}

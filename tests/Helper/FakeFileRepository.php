<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\File\Contract\FileId;
use App\Domain\File\Contract\FileRepository;
use App\Domain\File\File;
use App\Domain\File\FileName;
use App\Domain\File\FileNamespace;

final class FakeFileRepository implements FileRepository
{
    /** @var list<File> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, File> $files */
    public function __construct(
        private array $files = [],
    ) {}

    public function findById(FileId $fileId): ?File
    {
        return $this->files[$fileId->value] ?? null;
    }

    public function create(File $file): void
    {
        $this->saved[] = $file;
        $this->files[$file->id->value] = $file;
    }

    public function delete(FileId $fileId): void
    {
        $this->deleted[] = $fileId->value;
        unset($this->files[$fileId->value]);
    }

    /** @return list<File> */
    public function findVersions(FileNamespace $fileNamespace, FileName $fileName): array
    {
        $matches = array_filter(
            $this->files,
            fn (File $file): bool => $file->namespace->value === $fileNamespace->value
                && $file->originalName->value === $fileName->value,
        );

        usort($matches, fn (File $a, File $b): int => $a->version->value <=> $b->version->value);

        return $matches;
    }

    public function latestVersion(FileNamespace $fileNamespace, FileName $fileName): ?File
    {
        $versions = $this->findVersions($fileNamespace, $fileName);

        if ($versions === []) {
            return null;
        }

        return $versions[count($versions) - 1];
    }

    public function nextVersionNumber(FileNamespace $fileNamespace, FileName $fileName): int
    {
        $latest = $this->latestVersion($fileNamespace, $fileName);

        return $latest instanceof File ? $latest->version->value + 1 : 1;
    }
}

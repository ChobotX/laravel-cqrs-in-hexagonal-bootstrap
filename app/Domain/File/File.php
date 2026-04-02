<?php

declare(strict_types=1);

namespace App\Domain\File;

use App\Domain\File\Contract\FileId;
use DateTimeImmutable;

final readonly class File
{
    public function __construct(
        public FileId $id,
        public FileNamespace $namespace,
        public FileName $originalName,
        public StoragePath $storagePath,
        public MimeType $mimeType,
        public int $sizeInBytes,
        public FileVersion $version,
        public string $uploadedBy,
        public DateTimeImmutable $uploadedAt,
    ) {}
}

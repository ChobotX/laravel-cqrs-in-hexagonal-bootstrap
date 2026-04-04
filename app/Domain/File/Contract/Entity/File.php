<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Entity;

use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\File\ValueObject\FileNamespace;
use App\Domain\File\ValueObject\FileVersion;
use App\Domain\File\ValueObject\StoragePath;
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

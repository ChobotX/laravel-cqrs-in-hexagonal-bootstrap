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

/**
 * Immutable read-model snapshot of a File returned from queries in the File context.
 */
final readonly class File
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public FileId $id,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public FileNamespace $namespace,
        /** Human-visible label or title. */
        public FileName $originalName,
        /** Filesystem or storage path as understood by infrastructure adapters. */
        public StoragePath $storagePath,
        /** Classifier string or type discriminator. */
        public MimeType $mimeType,
        /** Field `sizeInBytes` for this contract; see module docs for validation rules. */
        public int $sizeInBytes,
        /** Field `version` for this contract; see module docs for validation rules. */
        public FileVersion $version,
        /** Field `uploadedBy` for this contract; see module docs for validation rules. */
        public string $uploadedBy,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $uploadedAt,
    ) {}
}

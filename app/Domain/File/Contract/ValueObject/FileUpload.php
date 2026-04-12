<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\ValueObject;

use SplFileInfo;

/**
 * Contract-level value object for file upload used across File commands, queries, and events.
 */
final readonly class FileUpload
{
    public function __construct(
        /** Human-visible label or title. */
        public FileName $originalName,
        /** Classifier string or type discriminator. */
        public MimeType $mimeType,
        /** Field `sizeInBytes` for this contract; see module docs for validation rules. */
        public int $sizeInBytes,
        /** Uploaded or temp file reference for storage pipelines. */
        public SplFileInfo $file,
    ) {}
}

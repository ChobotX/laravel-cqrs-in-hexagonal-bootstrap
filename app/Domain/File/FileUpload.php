<?php

declare(strict_types=1);

namespace App\Domain\File;

use SplFileInfo;

final readonly class FileUpload
{
    public function __construct(
        public FileName $originalName,
        public MimeType $mimeType,
        public int $sizeInBytes,
        public SplFileInfo $file,
    ) {}
}

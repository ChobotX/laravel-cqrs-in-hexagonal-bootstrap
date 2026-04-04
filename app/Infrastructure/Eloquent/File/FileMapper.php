<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\File;

use App\Domain\File\Contract\File;
use App\Domain\File\Contract\FileId;
use App\Domain\File\Contract\FileName;
use App\Domain\File\Contract\MimeType;
use App\Domain\File\FileNamespace;
use App\Domain\File\FileVersion;
use App\Domain\File\StoragePath;

final readonly class FileMapper
{
    public function toDomain(FileModel $fileModel): File
    {
        return new File(
            id: new FileId($fileModel->id),
            namespace: new FileNamespace($fileModel->namespace),
            originalName: new FileName($fileModel->original_name),
            storagePath: new StoragePath($fileModel->storage_path),
            mimeType: new MimeType($fileModel->mime_type),
            sizeInBytes: $fileModel->size_in_bytes,
            version: new FileVersion($fileModel->version_number),
            uploadedBy: $fileModel->uploaded_by,
            uploadedAt: $fileModel->uploaded_at,
        );
    }
}

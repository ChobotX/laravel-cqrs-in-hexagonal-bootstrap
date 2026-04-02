<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\File;

use App\Domain\File\Contract\FileId;
use App\Domain\File\Contract\FileRepository;
use App\Domain\File\File;
use App\Domain\File\FileName;
use App\Domain\File\FileNamespace;

final readonly class EloquentFileRepository implements FileRepository
{
    public function __construct(
        private FileMapper $fileMapper,
    ) {}

    public function findById(FileId $fileId): ?File
    {
        $model = FileModel::find($fileId->value);

        if ($model === null) {
            return null;
        }

        return $this->fileMapper->toDomain($model);
    }

    public function create(File $file): void
    {
        $fileModel = new FileModel;
        $fileModel->id = $file->id->value;
        $fileModel->namespace = $file->namespace->value;
        $fileModel->original_name = $file->originalName->value;
        $fileModel->storage_path = $file->storagePath->value;
        $fileModel->mime_type = $file->mimeType->value;
        $fileModel->size_in_bytes = $file->sizeInBytes;
        $fileModel->version_number = $file->version->value;
        $fileModel->uploaded_by = $file->uploadedBy;
        $fileModel->uploaded_at = $file->uploadedAt;
        $fileModel->save();
    }

    public function delete(FileId $fileId): void
    {
        $model = FileModel::find($fileId->value);

        if ($model instanceof FileModel) {
            $model->delete();
        }
    }

    /** @return list<File> */
    public function findVersions(FileNamespace $fileNamespace, FileName $fileName): array
    {
        return array_values(
            FileModel::where('namespace', $fileNamespace->value)
                ->where('original_name', $fileName->value)
                ->orderBy('version_number')
                ->get()
                ->map(fn (FileModel $fileModel): File => $this->fileMapper->toDomain($fileModel))
                ->all(),
        );
    }

    public function latestVersion(FileNamespace $fileNamespace, FileName $fileName): ?File
    {
        $model = FileModel::where('namespace', $fileNamespace->value)
            ->where('original_name', $fileName->value)
            ->orderByDesc('version_number')
            ->first();

        return $model instanceof FileModel ? $this->fileMapper->toDomain($model) : null;
    }

    public function nextVersionNumber(FileNamespace $fileNamespace, FileName $fileName): int
    {
        /** @var int|null $maxVersion */
        $maxVersion = FileModel::where('namespace', $fileNamespace->value)
            ->where('original_name', $fileName->value)
            ->max('version_number');

        return ($maxVersion ?? 0) + 1;
    }
}

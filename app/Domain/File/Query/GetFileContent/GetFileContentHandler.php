<?php

declare(strict_types=1);

namespace App\Domain\File\Query\GetFileContent;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\File\Contract\File;
use App\Domain\File\Contract\FileId;
use App\Domain\File\Contract\FileRepository;
use App\Domain\File\Contract\FileStorage;
use App\Domain\File\Exception\FileNotFoundException;

/** @implements QueryHandler<GetFileContentQuery, string> */
final readonly class GetFileContentHandler implements QueryHandler
{
    public function __construct(
        private FileRepository $fileRepository,
        private FileStorage $fileStorage,
    ) {}

    public function handle(Query $query): string
    {
        $file = $this->fileRepository->findById(new FileId($query->id));

        if (! $file instanceof File) {
            throw new FileNotFoundException($query->id);
        }

        return $this->fileStorage->retrieve($file->storagePath);
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\File\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\File\Contract\File;
use App\Domain\File\Contract\FileId;
use App\Domain\File\Contract\FileRepository;
use App\Domain\File\Contract\Query\GetFileByIdQuery;
use App\Domain\File\Exception\FileNotFoundException;

/** @implements QueryHandler<GetFileByIdQuery, File> */
final readonly class GetFileByIdHandler implements QueryHandler
{
    public function __construct(
        private FileRepository $fileRepository,
    ) {}

    public function handle(Query $query): File
    {
        $file = $this->fileRepository->findById(new FileId($query->id));

        if (! $file instanceof File) {
            throw new FileNotFoundException($query->id);
        }

        return $file;
    }
}

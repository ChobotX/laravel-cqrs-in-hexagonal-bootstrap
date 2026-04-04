<?php

declare(strict_types=1);

namespace App\Domain\File\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\File\Contract\File;
use App\Domain\File\Contract\FileName;
use App\Domain\File\Contract\FileRepository;
use App\Domain\File\Contract\Query\GetFileVersionsQuery;
use App\Domain\File\FileNamespace;

/** @implements QueryHandler<GetFileVersionsQuery, list<File>> */
final readonly class GetFileVersionsHandler implements QueryHandler
{
    public function __construct(
        private FileRepository $fileRepository,
    ) {}

    /** @return list<File> */
    public function handle(Query $query): array
    {
        return $this->fileRepository->findVersions(
            new FileNamespace($query->namespace),
            new FileName($query->originalName),
        );
    }
}

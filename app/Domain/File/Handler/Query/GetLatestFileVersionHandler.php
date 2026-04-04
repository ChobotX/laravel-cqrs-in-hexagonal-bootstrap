<?php

declare(strict_types=1);

namespace App\Domain\File\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\File\Contract\Entity\File;
use App\Domain\File\Contract\Query\GetLatestFileVersionQuery;
use App\Domain\File\Contract\Repository\FileRepository;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Exception\FileNotFoundException;
use App\Domain\File\ValueObject\FileNamespace;

/** @implements QueryHandler<GetLatestFileVersionQuery, File> */
final readonly class GetLatestFileVersionHandler implements QueryHandler
{
    public function __construct(
        private FileRepository $fileRepository,
    ) {}

    public function handle(Query $query): File
    {
        $file = $this->fileRepository->latestVersion(
            new FileNamespace($query->namespace),
            new FileName($query->originalName),
        );

        if (! $file instanceof File) {
            throw new FileNotFoundException(sprintf('%s/%s', $query->namespace, $query->originalName));
        }

        return $file;
    }
}

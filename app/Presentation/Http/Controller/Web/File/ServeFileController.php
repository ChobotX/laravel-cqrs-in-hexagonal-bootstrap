<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\File;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\File\Contract\FileStorage;
use App\Domain\File\File;
use App\Domain\File\Query\GetFileById\GetFileByIdQuery;
use Illuminate\Http\Response;

#[RequiresPermission('files.storage.read')]
final readonly class ServeFileController
{
    public function __construct(
        private QueryBus $queryBus,
        private FileStorage $fileStorage,
    ) {}

    public function __invoke(string $fileId): Response
    {
        /** @var File $file */
        $file = $this->queryBus->dispatch(new GetFileByIdQuery($fileId));

        $contents = $this->fileStorage->retrieve($file->storagePath);

        return new Response($contents, Response::HTTP_OK, [
            'Content-Type' => $file->mimeType->value,
            'Content-Disposition' => sprintf('inline; filename="%s"', $file->originalName->value),
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}

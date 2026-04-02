<?php

declare(strict_types=1);

namespace App\Domain\File\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class FileStorageException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $path)
    {
        parent::__construct(sprintf('File storage operation failed for path [%s].', $path));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.file_storage_error');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\File\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class FileNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $id)
    {
        parent::__construct(sprintf('File with id [%s] not found.', $id));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.file_not_found', ['id' => $this->id]);
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}

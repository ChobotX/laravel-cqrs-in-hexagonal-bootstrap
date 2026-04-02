<?php

declare(strict_types=1);

namespace App\Domain\File\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class ImageProcessingException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $detail)
    {
        parent::__construct(sprintf('Image processing failed: %s', $detail));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.image_processing_error');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}

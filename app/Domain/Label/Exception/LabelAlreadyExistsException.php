<?php

declare(strict_types=1);

namespace App\Domain\Label\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class LabelAlreadyExistsException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $namespace,
        public readonly string $name,
    ) {
        parent::__construct(sprintf('A label [%s] already exists in namespace [%s].', $name, $namespace));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.label_already_exists', ['name' => $this->name]);
    }

    public function statusCode(): int
    {
        return HttpStatus::CONFLICT;
    }
}

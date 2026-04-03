<?php

declare(strict_types=1);

namespace App\Domain\Registry\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class DefinitionAlreadyExistsException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $namespace,
        public readonly string $slug,
    ) {
        parent::__construct(sprintf('A definition [%s] already exists in namespace [%s].', $slug, $namespace));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.definition_already_exists', ['slug' => $this->slug]);
    }

    public function statusCode(): int
    {
        return HttpStatus::CONFLICT;
    }
}

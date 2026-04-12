<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

/**
 * Thrown when creating a label would duplicate an existing name within the same namespace.
 */
final class LabelAlreadyExistsException extends RuntimeException implements DomainException
{
    public function __construct(
        /** Label namespace (owns uniqueness of `name`). */
        public readonly string $namespace,
        /** Display name that already exists in this namespace. */
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

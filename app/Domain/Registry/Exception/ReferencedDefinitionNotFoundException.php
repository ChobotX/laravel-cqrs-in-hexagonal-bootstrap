<?php

declare(strict_types=1);

namespace App\Domain\Registry\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class ReferencedDefinitionNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $namespace,
        public readonly string $slug,
    ) {
        parent::__construct(sprintf('Referenced definition [%s/%s] not found.', $namespace, $slug));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.referenced_definition_not_found', [
            'namespace' => $this->namespace,
            'slug' => $this->slug,
        ]);
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}

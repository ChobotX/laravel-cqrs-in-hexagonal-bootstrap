<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class EmailTemplateNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $type,
        public readonly string $locale,
    ) {
        parent::__construct(sprintf('Email template [%s] for locale [%s] not found.', $type, $locale));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.email_template_not_found', [
            'type' => $this->type,
            'locale' => $this->locale,
        ]);
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}

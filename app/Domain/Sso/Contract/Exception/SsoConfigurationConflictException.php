<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

use function sprintf;

final class SsoConfigurationConflictException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $providerType,
        public readonly string $slug,
    ) {
        parent::__construct(sprintf('SSO configuration with provider [%s] and slug [%s] already exists.', $providerType, $slug));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.sso_configuration_conflict', [
            'providerType' => $this->providerType,
            'slug' => $this->slug,
        ]);
    }

    public function statusCode(): int
    {
        return HttpStatus::CONFLICT;
    }
}

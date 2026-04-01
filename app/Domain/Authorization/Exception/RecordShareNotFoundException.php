<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class RecordShareNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $granteeUserId,
        public readonly string $resourceType,
        public readonly string $resourceId,
    ) {
        parent::__construct(sprintf(
            'Record share for user [%s] on %s [%s] not found.',
            $granteeUserId,
            $resourceType,
            $resourceId,
        ));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.record_share_not_found');
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}

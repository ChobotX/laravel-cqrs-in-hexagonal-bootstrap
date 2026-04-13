<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent;
use App\Domain\EmailTemplate\Contract\Service\TemplatedEmailDispatcher;
use DateTimeImmutable;

final class FakeTemplatedEmailDispatcher implements TemplatedEmailDispatcher
{
    /** @var list<array{userId: string, templateType: string, locale: string, variables: array<string, string|null>}> */
    public array $dispatched = [];

    public function dispatch(string $userId, string $templateType, string $locale, array $variables): TemplatedEmailSent
    {
        $this->dispatched[] = [
            'userId' => $userId,
            'templateType' => $templateType,
            'locale' => $locale,
            'variables' => $variables,
        ];

        return new TemplatedEmailSent(
            emailLogId: 'fake-email-log-id',
            templateType: $templateType,
            locale: $locale,
            recipientId: $userId,
            recipientEmail: 'fake@example.com',
            renderedSubject: 'fake-subject',
            renderedBodyMasked: 'fake-body',
            variableKeys: array_keys($variables),
            traceId: null,
            occurredAt: new DateTimeImmutable,
        );
    }
}

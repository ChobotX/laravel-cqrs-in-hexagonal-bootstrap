<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\EmailTemplate\Contract\Service\TemplatedEmailDispatcher;

final class FakeTemplatedEmailDispatcher implements TemplatedEmailDispatcher
{
    /** @var list<array{userId: string, templateType: string, locale: string, variables: array<string, string|null>}> */
    public array $dispatched = [];

    public function dispatch(string $userId, string $templateType, string $locale, array $variables): void
    {
        $this->dispatched[] = [
            'userId' => $userId,
            'templateType' => $templateType,
            'locale' => $locale,
            'variables' => $variables,
        ];
    }
}

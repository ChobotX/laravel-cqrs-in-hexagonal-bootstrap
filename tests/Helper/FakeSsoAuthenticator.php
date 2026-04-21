<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Service\SsoAuthenticator;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;
use App\Domain\Sso\Contract\ValueObject\SsoIdentity;
use RuntimeException;

final class FakeSsoAuthenticator implements SsoAuthenticator
{
    /** @var list<array{configuration: SsoConfiguration, payload: array<string, scalar|array<int|string, mixed>|null>}> */
    public array $completed = [];

    public function __construct(
        public ?SsoIdentity $nextIdentity = null,
        public ?RedirectInstruction $nextRedirect = null,
        public ?SsoConnectionTestResult $nextProbe = null,
        public bool $throwOnComplete = false,
    ) {}

    public function initiate(SsoConfiguration $ssoConfiguration): RedirectInstruction
    {
        return $this->nextRedirect ?? new RedirectInstruction('https://idp.example.com/authorize');
    }

    public function complete(SsoConfiguration $ssoConfiguration, array $callbackPayload, ?string $expectedNonce = null): SsoIdentity
    {
        $this->completed[] = ['configuration' => $ssoConfiguration, 'payload' => $callbackPayload, 'expectedNonce' => $expectedNonce];

        if ($this->throwOnComplete) {
            throw new RuntimeException('Authenticator failure');
        }

        return $this->nextIdentity ?? new SsoIdentity('subject-1', 'user@example.com', 'User');
    }

    public function probe(SsoConfiguration $ssoConfiguration): SsoConnectionTestResult
    {
        return $this->nextProbe ?? new SsoConnectionTestResult(true, 'OK');
    }
}

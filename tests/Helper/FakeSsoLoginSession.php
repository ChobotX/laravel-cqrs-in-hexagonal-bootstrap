<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Sso\Contract\Service\SsoLoginSession;

final class FakeSsoLoginSession implements SsoLoginSession
{
    public ?string $lastUserId = null;

    /** @var array<string, array{state: string, nonce: ?string}> */
    public array $handshakes = [];

    public function rememberHandshake(string $slug, string $state, ?string $nonce = null): void
    {
        $this->handshakes[$slug] = ['state' => $state, 'nonce' => $nonce];
    }

    public function consumeHandshake(string $slug, string $state): ?string
    {
        $entry = $this->handshakes[$slug] ?? null;

        if ($entry === null || $entry['state'] !== $state) {
            return null;
        }

        unset($this->handshakes[$slug]);

        return $entry['nonce'];
    }

    public function setLastResolvedUserId(string $userId): void
    {
        $this->lastUserId = $userId;
    }

    public function pullLastResolvedUserId(): ?string
    {
        $value = $this->lastUserId;
        $this->lastUserId = null;

        return $value;
    }

    public function clear(): void
    {
        $this->lastUserId = null;
        $this->handshakes = [];
    }
}

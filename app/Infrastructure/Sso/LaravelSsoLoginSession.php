<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Service\SsoLoginSession;
use Illuminate\Contracts\Session\Session;

use function is_array;
use function is_string;

final readonly class LaravelSsoLoginSession implements SsoLoginSession
{
    private const string USER_KEY = 'sso.last_resolved_user_id';

    private const string HANDSHAKE_KEY = 'sso.handshake';

    public function __construct(
        private Session $session,
    ) {}

    public function rememberHandshake(string $slug, string $state, ?string $nonce = null): void
    {
        /** @var array<string, array{state: string, nonce: ?string}> $handshakes */
        $handshakes = (array) $this->session->get(self::HANDSHAKE_KEY, []);
        $handshakes[$slug] = ['state' => $state, 'nonce' => $nonce];
        $this->session->put(self::HANDSHAKE_KEY, $handshakes);
    }

    public function consumeHandshake(string $slug, string $state): ?string
    {
        /** @var array<string, array{state: string, nonce: ?string}> $handshakes */
        $handshakes = (array) $this->session->get(self::HANDSHAKE_KEY, []);
        $entry = $handshakes[$slug] ?? null;

        if (! is_array($entry) || $entry['state'] !== $state) {
            return null;
        }

        unset($handshakes[$slug]);
        $this->session->put(self::HANDSHAKE_KEY, $handshakes);

        $nonce = $entry['nonce'] ?? null;

        return is_string($nonce) ? $nonce : null;
    }

    public function setLastResolvedUserId(string $userId): void
    {
        $this->session->put(self::USER_KEY, $userId);
    }

    public function pullLastResolvedUserId(): ?string
    {
        $value = $this->session->pull(self::USER_KEY);

        return is_string($value) ? $value : null;
    }

    public function clear(): void
    {
        $this->session->forget([self::USER_KEY, self::HANDSHAKE_KEY]);
    }
}

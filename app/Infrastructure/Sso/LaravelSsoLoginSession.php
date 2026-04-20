<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Service\SsoLoginSession;
use Illuminate\Contracts\Session\Session;

final readonly class LaravelSsoLoginSession implements SsoLoginSession
{
    private const string SESSION_KEY = 'sso.last_resolved_user_id';

    public function __construct(
        private Session $session,
    ) {}

    public function setLastResolvedUserId(string $userId): void
    {
        $this->session->put(self::SESSION_KEY, $userId);
    }

    public function pullLastResolvedUserId(): ?string
    {
        $value = $this->session->pull(self::SESSION_KEY);

        return is_string($value) ? $value : null;
    }
}

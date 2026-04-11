<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\User\Contract\Service\InviteLinkGenerator;
use Illuminate\Routing\UrlGenerator;

final readonly class LaravelInviteLinkGenerator implements InviteLinkGenerator
{
    private const int INVITE_EXPIRY_HOURS = 72;

    public function __construct(
        private UrlGenerator $urlGenerator,
    ) {}

    public function generate(string $userId): string
    {
        $relativePath = $this->urlGenerator->temporarySignedRoute(
            'invite.accept',
            now()->addHours(self::INVITE_EXPIRY_HOURS),
            ['userId' => $userId],
            absolute: false,
        );

        return $this->urlGenerator->to($relativePath);
    }
}

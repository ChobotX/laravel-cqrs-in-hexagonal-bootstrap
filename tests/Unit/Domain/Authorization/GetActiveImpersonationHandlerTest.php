<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Query\GetActiveImpersonationQuery;
use App\Domain\Authorization\Contract\Service\ImpersonationManager;
use App\Domain\Authorization\Handler\Query\GetActiveImpersonationHandler;
use Tests\Helper\FakeImpersonationManager;

it('returns impersonation data when active', function (): void {
    $impersonationManager = new FakeImpersonationManager;
    $impersonationManager->start('00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000002');

    $handler = new GetActiveImpersonationHandler($impersonationManager);

    $result = $handler->handle(new GetActiveImpersonationQuery(
        impersonatorId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($result)->toBe([
        'impersonator_id' => '00000000-0000-0000-0000-000000000001',
        'impersonated_user_id' => '00000000-0000-0000-0000-000000000002',
    ]);
});

it('returns null when impersonation is not active', function (): void {
    $impersonationManager = new FakeImpersonationManager;

    $handler = new GetActiveImpersonationHandler($impersonationManager);

    $result = $handler->handle(new GetActiveImpersonationQuery(
        impersonatorId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($result)->toBeNull();
});

it('returns null when active but user ids are null', function (): void {
    $impersonationManager = new class implements ImpersonationManager
    {
        public function start(string $impersonatorId, string $targetUserId): void {}

        public function stop(): void {}

        public function isActive(): bool
        {
            return true;
        }

        public function realUserId(): ?string
        {
            return null;
        }

        public function impersonatedUserId(): ?string
        {
            return null;
        }
    };

    $handler = new GetActiveImpersonationHandler($impersonationManager);

    $result = $handler->handle(new GetActiveImpersonationQuery(
        impersonatorId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($result)->toBeNull();
});

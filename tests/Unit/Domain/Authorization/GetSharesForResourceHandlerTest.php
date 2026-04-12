<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Enum\Action;
use App\Domain\Authorization\Contract\Query\GetSharesForResourceQuery;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;
use App\Domain\Authorization\Handler\Query\GetSharesForResourceHandler;
use Tests\Helper\FakeRecordShareRepository;

it('returns shares for the given resource', function (): void {
    $repo = new FakeRecordShareRepository;
    $repo->shared[] = new RecordShare(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'entry',
        resourceId: '00000000-0000-0000-0000-000000000099',
        action: Action::Read,
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    );

    $handler = new GetSharesForResourceHandler($repo);

    $result = $handler->handle(new GetSharesForResourceQuery('entry', '00000000-0000-0000-0000-000000000099'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->granteeUserId)->toBe('00000000-0000-0000-0000-000000000010');
});

it('returns empty list when no shares exist for a resource', function (): void {
    $repo = new FakeRecordShareRepository;

    $handler = new GetSharesForResourceHandler($repo);

    $result = $handler->handle(new GetSharesForResourceQuery('entry', '00000000-0000-0000-0000-000000000099'));

    expect($result)->toBeEmpty();
});

it('does not return shares for other resources', function (): void {
    $repo = new FakeRecordShareRepository;
    $repo->shared[] = new RecordShare(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'entry',
        resourceId: '00000000-0000-0000-0000-000000000088',
        action: Action::Read,
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    );

    $handler = new GetSharesForResourceHandler($repo);

    $result = $handler->handle(new GetSharesForResourceQuery('entry', '00000000-0000-0000-0000-000000000099'));

    expect($result)->toBeEmpty();
});

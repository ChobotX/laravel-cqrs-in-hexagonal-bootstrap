<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Query\GetRecordSharesQuery;
use App\Domain\Authorization\Enum\Action;
use App\Domain\Authorization\Handler\Query\GetRecordSharesHandler;
use App\Domain\Authorization\ValueObject\RecordShare;
use Tests\Helper\FakeRecordShareRepository;

it('returns record shares for a user', function (): void {
    $recordShare = new RecordShare(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
        action: Action::Read,
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    );

    $recordShareRepo = new FakeRecordShareRepository;
    $recordShareRepo->shared[] = $recordShare;

    $handler = new GetRecordSharesHandler($recordShareRepo);

    $result = $handler->handle(new GetRecordSharesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(1)
        ->and($result[0]->resourceType)->toBe('contact');
});

it('filters by resource type when provided', function (): void {
    $contactShare = new RecordShare(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
        action: Action::Read,
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    );

    $dealShare = new RecordShare(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'deal',
        resourceId: '00000000-0000-0000-0000-000000000088',
        action: Action::Read,
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    );

    $recordShareRepo = new FakeRecordShareRepository;
    $recordShareRepo->shared[] = $contactShare;
    $recordShareRepo->shared[] = $dealShare;

    $handler = new GetRecordSharesHandler($recordShareRepo);

    $result = $handler->handle(new GetRecordSharesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
    ));

    expect($result)->toHaveCount(1)
        ->and($result[0]->resourceType)->toBe('contact');
});

it('returns empty list when no shares exist', function (): void {
    $recordShareRepo = new FakeRecordShareRepository;

    $handler = new GetRecordSharesHandler($recordShareRepo);

    $result = $handler->handle(new GetRecordSharesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(0);
});

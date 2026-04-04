<?php

declare(strict_types=1);

use App\Domain\Authorization\Enum\Action;
use App\Domain\Authorization\ValueObject\RecordShare;

it('can be constructed with all properties', function (): void {
    $recordShare = new RecordShare(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
        action: Action::Read,
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    );

    expect($recordShare->granteeUserId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($recordShare->resourceType)->toBe('contact')
        ->and($recordShare->resourceId)->toBe('00000000-0000-0000-0000-000000000099')
        ->and($recordShare->action)->toBe(Action::Read)
        ->and($recordShare->grantorUserId)->toBe('00000000-0000-0000-0000-000000000001');
});

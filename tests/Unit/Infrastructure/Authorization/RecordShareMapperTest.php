<?php

declare(strict_types=1);

use App\Domain\Authorization\Action;
use App\Infrastructure\Eloquent\Authorization\RecordShareMapper;
use App\Infrastructure\Eloquent\Authorization\RecordShareModel;

it('maps model to domain entity', function (): void {
    $model = new RecordShareModel;
    $model->grantee_user_id = 'user-1';
    $model->resource_type = 'document';
    $model->resource_id = 'doc-1';
    $model->action = 'read';
    $model->grantor_user_id = 'user-2';
    $model->organization_id = 'org-1';

    $mapper = new RecordShareMapper;
    $recordShare = $mapper->toDomain($model);

    expect($recordShare->granteeUserId)->toBe('user-1');
    expect($recordShare->resourceType)->toBe('document');
    expect($recordShare->resourceId)->toBe('doc-1');
    expect($recordShare->action)->toBe(Action::Read);
    expect($recordShare->grantorUserId)->toBe('user-2');
    expect($recordShare->organizationId)->toBe('org-1');
});

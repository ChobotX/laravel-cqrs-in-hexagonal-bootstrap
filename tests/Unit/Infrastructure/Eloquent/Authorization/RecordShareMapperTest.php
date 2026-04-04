<?php

declare(strict_types=1);

use App\Domain\Authorization\Enum\Action;
use App\Infrastructure\Eloquent\Authorization\RecordShareMapper;
use App\Infrastructure\Eloquent\Authorization\RecordShareModel;

it('maps a record share model to domain', function (): void {
    $model = new RecordShareModel;
    $model->grantee_user_id = 'grantee-1';
    $model->resource_type = 'document';
    $model->resource_id = 'doc-1';
    $model->action = 'read';
    $model->grantor_user_id = 'grantor-1';

    $mapper = new RecordShareMapper;
    $recordShare = $mapper->toDomain($model);

    expect($recordShare->granteeUserId)->toBe('grantee-1')
        ->and($recordShare->resourceType)->toBe('document')
        ->and($recordShare->resourceId)->toBe('doc-1')
        ->and($recordShare->action)->toBe(Action::Read)
        ->and($recordShare->grantorUserId)->toBe('grantor-1');
});

it('maps different action types', function (string $actionValue, Action $expectedAction): void {
    $model = new RecordShareModel;
    $model->grantee_user_id = 'grantee-1';
    $model->resource_type = 'document';
    $model->resource_id = 'doc-1';
    $model->action = $actionValue;
    $model->grantor_user_id = 'grantor-1';

    $mapper = new RecordShareMapper;
    $recordShare = $mapper->toDomain($model);

    expect($recordShare->action)->toBe($expectedAction);
})->with([
    ['read', Action::Read],
    ['create', Action::Create],
    ['update', Action::Update],
    ['delete', Action::Delete],
]);

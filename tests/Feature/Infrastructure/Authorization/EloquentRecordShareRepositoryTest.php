<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Enum\Action;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;
use App\Infrastructure\Eloquent\Authorization\EloquentRecordShareRepository;
use App\Infrastructure\Eloquent\Authorization\RecordShareMapper;
use App\Infrastructure\Eloquent\User\UserModel;

function shareRepo(): EloquentRecordShareRepository
{
    return new EloquentRecordShareRepository(new RecordShareMapper);
}

function createShareUser(string $id): UserModel
{
    return UserModel::create(['id' => $id, 'name' => 'Test', 'email' => $id.'@share.com']);
}

it('shares and finds by grantee', function (): void {
    $eloquentRecordShareRepository = shareRepo();
    $userModel = createShareUser('550e8400-e29b-41d4-a716-446655440600');
    $grantor = createShareUser('550e8400-e29b-41d4-a716-446655440601');

    $share = new RecordShare(
        granteeUserId: $userModel->id,
        resourceType: 'document',
        resourceId: '550e8400-e29b-41d4-a716-446655440602',
        action: Action::Read,
        grantorUserId: $grantor->id,
    );

    $eloquentRecordShareRepository->share($share);
    $shares = $eloquentRecordShareRepository->findByGrantee($userModel->id);

    expect($shares)->toHaveCount(1);
    expect($shares[0]->resourceType)->toBe('document');
});

it('filters by resource type', function (): void {
    $eloquentRecordShareRepository = shareRepo();
    $userModel = createShareUser('550e8400-e29b-41d4-a716-446655440603');
    $grantor = createShareUser('550e8400-e29b-41d4-a716-446655440604');

    $eloquentRecordShareRepository->share(new RecordShare($userModel->id, 'document', '550e8400-e29b-41d4-a716-446655440605', Action::Read, $grantor->id));
    $eloquentRecordShareRepository->share(new RecordShare($userModel->id, 'image', '550e8400-e29b-41d4-a716-446655440606', Action::Read, $grantor->id));

    $docs = $eloquentRecordShareRepository->findByGrantee($userModel->id, 'document');

    expect($docs)->toHaveCount(1);
});

it('revokes a share', function (): void {
    $eloquentRecordShareRepository = shareRepo();
    $userModel = createShareUser('550e8400-e29b-41d4-a716-446655440607');
    $grantor = createShareUser('550e8400-e29b-41d4-a716-446655440608');

    $eloquentRecordShareRepository->share(new RecordShare($userModel->id, 'document', '550e8400-e29b-41d4-a716-446655440609', Action::Read, $grantor->id));
    $eloquentRecordShareRepository->revoke($userModel->id, 'document', '550e8400-e29b-41d4-a716-446655440609');

    expect($eloquentRecordShareRepository->findByGrantee($userModel->id))->toHaveCount(0);
});

it('checks if a share exists', function (): void {
    $eloquentRecordShareRepository = shareRepo();
    $userModel = createShareUser('550e8400-e29b-41d4-a716-446655440614');
    $grantor = createShareUser('550e8400-e29b-41d4-a716-446655440615');

    $eloquentRecordShareRepository->share(new RecordShare($userModel->id, 'document', '550e8400-e29b-41d4-a716-446655440616', Action::Read, $grantor->id));

    expect($eloquentRecordShareRepository->exists($userModel->id, 'document', '550e8400-e29b-41d4-a716-446655440616'))->toBeTrue()
        ->and($eloquentRecordShareRepository->exists($userModel->id, 'document', '550e8400-e29b-41d4-a716-446655440617'))->toBeFalse();
});

it('returns accessible resource ids', function (): void {
    $eloquentRecordShareRepository = shareRepo();
    $userModel = createShareUser('550e8400-e29b-41d4-a716-446655440610');
    $grantor = createShareUser('550e8400-e29b-41d4-a716-446655440611');

    $eloquentRecordShareRepository->share(new RecordShare($userModel->id, 'document', '550e8400-e29b-41d4-a716-446655440612', Action::Read, $grantor->id));
    $eloquentRecordShareRepository->share(new RecordShare($userModel->id, 'document', '550e8400-e29b-41d4-a716-446655440613', Action::Read, $grantor->id));

    $ids = $eloquentRecordShareRepository->accessibleResourceIds($userModel->id, 'document', Action::Read);

    expect($ids)->toHaveCount(2);
});

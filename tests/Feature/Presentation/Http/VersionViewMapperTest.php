<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\ValueObject\VersionNumber;
use App\Presentation\Http\Service\VersionViewMapper;

function vvmVersion(string $id, int $version, VersionStatus $versionStatus, int $fieldCount = 1): DefinitionVersion
{
    $fields = array_map(
        fn (int $i): StringField => new StringField('field_'.$i, 'Field '.$i),
        range(1, $fieldCount),
    );

    return new DefinitionVersion(
        new DefinitionVersionId($id),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber($version),
        new Schema($fields),
        $versionStatus,
    );
}

it('maps active version', function (): void {
    $versions = [vvmVersion('660e8400-e29b-41d4-a716-446655440001', 1, VersionStatus::Active, 3)];

    $result = VersionViewMapper::mapForView($versions, 'enumerations', 'brands');

    expect($result)->toHaveCount(1)
        ->and($result[0]['version'])->toBe(1)
        ->and($result[0]['fieldCount'])->toBe(3)
        ->and($result[0]['isDraft'])->toBeFalse()
        ->and($result[0]['activateRoute'])->toBeNull()
        ->and($result[0]['statusClasses'])->toContain('green');
});

it('maps draft version with activate route', function (): void {
    $versions = [vvmVersion('660e8400-e29b-41d4-a716-446655440002', 2, VersionStatus::Draft)];

    $result = VersionViewMapper::mapForView($versions, 'enumerations', 'brands');

    expect($result[0]['isDraft'])->toBeTrue()
        ->and($result[0]['activateRoute'])->toContain('versions/2/activate')
        ->and($result[0]['statusClasses'])->toContain('blue');
});

it('maps deprecated version', function (): void {
    $versions = [vvmVersion('660e8400-e29b-41d4-a716-446655440003', 3, VersionStatus::Deprecated)];

    $result = VersionViewMapper::mapForView($versions, 'enumerations', 'brands');

    expect($result[0]['isDraft'])->toBeFalse()
        ->and($result[0]['activateRoute'])->toBeNull()
        ->and($result[0]['statusClasses'])->toContain('gray');
});

it('maps multiple versions', function (): void {
    $versions = [
        vvmVersion('660e8400-e29b-41d4-a716-446655440001', 1, VersionStatus::Active, 5),
        vvmVersion('660e8400-e29b-41d4-a716-446655440002', 2, VersionStatus::Draft, 2),
        vvmVersion('660e8400-e29b-41d4-a716-446655440003', 3, VersionStatus::Deprecated, 3),
    ];

    $result = VersionViewMapper::mapForView($versions, 'enumerations', 'brands');

    expect($result)->toHaveCount(3)
        ->and($result[0]['version'])->toBe(1)
        ->and($result[1]['version'])->toBe(2)
        ->and($result[2]['version'])->toBe(3);
});

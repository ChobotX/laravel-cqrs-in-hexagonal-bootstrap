<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMapper;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;

it('maps an organization model to domain', function (): void {
    $model = new OrganizationModel;
    $model->id = '550e8400-e29b-41d4-a716-446655440000';
    $model->name = 'Acme Corp';
    $model->slug = 'acme-corp';
    $model->description = 'A test organization';

    $mapper = new OrganizationMapper;
    $organization = $mapper->toDomain($model);

    expect($organization->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($organization->name->value)->toBe('Acme Corp')
        ->and($organization->slug->value)->toBe('acme-corp')
        ->and($organization->description)->toBe('A test organization');
});

it('maps organization with empty description', function (): void {
    $model = new OrganizationModel;
    $model->id = '550e8400-e29b-41d4-a716-446655440001';
    $model->name = 'Beta Inc';
    $model->slug = 'beta-inc';
    $model->description = '';

    $mapper = new OrganizationMapper;
    $organization = $mapper->toDomain($model);

    expect($organization->description)->toBe('');
});

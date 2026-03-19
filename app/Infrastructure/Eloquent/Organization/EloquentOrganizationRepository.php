<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationRepository;
use App\Domain\Organization\OrganizationSlug;
use Illuminate\Support\Facades\DB;

final readonly class EloquentOrganizationRepository implements OrganizationRepository
{
    public function __construct(
        private OrganizationMapper $organizationMapper,
    ) {}

    /** @return list<Organization> */
    public function findAll(): array
    {
        $models = OrganizationModel::all();

        return array_values(
            $models->map(fn (OrganizationModel $organizationModel): Organization => $this->organizationMapper->toDomain($organizationModel))->all(),
        );
    }

    public function findById(OrganizationId $organizationId): ?Organization
    {
        $model = OrganizationModel::find($organizationId->value);

        if ($model === null) {
            return null;
        }

        return $this->organizationMapper->toDomain($model);
    }

    public function findBySlug(OrganizationSlug $organizationSlug): ?Organization
    {
        $model = OrganizationModel::where('slug', $organizationSlug->value)->first();

        if (! $model instanceof OrganizationModel) {
            return null;
        }

        return $this->organizationMapper->toDomain($model);
    }

    /** @return list<Organization> */
    public function findByIds(array $ids): array
    {
        $models = OrganizationModel::whereIn('id', $ids)->get();

        return array_values(
            $models->map(fn (OrganizationModel $organizationModel): Organization => $this->organizationMapper->toDomain($organizationModel))->all(),
        );
    }

    public function create(Organization $organization): void
    {
        DB::transaction(function () use ($organization): void {
            $organizationModel = new OrganizationModel;
            $organizationModel->id = $organization->id->value;
            $organizationModel->name = $organization->name->value;
            $organizationModel->slug = $organization->slug->value;
            $organizationModel->description = $organization->description;
            $organizationModel->save();
        });
    }

    public function update(Organization $organization): void
    {
        DB::transaction(function () use ($organization): void {
            $model = OrganizationModel::findOrFail($organization->id->value);
            $model->name = $organization->name->value;
            $model->slug = $organization->slug->value;
            $model->description = $organization->description;
            $model->save();
        });
    }

    public function delete(OrganizationId $organizationId): void
    {
        $model = OrganizationModel::find($organizationId->value);

        if ($model instanceof OrganizationModel) {
            $model->delete();
        }
    }
}

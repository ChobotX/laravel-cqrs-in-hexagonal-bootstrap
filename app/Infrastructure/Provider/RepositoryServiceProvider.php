<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Contract\IdGenerator;
use App\Domain\Authorization\RecordShareRepository;
use App\Domain\Authorization\RoleRepository;
use App\Domain\Authorization\UserPermissionRepository;
use App\Domain\Organization\OrganizationMemberRepository;
use App\Domain\Organization\OrganizationRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentRecordShareRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentRoleRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentUserPermissionRepository;
use App\Infrastructure\Eloquent\Organization\EloquentOrganizationMemberRepository;
use App\Infrastructure\Eloquent\Organization\EloquentOrganizationRepository;
use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\UuidIdGenerator;
use Illuminate\Support\ServiceProvider;
use Override;

final class RepositoryServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(RoleRepository::class, EloquentRoleRepository::class);
        $this->app->bind(UserPermissionRepository::class, EloquentUserPermissionRepository::class);
        $this->app->bind(RecordShareRepository::class, EloquentRecordShareRepository::class);
        $this->app->bind(OrganizationRepository::class, EloquentOrganizationRepository::class);
        $this->app->bind(OrganizationMemberRepository::class, EloquentOrganizationMemberRepository::class);
        $this->app->bind(IdGenerator::class, UuidIdGenerator::class);
    }
}

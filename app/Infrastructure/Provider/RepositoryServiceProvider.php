<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Contract\IdGenerator;
use App\Contract\Notification\NotificationChannelSenderRegistry;
use App\Contract\Notification\RecipientResolver;
use App\Domain\Authorization\RecordShareRepository;
use App\Domain\Authorization\RoleRepository;
use App\Domain\Authorization\UserPermissionRepository;
use App\Domain\Label\LabelRepository;
use App\Domain\Notification\NotificationPreferenceRepository;
use App\Domain\Notification\NotificationRepository;
use App\Domain\Team\TeamMemberRepository;
use App\Domain\Team\TeamRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentRecordShareRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentRoleRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentUserPermissionRepository;
use App\Infrastructure\Eloquent\Label\EloquentLabelRepository;
use App\Infrastructure\Eloquent\Notification\EloquentNotificationPreferenceRepository;
use App\Infrastructure\Eloquent\Notification\EloquentNotificationRepository;
use App\Infrastructure\Eloquent\Team\EloquentTeamMemberRepository;
use App\Infrastructure\Eloquent\Team\EloquentTeamRepository;
use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\Notification\ChannelSenderRegistry;
use App\Infrastructure\Notification\EloquentRecipientResolver;
use App\Infrastructure\Notification\EmailNotificationSender;
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
        $this->app->bind(TeamRepository::class, EloquentTeamRepository::class);
        $this->app->bind(TeamMemberRepository::class, EloquentTeamMemberRepository::class);
        $this->app->bind(LabelRepository::class, EloquentLabelRepository::class);
        $this->app->bind(NotificationRepository::class, EloquentNotificationRepository::class);
        $this->app->bind(NotificationPreferenceRepository::class, EloquentNotificationPreferenceRepository::class);
        $this->app->bind(RecipientResolver::class, EloquentRecipientResolver::class);
        $this->app->bind(NotificationChannelSenderRegistry::class, fn (): ChannelSenderRegistry => new ChannelSenderRegistry([
            'email' => $this->app->make(EmailNotificationSender::class),
        ]));
        $this->app->bind(IdGenerator::class, UuidIdGenerator::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Contract\IdGenerator;
use App\Contract\Notification\NotificationBroadcaster;
use App\Contract\Notification\NotificationChannelSenderRegistry;
use App\Contract\Notification\RecipientResolver;
use App\Domain\Authorization\Contract\RecordShareRepository;
use App\Domain\Authorization\Contract\RoleRepository;
use App\Domain\Authorization\Contract\UserPermissionRepository;
use App\Domain\File\Contract\FileRepository;
use App\Domain\File\Contract\FileStorage;
use App\Domain\Label\Contract\LabelRepository;
use App\Domain\Notification\Contract\NotificationPreferenceRepository;
use App\Domain\Notification\Contract\NotificationRepository;
use App\Domain\Team\Contract\TeamMemberRepository;
use App\Domain\Team\Contract\TeamRepository;
use App\Domain\User\Contract\UserRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentRecordShareRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentRoleRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentUserPermissionRepository;
use App\Infrastructure\Eloquent\File\EloquentFileRepository;
use App\Infrastructure\Eloquent\Label\EloquentLabelRepository;
use App\Infrastructure\Eloquent\Notification\EloquentNotificationPreferenceRepository;
use App\Infrastructure\Eloquent\Notification\EloquentNotificationRepository;
use App\Infrastructure\Eloquent\Team\EloquentTeamMemberRepository;
use App\Infrastructure\Eloquent\Team\EloquentTeamRepository;
use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\Filesystem\LaravelFileStorage;
use App\Infrastructure\Notification\ChannelSenderRegistry;
use App\Infrastructure\Notification\EloquentRecipientResolver;
use App\Infrastructure\Notification\EmailNotificationSender;
use App\Infrastructure\Notification\LaravelNotificationBroadcaster;
use App\Infrastructure\UuidIdGenerator;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
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
        $this->app->bind(NotificationBroadcaster::class, LaravelNotificationBroadcaster::class);
        $this->app->bind(FileRepository::class, EloquentFileRepository::class);
        $this->app->bind(FileStorage::class, fn (): LaravelFileStorage => new LaravelFileStorage(
            $this->app->make(FilesystemFactory::class)->disk('files'),
        ));
        $this->app->bind(IdGenerator::class, UuidIdGenerator::class);
    }
}

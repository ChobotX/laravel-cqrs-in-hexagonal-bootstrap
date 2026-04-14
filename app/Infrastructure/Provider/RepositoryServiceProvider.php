<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Contract\IdGenerator;
use App\Domain\AuditLog\Contract\Repository\AuditLogRepository;
use App\Domain\AuditLog\Contract\Repository\AuditLogWriter;
use App\Domain\Authorization\Contract\Repository\RecordShareRepository;
use App\Domain\Authorization\Contract\Repository\RoleRepository;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\EmailTemplate\Contract\Repository\EmailLogRepository;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;
use App\Domain\EmailTemplate\Contract\Service\DefaultEmailTemplateSeedData;
use App\Domain\EmailTemplate\Contract\Service\EmailSender;
use App\Domain\EmailTemplate\Contract\Service\TemplateCompiler;
use App\Domain\EmailTemplate\Contract\Service\TemplatedEmailDispatcher;
use App\Domain\EmailTemplate\Service\DefaultEmailTemplateSeedDataFromConstant;
use App\Domain\EmailTemplate\Service\DefaultTemplatedEmailDispatcher;
use App\Domain\File\Contract\Repository\FileRepository;
use App\Domain\File\Contract\Service\FileStorage;
use App\Domain\File\Contract\Service\ImageProcessor;
use App\Domain\GridPreset\Contract\Repository\GridPresetRepository;
use App\Domain\Label\Contract\Repository\LabelRepository;
use App\Domain\Notification\Contract\Repository\NotificationPreferenceRepository;
use App\Domain\Notification\Contract\Repository\NotificationRepository;
use App\Domain\Notification\Contract\Service\NotificationBroadcaster;
use App\Domain\Notification\Contract\Service\NotificationChannelSenderRegistry;
use App\Domain\Notification\Contract\Service\RecipientResolver;
use App\Domain\Notification\Service\DefaultRecipientResolver;
use App\Domain\Registry\Contract\Repository\DefinitionRepository;
use App\Domain\Registry\Contract\Repository\DefinitionVersionRepository;
use App\Domain\Registry\Contract\Repository\EntryRepository;
use App\Domain\Registry\Contract\Service\JsonSchemaValidator;
use App\Domain\Registry\Contract\Service\SchemaSerializer;
use App\Domain\Team\Contract\Repository\TeamMemberRepository;
use App\Domain\Team\Contract\Repository\TeamRepository;
use App\Domain\User\Contract\Repository\PasswordHistoryRepository;
use App\Domain\User\Contract\Repository\PasswordRotationSettingsRepository;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Infrastructure\Eloquent\AuditLog\EloquentAuditLogRepository;
use App\Infrastructure\Eloquent\AuditLog\EloquentAuditLogWriter;
use App\Infrastructure\Eloquent\Authorization\EloquentRecordShareRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentRoleRepository;
use App\Infrastructure\Eloquent\Authorization\EloquentUserPermissionRepository;
use App\Infrastructure\Eloquent\EmailTemplate\EloquentEmailLogRepository;
use App\Infrastructure\Eloquent\EmailTemplate\EloquentEmailTemplateRepository;
use App\Infrastructure\Eloquent\File\EloquentFileRepository;
use App\Infrastructure\Eloquent\GridPreset\EloquentGridPresetRepository;
use App\Infrastructure\Eloquent\Label\EloquentLabelRepository;
use App\Infrastructure\Eloquent\Notification\EloquentNotificationPreferenceRepository;
use App\Infrastructure\Eloquent\Notification\EloquentNotificationRepository;
use App\Infrastructure\Eloquent\Registry\EloquentDefinitionRepository;
use App\Infrastructure\Eloquent\Registry\EloquentDefinitionVersionRepository;
use App\Infrastructure\Eloquent\Registry\EloquentEntryRepository;
use App\Infrastructure\Eloquent\Team\EloquentTeamMemberRepository;
use App\Infrastructure\Eloquent\Team\EloquentTeamRepository;
use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\EmailTemplate\BladeTemplateCompiler;
use App\Infrastructure\EmailTemplate\LaravelEmailSender;
use App\Infrastructure\File\GdImageProcessor;
use App\Infrastructure\File\LaravelFileStorage;
use App\Infrastructure\Notification\ChannelSenderRegistry;
use App\Infrastructure\Notification\EmailNotificationSender;
use App\Infrastructure\Notification\LaravelNotificationBroadcaster;
use App\Infrastructure\Registry\JsonSchemaSerializerImpl;
use App\Infrastructure\Registry\OpisJsonSchemaValidator;
use App\Infrastructure\User\EloquentPasswordHistoryRepository;
use App\Infrastructure\User\EloquentPasswordRotationSettingsRepository;
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
        $this->app->bind(PasswordRotationSettingsRepository::class, EloquentPasswordRotationSettingsRepository::class);
        $this->app->bind(PasswordHistoryRepository::class, EloquentPasswordHistoryRepository::class);
        $this->app->bind(RoleRepository::class, EloquentRoleRepository::class);
        $this->app->bind(UserPermissionRepository::class, EloquentUserPermissionRepository::class);
        $this->app->bind(RecordShareRepository::class, EloquentRecordShareRepository::class);
        $this->app->bind(TeamRepository::class, EloquentTeamRepository::class);
        $this->app->bind(TeamMemberRepository::class, EloquentTeamMemberRepository::class);
        $this->app->bind(LabelRepository::class, EloquentLabelRepository::class);
        $this->app->bind(NotificationRepository::class, EloquentNotificationRepository::class);
        $this->app->bind(NotificationPreferenceRepository::class, EloquentNotificationPreferenceRepository::class);
        $this->app->bind(RecipientResolver::class, DefaultRecipientResolver::class);
        $this->app->bind(NotificationChannelSenderRegistry::class, fn (): ChannelSenderRegistry => new ChannelSenderRegistry([
            'email' => $this->app->make(EmailNotificationSender::class),
        ]));
        $this->app->bind(NotificationBroadcaster::class, LaravelNotificationBroadcaster::class);
        $this->app->bind(GridPresetRepository::class, EloquentGridPresetRepository::class);
        $this->app->bind(FileRepository::class, EloquentFileRepository::class);
        $this->app->bind(FileStorage::class, fn (): LaravelFileStorage => new LaravelFileStorage(
            $this->app->make(FilesystemFactory::class)->disk('files'),
        ));
        $this->app->bind(ImageProcessor::class, GdImageProcessor::class);
        $this->app->bind(AuditLogRepository::class, EloquentAuditLogRepository::class);
        $this->app->bind(AuditLogWriter::class, EloquentAuditLogWriter::class);
        $this->app->bind(IdGenerator::class, UuidIdGenerator::class);
        $this->app->bind(DefinitionRepository::class, EloquentDefinitionRepository::class);
        $this->app->bind(DefinitionVersionRepository::class, EloquentDefinitionVersionRepository::class);
        $this->app->bind(EntryRepository::class, EloquentEntryRepository::class);
        $this->app->bind(JsonSchemaValidator::class, OpisJsonSchemaValidator::class);
        $this->app->bind(SchemaSerializer::class, JsonSchemaSerializerImpl::class);
        $this->app->bind(EmailTemplateRepository::class, EloquentEmailTemplateRepository::class);
        $this->app->bind(EmailLogRepository::class, EloquentEmailLogRepository::class);
        $this->app->bind(TemplateCompiler::class, BladeTemplateCompiler::class);
        $this->app->bind(EmailSender::class, LaravelEmailSender::class);
        $this->app->bind(TemplatedEmailDispatcher::class, DefaultTemplatedEmailDispatcher::class);
        $this->app->bind(DefaultEmailTemplateSeedData::class, DefaultEmailTemplateSeedDataFromConstant::class);
    }
}

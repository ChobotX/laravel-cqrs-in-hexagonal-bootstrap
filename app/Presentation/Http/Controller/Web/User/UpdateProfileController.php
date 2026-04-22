<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\IdGenerator;
use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\Notification\Contract\Command\UpdateNotificationPreferencesCommand;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\User\Contract\Command\UpdateProfileCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetOwnProfileQuery;
use App\Presentation\Http\Request\Web\User\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

#[SkipPermissionCheck(reason: 'Profile update is available to all authenticated users')]
final readonly class UpdateProfileController
{
    private const string AVATAR_NAMESPACE = 'user-avatars';

    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(UpdateProfileRequest $updateProfileRequest): RedirectResponse
    {
        $userId = $this->authenticatedUser->id() ?? '';

        $password = $updateProfileRequest->string('password')->toString();
        $avatarFileId = $this->resolveAvatarFileId($updateProfileRequest, $userId);

        $this->commandBus->dispatch(new UpdateProfileCommand(
            userId: $userId,
            name: $updateProfileRequest->string('name')->toString(),
            email: $updateProfileRequest->has('email') ? $updateProfileRequest->string('email')->toString() : null,
            rawPassword: $password !== '' ? $password : null,
            avatarFileId: $avatarFileId,
        ));

        $this->syncNotificationPreferences($updateProfileRequest, $userId);

        return redirect()->route('profile')->with('success', __('messages.profile.updated'));
    }

    private function resolveAvatarFileId(UpdateProfileRequest $updateProfileRequest, string $userId): ?string
    {
        if ($updateProfileRequest->boolean('remove_avatar')) {
            return null;
        }

        $file = $updateProfileRequest->file('avatar');

        if ($file instanceof UploadedFile) {
            $fileId = $this->idGenerator->generate();

            $this->commandBus->dispatch(new StoreAvatarCommand(
                id: $fileId,
                namespace: self::AVATAR_NAMESPACE,
                uploadedBy: $userId,
                upload: new FileUpload(
                    originalName: new FileName($file->getClientOriginalName()),
                    mimeType: new MimeType($file->getMimeType() ?? 'image/jpeg'),
                    sizeInBytes: (int) $file->getSize(),
                    file: $file,
                ),
            ));

            return $fileId;
        }

        /** @var User $currentUser */
        $currentUser = $this->queryBus->dispatch(new GetOwnProfileQuery($userId));

        return $currentUser->avatarFileId?->value;
    }

    private function syncNotificationPreferences(UpdateProfileRequest $updateProfileRequest, string $userId): void
    {
        /** @var array<string, array<string, string>>|null $submitted */
        $submitted = $updateProfileRequest->input('notification_preferences');

        if ($submitted === null) {
            return;
        }

        $preferences = [];

        foreach ($submitted as $level => $channels) {
            $channelList = [];

            if (isset($channels['email']) && (bool) $channels['email']) {
                $channelList[] = NotificationChannel::Email->value;
            }

            $preferences[$level] = $channelList;
        }

        $this->commandBus->dispatch(new UpdateNotificationPreferencesCommand(
            userId: $userId,
            preferences: $preferences,
        ));
    }
}

<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Domain\Authorization\Command\SyncUserRoles\SyncUserRolesCommand;
use App\Domain\Notification\Command\UpdateNotificationPreferences\UpdateNotificationPreferencesCommand;
use App\Domain\Notification\Contract\NotificationChannel;
use App\Domain\Team\Command\SyncUserTeams\SyncUserTeamsCommand;
use App\Domain\User\Command\UpdateProfile\UpdateProfileCommand;
use App\Presentation\Http\Request\Web\User\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck(reason: 'Profile update is available to all authenticated users')]
final readonly class UpdateProfileController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(UpdateProfileRequest $updateProfileRequest): RedirectResponse
    {
        $userId = $this->authenticatedUser->id() ?? '';

        $password = $updateProfileRequest->string('password')->toString();

        $this->commandBus->dispatch(new UpdateProfileCommand(
            userId: $userId,
            name: $updateProfileRequest->string('name')->toString(),
            email: $updateProfileRequest->has('email') ? $updateProfileRequest->string('email')->toString() : null,
            rawPassword: $password !== '' ? $password : null,
        ));

        /** @var list<string>|null $submittedRoleIds */
        $submittedRoleIds = $updateProfileRequest->has('roles') ? $updateProfileRequest->input('roles', []) : null;
        $this->commandBus->dispatch(new SyncUserRolesCommand($userId, $submittedRoleIds, $userId));

        /** @var list<string>|null $submittedTeamIds */
        $submittedTeamIds = $updateProfileRequest->has('teams') ? $updateProfileRequest->input('teams', []) : null;
        $this->commandBus->dispatch(new SyncUserTeamsCommand($userId, $submittedTeamIds, $userId));

        $this->syncNotificationPreferences($updateProfileRequest, $userId);

        return redirect()->route('profile')->with('success', __('messages.profile.updated'));
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

<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\User;

use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\User\Contract\Command\UpdateProfileWithAvatarAndPreferencesCommand;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class UpdateProfileRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string|\Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\Unique>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::id())],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:10240'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ];
    }

    public function toCommand(string $userId): UpdateProfileWithAvatarAndPreferencesCommand
    {
        $file = $this->file('avatar');
        $avatarUpload = $file instanceof UploadedFile
            ? new FileUpload(
                originalName: new FileName($file->getClientOriginalName()),
                mimeType: new MimeType($file->getMimeType() ?? 'image/jpeg'),
                sizeInBytes: (int) $file->getSize(),
                file: $file,
            )
            : null;

        $password = $this->string('password')->toString();

        return new UpdateProfileWithAvatarAndPreferencesCommand(
            userId: $userId,
            name: $this->string('name')->toString(),
            email: $this->has('email') ? $this->string('email')->toString() : null,
            rawPassword: $password !== '' ? $password : null,
            avatarUpload: $avatarUpload,
            removeAvatar: $this->boolean('remove_avatar'),
            notificationPreferences: $this->extractNotificationPreferences(),
        );
    }

    /** @return array<string, list<string>>|null */
    private function extractNotificationPreferences(): ?array
    {
        /** @var array<string, array<string, string>>|null $submitted */
        $submitted = $this->input('notification_preferences');

        if ($submitted === null) {
            return null;
        }

        $preferences = [];

        foreach ($submitted as $level => $channels) {
            $channelList = [];

            if (isset($channels['email']) && (bool) $channels['email']) {
                $channelList[] = NotificationChannel::Email->value;
            }

            $preferences[$level] = $channelList;
        }

        return $preferences;
    }
}

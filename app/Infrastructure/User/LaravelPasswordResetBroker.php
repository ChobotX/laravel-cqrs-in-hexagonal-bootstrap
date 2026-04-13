<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Contract\Exception\InvalidPasswordResetTokenException;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Hash;

final readonly class LaravelPasswordResetBroker implements PasswordResetBroker
{
    public function __construct(
        private PasswordBrokerManager $passwordBrokerManager,
        private UserProvider $userProvider,
        private UrlGenerator $urlGenerator,
    ) {}

    public function createResetLink(string $email): ?string
    {
        $user = $this->userProvider->retrieveByCredentials(['email' => $email]);

        if (! $user instanceof CanResetPassword) {
            return null;
        }

        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = $this->passwordBrokerManager->broker();
        $token = $broker->createToken($user);

        return $this->urlGenerator->route('password.reset', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(string $email, string $token, string $newPassword): string
    {
        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = $this->passwordBrokerManager->broker();

        $status = $broker->reset(
            [
                'email' => $email,
                'token' => $token,
                'password' => $newPassword,
            ],
            static function (CanResetPassword $canResetPassword, string $password): void {
                if ($canResetPassword instanceof UserModel) {
                    $canResetPassword->password = Hash::make($password);
                    $canResetPassword->save();
                }
            },
        );

        if ($status !== \Illuminate\Auth\Passwords\PasswordBroker::PASSWORD_RESET) {
            throw new InvalidPasswordResetTokenException;
        }

        $user = $this->userProvider->retrieveByCredentials(['email' => $email]);

        if (! $user instanceof UserModel) {
            throw new InvalidPasswordResetTokenException;
        }

        return $user->id;
    }
}

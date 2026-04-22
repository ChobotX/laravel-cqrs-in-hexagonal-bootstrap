<?php

declare(strict_types=1);

namespace App\Infrastructure\Authorization;

use App\Contract\Auth\ImpersonationManager;
use App\Infrastructure\Eloquent\Authorization\ImpersonationSessionModel;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final readonly class SessionImpersonationManager implements ImpersonationManager
{
    public function __construct(
        private Request $request,
        private Guard $guard,
    ) {}

    public function start(string $impersonatorId, string $targetUserId): void
    {
        ImpersonationSessionModel::where('impersonator_user_id', $impersonatorId)
            ->whereNull('ended_at')
            ->update(['ended_at' => now()]);

        $impersonationSessionModel = new ImpersonationSessionModel;
        $impersonationSessionModel->impersonator_user_id = $impersonatorId;
        $impersonationSessionModel->impersonated_user_id = $targetUserId;
        $impersonationSessionModel->token = Str::uuid()->toString();
        $impersonationSessionModel->started_at = now()->toDateTimeString();
        $impersonationSessionModel->save();
    }

    public function stop(): void
    {
        $this->activeSession()?->update(['ended_at' => now()]);
    }

    public function isActive(): bool
    {
        return $this->activeSession() instanceof ImpersonationSessionModel;
    }

    public function realUserId(): ?string
    {
        return $this->activeSession()?->impersonator_user_id;
    }

    public function impersonatedUserId(): ?string
    {
        return $this->activeSession()?->impersonated_user_id;
    }

    private function activeSession(): ?ImpersonationSessionModel
    {
        $token = $this->request->header('X-Impersonate-Token');

        if ($token !== null) {
            return ImpersonationSessionModel::where('token', $token)
                ->whereNull('ended_at')
                ->first();
        }

        /** @var string|null $guardId */
        $guardId = $this->guard->id();

        if ($guardId === null) {
            return null;
        }

        return ImpersonationSessionModel::where('impersonator_user_id', $guardId)
            ->whereNull('ended_at')
            ->first();
    }
}

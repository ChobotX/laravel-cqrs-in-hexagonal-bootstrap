<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Domain\User\Contract\Command\MarkTotpBackupCodesDownloadedCommand;
use App\Domain\User\Contract\Query\GetPendingTotpBackupCodesPayloadQuery;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[SkipPermissionCheck('Authenticated users download own pending TOTP backup codes')]
final readonly class DownloadOwnTotpBackupCodesController
{
    private const string DOWNLOAD_FILENAME = 'totp-backup-codes.txt';

    public function __construct(
        private QueryBus $queryBus,
        private CommandBus $commandBus,
    ) {}

    public function __invoke(): StreamedResponse
    {
        $userId = (string) Auth::id();
        $codes = $this->queryBus->dispatch(new GetPendingTotpBackupCodesPayloadQuery($userId));

        if ($codes === null) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->commandBus->dispatch(new MarkTotpBackupCodesDownloadedCommand($userId));

        return response()->streamDownload(static function () use ($codes): void {
            echo implode(PHP_EOL, $codes);
        }, self::DOWNLOAD_FILENAME, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}

<?php

declare(strict_types=1);

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\EmailTemplate\Contract\Entity\EmailLog;
use App\Domain\EmailTemplate\Contract\Query\ListEmailLogsQuery;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailLogId;
use App\Domain\EmailTemplate\Handler\Query\ListEmailLogsHandler;
use Tests\Helper\FakeEmailLogRepository;

function makeEmailLog(string $logId, string $recipientId = '660e8400-e29b-41d4-a716-446655440000'): EmailLog
{
    return new EmailLog(
        id: new EmailLogId($logId),
        templateType: 'user_invite',
        locale: 'en',
        recipientId: $recipientId,
        recipientEmail: 'test@example.com',
        renderedSubject: 'Rendered subject',
        renderedBodyMasked: '<p>Rendered body</p>',
        variableKeys: ['userName', 'link'],
        traceId: 'trace-123',
        sentAt: new DateTimeImmutable('2025-01-01 00:00:00'),
    );
}

it('returns paginated results from repository', function (): void {
    $emailLog = makeEmailLog('550e8400-e29b-41d4-a716-446655440001');
    $log2 = makeEmailLog('550e8400-e29b-41d4-a716-446655440002');
    $repository = new FakeEmailLogRepository([$emailLog, $log2]);
    $handler = new ListEmailLogsHandler($repository);

    $query = new ListEmailLogsQuery;

    $paginatedResult = $handler->handle($query);

    expect($paginatedResult)->toBeInstanceOf(PaginatedResult::class)
        ->and($paginatedResult->items)->toHaveCount(2)
        ->and($paginatedResult->total)->toBe(2)
        ->and($paginatedResult->items[0])->toBe($emailLog)
        ->and($paginatedResult->items[1])->toBe($log2);
});

it('respects pagination page and per-page settings', function (): void {
    $ids = [
        '550e8400-e29b-41d4-a716-446655440001',
        '550e8400-e29b-41d4-a716-446655440002',
        '550e8400-e29b-41d4-a716-446655440003',
        '550e8400-e29b-41d4-a716-446655440004',
        '550e8400-e29b-41d4-a716-446655440005',
    ];
    $logs = [];
    foreach ($ids as $id) {
        $logs[] = makeEmailLog($id);
    }

    $repository = new FakeEmailLogRepository($logs);
    $handler = new ListEmailLogsHandler($repository);

    $listEmailLogsQuery = (new ListEmailLogsQuery)->withPagination(new Pagination(page: 2, perPage: 2));

    $paginatedResult = $handler->handle($listEmailLogsQuery);

    expect($paginatedResult->items)->toHaveCount(2)
        ->and($paginatedResult->total)->toBe(5)
        ->and($paginatedResult->pagination->page)->toBe(2)
        ->and($paginatedResult->pagination->perPage)->toBe(2);
});

it('returns empty result when repository has no logs', function (): void {
    $repository = new FakeEmailLogRepository;
    $handler = new ListEmailLogsHandler($repository);

    $paginatedResult = $handler->handle(new ListEmailLogsQuery);

    expect($paginatedResult->items)->toBe([])
        ->and($paginatedResult->total)->toBe(0);
});

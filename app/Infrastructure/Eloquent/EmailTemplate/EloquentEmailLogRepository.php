<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\EmailTemplate;

use App\Domain\EmailTemplate\Contract\Entity\EmailLog;
use App\Domain\EmailTemplate\Contract\Repository\EmailLogRepository;

final readonly class EloquentEmailLogRepository implements EmailLogRepository
{
    public function __construct(
        private EmailLogMapper $emailLogMapper,
    ) {}

    public function create(EmailLog $emailLog): void
    {
        EmailLogModel::create([
            'id' => $emailLog->id->value,
            'template_type' => $emailLog->templateType,
            'locale' => $emailLog->locale,
            'recipient_id' => $emailLog->recipientId,
            'recipient_email' => $emailLog->recipientEmail,
            'rendered_subject' => $emailLog->renderedSubject,
            'rendered_body_masked' => $emailLog->renderedBodyMasked,
            'variable_keys' => $emailLog->variableKeys,
            'trace_id' => $emailLog->traceId,
            'sent_at' => $emailLog->sentAt->format('Y-m-d H:i:s'),
        ]);
    }

    /** @return list<EmailLog> */
    public function findByRecipient(string $recipientId, int $limit, int $offset): array
    {
        /* @var list<EmailLog> */
        return array_values(
            EmailLogModel::where('recipient_id', $recipientId)
                ->orderBy('sent_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->map(fn (EmailLogModel $emailLogModel): EmailLog => $this->emailLogMapper->toDomain($emailLogModel))
                ->all(),
        );
    }

    /** @return list<EmailLog> */
    public function findAll(int $limit, int $offset): array
    {
        /* @var list<EmailLog> */
        return array_values(
            EmailLogModel::orderBy('sent_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->map(fn (EmailLogModel $emailLogModel): EmailLog => $this->emailLogMapper->toDomain($emailLogModel))
                ->all(),
        );
    }

    public function countAll(): int
    {
        return EmailLogModel::count();
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\EmailTemplate;

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;

final readonly class EloquentEmailTemplateRepository implements EmailTemplateRepository
{
    public function __construct(
        private EmailTemplateMapper $emailTemplateMapper,
    ) {}

    public function findByTypeAndLocale(string $type, string $locale): ?EmailTemplate
    {
        $model = EmailTemplateModel::where('type', $type)
            ->where('locale', $locale)
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->emailTemplateMapper->toDomain($model);
    }

    /** @return list<EmailTemplate> */
    public function findAllByType(string $type): array
    {
        /* @var list<EmailTemplate> */
        return array_values(
            EmailTemplateModel::where('type', $type)
                ->get()
                ->map(fn (EmailTemplateModel $emailTemplateModel): EmailTemplate => $this->emailTemplateMapper->toDomain($emailTemplateModel))
                ->all(),
        );
    }

    /** @return list<EmailTemplate> */
    public function findAll(): array
    {
        /* @var list<EmailTemplate> */
        return array_values(
            EmailTemplateModel::all()
                ->map(fn (EmailTemplateModel $emailTemplateModel): EmailTemplate => $this->emailTemplateMapper->toDomain($emailTemplateModel))
                ->all(),
        );
    }

    public function updateContent(string $type, string $locale, string $subjectTemplate, string $bodyTemplate): void
    {
        $model = EmailTemplateModel::where('type', $type)
            ->where('locale', $locale)
            ->first();

        if ($model === null) {
            return;
        }

        $model->subject_template = $subjectTemplate;
        $model->body_template = $bodyTemplate;
        $model->save();
    }

    public function deleteByTypeAndLocale(string $type, string $locale): void
    {
        EmailTemplateModel::where('type', $type)
            ->where('locale', $locale)
            ->delete();
    }

    public function create(EmailTemplate $emailTemplate): void
    {
        EmailTemplateModel::create([
            'id' => $emailTemplate->id->value,
            'type' => $emailTemplate->type->value,
            'locale' => $emailTemplate->locale->value,
            'subject_template' => $emailTemplate->subjectTemplate,
            'body_template' => $emailTemplate->bodyTemplate,
        ]);
    }
}

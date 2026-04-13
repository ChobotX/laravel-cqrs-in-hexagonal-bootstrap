<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;
use App\Domain\EmailTemplate\Contract\Service\EmailSender;
use App\Domain\EmailTemplate\Contract\Service\TemplateCompiler;
use App\Domain\Notification\Contract\Service\NotificationChannelSender;
use App\Domain\Notification\Exception\NotificationEmailTemplateNotFoundException;

final readonly class EmailNotificationSender implements NotificationChannelSender
{
    private const string FALLBACK_LOCALE = 'en';

    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
        private TemplateCompiler $templateCompiler,
        private EmailSender $emailSender,
        private Translator $translator,
    ) {}

    public function send(
        string $recipientId,
        string $recipientEmail,
        string $type,
        string $title,
        string $body,
        string $level,
        ?string $link,
    ): void {
        $locale = $this->translator->locale();
        $template = $this->emailTemplateRepository->findByTypeAndLocale('notification', $locale);

        if (! $template instanceof EmailTemplate && $locale !== self::FALLBACK_LOCALE) {
            $template = $this->emailTemplateRepository->findByTypeAndLocale('notification', self::FALLBACK_LOCALE);
        }

        if (! $template instanceof EmailTemplate) {
            throw new NotificationEmailTemplateNotFoundException($locale, self::FALLBACK_LOCALE);
        }

        $variables = [
            'title' => $title,
            'body' => $body,
            'link' => $link,
        ];

        $renderedEmail = $this->templateCompiler->compile(
            $template->subjectTemplate,
            $template->bodyTemplate,
            $variables,
        );

        $this->emailSender->sendHtml($recipientEmail, $renderedEmail->subject, $renderedEmail->htmlBody);
    }

    public function supports(): string
    {
        return 'email';
    }
}

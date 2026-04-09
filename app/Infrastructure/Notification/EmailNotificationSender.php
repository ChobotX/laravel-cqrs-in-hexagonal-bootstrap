<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Application\Bus\QueryBus;
use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;
use App\Domain\EmailTemplate\Contract\Service\EmailSender;
use App\Domain\EmailTemplate\Contract\Service\TemplateCompiler;
use App\Domain\Notification\Contract\Service\NotificationChannelSender;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use Psr\Log\LoggerInterface;

final readonly class EmailNotificationSender implements NotificationChannelSender
{
    private const string FALLBACK_LOCALE = 'en';

    public function __construct(
        private QueryBus $queryBus,
        private EmailTemplateRepository $emailTemplateRepository,
        private TemplateCompiler $templateCompiler,
        private EmailSender $emailSender,
        private Translator $translator,
        private LoggerInterface $logger,
    ) {}

    public function send(
        string $recipientId,
        string $type,
        string $title,
        string $body,
        string $level,
        ?string $link,
    ): void {
        /** @var User $user */
        $user = $this->queryBus->dispatch(new GetUserByIdQuery($recipientId));

        $locale = $this->translator->locale();
        $template = $this->emailTemplateRepository->findByTypeAndLocale('notification', $locale);

        if (! $template instanceof \App\Domain\EmailTemplate\Contract\Entity\EmailTemplate && $locale !== self::FALLBACK_LOCALE) {
            $template = $this->emailTemplateRepository->findByTypeAndLocale('notification', self::FALLBACK_LOCALE);
        }

        if (! $template instanceof \App\Domain\EmailTemplate\Contract\Entity\EmailTemplate) {
            $this->logger->warning('Notification email template not found, skipping email delivery', [
                'recipientId' => $recipientId,
                'locale' => $locale,
            ]);

            return;
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

        $this->emailSender->sendHtml($user->email->value, $renderedEmail->subject, $renderedEmail->htmlBody);
    }

    public function supports(): string
    {
        return 'email';
    }
}

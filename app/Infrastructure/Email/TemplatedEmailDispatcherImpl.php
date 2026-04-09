<?php

declare(strict_types=1);

namespace App\Infrastructure\Email;

use App\Contract\Event\EventCollector;
use App\Contract\IdGenerator;
use App\Contract\Tenancy\TenantContext;
use App\Contract\Tracing\TraceContext;
use App\Domain\EmailTemplate\Constant\EmailTemplateTypes;
use App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;
use App\Domain\EmailTemplate\Contract\Service\EmailSender;
use App\Domain\EmailTemplate\Contract\Service\TemplateCompiler;
use App\Domain\EmailTemplate\Contract\Service\TemplatedEmailDispatcher;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

final readonly class TemplatedEmailDispatcherImpl implements TemplatedEmailDispatcher
{
    private const string FALLBACK_LOCALE = 'en';

    private const string MASK = '***';

    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
        private UserRepository $userRepository,
        private TemplateCompiler $templateCompiler,
        private EmailSender $emailSender,
        private TenantContext $tenantContext,
        private TraceContext $traceContext,
        private IdGenerator $idGenerator,
        private EventCollector $eventCollector,
    ) {}

    /** @param array<string, string|null> $variables */
    public function dispatch(string $userId, string $templateType, string $locale, array $variables): void
    {
        $template = $this->emailTemplateRepository->findByTypeAndLocale($templateType, $locale);

        if (! $template instanceof \App\Domain\EmailTemplate\Contract\Entity\EmailTemplate && $locale !== self::FALLBACK_LOCALE) {
            $template = $this->emailTemplateRepository->findByTypeAndLocale($templateType, self::FALLBACK_LOCALE);
        }

        if (! $template instanceof \App\Domain\EmailTemplate\Contract\Entity\EmailTemplate) {
            throw new EmailTemplateNotFoundException($templateType, $locale);
        }

        $user = $this->userRepository->findById(new UserId($userId));

        if (! $user instanceof User) {
            throw new UserNotFoundException($userId);
        }

        $variables['tenantName'] = $this->tenantContext->currentTenantName();

        $renderedEmail = $this->templateCompiler->compile(
            $template->subjectTemplate,
            $template->bodyTemplate,
            $variables,
        );

        $this->emailSender->sendHtml($user->email->value, $renderedEmail->subject, $renderedEmail->htmlBody);

        $maskedVariables = $this->maskSensitive($templateType, $variables);
        $maskedRendered = $this->templateCompiler->compile(
            $template->subjectTemplate,
            $template->bodyTemplate,
            $maskedVariables,
        );

        $this->eventCollector->collect(new TemplatedEmailSent(
            emailLogId: $this->idGenerator->generate(),
            templateType: $templateType,
            locale: $locale,
            recipientId: $user->id->value,
            recipientEmail: $user->email->value,
            renderedSubject: $renderedEmail->subject,
            renderedBodyMasked: $maskedRendered->htmlBody,
            variableKeys: array_keys($variables),
            traceId: $this->traceContext->traceId(),
            occurredAt: new DateTimeImmutable,
        ));
    }

    /**
     * @param  array<string, string|null>  $variables
     * @return array<string, string|null>
     */
    private function maskSensitive(string $templateType, array $variables): array
    {
        $typeConfig = EmailTemplateTypes::TYPES[$templateType] ?? null;

        if ($typeConfig === null) {
            return $variables;
        }

        $masked = [];

        foreach ($variables as $key => $value) {
            $isSensitive = $typeConfig['variables'][$key]['sensitive'] ?? false;
            $masked[$key] = $isSensitive ? self::MASK : $value;
        }

        return $masked;
    }
}

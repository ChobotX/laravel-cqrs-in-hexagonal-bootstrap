<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\EmailTemplate\Constant\EmailTemplateTypes;
use App\Domain\EmailTemplate\Contract\Query\ListEmailTemplatesQuery;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;

/** @implements QueryHandler<ListEmailTemplatesQuery, list<array{type: string, name: string, description: string, locales: list<string>, updatedAt: ?string}>> */
final readonly class ListEmailTemplatesHandler implements QueryHandler
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
    ) {}

    /** @return list<array{type: string, name: string, description: string, locales: list<string>, updatedAt: ?string}> */
    public function handle(Query $query): array
    {
        $allTemplates = $this->emailTemplateRepository->findAll();

        $grouped = [];

        foreach ($allTemplates as $template) {
            $grouped[$template->type->value][] = $template;
        }

        $result = [];

        foreach (EmailTemplateTypes::TYPES as $type => $config) {
            $templates = $grouped[$type] ?? [];
            $locales = [];
            $latestUpdate = null;

            foreach ($templates as $template) {
                $locales[] = $template->locale->value;
                $updatedStr = $template->updatedAt->format('c');

                if ($latestUpdate === null || $updatedStr > $latestUpdate) {
                    $latestUpdate = $updatedStr;
                }
            }

            $result[] = [
                'type' => $type,
                'name' => $config['name'],
                'description' => $config['description'],
                'locales' => $locales,
                'updatedAt' => $latestUpdate,
            ];
        }

        return $result;
    }
}

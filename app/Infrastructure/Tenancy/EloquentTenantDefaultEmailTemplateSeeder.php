<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Domain\EmailTemplate\Contract\Service\DefaultEmailTemplateSeedData;
use App\Domain\Tenancy\Contract\Service\TenantDefaultEmailTemplateSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class EloquentTenantDefaultEmailTemplateSeeder implements TenantDefaultEmailTemplateSeeder
{
    public function __construct(
        private DefaultEmailTemplateSeedData $defaultEmailTemplateSeedData,
    ) {}

    public function seed(): void
    {
        foreach ($this->defaultEmailTemplateSeedData->templatesByTypeLocaleKey() as $key => $template) {
            [$type, $locale] = explode(':', $key);

            DB::connection('tenant')->table('email_templates')->insertOrIgnore([
                'id' => Str::uuid()->toString(),
                'type' => $type,
                'locale' => $locale,
                'subject_template' => $template['subject'],
                'body_template' => $template['body'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

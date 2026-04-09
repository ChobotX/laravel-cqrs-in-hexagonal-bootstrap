<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\EmailTemplate\Constant\DefaultEmailTemplates;
use App\Infrastructure\Eloquent\EmailTemplate\EmailTemplateModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DefaultEmailTemplates::DEFAULTS as $key => $template) {
            [$type, $locale] = explode(':', $key);

            $exists = EmailTemplateModel::where('type', $type)
                ->where('locale', $locale)
                ->exists();

            if ($exists) {
                continue;
            }

            EmailTemplateModel::create([
                'id' => Str::uuid()->toString(),
                'type' => $type,
                'locale' => $locale,
                'subject_template' => $template['subject'],
                'body_template' => $template['body'],
            ]);
        }
    }
}

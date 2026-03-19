<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Locale;

use App\Presentation\Http\Request\FormRequest;
use Illuminate\Contracts\Config\Repository;

final class SwitchLocaleRequest extends FormRequest
{
    public function __construct(
        private readonly Repository $repository,
    ) {
        parent::__construct();
    }

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        /** @var list<string> $allowedLocales */
        $allowedLocales = $this->repository->get('app.locales', []);

        return [
            'locale' => ['required', 'string', 'in:'.implode(',', $allowedLocales)],
        ];
    }

    public function locale(): string
    {
        return $this->string('locale')->toString();
    }
}

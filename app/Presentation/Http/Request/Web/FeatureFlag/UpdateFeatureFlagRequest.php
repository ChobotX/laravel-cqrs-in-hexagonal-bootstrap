<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\FeatureFlag;

use App\Presentation\Http\Request\FormRequest;

final class UpdateFeatureFlagRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'enabled' => ['required'],
            'value' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function flagEnabled(): bool
    {
        return $this->boolean('enabled');
    }

    public function flagValue(): ?string
    {
        if (! $this->has('value')) {
            return null;
        }

        return $this->string('value')->toString();
    }
}

<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\InternalApi\FeatureFlag;

use App\Presentation\Http\Request\FormRequest;

final class ToggleFeatureFlagRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'enabled' => ['required', 'boolean'],
        ];
    }

    public function flagEnabled(): bool
    {
        return $this->boolean('enabled');
    }
}

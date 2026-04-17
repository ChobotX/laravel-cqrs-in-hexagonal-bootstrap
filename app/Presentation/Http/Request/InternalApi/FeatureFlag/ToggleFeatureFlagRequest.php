<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\InternalApi\FeatureFlag;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class ToggleFeatureFlagRequest extends FormRequest
{
    use HandlesFormRequest;

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

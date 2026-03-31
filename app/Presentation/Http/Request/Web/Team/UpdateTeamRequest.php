<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Team;

use App\Domain\Team\Command\UpdateTeam\UpdateTeamCommand;
use App\Domain\Team\TeamSlug;
use App\Presentation\Http\Request\FormRequest;

final class UpdateTeamRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:'.TeamSlug::MAX_LENGTH, 'regex:'.TeamSlug::SLUG_PATTERN, 'min:'.TeamSlug::MIN_LENGTH],
            'description' => ['nullable', 'string'],
            'parent_team_id' => ['nullable', 'uuid'],
            'labels' => ['sometimes', 'array'],
            'labels.*' => ['string', 'uuid'],
        ];
    }

    public function toCommand(): UpdateTeamCommand
    {
        $parentTeamId = $this->string('parent_team_id')->toString();

        return new UpdateTeamCommand(
            id: $this->routeString('teamId'),
            name: $this->string('name')->toString(),
            slug: $this->string('slug')->toString(),
            description: $this->string('description')->toString(),
            parentTeamId: $parentTeamId !== '' ? $parentTeamId : null,
        );
    }
}

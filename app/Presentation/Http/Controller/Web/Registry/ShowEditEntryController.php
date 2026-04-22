<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Auth\AuthorizationChecker;
use App\Contract\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Domain\Registry\Contract\Query\GetEntryByIdQuery;
use App\Domain\Registry\Contract\Query\GetSerializedSchemaQuery;
use App\Domain\Registry\Contract\ValueObject\JsonSchema;
use Illuminate\View\View;

#[RequiresPermission('registry.entries.update')]
final readonly class ShowEditEntryController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(string $namespace, string $slug, string $id): View
    {
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        abort_unless($definition !== null, HttpStatus::NOT_FOUND);

        $entry = $this->queryBus->dispatch(new GetEntryByIdQuery($id));

        abort_unless($entry !== null, HttpStatus::NOT_FOUND);

        $jsonSchema = $this->queryBus->dispatch(new GetSerializedSchemaQuery($definition->id->value));

        abort_unless($jsonSchema instanceof JsonSchema, HttpStatus::NOT_FOUND);

        $currentUserId = $this->authenticatedUser->id() ?? '';
        $canShareEntry = $currentUserId !== ''
            && $this->authorizationChecker->can($currentUserId, 'registry.entries.update')
            && $this->authorizationChecker->can($currentUserId, 'users.list.read');

        return view('registry.entries.edit', [
            'definition' => $definition,
            'entry' => $entry,
            'schema' => $jsonSchema->encoded,
            'canShareEntry' => $canShareEntry,
        ]);
    }
}

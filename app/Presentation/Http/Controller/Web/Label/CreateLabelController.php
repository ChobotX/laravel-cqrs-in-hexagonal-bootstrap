<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Label;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\IdGenerator;
use App\Domain\Label\Command\CreateLabel\CreateLabelCommand;
use App\Domain\Label\Exception\LabelAlreadyExistsException;
use App\Domain\Label\Label;
use App\Domain\Label\Query\SearchLabels\SearchLabelsQuery;
use App\Presentation\Http\Request\Web\Label\CreateLabelRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('labels.management.create')]
final readonly class CreateLabelController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(CreateLabelRequest $createLabelRequest): JsonResponse
    {
        $namespace = $createLabelRequest->namespace();
        $name = $createLabelRequest->name();
        $id = $this->idGenerator->generate();

        try {
            $this->commandBus->dispatch(new CreateLabelCommand($id, $namespace, $name));
        } catch (LabelAlreadyExistsException) {
            // @silent: intentional recovery — returns existing label instead of failing
            /** @var list<Label> $existing */
            $existing = $this->queryBus->dispatch(new SearchLabelsQuery(
                namespace: $namespace,
                term: $name,
                limit: 1,
            ));

            if ($existing !== []) {
                return new JsonResponse(['data' => [
                    'id' => $existing[0]->id->value,
                    'name' => $existing[0]->name->value,
                ]]);
            }
        }

        return new JsonResponse(['data' => ['id' => $id, 'name' => $name]], 201);
    }
}

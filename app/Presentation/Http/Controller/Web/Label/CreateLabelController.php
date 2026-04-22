<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Label;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Contract\IdGenerator;
use App\Domain\Label\Contract\Command\CreateLabelCommand;
use App\Domain\Label\Contract\Entity\Label;
use App\Domain\Label\Contract\Exception\LabelAlreadyExistsException;
use App\Domain\Label\Contract\Query\SearchLabelsQuery;
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

        return new JsonResponse(['data' => ['id' => $id, 'name' => $name]], HttpStatus::CREATED);
    }
}

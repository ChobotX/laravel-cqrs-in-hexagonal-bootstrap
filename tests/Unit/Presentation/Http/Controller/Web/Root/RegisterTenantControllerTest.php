<?php

declare(strict_types=1);

use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\IdGenerator;
use App\Domain\Tenancy\Contract\Command\CreateTenantCommand;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Presentation\Http\Controller\Web\Root\RegisterTenantController;
use App\Presentation\Http\Request\Root\RegisterTenantFormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

uses(Tests\TestCase::class);

it('dispatches tenant commands then redirects to tenant login', function (): void {
    $slug = 'regcov'.bin2hex(random_bytes(4));
    $domain = $slug;

    $registerTenantFormRequest = RegisterTenantFormRequest::create(
        'http://laravel-bootstrap.local/register',
        'POST',
        [
            'name' => 'Coverage Corp',
            'slug' => $slug,
            'domain' => $domain,
            'admin_name' => 'Admin',
            'admin_email' => 'admin@example.test',
        ],
    );
    $registerTenantFormRequest->setContainer(app());
    $registerTenantFormRequest->setRedirector(app(Redirector::class));
    $registerTenantFormRequest->validateResolved();

    $commandBus = new class implements CommandBus
    {
        /** @var list<Command> */
        public array $dispatched = [];

        public function dispatch(Command $command): void
        {
            $this->dispatched[] = $command;
        }
    };

    $idGenerator = new class implements IdGenerator
    {
        public function generate(): string
        {
            return '00000000-0000-0000-0000-0000000000aa';
        }
    };

    $controller = new RegisterTenantController($commandBus, $idGenerator);
    $redirectResponse = $controller($registerTenantFormRequest);

    expect($redirectResponse)->toBeInstanceOf(RedirectResponse::class)
        ->and($redirectResponse->getTargetUrl())->toBe('http://'.$domain.'.laravel-bootstrap.local/login');

    expect($commandBus->dispatched)->toHaveCount(2)
        ->and($commandBus->dispatched[0])->toBeInstanceOf(CreateTenantCommand::class)
        ->and($commandBus->dispatched[1])->toBeInstanceOf(InitializeTenantAdminCommand::class);
});

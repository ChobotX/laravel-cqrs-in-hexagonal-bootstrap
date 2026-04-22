<?php

declare(strict_types=1);

namespace App\Domain\PhpStanFixtures\Simulator;

use App\Domain\User\Contract\Repository\UserRepository;

final readonly class DomainCrossDomainRepositoryImport
{
    public function __construct(private UserRepository $userRepository) {}
}

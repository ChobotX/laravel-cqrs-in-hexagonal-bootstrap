<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

use function fake;

/** @extends Factory<RoleModel> */
final class RoleModelFactory extends Factory
{
    protected $model = RoleModel::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'description' => fake()->sentence(),
            'is_system' => false,
            'organization_id' => null,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use function fake;

/** @extends Factory<OrganizationModel> */
final class OrganizationModelFactory extends Factory
{
    protected $model = OrganizationModel::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'slug' => Str::slug(fake()->unique()->company()),
            'description' => fake()->sentence(),
        ];
    }
}

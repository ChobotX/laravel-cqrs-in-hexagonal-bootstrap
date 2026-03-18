<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

use function fake;

/** @extends Factory<UserModel> */
final class UserModelFactory extends Factory
{
    protected $model = UserModel::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ];
    }
}

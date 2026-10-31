<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Database\Factories;

use Drivkraft\Valkyrie\Models\Ability;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ability>
 */
final class AbilityFactory extends Factory
{
    protected $model = Ability::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(2),
            'only_owned' => false,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\StandardSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StandardSource>
 */
class StandardSourceFactory extends Factory
{
    protected $model = StandardSource::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'code' => strtoupper($this->faker->unique()->lexify('SRC-???')),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}

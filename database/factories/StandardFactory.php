<?php

namespace Database\Factories;

use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Standard>
 */
class StandardFactory extends Factory
{
    protected $model = Standard::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('STD-???')),
            'name' => $this->faker->words(4, true),
            'description' => $this->faker->optional()->paragraph(),
            'standard_source_id' => StandardSource::factory(),
            'is_active' => true,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}

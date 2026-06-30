<?php

namespace Database\Factories;

use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Indicator>
 */
class IndicatorFactory extends Factory
{
    protected $model = Indicator::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'standard_id' => Standard::factory(),
            'parent_indicator_id' => null,
            'code' => strtoupper($this->faker->unique()->lexify('IND-???')),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'calculation_method' => Indicator::COUNT,
            'measurement_unit' => 'unit',
            'weight' => $this->faker->randomFloat(2, 1, 100),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}

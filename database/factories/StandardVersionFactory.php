<?php

namespace Database\Factories;

use App\Models\QualityPeriod;
use App\Models\Standard;
use App\Models\StandardVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StandardVersion>
 */
class StandardVersionFactory extends Factory
{
    protected $model = StandardVersion::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'standard_id' => Standard::factory(),
            'quality_period_id' => QualityPeriod::factory(),
            'version' => 'v'.$this->faker->numerify('#'),
            'start_date' => $this->faker->date(),
            'end_date' => null,
            'status' => 'draft',
            'is_active' => true,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}

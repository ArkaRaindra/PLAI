<?php

namespace Tests\Feature;

use App\Models\Indicator;
use App\Models\QualityPeriod;
use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class TargetUniqueValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unique_validation_does_not_treat_nested_attribute_as_column(): void
    {
        $validator = Validator::make(
            [
                'data' => [
                    'quality_period_id' => 3,
                ],
                'indicator_id' => 1,
            ],
            [
                // Filament / Livewire commonly validates nested form state under "data.*".
                'data.quality_period_id' => [
                    Rule::unique('targets', 'quality_period_id')
                        ->where(fn ($query) => $query->where('indicator_id', 1)),
                ],
            ],
        );

        $this->assertTrue($validator->passes());
    }

    public function test_target_indicator_and_quality_period_must_be_unique_together(): void
    {
        $indicator = Indicator::factory()->create();
        $qualityPeriod = QualityPeriod::factory()->create();

        Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $qualityPeriod->id,
            'target_value' => 10,
        ]);

        $validator = Validator::make(
            [
                'indicator_id' => $indicator->id,
                'quality_period_id' => $qualityPeriod->id,
            ],
            [
                'indicator_id' => [
                    Rule::unique('targets', 'indicator_id')
                        ->where(fn ($query) => $query->where('quality_period_id', $qualityPeriod->id)),
                ],
            ],
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('indicator_id', $validator->errors()->messages());
    }
}

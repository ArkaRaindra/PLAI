<?php

namespace Tests\Feature;

use App\Models\AuditChecklistTemplate;
use App\Models\AuditCycle;
use App\Models\QualityPeriod;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditCycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }

    public function test_a_cycle_can_be_created_and_defaults_to_draft(): void
    {
        $period = $this->makeQualityPeriod();
        $template = $this->makeTemplate();

        $cycle = AuditCycle::query()->create([
            'quality_period_id' => $period->id,
            'checklist_template_id' => $template->id,
        ]);

        $this->assertSame($period->id, $cycle->quality_period_id);
        $this->assertSame($template->id, $cycle->checklist_template_id);
        $this->assertSame('draft', $cycle->status);
    }

    public function test_status_progresses_through_valid_transitions(): void
    {
        $cycle = $this->makeCycle();

        $this->assertTrue($cycle->canTransitionTo('active'));
        $cycle->update(['status' => 'active']);
        $this->assertSame('active', $cycle->fresh()->status);

        $this->assertTrue($cycle->canTransitionTo('closed'));
        $cycle->update(['status' => 'closed']);
        $this->assertSame('closed', $cycle->fresh()->status);
    }

    public function test_an_invalid_status_transition_is_rejected(): void
    {
        $cycle = $this->makeCycle();

        // draft cannot jump straight to closed.
        $this->assertFalse($cycle->canTransitionTo('closed'));

        $this->expectException(\RuntimeException::class);
        $cycle->update(['status' => 'closed']);
    }

    public function test_closed_is_a_terminal_status(): void
    {
        $this->assertSame([], AuditCycle::TRANSITIONS['closed']);
    }

    private function makeQualityPeriod(): QualityPeriod
    {
        return QualityPeriod::query()->create([
            'code' => 'QP-'.fake()->unique()->numerify('###'),
            'name' => 'Periode '.fake()->year(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);
    }

    private function makeTemplate(): AuditChecklistTemplate
    {
        return AuditChecklistTemplate::query()->create([
            'name' => 'Checklist Audit Prodi',
            'version_no' => '1.0',
        ]);
    }

    private function makeCycle(): AuditCycle
    {
        return AuditCycle::query()->create([
            'quality_period_id' => $this->makeQualityPeriod()->id,
            'checklist_template_id' => $this->makeTemplate()->id,
        ]);
    }
}

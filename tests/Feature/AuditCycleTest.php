<?php

namespace Tests\Feature;

use App\Models\AuditChecklistTemplate;
use App\Models\AuditCycle;
use App\Models\QualityPeriod;
use App\Models\User;
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

    public function test_a_cycle_can_be_created_for_an_active_period(): void
    {
        $period = $this->makeQualityPeriod('active');
        $template = $this->makeTemplate();

        $cycle = AuditCycle::query()->create([
            'quality_period_id' => $period->id,
            'checklist_template_id' => $template->id,
        ]);

        $this->assertSame($period->id, $cycle->quality_period_id);
        $this->assertSame($template->id, $cycle->checklist_template_id);
        $this->assertSame('draft', $cycle->status);
    }

    public function test_a_cycle_cannot_be_created_for_a_non_active_period(): void
    {
        $period = $this->makeQualityPeriod('draft');
        $template = $this->makeTemplate();

        $this->expectException(\RuntimeException::class);

        AuditCycle::query()->create([
            'quality_period_id' => $period->id,
            'checklist_template_id' => $template->id,
        ]);
    }

    public function test_only_one_cycle_is_allowed_per_period(): void
    {
        $period = $this->makeQualityPeriod('active');
        $template = $this->makeTemplate();

        AuditCycle::query()->create([
            'quality_period_id' => $period->id,
            'checklist_template_id' => $template->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        AuditCycle::query()->create([
            'quality_period_id' => $period->id,
            'checklist_template_id' => $template->id,
        ]);
    }

    public function test_status_progresses_through_valid_transitions(): void
    {
        $period = $this->makeQualityPeriod('active');
        $template = $this->makeTemplate();

        $cycle = AuditCycle::query()->create([
            'quality_period_id' => $period->id,
            'checklist_template_id' => $template->id,
        ]);

        $this->assertTrue($cycle->canTransitionTo('ongoing'));
        $cycle->update(['status' => 'ongoing']);
        $this->assertSame('ongoing', $cycle->fresh()->status);

        $cycle->update(['status' => 'completed']);
        $this->assertSame('completed', $cycle->fresh()->status);
    }

    public function test_an_invalid_status_transition_is_rejected(): void
    {
        $period = $this->makeQualityPeriod('active');
        $template = $this->makeTemplate();

        $cycle = AuditCycle::query()->create([
            'quality_period_id' => $period->id,
            'checklist_template_id' => $template->id,
        ]);

        $this->assertFalse($cycle->canTransitionTo('completed'));

        $this->expectException(\RuntimeException::class);

        $cycle->update(['status' => 'completed']);
    }

    public function test_completed_and_cancelled_are_terminal_statuses(): void
    {
        $this->assertSame([], AuditCycle::TRANSITIONS['completed']);
        $this->assertSame([], AuditCycle::TRANSITIONS['cancelled']);
    }

    public function test_auditor_can_manage_cycles_while_dosen_cannot(): void
    {
        $auditor = User::factory()->create(['is_active' => true]);
        $auditor->assignRole('auditor');

        $dosen = User::factory()->create(['is_active' => true]);
        $dosen->assignRole('dosen');

        $period = $this->makeQualityPeriod('active');
        $template = $this->makeTemplate();

        $cycle = AuditCycle::query()->create([
            'quality_period_id' => $period->id,
            'checklist_template_id' => $template->id,
        ]);

        $this->assertTrue($auditor->can('create', AuditCycle::class));
        $this->assertTrue($auditor->can('update', $cycle));

        $this->assertFalse($dosen->can('create', AuditCycle::class));
        $this->assertFalse($dosen->can('update', $cycle));
    }

    private function makeQualityPeriod(string $status): QualityPeriod
    {
        return QualityPeriod::query()->create([
            'code' => 'QP-'.fake()->unique()->numerify('###'),
            'name' => 'Periode '.fake()->year(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'status' => $status,
            'is_active' => $status === 'active',
        ]);
    }

    private function makeTemplate(): AuditChecklistTemplate
    {
        return AuditChecklistTemplate::query()->create([
            'name' => 'Checklist Audit Prodi',
            'version_no' => 1,
        ]);
    }
}
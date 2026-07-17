<?php

namespace Tests\Feature;

use App\Models\AuditAssignment;
use App\Models\AuditChecklistTemplate;
use App\Models\AuditCycle;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\QualityPeriod;
use App\Models\User;
use App\Models\UserPosition;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditAssignmentTest extends TestCase
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

    public function test_an_auditor_can_be_assigned_to_audit_another_unit(): void
    {
        $cycle = $this->makeCycle();
        $auditorUnit = $this->makeOrganizationUnit();
        $auditeeUnit = $this->makeOrganizationUnit();
        $auditorPosition = $this->makeAuditorPosition($auditorUnit);

        $assignment = AuditAssignment::query()->create([
            'audit_cycle_id' => $cycle->id,
            'auditor_position_id' => $auditorPosition->id,
            'organization_unit_id' => $auditeeUnit->id,
        ]);

        $this->assertSame($cycle->id, $assignment->audit_cycle_id);
        $this->assertSame($auditorPosition->id, $assignment->auditor_position_id);
        $this->assertSame($auditeeUnit->id, $assignment->organization_unit_id);
        $this->assertNotNull($assignment->assigned_at);
    }

    public function test_assigned_at_defaults_to_now_when_not_given(): void
    {
        $cycle = $this->makeCycle();
        $auditeeUnit = $this->makeOrganizationUnit();
        $auditorPosition = $this->makeAuditorPosition($this->makeOrganizationUnit());

        $assignment = AuditAssignment::query()->create([
            'audit_cycle_id' => $cycle->id,
            'auditor_position_id' => $auditorPosition->id,
            'organization_unit_id' => $auditeeUnit->id,
        ]);

        $this->assertNotNull($assignment->assigned_at);
        $this->assertTrue($assignment->assigned_at->isToday());
    }

    public function test_an_auditor_cannot_be_assigned_to_audit_their_own_unit(): void
    {
        $cycle = $this->makeCycle();
        $unit = $this->makeOrganizationUnit();
        $auditorPosition = $this->makeAuditorPosition($unit);

        $this->expectException(\RuntimeException::class);

        AuditAssignment::query()->create([
            'audit_cycle_id' => $cycle->id,
            'auditor_position_id' => $auditorPosition->id,
            'organization_unit_id' => $unit->id,
        ]);
    }

    public function test_an_inactive_auditor_position_cannot_be_assigned(): void
    {
        $cycle = $this->makeCycle();
        $auditeeUnit = $this->makeOrganizationUnit();
        $auditorPosition = $this->makeAuditorPosition($this->makeOrganizationUnit(), isActive: false);

        $this->expectException(\RuntimeException::class);

        AuditAssignment::query()->create([
            'audit_cycle_id' => $cycle->id,
            'auditor_position_id' => $auditorPosition->id,
            'organization_unit_id' => $auditeeUnit->id,
        ]);
    }

    public function test_an_inactive_auditee_unit_cannot_be_assigned(): void
    {
        $cycle = $this->makeCycle();
        $auditeeUnit = $this->makeOrganizationUnit(isActive: false);
        $auditorPosition = $this->makeAuditorPosition($this->makeOrganizationUnit());

        $this->expectException(\RuntimeException::class);

        AuditAssignment::query()->create([
            'audit_cycle_id' => $cycle->id,
            'auditor_position_id' => $auditorPosition->id,
            'organization_unit_id' => $auditeeUnit->id,
        ]);
    }

    public function test_assignment_cannot_be_added_to_a_completed_cycle(): void
    {
        $cycle = $this->makeCycle();
        $cycle->update(['status' => 'ongoing']);
        $cycle->update(['status' => 'completed']);

        $auditeeUnit = $this->makeOrganizationUnit();
        $auditorPosition = $this->makeAuditorPosition($this->makeOrganizationUnit());

        $this->expectException(\RuntimeException::class);

        AuditAssignment::query()->create([
            'audit_cycle_id' => $cycle->id,
            'auditor_position_id' => $auditorPosition->id,
            'organization_unit_id' => $auditeeUnit->id,
        ]);
    }

    public function test_duplicate_assignment_is_rejected(): void
    {
        $cycle = $this->makeCycle();
        $auditeeUnit = $this->makeOrganizationUnit();
        $auditorPosition = $this->makeAuditorPosition($this->makeOrganizationUnit());

        AuditAssignment::query()->create([
            'audit_cycle_id' => $cycle->id,
            'auditor_position_id' => $auditorPosition->id,
            'organization_unit_id' => $auditeeUnit->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        AuditAssignment::query()->create([
            'audit_cycle_id' => $cycle->id,
            'auditor_position_id' => $auditorPosition->id,
            'organization_unit_id' => $auditeeUnit->id,
        ]);
    }

    private function makeCycle(): AuditCycle
    {
        $period = QualityPeriod::query()->create([
            'code' => 'QP-'.fake()->unique()->numerify('###'),
            'name' => 'Periode '.fake()->year(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'status' => 'active',
            'is_active' => true,
        ]);

        $template = AuditChecklistTemplate::query()->create([
            'name' => 'Checklist Audit Prodi',
            'version_no' => 1,
        ]);

        return AuditCycle::query()->create([
            'quality_period_id' => $period->id,
            'checklist_template_id' => $template->id,
        ]);
    }

    private function makeOrganizationUnit(bool $isActive = true): OrganizationUnit
    {
        return OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => $isActive,
        ]);
    }

    private function makeAuditorPosition(OrganizationUnit $unit, bool $isActive = true): UserPosition
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('auditor');

        $position = Position::query()->create([
            'code' => 'POS-'.fake()->unique()->numerify('###'),
            'name' => 'Auditor Internal',
        ]);

        return UserPosition::query()->create([
            'user_id' => $user->id,
            'position_id' => $position->id,
            'organization_unit_id' => $unit->id,
            'start_date' => now()->toDateString(),
            'is_active' => $isActive,
        ]);
    }
}
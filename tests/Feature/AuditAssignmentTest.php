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

    public function test_an_auditor_can_be_assigned_to_an_audit_unit(): void
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

    public function test_relationships_resolve_to_their_models(): void
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

        $this->assertSame($auditorUnit->id, $assignment->auditorPosition->organization_unit_id);
        $this->assertSame($auditeeUnit->name, $assignment->organizationUnit->name);
        $this->assertSame($cycle->id, $assignment->auditCycle->id);
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

    private function makeOrganizationUnit(bool $isActive = true): OrganizationUnit
    {
        return OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => $isActive,
        ]);
    }

    private function makeAuditorPosition(OrganizationUnit $unit): UserPosition
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
            'is_active' => true,
        ]);
    }
}

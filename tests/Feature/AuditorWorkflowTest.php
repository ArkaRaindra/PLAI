<?php

namespace Tests\Feature;

use App\Models\AuditAssignment;
use App\Models\AuditChecklistTemplate;
use App\Models\AuditCycle;
use App\Models\AuditFinding;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\QualityPeriod;
use App\Models\User;
use App\Models\UserPosition;
use App\Models\WorkflowInstance;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditorWorkflowTest extends TestCase
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

    public function test_audit_finding_gets_an_open_workflow_instance_on_creation(): void
    {
        $finding = $this->makeAuditFinding();

        $this->assertNotNull($finding->workflowInstance);
        $this->assertSame('open', $finding->workflowInstance->current_status);
        $this->assertCount(1, $finding->workflowInstance->histories);
        $this->assertSame('audit_finding', $finding->workflowInstance->entity_type);
    }

    public function test_workflow_follows_the_full_open_to_closed_chain(): void
    {
        $superAdmin = $this->makeUser('super-admin');
        $finding = $this->makeAuditFinding();

        $this->actingAs($superAdmin);

        $finding->transitionWorkflowTo('assigned');
        $this->assertSame('assigned', $finding->workflowInstance->fresh()->current_status);

        $finding->transitionWorkflowTo('corrective_action', 'Ditemukan 2 temuan minor.');
        $this->assertSame('corrective_action', $finding->workflowInstance->fresh()->current_status);

        $finding->transitionWorkflowTo('verification');
        $this->assertSame('verification', $finding->workflowInstance->fresh()->current_status);

        $finding->transitionWorkflowTo('closed', 'Tindak lanjut terverifikasi.');
        $this->assertSame('closed', $finding->workflowInstance->fresh()->current_status);

        // open -> assigned -> corrective_action -> verification -> closed
        $this->assertCount(5, $finding->workflowInstance->fresh()->histories);
    }

    public function test_failed_verification_can_be_sent_back_to_corrective_action(): void
    {
        $finding = $this->makeAuditFinding();

        $this->fastForward($finding->workflowInstance, ['assigned', 'corrective_action', 'verification']);

        $finding->workflowInstance->fresh()->transitionTo('corrective_action', 'Tindak lanjut belum memadai.');

        $this->assertSame('corrective_action', $finding->workflowInstance->fresh()->current_status);
    }

    public function test_open_cannot_jump_directly_to_verification(): void
    {
        $finding = $this->makeAuditFinding();

        $this->expectException(\RuntimeException::class);

        $finding->workflowInstance->transitionTo('verification');
    }

    public function test_closed_is_a_terminal_state(): void
    {
        $finding = $this->makeAuditFinding();

        $this->fastForward($finding->workflowInstance, ['assigned', 'corrective_action', 'verification', 'closed']);

        $this->assertFalse($finding->workflowInstance->fresh()->canTransitionTo('assigned'));
        $this->assertFalse($finding->workflowInstance->fresh()->canTransitionTo('corrective_action'));
        $this->assertFalse($finding->workflowInstance->fresh()->canTransitionTo('verification'));
    }

    public function test_super_admin_can_force_transition_out_of_sequence(): void
    {
        $superAdmin = $this->makeUser('super-admin');
        $finding = $this->makeAuditFinding();

        $this->actingAs($superAdmin);

        $finding->workflowInstance->forceTransitionTo('verification', 'Dikoreksi oleh super admin.');

        $this->assertSame('verification', $finding->workflowInstance->fresh()->current_status);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    private function makeAuditAssignment(): AuditAssignment
    {
        $unit = OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => 'system',
        ]);

        $position = Position::query()->create([
            'code' => 'POS-'.fake()->unique()->numerify('###'),
            'name' => 'Auditor Internal',
            'created_by' => 'system',
        ]);

        $auditorUser = User::factory()->create(['is_active' => true]);
        $auditorUser->assignRole('auditor');

        $auditorPosition = UserPosition::query()->create([
            'user_id' => $auditorUser->id,
            'position_id' => $position->id,
            'organization_unit_id' => $unit->id,
            'start_date' => now()->subMonth(),
            'is_active' => true,
            'created_by' => 'system',
        ]);

        $qualityPeriod = QualityPeriod::query()->create([
            'code' => 'QP-'.fake()->unique()->numerify('###'),
            'name' => 'Periode '.fake()->year(),
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'status' => 'active',
            'created_by' => 'system',
        ]);

        $checklistTemplate = AuditChecklistTemplate::query()->create([
            'name' => 'Template Checklist AMI',
            'version_no' => '1.0',
            'created_by' => 'system',
        ]);

        $auditCycle = AuditCycle::query()->create([
            'quality_period_id' => $qualityPeriod->id,
            'checklist_template_id' => $checklistTemplate->id,
            'status' => 'active',
            'created_by' => 'system',
        ]);

        return AuditAssignment::query()->create([
            'audit_cycle_id' => $auditCycle->id,
            'auditor_position_id' => $auditorPosition->id,
            'organization_unit_id' => $unit->id,
            'created_by' => 'system',
        ]);
    }

    private function makeAuditFinding(): AuditFinding
    {
        $assignment = $this->makeAuditAssignment();

        return AuditFinding::query()->create([
            'audit_assignment_id' => $assignment->id,
            'title' => fake()->sentence(),
            'category' => 'kts',
            'severity' => 'major',
            'description' => fake()->paragraph(),
            'created_by' => 'system',
        ]);
    }

    /**
     * @param  list<string>  $steps
     */
    private function fastForward(WorkflowInstance $instance, array $steps): void
    {
        foreach ($steps as $step) {
            $instance->fresh()->transitionTo($step);
        }
    }
}

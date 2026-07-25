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

    public function test_audit_assignment_gets_an_open_workflow_instance_on_creation(): void
    {
        $assignment = $this->makeAuditAssignment();

        $this->assertNotNull($assignment->workflowInstance);
        $this->assertSame('open', $assignment->workflowInstance->current_status);
        $this->assertCount(1, $assignment->workflowInstance->histories);
        $this->assertSame('audit_assignment', $assignment->workflowInstance->entity_type);
    }

    public function test_workflow_follows_the_full_open_to_closed_chain(): void
    {
        $superAdmin = $this->makeUser('super-admin');
        $assignment = $this->makeAuditAssignment();

        $this->actingAs($superAdmin);

        $assignment->transitionWorkflowTo('assigned');
        $this->assertSame('assigned', $assignment->workflowInstance->fresh()->current_status);

        $assignment->transitionWorkflowTo('corrective_action', 'Ditemukan 2 temuan minor.');
        $this->assertSame('corrective_action', $assignment->workflowInstance->fresh()->current_status);

        $assignment->transitionWorkflowTo('verification');
        $this->assertSame('verification', $assignment->workflowInstance->fresh()->current_status);

        $assignment->transitionWorkflowTo('closed', 'Tindak lanjut terverifikasi.');
        $this->assertSame('closed', $assignment->workflowInstance->fresh()->current_status);

        // open -> assigned -> corrective_action -> verification -> closed
        $this->assertCount(5, $assignment->workflowInstance->fresh()->histories);
    }

    public function test_failed_verification_can_be_sent_back_to_corrective_action(): void
    {
        $assignment = $this->makeAuditAssignment();

        $this->fastForward($assignment->workflowInstance, ['assigned', 'corrective_action', 'verification']);

        $assignment->workflowInstance->fresh()->transitionTo('corrective_action', 'Tindak lanjut belum memadai.');

        $this->assertSame('corrective_action', $assignment->workflowInstance->fresh()->current_status);
    }

    public function test_open_cannot_jump_directly_to_verification(): void
    {
        $assignment = $this->makeAuditAssignment();

        $this->expectException(\RuntimeException::class);

        $assignment->workflowInstance->transitionTo('verification');
    }

    public function test_closed_is_a_terminal_state(): void
    {
        $assignment = $this->makeAuditAssignment();

        $this->fastForward($assignment->workflowInstance, ['assigned', 'corrective_action', 'verification', 'closed']);

        $this->assertFalse($assignment->workflowInstance->fresh()->canTransitionTo('assigned'));
        $this->assertFalse($assignment->workflowInstance->fresh()->canTransitionTo('corrective_action'));
        $this->assertFalse($assignment->workflowInstance->fresh()->canTransitionTo('verification'));
    }

    public function test_super_admin_can_force_transition_out_of_sequence(): void
    {
        $superAdmin = $this->makeUser('super-admin');
        $assignment = $this->makeAuditAssignment();

        $this->actingAs($superAdmin);

        $assignment->workflowInstance->forceTransitionTo('verification', 'Dikoreksi oleh super admin.');

        $this->assertSame('verification', $assignment->workflowInstance->fresh()->current_status);
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
            'is_active' => true,
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

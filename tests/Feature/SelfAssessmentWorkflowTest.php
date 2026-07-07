<?php

namespace Tests\Feature;

use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\QualityPeriod;
use App\Models\SelfAssessment;
use App\Models\SelfAssessmentDetail;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelfAssessmentWorkflowTest extends TestCase
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

    public function test_draft_can_be_submitted_when_it_has_at_least_one_detail(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $selfAssessment = $this->makeSelfAssessment($kaprodi);
        $this->addDetail($selfAssessment, $kaprodi, score: 80);

        $this->actingAs($kaprodi);

        $this->assertTrue($kaprodi->can('submit', $selfAssessment));

        $selfAssessment->update(['status' => 'submitted']);

        $this->assertSame('submitted', $selfAssessment->fresh()->status);
        $this->assertSame($kaprodi->id, $selfAssessment->fresh()->submitted_by);
    }

    public function test_draft_without_details_cannot_be_submitted(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $selfAssessment = $this->makeSelfAssessment($kaprodi);

        $this->assertFalse($kaprodi->can('submit', $selfAssessment));
    }

    public function test_draft_cannot_jump_directly_to_approved(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $selfAssessment = $this->makeSelfAssessment($kaprodi);
        $this->addDetail($selfAssessment, $kaprodi, score: 80);

        $this->actingAs($kaprodi);

        $this->expectException(\RuntimeException::class);

        $selfAssessment->update(['status' => 'approved']);
    }

    public function test_submitted_can_be_approved_by_ketua_lpm(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $ketuaLpm = $this->makeUser('ketua-lpm');
        $selfAssessment = $this->makeSelfAssessment($kaprodi);
        $this->addDetail($selfAssessment, $kaprodi, score: 80);
        $selfAssessment->update(['status' => 'submitted']);

        $this->actingAs($ketuaLpm);
        $this->assertTrue($ketuaLpm->can('approve', $selfAssessment));

        $selfAssessment->update(['status' => 'approved']);

        $this->assertSame('approved', $selfAssessment->fresh()->status);
        $this->assertSame($ketuaLpm->id, $selfAssessment->fresh()->approved_by);
    }

    public function test_kaprodi_cannot_approve_own_self_assessment(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $selfAssessment = $this->makeSelfAssessment($kaprodi);
        $this->addDetail($selfAssessment, $kaprodi, score: 80);
        $selfAssessment->update(['status' => 'submitted']);

        $this->assertFalse($kaprodi->can('approve', $selfAssessment));
    }

    public function test_approved_is_a_terminal_status(): void
    {
        $ketuaLpm = $this->makeUser('ketua-lpm');
        $selfAssessment = $this->makeSelfAssessment($ketuaLpm, status: 'approved');

        $this->actingAs($ketuaLpm);

        $this->expectException(\RuntimeException::class);

        $selfAssessment->update(['status' => 'submitted']);
    }

    public function test_submitted_can_be_rejected_with_note(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $ketuaLpm = $this->makeUser('ketua-lpm');
        $selfAssessment = $this->makeSelfAssessment($kaprodi);
        $this->addDetail($selfAssessment, $kaprodi, score: 80);
        $selfAssessment->update(['status' => 'submitted']);

        $this->actingAs($ketuaLpm);
        $this->assertTrue($ketuaLpm->can('reject', $selfAssessment));

        $selfAssessment->update([
            'status' => 'rejected',
            'note_rejected' => 'Analisis capaian belum lengkap',
        ]);

        $selfAssessment->refresh();

        $this->assertSame('rejected', $selfAssessment->status);
        $this->assertSame($ketuaLpm->id, $selfAssessment->rejected_by);
        $this->assertSame('Analisis capaian belum lengkap', $selfAssessment->note_rejected);
    }

    public function test_kaprodi_cannot_reject_own_self_assessment(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $selfAssessment = $this->makeSelfAssessment($kaprodi);
        $this->addDetail($selfAssessment, $kaprodi, score: 80);
        $selfAssessment->update(['status' => 'submitted']);

        $this->assertFalse($kaprodi->can('reject', $selfAssessment));
    }

    public function test_rejected_can_be_resubmitted(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $selfAssessment = $this->makeSelfAssessment($kaprodi, status: 'rejected');
        $this->addDetail($selfAssessment, $kaprodi, score: 80);

        $this->actingAs($kaprodi);
        $this->assertTrue($kaprodi->can('submit', $selfAssessment));

        $selfAssessment->update(['status' => 'submitted']);

        $this->assertSame('submitted', $selfAssessment->fresh()->status);
    }

    public function test_final_score_is_recalculated_automatically_from_details(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $selfAssessment = $this->makeSelfAssessment($kaprodi);

        $this->addDetail($selfAssessment, $kaprodi, score: 80);
        $this->assertSame('80.00', $selfAssessment->fresh()->final_score);

        $detail = $this->addDetail($selfAssessment, $kaprodi, score: 90, indicator: Indicator::factory()->create([
            'created_by' => $kaprodi->id,
            'updated_by' => $kaprodi->id,
        ]));

        $this->assertSame('85.00', $selfAssessment->fresh()->final_score);

        $detail->delete();

        $this->assertSame('80.00', $selfAssessment->fresh()->final_score);
    }

    public function test_cannot_have_duplicate_organization_unit_and_period(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $unit = OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $kaprodi->id,
        ]);
        $period = QualityPeriod::factory()->create([
            'created_by' => $kaprodi->id,
            'updated_by' => $kaprodi->id,
        ]);

        SelfAssessment::query()->create([
            'organization_unit_id' => $unit->id,
            'quality_period_id' => $period->id,
            'status' => 'draft',
            'created_by' => (string) $kaprodi->id,
        ]);

        $this->expectException(QueryException::class);

        SelfAssessment::query()->create([
            'organization_unit_id' => $unit->id,
            'quality_period_id' => $period->id,
            'status' => 'draft',
            'created_by' => (string) $kaprodi->id,
        ]);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    private function makeSelfAssessment(User $creator, string $status = 'draft'): SelfAssessment
    {
        $unit = OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $creator->id,
        ]);

        $period = QualityPeriod::factory()->create([
            'created_by' => $creator->id,
            'updated_by' => $creator->id,
        ]);

        return SelfAssessment::query()->create([
            'organization_unit_id' => $unit->id,
            'quality_period_id' => $period->id,
            'status' => $status,
            'created_by' => (string) $creator->id,
        ]);
    }

    private function addDetail(
        SelfAssessment $selfAssessment,
        User $creator,
        float $score,
        ?Indicator $indicator = null,
    ): SelfAssessmentDetail {
        $indicator ??= Indicator::factory()->create([
            'created_by' => $creator->id,
            'updated_by' => $creator->id,
        ]);

        return $selfAssessment->details()->create([
            'indicator_id' => $indicator->id,
            'score' => $score,
            'created_by' => (string) $creator->id,
        ]);
    }

    public function test_detail_score_analysis_strength_and_weakness_are_persisted(): void
{
    $kaprodi = $this->makeUser('kaprodi');
    $selfAssessment = $this->makeSelfAssessment($kaprodi);

    $detail = $selfAssessment->details()->create([
        'indicator_id' => Indicator::factory()->create([
            'created_by' => $kaprodi->id, 'updated_by' => $kaprodi->id,
        ])->id,
        'score' => 75.5,
        'analysis' => 'Capaian sudah sesuai target namun perlu penguatan dokumentasi.',
        'strength' => 'Partisipasi dosen dalam pelaporan tinggi.',
        'weakness' => 'Bukti evaluasi belum terarsip rapi.',
        'created_by' => (string) $kaprodi->id,
    ]);

    $detail->refresh();

    $this->assertSame('75.50', $detail->score);
    $this->assertSame('Capaian sudah sesuai target namun perlu penguatan dokumentasi.', $detail->analysis);
    $this->assertSame('Partisipasi dosen dalam pelaporan tinggi.', $detail->strength);
    $this->assertSame('Bukti evaluasi belum terarsip rapi.', $detail->weakness);

    $detail->update(['analysis' => 'Diperbarui setelah tinjauan.']);
    $this->assertSame('Diperbarui setelah tinjauan.', $detail->fresh()->analysis);
}
}

<?php

namespace Tests\Feature;

use App\Filament\Prodi\Widgets\AchievementScorePerProdiWidget;
use App\Models\AuditEvidence;
use App\Models\AuditScore;
use App\Models\Faculty;
use App\Models\Period;
use App\Models\Standard;
use App\Models\StudyProgram;
use App\Models\SubStandard;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class AchievementScorePerProdiWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_achievement_score_is_grouped_by_study_program_and_averaged_across_active_prodi(): void
    {
        $period = Period::create([
            'name' => '2026',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'is_active' => true,
        ]);
        $viewer = User::factory()->create([
            'is_active' => true,
            'period_id' => $period->id,
        ]);
        $viewer->assignRole('prodi');
        $this->actingAs($viewer);

        $faculty = Faculty::create(['name' => 'Fakultas A']);
        $akuntansi = StudyProgram::create([
            'faculty_id' => $faculty->id,
            'name' => 'Akuntansi',
        ]);
        $informatika = StudyProgram::create([
            'faculty_id' => $faculty->id,
            'name' => 'Informatika',
        ]);

        $standard = Standard::create([
            'code' => 'STD1',
            'name' => 'Standard 1',
        ]);
        $subStandard = SubStandard::create([
            'standard_id' => $standard->id,
            'code' => 'SUB1',
            'indicator' => 'Indicator 1',
            'max_score' => 10,
        ]);

        $akuntansiProdi = User::factory()->create([
            'name' => 'Akuntansi Prodi',
            'is_active' => true,
            'period_id' => $period->id,
            'study_program_id' => $akuntansi->id,
        ]);
        $akuntansiProdi->assignRole('prodi');

        $informatikaProdiA = User::factory()->create([
            'name' => 'Informatika Prodi A',
            'is_active' => true,
            'period_id' => $period->id,
            'study_program_id' => $informatika->id,
        ]);
        $informatikaProdiA->assignRole('prodi');

        $informatikaProdiB = User::factory()->create([
            'name' => 'Informatika Prodi B',
            'is_active' => true,
            'period_id' => $period->id,
            'study_program_id' => $informatika->id,
        ]);
        $informatikaProdiB->assignRole('prodi');

        $inactiveProdi = User::factory()->create([
            'name' => 'Inactive Prodi',
            'is_active' => false,
            'period_id' => $period->id,
            'study_program_id' => $informatika->id,
        ]);
        $inactiveProdi->assignRole('prodi');

        $akuntansiEvidence = AuditEvidence::create([
            'user_id' => $akuntansiProdi->id,
            'standard_id' => $standard->id,
            'sub_standard_id' => $subStandard->id,
            'period_id' => $period->id,
            'title' => 'Akuntansi Evidence',
            'description' => 'Akuntansi Evidence',
            'status' => 'approved',
        ]);
        AuditScore::create([
            'audit_evidence_id' => $akuntansiEvidence->id,
            'user_id' => $akuntansiProdi->id,
            'sub_standard_id' => $subStandard->id,
            'period_id' => $period->id,
            'auditor_id' => $viewer->id,
            'score' => 40,
        ]);

        $informatikaEvidenceA = AuditEvidence::create([
            'user_id' => $informatikaProdiA->id,
            'standard_id' => $standard->id,
            'sub_standard_id' => $subStandard->id,
            'period_id' => $period->id,
            'title' => 'Informatika Evidence A',
            'description' => 'Informatika Evidence A',
            'status' => 'approved',
        ]);
        AuditScore::create([
            'audit_evidence_id' => $informatikaEvidenceA->id,
            'user_id' => $informatikaProdiA->id,
            'sub_standard_id' => $subStandard->id,
            'period_id' => $period->id,
            'auditor_id' => $viewer->id,
            'score' => 80,
        ]);

        $informatikaEvidenceB = AuditEvidence::create([
            'user_id' => $informatikaProdiB->id,
            'standard_id' => $standard->id,
            'sub_standard_id' => $subStandard->id,
            'period_id' => $period->id,
            'title' => 'Informatika Evidence B',
            'description' => 'Informatika Evidence B',
            'status' => 'approved',
        ]);
        AuditScore::create([
            'audit_evidence_id' => $informatikaEvidenceB->id,
            'user_id' => $informatikaProdiB->id,
            'sub_standard_id' => $subStandard->id,
            'period_id' => $period->id,
            'auditor_id' => $viewer->id,
            'score' => 100,
        ]);

        $inactiveEvidence = AuditEvidence::create([
            'user_id' => $inactiveProdi->id,
            'standard_id' => $standard->id,
            'sub_standard_id' => $subStandard->id,
            'period_id' => $period->id,
            'title' => 'Inactive Evidence',
            'description' => 'Inactive Evidence',
            'status' => 'approved',
        ]);
        AuditScore::create([
            'audit_evidence_id' => $inactiveEvidence->id,
            'user_id' => $inactiveProdi->id,
            'sub_standard_id' => $subStandard->id,
            'period_id' => $period->id,
            'auditor_id' => $viewer->id,
            'score' => 0,
        ]);

        $data = $this->widgetData();

        $this->assertSame(['Akuntansi', 'Informatika'], $data['labels']);
        $this->assertSame([40.0, 90.0], $data['datasets'][1]['data']);
    }

    private function widgetData(): array
    {
        $widget = new AchievementScorePerProdiWidget;
        $method = new ReflectionMethod($widget, 'getData');
        $method->setAccessible(true);

        return $method->invoke($widget);
    }
}

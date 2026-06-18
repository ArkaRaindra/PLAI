<?php

namespace Tests\Feature;

use App\Filament\Prodi\Resources\AuditScores\Pages\ListAuditScores;
use App\Filament\Prodi\Widgets\AchievementScorePerProdiWidget;
use App\Filament\Prodi\Widgets\RadarScoreWidget;
use App\Filament\Prodi\Widgets\StatusKeberhasilanWidget;
use App\Models\AuditEvidence;
use App\Models\AuditScore;
use App\Models\Period;
use App\Models\Standard;
use App\Models\SubStandard;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use ReflectionMethod;
use Tests\TestCase;

class StatusKeberhasilanWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_diagram_pencapaian_registers_widgets_in_required_order(): void
    {
        $method = new ReflectionMethod(ListAuditScores::class, 'getHeaderWidgets');
        $widgets = $method->invoke(app(ListAuditScores::class));

        $achievementIndex = array_search(AchievementScorePerProdiWidget::class, $widgets, true);
        $radarIndex = array_search(RadarScoreWidget::class, $widgets, true);
        $statusIndex = array_search(StatusKeberhasilanWidget::class, $widgets, true);

        $this->assertNotFalse($achievementIndex);
        $this->assertNotFalse($radarIndex);
        $this->assertNotFalse($statusIndex);
        $this->assertGreaterThan($achievementIndex, $radarIndex);
        $this->assertGreaterThan($radarIndex, $statusIndex);
    }

    public function test_status_keberhasilan_widget_shows_user_prodi_status_with_badges(): void
    {
        $period = Period::create([
            'name' => '2026',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'period_id' => $period->id,
            'is_active' => true,
        ]);
        $user->assignRole('prodi');

        $standardOne = Standard::create([
            'code' => 'STD1',
            'name' => 'Standard 1',
        ]);
        $standardTwo = Standard::create([
            'code' => 'STD2',
            'name' => 'Standard 2',
        ]);

        $subStandardOne = SubStandard::create([
            'standard_id' => $standardOne->id,
            'code' => 'SUB1',
            'indicator' => 'Indicator 1',
            'max_score' => 4,
        ]);
        $subStandardTwo = SubStandard::create([
            'standard_id' => $standardTwo->id,
            'code' => 'SUB2',
            'indicator' => 'Indicator 2',
            'max_score' => 4,
        ]);

        $evidenceOne = AuditEvidence::create([
            'user_id' => $user->id,
            'standard_id' => $standardOne->id,
            'sub_standard_id' => $subStandardOne->id,
            'period_id' => $period->id,
            'title' => 'Evidence 1',
            'file_path' => 'files/evidence-1.pdf',
            'status' => 'approved',
        ]);
        $evidenceTwo = AuditEvidence::create([
            'user_id' => $user->id,
            'standard_id' => $standardTwo->id,
            'sub_standard_id' => $subStandardTwo->id,
            'period_id' => $period->id,
            'title' => 'Evidence 2',
            'status' => 'approved',
        ]);

        $evidenceOne->subStandards()->sync([$subStandardOne->id]);
        $evidenceTwo->subStandards()->sync([$subStandardTwo->id]);

        AuditScore::create([
            'user_id' => $user->id,
            'sub_standard_id' => $subStandardOne->id,
            'period_id' => $period->id,
            'score' => 4,
            'audit_evidence_id' => $evidenceOne->id,
        ]);
        AuditScore::create([
            'user_id' => $user->id,
            'sub_standard_id' => $subStandardTwo->id,
            'period_id' => $period->id,
            'score' => 2,
            'audit_evidence_id' => $evidenceTwo->id,
        ]);

        $this->actingAs($user);

        Livewire::test(StatusKeberhasilanWidget::class)
            ->assertSee('Nilai assesmen Kecukupan')
            ->assertSee('4,00')
            ->assertSee('Syarat perlu terakreditasi')
            ->assertSee('Terpenuhi')
            ->assertSee('Diperlukan 4,00 dari 8,00')
            ->assertSee('Syarat perlu Peringkat Unggul')
            ->assertSee('Belum Terpenuhi')
            ->assertSee('Diperlukan 7,20 dari 8,00')
            ->assertSee('Syarat perlu peringkat Baik Sekali')
            ->assertSee('Terpenuhi')
            ->assertSee('Diperlukan 6,00 dari 8,00')
            ->assertSee('2 Element')
            ->assertSee('1 Berkas');
    }
}

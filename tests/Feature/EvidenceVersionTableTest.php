<?php

namespace Tests\Feature;

use App\Events\EvidenceRecorded;
use App\Filament\SuperAdmin\Resources\Realizations\Pages\ViewRealization;
use App\Models\EvidenceVersions;
use App\Models\Indicator;
use App\Models\IndicatorOwner;
use App\Models\OrganizationUnit;
use App\Models\Realization;
use App\Models\Target;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EvidenceVersionTableTest extends TestCase
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

    public function test_view_realization_shows_evidence_version_table_with_lihat_action(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$realization, $organizationUnit] = $this->createRealizationContext($admin);

        $this->actingAs($admin);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti Awal',
            description: 'Deskripsi',
            type: 'url',
            filePath: null,
            urlPath: 'https://example.com/v1',
        );

        Livewire::test(ViewRealization::class, ['record' => $realization->id])
            ->assertOk()
            ->assertSee('Bukti')
            ->assertSee('1.0')
            ->assertSee('Bukti Awal')
            ->assertTableActionExists('viewEvidence')
            ->assertTableActionVisible('viewEvidence', EvidenceVersions::query()->sole())
            ->assertTableActionExists('previewEvidence')
            ->assertTableActionVisible('previewEvidence', EvidenceVersions::query()->sole());
    }

    public function test_edit_action_visible_only_when_single_version_exists(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$realization, $organizationUnit] = $this->createRealizationContext($admin);

        $this->actingAs($admin);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti File Awal',
            description: null,
            type: 'file',
            filePath: 'evidences/v1.pdf',
            urlPath: null,
        );

        $version = EvidenceVersions::query()->sole();

        Livewire::test(ViewRealization::class, ['record' => $realization->id])
            ->assertTableActionVisible('editEvidence', $version);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti File Baru',
            description: null,
            type: 'file',
            filePath: 'evidences/v2.pdf',
            urlPath: null,
        );

        $version->refresh();

        Livewire::test(ViewRealization::class, ['record' => $realization->id])
            ->assertTableActionHidden('editEvidence', $version);
    }

    /**
     * @return array{0: Realization, 1: OrganizationUnit}
     */
    private function createRealizationContext(User $admin): array
    {
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        IndicatorOwner::query()->create([
            'indicator_id' => $indicator->id,
            'organization_unit_id' => $organizationUnit->id,
            'is_primary' => true,
            'created_by' => (string) $admin->id,
        ]);

        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $indicator->standardVersion->quality_period_id,
            'target_value' => 100,
            'created_by' => (string) $admin->id,
        ]);

        $realization = Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $organizationUnit->id,
            'actual_value' => 10,
            'score' => 80,
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);

        return [$realization, $organizationUnit];
    }
}

<?php

namespace Tests\Feature;

use App\Events\EvidenceRecorded;
use App\Models\Evidences;
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
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EvidenceFileDownloadTest extends TestCase
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

    public function test_authenticated_user_can_download_private_evidence_file(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('evidences/sample.pdf', 'pdf-content');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$realization, $organizationUnit] = $this->createRealizationContext($admin);

        $this->actingAs($admin);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti File',
            description: null,
            type: 'file',
            filePath: 'evidences/sample.pdf',
            urlPath: null,
        );

        $version = EvidenceVersions::query()->sole();

        $this->get(route('evidence-files.download', $version))
            ->assertOk()
            ->assertDownload('sample.pdf');
    }

    public function test_authenticated_user_can_preview_private_evidence_file_inline(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('evidences/sample.pdf', 'pdf-content');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$realization, $organizationUnit] = $this->createRealizationContext($admin);

        $this->actingAs($admin);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti File',
            description: null,
            type: 'file',
            filePath: 'evidences/sample.pdf',
            urlPath: null,
        );

        $version = EvidenceVersions::query()->sole();

        $response = $this->get(route('evidence-files.preview', $version));

        $response->assertOk();
        $this->assertStringContainsString('inline', (string) $response->headers->get('content-disposition'));
    }

    public function test_guest_cannot_download_evidence_file(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('evidences/sample.pdf', 'pdf-content');

        $admin = User::factory()->create(['is_active' => true]);
        [$realization, $organizationUnit] = $this->createRealizationContext($admin);

        $this->actingAs($admin);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti File',
            description: null,
            type: 'file',
            filePath: 'evidences/sample.pdf',
            urlPath: null,
        );

        $version = EvidenceVersions::query()->sole();

        auth()->logout();

        $this->getJson(route('evidence-files.download', $version))
            ->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_download_evidence_file(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('evidences/sample.pdf', 'pdf-content');

        $admin = User::factory()->create(['is_active' => true]);
        $viewer = User::factory()->create(['is_active' => true]);
        [$realization, $organizationUnit] = $this->createRealizationContext($admin);

        $this->actingAs($admin);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti File',
            description: null,
            type: 'file',
            filePath: 'evidences/sample.pdf',
            urlPath: null,
        );

        $version = EvidenceVersions::query()->sole();

        $this->actingAs($viewer);

        $this->get(route('evidence-files.download', $version))
            ->assertNotFound();
    }

    public function test_authenticated_user_can_download_evidence_file(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('evidences/sample.pdf', 'pdf-content');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $unit = $this->createUnit($admin);

        $evidence = Evidences::query()->create([
            'organization_unit_id' => $unit->id,
            'title' => 'Bukti File',
            'type' => 'file',
            'file_path' => 'evidences/sample.pdf',
            'created_by' => (string) $admin->id,
            'updated_by' => (string) $admin->id,
        ]);

        $this->get(route('evidences.download', $evidence))
            ->assertOk()
            ->assertDownload('sample.pdf');
    }

    public function test_download_returns_not_found_for_url_type_or_missing_file(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $unit = $this->createUnit($admin);

        $evidence = Evidences::query()->create([
            'organization_unit_id' => $unit->id,
            'title' => 'Bukti URL',
            'type' => 'url',
            'url_path' => 'https://example.com',
            'created_by' => (string) $admin->id,
            'updated_by' => (string) $admin->id,
        ]);

        $this->get(route('evidences.download', $evidence))
            ->assertNotFound();
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

    private function createUnit(User $admin): OrganizationUnit
    {
        return OrganizationUnit::query()->create([
            'code' => 'FT-'.fake()->unique()->numerify('###'),
            'name' => 'Fakultas Teknik',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Evidences;
use App\Models\EvidenceVersions;
use App\Models\OrganizationUnit;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\StandardVersion;
use App\Models\User;
use App\Services\Versioning\VersionGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersionGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_one_point_zero_when_no_versions_exist(): void
    {
        $admin = User::factory()->create();
        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        $evidence = Evidences::query()->create([
            'organization_unit_id' => $organizationUnit->id,
            'title' => 'Test',
            'created_by' => (string) $admin->id,
        ]);

        $service = app(VersionGeneratorService::class);

        $this->assertSame('1.0', $service->next(
            EvidenceVersions::class,
            'evidence_id',
            $evidence->id,
        ));
    }

    public function test_increments_major_minor_version(): void
    {
        $admin = User::factory()->create();
        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        $evidence = Evidences::query()->create([
            'organization_unit_id' => $organizationUnit->id,
            'title' => 'Test',
            'current_version' => '1.0',
            'created_by' => (string) $admin->id,
        ]);

        EvidenceVersions::query()->create([
            'evidence_id' => $evidence->id,
            'version' => '1.0',
            'type' => 'url',
            'url_path' => 'https://example.com',
            'uploaded_by' => $admin->id,
        ]);

        $service = app(VersionGeneratorService::class);

        $this->assertSame('2.0', $service->next(
            EvidenceVersions::class,
            'evidence_id',
            $evidence->id,
        ));

        EvidenceVersions::query()->create([
            'evidence_id' => $evidence->id,
            'version' => '2.0',
            'type' => 'file',
            'file_path' => 'evidences/sample.pdf',
            'uploaded_by' => $admin->id,
        ]);

        $this->assertSame('3.0', $service->next(
            EvidenceVersions::class,
            'evidence_id',
            $evidence->id,
        ));
    }

    public function test_scopes_versions_by_foreign_key(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $standardA = Standard::create([
            'code' => 'A',
            'name' => 'Standard A',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $standardB = Standard::create([
            'code' => 'B',
            'name' => 'Standard B',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersion::factory()->create([
            'standard_id' => $standardA->id,
            'version' => '2.0',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $service = app(VersionGeneratorService::class);

        $this->assertSame('1.0', $service->next(
            StandardVersion::class,
            'standard_id',
            $standardB->id,
        ));
    }
}

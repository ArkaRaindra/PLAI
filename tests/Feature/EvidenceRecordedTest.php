<?php

namespace Tests\Feature;

use App\Events\EvidenceRecorded;
use App\Models\EvidenceLinks;
use App\Models\Evidences;
use App\Models\EvidenceVersions;
use App\Models\Indicator;
use App\Models\IndicatorOwner;
use App\Models\OrganizationUnit;
use App\Models\Realization;
use App\Models\Target;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvidenceRecordedTest extends TestCase
{
    use RefreshDatabase;

    public function test_listener_is_registered(): void
    {
        $this->assertTrue(
            app('events')->hasListeners(EvidenceRecorded::class),
        );
    }

    public function test_dispatch_creates_evidence_records_linked_to_realization_with_url(): void
    {
        $admin = User::factory()->create();
        [$realization, $organizationUnit] = $this->createRealizationContext($admin);

        $this->actingAs($admin);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti Realisasi',
            description: 'Deskripsi bukti',
            type: 'url',
            filePath: null,
            urlPath: 'https://example.com/evidence.pdf',
        );

        $this->assertDatabaseHas('evidences', [
            'organization_unit_id' => $organizationUnit->id,
            'title' => 'Bukti Realisasi',
            'description' => 'Deskripsi bukti',
            'current_version' => '1.0',
        ]);

        $evidence = Evidences::query()->sole();

        $this->assertDatabaseHas('evidence_versions', [
            'evidence_id' => $evidence->id,
            'version' => '1.0',
            'type' => 'url',
            'url_path' => 'https://example.com/evidence.pdf',
            'uploaded_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('evidence_links', [
            'evidence_id' => $evidence->id,
            'reference_type' => 'realization',
            'reference_id' => $realization->id,
        ]);
    }

    public function test_dispatch_creates_evidence_records_with_file_path(): void
    {
        $admin = User::factory()->create();
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

        $evidence = Evidences::query()->sole();

        $this->assertDatabaseHas('evidence_versions', [
            'evidence_id' => $evidence->id,
            'type' => 'file',
            'file_path' => 'evidences/sample.pdf',
        ]);
    }

    public function test_realization_exposes_evidence_form_data(): void
    {
        $admin = User::factory()->create();
        [$realization, $organizationUnit] = $this->createRealizationContext($admin);

        $this->actingAs($admin);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti Realisasi',
            description: 'Deskripsi bukti',
            type: 'url',
            filePath: null,
            urlPath: 'https://example.com/evidence.pdf',
        );

        $realization->refresh();

        $this->assertSame([
            'title' => 'Bukti Realisasi',
            'description' => 'Deskripsi bukti',
            'type' => 'url',
            'file_path' => null,
            'url_path' => 'https://example.com/evidence.pdf',
        ], $realization->evidenceFormData());
    }

    public function test_dispatch_updates_url_without_bumping_version(): void
    {
        $admin = User::factory()->create();
        [$realization, $organizationUnit] = $this->createRealizationContext($admin);

        $this->actingAs($admin);

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti Awal',
            description: null,
            type: 'url',
            filePath: null,
            urlPath: 'https://example.com/v1',
        );

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti Diperbarui',
            description: 'Versi baru',
            type: 'url',
            filePath: null,
            urlPath: 'https://example.com/v2',
        );

        $this->assertSame(1, Evidences::query()->count());
        $this->assertSame(1, EvidenceLinks::query()->count());
        $this->assertSame(1, EvidenceVersions::query()->count());

        $evidence = Evidences::query()->sole();

        $this->assertSame('Bukti Diperbarui', $evidence->title);
        $this->assertSame('Versi baru', $evidence->description);
        $this->assertSame('1.0', $evidence->current_version);

        $this->assertDatabaseHas('evidence_versions', [
            'evidence_id' => $evidence->id,
            'version' => '1.0',
            'url_path' => 'https://example.com/v2',
        ]);
    }

    public function test_dispatch_creates_new_version_when_new_file_uploaded(): void
    {
        $admin = User::factory()->create();
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

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti File Baru',
            description: null,
            type: 'file',
            filePath: 'evidences/v2.pdf',
            urlPath: null,
        );

        $this->assertSame(2, EvidenceVersions::query()->count());

        $evidence = Evidences::query()->sole();

        $this->assertSame('2.0', $evidence->current_version);
        $this->assertDatabaseHas('evidence_versions', [
            'evidence_id' => $evidence->id,
            'version' => '2.0',
            'file_path' => 'evidences/v2.pdf',
        ]);
    }

    public function test_file_then_url_keeps_single_version(): void
    {
        $admin = User::factory()->create();
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

        EvidenceRecorded::dispatch(
            reference: $realization,
            organizationUnitId: $organizationUnit->id,
            title: 'Bukti URL',
            description: null,
            type: 'url',
            filePath: null,
            urlPath: 'https://example.com/evidence.pdf',
        );

        $this->assertSame(1, EvidenceVersions::query()->count());

        $evidence = Evidences::query()->sole();

        $this->assertSame('1.0', $evidence->current_version);
        $this->assertDatabaseHas('evidence_versions', [
            'evidence_id' => $evidence->id,
            'version' => '1.0',
            'type' => 'url',
            'url_path' => 'https://example.com/evidence.pdf',
        ]);
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

<?php

namespace Tests\Feature;

use App\Filament\SuperAdmin\Resources\Standards\StandardResource;
use App\Models\QualityPeriod;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\StandardVersion;
use App\Models\User;
use App\Support\StandardVersionPersister;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandardVersionTest extends TestCase
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

    public function test_sync_without_version_removes_root_and_descendant_versions(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $root = Standard::create([
            'code' => 'ROOT',
            'name' => 'Root Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $child = Standard::create([
            'code' => 'CHILD',
            'name' => 'Child Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ], $root);

        $versionData = [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ];

        StandardVersionPersister::sync($root, $versionData);
        StandardVersionPersister::inheritFromParent($child, $root);

        $this->assertDatabaseCount('standard_versions', 2);

        StandardVersionPersister::sync($root, [
            'include_standard_version' => false,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriod' => [],
            'standardVersion' => [],
        ]);

        $this->assertDatabaseCount('standard_versions', 0);
    }

    public function test_create_standard_with_existing_quality_period(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'WITH-VER',
            'name' => 'Standard With Version',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($standard, [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ]);

        $this->assertDatabaseHas('standard_versions', [
            'standard_id' => $standard->id,
            'quality_period_id' => $period->id,
            'version' => '1.0',
        ]);
        $this->assertDatabaseCount('quality_periods', 1);
    }

    public function test_create_standard_with_new_quality_period(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'NEW-PER',
            'name' => 'Standard With New Period',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($standard, [
            'include_standard_version' => true,
            'quality_period_mode' => 'new',
            'quality_period_id' => null,
            'qualityPeriod' => [
                'code' => 'QP-2026',
                'name' => 'Periode 2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'status' => 'draft',
                'is_active' => true,
            ],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ]);

        $this->assertDatabaseHas('quality_periods', [
            'code' => 'QP-2026',
            'name' => 'Periode 2026',
        ]);
        $this->assertDatabaseHas('standard_versions', [
            'standard_id' => $standard->id,
            'version' => '1.0',
        ]);
    }

    public function test_edit_removes_standard_version_when_toggle_off(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'DEL-VER',
            'name' => 'Standard To Unlink',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersion::factory()->create([
            'standard_id' => $standard->id,
            'quality_period_id' => $period->id,
            'version' => '1.0',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $standard->refresh();

        StandardVersionPersister::sync($standard, [
            'include_standard_version' => false,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriod' => [],
            'standardVersion' => [],
        ]);

        $this->assertDatabaseCount('standard_versions', 0);
    }

    public function test_standards_can_be_filtered_by_quality_period(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $periodA = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $periodB = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standardA = Standard::create([
            'code' => 'STD-A',
            'name' => 'Standard A',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $standardB = Standard::create([
            'code' => 'STD-B',
            'name' => 'Standard B',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersion::factory()->create([
            'standard_id' => $standardA->id,
            'quality_period_id' => $periodA->id,
            'version' => '1.0',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersion::factory()->create([
            'standard_id' => $standardB->id,
            'quality_period_id' => $periodB->id,
            'version' => '1.0',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $filtered = Standard::query()
            ->where('standard_source_id', $source->id)
            ->whereHas('standardVersions', fn ($query) => $query->where('quality_period_id', $periodA->id))
            ->pluck('code')
            ->all();

        $this->assertSame(['STD-A'], $filtered);
    }

    public function test_standards_without_filter_show_all_in_source(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        Standard::create([
            'code' => 'STD-1',
            'name' => 'Standard 1',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Standard::create([
            'code' => 'STD-2',
            'name' => 'Standard 2',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $count = Standard::query()
            ->where('standard_source_id', $source->id)
            ->count();

        $this->assertSame(2, $count);
    }

    public function test_to_form_data_prefills_version_fields(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'FILL',
            'name' => 'Standard Fill',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $version = StandardVersion::factory()->create([
            'standard_id' => $standard->id,
            'quality_period_id' => $period->id,
            'version' => 'v2',
            'status' => 'active',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $formData = StandardVersionPersister::toFormData($version);

        $this->assertTrue($formData['include_standard_version']);
        $this->assertSame('existing', $formData['quality_period_mode']);
        $this->assertSame($period->id, $formData['quality_period_id']);
        $this->assertSame('v2', $formData['standardVersion']['version']);
    }

    public function test_from_parent_throws_when_parent_has_no_version(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $parent = Standard::create([
            'code' => 'NO-VER',
            'name' => 'Parent Without Version',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        StandardVersionPersister::fromParent($parent);
    }

    public function test_child_inherits_parent_version_on_create(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $parent = Standard::create([
            'code' => 'PARENT',
            'name' => 'Parent Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($parent, [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ]);

        $child = Standard::create([
            'code' => 'CHILD',
            'name' => 'Child Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ], $parent);

        StandardVersionPersister::inheritFromParent($child, $parent);

        $this->assertDatabaseHas('standard_versions', [
            'standard_id' => $child->id,
            'quality_period_id' => $period->id,
            'version' => '1.0',
        ]);
    }

    public function test_root_version_change_cascades_to_descendants(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $periodA = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $periodB = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $root = Standard::create([
            'code' => 'ROOT',
            'name' => 'Root Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $child = Standard::create([
            'code' => 'CHILD',
            'name' => 'Child Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ], $root);

        $grandchild = Standard::create([
            'code' => 'GRAND',
            'name' => 'Grandchild Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ], $child);

        $initialVersionData = [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $periodA->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ];

        StandardVersionPersister::sync($root, $initialVersionData);
        StandardVersionPersister::inheritFromParent($child, $root);
        StandardVersionPersister::inheritFromParent($grandchild, $child);

        StandardVersionPersister::sync($root, [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $periodB->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'active',
                'is_active' => true,
            ],
        ]);

        $child->refresh()->load('standardVersion');
        $grandchild->refresh()->load('standardVersion');

        $this->assertSame('2.0', $root->fresh()->standardVersion?->version);
        $this->assertSame('2.0', $child->standardVersion?->version);
        $this->assertSame('2.0', $grandchild->standardVersion?->version);
        $this->assertSame($periodB->id, $child->standardVersion?->quality_period_id);
        $this->assertSame($periodB->id, $grandchild->standardVersion?->quality_period_id);
    }

    public function test_child_standard_update_does_not_change_inherited_version(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $parent = Standard::create([
            'code' => 'PARENT',
            'name' => 'Parent Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($parent, [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ]);

        $child = Standard::create([
            'code' => 'CHILD',
            'name' => 'Child Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ], $parent);

        StandardVersionPersister::inheritFromParent($child, $parent);

        $child->update(['name' => 'Updated Child Name']);

        $this->assertSame('1.0', $child->fresh()->standardVersion?->version);
        $this->assertSame($period->id, $child->fresh()->standardVersion?->quality_period_id);
    }

    public function test_deleting_standard_cascades_to_standard_versions(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $parent = Standard::create([
            'code' => 'PARENT',
            'name' => 'Parent Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($parent, [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ]);

        $child = Standard::create([
            'code' => 'CHILD',
            'name' => 'Child Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ], $parent);

        StandardVersionPersister::inheritFromParent($child, $parent);

        $childVersionId = $child->fresh()->standardVersion?->id;
        $this->assertNotNull($childVersionId);

        $child->delete();

        $this->assertDatabaseMissing('standards', ['id' => $child->id]);
        $this->assertDatabaseMissing('standard_versions', ['id' => $childVersionId]);
        $this->assertDatabaseHas('standards', ['id' => $parent->id]);
        $this->assertDatabaseCount('standard_versions', 1);
    }

    public function test_edit_same_quality_period_does_not_bump_version(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'EDIT-VER',
            'name' => 'Standard Edit Version',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($standard, [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ]);

        StandardVersionPersister::sync($standard->fresh(), [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->addDay()->toDateString(),
                'status' => 'active',
                'is_active' => true,
            ],
        ]);

        $this->assertDatabaseCount('standard_versions', 1);
        $this->assertSame('1.0', $standard->fresh()->standardVersion?->version);
        $this->assertSame('active', $standard->fresh()->standardVersion?->status->value);
    }

    public function test_quality_period_change_creates_new_version_row(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $periodA = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $periodB = QualityPeriod::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'NEW-VER',
            'name' => 'Standard New Version',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($standard, [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $periodA->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ]);

        StandardVersionPersister::sync($standard->fresh(), [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $periodB->id,
            'qualityPeriod' => [],
            'standardVersion' => [
                'start_date' => now()->toDateString(),
                'status' => 'active',
                'is_active' => true,
            ],
        ]);

        $this->assertDatabaseCount('standard_versions', 2);
        $this->assertSame('2.0', $standard->fresh()->standardVersion?->version);
        $this->assertSame($periodB->id, $standard->fresh()->standardVersion?->quality_period_id);
    }

    public function test_create_child_standard_redirects_when_parent_has_no_version(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $parent = Standard::create([
            'code' => 'PARENT',
            'name' => 'Parent Without Version',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(StandardResource::getCreateUrl($source->id, $parent->id))
            ->assertRedirect(StandardResource::getManageStandardsUrl($source->id));
    }
}

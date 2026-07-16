<?php

namespace Tests\Feature;

use App\Models\AuditChecklistTemplate;
use App\Models\QualityPeriod;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\StandardVersion;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditChecklistTemplateTest extends TestCase
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

    public function test_a_template_can_be_created_with_items_in_sequence(): void
    {
        $standardVersion = $this->makeStandardVersion();

        $template = AuditChecklistTemplate::query()->create([
            'name' => 'Checklist Audit Prodi',
            'version_no' => 1,
        ]);

        $template->items()->create([
            'standard_version_id' => $standardVersion->id,
            'question' => 'Apakah dokumen kurikulum tersedia?',
        ]);

        $template->items()->create([
            'standard_version_id' => $standardVersion->id,
            'question' => 'Apakah RPS setiap mata kuliah lengkap?',
        ]);

        $template->refresh();

        $this->assertSame(1, $template->version_no);
        $this->assertSame(2, $template->items()->count());
        $this->assertSame(1, $template->items()->first()->sequence);
        $this->assertSame(2, $template->items()->skip(1)->first()->sequence);
    }

    public function test_only_the_latest_version_per_name_is_active(): void
    {
        AuditChecklistTemplate::query()->create(['name' => 'Checklist Audit Prodi', 'version_no' => 1]);
        $v2 = AuditChecklistTemplate::query()->create(['name' => 'Checklist Audit Prodi', 'version_no' => 2]);

        $activeNames = AuditChecklistTemplate::query()->active()->pluck('version_no', 'name')->all();

        $this->assertSame(['Checklist Audit Prodi' => 2], $activeNames);
        $this->assertTrue($v2->isActiveVersion());
    }

    public function test_creating_a_new_version_clones_items_and_increments_version_no(): void
    {
        $standardVersion = $this->makeStandardVersion();

        $v1 = AuditChecklistTemplate::query()->create([
            'name' => 'Checklist Audit Prodi',
            'version_no' => 1,
        ]);

        $v1->items()->create([
            'standard_version_id' => $standardVersion->id,
            'question' => 'Apakah dokumen kurikulum tersedia?',
        ]);
        $v1->items()->create([
            'standard_version_id' => $standardVersion->id,
            'question' => 'Apakah RPS setiap mata kuliah lengkap?',
        ]);

        $v2 = AuditChecklistTemplate::createNewVersion($v1);

        $this->assertSame('Checklist Audit Prodi', $v2->name);
        $this->assertSame(2, $v2->version_no);
        $this->assertSame(2, $v2->items()->count());
        $this->assertNotSame($v1->id, $v2->id);

        // v1's items must remain untouched (a true copy, not a move).
        $this->assertSame(2, $v1->fresh()->items()->count());

        $this->assertFalse($v1->fresh()->isActiveVersion());
        $this->assertTrue($v2->isActiveVersion());
    }

    public function test_auditor_can_manage_templates_while_dosen_cannot(): void
    {
        $auditor = User::factory()->create(['is_active' => true]);
        $auditor->assignRole('auditor');

        $dosen = User::factory()->create(['is_active' => true]);
        $dosen->assignRole('dosen');

        $template = AuditChecklistTemplate::query()->create([
            'name' => 'Checklist Audit Prodi',
            'version_no' => 1,
        ]);

        $this->assertTrue($auditor->can('create', AuditChecklistTemplate::class));
        $this->assertTrue($auditor->can('update', $template));
        $this->assertTrue($auditor->can('delete', $template));

        $this->assertFalse($dosen->can('create', AuditChecklistTemplate::class));
        $this->assertFalse($dosen->can('update', $template));
        $this->assertFalse($dosen->can('delete', $template));
    }

    private function makeStandardVersion(): StandardVersion
    {
        $source = StandardSource::query()->create([
            'code' => 'SRC-'.fake()->unique()->numerify('###'),
            'name' => 'Sumber '.fake()->word(),
            'is_active' => true,
            'is_external' => false,
        ]);

        $standard = Standard::query()->create([
            'code' => 'STD-'.fake()->unique()->numerify('###'),
            'name' => 'Standar '.fake()->word(),
            'standard_source_id' => $source->id,
            'is_active' => true,
        ]);

        $qualityPeriod = QualityPeriod::query()->create([
            'code' => 'QP-'.fake()->unique()->numerify('###'),
            'name' => 'Periode '.fake()->year(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);

        return StandardVersion::query()->create([
            'standard_id' => $standard->id,
            'quality_period_id' => $qualityPeriod->id,
            'version' => '1.0',
            'start_date' => now()->toDateString(),
            'status' => 'active',
            'is_active' => true,
        ]);
    }
}

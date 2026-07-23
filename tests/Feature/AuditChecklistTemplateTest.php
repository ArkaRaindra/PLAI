<?php

namespace Tests\Feature;

use App\Models\AuditChecklistTemplate;
use App\Models\QualityPeriod;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\StandardVersion;
use App\Services\Versioning\VersionGeneratorService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
            'version_no' => '1.0',
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

        $this->assertSame('1.0', $template->version_no);
        $this->assertSame(2, $template->items()->count());
        $this->assertSame(1, $template->items()->first()->sequence);
        $this->assertSame(2, $template->items()->skip(1)->first()->sequence);
    }

    public function test_version_no_is_stored_as_a_string_version(): void
    {
        $template = AuditChecklistTemplate::query()->create([
            'name' => 'Checklist Audit Prodi',
            'version_no' => '2.0',
        ]);

        $this->assertSame('2.0', $template->fresh()->version_no);
    }

    public function test_creating_a_new_version_generates_the_next_version_and_clones_items(): void
    {
        $standardVersion = $this->makeStandardVersion();

        $v1 = AuditChecklistTemplate::query()->create([
            'name' => 'Checklist Audit Prodi',
            'version_no' => '1.0',
        ]);

        $v1->items()->create([
            'standard_version_id' => $standardVersion->id,
            'question' => 'Apakah dokumen kurikulum tersedia?',
        ]);
        $v1->items()->create([
            'standard_version_id' => $standardVersion->id,
            'question' => 'Apakah RPS setiap mata kuliah lengkap?',
        ]);

        $versionNo = app(VersionGeneratorService::class)->next(
            AuditChecklistTemplate::class,
            'name',
            $v1->name,
            'version_no',
        );

        $v2 = AuditChecklistTemplate::query()->create([
            'name' => $v1->name,
            'version_no' => $versionNo,
        ]);

        foreach ($v1->items()->get() as $item) {
            $v2->items()->create([
                'standard_version_id' => $item->standard_version_id,
                'question' => $item->question,
                'sequence' => $item->sequence,
            ]);
        }

        $this->assertSame('2.0', $v2->version_no);
        $this->assertSame(2, $v2->items()->count());
        $this->assertNotSame($v1->id, $v2->id);

        // v1's items must remain untouched (a true copy, not a move).
        $this->assertSame(2, $v1->fresh()->items()->count());
    }

    public function test_only_the_latest_version_per_name_is_selectable(): void
    {
        AuditChecklistTemplate::query()->create(['name' => 'Checklist A', 'version_no' => '1.0']);
        AuditChecklistTemplate::query()->create(['name' => 'Checklist A', 'version_no' => '3.0']);
        AuditChecklistTemplate::query()->create(['name' => 'Checklist A', 'version_no' => '2.0']);
        AuditChecklistTemplate::query()->create(['name' => 'Checklist B', 'version_no' => '1.0']);

        $latest = AuditChecklistTemplate::query()
            ->select('name')
            ->selectRaw('MAX(CAST(version_no AS DECIMAL(10,1))) as max_version')
            ->groupBy('name')
            ->get()
            ->mapWithKeys(fn ($row): array => ["{$row->name}|{$row->max_version}" => true]);

        $selectable = AuditChecklistTemplate::query()
            ->whereIn(DB::raw("CONCAT(name, '|', CAST(version_no AS DECIMAL(10,1)))"), $latest->keys()->values()->all())
            ->whereIn('name', ['Checklist A', 'Checklist B'])
            ->orderBy('name')
            ->orderByDesc(DB::raw('CAST(version_no AS DECIMAL(10,1))'))
            ->get()
            ->map(fn (AuditChecklistTemplate $template): string => "{$template->name} (v{$template->version_no})")
            ->values()
            ->all();

        $this->assertSame([
            'Checklist A (v3.0)',
            'Checklist B (v1.0)',
        ], $selectable);
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

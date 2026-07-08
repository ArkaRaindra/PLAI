<?php

namespace Tests\Unit\Support\TraceabilityLinks;

use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\Target;
use App\Models\TraceabilityLinks;
use App\Models\User;
use App\Support\TraceabilityLinks\TraceabilityLinkPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraceabilityLinkPresenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_relation_label_returns_indonesian_label(): void
    {
        $this->assertSame('Mendefinisikan', TraceabilityLinkPresenter::relationLabel('defines'));
        $this->assertSame('Diukur melalui', TraceabilityLinkPresenter::relationLabel('measured_by'));
    }

    public function test_entity_type_label_returns_indonesian_label(): void
    {
        $this->assertSame('Standar', TraceabilityLinkPresenter::entityTypeLabel('standard'));
        $this->assertSame('Indikator', TraceabilityLinkPresenter::entityTypeLabel('indicator'));
    }

    public function test_icon_for_returns_tabler_icon_per_entity_type(): void
    {
        $this->assertSame('tabler-book-2', TraceabilityLinkPresenter::iconFor('standard'));
        $this->assertSame('tabler-chart-bar', TraceabilityLinkPresenter::iconFor('indicator'));
        $this->assertSame('tabler-flag', TraceabilityLinkPresenter::iconFor('target'));
    }

    public function test_target_display_name_resolves_morph_model(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $standard = Standard::create([
            'code' => 'STD-01',
            'name' => 'Standard Pendidikan',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator = Indicator::factory()->create([
            'name' => 'Indikator Kelulusan',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $link = TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $standard->id,
            'target_type' => 'indicator',
            'target_id' => $indicator->id,
            'relation_type' => 'defines',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        $link->load(['source', 'target']);

        $this->assertSame('Indikator Kelulusan', TraceabilityLinkPresenter::targetDisplayName($link));
        $this->assertSame('Standard Pendidikan', TraceabilityLinkPresenter::sourceDisplayName($link));
    }

    public function test_target_display_name_formats_target_percentage(): void
    {
        $admin = User::factory()->create();
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $indicator->standardVersion->quality_period_id,
            'target_value' => 90,
            'created_by' => (string) $admin->id,
        ]);

        $link = TraceabilityLinks::query()->create([
            'source_type' => 'indicator',
            'source_id' => $indicator->id,
            'target_type' => 'target',
            'target_id' => $target->id,
            'relation_type' => 'defines',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        $link->load(['source', 'target']);

        $this->assertSame('Target 90%', TraceabilityLinkPresenter::targetDisplayName($link));
    }

    public function test_format_metadata_returns_human_readable_summary(): void
    {
        $link = new TraceabilityLinks([
            'metadata' => [
                'name' => 'Root Standard',
                'code' => 'ROOT',
                'standard_source_id' => 1,
            ],
        ]);

        $this->assertSame('Nama: Root Standard · Kode: ROOT', TraceabilityLinkPresenter::formatMetadata($link));
    }

    public function test_journey_lines_show_source_relation_and_target(): void
    {
        $admin = User::factory()->create();
        $indicator = Indicator::factory()->create([
            'name' => 'Indikator Kelulusan',
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

        $link = TraceabilityLinks::query()->create([
            'source_type' => 'indicator',
            'source_id' => $indicator->id,
            'target_type' => 'organization_unit',
            'target_id' => $organizationUnit->id,
            'relation_type' => 'supported_by',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        $link->load(['source', 'target']);

        $this->assertSame([
            'Indikator Kelulusan',
            '↓',
            'Didukung oleh',
            '↓',
            'Fakultas Teknik',
        ], TraceabilityLinkPresenter::journeyLines($link));
    }

    public function test_timeline_at_falls_back_to_created_at(): void
    {
        $admin = User::factory()->create();
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $link = TraceabilityLinks::query()->create([
            'source_type' => 'indicator',
            'source_id' => $indicator->id,
            'target_type' => 'indicator',
            'target_id' => $indicator->id,
            'relation_type' => 'related_to',
            'performed_at' => null,
            'created_by' => $admin->id,
        ]);

        $this->assertNotNull($link->created_at);
        $this->assertTrue(TraceabilityLinkPresenter::timelineAt($link)->equalTo($link->created_at));
    }
}

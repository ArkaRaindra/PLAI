<?php

namespace Tests\Feature;

use App\Filament\SuperAdmin\Pages\ManageStandards;
use App\Filament\SuperAdmin\Resources\Indicators\IndicatorResource;
use App\Filament\SuperAdmin\Resources\Indicators\Pages\EditIndicator;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorTest extends TestCase
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

    public function test_indicator_list_redirects_without_standard_id(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        $this->actingAs($admin)
            ->get('/super-admin/indicators')
            ->assertRedirect(ManageStandards::getUrl());
    }

    public function test_indicator_list_is_scoped_to_standard(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

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

        Indicator::factory()->create([
            'standard_id' => $standardA->id,
            'code' => 'IND-A',
            'name' => 'Indikator A',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Indicator::factory()->create([
            'standard_id' => $standardB->id,
            'code' => 'IND-B',
            'name' => 'Indikator B',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(IndicatorResource::getListUrl($standardA->id))
            ->assertOk()
            ->assertSee('IND-A')
            ->assertSee('Indikator A')
            ->assertDontSee('IND-B')
            ->assertDontSee('Indikator B');
    }

    public function test_create_indicator_page_is_accessible_with_standard_id(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'STD-NEW',
            'name' => 'Standard New',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(IndicatorResource::getCreateUrl($standard->id))
            ->assertOk();
    }

    public function test_indicator_is_stored_with_standard_id(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'STD-STORE',
            'name' => 'Standard Store',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $indicator = Indicator::factory()->create([
            'standard_id' => $standard->id,
            'code' => 'IND-001',
            'name' => 'Indikator Satu',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertSame($standard->id, $indicator->standard_id);
        $this->assertTrue($standard->indicators()->whereKey($indicator->id)->exists());
    }

    public function test_edit_indicator_page_prefills_form_with_record_data(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'STD-EDIT',
            'name' => 'Standard Edit',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $indicator = Indicator::factory()->create([
            'standard_id' => $standard->id,
            'code' => 'IND-001',
            'name' => 'Indikator Satu',
            'calculation_method' => Indicator::COUNT,
            'measurement_unit' => 'unit',
            'weight' => 1.50,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(EditIndicator::class, ['record' => $indicator->getKey()])
            ->assertFormSet([
                'standard_id' => $standard->id,
                'code' => 'IND-001',
                'name' => 'Indikator Satu',
                'calculation_method' => Indicator::COUNT,
                'measurement_unit' => 'unit',
                'weight' => '1.50',
            ]);
    }

    public function test_edit_indicator_can_save_changes(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'STD-SAVE',
            'name' => 'Standard Save',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $indicator = Indicator::factory()->create([
            'standard_id' => $standard->id,
            'code' => 'IND-SAVE',
            'name' => 'Nama Lama',
            'calculation_method' => Indicator::COUNT,
            'measurement_unit' => 'unit',
            'weight' => 1,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(EditIndicator::class, ['record' => $indicator->getKey()])
            ->fillForm(['name' => 'Nama Baru'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('indicators', [
            'id' => $indicator->id,
            'standard_id' => $standard->id,
            'name' => 'Nama Baru',
        ]);
    }

    public function test_manage_standards_tree_renders_indicator_action_with_tooltip(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'TREE',
            'name' => 'Tree Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageStandards::class, ['standardSourceId' => (string) $source->id])
            ->assertSeeHtml("content: 'Indikator'")
            ->assertSeeHtml("content: 'Ubah'")
            ->assertSeeHtml("content: 'Hapus'")
            ->assertSeeHtml("content: 'Tambah Sub-standar'")
            ->assertSeeHtml(IndicatorResource::getCreateUrl($standard->id));
    }

    public function test_manage_standards_tree_shows_indicator_count_badge_when_standard_has_indicators(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'COUNT',
            'name' => 'Standard With Indicators',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Indicator::factory()->count(4)->create([
            'standard_id' => $standard->id,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageStandards::class, ['standardSourceId' => (string) $source->id])
            ->assertSee('4 Indikator')
            ->assertSeeHtml(IndicatorResource::getListUrl($standard->id));
    }

    public function test_manage_standards_tree_hides_indicator_count_badge_when_standard_has_none(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        Standard::create([
            'code' => 'EMPTY',
            'name' => 'Standard Without Indicators',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageStandards::class, ['standardSourceId' => (string) $source->id])
            ->assertSee('EMPTY')
            ->assertDontSee('0 Indikator');
    }
}

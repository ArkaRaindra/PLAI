<?php

namespace Tests\Feature;

use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandardNestedsetTest extends TestCase
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

    public function test_scoped_trees_do_not_mix_between_standard_sources(): void
    {
        $admin = User::factory()->create();
        $sourceA = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $sourceB = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        Standard::scoped(['standard_source_id' => $sourceA->id])->create([
            'code' => 'A-ROOT',
            'name' => 'Root A',
            'standard_source_id' => $sourceA->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Standard::scoped(['standard_source_id' => $sourceB->id])->create([
            'code' => 'B-ROOT',
            'name' => 'Root B',
            'standard_source_id' => $sourceB->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $treeA = Standard::scoped(['standard_source_id' => $sourceA->id])->defaultOrder()->get();
        $treeB = Standard::scoped(['standard_source_id' => $sourceB->id])->defaultOrder()->get();

        $this->assertCount(1, $treeA);
        $this->assertCount(1, $treeB);
        $this->assertSame('A-ROOT', $treeA->first()->code);
        $this->assertSame('B-ROOT', $treeB->first()->code);
    }

    public function test_standard_can_have_nested_child(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

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

        $this->assertTrue($root->isAncestorOf($child));
        $this->assertTrue($child->isDescendantOf($root));
    }

    public function test_standard_stores_description(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::scoped(['standard_source_id' => $source->id])->create([
            'code' => 'DESC',
            'name' => 'Standard With Description',
            'description' => 'Deskripsi standar untuk node ini.',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertSame('Deskripsi standar untuk node ini.', $standard->fresh()->description);
    }

    public function test_scoped_query_returns_empty_when_source_has_no_standards(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standards = Standard::scoped(['standard_source_id' => $source->id])->defaultOrder()->get();

        $this->assertCount(0, $standards);
    }

    public function test_parent_with_children_cannot_be_deleted_when_config_disallows(): void
    {
        config(['sn-filament-nestedset.allow_delete_parent' => false]);

        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $root = Standard::create([
            'code' => 'ROOT',
            'name' => 'Root Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Standard::create([
            'code' => 'CHILD',
            'name' => 'Child Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ], $root);

        $root->refresh()->load('children');

        $this->assertTrue($root->children->isNotEmpty());
        $this->assertFalse(config('sn-filament-nestedset.allow_delete_parent'));
    }
}

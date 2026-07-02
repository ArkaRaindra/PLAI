<?php

namespace Tests\Feature;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\User;
use App\Models\UserPosition;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionTest extends TestCase
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

    public function test_position_can_be_created(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $this->actingAs($admin);

        $position = Position::create([
            'code' => 'AUTO',
            'name' => 'Auto Blameable Position',
            'description' => 'Created without explicit blame fields',
        ]);

        $this->assertModelExists($position);
        $this->assertEquals($admin->id, $position->created_by);
        $this->assertNull($position->updated_by);

        $position->update([
            'name' => 'Auto Blameable Position Updated',
        ]);

        $this->assertEquals($admin->id, $position->fresh()->updated_by);

        $position = Position::create([
            'code' => 'TEST',
            'name' => 'Test Position',
            'description' => 'Test Description',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertModelExists($position);
        $this->assertSame('TEST', $position->code);
        $this->assertSame('Test Position', $position->name);
        $this->assertSame('Test Description', $position->description);
        $this->assertSame($admin->id, $position->created_by);
    }

    public function test_position_can_be_updated(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $position = Position::create([
            'code' => 'TEST',
            'name' => 'Test Position',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $position->update([
            'name' => 'Updated Position',
            'description' => 'Updated Description',
            'updated_by' => $admin->id,
        ]);

        $this->assertSame('Updated Position', $position->fresh()->name);
        $this->assertSame('Updated Description', $position->fresh()->description);
    }

    public function test_position_can_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $position = Position::create([
            'code' => 'TEST',
            'name' => 'Test Position',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $position->delete();

        $this->assertModelMissing($position);
    }

    public function test_position_code_must_be_unique(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        Position::create([
            'code' => 'TEST',
            'name' => 'First Position',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->expectException(QueryException::class);

        Position::create([
            'code' => 'TEST',
            'name' => 'Second Position',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
    }

    public function test_position_belongs_to_created_by_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $position = Position::create([
            'code' => 'TEST',
            'name' => 'Test Position',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertEquals($admin->id, $position->createdBy->id);
        $this->assertEquals($admin->username, $position->createdBy->username);
    }

    public function test_position_belongs_to_updated_by_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $position = Position::create([
            'code' => 'TEST',
            'name' => 'Test Position',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertEquals($admin->id, $position->updatedBy->id);

        $updater = User::factory()->create();
        $updater->assignRole('auditor');

        $position->update([
            'name' => 'Updated',
            'updated_by' => $updater->id,
        ]);

        $this->assertEquals($updater->id, $position->fresh()->updatedBy->id);
    }

    public function test_position_has_user_positions_relationship(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $position = Position::create([
            'code' => 'TEST',
            'name' => 'Test Position',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $user = User::factory()->create();
        $user->assignRole('dosen');

        $organizationUnit = OrganizationUnit::create([
            'code' => 'UPM',
            'name' => 'Unit Penjaminan Mutu',
            'type' => 'UPM',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $userPosition = UserPosition::create([
            'user_id' => $user->id,
            'position_id' => $position->id,
            'organization_unit_id' => $organizationUnit->id,
            'start_date' => now()->toDateString(),
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertTrue($position->userPositions()->where('id', $userPosition->id)->exists());
        $this->assertSame(1, $position->userPositions()->count());
    }
}

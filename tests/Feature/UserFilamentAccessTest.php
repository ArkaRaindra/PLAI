<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFilamentAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_super_admin_can_only_access_super_admin_panel(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('super-admin');

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('super-admin')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('auditor')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('fakultas')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('prodi')));
    }

    public function test_auditor_can_only_access_auditor_panel(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('auditor');

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('super-admin')));
        $this->assertTrue($user->canAccessPanel(Filament::getPanel('auditor')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('fakultas')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('prodi')));
    }

    public function test_fakultas_can_only_access_fakultas_panel(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('fakultas');

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('super-admin')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('auditor')));
        $this->assertTrue($user->canAccessPanel(Filament::getPanel('fakultas')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('prodi')));
    }

    public function test_prodi_role_can_access_prodi_panel(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('prodi');

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('super-admin')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('auditor')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('fakultas')));
        $this->assertTrue($user->canAccessPanel(Filament::getPanel('prodi')));
    }

    public function test_unit_penunjang_role_can_access_prodi_panel(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('unit-penunjang');

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('super-admin')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('auditor')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('fakultas')));
        $this->assertTrue($user->canAccessPanel(Filament::getPanel('prodi')));
    }

    public function test_inactive_user_cannot_access_any_panel(): void
    {
        $user = User::factory()->create(['is_active' => false]);
        $user->assignRole('super-admin');

        foreach (['super-admin', 'auditor', 'fakultas', 'prodi'] as $panelId) {
            $this->assertFalse($user->canAccessPanel(Filament::getPanel($panelId)));
        }
    }

    public function test_super_admin_cannot_access_prodi_panel_via_http(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('super-admin');

        $this->actingAs($user)
            ->get('/prodi')
            ->assertNotFound();
    }

    public function test_prodi_user_can_access_prodi_panel_via_http(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('prodi');

        $this->actingAs($user)
            ->get('/prodi')
            ->assertOk();
    }

    public function test_welcome_page_lists_portal_login_links(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Welcome'));
    }
}

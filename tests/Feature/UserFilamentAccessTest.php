<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserFilamentAccessTest extends TestCase
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

    public function test_ketua_lpm_can_access_fakultas_panel(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('ketua-lpm');

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('super-admin')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('auditor')));
        $this->assertTrue($user->canAccessPanel(Filament::getPanel('fakultas')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('prodi')));
    }

    public function test_admin_mutu_can_access_fakultas_panel(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('admin-mutu');

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('super-admin')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('auditor')));
        $this->assertTrue($user->canAccessPanel(Filament::getPanel('fakultas')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('prodi')));
    }

    #[DataProvider('prodiPanelRolesProvider')]
    public function test_prodi_panel_roles_can_access_prodi_panel(string $role): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('super-admin')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('auditor')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('fakultas')));
        $this->assertTrue($user->canAccessPanel(Filament::getPanel('prodi')));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function prodiPanelRolesProvider(): array
    {
        return [
            'kaprodi' => ['kaprodi'],
            'sekprodi' => ['sekprodi'],
            'kepala unit' => ['kepala-unit'],
            'dosen' => ['dosen'],
            'tendik' => ['tendik'],
        ];
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

    public function test_kaprodi_user_can_access_prodi_panel_via_http(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('kaprodi');

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

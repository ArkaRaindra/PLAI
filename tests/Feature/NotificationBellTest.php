<?php

namespace Tests\Feature;

use App\Models\Notification as AppNotification;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationBellTest extends TestCase
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

    private function superAdmin(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('super-admin');

        return $user;
    }

    public function test_topbar_bell_renders_stored_notifications(): void
    {
        $user = $this->superAdmin();

        AppNotification::query()->create([
            'user_id' => $user->id,
            'title' => 'Evidence Disetujui',
            'message' => 'Evidence Anda telah disetujui',
            'is_read' => false,
            'type' => 'App\Notifications\EvidenceApprovedNotification',
            'notifiable_type' => 'user',
            'notifiable_id' => $user->id,
            'data' => ['title' => 'Evidence Disetujui'],
        ]);

        $this->actingAs($user)
            ->get('/super-admin')
            ->assertOk()
            ->assertSee('Evidence Disetujui')
            ->assertSee('Notifikasi');
    }

    public function test_topbar_bell_shows_mark_all_action_when_unread_exist(): void
    {
        $user = $this->superAdmin();

        AppNotification::query()->create([
            'user_id' => $user->id,
            'title' => 'Evidence Baru',
            'message' => 'Menunggu review',
            'is_read' => false,
            'type' => 'App\Notifications\EvidenceSubmittedNotification',
            'notifiable_type' => 'user',
            'notifiable_id' => $user->id,
            'data' => ['title' => 'Evidence Baru'],
        ]);

        $this->actingAs($user)
            ->get('/super-admin')
            ->assertOk()
            ->assertSee('Tandai semua dibaca');
    }
}

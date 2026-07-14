<?php

namespace Tests\Feature;

use App\Models\EvidenceLinks;
use App\Models\Evidences;
use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\Standard;
use App\Models\User;
use App\Support\EvidenceLink\LinkableTypeRegistry;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvidenceLinkTest extends TestCase
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

    public function test_evidence_can_be_linked_to_an_indicator(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);
        $indicator = Indicator::factory()->create();

        $this->actingAs($dosen);
        $this->assertTrue($dosen->can('create', EvidenceLinks::class));

        $link = EvidenceLinks::query()->create([
            'evidence_id' => $evidence->id,
            'reference_type' => 'indicator',
            'reference_id' => $indicator->id,
            'created_by' => (string) $dosen->id,
        ]);

        $this->assertInstanceOf(Indicator::class, $link->reference);
        $this->assertSame($indicator->id, $link->reference->id);
        $this->assertSame($indicator->name, $link->referenceTitle());
        $this->assertSame('Indikator', $link->referenceTypeLabel());
    }

    public function test_evidence_can_be_linked_to_a_standard_document(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $standard = Standard::factory()->create();

        $this->actingAs($adminMutu);

        $link = EvidenceLinks::query()->create([
            'evidence_id' => $evidence->id,
            'reference_type' => 'standard',
            'reference_id' => $standard->id,
            'created_by' => (string) $adminMutu->id,
        ]);

        $this->assertInstanceOf(Standard::class, $link->reference);
        $this->assertSame($standard->name, $link->referenceTitle());
        $this->assertSame('Dokumen (Standar)', $link->referenceTypeLabel());
    }

    public function test_finding_and_risk_types_are_registered_but_not_yet_available(): void
    {
        $this->assertFalse(LinkableTypeRegistry::isAvailable('finding'));
        $this->assertFalse(LinkableTypeRegistry::isAvailable('risk'));
        $this->assertTrue(LinkableTypeRegistry::isAvailable('indicator'));
        $this->assertTrue(LinkableTypeRegistry::isAvailable('standard'));

        $this->assertArrayNotHasKey('finding', LinkableTypeRegistry::availableOptions());
        $this->assertArrayHasKey('indicator', LinkableTypeRegistry::availableOptions());
    }

    public function test_dosen_cannot_delete_another_users_link(): void
    {
        $dosen = $this->makeUser('dosen');
        $anotherDosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);
        $indicator = Indicator::factory()->create();

        $this->actingAs($dosen);

        $link = EvidenceLinks::query()->create([
            'evidence_id' => $evidence->id,
            'reference_type' => 'indicator',
            'reference_id' => $indicator->id,
            'created_by' => (string) $dosen->id,
        ]);

        $this->assertFalse($anotherDosen->can('delete', $link));
        $this->assertTrue($dosen->can('delete', $link));
    }

    public function test_admin_mutu_can_manage_any_link(): void
    {
        $dosen = $this->makeUser('dosen');
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($dosen);
        $indicator = Indicator::factory()->create();

        $this->actingAs($dosen);
        $link = EvidenceLinks::query()->create([
            'evidence_id' => $evidence->id,
            'reference_type' => 'indicator',
            'reference_id' => $indicator->id,
            'created_by' => (string) $dosen->id,
        ]);

        $this->assertTrue($adminMutu->can('update', $link));
        $this->assertTrue($adminMutu->can('delete', $link));
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    private function makeEvidence(User $creator): Evidences
    {
        $unit = OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $creator->id,
        ]);

        return Evidences::query()->create([
            'organization_unit_id' => $unit->id,
            'title' => 'Bukti '.fake()->words(3, true),
            'description' => fake()->sentence(),
            'created_by' => (string) $creator->id,
        ]);
    }
}

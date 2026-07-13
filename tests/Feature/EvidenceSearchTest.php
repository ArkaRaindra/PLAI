<?php

namespace Tests\Feature;

use App\Models\Evidences;
use App\Models\OrganizationUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvidenceSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_matches_keyword_in_title_or_description(): void
    {
        $owner = $this->makeUser();
        $unit = $this->makeUnit($owner);

        $matchByTitle = $this->makeEvidence($owner, $unit, [
            'title' => 'Laporan Kinerja Dosen Semester Ganjil',
            'description' => 'Ringkasan kegiatan tridharma.',
        ]);
        $matchByDescription = $this->makeEvidence($owner, $unit, [
            'title' => 'Dokumen Rapat Tinjauan Manajemen',
            'description' => 'Membahas evaluasi kinerja dosen tahun ini.',
        ]);
        $noMatch = $this->makeEvidence($owner, $unit, [
            'title' => 'SK Pengangkatan Panitia',
            'description' => 'Surat keputusan panitia wisuda.',
        ]);

        $results = Evidences::query()->search('kinerja dosen')->pluck('id');

        $this->assertTrue($results->contains($matchByTitle->id));
        $this->assertTrue($results->contains($matchByDescription->id));
        $this->assertFalse($results->contains($noMatch->id));
    }

    public function test_search_with_blank_keyword_returns_everything(): void
    {
        $owner = $this->makeUser();
        $unit = $this->makeUnit($owner);

        $this->makeEvidence($owner, $unit);
        $this->makeEvidence($owner, $unit);

        $this->assertSame(2, Evidences::query()->search(null)->count());
        $this->assertSame(2, Evidences::query()->search('')->count());
    }

    public function test_filter_by_type(): void
    {
        $owner = $this->makeUser();
        $unit = $this->makeUnit($owner);

        $file = $this->makeEvidence($owner, $unit, ['type' => 'file']);
        $this->makeEvidence($owner, $unit, ['type' => 'url']);

        $results = Evidences::query()->type('file')->pluck('id');

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($file->id));
    }

    public function test_filter_by_unit(): void
    {
        $owner = $this->makeUser();
        $unitA = $this->makeUnit($owner, 'Prodi A');
        $unitB = $this->makeUnit($owner, 'Prodi B');

        $evidenceA = $this->makeEvidence($owner, $unitA);
        $this->makeEvidence($owner, $unitB);

        $results = Evidences::query()->unit($unitA->id)->pluck('id');

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($evidenceA->id));
    }

    public function test_filter_by_workflow_status(): void
    {
        $owner = $this->makeUser();
        $unit = $this->makeUnit($owner);

        $draft = $this->makeEvidence($owner, $unit);
        $another = $this->makeEvidence($owner, $unit);
        $another->transitionWorkflowTo('submitted');

        $draftResults = Evidences::query()->status('draft')->pluck('id');
        $submittedResults = Evidences::query()->status('submitted')->pluck('id');

        $this->assertTrue($draftResults->contains($draft->id));
        $this->assertFalse($draftResults->contains($another->id));
        $this->assertTrue($submittedResults->contains($another->id));
    }

    public function test_filters_can_be_combined(): void
    {
        $owner = $this->makeUser();
        $unit = $this->makeUnit($owner);

        $target = $this->makeEvidence($owner, $unit, [
            'title' => 'SOP Penerimaan Mahasiswa Baru',
            'type' => 'file',
        ]);
        $this->makeEvidence($owner, $unit, [
            'title' => 'SOP Ujian Akhir',
            'type' => 'file',
        ]);
        $this->makeEvidence($owner, $unit, [
            'title' => 'Laporan Penerimaan Mahasiswa Baru',
            'type' => 'url',
        ]);

        $results = Evidences::query()
            ->search('penerimaan mahasiswa')
            ->type('file')
            ->pluck('id');

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($target->id));
    }

    private function makeUser(): User
    {
        return User::factory()->create(['is_active' => true]);
    }

    private function makeUnit(User $creator, string $name = 'Prodi Teknik Informatika'): OrganizationUnit
    {
        return OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => $name,
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $creator->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeEvidence(User $owner, OrganizationUnit $unit, array $overrides = []): Evidences
    {
        $this->actingAs($owner);

        return Evidences::query()->create(array_merge([
            'organization_unit_id' => $unit->id,
            'title' => 'Bukti '.fake()->words(3, true),
            'description' => fake()->sentence(),
            'created_by' => (string) $owner->id,
        ], $overrides));
    }
}

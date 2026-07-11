<?php

namespace Database\Seeders;

use App\Enums\EvidenceCategory;
use App\Models\Evidences;
use App\Models\OrganizationUnit;
use App\Models\User;
use Illuminate\Database\Seeder;

class EvidenceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();
        $units = OrganizationUnit::whereIn('code', ['FT', 'TI', 'TM', 'UPM', 'P3M', 'SPI'])->get();

        $categories = [
            EvidenceCategory::Sop,
            EvidenceCategory::Sk,
            EvidenceCategory::Pedoman,
            EvidenceCategory::Laporan,
            EvidenceCategory::Audit,
            EvidenceCategory::Rtm,
            EvidenceCategory::Kerjasama,
            EvidenceCategory::Penelitian,
            EvidenceCategory::Pkm,
            EvidenceCategory::Akreditasi,
        ];

        $titles = [
            'SOP Penjaminan Mutu Internal',
            'SK Pendirian Program Studi',
            'Pedoman Evaluasi Dosen',
            'Laporan Audit Mutu 2025',
            'Hasil Review Standar ISO',
            'RTM Meeting Minutes',
            'MoD dengan industri mitra',
            'Laporan Penelitian Dosen',
            'Laporan PKM Mahasiswa',
            'Bukti Akreditasi Unggulan',
        ];

        foreach ($titles as $index => $title) {
            $unit = $units->get($index % $units->count());

            Evidences::query()->create([
                'organization_unit_id' => $unit->id,
                'title' => $title,
                'description' => 'Bukti akreditasi untuk '.$title.' di '.$unit->name,
                'category' => $categories[$index % count($categories)]->value,
                'current_version' => 'v1',
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);
        }
    }
}

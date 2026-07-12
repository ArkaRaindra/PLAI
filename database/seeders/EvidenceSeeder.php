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
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();
        $units = OrganizationUnit::whereIn('type', ['PROGRAM STUDI', 'UPM', 'P3M', 'SPI'])->get();

        if ($units->isEmpty()) {
            return;
        }

        $evidenceTemplates = [
            ['title' => 'SOP Penjaminan Mutu Internal', 'category' => EvidenceCategory::Sop],
            ['title' => 'SK Pendirian Program Studi', 'category' => EvidenceCategory::Sk],
            ['title' => 'Pedoman Evaluasi Dosen', 'category' => EvidenceCategory::Pedoman],
            ['title' => 'Laporan Audit Mutu 2025', 'category' => EvidenceCategory::Laporan],
            ['title' => 'Hasil Review Standar ISO', 'category' => EvidenceCategory::Audit],
            ['title' => 'RTM Meeting Minutes Q3', 'category' => EvidenceCategory::Rtm],
            ['title' => 'MoD dengan Industri Mitra', 'category' => EvidenceCategory::Kerjasama],
            ['title' => 'Laporan Penelitian Dosen', 'category' => EvidenceCategory::Penelitian],
            ['title' => 'Laporan PKM Mahasiswa', 'category' => EvidenceCategory::Pkm],
            ['title' => 'Bukti Akreditasi Unggulan', 'category' => EvidenceCategory::Akreditasi],
            ['title' => 'Dokumen Rencana Strategis', 'category' => EvidenceCategory::Pedoman],
            ['title' => 'Laporan Kinerja Semester', 'category' => EvidenceCategory::Laporan],
        ];

        foreach ($evidenceTemplates as $template) {
            $unit = $units->random();
            $uniqueTitle = $template['title'].' - '.$unit->name;

            Evidences::query()->firstOrCreate(
                [
                    'title' => $uniqueTitle,
                    'organization_unit_id' => $unit->id,
                ],
                [
                    'description' => 'Bukti akreditasi untuk '.$template['title'].' di '.$unit->name,
                    'category' => $template['category']->value,
                    'current_version' => 'v1',
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ]
            );
        }
    }
}

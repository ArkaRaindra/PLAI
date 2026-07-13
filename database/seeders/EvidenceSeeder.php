<?php

namespace Database\Seeders;

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
            'SOP Penjaminan Mutu Internal',
            'SK Pendirian Program Studi',
            'Pedoman Evaluasi Dosen',
            'Laporan Audit Mutu 2025',
            'Hasil Review Standar ISO',
            'RTM Meeting Minutes Q3',
            'MoD dengan Industri Mitra',
            'Laporan Penelitian Dosen',
            'Laporan PKM Mahasiswa',
            'Bukti Akreditasi Unggulan',
            'Dokumen Rencana Strategis',
            'Laporan Kinerja Semester',
        ];

        foreach ($evidenceTemplates as $title) {
            $unit = $units->random();
            $uniqueTitle = $title.' - '.$unit->name;

            Evidences::query()->firstOrCreate(
                [
                    'title' => $uniqueTitle,
                    'organization_unit_id' => $unit->id,
                ],
                [
                    'description' => 'Bukti akreditasi untuk '.$title.' di '.$unit->name,
                    'current_version' => 'v1',
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\EvidenceReview;
use App\Models\Evidences;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class EvidenceReviewSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();

        Auth::setUser($admin);

        $evidences = Evidences::all();

        if ($evidences->isEmpty()) {
            return;
        }

        $reviewNotes = [
            null,
            'Bukti sudah lengkap dan sesuai standar.',
            'Bukti tidak relevan dengan indikator yang dinilai.',
        ];

        foreach ($evidences as $index => $evidence) {
            $note = $reviewNotes[$index % count($reviewNotes)];

            EvidenceReview::query()->firstOrCreate(
                ['evidence_id' => $evidence->id],
                [
                    'review_notes' => $note,
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ]
            );
        }
    }
}

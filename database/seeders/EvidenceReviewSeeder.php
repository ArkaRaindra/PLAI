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
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();
        $auditor = User::where('email', 'auditor@example.com')->firstOrFail();
        $adminMutu = User::where('email', 'adminmutu@example.com')->firstOrFail();
        $ketuaLpm = User::where('email', 'ketualpm@example.com')->firstOrFail();

        Auth::setUser($admin);

        $evidences = Evidences::orderBy('id')->get();

        if ($evidences->isEmpty()) {
            return;
        }

        $reviewers = [$auditor, $adminMutu, $ketuaLpm];

        $scenarios = [
            [
                'status' => 'pending',
                'review_notes' => null,
            ],
            [
                'status' => 'approved',
                'review_notes' => 'Bukti sudah lengkap dan sesuai standar.',
            ],
            [
                'status' => 'rejected',
                'review_notes' => 'Bukti tidak relevan dengan indikator yang dinilai.',
            ],
            [
                'status' => 'revision_needed',
                'review_notes' => 'Mohon lampirkan dokumen pendukung tambahan.',
            ],
        ];

        foreach ($evidences->values() as $index => $evidence) {
            $scenario = $scenarios[$index % count($scenarios)];
            $reviewer = $reviewers[$index % count($reviewers)];

            EvidenceReview::query()->firstOrCreate(
                [
                    'evidence_id' => $evidence->id,
                    'reviewer_id' => $reviewer->id,
                ],
                [
                    'status' => $scenario['status'],
                    'review_notes' => $scenario['review_notes'],
                    'assigned_by' => $admin->id,
                    'assigned_at' => now()->subDays(7 - ($index % 7)),
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ],
            );
        }
    }
}

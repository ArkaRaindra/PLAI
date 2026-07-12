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
        $auditor = User::where('email', 'auditor@example.com')->first() ?? User::first();
        $adminMutu = User::where('email', 'adminmutu@example.com')->first() ?? User::where('role', 'admin-mutu')->first() ?? User::first();
        $ketuaLpm = User::where('email', 'ketualpm@example.com')->first() ?? User::where('role', 'ketua-lpm')->first() ?? User::first();

        Auth::setUser($admin);

        $evidences = Evidences::all();

        if ($evidences->isEmpty()) {
            return;
        }

        $reviewers = [$auditor, $adminMutu, $ketuaLpm];

        $reviewNotes = [
            'pending' => null,
            'approved' => 'Bukti sudah lengkap dan sesuai standar.',
            'rejected' => 'Bukti tidak relevan dengan indikator yang dinilai.',
            'revision_needed' => 'Mohon lampirkan dokumen pendukung tambahan.',
        ];

        foreach ($evidences as $index => $evidence) {
            $reviewer = $reviewers[$index % count($reviewers)];

            $statusOptions = ['pending', 'approved', 'rejected', 'revision_needed'];
            $status = $statusOptions[$index % count($statusOptions)];

            $review = EvidenceReview::query()->firstOrCreate(
                [
                    'evidence_id' => $evidence->id,
                    'reviewer_id' => $reviewer->id,
                ],
                [
                    'status' => $status,
                    'review_notes' => $reviewNotes[$status],
                    'assigned_by' => $admin->id,
                    'assigned_at' => now()->subDays(7 - ($index % 7)),
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ]
            );

            if (in_array($status, ['approved', 'rejected', 'revision_needed'], true)) {
                $review->update([
                    'reviewed_at' => now()->subDays(1),
                ]);
            }
        }
    }
}

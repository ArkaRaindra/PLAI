<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audit_scores', function (Blueprint $table): void {
            $table->foreignId('audit_evidence_id')
                ->nullable()
                ->constrained('audit_evidences')
                ->nullOnDelete();
        });

        DB::table('audit_scores')
            ->whereNull('audit_evidence_id')
            ->chunkById(100, function ($scores): void {
                foreach ($scores as $score) {
                    $evidenceIds = DB::table('audit_evidences')
                        ->where('user_id', $score->user_id)
                        ->where('sub_standard_id', $score->sub_standard_id)
                        ->where('period_id', $score->period_id)
                        ->pluck('id');

                    if ($evidenceIds->count() === 1) {
                        DB::table('audit_scores')
                            ->whereKey($score->id)
                            ->update(['audit_evidence_id' => $evidenceIds->first()]);
                    }
                }
            });

        Schema::table('audit_scores', function (Blueprint $table): void {
            $table->unique('audit_evidence_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_scores', function (Blueprint $table): void {
            $table->dropUnique(['audit_evidence_id']);
            $table->dropForeign(['audit_evidence_id']);
            $table->dropColumn('audit_evidence_id');
        });
    }
};

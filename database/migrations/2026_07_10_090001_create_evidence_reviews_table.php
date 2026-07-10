<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidence_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidence_id')->constrained('evidences')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'revision_needed'])->default('prnding');
            $table->text('review_notes')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->index(['evidence_id', 'status']);
            $table->index(['reviewer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_reviews');
    }
};

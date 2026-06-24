<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evidence_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidence_id')->constrained('evidences');
            $table->foreignId('reviewer_id')->constrained('users');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_reviews');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->constrained('targets')->cascadeOnDelete();
            $table->foreignId('organization_unit_id')->constrained('organization_units')->cascadeOnDelete();
            $table->decimal('actual_value', 18, 2);
            $table->decimal('score', 18, 2);
            $table->text('notes')->nullable();
            $table->text('note_rejected')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['target_id', 'organization_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realizations');
    }
};

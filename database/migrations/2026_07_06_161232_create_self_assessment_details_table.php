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
        Schema::create('self_assessment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('self_assessment_id')->constrained('self_assessments')->cascadeOnDelete();
            $table->foreignId('indicator_id')->constrained('indicators')->cascadeOnDelete();
            $table->decimal('score', 8, 2)->nullable();
            $table->text('analysis')->nullable();
            $table->text('strength')->nullable();
            $table->text('weakness')->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['self_assessment_id', 'indicator_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('self_assessment_details');
    }
};

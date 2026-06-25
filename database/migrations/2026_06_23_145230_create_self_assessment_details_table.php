<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_assessment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('self_assessment_id')->constrained('self_assessments');
            $table->foreignId('indicator_id')->constrained('indicators');
            $table->decimal('score', 8, 2);
            $table->text('analysis')->nullable();
            $table->text('strength')->nullable();
            $table->text('weakness')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_assessment_details');
    }
};

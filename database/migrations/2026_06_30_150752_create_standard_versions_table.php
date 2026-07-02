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
        Schema::create('standard_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards')->cascadeOnDelete();
            $table->foreignId('quality_period_id')->constrained('quality_periods')->cascadeOnDelete();
            $table->string('version');
            $table->date('start_date')->default(now());
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standard_versions');
    }
};

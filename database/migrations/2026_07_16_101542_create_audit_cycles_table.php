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
        Schema::create('audit_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_period_id')->constrained('quality_periods')->cascadeOnDelete();
            $table->foreignId('checklist_template_id')->constrained('audit_checklist_templates')->nullOnDelete();
            $table->enum('status', ['draft', 'ongoing', 'completed','cancelled' ])->default('draft');
            $table->timestamp('created_by')->nullable();
            $table->timestamp('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_cycles');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_period_id')->constrained('quality_periods');
            $table->foreignId('checklist_template_id')->constrained('audit_checklist_templates');
            $table->string('status');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_cycles');
    }
};

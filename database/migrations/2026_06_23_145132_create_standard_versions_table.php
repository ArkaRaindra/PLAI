<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standard_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards');
            $table->foreignId('quality_period_id')->constrained('quality_periods');
            $table->integer('version_no');
            $table->text('description');
            $table->date('effective_date');
            $table->boolean('is_active');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standard_versions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accreditation_indicator_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained('indicators');
            $table->unsignedBigInteger('accreditation_criteria_id');
            $table->foreign('accreditation_criteria_id', 'fk_a_i_m_criteria')->references('id')->on('accreditation_criteria');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accreditation_indicator_mappings');
    }
};

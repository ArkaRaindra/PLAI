<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accreditation_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('framework_id')->constrained('accreditation_frameworks');
            $table->string('code');
            $table->string('name');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accreditation_criteria');
    }
};

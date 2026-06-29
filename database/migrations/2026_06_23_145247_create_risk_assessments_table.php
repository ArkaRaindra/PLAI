<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_id')->constrained('risks');
            $table->integer('probability');
            $table->integer('impact');
            $table->integer('score');
            $table->foreignId('assessed_by')->constrained('users');
            $table->timestamp('assessment_date');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_assessments');
    }
};

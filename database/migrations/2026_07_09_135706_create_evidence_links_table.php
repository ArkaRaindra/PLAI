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
        Schema::create('evidence_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidence_id')->constrained('evidences');
            $table->unsignedBigInteger('reference_id');

            $table->string('reference_type', 100);
            // indicator
            // finding
            // risk
            // realization
            // document

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
        Schema::dropIfExists('evidence_links');
    }
};

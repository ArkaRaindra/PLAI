<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidence_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidence_id')->constrained('evidences');
            $table->string('reference_type');
            $table->bigInteger('reference_id');
            $table->timestamp('created_at');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_links');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traceability_links', function (Blueprint $table) {
            $table->id();
            $table->string('source_type');
            $table->bigInteger('source_id');
            $table->string('target_type');
            $table->bigInteger('target_id');
            $table->string('relation_type');
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traceability_links');
    }
};

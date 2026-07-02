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
        Schema::create('indicator_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internal_indicator_id')->nullable()->constrained('indicators');
            $table->foreignId('external_indicator_id')->nullable()->constrained('indicators');
            $table->boolean('is_primary')->default(true);
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('indicator_mappings');
    }
};

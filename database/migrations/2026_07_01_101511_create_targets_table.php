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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained('indicators');
            $table->foreignId('quality_period_id')->constrained('quality_periods');
            $table->decimal('target_value', 10, 2);
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->unique(
                ['indicator_id', 'quality_period_id'],
                'targets_indicator_period_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};

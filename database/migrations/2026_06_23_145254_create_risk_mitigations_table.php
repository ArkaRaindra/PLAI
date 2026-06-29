<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_mitigations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_id')->constrained('risks');
            $table->foreignId('owner_position_id')->constrained('user_positions');
            $table->date('due_date');
            $table->text('mitigation_plan');
            $table->string('status');
            $table->timestamps();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_mitigations');
    }
};

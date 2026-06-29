<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capa_sla_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corrective_action_id')->constrained('corrective_actions');
            $table->date('target_date');
            $table->date('completed_date')->nullable();
            $table->string('sla_status');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capa_sla_logs');
    }
};

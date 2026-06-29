<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corrective_action_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corrective_action_id')->constrained('corrective_actions');
            $table->integer('progress_percentage');
            $table->text('description');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamp('updated_at');
            $table->string('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corrective_action_updates');
    }
};

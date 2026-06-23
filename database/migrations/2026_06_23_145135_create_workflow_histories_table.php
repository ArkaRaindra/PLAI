<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained('workflow_instances');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->foreignId('acted_by')->constrained('users');
            $table->timestamp('acted_at');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_histories');
    }
};

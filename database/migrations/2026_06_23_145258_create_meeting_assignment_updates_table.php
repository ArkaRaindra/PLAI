<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_assignment_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_assignment_id')->constrained('meeting_assignments');
            $table->integer('progress_percentage');
            $table->text('notes');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamp('updated_at');
            $table->string('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_assignment_updates');
    }
};

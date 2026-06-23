<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('decision_id')->constrained('decisions');
            $table->foreignId('assignee_position_id')->constrained('user_positions');
            $table->date('due_date');
            $table->string('status');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_assignments');
    }
};

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
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('faculty_id')
                ->nullable()
                ->constrained('faculties')
                ->nullOnDelete();

            $table->foreignId('study_program_id')
                ->nullable()
                ->constrained('study_programs')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['study_program_id']);
            $table->dropForeign(['faculty_id']);
            $table->dropColumn(['study_program_id', 'faculty_id']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_response_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('survey_responses');
            $table->foreignId('question_id')->constrained('survey_questions');
            $table->integer('score');
            $table->text('comment')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_response_details');
    }
};

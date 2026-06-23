<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_assignment_id')->constrained('audit_assignments');
            $table->foreignId('indicator_id')->nullable()->constrained('indicators');
            $table->string('title');
            $table->string('category');
            $table->string('severity');
            $table->text('description');
            $table->text('root_cause')->nullable();
            $table->text('recommendation')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_findings');
    }
};

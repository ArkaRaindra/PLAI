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
        Schema::create('audit_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_assignment_id')->constrained('audit_assignmentd')->cascadeOnDelete();
            $table->foreignId('indicator_id')->nullable()->constrained('indicators')->nullOnDelet();
            $table->string('title');
            $table->string('category');
            $table->string('severity');
            $table->text('description');
            $table->text('root_cause')->nullable();
            $table->text('recommendation')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('open');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_findings');
    }
};

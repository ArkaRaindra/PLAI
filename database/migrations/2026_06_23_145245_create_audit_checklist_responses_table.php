<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_checklist_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_assignment_id')->constrained('audit_assignments');
            $table->foreignId('checklist_item_id')->constrained('audit_checklist_template_items');
            $table->text('answer');
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_checklist_responses');
    }
};

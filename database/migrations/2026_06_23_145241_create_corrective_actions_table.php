<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_finding_id')->constrained('audit_findings');
            $table->foreignId('decision_id')->nullable()->constrained('decisions');
            $table->foreignId('organization_unit_id')->constrained('organization_units');
            $table->foreignId('owner_position_id')->constrained('user_positions');
            $table->date('due_date');
            $table->text('plan');
            $table->enum('status', ['draft', 'submitted', 'approved', 'in_progress', 'completed', 'closed'])->default('draft');
            $table->timestamps();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corrective_actions');
    }
};

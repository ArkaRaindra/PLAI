<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_unit_id')->constrained('organization_units');
            $table->foreignId('audit_finding_id')->nullable()->constrained('audit_findings');
            $table->string('code');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->foreignId('risk_owner_position_id')->constrained('user_positions');
            $table->string('status');
            $table->timestamps();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};

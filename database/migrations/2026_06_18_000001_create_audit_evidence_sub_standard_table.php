<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_evidence_sub_standard', function (Blueprint $table): void {
            $table->foreignId('audit_evidence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_standard_id')->constrained()->cascadeOnDelete();
            $table->primary(['audit_evidence_id', 'sub_standard_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_evidence_sub_standard');
    }
};

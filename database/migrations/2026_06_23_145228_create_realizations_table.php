<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->constrained('targets');
            $table->foreignId('organization_unit_id')->constrained('organization_units');
            $table->decimal('actual_value', 18, 2);
            $table->decimal('score', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status');
            $table->foreignId('submitted_by')->nullable()->constrained('users');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realizations');
    }
};

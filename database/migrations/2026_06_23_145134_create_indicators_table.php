<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_version_id')->constrained('standard_versions');
            $table->foreignId('parent_id')->nullable()->constrained('indicators');
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('measurement_formula')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('weight', 8, 2);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }

};
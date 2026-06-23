<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_unit_id')->constrained('organization_units');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('current_version');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidences');
    }
};

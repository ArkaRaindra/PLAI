<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standard_responsibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_version_id')->constrained('standard_versions');
            $table->string('ppepp_stage');
            $table->foreignId('user_position_id')->constrained('user_positions');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standard_responsibilities');
    }
};
